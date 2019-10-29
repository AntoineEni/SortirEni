$("[id^=action_]").on("click", redirectToGoodFunction);

function redirectToGoodFunction() {
    var id = $(this).attr("id");
    var url = $(this).attr("data-url");
    var isAjax = $(this).attr("data-ajax");

    var fn =  window[id.split("_")[1]];

    if (typeof fn === "function") {
        fn.apply(null, [url, isAjax]);
    } else {
        launchRequest(url, isAjax);
    }
}

function publish(url, isAjax) {
    confirmAndRequest(url, isAjax, "publish");
}

function remove(url, isAjax) {
    confirmAndRequest(url, isAjax, "remove");
}

function cancel(url, isAjax) {
    console.log('verif');
    confirmAndRequest(url, isAjax, "cancel");
}

function confirmAndRequest(url, isAjax, type) {
    $("#loader").removeClass("d-none");
    Swal.fire({
        type: "warning",
        title: Translator.trans("event.detail.swal." + type + ".title"),
        text: Translator.trans("event.detail.swal." + type + ".text"),
        allowOutsideClick: false,
        showCancelButton: true,
        cancelButtonText: Translator.trans("app.cancel"),
    }).then((result) => {
        if (result.value) {
            launchRequest(url, isAjax);
        } else {
            $("#loader").addClass("d-none");
        }
    });
}

function launchRequest(url, isAjax) {
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
                    title: Translator.trans("app.baderror"),
                    text: Translator.trans("app.trylater"),
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

$('#table').DataTable({
    language: {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json",
    },
    responsive: true,
    scrollCollapse: true,
    paging: false,
    recherche: false,
    searching: false,
    info: false,
});

