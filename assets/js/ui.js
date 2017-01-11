function registerListeners() {
    var rippleSettings = {
        debug: false,
        on: 'mousedown',
        opacity: 0.3,
        color: "auto",
        multi: true,
        duration: 0.3,
        rate: function (pxPerSecond) {
            return pxPerSecond;
        },
        easing: 'linear'
    };

    $.ripple(".nav > li > a", rippleSettings);
    $.ripple(".btn", rippleSettings);

    $(".text > input").on("input", function () {
        var me = $(this);
        if(me.val().length > 0)
            me.addClass("hastext");
        else
            me.removeClass("hastext");
    });

    $("*[data-to-page]").click(function (e) {
        var me = $(this), toPage = me.attr("data-to-page");
        e.preventDefault();
        if(toPage == "refresh")
            toPage = currentPage;
        loadPage(toPage);
    });

    var contextMenu = $("#dropdownContextMenu");
    $(document).mousedown(function (e) {
        if (e.which == 1)
            contextMenu.removeClass("open");
    });

    $(this).bind("contextmenu", function (e) {
        if(e.shiftKey)
            return;
        e.preventDefault();
        var x = e.clientX, y = e.clientY;
        contextMenu.removeClass("open");
        setTimeout(function () {
            contextMenu.css({transform: "translate(" + x + "px, " + y + "px)"});
            contextMenu.addClass("open");
        }, 10);
    });

    var lastHeight = 0;

    $(window).scroll(function (e) {
        var navbar = $(".navbar-fixed-top"),
            scrollTop = $(document).scrollTop(),
            firstHeight = navbar.children().first().outerHeight();
        if (scrollTop < firstHeight) {
            navbar.css({transform: "translateY(-" + scrollTop + "px)"});
            lastHeight = scrollTop;
        }
        if (scrollTop >= firstHeight) {
            navbar.css({transform: "translateY(-" + firstHeight + "px)"});
        }
    });
}

function showAlert(object, timeout) {
    object.fadeIn(200, function () {
        var me = $(this);
        setTimeout(function () {
            me.fadeOut(200);
        }, timeout)
    });
}

function hideAllAlerts() {
    $("#errorAccountRegistered").fadeOut(200);
    $("#successAccountCreated").fadeOut(200);
}

function startsWith(haystack, needle) {
    return haystack.substr(0, needle.length) == needle;
}

function endsWith(haystack, needle) {
    return haystack.substr(needle.length, haystack.length) == needle;
}
