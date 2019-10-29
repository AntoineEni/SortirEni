$("#modalAddLocation").on("submit", "form", function(e) {
    //Disable automatic form send
    e.preventDefault();

    //Create the new location object
    var newLocation = {
        name: $("#location_name").val(),
        street: $("#location_street").val(),
        latitude: $("#location_latitude").val(),
        longitude: $("#location_longitude").val(),
        city: $("#location_city").val(),
    };

    $.ajax({
        method: "POST",
        url: $("#submitFormLocation").attr("data-url"),
        data: newLocation,
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
        complete: function () {
            $("#modalAddLocation").modal("hide");
            $("#loader").addClass("d-none");
        }
    });
});