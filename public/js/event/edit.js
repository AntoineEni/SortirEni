$(document).ready(function() {
    dateDebut = new Date(JSON.parse(dateDebut).date);
    dateCloture = new Date(JSON.parse(dateCloture).date);

    $("#event_heureDebut_hour").val(dateDebut.getHours());
    $("#event_heureDebut_minute").val(dateDebut.getMinutes());
    $("#event_heureCloture_hour").val(dateCloture.getHours());
    $("#event_heureCloture_minute").val(dateCloture.getMinutes());
});