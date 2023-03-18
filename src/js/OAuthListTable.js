function deleteKey(id) {
    fetch("api/deleteOAuth.php?id=" + id)
        .then(response => response.json())
        .then(data => {
            if (data.exit === "success") {
                console.log("Deleted OAuth key:", data);
                $.notify("Chiave OAuth eliminata", "success");
            } else {
                // Handle error case
                console.error("Failed to delete OAuth key");
                $.notify("Errore nell'eliminazione della chiave OAuth", "error");
            }
            updateOAuthTable();
        })
        .catch(error => {
            console.error("Failed to delete OAuth key:", error);
            $.notify("Errore nell'eliminazione della chiave OAuth", "error");
        });
}
function updateCommento(id, text) {
    fetch("api/updateOAuthInfo.php?field=commento&value=" + text + "&id=" + id)
        .then(response => response.json())
        .then(data => {
            if (data.exit === "success") {
                console.log("Updated OAuth key commento:", data);
                $.notify("Commento aggiornato", "success");
            } else {
                // Handle error case
                console.error("Failed to update OAuth key commento");
                $.notify("Errore nell'aggiornamento del commento", "error");
            }
            updateOAuthTable();
        })
        .catch(error => {
            console.error("Failed to update OAuth key commento:", error);
            $.notify("Errore nell'aggiornamento del commento", "error");
        });
}
$.notify.defaults({ globalPosition: 'bottom right' })

$(document).ready(function () {
    // make the text editable
    const nomeInput = new ModifiableTextInput("prova", "prova2", (text) => updateField("first_name", text));
});
function createNewOAuth() {
    /*type: string (the type of the OAuth key)
grade: int (the grade of the OAuth key)
expiration: string (the expiration date of the OAuth key)
access_to: json string (the access to of the OAuth key)
commento: string (the comment of the OAuth key)*/
    const type = "admin_created";
    const grade = 0;
    // add one month to expiration and convert to mysql timestamp format
    const expiration = new Date(new Date().setMonth(new Date().getMonth() + 1)).toISOString().slice(0, 19).replace('T', ' ');
    const access_to = JSON.stringify({ "administration.view": true });
    const commento = "commento";
    fetch("api/createOAuth.php?type=" + type + "&grade=" + grade + "&expiration=" + expiration + "&access_to=" + access_to + "&commento=" + commento)
        .then(response => response.json())
        .then(data => {
            if (data.exit === "success") {
                console.log("Created OAuth key:", data);
                $.notify("Chiave OAuth creata", "success");
            } else {
                // Handle error case
                console.error("Failed to create OAuth key");
                $.notify("Errore nella creazione della chiave OAuth", "error");
            }
            updateOAuthTable();
        })
        .catch(error => {
            console.error("Failed to create OAuth key:", error);
            $.notify("Errore nella creazione della chiave OAuth", "error");
        });
}
function updateOAuthTable() {
    // Fetch OAuth keys data from API and update table
    fetch("api/getOAuthList.php?purpose=admin_created")
        .then(response => response.json())
        .then(data => {
            if (data.exit === "success") {
                console.log("Fetched OAuth keys data:", data);
                const oauthKeys = data.OAuth_keys;
                const tableBody = $("#oauth-table tbody");

                // Clear existing rows from table
                tableBody.empty();

                // Add new rows to table
                oauthKeys.forEach((key, index) => {
                    const row = $("<tr>");
                    row.append($("<td>").html('<a class="has-text-danger" href="#" onClick="deleteKey(' + key.id + ');"><i class="fas fa-trash-alt"></i> elimina</a>'));
                    row.append($("<td>").html(key.commento + '<button class="button ml-2 is-small is-rounded is-outlined" style="background-color: #f5f5f5; display: none;" id="bcommento' + key.id + '"><span class="icon"><i class="fas fa-edit"></i></span></button>').attr("id", "commento-" + key.id));
                    row.append($("<td>").text(key.expiration));
                    row.append($("<td>").html(`<a href="OAuthView.php?id=`+ key.id +`">settings <i class="fas fa-cog"></i></a>`));
                    tableBody.append(row);
                    const nomeInput = new ModifiableTextInput("commento-" + key.id, "bcommento" + key.id, (text) => updateCommento(key.id, text));

                });
            } else {
                // Handle error case
                console.error("Failed to fetch OAuth keys data");
            }
        })
        .catch(error => {
            console.error("Failed to fetch OAuth keys data:", error);
        });
}
$(document).ready(function () {
    updateOAuthTable();
});
