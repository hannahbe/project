$(document).ready(function () {

    // display another upload and quantity fields on click on "Add file"
    $("input[value='Add file']").click(function (event) {
        event.preventDefault();
        var count = $(".input_file").length;
        $("label.input_file:last").clone().find('input').attr({
            'id': 'input_file[' + count + ']',
            'name': 'input_file[' + count + ']'
        }).val("").end().insertAfter("label.input_quantity:last");
        $("label.input_quantity:last").clone().find('input').attr({
            'id': 'input_quantity[' + count + ']',
            'name': 'input_quantity[' + count + ']'
        }).val("1").end().insertAfter("label.input_file:last");
        $('#remove_file').removeAttr('disabled');
    });

    // remove last upload and quantity fields on click on "Remove file"
    $("input[value='Remove file']").click(function (event) {
        event.preventDefault();
        $("label.input_quantity:last").remove();
        $("label.input_file:last").remove();
        if (!canRemove())
            $('#remove_file').attr('disabled', true);
    });

    //return true if we can remove last upload and quantity fields (= if there are at least 2 upload fields)
    function canRemove() {
        var remove = false;
        var e = document.getElementsByClassName("input_file");
        if (e.length > 1)
            remove = true;
        return remove;
    }

});