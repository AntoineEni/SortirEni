$(document).ready(function() {
    $.fn.dataTable.ext.search.push(
        function (settings, data, dataIndex) {
            var min = $('#min').datepicker("getDate");
            var max = $('#max').datepicker("getDate");
            var startDate = new Date(data[1]);
            if (min == null && max == null) { return true; }
            if (min == null && startDate <= max) { return true; }
            if (max == null && startDate >= min) { return true; }
            if (startDate <= max && startDate >= min) { return true; }
            return false;
        },
        function (settings, data, dataIndex) {
            var organisateur = $('#organisateur').is(':checked');
            var inscrit = data[6];
            if (!organisateur) { return true; }
            if (inscrit == $('#organisateur').attr('value') && organisateur) { return true; }

            return false;
        },
        function (settings, data, dataIndex) {
            var inscritC = $('#inscrit').is(':checked');
            var inscrit = data[5];
            if (!inscritC) { return true; }
            if (inscrit == 'X' && inscritC) { return true; }

            return false;
        },
        function (settings, data, dataIndex) {
            var Ninscrit = $('#Ninscrit').is(':checked');
            var inscrit = data[5];
            if (!Ninscrit) { return true; }
            if (inscrit != 'X' && Ninscrit) { return true; }

            return false;
        },
        function (settings, data, dataIndex) {
            var datePasser = $('#passer').is(':checked');
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
        "columnDefs": [
            {
                "targets": [ 8 ],
                "visible": false,
            },
        ],
    } );
// Event listener to the two range filtering inputs to redraw on input
    $('#min, #max, #organisateur,#inscrit,#Ninscrit,#passer,#event_filtre_site').change(function () {
        table.draw();
    });


} );