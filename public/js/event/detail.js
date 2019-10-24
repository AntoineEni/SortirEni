$("#inscription").on("click", inscrireUserToEvent);

function inscrireUserToEvent() {
    var url = $(this).attr("data-url");
    $.ajax({
        method: "POST",
        url: url,
        beforeSend: function() {
            $("#loader").removeClass("d-none");
        },
        success: function(jsonResponse) {
            if (jsonResponse.ok) {
                Swal.fire({
                    type: "success",
                    title: "Inscription réussie",
                    showConfirmButton: false,
                    timer: 1500,
                });
            } else {
                Swal.fire({
                    type: "error",
                    title: jsonResponse.errors,
                });
            }
        },
        error: function(jsonReponse) {
            Swal.fire({
                type: "error",
                title: "Something went wrong",
                text: "Please try later",
            });
        },
        complete: function() {
            $("#loader").addClass("d-none");
        }
    });
}