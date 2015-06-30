/* global and functions used in the admin screens */

var canvas_initial_size = 300;
var canvas;
var context;

var sub_area;   // x, y, width, height, proportion, select, drag, resize['top'], resize['right'], resize['bottom'], resize['left']

var product_img = null;

var box_size = 10;
var half_box_size = 5;

/***** CLASS SubArea *****/

//calculate new dimensions of the area rectangle, called when the sublimation dimensions are changed
function SubArea(w, h) {
    this.select = false;
    this.drag = false;
    this.resize = [];
    this.resize['top'] = false;
    this.resize['right'] = false;
    this.resize['bottom'] = false;
    this.resize['left'] = false;

    this.proportion = w / h;
    if (this.proportion < 1) {
        this.height = canvas.height / 2;
        this.width = this.height * this.proportion;
    }
    else {
        this.width = canvas.width / 2;
        this.height = this.width / this.proportion;
    }
    this.x = (canvas.width - this.width) / 2;
    this.y = (canvas.height - this.height) / 2;

    set_hidden_x(this.x / canvas.width);
    set_hidden_y(this.y / canvas.height);
    set_hidden_width(this.width / canvas.width);
}

//check if mouse(x,y) is in area
SubArea.prototype.mouseIsIn = function (x, y) {
    return (x > this.x && x < this.x + this.width && y > this.y && y < this.y + this.height);
};

//check if mouse(x,y) is in top resize box
SubArea.prototype.mouseInTopBox = function (x, y) {
    var half_width = this.width / 2;
    return (x > this.x + half_width - half_box_size && x < this.x + half_width + half_box_size && y > this.y - half_box_size && y < this.y + half_box_size);
};

//check if mouse(x,y) is in right resize box
SubArea.prototype.mouseInRightBox = function (x, y) {
    var half_height = this.height / 2;
    return (x > this.x + this.width - half_box_size && x < this.x + this.width + half_box_size && y > this.y + half_height - half_box_size && y < this.y + half_height + half_box_size);
};

//check if mouse(x,y) is in bottom resize box
SubArea.prototype.mouseInBottomBox = function (x, y) {
    var half_width = this.width / 2;
    return (x > this.x + half_width - half_box_size && x < this.x + half_width + half_box_size && y > this.y + this.height - half_box_size && y < this.y + this.height + half_box_size);
};

//check if mouse(x,y) is in left resize box
SubArea.prototype.mouseInLeftBox = function (x, y) {
    var half_height = this.height / 2;
    return (x > this.x - half_box_size && x < this.x + half_box_size && y > this.y + half_height - half_box_size && y < this.y + half_height + half_box_size);
};

//enable the relevant resize (top, right, bottom or left)
SubArea.prototype.enableResize = function (x, y) {
    if (this.mouseInTopBox(x, y))
        this.resize['top'] = true;
    else if (this.mouseInRightBox(x, y))
        this.resize['right'] = true;
    else if (this.mouseInBottomBox(x, y))
        this.resize['bottom'] = true;
    else if (this.mouseInLeftBox(x, y))
        this.resize['left'] = true;
    else
        return false;
    return true;
};

SubArea.prototype.disableResize = function () {
    this.resize['top'] = false;
    this.resize['right'] = false;
    this.resize['bottom'] = false;
    this.resize['left'] = false;
};

//check wether the area is currently being resized
SubArea.prototype.resizing = function () {
    return (this.resize['top'] || this.resize['right'] || this.resize['bottom'] || this.resize['left']);
};

//calculate new position of x and y (top left point) of the sublimation area, called when dragging it
SubArea.prototype.move = function (x, y) {
    if (x < this.width / 2) this.x = 0;
    else if (x > canvas.width - this.width / 2) this.x = canvas.width - this.width;
    else this.x = x - this.width / 2;

    if (y < this.height / 2) this.y = 0;
    else if (y > canvas.height - this.height / 2) this.y = canvas.height - this.height;
    else this.y = y - this.height / 2;

    set_hidden_x(this.x / canvas.width);
    set_hidden_y(this.y / canvas.height);
};

SubArea.prototype.resizeArea = function (x, y) {
    if (this.resize['top'])
        this.resizeTop(y);
    else if (this.resize['right'])
        this.resizeRight(x);
    else if (this.resize['bottom'])
        this.resizeBottom(y);
    else if (this.resize['left'])
        this.resizeLeft(x);
};

SubArea.prototype.resizeTop = function (y) {
    var y_bottom = this.y + this.height;
    var new_height = this.height - y + this.y;
    var new_width = new_height * this.proportion;
    if (y < y_bottom && new_height + this.y < canvas.height && new_width + this.x < canvas.width) {
        this.height = new_height;
        this.width = new_width;
        this.y = y;
        set_hidden_width(this.width / canvas.width);
        set_hidden_y(this.y / canvas.height);
    }
};

SubArea.prototype.resizeRight = function (x) {
    var new_width = x - this.x;
    var new_height = new_width / this.proportion;
    if (x > this.x && new_height + this.y < canvas.height && new_width + this.x < canvas.width) {
        this.height = new_height;
        this.width = new_width;
        set_hidden_width(this.width / canvas.width);
    }
};

SubArea.prototype.resizeBottom = function (y) {
    var new_height = y - this.y;
    var new_width = new_height * this.proportion;
    if (y > this.y && new_height + this.y < canvas.height && new_width + this.x < canvas.width) {
        this.height = new_height;
        this.width = new_width;
        set_hidden_width(this.width / canvas.width);
    }
};

SubArea.prototype.resizeLeft = function (x) {
    var x_right = this.x + this.width;
    var new_width = this.width - x + this.x;
    var new_height = new_width / this.proportion;
    if (x < x_right && new_height + this.y < canvas.height && new_width + this.x < canvas.width) {
        this.height = new_height;
        this.width = new_width;
        this.x = x;
        set_hidden_width(this.width / canvas.width);
        set_hidden_x(this.x / canvas.width);
    }
};

SubArea.prototype.draw = function () {
    context.fillStyle = 'darkcyan';
    context.fillRect(this.x, this.y, this.width, this.height);
    if (this.select)
        this.drawSelect();
};

SubArea.prototype.drawSelect = function () {
    var half_width = this.width / 2;
    var half_height = this.height / 2;
    context.strokeStyle = 'darkred';
    context.fillStyle = 'darkred';
    context.lineWidth = 2;
    context.strokeRect(this.x, this.y, this.width, this.height);  //draw darkred border around the sublimation area
    //draw 4 resize boxes:
    context.fillRect(this.x + half_width - half_box_size, this.y - half_box_size, box_size, box_size);                   //top
    context.fillRect(this.x + this.width - half_box_size, this.y + half_height - half_box_size, box_size, box_size);     //right
    context.fillRect(this.x + half_width - half_box_size, this.y + this.height - half_box_size, box_size, box_size);     //bottom
    context.fillRect(this.x - half_box_size, this.y + half_height - half_box_size, box_size, box_size);                  //left
};

/***** END OF CLASS SubArea *****/

/***** SET HIDDEN FIELDS *****/

function set_hidden_x(x) {
    document.getElementById('x_pos').value = x;
}

function set_hidden_y(y) {
    document.getElementById('y_pos').value = y;
}

function set_hidden_width(w) {
    document.getElementById('width_pos').value = w;
}

/***** END OF SET HIDDEN FIELDS *****/
  
//gets mouse position according to canvas coordinates
function getMousePos(evt) {
    var rect = canvas.getBoundingClientRect();
    return {
        x: evt.clientX - rect.left,
        y: evt.clientY - rect.top
    };
}

//clear canvas and draw the product image
function drawBackground() {
    if (product_img != null) {
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.drawImage(product_img, 0, 0, canvas.width, canvas.height);
    }
}

$(document).ready(function () {

    canvas = document.getElementById('myCanvas');
    context = canvas.getContext('2d');

    canvas.addEventListener('mousedown', function (evt) {
        var mousePos = getMousePos(evt);
        // if the mouse is in sublimation area then enable drag and select
        if (sub_area.mouseIsIn(mousePos.x, mousePos.y)) {
            sub_area.select = true;
            sub_area.drag = true;
        }
        // if the mouse is outside the rectangle and we are not resizing it, then disable select
        else if (!sub_area.enableResize(mousePos.x, mousePos.y))
            sub_area.select = false;
        // if select is enabled and the mouse is in one of the resize boxes, then disable drag and enable relevant resize flag this action is done by calling sub_area.resize)
        if (sub_area.select && sub_area.enableResize(mousePos.x, mousePos.y))
            sub_area.drag = false;
        drawBackground();
        if (!document.getElementById('isMug').checked)
            sub_area.draw();
    }, false);

    canvas.addEventListener('mouseup', function (evt) {
        sub_area.drag = false;
        sub_area.disableResize();
        drawBackground();
        if (!document.getElementById('isMug').checked)
            sub_area.draw();
    }, false);

    canvas.addEventListener('mousemove', function (evt) {
        var mousePos = getMousePos(evt);
        //change cursor look
        if (sub_area.select) {
            if (sub_area.mouseInTopBox(mousePos.x, mousePos.y) || sub_area.mouseInBottomBox(mousePos.x, mousePos.y))
                $('html,body').css('cursor', 'ns-resize');
            else if (sub_area.mouseInRightBox(mousePos.x, mousePos.y) || sub_area.mouseInLeftBox(mousePos.x, mousePos.y))
                $('html,body').css('cursor', 'ew-resize');
            else if (sub_area.drag)
                $('html,body').css('cursor', 'move');
            else if (!sub_area.resizing())
                $('html,body').css('cursor', 'default');
        }
        if (sub_area.resizing())
            sub_area.resizeArea(mousePos.x, mousePos.y);
        else if (sub_area.drag)
            sub_area.move(mousePos.x, mousePos.y);
        drawBackground();
        if (!document.getElementById('isMug').checked)
            sub_area.draw();
    }, false);

    //when admin fills/deletes one of the text fields, check if all required fields are filled and enable/disable the submit button
    $('#product_name, #product_price, #product_height, #product_width').bind('keyup', function () {
        if (allFilled('.required'))
            $('#add_product').removeAttr('disabled');
        else
            $('#add_product').attr('disabled', true);
    });

    //when admin fills/deletes one of the sublimation area dimension fields
    $('#product_height, #product_width').bind('keyup', function () {
        //if the admin already uploaded an image for the product, draw image
        if (document.getElementById('image_file').value != '') {
            drawBackground();
            //if all dimensions are filled and the product isn't a mug, create sub_area object and draw it
            if (allFilled('.dimension') && !document.getElementById('isMug').checked) {
                sub_area = new SubArea(document.getElementById('product_width').value, document.getElementById('product_height').value);
                sub_area.draw();
            }
        }

    });

    //when "product is mug" is checked/unchecked, if checked then draw only product picture, if unchecked and dublimation area dimensions are filled, then draw also sublimation area
    $(function () {
        $('#isMug').change(function () {
            if (document.getElementById('image_file').value != '') {
                drawBackground();
                if (allFilled('.dimension') && !this.checked)
                    sub_area.draw();    //draw sub_area
            }
        });
    });

    //when the product image is changed, check wether enable or disable submit button, and show image (on canvas)
    $(function () {
        $('#image_file').change(function () {
            if (allFilled('.dimension'))
                sub_area = new SubArea(document.getElementById('product_width').value, document.getElementById('product_height').value);
            readImage(this);
            if (allFilled('.required'))
                $('#add_product').removeAttr('disabled');
            else
                $('#add_product').attr('disabled', true);
            $('#myCanvas').removeAttr('hidden');
        });
    });

    //when the product icon is changed, check wether enable or disable submit button, and show icon
    $(function () {
        $('#icon_file').change(function () {
            readIcon(this);
            if (allFilled('.required'))
                $('#add_product').removeAttr('disabled');
            else
                $('#add_product').attr('disabled', true);
            $('#icon_file_preview').removeAttr('hidden');
        });
    });

    //check if all required fields are filled
    function allFilled(wanted_class) {
        var filled = true;
        $(wanted_class).each(function () {
            if ($(this).val().trim() == '')
                filled = false;
        });
        return filled;
    }

    //function to read and show the image input before submiting form
    function readImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            product_img = new Image();

            product_img.onload = function () {
                var image_width = this.width;
                var image_height = this.height;
                var width, height;
                if (image_width > image_height) {
                    width = canvas_initial_size;
                    height = (image_height / image_width) * canvas_initial_size;
                }
                else {
                    height = canvas_initial_size;
                    width = (image_width / image_height) * canvas_initial_size;
                }
                canvas.width = width;
                canvas.height = height;

                drawBackground();
                //if dimensions are filled and product isn't a mug, we want the admin to draw the sublimation area
                if (allFilled('.dimension') && !document.getElementById('isMug').checked)
                    sub_area.draw();
            };

            reader.onload = function (e) {
                product_img.src = e.target.result;
            };

            reader.readAsDataURL(input.files[0]);
        }
    }

    //read and show the image input for the product icon before submitting form
    function readIcon(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                $('#icon_file_preview')
            .attr('src', e.target.result)
            .width(100)
            .height(auto);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

});