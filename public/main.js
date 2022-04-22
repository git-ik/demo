/**
 * Toggle tree elements in objects tree
 * [/index page]
 * 
 * @param {int} id
 * @param {object} element
 */
function toggleTreeElement(id, element) {
    let childTreeContainer = document.getElementById('invisible' + id);
    if (childTreeContainer.classList.contains('invisible')) {
        element.innerHTML = '[-]';
        childTreeContainer.classList.remove('invisible');
    } else {
        element.innerHTML = '[+]';
        childTreeContainer.classList.add('invisible');
    }
}

/**
 * Load object description
 * [/index page]
 * 
 * @param {int} id
 */
function ajaxLoadDescription(id) {
    var container = document.getElementById('objectDescription');
    container.innerHTML = '<center>... [LOADING INFO] ...</canter>';
    container.style.opacity = '0.5';

    let formData = new FormData();
    formData.append('id', id);

    let request = new XMLHttpRequest();
    request.open('POST', '/api/api.php');
    request.setRequestHeader('accept', 'application/json');
    request.addEventListener("readystatechange", () => {
        if (request.readyState === 4 && request.status === 200) {
            let responseData = JSON.parse(request.responseText);
            setTimeout(function () {
                container.innerHTML = responseData.description;
                container.style.opacity = '1';
            }, 500);
        }
    });
    request.send(formData);
}


/**
 * Render canvas
 */
var canvas;
var cWidth;
var cHeight;
var ctx;
var xOffsetLeft;
var xOffsetRight;
var xOffset;
var rCount;
var movingDirection;
var isStarted;
var timeLapse;
function draw(ctx) {
    setTimeout(function () {
        if (xOffset >= xOffsetRight) {
            movingDirection = 'left';
        }
        if (xOffset <= xOffsetLeft) {
            movingDirection = 'right';
        }
        if (movingDirection == 'right') {
            xOffset = xOffset + 15;
        } else {
            xOffset = xOffset - 15;
        }

        ctx.clearRect(0, 0, cWidth, cHeight);

        ctx.fillStyle = '#000000';
        for (i = 0; i <= rCount; i++) {
            ctx.fillRect(i * 15, 0, 10, 10);
            ctx.fillRect(i * 15, 15, 10, 10);
            ctx.fillRect(i * 15, 30, 10, 10);
            ctx.fillRect(i * 15, 45, 10, 10);
            ctx.fillRect(i * 15, 60, 10, 10);
            ctx.fillRect(i * 15, 75, 10, 10);
        }

        ctx.fillStyle = '#64FF4B';
        let randY = Math.round(Math.random() * (6 - 0) + 0);
        if (randY > 4) {
            ctx.fillRect(xOffset, 0, 10, 10);
        }
        if (randY == 5) {
            ctx.fillRect(xOffset, 15, 10, 10);
        }
        if (randY < 2) {
            ctx.fillRect(xOffset, 30, 10, 10);
        }
        if (randY == 3) {
            ctx.fillRect(xOffset, 45, 10, 10);
        }
        if (randY == 0) {
            ctx.fillRect(xOffset, 60, 10, 10);
        }
        if (randY == 6) {
            ctx.fillRect(xOffset, 75, 10, 10);
        }

        draw(ctx);
    }, 50);
}

function start(cEl, isStarted) {
    if (!isStarted) {
        canvas = document.getElementById(cEl);
    }

    if (!isStarted && canvas == null) {
        return;
    }

    cWidth = window.innerWidth - 110;
    cHeight = 85;
    canvas.width = cWidth;
    canvas.height = cHeight;
    xOffset = 0;
    xOffsetLeft = 0;
    rCount = Math.floor(cWidth / 15);
    xOffsetRight = rCount * 15;

    if (canvas !== undefined) {
        if (!isStarted) {
            movingDirection = 'right';
            ctx = canvas.getContext('2d');
            draw(ctx);
            isStarted = true;
        }
        window.addEventListener('resize', function () {
            clearTimeout(timeLapse);
            timeLapse = setTimeout(function () {
                start('canvas1', true);
                start('canvas2', true);
                start('canvas3', true);
            }, 50);
        });
    }
}

document.addEventListener('DOMContentLoaded', function () {
    start('canvas1', false);
    start('canvas2', false);
    start('canvas3', false);
});