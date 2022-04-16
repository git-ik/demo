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
    formData.append('description', true);

    let request = new XMLHttpRequest();
    request.open('POST', 'index.php');
    request.setRequestHeader('accept', 'application/json');
    request.addEventListener("readystatechange", () => {
        if (request.readyState === 4 && request.status === 200) {
            setTimeout(function () {
                container.innerHTML = request.responseText;
                container.style.opacity = '1';
            }, 500);
        }
    });
    request.send(formData);
}

