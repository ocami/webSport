function messageFullPage(message) {
    var fullPageDiv = document.createElement('div');
    fullPageDiv.className = 'fullPage fullPage-alert';
    var alert = document.createElement('div');
    alert.className = 'alert alert-success  col-xs-7';
    alert.append(message);
    var img = document.createElement('div');
    img.className = ('col-xs-offset-2 col-xs-1 fullPage-alert-img')

    fullPageDiv.append(img);
    fullPageDiv.append(alert);

    $('.container').before(fullPageDiv);

    $(window).scrollTop(0);
}

function loaderStart() {
    var fullPageDiv = document.createElement('div');
    fullPageDiv.className = 'fullPage';
    var loader = document.createElement('div');
    loader.className = 'loader';
    fullPageDiv.append(loader);
    $('.container-fluid').before(fullPageDiv);
    $(window).scrollTop(0);
}

function loaderStop() {
    $('.fullPage').fadeOut(1000);
}

function loaderDivStart(divToLoad) {
    $(".loader-div").height(divToLoad.height());
    $(".loader-div").width(divToLoad.width());
    divToLoad.hide();
    $(".loader-div").show();
}

function loaderDivStop(divToLoad) {
    $(".loader-div").hide();
    divToLoad.show();
}