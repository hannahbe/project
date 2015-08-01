/* globals and functions used in sublimation service step 2: the online designer */

var BACKSPACE = 8, ENTER = 13, DELETE = 46, LEFT_ARROW = 37, RIGHT_ARROW = 39;
var RESOLUTION_FACTOR = 2;
var ROTATION = 0.02;
var box_size = 6 * RESOLUTION_FACTOR;          //size of the resize boxes
var half_box_size = 3 * RESOLUTION_FACTOR;

var design_history;

var online_designer;        //online designer canvas
var od_context;             //online designer canvas context
var background = 'color';   //possible values: 'color' or 'bg'
var od_bg = [];             //online designer background - od_bg['image'], od_bg['src_width'], od_bg['src_height']
var od_color = 'white';     //online designer background color

var tooltip;                //tooltip canvas
var tt_context;             //tooltip canvas context

var txtArray = [];

//both arrays below will be sorted according to images superposition: from bottom image to top image
var imgArray = [];          //the array of ImageItems with src, current x and y (top left coordinates), current width and height, proportion, rotate, select, drag, resize
var imgLoaded = [];         //the array that holds loaded images

var preview;                //preview canvas
var preview_context;        //preview canvas context
var preview_product;        //the product image
var preview_design = [];    //values of the image that will appear on the product as a design preview - preview_design['x'], preview_design['y'], preview_design['width'], preview_design['height']

var enable_canvas;
var drag_image = [];        //used to save the coordinates of an image before dragging it
var resize_image = [];      //used to save the coordinates of an image before resizing it
var drag_text = [];         //used to save the coordinates of a text select box before dragging it
var resize_text = [];       //used to save the coordinates of a text seelct box before resizing it
var prev_text_color;
var prev_bg_color;

$(document).ready(function () {

    design_history = new CanvasHistory();

    setBackgroundBarHeight();
    loadDesignerCanvas();
    loadToolTipCanvas();
    loadDesignerPreview();

    // on window resize, resize html canvas (not logical canvas)
    window.addEventListener('resize', function () {
        var container = document.getElementById('designer-online');
        if (container == null)
            return;
        var max_width = 0.9 * container.offsetWidth;
        var max_height = 0.6 * window.screen.height;
        var width, height;
        var proportion = document.getElementById('pproportion').value;
        width = max_width;
        height = width / proportion;
        if (height > max_height) {
            height = max_height;
            width = height * proportion;
        }
        RESOLUTION_FACTOR = (online_designer.width / width);
        online_designer.style.width = width + 'px';
        online_designer.style.height = height + 'px';
        for (var i = 0; i < txtArray.length; i++)
            txtArray[i].setFontSize(txtArray[i].fontSize);
        drawDesignerCanvas();
    }, true);

    if (document.getElementById('designer-container') == null)
        return;

    document.getElementById('designer-container').addEventListener('mousedown', function (evt) {
        if (!enable_canvas)
            return;
        var mousePos = getMousePos(evt);

        // first we search for a text that is selected / moved / resized by the mouse
        var foundTxt = mouseDownAction(mousePos.x, mousePos.y, txtArray);
        // then we search for an image that is selected / moved / resized by the mouse
        if (!foundTxt)
            mouseDownAction(mousePos.x, mousePos.y, imgArray);

        drawDesignerCanvas();
    }, false);

    /* this function iterates over the array (txtArray or imgArray) from top item to bottom item */
    /* to check if the user is selecting one item, and do the relevant action                    */
    function mouseDownAction(x, y, array) {
        var found = false;
        var i = array.length - 1;
        while (i >= 0 && !found) {
            if (array[i].mouseIn('item', x, y)) {
                if (array == txtArray) {    //txtArray
                    $('#txt_edit').val(array[i].text);
                    $('#edit-text').show(250);
                    array[i].select['enabled'] = true;
                }
                else    //imgArray
                    array[i].select = true;

                if (!array[i].drag) {
                    //save the previous position so when finished dragging we save it to history
                    if (array == txtArray) {    //txtArray
                        drag_text['x'] = array[i].select['x'];
                        drag_text['y'] = array[i].select['y'];
                    }
                    else {  //imgArray
                        drag_image['x'] = array[i].x;
                        drag_image['y'] = array[i].y;
                    }
                }
                array[i].drag = true;
                // if the text the user selected isn't on top, put it on top by moving it to the end of the array
                if (i < array.length - 1) {
                    if (array == txtArray)
                        array[array.length - 1].select['enabled'] = false;
                    else    // imgArray
                        array[array.length - 1].select = false;
                    var temp = array[i];
                    array.splice(i, 1);          //remove element from array
                    array.push(temp);           //push element to the end of array

                    if (array == imgArray) {
                        var tempImgLoaded = imgLoaded[i];
                        imgLoaded.splice(i, 1);         //remove element from imgLoaded
                        imgLoaded.push(tempImgLoaded);  //push element to the end of imgLoaded
                    }

                    //save changes to history
                    design_history.saveSuperposition((array == txtArray ? 'text' : 'image'), i);
                }
                found = true;
            }
            // if the mouse is outside the image and we are not resizing it, then disable select
            else if (!array[i].enableResize(x, y)) {
                if (array == txtArray) {    // txtArray
                    $('#edit-text').hide(250);
                    array[i].select['enabled'] = false;
                }
                else    // imgArray
                    array[i].select = false;
                array[i].drag = false;
            }
            // if select is enabled and the mouse is in one of the resize boxes, then disable drag and enable relevant resize flag (this action is done by enableResize)
            if (array == txtArray && array[i].select['enabled'] && array[i].enableResize(x, y)) {
                resize_text['x'] = array[i].select['x'];
                resize_text['y'] = array[i].select['y'];
                resize_text['w'] = array[i].select['width'];
                resize_text['h'] = array[i].select['height'];
                array[i].drag = false;
                found = true;
            }
            else if (array == imgArray && array[i].select && array[i].enableResize(x, y)) {
                resize_image['x'] = array[i].x;
                resize_image['y'] = array[i].y;
                resize_image['w'] = array[i].width;
                resize_image['h'] = array[i].height;
                array[i].drag = false;
                found = true;
            }
            i--;
            if (found && array == txtArray && imgArray.length > 0)
                imgArray[imgArray.length - 1].select = false;
        }
        return found;
    }

    document.body.addEventListener('mouseup', function (evt) {
        if (!enable_canvas)
            return;

        var mousePos = getMousePos(evt);
        if (mousePos.x < 0 || mousePos.x > online_designer.width || mousePos.y < 0 || mousePos.y > online_designer.height)
            $('html,body').css('cursor', 'default');

        if (!mouseUpAction(txtArray))
            mouseUpAction(imgArray);

        drawDesignerCanvas();
        drawPreviewCanvas();
    }, false);

    /* check if the top item in the array is selected and do relevant action */
    function mouseUpAction(array) {
        if (array.length > 0) {
            if ((array == txtArray && array[array.length - 1].select['enabled']) || (array == imgArray && array[array.length - 1].select)) {
                if (array[array.length - 1].drag) {
                    //save dragging to history
                    if (array == txtArray)
                        design_history.saveMove('text', drag_text['x'], drag_text['y'], txtArray[txtArray.length - 1].select['x'], txtArray[txtArray.length - 1].select['y']);
                    else
                        design_history.saveMove('image', drag_image['x'], drag_image['y'], imgArray[imgArray.length - 1].x, imgArray[imgArray.length - 1].y);
                    array[array.length - 1].drag = false;
                }

                if (array[array.length - 1].resizing()) {
                    array[array.length - 1].disableResize();
                    //save resizing to history
                    if (array == txtArray)
                        design_history.saveResize('text', resize_text['x'], resize_text['y'], resize_text['w'], resize_text['h'], txtArray[txtArray.length - 1].select['x'], txtArray[txtArray.length - 1].select['y'], txtArray[txtArray.length - 1].select['width'], txtArray[txtArray.length - 1].select['height']);
                    else
                        design_history.saveResize('image', resize_image['x'], resize_image['y'], resize_image['w'], resize_image['h'], imgArray[imgArray.length - 1].x, imgArray[imgArray.length - 1].y, imgArray[imgArray.length - 1].width, imgArray[imgArray.length - 1].height);
                }
                return true;
            }
        }
        return false;
    }

    online_designer.addEventListener('mousemove', function (evt) {
        if (!enable_canvas)
            return;

        var mousePos = getMousePos(evt);

        //the tooltip canvas is visible only when the mouse is in the area of the selected image or text (if there is one, it's the top one)
        if ((imgArray.length > 0 && imgArray[imgArray.length - 1].select && !imgArray[imgArray.length - 1].drag
            && !imgArray[imgArray.length - 1].resizing() && imgArray[imgArray.length - 1].mouseIn('item', mousePos.x, mousePos.y))
            || (txtArray.length > 0 && txtArray[txtArray.length - 1].select['enabled'] && !txtArray[txtArray.length - 1].drag
            && !txtArray[txtArray.length - 1].resizing() && txtArray[txtArray.length - 1].mouseIn('item', mousePos.x, mousePos.y))) {
            tooltip.style.left = (evt.screenX - 75) + "px";
            tooltip.style.top = (evt.screenY + 50) + "px";
        }
        else
            tooltip.style.left = "-200px";

        if (!mouseMoveAction(mousePos.x, mousePos.y, txtArray))
            mouseMoveAction(mousePos.x, mousePos.y, imgArray);

        //we don't show the preview when the mouse is moving, only when it's still
        drawDesignerCanvas();
    }, false);

    /* check the top item in array to see if we are resizing it or dragging it, change cursor look and call the relevant function */
    function mouseMoveAction(x, y, array) {
        if (array.length > 0) {
            if ((array == txtArray && array[array.length - 1].select['enabled']) || (array == imgArray && array[array.length - 1].select)) {
                //change cursor look
                if (array == imgArray && (array[array.length - 1].mouseIn('top_box', x, y) || array[array.length - 1].mouseIn('bottom_box', x, y)))
                    $('html,body').css('cursor', 'ns-resize');
                if (array[array.length - 1].mouseIn('right_box', x, y) || array[array.length - 1].mouseIn('left_box', x, y))
                    $('html,body').css('cursor', 'ew-resize');
                else if (array[array.length - 1].drag)
                    $('html,body').css('cursor', 'move');
                else if (!array[array.length - 1].resizing())
                    $('html,body').css('cursor', 'default');

                //call relevant function
                if (array[array.length - 1].resizing())
                    array == txtArray ? array[array.length - 1].resizeTextBox(x, y) : array[array.length - 1].resizeImage(x, y);
                else if (array[array.length - 1].drag)
                    array[array.length - 1].move(x, y);
                return true;
            }
        }
        return false;
    }

    // prevent the backspace key from navigating back.
    $(document).unbind('keydown').bind('keydown', function (event) {
        var doPrevent = false;
        if (event.keyCode === BACKSPACE) {
            var d = event.srcElement || event.target;
            if ((d.tagName.toUpperCase() === 'INPUT'
                && (d.type.toUpperCase() === 'TEXT' || d.type.toUpperCase() === 'NUMBER' || d.type.toUpperCase() === 'COLOR' || d.type.toUpperCase() === 'FILE'))
                || d.tagName.toUpperCase() === 'TEXTAREA') {
                doPrevent = d.readOnly || d.disabled;
            }
            else
                doPrevent = true;
        }
        if (doPrevent)
            event.preventDefault();
    });

    // add keyup listener to detecte use of 'delete' and delete the selected image
    document.body.addEventListener('keyup', function (evt) {
        if (!enable_canvas)
            return;

        var code = evt.which || evt.keyCode;
        var change = false;

        //if the key is delete or backspace, delete top item if selected (top item is top text or top image, text is always above image)
        if (code == DELETE || code == BACKSPACE) {
            if (code == DELETE && txtArray.length > 0 && txtArray[txtArray.length - 1].select['enabled']) {
                $('#edit-text').hide(250);                          //hide the edit text box
                design_history.saveTextDelete(txtArray[txtArray.length - 1].text, txtArray[txtArray.length - 1].fontFamily, txtArray[txtArray.length - 1].fontSize, txtArray[txtArray.length - 1].color);    //save delete text to history
                txtArray.splice(txtArray.length - 1, 1);            //remove element from txtArray
                change = true;
            }
            else if (imgArray.length > 0 && imgArray[imgArray.length - 1].select) {
                //save delete image to history
                design_history.saveImageDelete(imgArray[imgArray.length - 1].src);
                //remove image from arrays
                imgLoaded.splice(imgArray.length - 1, 1);         //remove element from imgLoaded
                imgArray.splice(imgArray.length - 1, 1);          //remove element from imgArray
                change = true;
            }
        }

        else if (code == ENTER) {
            if ($('#add-text').is(":visible")) {
                $('#add-text').hide(250);
                addText();
            }
            else if ($('#edit-text').is(":visible")) {
                editText();
            }
        }

        //left arrow => rotate selected item left, right arrow => rotate selected item right
        else if (code == LEFT_ARROW || code == RIGHT_ARROW) {
            change = KeyUpRotate(code, txtArray);
            if (!change)
                change = KeyUpRotate(code, imgArray);
        }

        if (change) {
            drawDesignerCanvas();
            drawPreviewCanvas();
        }
    }, false);

    function KeyUpRotate(code, array) {
        if (array.length > 0) {
            if ((array == txtArray && array[array.length - 1].select['enabled']) || (array == imgArray && array[array.length - 1].select)) {
                design_history.saveRotate((array == txtArray ? 'text' : 'image'), code == LEFT_ARROW ? 'left' : 'right');     //save rotation to history
                code == LEFT_ARROW ? array[array.length - 1].rotate -= ROTATION : array[array.length - 1].rotate += ROTATION;
                return true;
            }
        }
    }

});

//gets mouse position according to canvas coordinates
function getMousePos(evt) {
    var rect = online_designer.getBoundingClientRect();
    return {
        x: RESOLUTION_FACTOR*(evt.clientX - rect.left),
        y: RESOLUTION_FACTOR*(evt.clientY - rect.top)
    };
}

/***** INITIALIZATION FUNCTION *****/

function setBackgroundBarHeight() {
    var screen_height = window.screen.height;
    var bar_height = 0.06 * screen_height;
    var bar = document.getElementById('background-screen-bar');
    if (bar != null)
        bar.style.height = bar_height;
}

function loadDesignerCanvas() {
    online_designer = document.getElementById('designer-canvas');
    if (online_designer == null)
        return;
    od_context = online_designer.getContext('2d');

    //set canvas dimensions
    var container = document.getElementById('designer-online');
    var max_width = 0.9 * container.offsetWidth;
    var max_height = 0.6 * window.screen.height;
    var width, height;
    var proportion = document.getElementById('pproportion').value;
    width = max_width;
    height = width / proportion;
    if (height > max_height) {
        height = max_height;
        width = height * proportion;
    }

    online_designer.style.width = width + 'px';
    online_designer.style.height = height + 'px';
    online_designer.width = RESOLUTION_FACTOR *  width;
    online_designer.height = RESOLUTION_FACTOR *  height;

    drawDesignerCanvas();
    online_designer.style.boxShadow = "5px 5px 5px #999";

    enable_canvas = true;
}

function loadToolTipCanvas() {
    tooltip = document.getElementById('tooltip-canvas');
    if (tooltip == null)
        return;
    tt_context = tooltip.getContext('2d');
    tt_context.font = '11px Arial';
    var line_1 = "Use the left and right arrows";
    var line_2 = "to rotate the selected item";
    var line_3 = "or the delete keynote to";
    var line_4 = "remove the item";
    tt_context.fillText(line_1, 5, 15);
    tt_context.fillText(line_2, 5, 30);
    tt_context.fillText(line_3, 5, 45);
    tt_context.fillText(line_4, 5, 60);
}

function loadDesignerPreview() {
    preview = document.getElementById('preview-canvas');
    if (preview == null)
        return;
    preview_context = preview.getContext('2d');

    preview_product = new Image();
    preview_product.src = "" + document.getElementById('pimage').value;

    //onload, set canvas dimensions, draw product image, and set values of the design image
    preview_product.onload = function () {
        var image_width = this.width;
        var image_height = this.height;
        preview.height = image_height * preview.width / image_width;
        preview_context.clearRect(0, 0, preview.width, preview.height);
        preview_context.drawImage(preview_product, 0, 0, preview.width, preview.height);

        // set all values of the image that will appear on the product as a design preview
        if (document.getElementById('pwidth').value != 0) {
            preview_design['isMug'] = false;
            preview_design['x'] = document.getElementById('px').value * preview.width;
            preview_design['y'] = document.getElementById('py').value * preview.height;
            preview_design['width'] = document.getElementById('pwidth').value * preview.width;
            preview_design['height'] = preview_design['width'] * online_designer.height / online_designer.width;
        }
        else {
            preview_design['isMug'] = true;
        }
    };

    /*else {
        preview.height = 0;
        preview_design['isMug'] = true;
        /*preview_context.clearRect(0, 0, preview.width, preview.height);
        preview_design['isMug'] = true;
        preview_design['cylinder_height'] = 0.9 * preview.height;
        preview_design['cylinder-radius'] = preview_design['cylinder_height'] * document.getElementById('pproportion').value / (2 * Math.PI);
        var scene = new THREE.Scene();
        var geometry = new THREE.CylinderGeometry(preview_design['cylinder-radius'], preview_design['cylinder-radius'], preview_design['cylinder_height'], 50, 50, true);
        var material = new THREE.MeshBasicMaterial( {color: 0xffff00} );
        var cylinder = new THREE.Mesh( geometry, material );
        scene.add( cylinder );*/
              // this function is executed on each animation frame
              // revolutions per second
       /* var angularSpeed = 0.2; 
        var lastTime = 0;
        function animate(){
        // update
        var time = (new Date()).getTime();
        var timeDiff = time - lastTime;
        var angleChange = angularSpeed * timeDiff * 2 * Math.PI / 1000;
        cylinder.rotation.x += angleChange;
        lastTime = time;
 
        // render
        renderer.render(scene, camera);
 
        // request new frame
        requestAnimationFrame(function(){
            animate();
        });
        }
 
          // renderer
          var renderer = new THREE.WebGLRenderer({alpha:true});
          renderer.setClearColor( 0x000000, 0 ); // the default
          renderer.setSize(document.getElementById('designer-preview').offsetWidth, online_designer.height);
          document.getElementById('designer-preview').appendChild(renderer.domElement);
 
          // camera
          var camera = new THREE.PerspectiveCamera(45,  renderer.domElement.width / online_designer.height, 1, 1000);
          camera.position.z = 700;
 
          // scene
          var scene = new THREE.Scene();

          // material
          var imageObj = new Image();
    imageObj.src = online_designer.toDataURL();  // the design image
    var texture = THREE.ImageUtils.loadTexture( imageObj.src );
    texture.minFilter = THREE.NearestFilter;
      var material = new THREE.MeshLambertMaterial({
        map: texture
      });
                
          // cylinder
          // API: THREE.CylinderGeometry(bottomRadius, topRadius, height, segmentsRadius, segmentsHeight)
          preview_design['cylinder_height'] = 1.5*online_designer.height;
          preview_design['cylinder-radius'] = 1.5*(preview_design['cylinder_height'] * parseInt(document.getElementById('pproportion').value)) / (2 * Math.PI);
          var cylinder = new THREE.Mesh(new THREE.CylinderGeometry(preview_design['cylinder-radius'], preview_design['cylinder-radius'], preview_design['cylinder_height'], 50, 50, false), material);
          cylinder.overdraw = true;
          scene.add(cylinder);
 
          // start animation
          animate();
    }*/
}

/***** END OF INITIALIZATION FUNCTION *****/

/***** TOOLBAR FUNCTIONS ****/

/***** history *****/

function undo() {
    if (design_history.enableUndo()) {
        if (parseCommand(design_history.getUndoCmd().split(" ")))
            design_history.prevStep();
    }
}

function redo() {
    if (design_history.enableRedo()) {
        if (parseCommand(design_history.getRedoCmd().split(" ")))
            design_history.nextStep();
    }
}

/*
  background color 'color'
  background bg 'src'
  image add 'src'
  image delete
  image move x y
  image superposition i top
  image superposition top i
  image resize x y width height
  image rotate direction
  text add 'text' font size color
  text delete
  text edit 'text'
  text size 'size'
  text font 'font'
  text color 'color'
  text bold
  text italic
  text underline
  text center
  text left
  text right
  text move x y
  text superposition i top
  text superposition top i
  text resize x y width height
  text rotate direction
*/
function parseCommand(command) {
    
    if (command == null || command[0] == null)
        return false;

    if (command[0] == 'background') {

        if (command[1] == null || command[2] == null || (command[1] != 'color' && command[1] != 'bg'))
                return false;
        background = command[1];                        //background = 'color' or 'bg'        
        if (command[1] == 'color')      // background color 'color'  
            od_color = command[2];                      //change background's color
        else                            // background bg 'src'                     
            setDesignerBackground(command[2], false);   //false = don't save this background change to history, just do the actual change
        
    }

    else if (command[0] == 'image') {
        
        if (command[1] != 'add' && imgArray.length == 0)
            return false;

        switch (command[1]) {

            case null:
            case '': return false;

            case 'add': // image add 'src'
                if (command[2] == null)
                    return false;
                loadImg(command[2]);
                return true;    // not break because in loadImage the drawing functions for the canvas and the preview are already called

            case 'delete':  // image delete
                imgLoaded.splice(imgArray.length - 1, 1);         //remove last element from imgLoaded
                imgArray.splice(imgArray.length - 1, 1);          //remove last element from imgArray
                break;

            case 'move':    // image move x y             
                if (command[2] == null || command[3] == null)
                    return false;
                imgArray[imgArray.length - 1].x = parseFloat(command[2]);       //set x of the top image
                imgArray[imgArray.length - 1].y = parseFloat(command[3]);       //set y of the top image
                break;

            case 'superposition': 
                if (command[2] == null || command[3] == null || imgArray.length < 2)
                    return false;
                imgArray[imgArray.length - 1].select = false;

                if (command[2] == 'top') {  // image superposition top i
                    // pop top and put it in index i in both images arrays
                    var i = parseInt(command[3]);
                    var tmp = imgArray.pop();
                    tmp.select = false;
                    imgArray.splice(i, 0, tmp);
                    tmp = imgLoaded.pop();
                    imgLoaded.splice(i, 0, tmp);
                }

                else {  // image superposition i top
                    // put image in index i on top
                    var i = parseInt(command[2]);
                    var tempImg = imgArray[i];
                    var tempImgLoaded = imgLoaded[i];
                    imgArray.splice(i, 1);              //remove element i from imgArray
                    imgLoaded.splice(i, 1);             //remove element i from imgLoaded
                    imgArray.push(tempImg);             //push element to the end of imgArray
                    imgLoaded.push(tempImgLoaded);      //push element to the end of imgLoaded
                }

                break;

            case 'resize':  // image resize x y width height
                if (command[2] == null || command[3] == null || command[4] == null || command[5] == null)
                    return false;
                //set new x, y, width and height for top image that was resized
                imgArray[imgArray.length - 1].x = parseFloat(command[2]);
                imgArray[imgArray.length - 1].y = parseFloat(command[3]);
                imgArray[imgArray.length - 1].width = parseFloat(command[4]);
                imgArray[imgArray.length - 1].height = parseFloat(command[5]);
                break;
            
            case 'rotate':  //image rotate direction
                if (command[2] == null)
                    return false;
                imgArray[imgArray.length - 1].rotate += (command[2] == 'left' ? (-ROTATION) : ROTATION);
                break;

            default: return false;
        }
    }

    else if (command[0] == 'text') {

        if (command[1] != 'add' && txtArray.length == 0)
            return false;
        
        $('#edit-text').hide(250); 

        switch (command[1]) {

            case null:
            case '': return false;

            case 'add': // text add size color
                if (command[2] == null || command[3] == null || command[4] == null || command[5] == null)
                    return false;
                var txtItem = new TextItem(decodeURI(command[2]), decodeURI(command[3]), parseInt(command[4]), command[5]);
                //if there is already texts and/or images on the canvas, disable select flag from the top text and top image
                if (txtArray.length > 0)
                    txtArray[txtArray.length - 1].select['enabled'] = false;
                if (imgArray.length > 0)
                    imgArray[imgArray.length - 1].select = false;
                txtArray.push(txtItem);
                break;

            case 'delete':  // text delete
                txtArray.splice(txtArray.length - 1, 1);          //remove last element from imgArray
                break;

            case 'edit':    // text edit 'text'
                if (command[2] == null)
                    return false;
                txtArray[txtArray.length - 1].editText(decodeURI(command[2]));
                //txtArray[txtArray.length - 1].setSelectValues();
                break;

            case 'font':    // text font 'font'
                if (command[2] == null)
                    return false;
                txtArray[txtArray.length - 1].setFont(decodeURI(command[2]));
                break;

            case 'size':    // text size 'size'
                if (command[2] == null)
                    return false;
                txtArray[txtArray.length - 1].setFontSize(parseInt(command[2]));
                break;

            case 'color':   // text color 'color'
                if (command[2] == null)
                    return false;
                txtArray[txtArray.length - 1].color = command[2];
                break;

            case 'bold':        // text bold
            case 'italic':      // text italic
            case 'underline':   //text underline
                txtArray[txtArray.length - 1].changeEmphasis(command[1]);
                break;

            case 'center':      // text center
            case 'left':        // text left
            case 'right':       // text right
                txtArray[txtArray.length - 1].alignText(command[1]);
                break;

            case 'move':    // text move x y             
                if (command[2] == null || command[3] == null)
                    return false;
                txtArray[txtArray.length - 1].select['x'] = parseFloat(command[2]);       //set x of the top text select box
                txtArray[txtArray.length - 1].select['y'] = parseFloat(command[3]);       //set y of the top text select box
                txtArray[txtArray.length - 1].setXY();
                break;

            case 'superposition': 
                if (command[2] == null || command[3] == null || txtArray.length < 2)
                    return false;
                txtArray[txtArray.length - 1].select['enabled'] = false;

                if (command[2] == 'top') {  // text superposition top i
                    // pop top and put it in index i in text array
                    var i = parseInt(command[3]);
                    var tmp = txtArray.pop();
                    tmp.select['enabled'] = false;
                    txtArray.splice(i, 0, tmp);
                }

                else {  // text superposition i top
                    // put text in index i on top
                    var i = parseInt(command[2]);
                    var tmp = txtArray[i];
                    txtArray.splice(i, 1);          //remove element i from txtArray
                    txtArray.push(tmp);             //push element to the end of txtArray
                }

                break;

            case 'resize':  // text resize x y width height
                if (command[2] == null || command[3] == null || command[4] == null || command[5] == null)
                    return false;
                //set new x, y, width and height for top text that was resized
                txtArray[txtArray.length - 1].select['x'] = parseFloat(command[2]);
                txtArray[txtArray.length - 1].select['y'] = parseFloat(command[3]);
                txtArray[txtArray.length - 1].select['width'] = parseFloat(command[4]);
                txtArray[txtArray.length - 1].select['height'] = parseFloat(command[5]);
                txtArray[txtArray.length - 1].setLines();
                txtArray[txtArray.length - 1].setXY();
                break;
            
            case 'rotate':  //text rotate direction
                if (command[2] == null)
                    return false;
                txtArray[txtArray.length - 1].rotate += (command[2] == 'left' ? (-ROTATION) : ROTATION);
                break;

            default: return false;
        }

    }

    drawDesignerCanvas();
    drawPreviewCanvas();

    return true;

}

/***** end of history *****/

function chooseTxtColor() {
    if (txtArray.length > 0)
        prev_text_color = txtArray[txtArray.length - 1].color;
    $('#txt_color').click();
}

function uploadImage() {
    $('#image_file').click();
}

function chooseBgColor() {
    prev_bg_color = od_color;
    $('#bg_color').click();
}

function showHideTextForm() {
    $('#edit-text').hide(250);
    $('#add-text').toggle(250);
    if (imgArray.length > 0 && imgArray[imgArray.length - 1].select) {
        imgArray[imgArray.length - 1].select = false;
        drawDesignerCanvas();
        drawPreviewCanvas();
    }
    if (txtArray.length > 0 && txtArray[txtArray.length - 1].select['enabled']) {
        txtArray[txtArray.length - 1].select['enabled'] = false;
        drawDesignerCanvas();
        drawPreviewCanvas();
    }
}

function inputTextFocus(txt){
    if(txt.value==txt.defaultValue){ txt.value=""; txt.style.color="black"; }
}
function inputTextBlur(txt){
    if(txt.value==""){ txt.value="Write your text here"; txt.style.color="grey"; }
}

function addText() {
    var txt_input = document.getElementById('txt_input').value;
    var e = document.getElementById("font-family");
    var ft_family = e.options[e.selectedIndex].value;;
    var ft_size = document.getElementById('font-size').value;
    var txt_color = document.getElementById('txt_color').value;
    if (txt_input == null || txt_input.trim() == "")
        alert("Please write text");
    else if (ft_size == null || ft_size.trim() == "" || isNaN(ft_size.trim()))
        alert("Please enter a valid font size");
    else if (!isColor(txt_color))
        alert("Please enter a valid color");
    else {
        $('#add-text').toggle();
        design_history.saveTextAdd(txt_input, ft_family, parseInt(ft_size), txt_color);    //save to history
        var txtItem = new TextItem(txt_input, ft_family, parseInt(ft_size), txt_color);

        //if there is already texts and/or images on the canvas, disable select flag from the top text and top image
        if (txtArray.length > 0)
            txtArray[txtArray.length - 1].select['enabled'] = false;
        if (imgArray.length > 0)
            imgArray[imgArray.length - 1].select = false;
        txtArray.push(txtItem);
        drawDesignerCanvas();
        drawPreviewCanvas();
    }
    //reinitialize instructions in the text inbox
    var txt = document.getElementById('txt_input');
    txt.value="Write your text here";
    txt.style.color="grey";
}

function editText() {
    var txt_input = document.getElementById('txt_edit').value;
    if (txtArray.length > 0 && txtArray[txtArray.length - 1].select['enabled']) {
        design_history.saveTextFeature('edit', txtArray[txtArray.length - 1].text, txt_input);
        txtArray[txtArray.length - 1].editText(txt_input);
        drawDesignerCanvas();
        drawPreviewCanvas();
    }
}

function setFont(font_input) {
    if (txtArray.length > 0 && txtArray[txtArray.length - 1].select['enabled']){
        design_history.saveTextFeature('font', txtArray[txtArray.length - 1].fontFamily, font_input.value);   //save to history
        txtArray[txtArray.length - 1].setFont(font_input.value);
        drawDesignerCanvas();
        drawPreviewCanvas();
    }
}

function setSize(size_input) {
    var new_size = size_input.value;
    if (new_size == null || new_size.trim() == "" || isNaN(new_size.trim()))
        alert("Font size must be a number");
    else if (txtArray.length > 0 && txtArray[txtArray.length - 1].select['enabled']){
        design_history.saveTextFeature('size', txtArray[txtArray.length - 1].fontSize, parseInt(new_size));
        txtArray[txtArray.length - 1].setFontSize(parseInt(new_size));
        drawDesignerCanvas();
        drawPreviewCanvas();
    }
}

function showTxtColorPreview(txtColor) {
    //alert('' + txtColor.value);
    if (txtArray.length > 0 && txtArray[txtArray.length - 1].select['enabled']) {
        var prev_color = txtArray[txtArray.length - 1].color;
        if (setColorText(txtColor, false))
            txtArray[txtArray.length - 1].color = prev_color;
        return;
    }
}

function setColorText(txtColor, saveInHistory) {
    // we will first validate the color input because not all browsers support input color yet
    if (!isColor(txtColor.value)) {
        alert("Unvalid color, please write in hexadecimal format: #000000 -> #ffffff");
        return false;
    }
    if (txtArray.length > 0 && txtArray[txtArray.length - 1].select['enabled']) {
        //if (saveInHistory)
        //    alert('hello ' + txtColor.value);
        if (saveInHistory && prev_text_color != txtColor.value.trim())
            design_history.saveTextFeature('color', prev_text_color, txtColor.value.trim());  //save to history
        txtArray[txtArray.length - 1].color = txtColor.value.trim();
        drawDesignerCanvas();
        drawPreviewCanvas();
        return;
    }
    return true;
}

function emphasize(emphasis) {
    if (txtArray.length > 0 && txtArray[txtArray.length - 1].select['enabled']) {
        design_history.saveTextEmphasis(emphasis);     //save emphasis to history
        txtArray[txtArray.length - 1].changeEmphasis(emphasis);
        drawDesignerCanvas();
        drawPreviewCanvas();
        return;
    }
}

function alignText(position) {
    if (txtArray.length > 0 && txtArray[txtArray.length - 1].select['enabled']) {
        design_history.saveTextFeature('align', txtArray[txtArray.length - 1].textAlign, position);
        txtArray[txtArray.length - 1].alignText(position);
        drawDesignerCanvas();
        drawPreviewCanvas();
        return;
    }
}

function readImg(input) {
    if (input.files && input.files[0]) {

        var reader = new FileReader();

        reader.onload = function (e) {
            design_history.saveImageAdd(e.target.result);
            return loadImg(e.target.result);
        };

        reader.readAsDataURL(input.files[0]);
    }  
}

function loadImg (src) {

    var imageObj = new Image();
    imageObj.src = src;
    var imageItem;

    imageObj.onload = function () {
        imageItem = new ImageItem(this.width, this.height);
        imageItem.src = imageObj.src;
        //if there is already texts and/or images on the canvas, disable select flag from the top text and top image
        if (txtArray.length > 0)
            txtArray[txtArray.length - 1].select['enabled'] = false;
        if (imgArray.length > 0)
            imgArray[imgArray.length - 1].select = false;
        imgArray.push(imageItem);
        imgLoaded[imgArray.length - 1] = new Image();
        imgLoaded[imgArray.length - 1].onload = function () {
            drawDesignerCanvas();
            drawPreviewCanvas();
        };
        imgLoaded[imgArray.length - 1].onerror = function () { alert("image load failed"); }
        imgLoaded[imgArray.length - 1].src = imgArray[imgArray.length - 1].src;
        return true;
    };

    imageObj.onerror = function () {
        return false;
    }

    return true;

}

function setDesignerBackground(bgRadio, saveInHistory) {

    if (bgRadio == 'transparent') {
        design_history.saveColor(background, background == 'color'? od_color : od_bg['image'].src, 'transparent');
        od_color = 'transparent';
        background = 'color';
        drawDesignerCanvas();
        drawPreviewCanvas();
        return;
    }

    if (saveInHistory)
        design_history.saveBackground(background, background == 'color'? od_color : od_bg['image'].src, bgRadio);

    od_bg['image'] = new Image();
    od_bg['image'].onload = function () {
        var image_width = this.width;
        var image_height = this.height;
        var od_proportion = online_designer.width / online_designer.height;
        var image_proportion = image_width / image_height;
        if (od_proportion > image_proportion) {
            od_bg['src_width'] = image_width;
            od_bg['src_height'] = od_bg['src_width'] / od_proportion;
        }
        else {
            od_bg['src_height'] = image_height;
            od_bg['src_width'] = od_bg['src_height'] * od_proportion;
        }
        background = 'bg';
        drawDesignerCanvas();
        drawPreviewCanvas();
    }
    od_bg['image'].src = "" + bgRadio;
}

function showColorPreview(bgColor) {
    var prev_color = od_color;
    var prev_background = background;

    //if setColorBackground returned true, that means background and od_color changed, so change it back
    if (setColorBackground(bgColor, false)) {
        od_color = prev_color;
        background = prev_background;
    }
}

function setColorBackground(bgColor, saveInHistory) {
    // we will first validate the color input because not all browsers support input color yet
    if (!isColor(bgColor.value)) {
        alert("Unvalid color, please write in hexadecimal format: #000000 -> #ffffff");
        return false;
    }

    if (saveInHistory && (background != 'color' || prev_bg_color != bgColor.value))
        design_history.saveColor(background, background == 'color'? prev_bg_color: od_bg['image'].src, bgColor.value);

    //if we got here, then the input was a color, we can set the background color
    od_color = bgColor.value;
    background = 'color';
    drawDesignerCanvas();
    drawPreviewCanvas();

    return true;
}

function isColor(input) {
    var color_input = "" + input;
    if (color_input.length != 7 || !color_input.startsWith('#'))
        return false;
    for (var i = 1; i < color_input.length; i++) {
        if (!isHexaDigit(color_input.charAt(i)))
            return false;
    }
    return true;
}

function isHexaDigit(char) {
    if (char >= '0' && char <= '9')
        return true;
    if (char >= 'a' && char <= 'f')
        return true;
    if (char >= 'A' && char <= 'F')
        return true;
    return false;
}

/***** TOOLBAR FUNCTIONS ****/

/***** DRAW FUNCTIONS *****/

function drawDesignerCanvas() {

    od_context.clearRect(0, 0, online_designer.width, online_designer.height);
 
    // if current background is color, fill canvas with the choosen color
    if (background == 'color') {
        od_context.fillStyle = od_color;
        od_context.fillRect(0, 0, online_designer.width, online_designer.height);      
    }
 
    //else crop the choosen background to fit the canvas
    else
        od_context.drawImage(od_bg['image'], 0, 0, od_bg['src_width'], od_bg['src_height'], 0, 0, online_designer.width, online_designer.height);

    //... and finally draw the images and the texts
    drawImages();
    drawTexts();
}

function drawImages() {
    //loop on the images array, draw each image(already loaded in array imgLoaded) on its location with its dimensions (held in array imhArray)
    for (var i = 0; i < imgArray.length; i++)
        imgArray[i].drawImageItem(imgLoaded[i]);
}

function drawTexts() {
    for (var i = 0; i < txtArray.length; i++)
        txtArray[i].drawTextItem();
}

function drawPreviewCanvas() {

    $('#canvas_url').val(online_designer.toDataURL("image/jpeg", 1.0).replace(/^data:image\/(png|jpeg);base64,/, ""));

    var imageObj = new Image();
    imageObj.src = online_designer.toDataURL();  // the design image
    //onload, clear, draw product, and then draw design on top
    imageObj.onload = function () {
        if (!preview_design['isMug']) {
            preview_context.clearRect(0, 0, preview.width, preview.height);
            preview_context.drawImage(preview_product, 0, 0, preview.width, preview.height);
            preview_context.drawImage(imageObj, preview_design['x'], preview_design['y'], preview_design['width'], preview_design['height']);
        }
        else {
            preview_context.clearRect(0, 0, preview.width, preview.height);
            preview_context.drawImage(preview_product, 0, 0, preview.width, preview.height);
        }
    };
}

function drawPreviousDesign(url) {
    var imageObj = new Image();
    imageObj.src = "" + url;
    imageObj.onload = function () {
        od_context.drawImage(imageObj, 0, 0, online_designer.width, online_designer.height);
        preview_context.clearRect(0, 0, preview.width, preview.height);
        preview_context.drawImage(preview_product, 0, 0, preview.width, preview.height);
        if (!preview_design['isMug'])
            preview_context.drawImage(imageObj, preview_design['x'], preview_design['y'], preview_design['width'], preview_design['height']);
    };
}

/***** END OF DRAW FUNCTIONS *****/

function showMatchingCanvas(url) {
    if (url == '') {
        $('#designer-toolbar').show(250);
        drawDesignerCanvas();
        drawPreviewCanvas();
        enable_canvas = true;
    }
    else {
        $('#edit-text').hide(250);
        $('#designer-toolbar').hide(250);
        drawPreviousDesign(url);
        enable_canvas = false;
    }
}