//this is meant to be used by sistema.php

//these will later be set by onload
var table, progressBar;
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
                    row.append($("<td>").html('<a class="button is-small is-rounded is-outlined is-primary" onClick="recoverFromDrive(\'' + backup.file_id + '\');"><span class="icon"> <i class="fas fa-window-restore"></i> </span></a>'));
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
function recoverFromDrive(file_id) {
    backupIsLoading();
    fetch("backupapi/recoverBackupImage.php?file_id=" + file_id)
        .then(response => response.json())
        .then(data => {
            if (data.exit === "success") {
                console.log("Recover from drive:", data);
                $.notify("Recupero completato", "success");
            } else {
                // Handle error case
                console.error("Failed to recover from drive");
                $.notify("Errore nel recupero", "error");
            }
            updateBackupsTable();
        })
        .catch(error => {
            console.error("Failed to recover from drive:", error);
            $.notify("Errore nel recupero", "error");
        });
}

window.addEventListener('resize', () => {
    resizeBackupsTable();
});

window.addEventListener('load', () => {
    table = document.getElementById('backups-table');
    progressBar = document.getElementById('loading_backups_icon');
    resizeBackupsTable();
    updateBackupsTable();

    // Get the button element
    const uploadBtn = document.getElementById('backup-upload-btn');

    // Add an event listener to the button
    uploadBtn.addEventListener('click', () => {
        // Create a file input element
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.accept = '.zip';
        fileInput.name = 'backup_data';
        fileInput.onchange = () => {
            backupIsLoading();
            // Create a new FormData object
            const formData = new FormData();
            // Append the file to the FormData object
            formData.append('backup', fileInput.files[0]);

            // Send a POST request to the server to upload the file
            fetch('backupapi/recoverBackupImage.php', {
                method: 'POST',
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.exit === "success") {
                        console.log("Recover from drive:", data);
                        $.notify("Recupero completato", "success");
                    } else {
                        // Handle error case
                        console.error("Failed to recover from drive");
                        $.notify("Errore nel recupero", "error");
                    }
                    updateBackupsTable();
                })
                .catch(error => console.error(error));
        };

        // Click the file input element
        fileInput.click();
    });
});