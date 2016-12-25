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
    registerListeners();
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
