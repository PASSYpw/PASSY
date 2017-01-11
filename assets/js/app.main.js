var currentPage = "passwords", switchingPage = false;

$(document).ready(function () {
    var rippleSettings = {
        debug: false,
        on: 'mousedown',
        opacity: 0.41,
        color: "auto",
        multi: true,
        duration: 0.4,
        rate: function (pxPerSecond) {
            return pxPerSecond;
        },
        easing: 'linear'
    };

    $.ripple(".nav > li > a", rippleSettings);
    $.ripple(".btn-flat", rippleSettings);
    applyCurrentPage();
    loadPage(currentPage);
    registerPageListeners();
    registerListeners();
});

function applyCurrentPage() {
    var anchor = window.location.href.substring(window.location.href.indexOf("#"));
    if (anchor.substring(0, 4) === "#!p=" && anchor.length > 1) {
        currentPage = anchor.substring(4);
    }
}

function registerPageListeners() {
    $("#btnAdd").click(function (e) {
        e.preventDefault();
        $("#modalAdd").modal('toggle');
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
                if (startsWith(data, "pass_")) {
                    refresh();
                    $(".modal.fade.in").modal("hide");
                } else if(startsWith(data, "database_error")) {
                    showAlert($("#errorDatabase"), 3000)
                } else {
                    showAlert($("#errorUnknown"), 3000)
                }
            }
        })
    });
    $("#btnRefresh").click(function (e) {
        e.preventDefault();
        refresh();
    });
    $('#tbodyPasswords').on('click', '*[data-password-id]', function (e) {
        var me = $(this), passwordId = me.data("password-id");
        e.preventDefault();
        $.ajax({
            url: "backend/getPassword.php",
            method: "post",
            data: "id=" + encodeURIComponent(passwordId),
            success: function (data) {
                alert(data);
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
    var oldPage = $("#page_" + currentPage), newPage = $("#page_" + page);
    currentPage = page;

    if (page == "passwords") {
        $.ajax({
            url: "backend/getPasswords.php",
            success: function (data) {
                $("#tbodyPasswords").html(data);
            }
        })
    }

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
        if (callback != null)
            callback();
    });
}