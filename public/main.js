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

var canvas;
var ctx;
var xOffsetLeft = 0;
var xOffsetRight = 0;
var xOffset = 0;
var yOffset = 45;
var toggleMovingDirection = 'right';

function drawExample() {
    setTimeout(function () {
        if (xOffset > xOffsetRight) {
            toggleMovingDirection = 'left';
        }
        if (xOffset < xOffsetLeft) {
            toggleMovingDirection = 'right';
        }
        if (toggleMovingDirection == 'right') {
            xOffset = xOffset + 15;
        } else {
            xOffset = xOffset - 15;
        }

        ctx.clearRect(0, 0, window.innerWidth - 60, 60);
        ctx.fillRect(xOffset, yOffset, 10, 10);
        ctx.fillRect(xOffset + 15, yOffset, 10, 10);
        ctx.fillRect(xOffset + 30, yOffset, 10, 10);

        drawExample();
    }, 50);
}

function defineDrawParams() {
    canvas = document.getElementById('canvas');
    canvas.width = window.innerWidth - 60;
    canvas.height = 60;
    ctx = canvas.getContext('2d');
    let hElements = document.getElementsByTagName('h1');
    let pos = hElements[0].getBoundingClientRect();
    xOffset = pos.x - 60;
    xOffsetLeft = pos.x - 60;
    xOffsetRight = pos.x + hElements[0].offsetWidth;
}

document.addEventListener('DOMContentLoaded', function () {
    defineDrawParams();
    drawExample();

    window.addEventListener('resize', () => {
        defineDrawParams();
    });
});