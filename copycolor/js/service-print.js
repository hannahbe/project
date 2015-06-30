$(document).ready(function () {

    $(".upload_file").on("click", function () {
        var index = $(this).attr('id').split('-')[1];
        var input_file_id = '#uploaded_file-' + index;
        $(input_file_id).click();
    });

    $('.uploaded_file').change(function () {
        var index = $(this).attr('id').split('-')[1];
        var filename = $(this).val();
        var filename_id = '#filename-' + index;
        $(filename_id).html('' + filename);
    });

    $('.unchoosen').hover(enterPrintFile, leavePrintFile);

    $('.choose_product').on("click", function () {
        var index = $(this).attr('id').split('-')[1];
        if ($(this).hasClass("choosen")) {
            $('#print-options-' + index).hide(250);
            toUnchoosen($(this));
        }
        else {
            var prev_index = $('#current_index').val();
            $('#print-options-' + prev_index).hide(250);
            $('#print-options-' + index).show(250);
            var other_selected = null;
            if (document.getElementsByClassName("choosen").length > 0)
                other_selected = $(".choosen").first();
            toChoosen($(this), $(this).css('background-color') == 'transparent');  // if cell's background is transparent then change color
            if (other_selected != null)
                toUnchoosen(other_selected);
            $('#current_index').val(index);
        }
    });

    $("select.cascade").change(function () {
        var fileno = $(this).attr('id').split('-')[1];
        var index = $(this).attr('id').split('-')[2];
        var classList = $(this).attr('class').split(/\s+/);
        if (classList == null || classList.length == 0)
            return;
        var current_option = classList[0] == "cascade" ? classList[1] : classList[0];
        var current_option_val = $(this).val();
        index = parseInt(index) - 1;
        var next_selected = $('select#dropdown-' + fileno + '-' + index);
        classList = next_selected.attr('class').split(/\s+/);
        if (classList == null || classList.length == 0)
            return;
        var next_option = classList[0];
        var data = {
            'action': 'populate_next',
            'select': next_option,
            'field': current_option,
            'value': current_option_val,
            'cat': $('#category').val()
        };
        $.post(MyAjax.ajaxurl, data, function (response) {
            next_selected.html(response);
            next_selected.removeAttr('disabled');
            for (var i = index - 1; i > 0; i--) {
                $('select#dropdown-' + fileno + '-' + i).html('<option disabled selected> - בחר - </option>');
                $('select#dropdown-' + fileno + '-' + i).attr('disabled', true);
            }
        });
    });

});

function toUnchoosen (element) {
    element.removeClass("choosen");
    element.addClass("unchoosen");
    var col = element.parent().children().index(element);
    var row = element.parent().parent().children().index(element.parent());
    element.addClass('change-color-icon');
    $('#print-table tr').eq(row + 1).find('td').eq(col).addClass('change-color-name');
    $('#print-table tr').eq(row + 2).find('td').eq(col).addClass('change-color-file');
    invertUpload('transparent', 'black', invertColors(element.find('img').attr('src')));
    element.removeClass('change-color-icon');
    $('#print-table tr').eq(row + 1).find('td').eq(col).removeClass('change-color-name');
    $('#print-table tr').eq(row + 2).find('td').eq(col).removeClass('change-color-file');
    element.hover(enterPrintFile, leavePrintFile);
}

function toChoosen (element, changeColor) {
    element.removeClass("unchoosen");
    element.addClass("choosen");
    var col = element.parent().children().index(element);
    var row = element.parent().parent().children().index(element.parent());
    if (changeColor) {
        element.addClass('change-color-icon');
        $('#print-table tr').eq(row + 1).find('td').eq(col).addClass('change-color-name');
        $('#print-table tr').eq(row + 2).find('td').eq(col).addClass('change-color-file');
        invertUpload(GREY, 'white', invertColors(element.find('img').attr('src')));
    }
    element.removeClass('change-color-icon');
    $('#print-table tr').eq(row + 1).find('td').eq(col).removeClass('change-color-name');
    $('#print-table tr').eq(row + 2).find('td').eq(col).removeClass('change-color-file');
    $('.choosen').unbind('mouseenter mouseleave');
}