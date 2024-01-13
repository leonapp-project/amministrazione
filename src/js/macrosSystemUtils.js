 // onLoad, make so that when the button #upload-csv-file is clicked, the file upload dialog is opened and fetch the file
// to api/updateWhitelist.php as csv_file
function askForWhitelistFile() {
    console.log("askForWhitelistFile() called");
    const fileInput = document.createElement('input');
    fileInput.type = 'file';
    fileInput.accept = '.csv';
    fileInput.name = 'csv_file';
    fileInput.onchange = () => {
        // Create a new FormData object
        const formData = new FormData();
        // Append the file to the FormData object
        formData.append('csv_file', fileInput.files[0]);

        // Send a POST request to the server to upload the file
        fetch('api/updateWhitelist.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(data => {
                if (data.exit === "success") {
                    console.log("Update whitelist:", data);
                    $.notify("Whitelist aggiornata con successo", "success");
                } else {
                    // Handle error case
                    console.error("Error updating whitelist:", data);
                    $.notify("Errore nell'aggiornamento della whitelist", "error");
                }
            })
            .catch(error => console.error(error));
    };

    // Click the file input element
    fileInput.click();
}

// function to be called when the view whitelist button is clicked
// asks to api/getWhitelist.php for the whitelist and show the whitelist-table-modal
function viewWhitelist() {
    console.log("viewWhitelist() called");
    fetch('api/getWhitelist.php')
        .then(response => response.json())
        .then(data => {
            if (data.exit === "success") {
                console.log("Whitelist:", data);
                // empty the table body 
                $('#whitelist-table').empty();
                // append the rows to the table body
                data.data.forEach((row) => {
                    $('#whitelist-table').append(`
                    <tr>
                        <td>${row.first_name}</td>
                        <td>${row.last_name}</td>
                        <td>${row.email}</td>
                        <td>${row.class_number}</td>
                        <td>${row.class_section}</td>
                    </tr>
                `);
                });
                document.getElementById("whitelist-table-modal").classList.add("is-active");
                // on ESC key press, close the modal
                document.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape') {
                        document.getElementById("whitelist-table-modal").classList.remove("is-active");
                        //remove the event listener\
                        document.removeEventListener('keydown', this);
                    }
                });
            } else {
                // Handle error case
                console.error("Error getting whitelist:", data);
                $.notify("Errore nel recupero della whitelist", "error");
            }
        })
        .catch(error => console.error(error));
}