var depCode;
var cityCode;
var locationData;

var locationAlert = $("#location_alert");
var locationLabel = $("#location_label");
var locationInput = $("#location_input");
var locationBtn = $(".next_step_btn");

$(document).ready(function () {
    nextStep('dep');
});

//Step**************************************************************
function nextStep(step) {
    switch (step) {

        case 'dep' :
            depAutocomplete();
            locationLabel.text('Département');
            locationInput.attr("placeholder", "Ex : 06 Alpes-Maritimes");
            locationBtn.val('depValidate');
            break;

        case 'depValidate' :
            depValidate();
            break;

        case 'city' :
            cityAutocomplete(depCode);
            locationLabel.text('Ville');
            locationInput.attr("placeholder", "Ex : nice");
            locationBtn.val('cityValidate');
            break;

        case 'cityValidate' :
            cityValidate();
            break;

        case 'address' :
            addressAutocomplete(cityCode);
            locationLabel.text('Adresse');
            locationInput.attr("placeholder", "Ex : 5 Promenade des Anglais");
            locationBtn.val('addressValidate');
            break;

        case 'addressValidate' :
            addressValidate();
            break;

        case 'confirm' :
            locationBtn.hide();
            $('#location_form .form-group').hide();
            locationConfirm();
            break;
    }
}

function returnStep(step) {
    locationInput.val('');
    $('#location_form .form-group').show();
    $('.location_map').remove();

    switch (step) {
        case 'dep' :
            $('.show_dep').remove();
            depCode = '';

        case  'city' :
            $('.show_city').remove();
            cityCode = '';

        case  'address' :
            $('.show_address').remove();
            locationData = '';
    }
    nextStep(step);
}

//Dep**************************************************************
function depAutocomplete() {
    $.get("../locationForm/departements2.json", function (data, status) {
        locationInput.autocomplete({
            source: data
        })
    });
}

function depValidate() {

    addShowElement(locationInput.val(), 'dep');
    depCode = locationInput.val().slice(0, 2);
    locationInput.val('');
    locationAlert.hide();

    $.get("../locationForm/departements3.json", function (data, status) {
        if (jQuery.inArray(depCode, data) !== -1) {
            nextStep('city');
        } else {
            nextStep('dep');
            locationAlert.text('Veuillez sélectionner un département dans la liste');
            locationAlert.show();
        }
    });
}

//City*************************************************************
function cityAutocomplete(dep) {
    $.ajax({
        url: "{{ path('address_getCitiesSlugByDep') }}",
        data: {dep: dep},
        dataType: "json",
        success: function (data) {
            var cities = [];
            $.map(data, function (item) {
                cities.push(item.villeSlug);
            });

            locationInput.autocomplete({
                source: cities
            })

        }
    });
}

function cityValidate() {
    var citySlug = locationInput.val();
    locationInput.val('');
    locationAlert.hide();

    $.ajax({
        url: "{{ path('address_getCitiesData') }}",
        data: {ville_slug: citySlug},
        dataType: "json",
        success: function (data) {
            var cityData = data[0];

            if (data.length > 0 && citySlug == cityData.villeSlug) {
                addShowElement(cityData.villeNomReel, 'city');
                cityCode = cityData.villeCodeCommune;
                nextStep('address');
            } else {
                nextStep('city');
                locationAlert.text('Veuillez sélectionner une commune dans la liste');
                locationAlert.show();
            }
        }
    });

}

//Address**********************************************************
function addressAutocomplete(cityCode) {
    locationInput.autocomplete({
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
        }
    });
}

function addressValidate() {
    $.ajax({
        url: "https://api-adresse.data.gouv.fr/search/?citycode=" + cityCode,
        data: {q: locationInput.val()},
        dataType: "json",
        success: function (data) {
            if (data.features.length > 0 && data.features[0].properties.name == locationInput.val()) {
                var locationFeatures = data.features[0];
                addShowElement(locationFeatures.properties.name, 'address');
                setLocationData(locationFeatures);
                nextStep('confirm');
            } else {
                locationAlert.text('Veuillez sélectionner une adresse dans la liste');
                locationAlert.hide();
                nextStep('address');
            }
        }
    });
}

//Confirm**********************************************************
function locationConfirm() {
    var map = document.createElement("div");
    map.className = "location_map";
    map.setAttribute('id', 'location_map');
    $('.next_step_btn').before(map);

    openLocationMap(locationData.x, locationData.y, 'location_map');
}

//Other************************************************************
function addShowElement(text, step) {
    var div = document.createElement("div");
    div.className = "form_show row show_" + step;
    var txt = document.createElement("p");
    txt.append(text);
    txt.className = "col-xs-7";
    var btn = document.createElement("button");
    btn.className = "col-xs-2 edit_bt btn btn-success btn-xs";
    btn.append('Modifier');

    btn.onclick = function () {
        returnStep(step);
    };

    div.append(txt);
    div.append(btn);

    $('#location_show').append(div);
}

function setLocationData(locationFeatures) {
    var f = locationFeatures;
    locationData = {
        id: f.properties.id,
        street: f.properties.name,
        postCode: f.properties.postcode,
        city: f.properties.city,
        x: f.geometry.coordinates[1],
        y: f.geometry.coordinates[0]
    }
}