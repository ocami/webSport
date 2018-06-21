var windowsSize = $(window).width();

//***********************************************/OTHER/***************************************************************/
/***********************************************************************************************************************
 /   admin/races.html.twig
 /
 / Where table select change
 /   - call function views/race/modelShow.html.twig raceShow([idRace])
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
                targets: 3,
                width: "5%"
            },
            {
                targets: 6,
                width: "20%"
            },
            {
                targets: 7,
                width: "3%"
            }
        ]
    }); //dataTables

    if (table.data().count()) {
        tableSelectRow(0);
        preRaceShow(0);
        $raceShow.show();
        loaderDivStart($raceShow);
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
        loaderDivStart($raceShow);
        tableSelectRow(indexSelected + 1);
    });

    $('#next').click(function () {
        loaderDivStart($raceShow);
        tableSelectRow(indexSelected - 1);
    });

    cb.click(function () {
        if (this.checked)
            cbChampionship(true);
        else
            cbChampionship(false);
    });

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

            raceShow(raceSeclected); //views/race/modelShow.html.twig
        } else {
            loaderDivStop($raceShow);
            $raceShow.hide();
        }
    }

    function raceShow(race) {
        var path = Routing.generate('race_json', {
            race: race
        });

        $.ajax({
            url: path,
            success: function (data) {
                hydrate(data);
            },
            error: function () {
                ajaxError();
            }
        });
    }

    function hydrate(data) {
        var date = data.race.dateTime.replace(/\-|\:/g, ' ');
        var date = date.split(" ");

        var date = new Date(Date.UTC(date[0], date[1], date[2], date[3] - 2, date[4], date[5]));

        $('#admin-race-name').text(data.race.name);
        $('#admin-race-competition-id').attr('href', data.race.organizerId);
        $('#admin-race-competition-organizer').text('Orga : ' + data.race.organizerName);
        $('#admin-race-competition-name').text(data.race.competitionName);
        $('#admin-race-distance').text(data.race.distance + ' Km');
        $('#admin-race-day').text(date.toLocaleDateString('fr-FR', {
            weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        }));
        $('#admin-race-hour').text(('0' + date.getHours()).slice(-2) + ' : ' + ('0' + date.getMinutes()).slice(-2));
        $('#admin-race-address').text(data.race.street);
        $('#admin-race-city').text(data.race.postCode + ' ' + data.race.city);

        var $categories = $('#admin-race-categories')
        $categories.empty();
        $.each(data.categories, function (index, value) {
            $categories.append(" <div class='col-xs-3 col-lg-2'><button class='btn-xs btn-info col-xs-12' disabled>" + value.name + "</button> </div>");
        });

        if (data.race.requestInChampionship)
            cb.prop('checked', true).checkboxradio('refresh');
        else
            cb.prop('checked', false).checkboxradio('refresh');


        loaderDivStop($raceShow);
    }

    function raceUpdate(raceSeclected, valid, inChampionship) {

        var path = Routing.generate('race_admin_supervise', {
            race: raceSeclected,
            valid: valid,
            inChampionship: inChampionship
        });

        $.ajax({
            url: path,
            success: function (data) {
                table.row(indexSelected).remove().draw();
                tableSelectRow(0);
                preRaceShow(0);
                updateNbRace();
            },
            error: function () {
                ajaxError();
            }
        });
    }

    function updateNbRace() {

        var path = Routing.generate('race_countNotSupervised');

        $.ajax({
            url: path,
            success: function (data) {
                navbarBadgeAdmin();
            },
            error: function () {
                ajaxError();
            }
        });
    }

    function tableSelectRow(index) {
        table.row(':eq(' + index + ')', {page: 'all'}).select();
    }
}

/***********************************************************************************************************************
 /   competition/showList.html.twig
 /**********************************************************************************************************************/
function competitionShowList() {

    var btnCompetitionPassed = $('#competition-list-button');
    var title = $('#competition-list-title');
    var futureCompetitions = $('#future-competition');
    var passedCompetitons = $('#passed-competition');

    //init
    panelCategoriesHeight();
    loaderStop();

    //event
    btnCompetitionPassed.click(function () {
        $('#competition-list-title-passed').hide();

        if (btnCompetitionPassed.val() == 'future')
            competitionDisplayNoPassed();
        else
            competitionDisplayPassed();
    });

    //function
    function competitionDisplayPassed() {
        btnCompetitionPassed.text("Compétitions à venir");
        title.text("Compétitions passées");
        btnCompetitionPassed.val('future');
        futureCompetitions.css('display', "none");
        passedCompetitons.css('display', "block");
    }

    function competitionDisplayNoPassed() {
        btnCompetitionPassed.text("Compétitions passées");
        title.text("Compétitions à venir");
        btnCompetitionPassed.val('passed');
        futureCompetitions.css('display', "block");
        passedCompetitons.css('display', "none");
    }
};

//***********************************************/TABLES/**************************************************************/
var language = {
    "search": "Rechercher:",
    "lengthMenu": "Voir _MENU_ compétiteurs  par page",
    "zeroRecords": "Aucune inscription",
    "paginate": {
        "first": "Premier",
        "last": "Dernier",
        "next": "Suivant",
        "previous": "précédent"
    }
};

/***********************************************************************************************************************
 /   race\show.html.twig
 /**********************************************************************************************************************/
function raceShow(race, inchampionship) {

    var $allBtnCategories = $('#race-show').find('button');
    var $btnCategories = $('#race-show-categories').find('button');
    var $collapseBtnCategories = $('#race-show-panel-categories').find('button');
    var $container = $('#show-race-tables');
    var rowTable;

    //init
    $(document).ready(function () {
        ranckTable('all');
    });

    //events
    $allBtnCategories.click(function () {
        $container.removeClass();
        ranckTable($(this).val());
    });

    $btnCategories.click(function () {
        $btnCategories.attr('class', 'btn-xs btn-info col-xs-12');
        $(this).attr('class', 'btn-xs btn-primary col-xs-12');
    });

    $collapseBtnCategories.click(function () {
        $("#collapse-race-categories").collapse('hide');
    });

    $('.profile-href').click(function () {
        var user = $(this).attr("data");
        profilData(user);
    });

    function ranckTable(category) {
        loaderDivStart($container);

        var path = Routing.generate('race_Table', {
            idRace: race,
            category: category
        });

        $.ajax({
            url: path,
            success: function (data) {
                displayTable(data, category);
            },
            error: function () {
                ajaxError();
            }
        });
    }

    function displayTable(data, category) {
        var options = {
            data: data.competitors,
            destroy: true,
            language: language,
            bInfo: false,
            order: [[0, "asc"]],
            responsive: true
        };

        if (category === 'all') {
            rowTable = $('#row-table-race');
            switch (data.race_state) {
                case 0:
                    raceOpenOptions();
                    break;

                case 1:
                    raceClosedOptions();
                    break;

                case 2:
                    racePassedOptions();
                    break;
            }
            $('#table-race-category-container').hide();
            $('#table-race-container').show();
            $('#table-race').dataTable(options);

        } else {
            rowTable = $('#row-table-race-category');
            switch (data.race_state) {
                case 0:
                    raceCategoryOpenOptions();
                    break;

                case 1:
                    raceCategoryCloseOptions();
                    break;

                case 2:
                    raceCategoryPassedOptions();
                    break;
            }
            $('#table-race-container').hide();
            $('#table-race-category-container').show();
            $('#table-race-category').dataTable(options);
        }

        loaderDivStop($container);
        $('html, body').animate({scrollTop: $($container).offset().top}, 750);

        function raceOpenOptions() {
            options.columns = [
                {
                    "data": "lastName",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.lastName + "</span>");
                    }
                },
                {
                    "data": "firstName",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.firstName + "</span>");
                    }
                },
                {data: 'category'}
            ];

            rowTable.empty();
            rowTable.append("<th>Nom</th>");
            rowTable.append("<th>Prénom</th>");
            rowTable.append("<th>Catégorie</th>");

            $container.addClass('col-lg-offset-2 col-lg-8');
        }

        function raceClosedOptions() {
            console.log('raceClosedOptions');

            options.columns = [
                {data: 'number', className: "row-ranck"},
                {
                    "data": "lastName",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.lastName + "</span>");
                    }
                },
                {
                    "data": "firstName",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.firstName + "</span>");
                    }
                },
                {data: 'category'},
            ];

            rowTable.empty();
            rowTable.append("<th>Dossard</th>");
            rowTable.append("<th>Nom</th>");
            rowTable.append("<th>Prénom</th>");
            rowTable.append("<th>Catégorie</th>");

            $container.addClass('col-lg-offset-2 col-lg-8');
        }

        function racePassedOptions() {
            console.log('racePassedOptions');

            options.columns = [
                {data: 'ranck', className: "row-ranck"},
                {
                    "data": "lastName",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.lastName + "</span>");
                    }
                },
                {
                    "data": "firstName",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.firstName + "</span>");
                    }
                },
                {data: 'number', className: "row-ranck"},
                {data: 'chronoString', className: "row-ranck"},
                {data: 'category'},
                {data: 'ranckCategory', className: "row-ranck"}
            ];
            options.columnDefs = [
                {
                    targets: 0,
                    width: "5%"
                },
                {
                    targets: 3,
                    width: "5%"
                },
                {
                    targets: 6,
                    width: "5%"
                }
            ];
            options.columnDefs = [
                {
                    targets: 1,
                    width: "5%"
                }
            ];

            rowTable.empty();
            rowTable.append("<th>Gen.</th>");
            rowTable.append("<th>Nom</th>");
            rowTable.append("<th>Prénom</th>");
            rowTable.append("<th>Doss.</th>");
            rowTable.append("<th>Chrono</th>");
            rowTable.append("<th>Catégorie</th>");
            rowTable.append("<th>Cls.</th>");

            if (inchampionship) {
                options.columns.push({data: 'points', className: "row-ranck"});
                options.columnDefs.push({
                    targets: 7,
                    width: "5%"
                });
                rowTable.append("<th>Point</th>");
            }

            $container.addClass('col-lg-offset-1 col-lg-10');
        }

        function raceCategoryOpenOptions() {
            console.log('raceCategoryOpenOptions');


            options.columns = [
                {
                    "data": "lastName",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.lastName + "</span>");
                    }
                },
                {
                    "data": "firstName",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.firstName + "</span>");
                    }
                },
            ];

            rowTable.empty();
            rowTable.append("<th>Nom</th>");
            rowTable.append("<th>Prénom</th>");

            $container.addClass('col-lg-offset-3 col-lg-6');
        }

        function raceCategoryCloseOptions() {
            console.log('raceCategoryCloseOptions');


            options.columns = [
                {data: 'number', className: "row-ranck"},
                {
                    "data": "lastName",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.lastName + "</span>");
                    }
                },
                {
                    "data": "firstName",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.firstName + "</span>");
                    }
                },
            ];
            options.columnDefs = [
                {
                    targets: 0,
                    width: "5%"
                }
            ];

            rowTable.empty();
            rowTable.append("<th>Dossard</th>");
            rowTable.append("<th>Nom</th>");
            rowTable.append("<th>Prénom</th>");

            $container.addClass('col-lg-offset-3 col-lg-6');
        }

        function raceCategoryPassedOptions() {
            console.log('raceCategoryPassedOptions');

            options.columns = [
                {data: 'ranckCategory', className: "row-ranck"},
                {
                    "data": "lastName",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.lastName + "</span>");
                    }
                },
                {
                    "data": "firstName",
                    "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                        $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.firstName + "</span>");
                    }
                },
                {data: 'number', className: "row-ranck"},
                {data: 'chronoString', className: "row-ranck"},
                {data: 'ranck', className: "row-ranck"}
            ];
            options.columnDefs = [
                {
                    targets: 0,
                    width: "5%"
                },
                {
                    targets: 3,
                    width: "5%"
                },
                {
                    targets: 5,
                    width: "5%"
                }
            ];

            rowTable.empty();
            rowTable.append("<th>Cls</th>");
            rowTable.append("<th>Nom</th>");
            rowTable.append("<th>Prénom</th>");
            rowTable.append("<th>Doss</th>");
            rowTable.append("<th>Chrono</th>");
            rowTable.append("<th>Gen.</th>");

            if (inchampionship) {
                options.columns.push({data: 'points', className: "row-ranck"});
                options.columnDefs.push({
                    targets: 5,
                    width: "5%"
                });
                rowTable.append("<th>Point</th>");
            }

            container.addClass('col-lg-offset-2 col-lg-8');
        }
    }
}

/***********************************************************************************************************************
 /   championship/show.html.twig
 /
 /**********************************************************************************************************************/
function championshipShow(category) {
    var $btnCategories = $('#championship-show-categories').find('button');
    var $container = $('#table');

    var options = {
        destroy: true,
        language: language,
        bInfo: false,
        order: [[0, "asc"]]
    };
    options.columns = [
        {data: 'ranck', className: "row-ranck"},
        {
            "data": "lastName",
            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.lastName + "</span>");
            }
        },
        {
            "data": "firstName",
            "fnCreatedCell": function (nTd, sData, oData, iRow, iCol) {
                $(nTd).html("<span class='profile-href' onclick='profilData(" + oData.id + " )' data-toggle='modal' data-target='#competitor-modal'>" + oData.firstName + "</span>");
            }
        },
        {data: 'points', className: "row-ranck"},
    ];
    options.columnDefs = [
        {
            targets: 0,
            width: "5%"
        },
        {
            targets: 3,
            width: "5%"
        }
    ];

    //inti
    ranckTable(category);

    //Envents
    $btnCategories.click(function () {

        ranckTable($(this).val());
        $btnCategories.attr('class', 'btn-xs btn-info col-xs-12');
        $(this).attr('class', 'btn-xs btn-primary col-xs-12');
    });

    $('.profile-href').click(function () {
        var user = $(this).attr("data");
        profilData(user);
    });

    function ranckTable(category) {
        loaderDivStart($container);

        var path = Routing.generate('championship_json', {
            idCategory: category
        });

        $.ajax({
            url: path,
            success: function (data) {
                displayTable(data, category);
            },
            error: function () {
                ajaxError();
            }
        });
    }

    function displayTable(data, category) {
        options.data = data;

        $('#table-championship').DataTable(options);
        loaderDivStop($container);
        loaderStop();
    }

}

//***********************************************/FORMS/***************************************************************/
/***********************************************************************************************************************
 /   FOSUserBundle/views/Registration/register_content.html.twig
 /**********************************************************************************************************************/
function registration() {
    $(function () {
        var radioBtn = $("#gender-radio input");
        var genderInput = $('#appbundle_competitor_sexe');

        radioBtn.checkboxradio();

        radioBtn.click(function () {
            if (this.checked) {
                genderInput.val($(this).val());
                genderInput.trigger('change');
            }
        });
    });

    var inputDate = $('#appbundle_competitor_date');
    var dp = $('#register-form-dp');

    dp.datepicker({
        language: 'fr',
        format: 'dd-mm-yyyy',
        startView: 2,
        autoclose: true,
        todayHighlight: true
    });


    dp.on('changeDate', function () {
        var compDate = parseDateFrToUs(dp.datepicker('getFormattedDate'));
        inputDate.val(compDate);
        inputDate.trigger('change');

    });
}

/***********************************************************************************************************************
 /   race\new.html.twig
 /**********************************************************************************************************************/
function raceDistance() {
    $('#distance-widget').change(function () {
        $('#appbundle_race_distance').val($('#distance-widget').val())
    })
}

function raceCategories() {
    var cbAllCat = $('#cbCategories-all');
    var cbOnce = $('#category-once input');
    var cbChamp = $('#cb-championship');
    var catInput = $('#appbundle_race_categoriesString');
    var champCheck = $('#appbundle_race_requestInChampionship');
    var categories;

    // Initalize
    $("#race-form-categories input").checkboxradio();
    if (catInput.val())
        categoriesUpdate();

    if ($('#appbundle_race_requestInChampionship').prop('checked'))
        $('#cb-championship').prop('checked', true).checkboxradio('refresh');

    //events
    cbChamp.click(function () {
        if (this.checked)
            champCheck.prop('checked', true);
        else
            champCheck.prop('checked', false);
    });

    cbAllCat.click(function () {
        if (this.checked)
            cbOnce.prop('checked', true).checkboxradio('refresh');
        else
            cbOnce.prop('checked', false).checkboxradio('refresh');

        categoriesChange();
    });

    cbOnce.click(function () {
        categoriesChange()
    });

    //functions
    function categoriesUpdate() {
        var oldCategories = $.parseJSON(catInput.val());

        cbOnce.each(function () {
            if (oldCategories.includes($(this).val()))
                $(this).prop('checked', true).checkboxradio('refresh');
        });

        categoriesChange();
    }

    function categoriesChange() {
        var i = 0;
        categories = [];
        cbOnce.each(function () {
            i++;
            if (this.checked)
                categories.push($(this).val());
        });

        if (categories.length < i)
            cbAllCat.prop('checked', false).checkboxradio('refresh');

        if (categories.length === i)
            cbAllCat.prop('checked', true).checkboxradio('refresh');

        if (categories.length > 0)
            catInput.val(JSON.stringify(categories));
        else
            catInput.val('');

        catInput.trigger('change');
    }
}

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

    if (inputDateStart.val().length !== 0)
        dpStart.datepicker('update', parseDateUsToFr(inputDateStart.val()));

    if (inputDateEnd.val().length !== 0)
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

//***********************************************/MODAL/***************************************************************/
/***********************************************************************************************************************
 /   modal/profile_competitor.html.twig
 /**********************************************************************************************************************/
function navbarCompetitorProfile() {
    var $profile = $('#profile-href');

    $profile.click(function () {
        var user = $(this).attr("data");
        profilData(user);

    });
}

function profilData(user) {

    var path = Routing.generate('competitor_json_userId', {userId: user});

    $.ajax({
        url: path,
        success: function (data) {
            $('#profile-name').text(data.firstName + ' ' + data.lastName);
            $('#profile-category').text(data.category);
            $('#profile-age').text(data.age + ' ans');
        },
        error: function () {
            ajaxError();
        }
    });
}

//***********************************************/PANEL/***************************************************************/
function panelCategoriesHeight() {
    $('.location-container').each(function () {
        var heigth = $(this).height();
        var id = $(this).attr('value');

        $('#categories-' + id).css('height', heigth + 'px');
    });
}

//***********************************************/NAV BAR/*************************************************************/
function organizerNavBar() {

  /*      //init
        checkElements();

        //events
        $(window).resize(function () {
            checkElements();
        });

        //functions
        function checkElements() {
            windowsSize = $(window).width();

            if (windowsSize < 815) {
                $('#badge').hide();
                $('#organizerNavBar-open').hide();
            }else {
                $('#badge').show();
                $('.organizer-content').show();
            }
        }*/
}

/*function openOrganizerNav() {
    if (windowsSize > 815) {
        document.getElementById("organizerNavBar").style.width = "11em";
        document.getElementById("main").style.marginLeft = "11em";
        $('.sidenav-open').css('display', 'inline');
    }
}

function closeOrganizerNav() {
    document.getElementById("organizerNavBar").style.width = "0";
    document.getElementById("main").style.marginLeft = "0";
}*/

/***********************************************************************************************************************
 /   navbar/general.html.twig
 /**********************************************************************************************************************/
function navbarBadgeAdmin() {
    if (windowsSize > 800) {
        var path = Routing.generate('race_countNotSupervised');
        $.ajax({
            url: path,
            dataType: "json",
            success: function (data) {
                var badge = $('#badge');
                badge.text(data);

                if (data == 0) {
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
            },
            error: function () {
                ajaxError();
            }
        });
    }
}

//***********************************************/SEARCH BAR/**********************************************************/
/***********************************************************************************************************************
 /   race\showSearch.html.twig
 /**********************************************************************************************************************/
function showSearch() {
    var $searchBar = $('#search-bar');
    var $searchBarXs = $('#search-bar-xs');
    var $searchButtonXs = $('#button-search-xs');
    var $searchButton = $('#button-search');

    searchBarWidth();

    $(window).resize(function () {
        searchBarWidth();
    });

    function searchBarWidth() {
        var width = $(window).width();

        if (width <= 751) {
            $searchButtonXs.before($searchBar);
            $searchBar.css('margin-top', '0em');

            if (width > 650)
                $searchBarXs.css('padding', '0em 15em 0em 15em');

            if (width < 650 && width > 500)
                $searchBarXs.css('padding', '0em 10em 0em 10em');

            if (width < 500 && width > 375)
                $searchBarXs.css('padding', '0em 5em 0em 5em');

            if (width < 375)
                $searchBarXs.css('padding', '0em 0.5em 0em 0.5em');
        }
        else {
            $searchButton.after($searchBar);
            $searchBar.css('margin-top', '16em');
        }

        loaderStop();
    }
}

function searchBarRace() {

    var dataSearch = {
        categories: null,
        dep: null,
        dist: {min: null, max: null},
        date: {min: null, max: null},
        inChampionship: null,
        enrol: null
    };

    var jsonSearch = $('#data-search').val();
    var updateDataSearch;

    if (jsonSearch)
        updateDataSearch = JSON.parse(jsonSearch);
    else
        updateDataSearch = dataSearch;

    var $allSwitch = $("#search-bar").find('.switch-content');

    var $categoryContainer = $('#category-container');
    var $regionContainer = $('#region-container');
    var $distanceContainer = $('#distance-container');
    var $dateContainer = $('#date-container');
    var $championshipContainer = $('#championship-container');
    var $competitorContainer = $('#competitor-container');


    var $allCbDepartement = $regionContainer.find('.cb-dep');
    var $allCbCategory = $categoryContainer.find('input');
    var $inputDistance = $distanceContainer.find('input');
    var $inputDate = $dateContainer.find('input');
    var $inputChampionship = $championshipContainer.find('input');
    var $inputCompetitor = $competitorContainer.find('input');

    var $dateMin = $dateContainer.find('#date-min');
    var $dateMax = $dateContainer.find('#date-max');

    //init
    $dateContainer.find('.datepicker').datepicker({
        language: 'fr',
        format: 'dd-mm-yyyy',
        todayBtn: 'linked',
        autoclose: true,
    });
    searchUpdate();

    //update
    function searchUpdate() {

        if (updateDataSearch.categories) {
            $allCbCategory.each(function () {
                var categories = $(this).val();
                if ($.inArray(categories, updateDataSearch.categories) !== -1) {
                    $(this).prop('checked', true);
                    refreshCategory();
                }
            });
        }

        if (updateDataSearch.dep) {
            $allCbDepartement.each(function () {
                var dep = $(this).val();
                if ($.inArray(dep, updateDataSearch.dep) !== -1) {
                    $(this).prop('checked', true);
                    refreshDepartement($(this).attr('region'));
                }
            });
        }

        if (updateDataSearch.dist.min || updateDataSearch.dist.max) {
            if (updateDataSearch.dist.min)
                $('#distMin').val(updateDataSearch.dist.min);

            if (updateDataSearch.dist.max)
                $('#distMax').val(updateDataSearch.dist.max);

            switchActive($('#switch-distance'));
            refreshDistance('active');
        } else
            refreshDistance('disable');

        if (updateDataSearch.date.min || updateDataSearch.date.max) {
            if (updateDataSearch.date.min)
                $dateMin.datepicker('update', parseDateUsToFr(updateDataSearch.date.min));

            if (updateDataSearch.date.max)
                $dateMax.datepicker('update', parseDateUsToFr(updateDataSearch.date.max));

            switchActive($('#switch-date'));
            refreshDate('active');
        } else
            refreshDate('disable');

        if (updateDataSearch.inChampionship) {
            if (updateDataSearch.inChampionship === '1')
                $('#radio-championship-1').prop('checked', true);

            if (updateDataSearch.inChampionship === '0')
                $('#radio-championship-0').prop('checked', true);

            refreshChampionship('active');
        } else
            refreshChampionship('disable');

        if (updateDataSearch.enrol) {

            if ($.inArray('1', updateDataSearch.enrol) !== -1)
                $('#canEnrol').prop('checked', true);

            if ($.inArray('2', updateDataSearch.enrol) !== -1)
                $('#isEnrol').prop('checked', true);

            if ($.inArray('3', updateDataSearch.enrol) !== -1)
                $('#participated').prop('checked', true);

            refreshCompetitor();
        } else
            refreshCompetitor();
    }


    //events
    $allSwitch.click(function () {
        var $switcher = $(this);
        var status = $switcher.attr('status');
        var data = $switcher.attr('id').split('-');
        var initial = $switcher.attr('initial');

        if (initial !== '0') {
            status = initial;
            $switcher.attr('initial', '0');
        }

        switch (data[1]) {
            case 'region' :
                //data[2] = region.code
                refreshRegion(data[2], status);
                break;

            case 'category' :
                if (status === "active")
                    $allCbCategory.prop('checked', true);
                else
                    $allCbCategory.prop('checked', false);
                refreshCategory();
                break;

            case 'distance' :
                refreshDistance(status);
                break;

            case 'date' :
                refreshDate(status);
                break;

            case 'championship' :
                refreshChampionship(status);
                break;

            case 'competitor' :
                if (status === "active")
                    $inputCompetitor.prop('checked', true);
                else
                    $inputCompetitor.prop('checked', false);
                refreshCompetitor();
                break;
        }

        switch (status) {
            case 'disable' :
                switchDisable($switcher);
                break;

            case 'active' :
                switchActive($switcher);
                break;

            case 'initial' :
                break;
        }
    });

    $allCbDepartement.click(function () {
        refreshDepartement($(this).attr('region'));
    });

    $allCbCategory.click(function () {
        refreshCategory();
    });

    $inputDistance.change(function () {
        refreshDistance('active');
    });

    $inputDate.on('changeDate', function () {
        refreshDate('active');
    });

    $inputChampionship.click(function () {
        dataSearch.inChampionship = $(this).val();
    });

    $inputCompetitor.change(function () {
        refreshCompetitor();
    });

    $('#button-search, #button-search-xs').click(function () {
        var path = Routing.generate('races_search', {
            dataSearch: JSON.stringify(dataSearch)
        });

        window.location = path;
    });

    //functions/////////////////////////////////////////////////////////////////////////////////////////////
    function refreshRegion(region, status) {
        var $cbDep;
        if (region !== 'all')
            $cbDep = $("#region-" + region).find('.cb-dep');
        else
            $cbDep = $("#region-container").find('.cb-dep');

        if (status === "active")
            $cbDep.prop('checked', true);
        else
            $cbDep.prop('checked', false);

        refreshDepartement(region);
    }

    function refreshDepartement(region) {
        var cptDepReg = 0;
        var cptDepRegChecked = 0;
        var cptDepAll = 0;
        var cptDepAllChecked = 0;
        var departements = [];
        var $switcher = $("#switch-region-" + region);
        var $switcherAll = $("#switch-region-all");
        var $allSwitchRegion = $('#region-container').find('.switch-content');

        //count, if isChecked push in array data
        $allCbDepartement.each(function () {
            var dep = $(this).val();
            var depReg = $(this).attr('region');

            cptDepAll++;
            if (depReg === region)
                cptDepReg++;

            if (this.checked) {
                departements.push(dep);
                cptDepAllChecked++;

                if (depReg === region)
                    cptDepRegChecked++;
            }
        });

        //switchRegion refresh
        if (cptDepRegChecked === 0)
            switchDisable($switcher);
        else if (cptDepRegChecked === cptDepReg)
            switchActive($switcher);
        else
            switchInitial($switcher);

        //switcherSelectAll refresh
        if (cptDepAllChecked === 0) {
            $allSwitchRegion.each(function () {
                switchDisable($(this));
            })
        } else if (cptDepAllChecked === cptDepAll) {
            $allSwitchRegion.each(function () {
                switchActive($(this));
            })
        } else
            switchInitial($switcherAll);

        //data refresh
        if (cptDepAllChecked === 0 || cptDepAllChecked === cptDepAll)
            dataSearch.dep = null;
        else
            dataSearch.dep = departements;
    }

    function refreshCategory() {
        var cptCategory = 0;
        var cptCategoryChecked = 0;
        var $switcher = $("#switch-category");
        var categories = [];

        //count, if isChecked push in array data
        $allCbCategory.each(function () {
            var category = $(this).val();
            cptCategory++;
            if (this.checked) {
                categories.push(category);
                cptCategoryChecked++;
            }
        });

        //switchCategory refresh
        if (cptCategoryChecked === 0) {
            switchDisable($switcher);
        } else if (cptCategoryChecked === cptCategory)
            switchActive($switcher);
        else
            switchInitial($switcher);

        //data refresh
        if (cptCategoryChecked === 0 || cptCategoryChecked === cptCategory)
            dataSearch.categories = null;
        else
            dataSearch.categories = categories;
    }

    function refreshDistance(status) {
        var $distanceContainer = $('#distance-container');

        if (status === "active") {
            var inputMin = $('#distMin');
            var inputMax = $('#distMax');
            var min = inputMin.val();
            var max = inputMax.val();

            $distanceContainer.css({'opacity': 1, 'pointer-events': 'auto'});
            dataSearch.dist.min = min;
            dataSearch.dist.max = max;
            inputMin.attr('max', max);
            inputMax.attr('min', min);
        }
        else {
            $distanceContainer.css({'opacity': 0.5, 'pointer-events': 'none'});
            dataSearch.dist.min = null;
            dataSearch.dist.max = null;
        }
    }

    function refreshDate(status) {
        if (status === "active") {
            $dateContainer.css({'opacity': 1, 'pointer-events': 'auto'});

            if ($dateMin.val()) {
                dataSearch.date.min = parseDateFrToUs($dateMin.datepicker('getFormattedDate'));
                $dateMin.datepicker('setEndDate', $dateMax.datepicker('getDate'));
            } else
                dataSearch.date.min = null;
            if ($dateMax.val()) {
                dataSearch.date.max = parseDateFrToUs($dateMax.datepicker('getFormattedDate'));
                $dateMax.datepicker('setStartDate', $dateMin.datepicker('getDate'));
            } else
                dataSearch.date.max = null;
        }
        else {
            $dateContainer.css({'opacity': 0.5, 'pointer-events': 'none'});
            dataSearch.date.min = null;
            dataSearch.date.max = null;
        }

    }

    function refreshChampionship(status) {

        if (status === "active") {
            $championshipContainer.css({'opacity': 1, 'pointer-events': 'auto'});
            switchActive($('#switch-championship'));

            if ($('#radio-championship-1').is(':checked'))
                dataSearch.inChampionship = '1';

            if ($('#radio-championship-0').is(':checked'))
                dataSearch.inChampionship = '0';
        } else {
            $championshipContainer.css({'opacity': 0.5, 'pointer-events': 'none'});
            dataSearch.inChampionship = null;
        }
    }

    function refreshCompetitor() {
        var cptCompetitorInfo = 0;
        var cptCompetitorInfoChecked = 0;
        var $switcher = $("#switch-competitor");
        var competitorInfo = [];

        //count, if isChecked push in array data
        $inputCompetitor.each(function () {
            cptCompetitorInfo++;
            if (this.checked) {
                competitorInfo.push($(this).val());
                cptCompetitorInfoChecked++;
            }
        });

        //switchCompetitor refresh
        if (cptCompetitorInfoChecked === 0) {
            switchDisable($switcher);
            dataSearch.enrol = null;
        } else {
            if (cptCompetitorInfoChecked === cptCompetitorInfo)
                switchActive($switcher);
            else
                switchInitial($switcher);

            dataSearch.enrol = competitorInfo;
        }
    }

    //switcher
    function switchDisable($switcher) {
        $switcher.find('.info-slide').remove();
        $switcher.attr({
            'class': 'switch-content disable',
            'status': 'active',
            'initial': '0'
        });
    }

    function switchActive($switcher) {
        $switcher.find('.info-slide').remove();
        $switcher.attr({
            'class': 'switch-content active',
            'status': 'disable',
            'initial': '0'
        });
    }

    function switchInitial($switcher) {

        $switcher.attr({
            'status': 'initial',
            'class': 'switch-content initial',
            'initial': true
        });

        var notSelect = $('<span class="info-slide disable" title="tout supprimer"/>');
        var allSelect = $('<span class="info-slide active" title="tout sélectionner"/>');
        $switcher.append(notSelect);
        $switcher.append(allSelect);

        notSelect.hover(function () {
            $switcher.addClass('disable');
            $switcher.removeClass('initial');
        }, function () {
            $switcher.addClass('initial');
            $switcher.removeClass('disable');
        });

        allSelect.hover(function () {
            $switcher.addClass('active');
            $switcher.removeClass('initial');
        }, function () {
            $switcher.addClass('initial');
            $switcher.removeClass('active');
        });

        allSelect.click(function () {
            $switcher.find('.info-slide').remove();
            $switcher.attr('initial', 'active');
        });

        notSelect.click(function () {
            $switcher.find('.info-slide').remove();
            $switcher.attr('initial', 'disable');
        });
    }

    //date/////////////////////////////////////////////////////////////////////////////////////////////

}

function competitorSearchRace(dateSearch) {

    var today = todayDate();
    var date;

    if (dateSearch === 'future')
        date = {min: today, max: null};
    else
        date = {min: null, max: today};

    var dataSearch = {
        categories: null,
        dep: null,
        dist: {min: null, max: null},
        date: date,
        inChampionship: null,
        enrol: ['2', '3']
    };

    var path = Routing.generate('races_search', {
        dataSearch: JSON.stringify(dataSearch)
    });

    window.location = path;
}

function searchRaceAll() {
    var today = todayDate();

    var dataSearch = {
        categories: null,
        dep: null,
        dist: {min: null, max: null},
        date: {min: today, max: null},
        inChampionship: null,
        enrol: null
    };

    var path = Routing.generate('races_search', {
        dataSearch: JSON.stringify(dataSearch)
    });

    window.location = path;
}

function searchLayout() {

    //init
    searchBarRace();
    if ($(window).width() <= 751) {
        $('#index-container').css('margin-left', '0em')
    }
    panelCategoriesHeight();
    loaderStop();


    var $indexContent = $('#index-content');
    var $smallMap = $('#small-map');
    var $smallMapContainer = $('#small-map-container');
    var $indexMap = $('#index-map');
    var $indexMapConatiner = $('#index-map-container');
    var $btMap = $('#button-map');
    var dataMap;
    var pastIcon = L.icon({
        iconUrl: '/webSport/web/img/green.ico',
        iconSize: [30, 30],
        iconAnchor: [11, 31],
        popupAnchor: [5, -25]
    });
    var futureIcon = L.icon({
        iconUrl: '/webSport/web/img/pink.png',
        iconSize: [30, 30],
        iconAnchor: [11, 31],
        popupAnchor: [5, -25]
    });

    //init
    $indexMapConatiner.hide();
    initialiseMap();

    // Event**************************************************************************************************>
    $btMap.click(function () {
        $indexContent.hide();
        $indexMapConatiner.show();
        if ($(this).val() === 'not-initialized') {
            indexMapBuild(dataMap);
            $(this).val('is-initialized');
        }
    });

    $('#button-map-close').click(function () {
        $indexMapConatiner.hide();
        $indexContent.show();
    });

    $(window).resize(function () {
        if ($(window).width() <= 751) {
            // $('#left-container').hide();
            $('#index-container').css('margin-left', '0em');
        }
        else {
            // $('#left-container').show();
            $('#index-container').css('margin-left', '17em');
        }
    });


    // Functions**************************************************************************************************>
    function initialiseMap() {
        var path = Routing.generate('competition_get_geojson');
        $.ajax({
            url: path,
            success: function (data) {
                dataMap = data;
                smallMapBuild(data);
            },
            error: function () {
                ajaxError();
            }
        });
    }

    function smallMapBuild(data) {
        var FuturCompetitionLayer = layerBuild(data.futureCompetitions, pastIcon);

        var osmLayer = L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
            maxZoom: 18
        });
        var smallMap = L.map('small-map', {
            center: [46.52863469527167, 0],
            zoom: 4,
            layers: [osmLayer, FuturCompetitionLayer]
        });

        $btMap.css('visibility', 'visible');
    }

    function indexMapBuild(data) {
        var watercolorLayer = L.tileLayer('http://{s}.tile.stamen.com/watercolor/{z}/{x}/{y}.jpg', {
            attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
            maxZoom: 18
        });
        var FuturCompetitionLayer2 = layerBuild(data.futureCompetitions, pastIcon);
        var PastCompetitionLayer2 = layerBuild(data.pastCompetitions, futureIcon);

        var osmLayer2 = L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors',
            maxZoom: 18
        });
        var indexMap = L.map('index-map', {
            center: [46.52863469527167, 2.43896484375],
            zoom: 5,
            layers: [osmLayer2, PastCompetitionLayer2, FuturCompetitionLayer2]
        });

        //Layers Control **************************************************************************************************>
        var baseMaps = {
            "osmLayer": osmLayer2,
            "watercolorLayer": watercolorLayer
        };
        var overlayMaps = {
            "A venir": FuturCompetitionLayer2,
            "Passées": PastCompetitionLayer2
        };

        L.control.layers(baseMaps, overlayMaps).addTo(indexMap);
    }

    function layerBuild(data, icon) {
        return L.geoJSON(data, {
            pointToLayer: function (geoJsonPoint, latlng) {
                return L.marker(latlng, {icon: icon});
            }
        }).bindPopup(function (layer) {
            return layer.feature.properties.description;
        });
    }

}

//***********************************************/LOADER/**************************************************************/
function loaderStop() {
    $('#full-page').fadeOut(1000);
}

function loaderDivStart(divToLoad) {
    var $loaderDiv = $("#loader-div");
    $loaderDiv.height(divToLoad.height());
    $loaderDiv.width(divToLoad.width());
    divToLoad.hide();
    $loaderDiv.show();
}

function loaderDivStop(divToLoad) {
    var $loaderDiv = $("#loader-div");
    $loaderDiv.hide();
    divToLoad.show();
}

//***********************************************/TOOLS/***************************************************************/
function todayDate() {
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth() + 1; //January is 0!
    var yyyy = today.getFullYear();

    if (dd < 10) {
        dd = '0' + dd
    }

    if (mm < 10) {
        mm = '0' + mm
    }

    today = yyyy + '-' + mm + '-' + dd;

    return today;
}

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

function ajaxError() {
    $('#info').css('display', 'block');
    $('#info span').text('Erreur requête ajax');
    loaderStop();

    $('#info-button').click(function () {
        location.reload();
        $('#info').css('display', 'none');
    });
}

//***********************************************/PLUG-IN/*************************************************************/

//locationForm plug-in//////
(function ($) {

    var $output;

    var $locationContainer = $("<div class='col-xs-offset-1 col-xs-6'>");
    var $locationAlert = $("<div id='location_alert' class='alert alert-warning' role='alert' style='display: none'></div>");
    var $locationLoader = $("<div class='loader-div'></div>");
    var $locationForm = $("<div id='location_form' class='form-group'>");
    var $locationLabel = $("<label id='location_label'>Département</label>");
    var $locationInput = $("<input id='location_input' class='form-control' placeholder='Ex : 06 Alpes-Maritimes'>");
    var $locationBtnNextStep = $("<button id='next_step_btn' class='col-xs-2  btn btn-primary btn-sm' value='depValidate'>Suivant</button>");
    var $locationShow = $("<div id='location-show' class='col-xs-5'></div>");

    $.fn.locationForm = function (options) {

        var defaults = {"update": false};
        var parameters = $.extend(defaults, options);

        $output = parameters.output;

        $locationForm.append([$locationLabel, $locationInput]);
        $locationContainer.append([$locationAlert, $locationLoader, $locationForm, $locationBtnNextStep, $locationShow]);
        this.append([$locationContainer, $locationShow]);

        if ($output.val().length !== 0) {
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
        $.get('/webSport/web/json/departements.json', function (data, status) {
            $locationInput.autocomplete({
                source: data,
                messages: {
                    noResults: '',
                    results: function () {
                    }
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

        $.get("/webSport/web/json/departements.json", function (data, status) {
            if ($.inArray($locationInput.val(), data) !== -1) {
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
                        results: function () {
                        }
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
                results: function () {
                }
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
            },
            error: function () {
                ajaxError();
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
            depCode: depCode,
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

}(jQuery));

//ocmDateTime plug-in////////
(function ($) {

    var $dateShow = $("<div id='ocm-dt-date-show'></div>");
    var $hInput = $("<input id='ocm-dt-hour' type='number' min='0' max='23' maxlength='2' required>");
    var $mInput = $("<input id='ocm-dt-minute' type='number' min='0' max='59' maxlength='2' required>");
    var $dp = $("<div id='ocm-dt-datepicker'></div>");
    var $output;
    var parameters;

    $.fn.ocmDateTime = function (options) {

        var defaults = {
            "defaultHour": 12,
            "defaultMinute": 30
        };

        parameters = $.extend(defaults, options);
        $output = parameters.output;

        var $containerHour = $("<div class='n2 col-xs-5'></div>").append($hInput);
        var $separator = $("<div class='n2 col-xs-offset-1 col-xs-1 '><span class='separator'>:</span></div>");
        var $containerMinute = $("<div class='n2 col-xs-5'></div>").append($mInput);
        var $containerDp = $("<div class='calendar-show '></div>").append($dp);
        var $containerCalendar = $("<div class=' col-xs-12 col-sm-offset-0 col-sm-4 calendar'>").append($containerDp);

        var $containerTimeInput = $("<div id='ocm-dt-time' class='time-input col-xs-10'></div>").append([
            $containerHour,
            "<div class='n2 col-xs-1'></div>",
            $separator,
            $containerMinute
        ]);

        var $containerTime = $("<div class='col-xs-12 col-sm-8'>").append([
            $dateShow,
            "<div class='col-xs-1'></div>",
            $containerTimeInput
        ]);

        this.append([$containerTime, $containerCalendar]);

        $dp.datepicker({
            format: "yyyy-mm-dd",
            startDate: parameters.dateStart,
            endDate: parameters.dateEnd,
            language: "fr"
        });

        $dp.datepicker('update', parameters.defaultDate);

        $hInput.val(('0' + parameters.defaultHour).slice(-2));
        $mInput.val(('0' + parameters.defaultMinute).slice(-2));

        dateString();
        changeDateTime();

        return this;
    };

    //event
    $dp.on('changeDate', function () {
        dateString();
        changeDateTime();
    });

    $hInput.add($mInput).change(function () {
        $(this).val(('0' + $(this).val()).slice(-2));
        changeDateTime();
    });

    //function
    function dateString() {
        $dateShow.text(
            $dp.datepicker('getUTCDate').toLocaleDateString('fr-FR', {
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                }
            ));
    }

    function changeDateTime() {
        var hour = $hInput.val();
        var minute = $mInput.val();

        if ($.isNumeric(hour) && $.isNumeric(minute)) {

            if (hour > 23 || hour < 0)
                $hInput.val(parameters.defaultHour);

            if (minute > 59 || minute < 0)
                $mInput.val(parameters.defaultMinute);

            $output.val($dp.datepicker('getFormattedDate') + ' ' + $hInput.val() + ':' + $mInput.val() + ':00');

        } else {
            $hInput.val(parameters.defaultHour);
            $mInput.val(parameters.defaultMinute);
        }
        $output.trigger('change');
    }


}(jQuery));


//***********************************************/TEST/*************************************************************/
var test = $('#user-bar');
/*$(window).scroll(function () {
    console.log('scroll');
    console.log( $('#test-bar').position().top);
});*/

$(window).scroll(function(){
    console.log($(this).scrollTop());

    if ($(this).scrollTop() > 54) {
        test.addClass('fixed');
    } else {
        test.removeClass('fixed');
    }
});