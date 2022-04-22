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
var canvas = [];
var cWidth;
var cHeight;
var ctx = [];
var xOffsetLeft;
var xOffsetRight;
var xOffset = [];
var rCount;
var movingDirection = [];
var timeLapse;
function draw(cid) {
    setTimeout(function () {
        if (xOffset[cid] >= xOffsetRight) {
            movingDirection[cid] = 'left';
        }
        if (xOffset[cid] <= xOffsetLeft) {
            movingDirection[cid] = 'right';
        }
        if (movingDirection[cid] == 'right') {
            xOffset[cid] = xOffset[cid] + 15;
        } else {
            xOffset[cid] = xOffset[cid] - 15;
        }

        ctx[cid].clearRect(0, 0, cWidth, cHeight);

        ctx[cid].fillStyle = '#000000';
        for (i = 0; i <= rCount; i++) {
            ctx[cid].fillRect(i * 15, 0, 10, 10);
            ctx[cid].fillRect(i * 15, 15, 10, 10);
            ctx[cid].fillRect(i * 15, 30, 10, 10);
            ctx[cid].fillRect(i * 15, 45, 10, 10);
            ctx[cid].fillRect(i * 15, 60, 10, 10);
            ctx[cid].fillRect(i * 15, 75, 10, 10);
        }

        ctx[cid].fillStyle = '#64FF4B';
        let randY = Math.round(Math.random() * 6);
        if (randY > 4) {
            ctx[cid].fillRect(xOffset[cid], 0, 10, 10);
        }
        if (randY == 5) {
            ctx[cid].fillRect(xOffset[cid], 15, 10, 10);
        }
        if (randY < 2) {
            ctx[cid].fillRect(xOffset[cid], 30, 10, 10);
        }
        if (randY == 3) {
            ctx[cid].fillRect(xOffset[cid], 45, 10, 10);
        }
        if (randY == 0) {
            ctx[cid].fillRect(xOffset[cid], 60, 10, 10);
        }
        if (randY == 6) {
            ctx[cid].fillRect(xOffset[cid], 75, 10, 10);
        }

        draw(cid);
    }, 50);
}

function start(cid, isStarted) {
    if (!isStarted) {
        canvas[cid] = document.getElementById(cid);
    }

    if (canvas[cid] == null) {
        return;
    }

    cWidth = window.innerWidth - 110;
    cHeight = 85;
    canvas[cid].width = cWidth;
    canvas[cid].height = cHeight;
    xOffset[cid] = 0;
    xOffsetLeft = 0;
    rCount = Math.floor(cWidth / 15);
    xOffsetRight = rCount * 15;

    if (!isStarted) {
        movingDirection[cid] = 'right';
        ctx[cid] = canvas[cid].getContext('2d');
        draw(cid);
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

document.addEventListener('DOMContentLoaded', function () {
    start('canvas1', false);
    setTimeout(function () {
        start('canvas2', false);
    }, Math.round(Math.random() * 2000));
    setTimeout(function () {
        start('canvas3', false);
    }, Math.round(Math.random() * (4000 - 2000) + 2000));
});