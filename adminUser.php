<?php
// Path: adminUser.php
require_once "utils/checkAuth.php";
require_once("mysqli.php");

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AMMINISTRAZIONE - Dati Admin</title>
    <link rel="icon" href="favicon-logo.png">
    <link async defer rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.3/css/bulma.min.css">
    <script async defer src="https://kit.fontawesome.com/af303d99ee.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>
    <script src="src/js/modifiableText.js"></script>
    <style>
        .back-link {
            display: flex;
            align-items: center;
        }

        .back-link .icon {
            margin-right: 10px;
        }
    </style>
    <script async defer>
        var dati = {};
        var titolo, display_nome, display_cognome, display_email, display_username, display_classe, display_sezione, display_stato, display_data_iscrizione, display_ultimo_accesso;
        $.notify.defaults({ globalPosition: 'bottom right' })

        function updateAdminData(admin_id) {
            //fetch api/getSystemUserInfo.php?id=student_id

            fetch("api/getSystemUserInfo.php?id=" + admin_id)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.log(data.error);
                        return;
                    }
                    dati = data.data;
                    console.log(dati);
                    setCommonNames();
                    updateDisplayInfo();
                });
        }
        function updateDisplayInfo() {
            titolo.innerHTML = "Visualizzazione dell'utente " + dati.username;
            display_email.innerHTML = dati.email;
            display_username.innerHTML = dati.username;
            display_okey.innerHTML = dati.OAuth;
            //convert date to "il " dd/mm/yyyy " alle\alle" hh:mm
        }
        function setCommonNames() {
            titolo = document.getElementById("titolo");
            display_email = document.getElementById("email");
            display_username = document.getElementById("username");
            display_okey = document.getElementById("OAuth_key");
        }

        updateAdminData(<?php echo $_GET["id"]; ?>);

        /*
        This section includes all the update functions for each field
        */
        function updateField(field_name, new_value) {
            //fetch api/updateAdminUserInfo.php?id=admin_id&field=field_name&value=new_value
            fetch("api/updateAdminUserInfo.php?id=" + dati.id + "&field=" + field_name + "&value=" + new_value)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.log(data.error);
                        $.notify("Errore nel caricamento dei dati: " + data.error, "error");
                        return;
                    }
                    updateAdminData(<?php echo $_GET["id"]; ?>);
                    //success
                    display_message = "";
                    switch (field_name) {
                        case "email":
                            display_message = "Email aggiornata con successo";
                            break;
                        case "username":
                            display_message = "Username aggiornato con successo";
                            break;
                    }
                    $.notify(display_message, "success");
                });
        }

        function getOAuthList() {
            // Fetch OAuth keys data from API and update table
            fetch("api/getOAuthList.php?purpose=admin_created")
                .then(response => response.json())
                .then(data => {
                    if (data.exit === "success") {
                        const oauthKeys = data.OAuth_keys;
                        const select = document.getElementById("OAuth_select");
                        select.innerHTML = "<option value=\"\"> </option>";
                        oauthKeys.forEach(key => {
                            const option = document.createElement("option");
                            option.value = key.okey;
                            option.innerHTML = key.commento;
                            select.appendChild(option);
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
        function displayOAuthModal() {
            document.getElementById("bind-okey-modal").classList.add("is-active");
        }
        function updateOAuthKeyModal() {
            const select = document.getElementById("OAuth_select");
            const okey = select.value;
            if (okey === "") {
                $.notify("Seleziona una chiave OAuth", "error");
                return;
            }
            // Fetch OAuth keys data from API and update table
            fetch("api/updateAdminUserInfo.php?id=<?php echo $_GET["id"]; ?>&field=OAuth&value=" + okey)
                .then(response => response.json())
                .then(data => {
                    if (data.exit === "success") {
                        $.notify("Chiave OAuth aggiornata con successo", "success");
                        document.getElementById("bind-okey-modal").classList.remove("is-active");
                        updateAdminData(<?php echo $_GET["id"]; ?>);
        } else {
            // Handle error case
            console.error("Failed to update OAuth key");
            $.notify("Errore nell'aggiornamento della chiave OAuth", "error");
        }
                })
                .catch (error => {
            console.error("Failed to update OAuth key:", error);
            $.notify("Errore nell'aggiornamento della chiave OAuth", "error");
        });
        }
    </script>
</head>

<body>
    <?php
    require "navbar.php";
    ?>
    <section class="section">
        <div class="container">
            <!-- Title -->
            <h1 class="title" id="titolo">
                Visualizzazione dati di
            </h1>
            <!-- Subtitle -->
            <h3 class="subtitle">
                <div class="back-link">
                    <a href="/sistema.php">
                        <span class="icon">
                            <i class="fas fa-arrow-left"></i>
                        </span>
                        Torna alla pagina precedente
                    </a>
                </div>
            </h3>
            <!-- now display "Username" as the label for the "username" -->
            <div class="field">
                <label class="label">username</label>
                <div class="control is-flex">
                    <div class="is-size-5" id="username">
                        Prova
                    </div>
                    <button class="button ml-2 is-small is-rounded is-outlined" style="background-color: #f5f5f5;"
                        id="edit_username">
                        <span class="icon">
                            <i class="fas fa-edit"></i>
                        </span>
                    </button>
                </div>
                <p class="help is-info">
                    <span class="icon">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    Se si cambia il campo username, bisogna reimpostare la password
                </p>
            </div>
            <!-- now display "Email" as the label for the "email" -->
            <div class="field">
                <label class="label">Email</label>
                <div class="control is-flex">
                    <div class="is-size-5" id="email">
                        test.tt@aa.c
                    </div>
                    <button class="button ml-2 is-small is-rounded is-outlined" style="background-color: #f5f5f5;"
                        id="edit_email">
                        <span class="icon">
                            <i class="fas fa-edit"></i>
                        </span>
                    </button>
                </div>
            </div>
            <!-- now display the OAuth key associated with it and next to it a button to renew it that opens a modal that prompts the API key name to use -->
            <div class="field">
                <label class="label">OAuth key</label>
                <div class="control is-flex">
                    <div class="is-size-5" id="OAuth_key">
                        1234567890
                    </div>
                    <button class="button ml-2 is-small is-rounded is-outlined" style="background-color: #f5f5f5;"
                        onclick="displayOAuthModal()">
                        <span class="icon">
                            <i class="fas fa-edit"></i>
                        </span>
                    </button>
                </div>
            </div>
            <!-- display the last login date as HH:MM:SS DD/MM/YYYY -->
            <div class="field">
                <label class="label">Ultimo accesso</label>
                <div class="control">
                    <div class="is-size-5" id="ultimo_accesso">
                        12:00:00 01/01/2020
                    </div>
                </div>
            </div>

    </section>
    <!-- display a section to change the password with double prompt and button. Inputs not full width but small-->
    <section class="section">
        <div class="container">
            <h1 class="title">Cambia password</h1>
            <div class="box">
                <div class="field">
                    <label class="label">Nuova password</label>
                    <div class="control">
                        <input class="input" type="password" id="new_password">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Conferma nuova password</label>
                    <div class="control">
                        <input class="input" type="password" id="new_password_confirm">
                    </div>
                </div>
                <div class="field is-grouped is-right">
                    <div class="control">
                        <button class="button is-success" id="change_password_button">
                            Cambia password
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- modal with the title "Collega a OAuth key" with below a list of OAuth keys. When clicked call a function and close the modal -->
    <div class="modal" id="bind-okey-modal">
        <div class="modal-background"
            onclick="document.getElementById('bind-okey-modal').classList.remove('is-active');"></div>
        <div class="modal-content">
            <div class="box">
                <!-- Title -->
                <h1 class="title">
                    Collega a OAuth key
                </h1>
                <!-- Subtitle -->
                <h3 class="subtitle">
                    Seleziona una chiave OAuth da collegare a questo utente.
                </h3>
                <h3 class="subtitle">
                    N.B.: Se aggiorni il codice okey da altre parti, dovrai riassociare la chiave all'utente qui.
                </h3>
                <div class="field">
                    <label class="label">Lista chiavi disponibili</label>
                    <div class="control">
                        <div class="select is-fullwidth">
                            <select id="OAuth_select">
                                <option value=""> </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="field is-grouped">
                    <div class="control is-right">
                        <button class="button is-success" onclick="updateOAuthKeyModal();">
                            Collega
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <button class="modal-close is-large" aria-label="close"
            onclick="document.getElementById('bind-okey-modal').classList.remove('is-active');"></button>
    </div>

    <script>
        //const nomeInput = new ModifiableTextInput("nome", "edit_nome", (text) => console.log(`New name: ${text}`));
        const emailInput = new ModifiableTextInput("email", "edit_email", (text) => updateField("email", text));
        const usernameInput = new ModifiableTextInput("username", "edit_username", (text) => updateField("username", text));
        getOAuthList();


    </script>
</body>