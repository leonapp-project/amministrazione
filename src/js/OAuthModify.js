const permission_descriptions = {
    "administration": {
        "studenti": {
            "getList": "Ottieni la lista degli studenti",
            "getTicketsBought": "Ottieni la lista dei blocchi acquistati da uno studente (e quando)",
            "getAllBlocksBought": "Ottieni la lista dei blocchi acquistati da tutti gli studenti",
            "getInfo": "Ottieni le informazioni di uno studente",
            "enableBuyTickets": "Permetti ad uno studente di acquistare un biglietto",
            "updateInfo": "Aggiorna le informazioni di uno studente",
            "updateWhitelist": "Aggiorna la whitelist degli studenti",
        },
        "OAuth": {
            "getKeyList": "Ottieni la lista delle chiavi OAuth",
            "createKey": "Crea una nuova chiave OAuth",
            "getKeyInfo": "Ottieni le informazioni di una chiave OAuth",
            "updateInfo": "Aggiorna le informazioni e i permessi di una chiave OAuth",
            "delete": "Elimina una chiave OAuth",
        },
        "backup": {
            "view": "Visualizza i backup disponibili",
            "createImage": "Crea un'immagine di backup",
            "recoverImage": "Ripristina il sistema da un'immagine backup",
        },
        "view": {
            "view": "Permesso base di visualizzazione",
            "home": "Visualizza la pagina principale",
            "sistema": "Visualizza la pagina del sistema",
        }
    },

};

var permission = {
    "administration.studenti.*": true,
    "administration.OAuth.createKey": true,
    "administration.view": true,
}


function generatePermissionCheckboxes(obj, level = 0, lp = 0, base_permission = '') {
    let html = '';
    const keys = Object.keys(obj);
    for (let i = 0; i < keys.length; i++) {
        const key = keys[i];
        var tbase_permission = '';
        if (base_permission == '') {
            tbase_permission = key;
        } else {
            tbase_permission = base_permission + key;
        }
        const value = obj[key];
        const isObject = typeof value === 'object';
        const id = level + '-' + i;

        const margin_left = lp * 20;
        const display_perm = "(" + tbase_permission + (isObject ? '.*' : '') + ")" + (isObject ? '' : ' - ' + value);

        html += `<div class="field" style="margin-left: ${margin_left}px;">`;
        html += `<div class="control">`;
        html += `<label class="checkbox">`;
        const isChecked = permission[tbase_permission] || permission[tbase_permission + '.*'];
        html += `<input type="checkbox" id="${id}" data-permission=${tbase_permission + (isObject ? '.*' : '')} ${'data-parent="' + level + '"'} ${isChecked ? 'checked' : ''}> `;
        html += `${display_perm}`;
        html += `</label>`;
        html += `</div>`;

        if (isObject) {
            html += generatePermissionCheckboxes(value, id, (lp + 1), tbase_permission + '.');
        }
        html += `</div>`;
    }
    return html;
}
//updateCheckboxes transforms all the children of the checkbox to the same value of the checkbox
function updateCheckboxes(checkbox) {
    const id = checkbox.id;
    const isChecked = checkbox.checked;
    const parent = checkbox.getAttribute('data-parent');
    const children = document.querySelectorAll('[data-parent="' + id + '"]');
    for (let i = 0; i < children.length; i++) {
        const child = children[i];
        child.checked = isChecked;
        if (isChecked) {
            child.disabled = true;
        } else {
            child.disabled = false;
        }
        updateCheckboxes(child);
    }
}
//function updateCheckboxes2, that when the checkbox is set to false, it disables all the parents recursively
function updateCheckboxes2(checkbox) {
    const id = checkbox.id;
    const isChecked = checkbox.checked;
    const parent = checkbox.getAttribute('data-parent');
    const children = document.querySelectorAll('[data-parent="' + id + '"]');
    for (let i = 0; i < children.length; i++) {
        const child = children[i];
        child.checked = isChecked;
        updateCheckboxes2(child);
    }
    if (parent != null) {
        const parent_checkbox = document.getElementById(parent);
        if (!isChecked) {
            parent_checkbox.checked = false;
            updateCheckboxes2(parent_checkbox);
        }
    }
}
//updateCheckboxesFromPermissions
// for each checkbox set to true, call the updateCheckboxes
function updateCheckboxesFromPermissions() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    for (let i = 0; i < checkboxes.length; i++) {
        const checkbox = checkboxes[i];
        if (checkbox.checked) {
            updateCheckboxes(checkbox);
        }
    }
}

//updatePermissions function gets what permission
//get all the checkboxes set to true. If a permission ends with .* means it is a parent, so remove from the final permissions all the children but keep the parent
function updatePermissions() {
    const checkboxes = document.querySelectorAll('input[type="checkbox"]');
    const permissions = {};
    for (let i = 0; i < checkboxes.length; i++) {
        const checkbox = checkboxes[i];
        if (checkbox.checked) {
            const permission = checkbox.getAttribute('data-permission');
            permissions[permission] = true;
        }
    }

    const keys = Object.keys(permissions);
    for (let i = 0; i < keys.length; i++) {
        const key = keys[i];

        if (key.endsWith('.*')) {
            const parent = key.substring(0, key.length - 2);
            const children = Object.keys(permissions).filter((k) => k.startsWith(parent + '.'));
            for (let j = 0; j < children.length; j++) {
                const child = children[j];
                delete permissions[child];
            }
            permissions[parent + ".*"] = true;
        }
    }
    //check for each permissions if there is a children, if there is remove it
    for (let i = 0; i < keys.length; i++) {
        const key = keys[i];
        if (key.endsWith('.*')) {
            const parent = key.substring(0, key.length - 2);
            console.log(parent);
            const children = Object.keys(permissions).filter((k) => k.startsWith(parent + '.'));
            console.log(children)
            for (let j = 0; j < children.length; j++) {
                const child = children[j];
                console.log(child);
                //if it is not parent + .* then delete it
                if (child != parent + ".*") {
                    delete permissions[child];
                }
            }
        }
    }

    permission = permissions;
    $('#permission-json').html(JSON.stringify(permission, null, 4));
}

$(document).ready(function () {
    // Define the permissions object


    // Function to generate permission checkboxes


});
$(document).on('change', 'input[type="checkbox"]', function () {
    updateCheckboxes(this);
    updatePermissions();
    updateDBPermissions();
});

//set the notification up is-right

function updateOAuthKeyInfo(data) {
    /*
    {"exit":"success","data":{"id":27,"type":"admin_created","grade":0,"okey":"37663a06913a188d43ca98f8d8550fe7","expiration":"2023-04-11 16:12:31","access_to":"{\"all\": true}","commento":"Mastercom TEST"}}
    */
    const key_input = document.getElementById('key');
    var expiration_input = document.getElementById('expiration');
    //set expiration input to yyyy-MM-dd
    expiration_input.value = data.expiration.substring(0, 10);

    //set them to the received data
    key_input.value = data.okey;

    //now set the permissions
    permission = JSON.parse(data.access_to);
    console.log(permission);
    //update the checkboxes
    $('#permissions').html(generatePermissionCheckboxes(permission_descriptions));
    //when a checkbox is clicked, update all the checkboxes

    updateCheckboxesFromPermissions();
    updatePermissions();
}
function updateDBPermissions() {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    fetch("api/updateOAuthInfo.php?id=" + id + "&field=access_to&value=" + JSON.stringify(permission))
        .then(response => response.json())
        .then(data => {
            if (data.exit == "success") {
                $.notify("Permessi aggiornati", "success");
            } else {
                $.notify("Errore nell'aggiornamento dei permessi", "error");
            }
        })
        .catch(error => {
            console.error("Failed to update the permissions:", error);
            $.notify("Errore nell'aggiornamento dei permessi", "error");
        })
}
//when the expiration is changed, update the expiration using the api api/updateOAuthInfo.php
$(document).on('change', '#expiration', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    //convert the expiration to MySQL TIMESTAMP format
    const expiration = this.value + " 00:00:00";
    fetch("api/updateOAuthInfo.php?id=" + id + "&field=expiration&value=" + expiration)
        .then(response => response.json())
        .then(data => {
            if (data.exit == "success") {
                $.notify("Data di scadenza aggiornata", "success");
            } else {
                $.notify("Errore nell'aggiornamento della data di scadenza", "error");
            }
        })
        .catch(error => {
            console.error("Failed to update the expiration date:", error);
            $.notify("Errore nell'aggiornamento della data di scadenza", "error");
        });
});
//chen someone clicks the key, copy it to cliboard and show a notification
$(document).on('click', '#key', function () {
    const key = document.getElementById('key');
    key.select();
    key.setSelectionRange(0, 99999);
    document.execCommand("copy");
    $.notify("Chiave copiata negli appunti", "success");
});

$(document).ready(function () {
    //get the id from the url
    const urlParams = new URLSearchParams(window.location.search);
    const id = urlParams.get('id');
    fetch("api/getOAuthInfo.php?id=" + id)
        .then(response => response.json())
        .then(data => {
            if (data.exit == "success") {
                console.log(data);
                updateOAuthKeyInfo(data.data);

            } else {
                $.notify("Errore nel caricamento dei dati della chiave OAuth", "error");
            }
        })
        .catch(error => {
            console.error("Failed to load the OAuth key data:", error);
            $.notify("Errore nel caricamento dei dati della chiave OAuth", "error");
        });
});
$.notify.defaults({ globalPosition: 'bottom right' })
