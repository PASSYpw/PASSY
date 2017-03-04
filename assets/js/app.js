(function () {
    //##################################################################################################################
    //GLOBAL VARS
    //##################################################################################################################
    var currentPage = "login",
        currentScope = "logged_out",
        currentAlert,
        switchingPage = false,
        spinnerSVGSmall = '<svg class="spinner" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg>',
        rippleSettings = {
            debug: false,
            on: 'mousedown',
            opacity: 0.3,
            color: "auto",
            multi: true,
            duration: 0.3,
            easing: 'linear'
        }, hideAlert;

    //##################################################################################################################
    //GLOBAL METHODS
    //##################################################################################################################
    function showAlert(object, timeout) {
        if (hideAlert != null)
            hideAlert();
        clearInterval(currentAlert);
        object.fadeIn(100, function () {
            var me = $(this);
            hideAlert = function () {
                me.fadeOut(100);
                hideAlert = null;
            };
            if (timeout > 0) {
                currentAlert = setTimeout(hideAlert, timeout)
            }
        });
    }

    function startsWith(haystack, needle) {
        return haystack.substr(0, needle.length) == needle;
    }

    function getCurrentPage() {
        var anchor = location.href.substring(location.href.indexOf("#"));
        if (anchor.substring(0, 4) === "#!p=" && anchor.length > 1) {
            return anchor.substring(4);
        }
        return currentPage;
    }

    function loadPage(page, callback) {
        if (switchingPage)
            return;
        switchingPage = true;
        var oldPage = $("#page_" + currentPage), newPage = $("#page_" + page), spinner = $(".load-spinner");
        currentPage = page;
        changePageScope(newPage.attr("data-apply-page-scope"));

        spinner.addClass("shown");

        $("*[data-page-highlight]").each(function (index, elem) {
            elem = $(elem);
            if (elem.attr("data-page-highlight") != page) {
                elem.removeClass("active");
            }
        });

        var show = function () {
            $("*[data-page-highlight]").each(function (index, elem) {
                elem = $(elem);
                if (elem.attr("data-page-highlight") == page) {
                    elem.addClass("active");
                }
            });
            spinner.removeClass("shown");
            newPage.fadeIn(300);
            switchingPage = false;
            if (callback != null)
                callback();
        };

        oldPage.fadeOut(300, function () {
            if (page == "passwords" || page == "archive") {
                fetchPasswords(show);
            } else if (page == "login_history") {
                fetchIPLog(show);
            } else {
                show();
            }
        });
    }

    function refresh() {
        var refreshButton = $("#btnRefresh"), icon = refreshButton.find(".material-icons");
        icon.addClass("spin");
        refreshButton.addClass("disabled");
        refreshButton.attr("disabled", "");
        setTimeout(function () {
            loadPage(currentPage, function () {
                icon.removeClass("spin");
                refreshButton.removeClass("disabled");
                refreshButton.attr("disabled", null);
            });
        }, 100);
    }

    function changePageScope(scope) {
        currentScope = scope;
        $("*[data-page-scope]").each(function (i, elem) {
            elem = $(elem);
            if (elem.data("page-scope") != scope) {
                elem.attr("style", "display: none");
            } else {
                elem.attr("style", null);
            }
        });
    }

    function logout() {
        $.ajax({
            url: "backend/logout.php",
            success: function () {
                showAlert($("#successLoggedOut"), 3000);
                loadPage("login");
            }
        })
    }

    function sessionExpired() {
        showAlert($("#warningInactive"), 0);
        switchingPage = false;
        loadPage("login");
    }

    //##################################################################################################################
    //DOCUMENT LOAD
    //##################################################################################################################
    $(document).ready(function () {
        currentPage = getCurrentPage();

        $.ajax({
            url: "backend/status.php",
            success: function (data) {
                if (data.msg == "success" && currentScope == "logged_out") {
                    currentPage = "passwords";
                } else if (data.msg == "no_login" && currentScope == "logged_in") {
                    sessionExpired();
                }
                loadPage(currentPage);
                registerPageListeners();

                setInterval(function () {
                    $.ajax({
                        url: "backend/status.php",
                        success: function (data) {
                            if (data.msg == "no_login" && currentScope != "logged_out") {
                                sessionExpired();
                            }
                            if (data.msg == "success") {
                                const inputCurrentEmail = $("#inputCurrentEmail");
                                inputCurrentEmail.val(data.data.user_email);
                                inputCurrentEmail.change();
                            }
                        }
                    });
                }, 2000);
            }
        });
    });

    function registerPageListeners() {
        var passwordTable = $('#tbodyPasswords'),
            archivedPasswordTable = $('#tbodyArchivedPasswords'),
            anchor = location.href.substring(location.href.indexOf("#")),
            inputs = $(".text > input"),
            contextMenu = $("#dropdownContextMenu"),
            formEditPass = $("#formEditPasswordPassword");

        if (anchor == "#!r") {
            location.replace("#");
            location.reload(true)
        }

        $.ripple(".nav > li > a", rippleSettings);
        $.ripple(".btn:not([disabled])", rippleSettings);

        inputs.each(function (index, elem) {
            elem = $(elem);
            if (elem.val().length > 0)
                elem.addClass("hastext");
        });

        inputs.change(function () {
            const me = $(this);
            if (me.val().length > 0)
                me.addClass("hastext");
            else
                me.removeClass("hastext");
        });

        var delay = 100;
        $(".dropdown-menu").find("li").each(function (index, item) {
            item = $(item);
            item.css({"animation-delay": delay + "ms"});
            delay += 25;
        });

        var lastHeight = 0;
        const navbar = $(".navbar-fixed-top");
        $(window).scroll(function () {
            $(".dropdown.open").find(".dropdown-toggle").dropdown("toggle");
            $(".contextmenu.open").removeClass("open");

            var scrollTop = $(document).scrollTop(),
                firstHeight = navbar.children().first().outerHeight();

            if (scrollTop < firstHeight) {
                navbar.css({transform: "translateY(-" + scrollTop + "px)"});
                lastHeight = scrollTop;
            }
            if (scrollTop >= firstHeight) {
                navbar.css({transform: "translateY(-" + firstHeight + "px)"});
            }
        });

        $(window).focus(function () {
            $(".content").fadeIn(300);
        }).blur(function () {
            if (!$('iframe').is(':focus'))
                $(".content").fadeOut(300);
        });

        $(document).on("keydown", function (e) {
            if ((e.which || e.keyCode) == 116) {
                e.preventDefault();
                if (e.shiftKey)
                    location.reload(true);
                else
                    refresh();
            }
        });

        $(document).mouseup(function (e) {
            if (e.which == 1)
                contextMenu.removeClass("open");
        });

        $(document).bind("contextmenu", function (e) {
            if (e.shiftKey)
                return;
            var x = e.clientX, y = e.clientY;
            var hoverObject = $(document.elementFromPoint(x, y));
            if (hoverObject.hasClass("no-contextmenu") || hoverObject.parents(".no-contextmenu").length > 0)
                return;
            e.preventDefault();
            contextMenu.removeClass("open");
            setTimeout(function () {
                contextMenu.css({transform: "translate(" + x + "px, " + y + "px)"});
                contextMenu.addClass("open");
            }, 10);
        });

        $("*[data-to-page]").click(function (e) {
            var me = $(this), toPage = me.attr("data-to-page");
            e.preventDefault();
            if (toPage == "refresh")
                toPage = currentPage;
            loadPage(toPage);
        });

        $("#loginForm").submit(function (e) {
            var me = $(this);
            e.preventDefault();
            var data = me.serialize();
            me.find("input").attr("disabled", "disabled");
            me.find("button").attr("disabled", "disabled");
            if (hideAlert != null)
                hideAlert();
            $.ajax({
                url: me.attr("action"),
                method: me.attr("method"),
                data: data,
                success: function (data) {
                    if (data.success) {
                        loadPage("passwords");
                    } else {
                        if (data.msg == "already_logged_in") {
                            loadPage("passwords");
                        } else if (data.msg == "missing_arguments") {
                            showAlert($("#errorLoginFormInvalid"), 3000);
                        } else if (data.msg == "account_locked") {
                            showAlert($("#errorAccountLocked"), 3000);
                        } else if (data.msg == "invalid_email") {
                            showAlert($("#errorLoginEmailInvalid"), 3000);
                        } else if (data.msg == "invalid_credentials") {
                            showAlert($("#errorInvalidCredentials"), 3000);
                        } else if (startsWith(data.msg, "database_")) {
                            showAlert($("#errorLoginDatabase"), 3000);
                        }
                    }
                    me[0].reset();
                    me.find("input").change();
                    me.find("input").attr("disabled", null);
                    me.find("button").attr("disabled", null);
                },
                error: function () {
                    showAlert($("#errorLoginServer"), 3000);
                    me[0].reset();
                    me.find("input").change();
                    me.find("input").attr("disabled", null);
                    me.find("button").attr("disabled", null);
                }
            })
        });

        $("#registerForm").submit(function (e) {
            var me = $(this);
            e.preventDefault();
            var data = me.serialize();
            me.find("input").attr("disabled", "disabled");
            me.find("button").attr("disabled", "disabled");
            if (hideAlert != null)
                hideAlert();
            $.ajax({
                url: me.attr("action"),
                method: me.attr("method"),
                data: data,
                success: function (data) {
                    if (data.success) {
                        loadPage("login");
                        showAlert($("#successAccountCreated"), 3000);
                    } else {
                        if (data.msg == "already_logged_in") {
                            loadPage("passwords");
                        } else if (data.msg == "missing_arguments") {
                            showAlert($("#errorFormInvalid"), 3000);
                        } else if (data.msg == "passwords_not_match") {
                            showAlert($("#errorPasswordsNotMatch"), 3000);
                        } else if (data.msg == "verification_failed") {
                            showAlert($("#errorVerification"), 3000);
                        } else if (data.msg == "invalid_email") {
                            showAlert($("#errorEmailInvalid"), 3000);
                        } else if (data.msg == "already_registered") {
                            showAlert($("#errorAccountRegistered"), 3000);
                        } else if (startsWith(data.msg, "database_")) {
                            showAlert($("#errorDatabase"), 3000);
                        }
                    }
                    me[0].reset();
                    me.find("input").change();
                    me.find("input").attr("disabled", null);
                    me.find("button").attr("disabled", null);
                    grecaptcha.reset();
                },
                error: function () {
                    showAlert($("#errorRegisterServer"), 3000);
                    me[0].reset();
                    me.find("input").change();
                    me.find("input").attr("disabled", null);
                    me.find("button").attr("disabled", null);
                    grecaptcha.reset();
                }
            })
        });

        formEditPass.on("mouseenter", function () {
            $(this).attr("type", "text");
        });

        formEditPass.on("mouseleave", function () {
            $(this).attr("type", "password");
        });


        $("#btnAdd").click(function (e) {
            e.preventDefault();
            $("#modalAdd").modal('toggle');
        });

        $("#btnLogout").click(function (e) {
            e.preventDefault();
            logout();
        });

        $("#aRefresh").click(function (e) {
            e.preventDefault();
            refresh();
        });

        $("#formAddPassword").submit(function (e) {
            var me = $(this);
            e.preventDefault();
            var btn = me.find("button");
            btn.attr("disabled", "");
            $.ajax({
                url: me.attr("action"),
                method: me.attr("method"),
                data: me.serialize(),
                success: function (data) {
                    btn.attr("disabled", null);
                    if (data.success) {
                        me.find("input").val("");
                        me.find("input.hastext").removeClass("hastext");
                        refresh();
                        $(".modal.fade.in").modal("hide");
                    } else {
                        if (data.msg == "no_login") {
                            sessionExpired();
                            return;
                        }
                        if (startsWith(data.msg, "database_")) {
                            showAlert($("#errorDatabase"), 3000)
                        } else {
                            showAlert($("#errorUnknown"), 3000)
                        }
                    }
                }
            })
        });

        $("#formEditPassword").submit(function (e) {
            var me = $(this);
            e.preventDefault();
            var btn = me.find("button");
            btn.attr("disabled", "");
            $.ajax({
                url: me.attr("action"),
                method: me.attr("method"),
                data: me.serialize(),
                success: function (data) {
                    btn.attr("disabled", null);
                    if (data.success) {
                        me.find("input").val("");
                        me.find("input").change();
                        refresh();
                        $(".modal.fade.in").modal("hide");
                    } else {
                        if (data.msg == "no_login") {
                            sessionExpired();
                            return;
                        }
                        if (startsWith(data.msg, "database_")) {
                            showAlert($("#errorEditDatabase"), 3000)
                        } else {
                            showAlert($("#errorEditUnknown"), 3000)
                        }
                    }
                }
            })
        });

        $("#formChangePassword").submit(function (e) {
            var me = $(this);
            e.preventDefault();
            var btn = me.find("button");
            btn.attr("disabled", "");
            $.ajax({
                url: me.attr("action"),
                method: me.attr("method"),
                data: me.serialize(),
                success: function (data) {
                    btn.attr("disabled", null);
                    if (data.success) {
                        me.find("input").val("");
                        me.find("input").change();
                        logout();
                    } else {
                        if (data.msg == "no_login") {
                            sessionExpired();
                            return;
                        }
                        if (startsWith(data.msg, "database_")) {
                            showAlert($("#errorChangePasswordDatabase"), 3000)
                        } else {
                            showAlert($("#errorChangePasswordUnknown"), 3000)
                        }
                    }
                }
            })
        });

        $("#formChangeEmail").submit(function (e) {
            var me = $(this);
            e.preventDefault();
            var btn = me.find("button");
            btn.attr("disabled", "");
            $.ajax({
                url: me.attr("action"),
                method: me.attr("method"),
                data: me.serialize(),
                success: function (data) {
                    btn.attr("disabled", null);
                    if (data.success) {
                        me.find("input").val("");
                        me.find("input").change();
                        logout();
                    } else {
                        if (data.msg == "no_login") {
                            sessionExpired();
                            return;
                        }
                        if (startsWith(data.msg, "database_")) {
                            showAlert($("#errorChangeEmailDatabase"), 3000)
                        } else {
                            showAlert($("#errorChangeEmailUnknown"), 3000)
                        }
                    }
                }
            })
        });

        //PASSWORD ACTIONS

        passwordTable.on('click', '*[data-password-action="show"]', function (e) {
            var me = $(this), passwordId = me.data("password-id"), parent = me.parent();
            e.preventDefault();
            me.attr("disabled", "");
            me.html(spinnerSVGSmall);
            $.ajax({
                url: "backend/getPassword.php",
                method: "post",
                data: "id=" + encodeURIComponent(passwordId),
                success: function (data) {
                    if (data.success) {
                        parent.html("<span class='selectable no-contextmenu'>" + data.data.password + "</span>");
                        timeoutPassword(parent, passwordId);
                    } else {
                        if (data.msg == "no_login") {
                            sessionExpired();
                            return;
                        }
                        me.html("<i class='material-icons'>error</i>")
                    }
                }
            })
        });
        passwordTable.on('click', '*[data-password-action="edit"]', function (e) {
            var me = $(this), passwordId = me.data("password-id");
            e.preventDefault();
            me.attr("disabled", "");
            me.html(spinnerSVGSmall);
            $.ajax({
                url: "backend/getPassword.php",
                method: "post",
                data: "id=" + encodeURIComponent(passwordId),
                success: function (data) {
                    if (data.success) {
                        me.html("<i class='material-icons'>edit</i>");
                        me.attr("disabled", null);
                        var password = data.data.password;
                        password = password.replace(/&lt;/g, '<').replace(/&gt;/g, '>').replace(/&amp;/g, '&');
                        $("#formEditPasswordId").val(passwordId);
                        $("#formEditPasswordPassword").val(password).change();
                        $("#formEditPasswordUsername").val(data.data.username).change();
                        $("#formEditPasswordWebsite").val(data.data.website).change();
                        $("#modalEdit").modal("show");
                    } else {
                        if (data.msg == "no_login") {
                            sessionExpired();
                            return;
                        }
                        me.html("<i class='material-icons'>error</i>")
                    }
                },
                error: function () {
                    me.html("<i class='material-icons'>error</i>")
                }
            })

        });
        passwordTable.on('click', '*[data-password-action="share"]', function (e) {
            e.preventDefault();
            alert("Not implemented yet!");
        });
        passwordTable.on('click', '*[data-password-action="archive"]', function (e) {
            var me = $(this), passwordId = me.data("password-id");
            e.preventDefault();
            me.attr("disabled", "");
            me.html(spinnerSVGSmall);
            $.ajax({
                url: "backend/archivePassword.php",
                method: "post",
                data: "id=" + encodeURIComponent(passwordId),
                success: function (data) {
                    if (data.success) {
                        refresh();
                    } else {
                        if (data.msg == "no_login") {
                            sessionExpired();
                            return;
                        }
                        me.html("<i class='material-icons'>error</i>")
                    }
                },
                error: function () {
                    me.html("<i class='material-icons'>error</i>")
                }
            })
        });
        archivedPasswordTable.on('click', '*[data-password-action="restore"]', function (e) {
            var me = $(this), passwordId = me.data("password-id");
            e.preventDefault();
            me.attr("disabled", "");
            me.html(spinnerSVGSmall);
            $.ajax({
                url: "backend/restorePassword.php",
                method: "post",
                data: "id=" + encodeURIComponent(passwordId),
                success: function (data) {
                    if (data.success) {
                        refresh();
                    } else {
                        if (data.msg == "no_login") {
                            sessionExpired();
                            return;
                        }
                        me.html("<i class='material-icons'>error</i>")
                    }
                },
                error: function () {
                    me.html("<i class='material-icons'>error</i>")
                }
            })
        });
        archivedPasswordTable.on('click', '*[data-password-action="delete"]', function (e) {
            var me = $(this), passwordId = me.data("password-id");
            e.preventDefault();
            me.attr("disabled", "");
            me.html(spinnerSVGSmall);
            $.ajax({
                url: "backend/deletePassword.php",
                method: "post",
                data: "id=" + encodeURIComponent(passwordId),
                success: function (data) {
                    if (data.success) {
                        refresh();
                    } else {
                        if (data.msg == "no_login") {
                            sessionExpired();
                            return;
                        }
                        me.html("<i class='material-icons'>error</i>")
                    }
                },
                error: function () {
                    me.html("<i class='material-icons'>error</i>")
                }
            })
        });
    }


    //##################################################################################################################
    //PAGE SPECIFIC METHODS
    //##################################################################################################################

    function timeoutPassword(passwordObject, passwordId) {
        var timeLeft = 60;
        passwordObject.append(" <span id='timeLeft_" + passwordId + "' class='text-muted'></span>");
        var timeLeftDisplay = passwordObject.find("#timeLeft_" + passwordId);

        var timer = function () {
            timeLeftDisplay.html(timeLeft);
            if (timeLeft == 10) {
                timeLeftDisplay.addClass("text-danger");
                timeLeftDisplay.removeClass("text-muted");
            } else if (timeLeft == 0) {
                clearInterval(timerId);
                passwordObject.html("<a class='btn btn-default btn-flat btn-block' data-password-action='show' data-password-id='" + passwordId + "'><i class='material-icons'>remove_red_eye</i></a>");
            }
            timeLeft--;
        };
        timer();

        var timerId = setInterval(timer, 1000);
    }

    function fetchPasswords(callbackDone) {
        var tableBody = $("#tbodyPasswords");
        var tableArchivedBody = $("#tbodyArchivedPasswords");
        $.ajax({
            url: "backend/getPasswords.php",
            success: function (data) {
                if (data.success) {
                    var jsonData = data.data, tbody = "", tbodyArchived = "";
                    $.each(jsonData, function (index, item) {
                        var website = "<i>None</i>";
                        if (item.website != null) {
                            website = "<a href='" + item.website + "' target='_blank'>" + item.website + "</a>";
                        }

                        var username = "<i>None</i>";
                        if (item.username != null) {
                            username = item.username;
                        }

                        var row = "<tr id='" + item.password_id + "'>";
                        if (!item.archived) {
                            //Passwords page
                            row += "<td><span class='selectable no-contextmenu'> " + username + "</span></td>";
                            row += "<td><button class='btn btn-default btn-flat btn-block' data-password-action='show' data-password-id='" + item.password_id + "'><i class='material-icons'>remove_red_eye</i></button></td>";
                            row += "<td>" + website + "</td>";
                            row += "<td>" + item.date_added_nice + "</td>";
                            row += "<td>" +
                                "<button class='btn btn-default btn-flat btn-sm' data-password-action='edit' data-password-id='" + item.password_id + "'>" +
                                "<i class='material-icons'>edit</i>" +
                                "</button>" +
                                "<button class='btn btn-default btn-flat btn-sm' data-password-action='share' data-password-id='" + item.password_id + "'>" +
                                "<i class='material-icons'>share</i>" +
                                "</button>" +
                                "<button class='btn btn-default btn-flat btn-sm' data-password-action='archive' data-password-id='" + item.password_id + "'>" +
                                "<i class='material-icons'>archive</i>" +
                                "</button>" +
                                "</td>";
                            row += "</tr>";
                            tbody += row;
                        } else {
                            //Archived page
                            row += "<td><span class='selectable no-contextmenu'> " + username + "</span></td>";
                            row += "<td><button class='btn btn-default btn-flat btn-block' disabled='disabled'><i class='material-icons'>remove_red_eye</i></button></td>";
                            row += "<td>" + website + "</td>";
                            row += "<td>" + item.date_archived_nice + "</td>";
                            row += "<td><button class='btn btn-default btn-flat btn-sm' data-password-action='restore' data-password-id='" + item.password_id + "'><i class='material-icons'>unarchive</i></button><a class='btn btn-default btn-flat btn-sm' data-password-action='delete' data-password-id='" + item.password_id + "'><i class='material-icons'>delete</i></a></td>";
                            row += "</tr>";
                            tbodyArchived += row;
                        }
                    });
                    if (tbody.length == 0) {
                        tbody = "<tr><td>Empty</td><td></td><td></td><td></td><td></td></tr>";
                    }
                    if (tbodyArchived.length == 0) {
                        tbodyArchived = "<tr><td>Empty</td><td></td><td></td><td></td><td></td></tr>";
                    }
                    tableBody.html(tbody);
                    tableArchivedBody.html(tbodyArchived);
                    if (callbackDone != null)
                        callbackDone();
                } else {
                    if (data.msg == "no_login") {
                        sessionExpired();
                        return;
                    }

                    tableBody.html("<tr><td>Error: " + data.msg + "</td><td></td><td></td><td></td><td></td></tr>");
                    if (callbackDone != null)
                        callbackDone(data.msg);
                }
            },
            error: function (xhr, error) {
                tableBody.html("<tr><td>Error: " + error + "</td><td></td><td></td><td></td><td></td></tr>");
                if (callbackDone != null)
                    callbackDone(error);
            }
        })
    }

    function fetchIPLog(callbackDone) {
        var tableBody = $("#tbodyLoginHistory");
        $.ajax({
            url: "backend/getIPLog.php",
            success: function (data) {
                if (data.success) {
                    var jsonData = data.data, tbody = "";
                    $.each(jsonData, function (index, item) {
                        var row = "<tr>";

                        var location = "";
                        if (item.city != "")
                            location += item.city + ", ";
                        if (item.region != "")
                            location += item.region + ", ";
                        if (item.country != "")
                            location += item.country;


                        row += "<td><span>" + item.ip + "</span></td>";
                        row += "<td><span>" + location + "</span></td>";
                        row += "<td><span>" + item.user_agent + "</span></td>";
                        row += "<td><span>" + item.date_nice + "</span></td>";
                        row += "</tr>";

                        tbody += row;
                    });
                    if (tbody.length == 0) {
                        tbody = "<tr><td>Empty</td><td></td><td></td><td></td></tr>";
                    }
                    tableBody.html(tbody);
                    if (callbackDone != null)
                        callbackDone();
                } else {
                    if (data.msg == "no_login") {
                        sessionExpired();
                        return;
                    }

                    tableBody.html("<tr><td>Error: " + data.msg + "</td><td></td><td></td><td></td></tr>");
                    if (callbackDone != null)
                        callbackDone(data.msg);
                }
            },
            error: function (xhr, error) {
                tableBody.html("<tr><td>Error: " + error + "</td><td></td><td></td><td></td></tr>");
                if (callbackDone != null)
                    callbackDone(error);
            }
        })
    }

})();
