
$(function () {

    // clean modal data force every time load newest data from server.
    $("#pageModal").on("hidden.bs.modal", function() {
        $(this).removeData("bs.modal");
    });

    $("body").on("change", ".action-status-select", function () {
        var url = $(this).val();
        $(this).blur();
        $.get(url);
    });


    $("#sync-link").click(function(){
        if ("disabled" == $(this).attr("disabled")) {
            return false;
        }

        var url = $(this).attr("href");

        $(this).children("i").addClass("fa-spin");
        $(this).attr("href", "#");
        $(this).attr("disabled", true);

        //Ajax post
        $.get(url, function (dt) {
            location.reload();
        });

        return false;
    });
});