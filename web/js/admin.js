/***********************************************************************************************************************
/   navbar/general.html.twig
/**********************************************************************************************************************/

$(function(){
    $.ajax({
        url: "{{ path('race_countNotSupervised') }}",
        dataType: "json",
        success: function (data) {
            badgeCountRace(data);
        }
    });
});

function badgeCountRace(nb) {
    var badge = $('#badge');
    badge.text(nb);

    if(nb == 0){
        badge.attr({
            "title" : "Pas de nouvelle course à valider",
            "class" : "badge badge-success"
        });
    }else {
        badge.attr({
            "title" : "Nouvelles courses à valider",
            "class" : "badge badge-warning"
        });
    }
}
