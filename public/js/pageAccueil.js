$(document).ready(function() {
    //Manage the datatable filters
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var min = $('#event_filter_dateMin').datepicker("getDate");
            var max = $('#event_filter_dateMax').datepicker("getDate");
            var date = data[1].split('/');
            var startDate = new Date(Date.UTC(date[2], date[1]-1, date[0], 0, 0, 0))

            if (min == null && max == null) { return true; }
            if (min == null && startDate <= max) { return true; }
            if (max == null && startDate >= min) { return true; }
            if (startDate <= max && startDate >= min) { return true; }
            return false;
        },
        function (settings, data, dataIndex) {
            var organisateur = $('#event_filter_organisateur').is(':checked');
            var inscrit = data[6];
            if (!organisateur) { return true; }
            if (inscrit == $('#event_filter_organisateur').attr('value') && organisateur) { return true; }

            return false;
        },
        function (settings, data, dataIndex) {
            var inscritC = $('#event_filter_inscrit').is(':checked');
            var inscrit = data[5];
            if (!inscritC) { return true; }
            if (inscrit == 'X' && inscritC) { return true; }

            return false;
        },
        function (settings, data, dataIndex) {
            var Ninscrit = $('#event_filter_nInscrit').is(':checked');
            var inscrit = data[5];
            var organisateur = data[6];
            if (!Ninscrit) { return true; }
            else if (inscrit !== 'X' && organisateur !== $('#event_filter_organisateur').attr('value')) { return true; }

            return false;
        },
        function (settings, data, dataIndex) {
            var datePasser = $('#event_filter_finie').is(':checked');
            var inscrit = new Date(data[2]);
            var date = new Date();
            if (!datePasser) { return true; }
            if (inscrit >= date && datePasser) { return true; }

            return false;
        },
        function (settings, data, dataIndex) {
            var filtreSiteVal = $('#event_filter_site option:selected').val();
            var filtreSiteLabel = $('#event_filter_site option:selected').text();
            var inscrit = data[8];
            if (filtreSiteVal === null || typeof filtreSiteVal === "undefined" || filtreSiteVal === "") { return true; }
            if (filtreSiteLabel != false && inscrit == filtreSiteLabel ) {  return true; }

            return false;
        },
    );

    //Init the datatable
    var table = $('#tableAccueil').DataTable( {
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
        },
        responsive: true,
        "columnDefs": [
            {
                "targets": [ 8 ],
                "visible": false,
            },
            {
                "targets": [ 7 ],
                responsivePriority: 1,
            },
        ],
    } );

    //Disable some checkbox if some conditions verified
    $("#event_filter_inscrit").change(function() {
        $("#event_filter_nInscrit").prop("checked", false);
        $("#event_filter_organisateur").prop("checked", false);
    });

    $("#event_filter_nInscrit").change(function() {
        $("#event_filter_inscrit").prop("checked", false);
        $("#event_filter_organisateur").prop("checked", false);
    });

    $("#event_filter_organisateur").change(function() {
        $("#event_filter_inscrit").prop("checked", false);
        $("#event_filter_nInscrit").prop("checked", false);
    });

    // Event listener to the two range filtering inputs to redraw on input
    $('#event_filter_dateMin, #event_filter_dateMax, #event_filter_organisateur,#event_filter_inscrit,#event_filter_nInscrit,#event_filter_finie,#event_filter_site').change(function () {
        table.draw();
    });

});
