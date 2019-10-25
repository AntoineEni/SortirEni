$("[id^=action_]").on("click", launchRequest);

function launchRequest() {
    var url = $(this).attr("data-url");
    var isAjax = $(this).attr("data-ajax");

    if (isAjax === "true") {
        $.ajax({
            method: "POST",
            url: url,
            beforeSend: function () {
                $("#loader").removeClass("d-none");
            },
            success: function (jsonResponse) {
                if (jsonResponse.ok) {
                    Swal.fire({
                        type: "success",
                        title: jsonResponse.response,
                        showConfirmButton: false,
                        timer: 1500,
                    }).then((result) => {
                        window.location.reload();
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
                    title: "Something went wrong",
                    text: "Please try later",
                });
            },
            complete: function () {
                $("#loader").addClass("d-none");
            }
        });
    } else {
        window.location.href = url;
    }
}

$('#table').DataTable( {
    language: {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
    },
    responsive: true,
    scrollCollapse: true,
    paging: false,
    recherche:false,
    searching: false,
} );

