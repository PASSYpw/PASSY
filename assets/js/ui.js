var spinnerSVG = '<svg class="spinner" width="65px" height="65px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg>';
var spinnerSVGSmall = '<svg class="spinner" width="20px" height="20px" viewBox="0 0 66 66" xmlns="http://www.w3.org/2000/svg"><circle class="path" fill="none" stroke-width="6" stroke-linecap="round" cx="33" cy="33" r="30"></circle></svg>';
var rippleSettings = {
    debug: false,
    on: 'mousedown',
    opacity: 0.3,
    color: "auto",
    multi: true,
    duration: 0.3,
    easing: 'linear'
};

function registerListeners() {

    $.ripple(".nav > li > a", rippleSettings);
    $.ripple(".btn:not([disabled])", rippleSettings);

    var inputs = $(".text > input");

    if (inputs.val().length > 0)
        me.addClass("hastext");

    inputs.on("input", function () {
        var me = $(this);
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

    var contextMenu = $("#dropdownContextMenu");
    $("body").mouseup(function (e) {
        if (e.which == 1)
            contextMenu.removeClass("open");
    });

    $(this).bind("contextmenu", function (e) {
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

    var lastHeight = 0;

    $(window).scroll(function () {
        $(".dropdown.open").find(".dropdown-toggle").dropdown("toggle");
        $(".contextmenu.open").removeClass("open");

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

var currentAlert, hideAlert;

function showAlert(object, timeout) {
    if (hideAlert != null)
        hideAlert();
    clearInterval(currentAlert);
    object.fadeIn(100, function () {
        var me = $(this);
        hideAlert = function () {
            me.fadeOut(100);
        };
        currentAlert = setTimeout(hideAlert, timeout)
    });
}

function startsWith(haystack, needle) {
    return haystack.substr(0, needle.length) == needle;
}

function endsWith(haystack, needle) {
    return haystack.substr(needle.length, haystack.length) == needle;
}
