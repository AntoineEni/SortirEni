$("#modalAddLocation").on("submit", "form", function(e) {
    //Disable automatic form send
    e.preventDefault();

    $.ajax({
        method: "POST",
        url: $("#submitFormLocation").attr("data-url"),
        data: $("#formAddLocation").serialize(),
        beforeSend: function () {
            $("#loader").removeClass("d-none");
        },
        success: function (jsonResponse) {
            if (jsonResponse.ok) {
                var location = jsonResponse.location;
                Swal.fire({
                    type: "success",
                    title: jsonResponse.response,
                    showConfirmButton: false,
                    timer: 1500,
                }).then((result) => {
                    $("#event_lieu").append(new Option(location[1], location[0]));
                    $("#modalAddLocation").modal("hide");
                    $("#event_lieu option[value=" + location[0] + "]").attr('selected','selected');
                });
            } else {
                Swal.fire({
                    type: "error",
                    title: jsonResponse.response,
                });
            }
        },
        error: function (jsonReponse) {
            Swal.fire({
                type: "error",
                title: Translator.trans("app.baderror"),
                text: Translator.trans("app.trylater"),
            });
        },
        complete: function() {
            $("#loader").addClass("d-none");
        }
    });
});