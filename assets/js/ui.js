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


function registerListeners() {
    $("#btnAdd").click(function (e) {
        e.preventDefault();
        $("#modalAdd").modal('toggle');
    });

    $("#btnRefresh").click(function (e) {
        e.preventDefault();
        var me = $(this), icon = me.find(".material-icons");
        icon.addClass("spin");
        me.addClass("disabled");
        me.attr("disabled", "");
        setTimeout(function () {
            icon.removeClass("spin");
            me.removeClass("disabled");
            me.attr("disabled", null);
        }, 3300);
    });
    $("a[data-to-page]").click(function (e) {
        var me = $(this);
        e.preventDefault();
        loadPage(me.attr("data-to-page"));
    });
    $("#tableDownloads").find("tbody").find("tr").dblclick(function (e) {
        e.preventDefault();
        $("#modalDLInfo").modal('toggle');
    });

    var word = "";

    $(document).keydown(function (e) {
        word += e.key;
        if (e.key == "r") {
            $("#btnRefresh").click();
        }
        if (e.key == "+") {
            $("#btnAdd").click();
        }
        if (word == "helloworld") {
            alert("hello too! :)");
        }
    });

    var contextMenu = $("#dropdownContextMenu");
    $(document).click(function () {
        contextMenu.removeClass("open");
    });

    $(this).bind("contextmenu", function (e) {
        e.preventDefault();
        var x = e.clientX, y = e.clientY;
        contextMenu.removeClass("open");
        setTimeout(function () {
            contextMenu.css({transform: "translate(" + x + "px, " + y + "px)"});
            contextMenu.addClass("open");
        }, 10);
    });
}
