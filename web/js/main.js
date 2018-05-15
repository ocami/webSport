/***********************************************************************************************************************
 /   navbar/general.html.twig
 /**********************************************************************************************************************/
$(function () {
    var li = document.createElement("LI");

    $.getJSON("../json/navbar_championship.json", function (result) {
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

/***********************************************************************************************************************
 /   modal/profile_competitor.html.twig
 /**********************************************************************************************************************/





// $(document).ready(function () {
//     $('table.display').DataTable();
// });
//
//
// function displayDate() {
//     document.getElementById("demo").innerHTML = Date();
// }







