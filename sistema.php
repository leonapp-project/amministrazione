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
    <script src="src/js/tableForHome.js"></script>
    <script src="src/js/modifiableText.js"></script>

</head>

<body>
    <?php require "navbar.php"; ?>
    <!-- Title -->
    <section class="section">
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

    <section>
        <div class="container">
            <h1 class="title">
                Backup
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
                <a class="button is-warning is-light" href="#">
                    <span class="icon">
                        <i class="fas fa-upload"></i>
                    </span>
                    <span>Carica backup</span>
                </a>
            </div>
            <div class="table-container">
                <table id="backups-table" class="table is-hoverable is-striped is-responsive">
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
    </section>
    <script>
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
            const access_to = JSON.stringify({ "all": true });
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
                            row.append($("<td>").html(`<a href="#">settings <i class="fas fa-cog"></i></a>`));
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

    </script>
    <!-- below the scripts associated with the backups -->
    <script>
        // Get the table and progress bar elements
        const table = document.getElementById('backups-table');
        const progressBar = document.getElementById('loading_backups_icon');

        function resizeBackupsTable() {
            const tableWidth = table.offsetWidth;
            progressBar.style.width = `${tableWidth}px`;
        }
        function backupIsLoading() {
            progressBar.style.display = 'block';
            table.style.opacity = '0';
        }
        function backupIsLoaded() {
            progressBar.style.display = 'none';
            table.style.opacity = '1';
        }
        function updateBackupsTable() {
            //Fetch the backups from backupapi/getBackups.php
            // return is like this {"exit":"success","data":[{"file_id":"1MdojYhAB-Gretmd2uwKxS9S938VX1g5q","display_name":"Rubik_Beastly (1).zip","last_modified":"12\/03\/2023 12:46:51"}]}
            backupIsLoading();
            fetch("backupapi/getBackups.php")
                .then(response => response.json())
                .then(data => {
                    if (data.exit === "success") {
                        console.log("Fetched backups data:", data);
                        const backups = data.data;
                        const tableBody = $("#backups-table tbody");

                        // Clear existing rows from table
                        tableBody.empty();

                        // Add new rows to table
                        backups.forEach((backup, index) => {
                            const row = $("<tr>");
                            //display the name as a link https://drive.google.com/file/d/$fileId/view
                            row.append($("<td>").html('<a href="https://drive.google.com/file/d/' + backup.file_id + '/view" target="_blank">' + backup.display_name + '</a>'));
                            row.append($("<td>").text(backup.last_modified));
                            //append size in MB, so convert from B to MB         
                            row.append($("<td>").text((backup.size / 1024 / 1024).toFixed(2) + " MB"));       
                            row.append($("<td>").html('<a class="button is-small is-rounded is-outlined is-primary" href="backupapi/downloadBackup.php?file_id=' + backup.file_id + '"><span class="icon"> <i class="fas fa-download"></i> </span></a>'));
                            tableBody.append(row);
                        });
                    } else {
                        // Handle error case
                        console.error("Failed to fetch backups data");
                    }
                    backupIsLoaded();
                })
                .catch(error => {
                    console.error("Failed to fetch backups data:", error);
                    backupIsLoaded();
                });
        }
        function createBackupImage() {
            backupIsLoading();
            fetch("backupapi/createBackupImage.php?backupname=immagine%20manuale")
                .then(response => response.json())
                .then(data => {
                    if (data.exit === "success") {
                        console.log("Created backup image:", data);
                        $.notify("Backup creato", "success");
                    } else {
                        // Handle error case
                        console.error("Failed to create backup image");
                        $.notify("Errore nella creazione del backup", "error");
                    }
                    updateBackupsTable();
                })
                .catch(error => {
                    console.error("Failed to create backup image:", error);
                    $.notify("Errore nella creazione del backup", "error");
                });
        }

        window.addEventListener('resize', () => {
            resizeBackupsTable();
        });
        
        window.addEventListener('load', () => {
            resizeBackupsTable();
            updateBackupsTable();

                });
    </script>
</body>