$(document).ready(function() {
    //Bind the modal confirm button to closeModalArticle13 function
    $("#confirmArticle13Modal").on("click", closeModalArticle13);

    //If user never said yes to cookie, ask him
    if ($.cookie("article13") === null || typeof $.cookie("article13") === "undefined") {
        $("#modalArticle13").modal("show");
    }
});

/**
 * When user agreed to use cookie, save it into a cookie
 */
function closeModalArticle13() {
    $("#confirmArticle13Modal").html("<i class=\"fas fa-spinner fa-spin\"></i>");
    $("#confirmArticle13Modal").prop("disabled", true);

    $.cookie("article13", "OK", {
        expires: 395, //13 months in days
        path    : '/',
    });

    $("#modalArticle13").modal("hide");
}