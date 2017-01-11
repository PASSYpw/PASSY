var currentPage = "login", switchingPage = false;

$(document).ready(function () {

    applyCurrentPage();
    loadPage(currentPage);
    registerListeners();
    registerPageListeners();
});

function applyCurrentPage() {
    var anchor = window.location.href.substring(window.location.href.indexOf("#"));
    if (anchor.substring(0, 4) === "#!p=" && anchor.length > 1) {
        currentPage = anchor.substring(4);
    }
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
    $("#loginForm").submit(function (e) {
        var me = $(this);
        e.preventDefault();
        $.ajax({
            url: me.attr("action"),
            method: me.attr("method"),
            data: me.serialize(),
            success: function (data) {
                if (data == "success") {
                    location.replace("/manage/");
                } else if(data == "logged_in") {
                    location.replace("/manage/");
                }
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
                if (data == "success") {
                    loadPage("login");
                    showAlert($("#successAccountCreated"), 2000);
                } else if (data == "already_exists") {
                    showAlert($("#errorAccountRegistered"), 2000);
                } else if(data == "invalid_email") {
                    showAlert($("#errorEmailInvalid"), 2000);
                } else if(data == "logged_in") {
                    location.replace("/manage/");
                } else {
                    showAlert($("#errorFormInvalid"), 2000);
                }
            }
        })
    })
}
