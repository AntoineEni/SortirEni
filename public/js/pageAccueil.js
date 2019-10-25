$(document).ready(function() {
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var min = $('#event_filtre_dateMin').datepicker("getDate");
            var max = $('#event_filtre_dateMax').datepicker("getDate");
            var date = data[1].split('/');
            var startDate = new Date(Date.UTC(date[2], date[1]-1, date[0], 0, 0, 0))

            if (min == null && max == null) { return true; }
            if (min == null && startDate <= max) { return true; }
            if (max == null && startDate >= min) { return true; }
            if (startDate <= max && startDate >= min) { return true; }
            return false;
        },
        function (settings, data, dataIndex) {
            var organisateur = $('#event_filtre_organisateur').is(':checked');
            var inscrit = data[6];
            if (!organisateur) { return true; }
            if (inscrit == $('#event_filtre_organisateur').attr('value') && organisateur) { return true; }

            return false;
        },
        function (settings, data, dataIndex) {
            var inscritC = $('#event_filtre_inscrit').is(':checked');
            var inscrit = data[5];
            if (!inscritC) { return true; }
            if (inscrit == 'X' && inscritC) { return true; }

            return false;
        },
        function (settings, data, dataIndex) {
            var Ninscrit = $('#event_filtre_nInscrit').is(':checked');
            var inscrit = data[5];
            if (!Ninscrit) { return true; }
            if (inscrit != 'X' && Ninscrit) { return true; }

            return false;
        },
        function (settings, data, dataIndex) {
            var datePasser = $('#event_filtre_finie').is(':checked');
            var inscrit = new Date(data[2]);
            var date = new Date();
            if (!datePasser) { return true; }
            if (inscrit >= date && datePasser) { return true; }

            return false;
        },
        function (settings, data, dataIndex) {
            var filtreSite =$('#event_filtre_site option:selected').text();
            var inscrit = data[8];
            if (filtreSite == 'Sélectionnez un lieu') { return true; }
            if (filtreSite != false && inscrit == filtreSite ) {  return true; }

            return false;
        },
    );

    var table = $('#table').DataTable( {
        language: {
            "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/French.json"
        },
        responsive: true,
        "columnDefs": [
            {
                "targets": [ 8 ],
                "visible": false,
            },
        ],
    } );

    // Event listener to the two range filtering inputs to redraw on input
    $('#event_filtre_dateMin, #event_filtre_dateMax, #event_filtre_organisateur,#event_filtre_inscrit,#event_filtre_nInscrit,#event_filtre_finie,#event_filtre_site').change(function () {
        table.draw();
    });

} );