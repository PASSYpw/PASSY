(function () {
    var currentPage = "passwords", switchingPage = false;

    $(document).ready(function () {
        currentPage = getCurrentPage();
        loadPage(currentPage);
        registerPageListeners();
        registerListeners();
    });

    function getCurrentPage() {
        var anchor = location.href.substring(location.href.indexOf("#"));
        if (anchor.substring(0, 4) === "#!p=" && anchor.length > 1) {
            return anchor.substring(4);
        }
        return currentPage;
    }

    function registerPageListeners() {
        var passwordTable = $('#tbodyPasswords'), archivedPasswordTable = $('#tbodyArchivedPasswords');

        $("*[data-to-page]").click(function (e) {
            var me = $(this), toPage = me.attr("data-to-page");
            e.preventDefault();
            if (toPage == "refresh")
                toPage = currentPage;
            loadPage(toPage);
        });
        $(document).on("keydown", function (e) {
            if ((e.which || e.keyCode) == 116) {
                e.preventDefault();
                refresh();
            }
        });


        $("#btnAdd").click(function (e) {
            e.preventDefault();
            $("#modalAdd").modal('toggle');
        });

        $("#btnLogout").click(function (e) {
            e.preventDefault();
            logout();
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
                        if (startsWith(data.msg, "database_")) {
                            showAlert($("#errorDatabase"), 3000)
                        } else {
                            showAlert($("#errorUnknown"), 3000)
                        }
                    }
                }
            })
        });
        $("#aRefresh").click(function (e) {
            e.preventDefault();
            refresh();
        });

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
                        parent.html("<span class='selectable no-contextmenu'>" + data.data.password + "</span>")
                    } else {
                        me.html("<i class='material-icons'>error</i>")
                    }
                }
            })
        });
        passwordTable.on('click', '*[data-password-action="edit"]', function (e) {
            e.preventDefault();
            alert("Not implemented yet!");
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
                        me.html("<i class='material-icons'>error</i>")
                    }
                },
                error: function () {
                    me.html("<i class='material-icons'>error</i>")
                }
            })
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

    function loadPage(page, callback) {
        if (switchingPage)
            return;
        switchingPage = true;
        var oldPage = $("#page_" + currentPage), newPage = $("#page_" + page), spinner = $(".load-spinner");
        currentPage = page;

        spinner.addClass("shown");

        var show = function () {
            $("*[data-page-highlight]").each(function (index, elem) {
                elem = $(elem);
                if (elem.attr("data-page-highlight") == page) {
                    elem.addClass("active");
                } else {
                    elem.removeClass("active");
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

    function logout() {
        $.ajax({
            url: "backend/logout.php",
            success: function () {
                location.replace("../");
            }
        })
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
                        var website = "";
                        if (item.website == null) {
                            website = "<i>None</i>";
                        } else {
                            website = "<a href='" + item.website + "' target='_blank'>" + item.website + "</a>";
                        }
                        var row = "<tr id='" + item.password_id + "'>";
                        if (!item.archived) {
                            row += "<td><span class='selectable no-contextmenu'> " + item.username + "</span></td>";
                            row += "<td><a class='btn btn-default btn-flat btn-block' data-password-action='show' data-password-id='" + item.password_id + "'><i class='material-icons'>remove_red_eye</i></a></td>";
                            row += "<td>" + website + "</td>";
                            row += "<td>" + item.date_added_nice + "</td>";
                            row += "<td><a class='btn btn-default btn-flat btn-sm' data-password-action='edit' data-password-id='" + item.password_id + "'><i class='material-icons'>edit</i></a><a class='btn btn-default btn-flat btn-sm' data-password-action='share' data-password-id='" + item.password_id + "'><i class='material-icons'>share</i></a><a class='btn btn-default btn-flat btn-sm' data-password-action='archive' data-password-id='" + item.password_id + "'><i class='material-icons'>archive</i></a></td>";
                            row += "</tr>";
                            tbody += row;
                        } else {
                            row += "<td><span class='selectable no-contextmenu'> " + item.username + "</span></td>";
                            row += "<td><a class='btn btn-default btn-flat btn-block' disabled='disabled'><i class='material-icons'>remove_red_eye</i></a></td>";
                            row += "<td>" + website + "</td>";
                            row += "<td>" + item.date_archived_nice + "</td>";
                            row += "<td><a class='btn btn-default btn-flat btn-sm' data-password-action='restore' data-password-id='" + item.password_id + "'><i class='material-icons'>unarchive</i></a><a class='btn btn-default btn-flat btn-sm' data-password-action='delete' data-password-id='" + item.password_id + "'><i class='material-icons'>delete</i></a></td>";
                            row += "</tr>";
                            tbodyArchived += row;
                        }
                    });
                    if(tbody.length == 0) {
                        tbody = "<tr><td>Empty</td><td></td><td></td><td></td><td></td></tr>";
                    }
                    if(tbodyArchived.length == 0) {
                        tbodyArchived = "<tr><td>Empty</td><td></td><td></td><td></td><td></td></tr>";
                    }
                    tableBody.html(tbody);
                    tableArchivedBody.html(tbodyArchived);
                    if (callbackDone != null)
                        callbackDone();
                } else {
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
                        if(item.city != "")
                            location += item.city + ", ";
                        if(item.region != "")
                            location += item.region + ", ";
                        if(item.country != "")
                            location += item.country;


                        row += "<td><span>" + item.ip + "</span></td>";
                        row += "<td><span>" + location + "</span></td>";
                        row += "<td><span>" + item.user_agent + "</span></td>";
                        row += "<td><span>" + item.date_nice + "</span></td>";
                        row += "</tr>";

                        tbody += row;
                    });
                    if(tbody.length == 0) {
                        tbody = "<tr><td>Empty</td><td></td><td></td><td></td></tr>";
                    }
                    tableBody.html(tbody);
                    if (callbackDone != null)
                        callbackDone();
                } else {
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
