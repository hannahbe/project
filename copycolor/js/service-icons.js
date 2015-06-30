var GREY = '#2f2f2f';
var RGB_WHITE = 255;    // rgb white = {255, 255, 255}
var RGB_GREY = 47;      // rgb grey (the one that we use) = {47, 47, 47}

$(window).load(function () {

    $('.service-icon').hover(enterServiceIcon, leaveServiceIcon);

    $('.sidebar-icon').hover(enterSidebarIcon, leaveSidebarIcon);

    $('.add-design').hover(enterAddDesign, leaveAddDesign);

    $('.upload-icon').hover(enterPrintFile, leavePrintFile);

});

var enterServiceIcon = function () {
    var col = $(this).parent().children().index($(this));
    var row = $(this).parent().parent().children().index($(this).parent());
    $(this).addClass('change-color-icon');
    $('#service-table tr').eq(row + 1).find('td').eq(col).addClass('change-color-name');
    invertCell(GREY, 'white', invertColors($(this).find('img').attr('src')));
};

var leaveServiceIcon = function () {
    var col = $(this).parent().children().index($(this));
    var row = $(this).parent().parent().children().index($(this).parent());
    invertCell('transparent', 'black', invertColors($(this).find('img').attr('src')));
    $(this).removeClass('change-color-icon');
    $('#service-table tr').eq(row + 1).find('td').eq(col).removeClass('change-color-name');
};

var enterAddDesign = function () {
    $(this).addClass('change-color-icon');
    invertButton(GREY, 'transparent', invertColors($(this).attr('src')));
};

var leaveAddDesign = function () {
    invertButton('transparent', 'black', invertColors($(this).attr('src')));
    $(this).removeClass('change-color-icon');
};

var enterSidebarIcon = function () {
    var col = $(this).parent().children().index($(this));
    var row = $(this).parent().parent().children().index($(this).parent());
    $(this).addClass('change-color-icon');
    $('#designer-sidebar table tr').eq(row - 1).find('td').eq(col).addClass('change-color-name');
    invertCell(GREY, 'white', invertColors($(this).find('img').attr('src')));
};

var leaveSidebarIcon = function () {
    var col = $(this).parent().children().index($(this));
    var row = $(this).parent().parent().children().index($(this).parent());
    invertCell('transparent', 'black', invertColors($(this).find('img').attr('src')));
    $(this).removeClass('change-color-icon');
    $('#designer-sidebar table tr').eq(row - 1).find('td').eq(col).removeClass('change-color-name');
};

var enterPrintFile = function () {
    var col = $(this).parent().children().index($(this));
    var row = $(this).parent().parent().children().index($(this).parent());
    $(this).addClass('change-color-icon');
    $('#print-table tr').eq(row + 1).find('td').eq(col).addClass('change-color-name');
    $('#print-table tr').eq(row + 2).find('td').eq(col).addClass('change-color-file');
    invertUpload(GREY, 'white', invertColors($(this).find('img').attr('src')));
};

var leavePrintFile = function () {
    var col = $(this).parent().children().index($(this));
    var row = $(this).parent().parent().children().index($(this).parent());
    invertUpload('transparent', 'black', invertColors($(this).find('img').attr('src')));
    $(this).removeClass('change-color-icon');
    $('#print-table tr').eq(row + 1).find('td').eq(col).removeClass('change-color-name');
    $('#print-table tr').eq(row + 2).find('td').eq(col).removeClass('change-color-file');
};

function invertUpload(bg_color, font_color, src) {
    if (document.getElementsByClassName('change-color-file').length > 0) {
        document.getElementsByClassName('change-color-file')[0].style.backgroundColor = bg_color;
        document.getElementsByClassName('change-color-file')[0].style.color = font_color;
    }
    invertCell(bg_color, font_color, src);
}

function invertButton(bg_color, border_color, src) {
    if (document.getElementsByClassName('change-color-icon').length > 0) {
        document.getElementsByClassName('change-color-icon')[0].setAttribute('src', src);
        document.getElementsByClassName('change-color-icon')[0].style.backgroundColor = bg_color;
        document.getElementsByClassName('change-color-icon')[0].style.borderColor = border_color;
    }
}

function invertCell(bg_color, font_color, src) {
    if (document.getElementsByClassName('change-color-icon').length > 0) {
        document.getElementsByClassName('change-color-icon')[0].getElementsByTagName('img')[0].setAttribute('src', src);
        document.getElementsByClassName('change-color-icon')[0].style.backgroundColor = bg_color;
    }
    if (document.getElementsByClassName('change-color-name').length > 0) {
        document.getElementsByClassName('change-color-name')[0].style.backgroundColor = bg_color;
        document.getElementsByClassName('change-color-name')[0].style.color = font_color;
    }
}

// invert colors of the icon src
function invertColors(src) {
    var canvas = document.createElement('canvas');
    var ctx = canvas.getContext('2d');
    var imgObj = new Image();
    imgObj.src = src;
    canvas.width = imgObj.width;
    canvas.height = imgObj.height;
    ctx.drawImage(imgObj, 0, 0);
    //var width = Math.max(1, Math.floor(canvas.width));
    //var height = Math.max(1, Math.floor(canvas.height));
    var imgPixels = ctx.getImageData(0, 0, canvas.width, canvas.height);
    for (var y = 0; y < imgPixels.height; y++) {
        for (var x = 0; x < imgPixels.width; x++) {
            var i = (y * 4) * imgPixels.width + x * 4;
            if (imgPixels.data[i] == RGB_WHITE && imgPixels.data[i + 1] == RGB_WHITE && imgPixels.data[i + 2] == RGB_WHITE) {
                imgPixels.data[i] = RGB_GREY;
                imgPixels.data[i + 1] = RGB_GREY;
                imgPixels.data[i + 2] = RGB_GREY;
            }
            else {
                imgPixels.data[i] = RGB_WHITE;
                imgPixels.data[i + 1] = RGB_WHITE;
                imgPixels.data[i + 2] = RGB_WHITE;
            }
        }
    }
    ctx.putImageData(imgPixels, 0, 0, 0, 0, imgPixels.width, imgPixels.height);
    return canvas.toDataURL();
}