(function() {
    var currentPage = "login", switchingPage = false;

    $(document).ready(function () {
        currentPage = getCurrentPage();
        loadPage(currentPage);
        registerListeners();
        registerPageListeners();
    });

    function getCurrentPage() {
        var anchor = location.href.substring(location.href.indexOf("#"));
        if (anchor.substring(0, 4) === "#!p=" && anchor.length > 1) {
            return anchor.substring(4);
        }
        return currentPage;
    }

    function loadPage(page) {
        if (switchingPage)
            return;
        switchingPage = true;
        var oldPage = $("#page_" + currentPage), newPage = $("#page_" + page);
        currentPage = page;

        $("*[data-page-highlight]").each(function (index, elem) {
            elem = $(elem);
            if (elem.attr("data-page-highlight") == page) {
                elem.addClass("active");
            } else {
                elem.removeClass("active");
            }
        });

        oldPage.fadeOut(300, function () {
            newPage.fadeIn(300);
            switchingPage = false;
        });
    }

    function registerPageListeners() {
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
            $.ajax({
                url: me.attr("action"),
                method: me.attr("method"),
                data: me.serialize(),
                success: function (data) {
                    if(data.success) {
                        location.replace("manage/");
                    } else {
                        if(data.msg == "already_logged_in") {
                            location.replace("manage/");
                        } else if(data.msg == "missing_arguments") {
                            showAlert($("#errorLoginFormInvalid"), 3000);
                        } else if(data.msg == "account_locked") {
                            showAlert($("#errorAccountLocked"), 3000);
                        } else if(data.msg == "invalid_email") {
                            showAlert($("#errorLoginEmailInvalid"), 3000);
                        } else if(data.msg == "invalid_credentials") {
                            showAlert($("#errorInvalidCredentials"), 3000);
                        } else if(startsWith(data.msg, "database_")) {
                            showAlert($("#errorLoginDatabase"), 3000);
                        }
                    }
                },
                error: function () {
                    showAlert($("#errorLoginServer"), 3000);
                }
            })
        });

        $("#registerForm").submit(function (e) {
            var me = $(this);
            e.preventDefault();
            hideAllAlerts();
            $.ajax({
                url: me.attr("action"),
                method: me.attr("method"),
                data: me.serialize(),
                success: function (data) {
                    if(data.success) {
                        loadPage("login");
                        showAlert($("#successAccountCreated"), 3000);
                    } else {
                        if (data.msg == "already_logged_in") {
                            location.replace("manage/");
                            return;
                        } else if (data.msg == "missing_arguments") {
                            showAlert($("#errorFormInvalid"), 3000);
                        } else if (data.msg == "verification_failed") {
                            showAlert($("#errorVerificationFailed"), 3000);
                        } else if (data.msg == "invalid_email") {
                            showAlert($("#errorEmailInvalid"), 3000);
                        } else if (data.msg == "already_registered") {
                            showAlert($("#errorAccountRegistered"), 3000);
                        } else if (startsWith(data.msg, "database_")) {
                            showAlert($("#errorDatabase"), 3000);
                        }
                        grecaptcha.reset();
                    }
                },
                error: function () {
                    showAlert($("#errorRegisterServer"), 3000);
                }
            })
        })
    }
})();
