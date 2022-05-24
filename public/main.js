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

var aopacity = [];
var aopDirection = [];
function animateOpacity(el, min, max) {
    if (aopacity[el.id] >= max) {
        aopDirection[el.id] = true;
    }
    if (aopacity[el.id] <= min) {
        aopDirection[el.id] = false;
    }
    if (aopDirection[el.id] == true) {
        aopacity[el.id] = aopacity[el.id] - 0.01;
    } else {
        aopacity[el.id] = aopacity[el.id] + 0.01;
    }
    el.style.opacity = aopacity[el.id];
}

/**
 * Load object description
 * [/index page]
 * 
 * @param {int} id
 */
function ajaxLoadDescription(id) {
    var switcher = document.getElementById('objectsAccess');
    var container = document.getElementById('objectDescription');

    if (switcher && !switcher.checked) {
        container.innerHTML = '<span class="error">[информация об объекте не загружена]:</span><br>Возможность получить информацию об объектах отключена пользователем. Необходимо разрешить получение информации об объектах.';
        return;
    }

    container.innerHTML = '<center>... [LOADING INFO] ...</canter>';
    container.style.opacity = '0.5';

    let formData = new FormData();
    formData.append('id', id);

    let request = new XMLHttpRequest();
    request.open('POST', '/api/get-data');
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

var animate = 'off';

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

        ctx[cid].fillStyle = '#444444';
        for (i = 0; i <= rCount; i++) {
            ctx[cid].fillRect(i * 15 + 10, 0, 1, 10);
            ctx[cid].fillRect(i * 15 + 10, 15, 1, 10);
            ctx[cid].fillRect(i * 15 + 10, 30, 1, 10);
            ctx[cid].fillRect(i * 15 + 10, 45, 1, 10);
            ctx[cid].fillRect(i * 15 + 10, 60, 1, 10);
            ctx[cid].fillRect(i * 15 + 10, 75, 1, 10);
        }

        for (i = 0; i <= rCount; i++) {
            ctx[cid].fillRect(i * 15, 10, 10, 1);
            ctx[cid].fillRect(i * 15, 25, 10, 1);
            ctx[cid].fillRect(i * 15, 40, 10, 1);
            ctx[cid].fillRect(i * 15, 55, 10, 1);
            ctx[cid].fillRect(i * 15, 70, 10, 1);
            ctx[cid].fillRect(i * 15, 85, 10, 1);
        }

        if (animate == 'on') {
            let randY = Math.round(Math.random() * 6);
            let randC = Math.round(Math.random() * 10);
            if (randC == 1) {
                ctx[cid].fillStyle = '#2A6D1D';
            } else if (randC == 2) {
                ctx[cid].fillStyle = '#A35400';
            } else {
                ctx[cid].fillStyle = '#64FF4B';
            }
            if (randY > 4) {
                ctx[cid].fillRect(xOffset[cid], 0, 9, 9);
                ctx[cid].fillStyle = '#64FF4B';
            }
            if (randY == 5) {
                ctx[cid].fillRect(xOffset[cid], 15, 9, 9);
                ctx[cid].fillStyle = '#64FF4B';
            }
            if (randY < 2) {
                ctx[cid].fillRect(xOffset[cid], 30, 9, 9);
                ctx[cid].fillStyle = '#64FF4B';
            }
            if (randY == 3) {
                ctx[cid].fillRect(xOffset[cid], 45, 9, 9);
                ctx[cid].fillStyle = '#64FF4B';
            }
            if (randY == 0) {
                ctx[cid].fillRect(xOffset[cid], 60, 9, 9);
                ctx[cid].fillStyle = '#64FF4B';
            }
            if (randY == 6) {
                ctx[cid].fillRect(xOffset[cid], 75, 9, 9);
                ctx[cid].fillStyle = '#64FF4B';
            }
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
}

document.addEventListener('DOMContentLoaded', function () {
    start('canvas1', false);
    setTimeout(function () {
        start('canvas2', false);
    }, Math.round(Math.random() * 2000));
    setTimeout(function () {
        start('canvas3', false);
    }, Math.round(Math.random() * (4000 - 2000) + 2000));
    setTimeout(function () {
        start('canvas4', false);
    }, Math.round(Math.random() * (6000 - 3000) + 3000));

    let t1 = document.getElementById('t1');
    let t2 = document.getElementById('t2');
    let t3 = document.getElementById('t3');
    aopacity['t1'] = parseFloat(window.getComputedStyle(t1).getPropertyValue("opacity"));
    aopacity['t2'] = parseFloat(window.getComputedStyle(t2).getPropertyValue("opacity"));
    aopacity['t3'] = parseFloat(window.getComputedStyle(t3).getPropertyValue("opacity"));
    setInterval(function () {
        if (animate == 'off') {
            return;
        }
        animateOpacity(t1, 0.2, 0.7);
        animateOpacity(t2, 0.05, 0.6);
        animateOpacity(t3, 0, 0.4);
    }, 100);

    //play error sound (when wrong authorization data)
    let authForm = document.getElementById("authorization");
    if (authForm && authForm.querySelector(".error")) {
        let player = document.getElementById("player");
        let source = document.getElementById("source");
        source.setAttribute('src', 'public/error.mp3');
        player.volume = 0.1;
        player.load();
        player.play();
    }
    
});

window.addEventListener('resize', function () {
    clearTimeout(timeLapse);
    timeLapse = setTimeout(function () {
        start('canvas1', true);
        start('canvas2', true);
        start('canvas3', true);
        start('canvas4', true);
    }, 50);
});

var timeLapseBG;
function switchAnimation(el) {
    let player = document.getElementById("player");
    player.volume = 0.6;
    if (el.checked) {
        let bg = document.getElementById('bg');
        aopacity['bg'] = parseFloat(window.getComputedStyle(bg).getPropertyValue("opacity"));
        timeLapseBG = setInterval(function () {
            animateOpacity(bg, 0, 1);
            if (window.getComputedStyle(bg).getPropertyValue("opacity") == 0.2) {
                player.play();
                animate = 'on';
            }
            if (window.getComputedStyle(bg).getPropertyValue("opacity") == 1) {
                clearInterval(timeLapseBG);
            }
        }, 10);
    } else {
        player.pause();
        animate = 'off';
    }
}