/*** CLASS CanvasHistory ***/

function CanvasHistory() {
    this.undo = [];
    this.redo = [];
    this.step = 0;
    this.lastChange = 0;
}

CanvasHistory.prototype.nextStep = function () {
    this.step++;
};

CanvasHistory.prototype.prevStep = function () {
    this.step--;
};

CanvasHistory.prototype.enableUndo = function () {
    return this.step > 0;
};

CanvasHistory.prototype.enableRedo = function () {
    return this.step < this.lastChange;
};

CanvasHistory.prototype.getUndoCmd = function () {
    return this.undo[this.step];
};

CanvasHistory.prototype.getRedoCmd = function () {
    return this.redo[this.step];
};

CanvasHistory.prototype.saveColor = function (oldbg, oldbgfeature, newcolor) {
    this.redo[this.step++] = "background color " + newcolor;
    this.undo[this.step] = "background " + oldbg + " " + oldbgfeature;
    this.lastChange = this.step;
};

CanvasHistory.prototype.saveBackground = function (oldbg, oldbgfeature, newbgImg) {
    this.redo[this.step++] = "background bg " + newbgImg;
    this.undo[this.step] = "background " + oldbg + " " + oldbgfeature;
    this.lastChange = this.step;
};

CanvasHistory.prototype.saveImageAdd = function (src) {
    this.redo[this.step++] = "image add " + src;
    this.undo[this.step] = "image delete";
    this.lastChange = this.step;
};

CanvasHistory.prototype.saveImageDelete = function (src) {
    this.redo[this.step++] = "image delete";
    this.undo[this.step] = "image add " + src;
    this.lastChange = this.step;
};

CanvasHistory.prototype.saveResize = function (item, oldx, oldy, oldw, oldh, newx, newy, neww, newh) {
    if (newx != oldx || newy != oldy || neww != oldw || newh != oldh) {
        this.redo[this.step++] = item + " resize " + newx + " " + newy + " " + neww + " " + newh;
        this.undo[this.step] = item + " resize " + oldx + " " + oldy + " " + oldw + " " + oldh;
        this.lastChange = this.step;
    }
};

CanvasHistory.prototype.saveMove = function (item, oldx, oldy, newx, newy) {
    if (newx != oldx && newy != oldy) {
        this.redo[this.step++] = item + " move " + newx + " " + newy;
        this.undo[this.step] = item + " move " + oldx + " " + oldy;
        this.lastChange = this.step;
    }
};

CanvasHistory.prototype.saveSuperposition = function (item, index) {
    this.redo[this.step++] = item + " superposition " + index + " top";
    this.undo[this.step] = item + " superposition top " + index;
    this.lastChange = this.step;
};

CanvasHistory.prototype.saveRotate = function (item, direction) {
    this.redo[this.step++] = item + " rotate " + direction;
    this.undo[this.step] = item + " rotate " + (direction == 'left'? 'right' : 'left');
    this.lastChange = this.step;
};

CanvasHistory.prototype.saveTextAdd = function (text, font, size, color) {
    this.redo[this.step++] = "text add " + encodeURI(text) + " " + encodeURI(font) + " " + size + " " + color;
    this.undo[this.step] = "text delete";
    this.lastChange = this.step;
};

CanvasHistory.prototype.saveTextDelete = function (text, font, size, color) {
    this.redo[this.step++] = "text delete";
    this.undo[this.step] = "text add " + encodeURI(text) + " " + encodeURI(font) + " " + size + " " + color;
    this.lastChange = this.step;
};

/* text edit oldtext newtext
 * text size oldsize newsize
 * text font oldfont newfont
 * text color oldcolor newcolor
 * text align oldposition newposition
 */
CanvasHistory.prototype.saveTextFeature = function (feature, oldfeature, newfeature) {
    if (feature == 'edit' || feature == 'font') {
        oldfeature = encodeURI(oldfeature);
        newfeature = encodeURI(newfeature);
    }
    else if (feature == 'align')
        feature = '';
    else
        feature += ' ';
    this.redo[this.step++] = "text " + feature + newfeature;
    this.undo[this.step] = "text " + feature + oldfeature;
    this.lastChange = this.step;
};

CanvasHistory.prototype.saveTextEmphasis = function (emphasis) {
    this.redo[this.step++] = "text " + emphasis;
    this.undo[this.step] = "text " + emphasis;
    this.lastChange = this.step;
};

/*** END CLASS CanvasHistory ***/

/*** CLASS ImageItem ***/

function ImageItem(w, h) {
    this.src = "";
    this.rotate = 0;
    this.select = false;
    this.drag = false;
    this.resize = { top: false, right: false, bottom: false, left: false};
    var dim = setInitialImageDim(w, h);
    this.x = dim.ix;
    this.y = dim.iy;
    this.width = dim.iwidth;
    this.height = dim.iheight;
    this.proportion = this.width / this.height;
}

//rotate canvas system around the center of the image, after using this function we must restore the system coordinates
ImageItem.prototype.rotateSystem = function () {
    rotateSystem(this, this.x, this.y, this.width, this.height);
};

//check if the mouse is in the relevant area of the image: in the image or in one of the resize boxes
ImageItem.prototype.mouseIn = function (area, xpos, ypos) {
    return mouseIn(this, area, xpos, ypos, this.width, this.height);
};

ImageItem.prototype.drawImageItem = function (loaded_img) {

    this.rotateSystem();                                                              
    od_context.drawImage(loaded_img, -(this.width / 2), -(this.height / 2), this.width, this.height);   //draw the image
    od_context.restore();                                                                               //restore the coordinates

    if (this.select)
        this.drawSelect();
};

ImageItem.prototype.drawSelect = function () {
    drawSelect(this, this.width, this.height);
};

ImageItem.prototype.move = function (mouseX, mouseY) {
    if (mouseX < this.width / 2) this.x = 0;
    else if (mouseX > online_designer.width - this.width / 2) this.x = online_designer.width - this.width;
    else this.x = mouseX - this.width / 2;

    if (mouseY < this.height / 2) this.y = 0;
    else if (mouseY > online_designer.height - this.height / 2) this.y = online_designer.height - this.height;
    else this.y = mouseY - this.height / 2;
};

//enable the relevant resize (top, right, bottom or left) according to the mouse position
ImageItem.prototype.enableResize = function (xpos, ypos) {
    return enableResize(this, xpos, ypos);
};

ImageItem.prototype.disableResize = function () {
    disableResize(this);
};

//check wether the image is currently being resized
ImageItem.prototype.resizing = function () {
    return resizing(this);
};

ImageItem.prototype.resizeImage = function (xpos, ypos) {
    resize(this, xpos, ypos);
};

// when resizing an image, it keeps its proportions

ImageItem.prototype.resizeTop = function (y) {
    var y_bottom = this.y + this.height;
    var new_height = y_bottom - y;
    var new_width = new_height * this.proportion;
    if (y < y_bottom && new_height + this.y < online_designer.height && new_width + this.x < online_designer.width) {
        this.height = new_height;
        this.width = new_width;
        this.y = y;
    }
};

ImageItem.prototype.resizeRight = function (x) {
    var new_width = x - this.x;
    var new_height = new_width / this.proportion;
    if (x > this.x && new_height + this.y < online_designer.height && new_width + this.x < online_designer.width) {
        this.height = new_height;
        this.width = new_width;
    }
};

ImageItem.prototype.resizeBottom = function (y) {
    var new_height = y - this.y;
    var new_width = new_height * this.proportion;
    if (y > this.y && new_height + this.y < online_designer.height && new_width + this.x < online_designer.width) {
        this.height = new_height;
        this.width = new_width;
    }
};

ImageItem.prototype.resizeLeft = function (x) {
    var x_right = this.x + this.width;
    var new_width = x_right - x;
    var new_height = new_width / this.proportion;
    if (x < x_right && this.y + new_height < online_designer.height && this.x + new_width < online_designer.width) {
        this.height = new_height;
        this.width = new_width;
        this.x = x;
    }
};

/*** END CLASS ImageItem ***/

/*** CLASS TextItem ***/

function TextItem (text, family, size, color) {
    this.text = text;
    this.words = [];
    this.biggest_word_index = 0;    // holds the biggest word's index so we can set the minimum width of the select box according to the biggest word
    this.parseText();               // set words[] and biggest_word_index
    this.lines = [];
    this.lines[0] = text;
    this.biggest_line_index = 0;
    this.x = online_designer.width / 2;
    this.y = online_designer.height / 2;  
    this.color = color;
    this.textAlign = 'center';
    this.fontFamily = family;
    this.fontSize = size;
    this.offset = (this.fontSize / 4) < (2 * RESOLUTION_FACTOR) ? (2 * RESOLUTION_FACTOR) : this.fontSize / 4;
    this.bold = '';
    this.italic = '';
    this.underline = {};
    this.underline['enabled'] = false;
    this.setUnderlineThickness();
    this.select = {};
    this.select['enabled'] = false;
    this.setSelectValues();
    while (this.select['width'] >= online_designer.width || this.select['height'] >= online_designer.height) {
        this.fontSize--;
        this.setSelectValues();
    }
    $('#font-size').val(this.fontSize);
    this.drag = false;
    this.resize = { right: false, left: false};
    this.rotate = 0;
}

//parse the text into words and set biggest_word_index, called whenever the text is changed
TextItem.prototype.parseText = function () {
    this.words = this.text.split(" ");
    var biggest_word_length = 0;
    var biggest_word_index = 0;
    for (var i = 0; i < this.words.length; i++) {
        if (this.words[i].length > biggest_word_length) {
            biggest_word_length = this.words[i].length;
            biggest_word_index = i;
        }
    }
    this.biggest_word_index = biggest_word_index;
};

//called when text is changed or text item is created
TextItem.prototype.setSelectValues = function () {
    var fsize = RESOLUTION_FACTOR * this.fontSize;
    od_context.font = this.italic + this.bold + fsize + 'px ' + this.fontFamily;
    var biggest_line_width = od_context.measureText(this.lines[this.biggest_line_index]).width;

    this.select['x'] = this.x;
    switch (this.textAlign) {
        case "center":
            this.select['x'] -= biggest_line_width / 2;
            break;
        case "right":
            this.select['x'] -= biggest_line_width;
            break;
        default:
            break;
    }
    this.select['x'] -= this.offset;

    this.select['y'] = this.y - RESOLUTION_FACTOR * this.fontSize - this.offset;
    this.select['min_width'] = od_context.measureText(this.words[this.biggest_word_index]).width + 2 * this.offset;
    this.select['width'] = biggest_line_width + 2 * this.offset;
    this.select['height'] = this.lines.length * (RESOLUTION_FACTOR * this.fontSize + (3 * this.underline['thickness'])) + (2 * this.offset);
};

TextItem.prototype.setSelectHeight = function () {
    this.select['height'] = this.lines.length * (RESOLUTION_FACTOR * this.fontSize + (3 * this.underline['thickness'])) + (2 * this.offset);
};

// called when the select box was moved or resized, or text alignment changed
TextItem.prototype.setXY = function () {
    this.x = this.select['x'];
    switch (this.textAlign) {
        case "center": this.x += (this.select['width'] / 2); break;
        case "right": this.x += this.select['width'] - this.offset; break;
        case "left": this.x += this.offset; break;
        default: break;
    }
    this.y = this.select['y'] + RESOLUTION_FACTOR * this.fontSize + this.offset;
};

// set lines array and biggest line's index, used when resizing the select box
TextItem.prototype.setLines = function () {
    this.lines = [];
    var i = 0;
    this.lines[i] = '';
    var testLine = '';
    var biggest_line_length = 0;
    var biggest_line_index = 0;
    var fsize = RESOLUTION_FACTOR * this.fontSize;
    od_context.font = this.italic + this.bold + fsize + 'px ' + this.fontFamily;
    var new_line = true;

    for (var n = 0; n < this.words.length; n++) {

        testLine = this.lines[i] + this.words[n] + (n == this.words.length - 1 ? '' : ' ');
        if (od_context.measureText(testLine).width > this.select['width'] - 2 * this.offset && n > 0) {
            this.lines[i] = this.lines[i].trim();   // delete last space of the line
            if (this.lines[i].length > biggest_line_length) {
                biggest_line_length = this.lines[i].length;
                biggest_line_index = i;
            }
            i++;
            this.lines[i] = this.words[n] + ' ';
        }
        else
            this.lines[i] = testLine;
    }
    this.lines[i] = this.lines[i].trim();   //delete last space of the last line
    if (this.lines[i].length > biggest_line_length) {
        biggest_line_length = this.lines[i].length;
        biggest_line_index = i;
    }
    this.biggest_line_index = biggest_line_index;
};

//called when select box was moved, or text or font or size or emphasis or alignment was changed
TextItem.prototype.setUnderlineThickness = function () {
    this.underline['thickness'] = RESOLUTION_FACTOR * (this.fontSize) / 15;
    //underline['thickness'] in minimum 1px
    if (this.underline['thickness'] < RESOLUTION_FACTOR)
        this.underline['thickness'] = RESOLUTION_FACTOR;
};

//rotate canvas system around the center of the text (actually the center of the select area), after using this function we must restore the system coordinates
TextItem.prototype.rotateSystem = function () {
    rotateSystem(this, this.select['x'], this.select['y'], this.select['width'], this.select['height']);                                                          //rotate around that point
};

//check if the mouse is in the relevant area of the text: in the select area or in one of the resize boxes
TextItem.prototype.mouseIn = function (area, xpos, ypos) {
    return mouseIn(this, area, xpos, ypos, this.select['width'], this.select['height']);
};

TextItem.prototype.editText = function (new_text) {
    this.text = new_text;
    this.lines = [];
    this.parseText();
    this.setLines();
    this.setSelectValues();
    while (this.select['x'] + this.select['width'] >= online_designer.width || this.select['y'] + this.select['height'] >= online_designer.height) {
        this.fontSize--;
        this.setSelectValues();
    }
    $('#font-size').val(this.fontSize);
}

TextItem.prototype.setFont = function (new_font) {
    this.fontFamily = new_font;
    this.setSelectValues();
    while (this.select['x'] + this.select['width'] >= online_designer.width || this.select['y'] + this.select['height'] >= online_designer.height) {
        this.fontSize--;
        this.setSelectValues();
    }
    $('#font-size').val(this.fontSize);
}

TextItem.prototype.setFontSize = function (new_size) {
    this.fontSize = new_size;
    this.underline['thickness'] = RESOLUTION_FACTOR * (this.fontSize) / 15;
    //underline['thickness'] in minimum 1px
    if (this.underline['thickness'] < RESOLUTION_FACTOR)
        this.underline['thickness'] = RESOLUTION_FACTOR;
    this.offset = (this.fontSize / 4) < (2 * RESOLUTION_FACTOR) ? (2 * RESOLUTION_FACTOR) : this.fontSize / 4;
    var fsize = RESOLUTION_FACTOR * this.fontSize;
    od_context.font = this.italic + this.bold + fsize + 'px ' + this.fontFamily;
    this.setSelectValues();
    while (this.select['x'] + this.select['width'] >= online_designer.width || this.select['y'] + this.select['height'] >= online_designer.height) {
        this.fontSize--;
        this.setSelectValues();
    }
    $('#font-size').val(this.fontSize);
};

TextItem.prototype.changeEmphasis = function (type) {
    if (type == 'underline') {
        this.underline['enabled'] = this.underline['enabled'] ? false : true;
        return;
    }
    if (type == 'bold')
        this.bold = this.bold == '' ? 'bold ' : '';
    else if (type == 'italic')
        this.italic = this.italic == '' ? 'italic ' : '';

    //adjust select box around the text:
    var fsize = RESOLUTION_FACTOR * this.fontSize;
    od_context.font = this.italic + this.bold + fsize + 'px ' + this.fontFamily;
    this.select['width'] = od_context.measureText(this.lines[this.biggest_line_index]).width + 2 * this.offset;
    this.select['min_width'] = od_context.measureText(this.words[this.biggest_line_word]).width + 2 * this.offset;
    this.setXY();
    this.setSelectHeight();
    while (this.select['x'] + this.select['width'] >= online_designer.width || this.select['y'] + this.select['height'] >= online_designer.height) {
        this.fontSize--;
        this.setSelectValues();
    }
    $('#font-size').val(this.fontSize);
};

TextItem.prototype.alignText = function (new_position) {
    this.textAlign = new_position;
    this.setXY();
}

TextItem.prototype.drawTextItem = function () {
    var x = this.x;
    var y = this.y;
    var half_width = this.select['width'] / 2;
    var half_height = this.select['height'] / 2;

    var fsize = RESOLUTION_FACTOR * this.fontSize;
    od_context.font = this.italic + this.bold + fsize + 'px ' + this.fontFamily;
    od_context.fillStyle = this.color;
    od_context.textAlign = this.textAlign;

    this.rotateSystem();

    for (var i = 0; i < this.lines.length; i++) {
        od_context.fillText(this.lines[i], x - this.select['x'] - half_width, y - this.select['y'] - half_height);
        y += RESOLUTION_FACTOR * this.fontSize + 4 * this.underline['thickness'];
    }

    od_context.restore();   //restore the coordinates

    if (this.underline['enabled'])
        this.drawUnderline();
    if (this.select['enabled'])
        this.drawSelect();
};

TextItem.prototype.drawUnderline = function () {
    var half_width = this.select['width'] / 2;
    var half_height = this.select['height'] / 2;
    var x, line_width;
    var y = this.y + 2 * this.underline['thickness'];
    var fsize = RESOLUTION_FACTOR * this.fontSize;
    od_context.font = this.italic + this.bold + fsize + 'px ' + this.fontFamily;
    od_context.beginPath();
    od_context.strokeStyle = this.color;
    od_context.lineWidth = this.underline['thickness'];

    this.rotateSystem();

    for (var i = 0; i < this.lines.length; i++) {
        line_width = od_context.measureText(this.lines[i]).width;
        x = this.x;
        switch (this.textAlign) {
            case "center": x -= (line_width / 2); break;
            case "right": x -= line_width; break;
            default: break;
        }
        od_context.moveTo(x - this.select['x'] - half_width, y - this.select['y'] - half_height);
        od_context.lineTo(x - this.select['x'] + line_width - half_width, y - this.select['y'] - half_height);
        od_context.stroke();
        y += RESOLUTION_FACTOR * this.fontSize + 4 * this.underline['thickness'];
    }

    od_context.restore();   //restore the coordinates
};

TextItem.prototype.drawSelect = function () {
    drawSelect(this, this.select['width'], this.select['height']);
};

TextItem.prototype.move = function (mouseX, mouseY) {
    if (mouseX < this.select['width'] / 2) this.select['x'] = 0;
    else if (mouseX > online_designer.width - this.select['width'] / 2) this.select['x'] = online_designer.width - this.select['width'];
    else this.select['x'] = mouseX - this.select['width'] / 2;

    if (mouseY < this.select['height'] / 2) this.select['y'] = 0;
    else if (mouseY > online_designer.height - this.select['height'] / 2) this.select['y'] = online_designer.height - this.select['height'];
    else this.select['y'] = mouseY - this.select['height'] / 2;

    this.setXY();
};

//enable the relevant resize (top, right, bottom or left) according to the mouse position
TextItem.prototype.enableResize = function (xpos, ypos) {
    return enableResize(this, xpos, ypos);
};

TextItem.prototype.disableResize = function () {
    disableResize(this);
};

//check wether the image is currently being resized
TextItem.prototype.resizing = function () {
    return resizing(this);
};

TextItem.prototype.resizeTextBox = function (xpos, ypos) {
    resize(this, xpos, ypos);
    this.setXY();
    this.setLines();
    this.setSelectHeight();
};

TextItem.prototype.resizeRight = function (x) {
    var new_width = x - this.select['x'];
    if (x > this.select['x'] && this.select['x'] + new_width < online_designer.width && new_width >= this.select['min_width'])
        this.select['width'] = new_width;
};

TextItem.prototype.resizeLeft = function (x) {
    var x_right = this.select['x'] + this.select['width'];
    var new_width = x_right - x;
    if (x < x_right && new_width >= this.select['min_width']) {
        this.select['width'] = new_width;
        this.select['x'] = x;
    }
};

/*** END CLASS TextItem ***/

/***** CLASS HELP FUNCTIONS *****/

//returns initial dimensions and positions of an image (center of the canvas), called when a new ImageItem is created
function setInitialImageDim (width, height) {
    var max_width = online_designer.width;
    var max_height = online_designer.height;
    var initial_width = max_width / 2;
    var initial_height = initial_width * height / width;
    if (initial_height > max_height / 2) {
        initial_height = max_height / 2;
        initial_width = initial_height * width / height;
    }
    var initial_x = (max_width - initial_width) / 2;
    var initial_y = (max_height - initial_height) / 2;
    return {ix:initial_x, iy:initial_y, iwidth: initial_width, iheight: initial_height};
}

//check if the mouse is in the relevant area of the item
function mouseIn (obj, area, xpos, ypos, width, height) {
    var half_width = width / 2;
    var half_height = height / 2;
    obj.rotateSystem();
    od_context.beginPath();
    var isIn = false;
    switch (area) {
        case 'item':
            od_context.rect(-half_width, -half_height, width, height);
            break;
        case 'top_box':
            od_context.rect(- (1/RESOLUTION_FACTOR) * half_box_size, - (1/RESOLUTION_FACTOR) * half_box_size - half_height, box_size, box_size);
            break;
        case 'right_box':
            od_context.rect(half_width - (1/RESOLUTION_FACTOR) * half_box_size, - (1/RESOLUTION_FACTOR) * half_box_size, box_size, box_size);
            break;
        case 'bottom_box':
            od_context.rect(- (1/RESOLUTION_FACTOR) * half_box_size, half_height - (1/RESOLUTION_FACTOR) * half_box_size, box_size, box_size);
            break;
        case 'left_box':
            od_context.rect(- (1/RESOLUTION_FACTOR) * half_box_size - half_width, - (1/RESOLUTION_FACTOR) * half_box_size, box_size, box_size);
            break;
        default:
            return false;
    }
    var isIn = od_context.isPointInPath(xpos, ypos);
    od_context.restore();   //restore system coordinates
    return isIn;
}

function rotateSystem (obj, x, y, w, h) {
    var half_width = w / 2;
    var half_height = h / 2;
    od_context.save();                                      //save the current coordinates system
    od_context.translate(x + half_width, y + half_height);  //move to the center of the item
    od_context.rotate(obj.rotate);                         //... and rotate
}

//draw select for an object: ImageItem or TextItem
function drawSelect(obj, width, height) {
    var half_width = width / 2;
    var half_height = height / 2;

    od_context.strokeStyle = 'darkred';
    od_context.fillStyle = 'darkred';
    //if the background is a color closed to darkred, the color of the select rectangle and the resize boxes will be navy, so the user can see them clearly
    if (background == 'color' && looksLikeDarkred(od_color)) {
        od_context.strokeStyle = 'navy';
        od_context.fillStyle = 'navy';
    }
    od_context.lineWidth = 2 * RESOLUTION_FACTOR;

    obj.rotateSystem(); 

    od_context.strokeRect(- half_width, - half_height, width, height);                          //draw the border

    if (obj instanceof ImageItem) {
        od_context.fillRect(- half_box_size, - half_box_size - half_height, box_size, box_size);    //draw top resize box
        od_context.fillRect(- half_box_size, half_height - half_box_size, box_size, box_size);      //draw bottom resize box
    }
    
    od_context.fillRect(half_width - half_box_size,  - half_box_size, box_size, box_size);      //draw right resize box
    od_context.fillRect(- half_box_size - half_width, - half_box_size, box_size, box_size);     //draw left resize box

    od_context.restore();                                                                       //restore the coordinates
}

//called by drawSelect: checked if the color h is a color closed to darkred
function looksLikeDarkred(h) {
    var rgb = hexToRGB(h);
    return (rgb.r >= 71 && rgb.r <= 207 && rgb.g == rgb.b && rgb.g >= 0 && rgb.g <= 41);
}

//called by looksLikeDarkred
function hexToRGB(h) {
    return { r: hexToR(h), g: hexToG(h), b: hexToB(h) };
}
function hexToR(h) {return parseInt((cutHex(h)).substring(0,2),16)}
function hexToG(h) {return parseInt((cutHex(h)).substring(2,4),16)}
function hexToB(h) {return parseInt((cutHex(h)).substring(4,6),16)}
function cutHex(h) {return (h.charAt(0)=="#") ? h.substring(1,7):h}

function enableResize (obj, xpos, ypos) {
    if (obj instanceof ImageItem && obj.mouseIn('top_box', xpos, ypos))
        obj.resize['top'] = true;
    else if (obj.mouseIn('right_box', xpos, ypos))
        obj.resize['right'] = true;
    else if (obj instanceof ImageItem && obj.mouseIn('bottom_box', xpos, ypos))
        obj.resize['bottom'] = true;
    else if (obj.mouseIn('left_box', xpos, ypos))
        obj.resize['left'] = true;
    else
        return false;
    return true;
}

function disableResize (obj) {
    for (var pos in obj.resize)
        obj.resize[pos] = false;
}

//check wether the item is currently being resized
function resizing (obj) {
    for (var pos in obj.resize) {
        if (obj.resize[pos])
            return true;
    }
    return false;
}

function resize (obj, xpos, ypos) {
    if (obj instanceof ImageItem && obj.resize['top'])
        obj.resizeTop(ypos);
    else if (obj.resize['right'])
        obj.resizeRight(xpos);
    else if (obj instanceof ImageItem && obj.resize['bottom'])
        obj.resizeBottom(ypos);
    else if (obj.resize['left'])
        obj.resizeLeft(xpos);
}