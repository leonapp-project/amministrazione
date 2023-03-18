<?php
//Path: sistema.php
//Compare this snippet from home.php:

require_once "utils/checkAuth.php";
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>LEONAPP - AMMINISTRAZIONE</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.3/css/bulma.min.css">
    <script src="https://kit.fontawesome.com/af303d99ee.js" crossorigin="anonymous"></script>
    <!-- import jquery -->
    <script src="https://code.jquery.com/jquery-3.6.3.min.js"
        integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>
    <script src="src/js/modifiableText.js"></script>
    <script src="src/js/OAuthListTable.js"></script>
    <script src="src/js/backupsSystemUtils.js"></script>
    <script src="src/js/macrosSystemUtils.js"></script>
    <style>
        .is-max-height-25vh {
            overflow-y: auto;
            max-height: 25vh;
            width: fit-content;
        }
    </style>
</head>

<body>
    <?php require "navbar.php"; ?>
    <!-- Title -->
    <section class="section">
        <a name="top"></a>
        <div class="container">
            <h1 class="title">
                Chiavi OAuth
            </h1>
            <h2 class="subtitle">
                Gestisci le chiavi OAuth per i programmi e le piattaforme esterne
            </h2>
            <div class="buttons">
                <a class="button is-primary is-light" href="#" onClick="createNewOAuth();">
                    <span class="icon">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span>Crea chiave OAuth</span>
                </a>
            </div>
            <div class="table-container">
                <table id="oauth-table" class="table is-hoverable is-striped is-responsive">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th style="width: 200px">Commento / nome</th>
                            <th>Valida fino al</th>
                            <th>Impostazioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be added dynamically by the script -->
                    </tbody>
                </table>
            </div>

        </div>
    </section>

    <!-- Users section, create and delete such as the OAuth -->
    <section>
        <a name="users"></a>
        <div class="container">
            <h1 class="title">
                Utenti
            </h1>
            <h2 class="subtitle">
                Gestisci gli utenti del sistema
            </h2>
            <div class="buttons">
                <a class="button is-primary is-light" onClick="createNewAdminUser();">
                    <span class="icon">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span>Crea utente</span>
                </a>
            </div>
            <div class="table-container">
                <table id="users-table" class="table is-hoverable is-striped is-responsive">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Username</th>
                            <th>Impostazioni</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be added dynamically by the script -->
                    </tbody>
                </table>
            </div>

        </div>

    </section>

    <section>
        <a name="backups"></a>
        <div class="container">
            <h1 class="title">
                Backups
            </h1>
            <h2 class="subtitle">
                Gestisci i backups del sistema e delle tabelle
            </h2>
            <div class="buttons">
                <a class="button is-primary is-light" href="#" onClick="createBackupImage();">
                    <span class="icon">
                        <i class="fas fa-plus"></i>
                    </span>
                    <span>Crea backup</span>
                </a>
                <a class="button is-warning is-light" id="backup-upload-btn">
                    <span class="icon">
                        <i class="fas fa-upload"></i>
                    </span>
                    <span>Carica backup</span>
                </a>
            </div>
            <div class="table-container is-max-height-25vh">
                <table id="backups-table" class="table is-hoverable is-striped is-responsive ">
                    <thead>
                        <tr>
                            <th>Nome del backup</th>
                            <th>Data di caricamento</th>
                            <th>Dimensione</th>
                            <th>ripristina</th>
                        </tr>
                        <tr>
                            <div class="progress" id="loading_backups_icon" style="width: 100%">
                                <progress class="progress is-small is-secondary" max="100">15%</progress>
                            </div>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Rows will be added dynamically by the script -->
                    </tbody>
                </table>
            </div>

        </div>
    </section>
    <!-- put some padding some space -->
    <div style="padding: 20px;"></div>
    <section>
        <a name="macros"></a>
        <div class="container">
            <h1 class="title">
                Macros
            </h1>
            <h2 class="subtitle">
                Operazioni automatiche da eseguire
            </h2>
            <div class="buttons">
                <a class="button is-danger" href="#">
                    Cancella tutti i dati
                </a>
                <a class="button is-warning is-light" href="#">
                    Resetta le classi
                </a>
                <a class="button is-danger is-light" href="#">
                    Resetta le autorizzazioni
                </a>
            </div>
        </div>
        <div class="container">
            <h2 class="title is-4">
                Abilita nuovi studenti da .csv
                <p class="help is-info" style="margin-top: 0px;">
                    <span class="icon">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    Il csv deve rispettare il formato specificato su dev.leonapp.it
                </p>
            </h2>
            <div class="buttons">
                <a class="button is-warning is-light" id="upload-whitelist-file" onclick="askForWhitelistFile();">
                    <span class="icon">
                        <i class="fas fa-upload"></i>
                    </span>
                    <span>Carica file</span>
                </a>
            </div>
    </section>
    <!--Bulma  modal for the createNewAdminUser asking for username and passowrd -->
    <div class="modal" id="new-admin-user-modal">
        <div class="modal-background"></div>
        <div class="modal-content">
            <div class="box">
                <h1 class="title">Create New Admin User</h1>
                <div class="field">
                    <label class="label">Username</label>
                    <div class="control">
                        <input class="input" type="text" id="new-admin-username" placeholder="Enter username">
                    </div>
                </div>
                <div class="field">
                    <label class="label">Password</label>
                    <div class="control">
                        <input class="input" type="password" id="new-admin-password" placeholder="Enter password">
                    </div>
                </div>
                <div id="new-admin-user-modal-error" class="has-text-danger"></div>
                <div class="field is-grouped">
                    <div class="control">
                        <button class="button is-link" onclick="createNewAdminUserConfirm()">Submit</button>
                    </div>
                    <div class="control">
                        <button class="button"
                            onclick="document.getElementById('new-admin-user-modal').classList.remove('is-active')">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
        <button class="modal-close is-large" aria-label="close"
            onclick="document.getElementById('new-admin-user-modal').classList.remove('is-active')"></button>
    </div>


    <script>
        function createNewAdminUser() {
            document.getElementById("new-admin-user-modal").classList.add("is-active");
        }
        function createNewAdminUserConfirm() {
            //fetch api/createAdminUser.php
            fetch("api/createAdminUser.php?username=" + document.getElementById("new-admin-username").value + "&password=" + document.getElementById("new-admin-password").value)
                .then(response => response.json())
                .then(data => {
                    if (data.exit == "success") {
                        //update the table
                        fetch("api/getSystemUsers.php")
                            .then(response => response.json())
                            .then(data => {
                                if (data.exit == "success") {
                                    updateSystemUsersTable(data.data);
                                }
                            });
                        //close the modal
                        document.getElementById("new-admin-user-modal").classList.remove("is-active");
                    } else {
                        //show the error
                        document.getElementById("new-admin-user-modal-error").innerHTML = data.error;
                    }
                });
            // display the modal
        }


        function updateSystemUsersTable(data) {
            //clear the table
            document.getElementById("users-table").getElementsByTagName("tbody")[0].innerHTML = "";
            //add the rows
            for (let i = 0; i < data.length; i++) {
                const row = data[i];
                //create the row
                let tr = document.createElement("tr");
                //create the columns
                let td1 = document.createElement("td");
                let td2 = document.createElement("td");
                let td3 = document.createElement("td");
                //add the data to the columns
                td1.innerHTML = row[0];
                td2.innerHTML = row[3];
                //td3 is just settings with next a seetings icon
                td3.innerHTML = "<a href='#'><span class='icon'><i class='fas fa-cog'></i></span> impostazioni</a>";
                //add the columns to the row
                tr.appendChild(td1);
                tr.appendChild(td2);
                tr.appendChild(td3);
                //add the row to the table
                document.getElementById("users-table").getElementsByTagName("tbody")[0].appendChild(tr);
            }

        }
        //onload fetch the data from api/getSystemUsers.php and if data.exit==success then call updateSystemUsersTable(data)
        fetch("api/getSystemUsers.php")
            .then(response => response.json())
            .then(data => {
                if (data.exit == "success") {
                    updateSystemUsersTable(data.data);
                }
            });

    </script>
    <!-- below all the scripts associated with the macros -->
    <script>
        // this is the script that will be executed when the user clicks on the "cancella tutti i dati" button
        function deleteAllData() {

        }



    </script>
</body>