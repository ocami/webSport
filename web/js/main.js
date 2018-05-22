/***********************************************************************************************************************
 /   navbar/general.html.twig
 /**********************************************************************************************************************/
$(function () {
    var li = document.createElement("LI");

    $.getJSON("/webSport/web/json/navbar_championship.json", function (result) {
        $.each(result, function (key, championship) {
            var a = document.createElement("A");
            var textLI = document.createTextNode(championship.name);
            a.href = championship.path;
            a.appendChild(textLI);
            li.appendChild(a);
            $('#championdhips-menu').append(li);

        });

    });
});

function navbarBadgeAdmin() {
    var path = Routing.generate('race_countNotSupervised');

    $.ajax({
        url: path,
        dataType: "json",
        success: function (data) {
            badgeCountRace(data);
        }
    });

    function badgeCountRace(nb) {
        var badge = $('#badge');
        badge.text(nb);

        if (nb == 0) {
            badge.attr({
                "title": "Pas de nouvelle course à valider",
                "class": "badge badge-success"
            });
        } else {
            badge.attr({
                "title": "Nouvelles courses à valider",
                "class": "badge badge-warning"
            });
        }
    }
};

/***********************************************************************************************************************
 /   modal/profile_competitor.html.twig
 /**********************************************************************************************************************/
function navbarCompetitorProfile() {
    var $profile = $('#profile-href');

    $profile.click(function () {
        var user = $(this).attr("data");
        var path = Routing.generate('competitor_json_userId', {userId: user});

        $.ajax({
            url: path,
            success: function (data) {
                profilData(data);
            }
        });
    });

    function profilData(data) {
        $('#profile-name').text(data.firstName + ' ' + data.lastName);
        $('#profile-category').text(data.category);
        $('#profile-age').text(data.age + ' ans');
    }
};

/***********************************************************************************************************************
 /   admin/races.html.twig
 /
 / Where table select change
 /   - call function views/race/show.html.twig raceShow([idRace])
 /
 / isRaceValid / isRaceInChampionship
 /    - ajax  update
 /**********************************************************************************************************************/
function adminRaces() {
    var $raceShow = $('#admin-race-show');
    var raceSeclected;
    var indexSelected;
    var rowData;
    var cb = $('#cb-Championship');

    //initialize///////////////////////////////////////////////////////////////////////////////////////
    cb.checkboxradio(); //jquery-ui


    var table = $('#admin-races-table').DataTable({
        "info": false,
        "language": {
            "search": "Rechercher:",
            "lengthMenu": "Voir _MENU_ courses  par page",
            "zeroRecords": "Aucune courses non traitées",
            "paginate": {
                "first": "Premier",
                "last": "Dernier",
                "next": "Suivant",
                "previous": "précédent"
            },
        },

        select: {
            style: 'single',
            selector: 'td:first-child'
        },
        columns: [
            {data: 'checkbox'},
            {data: 'id'},
            {data: 'name'},
            {data: 'organizer'},
            {data: 'city'},
            {data: 'date'},
            {data: 'championship'}
        ],
        columnDefs: [
            {
                targets: 0,
                orderable: false,
                className: 'select-checkbox',
                width: "5%"
            },
            {
                targets: 1,
                visible: false
            },
            {
                targets: 5,
                width: "20%"
            },
            {
                targets: 6,
                width: "5%"
            }
        ]
    }); //dataTables

    if (table.data().count()) {
        tableSelectRow(0);
        preRaceShow(0);
        $raceShow.show();
        loaderStop();
    } else {
        $raceShow.hide();
        loaderStop();
    }

    //Event///////////////////////////////////////////////////////////////////////////////////////
    table.on('select', function (e, dt, type, index) {
        loaderDivStart($raceShow);
        preRaceShow(index);
    });

    $('#prev').click(function () {
        tableSelectRow(indexSelected + 1);
    });

    $('#next').click(function () {
        tableSelectRow(indexSelected - 1);
    });

    cb.click(function () {
        if (this.checked)
            cbChampionship(true);
        else
            cbChampionship(false);
    })

    $('#valid').click(function () {
        loaderDivStart($raceShow);

        var inChampionship = 0;

        if (cb.prop('checked'))
            inChampionship = 1;

        raceUpdate(raceSeclected, 1, inChampionship);
    });

    $('#refuse').click(function () {
        loaderDivStart($raceShow);

        raceUpdate(raceSeclected, 0, 0);
    });

    //functions///////////////////////////////////////////////////////////////////////////////////////

    function preRaceShow(index) {

        if (table.data().count()) {
            rowData = table.rows(index).data().toArray()[0];
            raceSeclected = rowData['id'];
            indexSelected = table.row(index).index();

            cbChampionshipRefresh();

            raceShow(raceSeclected); //views/race/show.html.twig
        }
    }

    function raceUpdate(raceSeclected, valid, inChampionship) {
        $.ajax({
            url: "{{ path('race_admin_supervise') }}",
            data: {race: raceSeclected, valid: valid, inChampionship: inChampionship},
            success: function (data) {
                table.row(indexSelected).remove().draw();
                tableSelectRow(0);
                preRaceShow(0);
                updateNbRace();
            }
        });
    }

    function updateNbRace() {
        $.ajax({
            url: "{{ path('race_countNotSupervised') }}",
            dataType: "json",
            success: function (data) {
                badgeCountRace(data);
            }
        });
    }


    //functions///////////////////////////////////////////////////////////////////////////////////////

    function cbChampionshipRefresh() {
        $('#cbChampionship').remove();

        if (rowData['championship'])
            cbChampionship(true);
        else
            cbChampionship(false);
    }

    function cbChampionship(is) {
        if (is)
            cb.prop('checked', true).checkboxradio('refresh');
        else
            cb.prop('checked', false).checkboxradio('refresh');
    }

    function tableSelectRow(index) {
        table.row(':eq(' + index + ')', {page: 'all'}).select();
    }
};

/***********************************************************************************************************************
 /   competition/showList.html.twig
 /**********************************************************************************************************************/
function competitionShowList() {

    var btnCompetitionPassed = $('#competitionDisplayButton');
    var competitionsNoPassed = $('#competitionsNoPassed');
    var competitionsPassed = $('#competitionsPassed');

    btnCompetitionPassed.click(function () {
        if (btnCompetitionPassed.val() == 'future')
            competitionDisplayNoPassed();
        else
            competitionDisplayPassed();
    });

    function competitionDisplayPassed() {
        btnCompetitionPassed.text("Compétition à venir");
        btnCompetitionPassed.val('future');
        competitionsNoPassed.css('display', "none");
        competitionsPassed.css('display', "block");
    }

    function competitionDisplayNoPassed() {
        btnCompetitionPassed.text("Compétition passées");
        btnCompetitionPassed.val('passed');
        competitionsNoPassed.css('display', "block");
        competitionsPassed.css('display', "none");
    }
};

/***********************************************************************************************************************
 /   competition\new.html.twig
 /**********************************************************************************************************************/
function competitionFormDates() {
    var inputDateStart = $('#appbundle_competition_dateStart');
    var inputDateEnd = $('#appbundle_competition_dateEnd');
    var dpStart = $('#dpStart');
    var dpEnd = $('#dpEnd');

    $('.datepicker').datepicker({
        language: 'fr',
        format: 'dd-mm-yyyy',
        todayBtn: 'linked',
        autoclose: true,
    });

    if( inputDateStart.val().length !== 0 )
        dpStart.datepicker('update', parseDateUsToFr(inputDateStart.val()));

    if( inputDateEnd.val().length !== 0 )
        dpEnd.datepicker('update', parseDateUsToFr(inputDateEnd.val()));

    dpStart.on('changeDate', function () {
        var startDate = parseDateFrToUs(dpStart.datepicker('getFormattedDate'));
        inputDateStart.val(startDate);
        inputDateStart.trigger('change');
        dpEnd.datepicker(
            'setStartDate', dpStart.datepicker('getDate')
        );
    });

    dpEnd.on('changeDate', function () {
        var startDate = parseDateFrToUs(dpEnd.datepicker('getFormattedDate'));
        inputDateEnd.val(startDate);
        inputDateEnd.trigger('change');
        dpStart.datepicker(
            'setEndDate', dpEnd.datepicker('getDate')
        );
    });
}

//$locationForm plug-in//////////////////////////////////////////////////////////////////////////////////////////////////////
(function ( $ ) {

    var $output;

    var $locationContainer = $("<div class='col-xs-offset-1 col-xs-6'>");
    var $locationAlert = $("<div id='location_alert' class='alert alert-warning' role='alert' style='display: none'></div>");
    var $locationLoader = $("<div class='loader-div'></div>");
    var $locationForm = $("<div id='location_form' class='form-group'>");
    var $locationLabel = $("<label id='location_label'>Département</label>");
    var $locationInput = $("<input id='location_input' class='form-control' placeholder='Ex : 06 Alpes-Maritimes'>");
    var $locationBtnNextStep = $("<button id='next_step_btn' class='col-xs-2  btn btn-primary btn-sm' value='depValidate'>Suivant</button>");
    var $locationShow = $("<div id='location-show' class='col-xs-5'></div>");

    $.fn.locationForm = function(options) {

        var defaults= {"update": false};
        var parameters = $.extend(defaults, options);

        $output = parameters.output;

        $locationForm.append([$locationLabel,$locationInput]);
        $locationContainer.append([$locationAlert,$locationLoader,$locationForm,$locationBtnNextStep,$locationShow]);
        this.append([$locationContainer, $locationShow]);

        if( $output.val().length !== 0 ){
            locationUpdate($.parseJSON($output.val()));
            return this;
        }

        depAutocomplete();

        return this;
    };

    //Initialize**************************************************************
    var depCode;
    var dep;
    var cityCode;
    var locationData;
    var locationDataIsValide = false;
    var locationMap;


    $locationInput.on('keyup', function (e) {
        if (e.keyCode === 13) {
            $locationBtnNextStep.trigger('click');
        }
    });


    //Step**************************************************************
    function nextStep(step) {
        switch (step) {
            case 'dep' :
                depAutocomplete();
                $locationLabel.text('Département');
                $locationInput.attr("placeholder", "Ex : 06 Alpes-Maritimes");
                $locationBtnNextStep.val('depValidate');
                loaderDivStop($locationForm);
                break;

            case 'depValidate' :
                loaderDivStart($locationForm);
                depValidate();
                break;

            case 'city' :
                cityAutocomplete(depCode);
                $locationLabel.text('Ville');
                $locationInput.attr("placeholder", "Ex : nice");
                $locationBtnNextStep.val('cityValidate');
                break;

            case 'cityValidate' :
                loaderDivStart($locationForm);
                cityValidate();
                break;

            case 'address' :
                addressAutocomplete(cityCode);
                $locationLabel.text('Adresse');
                $locationInput.attr("placeholder", "Ex : 5 Promenade des Anglais");
                $locationBtnNextStep.val('addressValidate');
                loaderDivStop($locationForm);
                break;

            case 'addressValidate' :
                addressValidate();
                break;

            case 'confirm' :
                $locationForm.hide();
                $locationBtnNextStep.hide();
                locationConfirm();
                break;
        }

        $locationInput.focus();
    }

    function returnStep(step) {
        $locationInput.val('');
        $locationForm.show();
        $locationBtnNextStep.show();
        locationDataIsValide = false;

        switch (step) {
            case 'dep' :
                $('#show_dep').remove();
                depCode = '';

            case  'city' :
                $('#show_city').remove();
                cityCode = '';

            case  'address' :
                $('#show_address').remove();
                locationData = '';
                if (typeof locationMap !== 'undefined')
                    locationMap.remove();
        }
        nextStep(step);
        $output.val('');
        $output.trigger('change');
    }

    //Dep**************************************************************
    function depAutocomplete() {
        $.get('/webSport/web/locationForm/departements.json', function (data, status) {
            $locationInput.autocomplete({
                source: data,
                messages: {
                    noResults: '',
                    results: function() {}
                },
                focus: function (event, ui) {
                    $(".ui-helper-hidden-accessible").hide();
                }
            });


          /*  $locationInput.autocomplete({
                source: data
            })*/
        });
    }

    function depValidate() {
        dep = $locationInput.val();
        depCode = dep.slice(0, 2);
        $locationAlert.hide();

        $.get("/webSport/web/locationForm/departements.json", function (data, status) {
            if ($.inArray( $locationInput.val(), data) !== -1) {
                addShowElement($locationInput.val(), 'dep');
                $locationInput.val('');
                nextStep('city');
            } else {
                nextStep('dep');
                $locationAlert.text('Veuillez sélectionner un département dans la liste');
                $locationAlert.show();
            }
        });
    }

    //City*************************************************************
    function cityAutocomplete(dep) {
        var path = Routing.generate('address_getCitiesSlugByDep', {dep: dep});
        $.ajax({
            url: path,
            success: function (data) {
                var cities = [];
                $.map(data, function (item) {
                    cities.push(item.villeSlug);
                });

                $locationInput.autocomplete({
                    source: cities,
                    messages: {
                        noResults: '',
                        results: function() {}
                    },
                    focus: function (event, ui) {
                        $(".ui-helper-hidden-accessible").hide();
                    }
                });

                loaderDivStop($locationForm);
                $locationInput.focus();
            }
        });
    }

    function cityValidate() {
        var citySlug = $locationInput.val();
        $locationInput.val('');
        $locationAlert.hide();

        $.ajax({
            url: Routing.generate('address_getCitiesData', {ville_slug: citySlug}),
            dataType: "json",
            success: function (data) {
                var cityData = data[0];

                if (data.length > 0 && citySlug == cityData.villeSlug) {
                    addShowElement(cityData.villeNomReel, 'city');
                    cityCode = cityData.villeCodeCommune;
                    nextStep('address');
                } else {
                    nextStep('city');
                    $locationAlert.text('Veuillez sélectionner une commune dans la liste');
                    $locationAlert.show();
                }
            }
        });

    }

    //Address**********************************************************
    function addressAutocomplete(cityCode) {
        $locationInput.autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: "https://api-adresse.data.gouv.fr/search/?citycode=" + cityCode,
                    data: {q: request.term},
                    dataType: "json",
                    success: function (data) {
                        response($.map(data.features, function (item) {
                            var truc = {label: item.properties.name, value: item.properties.name};
                            return truc;
                        }));
                        autocompleteAddressList = data.features;
                    }
                });
            },
            messages: {
                noResults: '',
                results: function() {}
            },
            focus: function (event, ui) {
                $(".ui-helper-hidden-accessible").hide();
            }
        });
    }

    function addressValidate() {
        $.ajax({
            url: "https://api-adresse.data.gouv.fr/search/?citycode=" + cityCode,
            data: {q: $locationInput.val()},
            dataType: "json",
            success: function (data) {
                if (data.features.length > 0 && data.features[0].properties.name == $locationInput.val()) {
                    var locationFeatures = data.features[0];
                    addShowElement(locationFeatures.properties.name, 'address');
                    setLocationData(locationFeatures);
                    $locationAlert.hide();
                    nextStep('confirm');
                } else {
                    $locationAlert.text('Veuillez sélectionner une adresse dans la liste');
                    $locationAlert.show();
                    nextStep('address');
                }
            }
        });
    }

    //Confirm**********************************************************
    function locationConfirm() {
        locationMap = $("<div>", {id: "location-map"});
        $locationForm.after(locationMap);
        openLocationMap(locationData.x, locationData.y, 'location-map');

        locationDataIsValide = true;


        $output.val(JSON.stringify(locationData));
        $output.trigger('change');
    }

    //Events************************************************************
    $locationShow.on('click', 'button', function (evt) {
        returnStep($(this).val());
    });

    $locationBtnNextStep.click(function () {
        nextStep($(this).val());
    });

    //Other************************************************************

    function addShowElement(text, step) {

        var div = $("<div>", {id: "show_" + step, class: "form_show row "});
        var txt = $("<p>", {class: "col-xs-7"}).append(text);
        var btn = $("<button>", {
            class: "col-xs-2 edit_bt btn btn-success btn-xs",
            value: step
        }).append('modifier');

        div.append(txt);
        div.append(btn);

        $locationShow.append(div);
    }

    function setLocationData(locationFeatures) {
        var f = locationFeatures;
        locationData = {
            id: f.properties.id,
            street: f.properties.name,
            depCode : depCode,
            dep: dep,
            postCode: f.properties.postcode,
            cityCode: cityCode,
            city: f.properties.city,
            x: f.geometry.coordinates[1],
            y: f.geometry.coordinates[0]
        }
    }

    function locationUpdate(data) {

        $locationBtnNextStep.hide();
        $locationForm.hide();

        depCode = data.depCode;
        cityCode = data.cityCode;

        addShowElement(data.dep, 'dep');
        addShowElement(data.city, 'city');
        addShowElement(data.street, 'address');

        locationMap = $("<div>", {id: "location-map"});
        $locationForm.after(locationMap);
        openLocationMap(data.x, data.y, 'location-map');


        locationDataIsValide = true;
    }

    // map leaflet**************************************************************************************************>
    function openLocationMap(x, y, mapId) {
        var map = L.map(mapId, {
            center: [x, y],
            zoom: 18,
        });
        L.marker([x, y]).addTo(map);
        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
            maxZoom: 18
        }).addTo(map);
    }

}( jQuery ));

//Tools//////////////////////////////////////////////////////////////////////////////////////////////////////
function parseDateFrToUs(date) {

    var d = new Date(date.split("-").reverse().join("-"));
    var dd = ('0' + d.getDate()).slice(-2);
    var mm = d.getMonth() + 1;
    var mm = ('0' + mm).slice(-2);
    var yy = d.getFullYear();
    var newdate = yy + "-" + mm + "-" + dd;

    return newdate;
}

function parseDateUsToFr(date) {

    var d = new Date(date);
    var dd = ('0' + d.getDate()).slice(-2);
    var mm = d.getMonth() + 1;
    var mm = ('0' + mm).slice(-2);
    var yy = d.getFullYear();
    var newdate = dd + "-" + mm + "-" + yy;

    return newdate;
}

// $(document).ready(function () {
//     $('table.display').DataTable();
// });
//
//
// function displayDate() {
//     document.getElementById("demo").innerHTML = Date();
// }







