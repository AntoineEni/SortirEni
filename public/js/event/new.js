//Manage the location adding in Ajax
$("#modalAddLocation").on("submit", "form", function(e) {
    //Disable automatic form send
    e.preventDefault();

    $.ajax({
        method: "POST",
        url: $("#submitFormLocation").attr("data-url"),
        data: $("#formAddLocation").serialize(),
        beforeSend: function () {
            //Show the loader
            $("#loader").removeClass("d-none");
        },
        success: function (jsonResponse) {
            if (jsonResponse.ok) {
                //If everything OK
                var location = jsonResponse.location;
                Swal.fire({
                    type: "success",
                    title: jsonResponse.response,
                    showConfirmButton: false,
                    timer: 1500,
                }).then((result) => {
                    //Add the location into the select and set it as selected
                    $("#event_lieu").append(new Option(location[1], location[0]));
                    $("#modalAddLocation").modal("hide");
                    $("#event_lieu option[value=" + location[0] + "]").attr('selected','selected');
                    $("#formAddLocation").trigger("reset");
                });
            } else {
                //If an error has been encountered
                Swal.fire({
                    type: "error",
                    title: jsonResponse.response,
                });
            }
        },
        error: function (jsonReponse) {
            //If server error
            Swal.fire({
                type: "error",
                title: Translator.trans("app.baderror"),
                text: Translator.trans("app.trylater"),
            });
        },
        complete: function() {
            //Hide the loader
            $("#loader").addClass("d-none");
        }
    });
});