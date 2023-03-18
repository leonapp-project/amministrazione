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
