$(document).ready(function () {

    // set subprices and total price
    function setPrices(element, type, service, initializing) {
        var pre = service == 'print' ? 'p_' : 's_';
        var obj = $(element);
        if (isNaN(parseInt(obj.val())) || (type == 'quantity' && parseInt(obj.val()) < 0) || (type == 'pages' && parseInt(obj.val()) < 1))
            return;
        var row = obj.parent().parent().parent().children().index(obj.parent().parent());
        var index = row / 3;
        if (type == 'quantity' && $('#num_pages-' + index).length > 0 && isNaN(parseInt($('#num_pages-' + index).val())))
            return;
        if (type == 'pages' && isNaN(parseInt($('#' + pre + 'quantity-' + index).val())))
            return;
        var prev_subprice_text = $('#' + pre + 'subprice-' + index).text();
        var prev_subprice = parseFloat(prev_subprice_text.split('₪')[0]);
        var subprice = $('#' + pre + 'unit_price-' + index).val() * parseInt(obj.val());
        if (!isInt(subprice)) subprice = subprice.toPrecision(2);
        if (type == 'quantity' && service == 'print' && $('#num_pages-' + index).length > 0)
            subprice = subprice * parseInt($('#num_pages-' + index).val());
        else if (type == 'pages')
            subprice = subprice * parseInt($('#' + pre + 'quantity-' + index).val());
        $('#' + pre + 'subprice-' + index).html(subprice + '₪');

        if (!initializing) {
            var prev_total_price_text = $('#total_price').text();
            var prev_total_price = parseFloat(prev_total_price_text.split('₪')[0]);
            var total_price = prev_total_price - prev_subprice + subprice;
            $('#total_price').html(total_price + '₪');
        }
    }

    $('.s_q_input').each(function () {
        setPrices(this, 'quantity', 'sublimation', true);

        $(this).on('input', function () { // oninput
            setPrices(this, 'quantity', 'sublimation', false);
        });
    });

    $('.p_q_input').each(function () {
        setPrices(this, 'quantity', 'print', true);

        $(this).on('input', function () { // oninput
            setPrices(this, 'quantity', 'print', false);
        });
    });

    $('.p_input').on('input', function () {
        setPrices(this, 'pages', 'print', false);
    });

});

function inputStep3TextFocus(txt){
    if(txt.value==txt.defaultValue){ txt.value=""; }
}
function inputStep3TextBlur(txt, val){
    if(txt.value==""){ txt.value=val; }
}

function isInt(n) {
   return n % 1 === 0;
}
