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
    $("#btnRefresh").click(function (e) {
        e.preventDefault();
        refresh();
    });

    var passwordTable = $('#tbodyPasswords');

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
                    parent.html("<span class='selectable'>" + data.data.password + "</span>")
                } else {
                    me.html("<i class='material-icons'>error</i>")
                }
            }
        })
    });
    passwordTable.on('click', '*[data-password-action="edit"]', function (e) {
        /*TODO: Implement editing
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
         parent.html("<span class='selectable'>" + data.data.password + "</span>")
         } else {
         me.html("<i class='material-icons'>error</i>")
         }
         }
         })*/
        alert("Not implemented yet!");
    });
    passwordTable.on('click', '*[data-password-action="delete"]', function (e) {
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


    oldPage.fadeOut(300, function () {
        if (page == "passwords") {
            $.ajax({
                url: "backend/getPasswords.php",
                success: function (data) {
                    var tableBody = $("#tbodyPasswords");
                    if (data.success) {
                        var jsonData = data.data, tbody = "";
                        $.each(jsonData, function (index, item) {
                            var website = "";
                            if (item.website == null) {
                                website = "<i>None</i>";
                            } else {
                                website = "<a href='" + item.website + "'>" + item.website + "</a>";
                            }

                            var row = "<tr>";
                            row += "<td>" + item.username + "</td>";
                            row += "<td><a class='btn btn-default btn-flat btn-block' data-password-action='show' data-password-id='" + item.password_id + "'><i class='material-icons'>remove_red_eye</i></a></td>";
                            row += "<td>" + website + "</td>";
                            row += "<td>" + item.date_added_nice + "</td>";
                            row += "<td><a class='btn btn-default btn-flat btn-sm' data-password-action='edit' data-password-id='" + item.password_id + "'><i class='material-icons'>edit</i></a><a class='btn btn-default btn-flat btn-sm' data-password-action='delete' data-password-id='" + item.password_id + "'><i class='material-icons'>delete</i></a></td>";
                            row += "</tr>";
                            tbody += row;
                        });
                        tableBody.html(tbody);
                    }


                    $("*[data-page-highlight]").each(function (index, elem) {
                        elem = $(elem);
                        if (elem.attr("data-page-highlight") == page) {
                            elem.addClass("active");
                        } else {
                            elem.removeClass("active");
                        }
                    });
                    newPage.fadeIn(300);
                    switchingPage = false;
                    if (callback != null)
                        callback();
                }
            })
        }
    });
}