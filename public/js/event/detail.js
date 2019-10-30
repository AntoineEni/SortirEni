//For all button of type action, bind to redirectToGoodFunction
$("[id^=action_]").on("click", redirectToGoodFunction);

/**
 * When click on action button, redirect to :
 * - specific function if exists
 * - launchRequest if doesn't
 */
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

/**
 * When publish an event, start by ask a confirmation
 * @param url : string URL to call
 * @param isAjax : string says if the action is in ajax or not
 */
function publish(url, isAjax) {
    confirmAndRequest(url, isAjax, "publish");
}

/**
 * When remove an event, start by ask a confirmation
 * @param url : string URL to call
 * @param isAjax : string says if the action is in ajax or not
 */
function remove(url, isAjax) {
    confirmAndRequest(url, isAjax, "remove");
}

/**
 * Ask a confirmation before redirect or call Ajax function
 * @param url : string URL to call
 * @param isAjax : string says if the action is in ajax or not
 * @param type : string specify the action type for the translation
 */
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

/**
 * Redirect to page or start an ajax call to specific url
 * @param url : string URL to call
 * @param isAjax : string says if the action is in ajax or not
 */
function launchRequest(url, isAjax) {
    if (isAjax === "true") {
        //Start ajax call
        $.ajax({
            method: "POST",
            url: url,
            beforeSend: function () {
                //Show the loader
                $("#loader").removeClass("d-none");
            },
            success: function (jsonResponse) {
                if (jsonResponse.ok) {
                    //If everything OK
                    Swal.fire({
                        type: "success",
                        title: jsonResponse.response,
                        showConfirmButton: false,
                        timer: 1500,
                    }).then((result) => {
                        window.location.reload();
                    });
                } else {
                    //If the ajax return an error
                    Swal.fire({
                        type: "error",
                        title: jsonResponse.response,
                        text: Translator.trans("app.trylater"),
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
            complete: function () {
                //Hide the loader
                $("#loader").addClass("d-none");
            }
        });
    } else {
        //If not ajax, redirect to URL
        window.location.href = url;
    }
}

/**
 * Init datatable
 */
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

