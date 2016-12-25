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
    $(document).mousedown(function (e) {
        if (e.which == 1)
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
