function myDatapiker(config) {
    var result = $('.result');
    var myDateTime = $('#myDateTime');

    addHtml();

    function addHtml() {
        myDateTime.append(" <div class=\"col-xs-12 col-sm-8\">\n" +
            "            <div class=\"date-show\"><p>vendredi 11 janvier</p></div>\n" +
            "            <div class=\"col-xs-1\" ></div>\n" +
            "            <div class=\"time-input  col-xs-10\">\n" +
            "                <div class=\"n2 col-xs-5\" >\n" +
            "                    <input class=\"time-hour\"  type=\"number\" min=\"0\" max=\"23\"  maxlength=\"2\"  required>\n" +
            "                </div>\n" +
            "                <div class=\"n2 col-xs-1\" ></div>\n" +
            "                <div class=\"n2 col-xs-offset-1 col-xs-1 \" ><span class=\"separator\">:</span></div>\n" +
            "                <div class=\"n2 col-xs-5\" >\n" +
            "                    <input class=\"time-minute\"  type=\"number\" min=\"0\" max=\"59\"  maxlength=\"2\"  required>\n" +
            "                </div>\n" +
            "            </div>\n" +
            "        </div>\n" +
            "        <div  class=\" col-xs-12 col-sm-offset-0 col-sm-4 calendar\">\n" +
            "            <div class=\"calendar-show \" >\n" +
            "                <div id=\"datepicker\"> </div>\n" +
            "            </div>\n" +
            "        </div>"
        );
    }

    var dp = $('#datepicker');
    var hInput = $('.time-input .time-hour');
    var mInput = $('.time-input .time-minute');


    //construct
    dp.datepicker({
        useCurrent: true,
        language: 'fr',
        format: 'yyyy-mm-dd',
        startDate: config.startDate,
        endDate: config.endDate
    });


    //init
    dp.datepicker('update', config.defaultDate);

    $('.time-hour').val(('0' + config.defaultHour).slice(-2))
    $('.time-minute').val(('0' + config.defaultMinute).slice(-2))

    dateString();
    changeDateTime();

    //event
    dp.on('changeDate', function () {
        dateString();
        changeDateTime();
    });

    $('.time-input input').change(function () {
        $(this).val( ('0' + $(this).val()).slice(-2));
        changeDateTime();
    });

    //function
    function dateString() {
        $('.date-show').text(
            dp.datepicker('getUTCDate').toLocaleDateString('fr-FR', {
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
                }
            ));
    }

    function changeDateTime() {
        var hour = hInput.val();
        var minute = mInput.val();

        if ($.isNumeric(hour) && $.isNumeric(minute)) {
            if (hour <= 23 && minute <= 59) {
                result.val(dp.datepicker('getFormattedDate') + ' ' + hour + ':' + minute + ':00');
            } else {
                result.val('');
            }
        } else {
            result.val('');
        }
        result.trigger('change');
    }
}