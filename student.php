<?php
// Path: student.php
// compare this snipped with api/getStudentInfo.php
/*
example API output
{
  "exit": "success",
  "data": {
    "id": 2,
    "first_name": "Niccolo",
    "last_name": "Pagano",
    "email": "niccolo.pagano@studentilicei.leonexiii.it",
    "last_login": "2023-02-27 16:57:29.771402",
    "date_joined": "2023-02-27 10:56:01.782840",
    "class_section": "cla",
    "can_buy_tickets": 1,
    "username": "niccolo",
    "class_number": "4"
  }
}

*/
require_once "utils/checkAuth.php";

// Connect to database
require_once("mysqli.php");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Data</title>
    <link async defer rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.3/css/bulma.min.css">
    <script async defer src="https://kit.fontawesome.com/af303d99ee.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script  src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script> 
    <!-- import src/js/modifiableText.js -->
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
        $.notify.defaults({globalPosition: 'bottom right'})

        function updateStudentData(student_id) {
            //fetch api/getStudentInfo.php?id=student_id

            fetch("api/getStudentInfo.php?id=" + student_id)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        console.log(data.error);
                        return;
                    }
                    dati = data.data;
                    console.log(dati);
                    setCommonNames();
                    console.log(titolo)
                    updateDisplayInfo();
                });
        }
        function updateDisplayInfo() {
            titolo.innerHTML = "Visualizzazione dati di " + dati.first_name + " " + dati.last_name;
            display_nome.innerHTML = dati.first_name;
            display_cognome.innerHTML = dati.last_name;
            display_email.innerHTML = dati.email;
            display_username.innerHTML = dati.username;
            display_classe.value = dati.class_number;
            display_sezione.value = dati.class_section;
            display_stato.checked = dati.can_buy_tickets == 1 ? true : false;
            //convert date to "il " dd/mm/yyyy " alle\alle" hh:mm
            display_ultimo_accesso.innerHTML = "il " + dati.last_login.split(" ")[0].split("-").reverse().join("/") + " alle " + dati.last_login.split(" ")[1].split(":").slice(0, 2).join(":");
        }
        function setCommonNames() {
            titolo = document.getElementById("titolo");
            display_nome = document.getElementById("nome");
            display_cognome = document.getElementById("cognome");
            display_email = document.getElementById("email");
            display_username = document.getElementById("username");
            display_classe = document.getElementById("classe");
            display_sezione = document.getElementById("sezione");
            display_stato = document.getElementById("puo_acquistare");
            display_ultimo_accesso = document.getElementById("ultimo_accesso");
        }

        updateStudentData(<?php echo $_GET["id"]; ?>);

        /*
        This section includes all the update functions for each field
        */
       function updateField(field_name, new_value) {
        //fetch api/updateStudentInfo.php?id=student_id&field=field_name&value=new_value
        fetch("api/updateStudentInfo.php?id=" + dati.id + "&field=" + field_name + "&value=" + new_value)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    console.log(data.error);
                    $.notify("Errore nel caricamento dei dati: " + data.error, "error");
                    return;
                }
                updateStudentData(<?php echo $_GET["id"]; ?>);
                //success
                display_message = "";
                switch (field_name) {
                    case "first_name":
                        display_message = "Nome aggiornato con successo";
                        break;
                    case "last_name":
                        display_message = "Cognome aggiornato con successo";
                        break;
                    case "email":
                        display_message = "Email aggiornata con successo";
                        break;
                    case "username":
                        display_message = "Username aggiornato con successo";
                        break;
                    case "class_number":
                        display_message = "Classe aggiornata con successo";
                        break;
                    case "class_section":
                        display_message = "Sezione aggiornata con successo";
                        break;
                    case "can_buy_tickets":
                        display_message = "Permessi aggiornati con successo";
                        break;
                    }
                $.notify(display_message, "success");
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
                    <a href="/home.php">
                        <span class="icon">
                            <i class="fas fa-arrow-left"></i>
                        </span>
                        Torna alla pagina principale
                    </a>
                </div>
            </h3>
            <!-- now display "Nome" as the label for the "first_name" -->
            <div class="field">
                <label class="label">Nome</label>
                <div class="control is-flex">
                    <div class="is-size-5" id="nome">
                        Prova
                    </div>
                    <button class="button ml-2 is-small is-rounded is-outlined" style="background-color: #f5f5f5;"
                        id="edit_nome">
                        <span class="icon">
                            <i class="fas fa-edit"></i>
                        </span>
                    </button>
                </div>
            </div>
            <!-- now display "Cognome" as the label for the "last_name" -->
            <div class="field">
                <label class="label">Cognome</label>
                <div class="control is-flex">
                    <div class="is-size-5" id="cognome">
                        Prova
                    </div>
                    <button class="button ml-2 is-small is-rounded is-outlined" style="background-color: #f5f5f5;"
                        id="edit_cognome">
                        <span class="icon">
                            <i class="fas fa-edit"></i>
                        </span>
                    </button>
                </div>
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
                <!-- add a small subtext with the icon of info that says "cambiare il campo email potrebbe comportare problemi" -->
                <p class="help is-info">
                    <span class="icon">
                        <i class="fas fa-info-circle"></i>
                    </span>
                    Cambiare il campo email potrebbe comportare problemi
                </p>
            </div>
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
            </div>
            <!-- now display "Classe" as the label for the "class" which is a selection -->
            <div class="field">
                <label class="label">Classe</label>
                <div class="control">
                    <div class="select">
                        <select id="classe" onChange="updateField('class_number', this.value)">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                            <option>4</option>
                            <option>5</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- now display "Sezione" as the label for the "section" which is a selection -->
            <div class="field">
                <label class="label">Sezione</label>
                <div class="control">
                    <div class="select">
                        <select id="sezione" onChange="updateField('class_section', this.value)">
                            <option value="cla">Classico</option>
                            <option value="sca">Scientifico A</option>
                            <option value="scb">Scientifico B</option>
                            <option value="scc">Scientifico C</option>
                            <option value="spo">Sportivo</option>
                        </select>
                    </div>
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

            <!-- header with the text "Focaccine" -->
            <h1 class="title">
                Area focaccine
            </h1>
            <!-- display if the user is allowed to buy with a checkbox -->
            <div class="field">
                <label class="label">Permesso di acquisto</label>
                <div class="control">
                    <label class="checkbox">
                        <input type="checkbox" id="puo_acquistare" onChange="updateField('can_buy_tickets', this.checked)">
                        Lo studente può acquistare blocchi di focaccine attraverso la piattaforma
                    </label>
                </div>
            </div>
            <!-- display the number of focaccine bought -->
            <div class="field">
                <label class="label">Focaccine acquistate in totale</label>
                <div class="control">
                    <div class="is-size-5" id="focaccine_acquistate">
                        0
                         - 
                        <!-- diplay "scarica tutti gli acquisti" as a link -->
                        <a href="#" class="has-text-info">scarica le scansioni (.xlsx)</a>
                    </div>
                </div>
            </div>
            <!-- display the number of focaccine tickets bought this month -->
            <div class="field">
                <label class="label">Blocchi acquistati questo bene</label>
                <div class="control">
                    <div class="is-size-5" id="blocchi_acquistati_mese">
                        0
                         - 
                        <a href="#" class="has-text-info">scarica tutti gli acquisti di ogni mese (.xlsx)</a>
                    </div>
                </div>
            </div>

    </section>
    <script>
        //const nomeInput = new ModifiableTextInput("nome", "edit_nome", (text) => console.log(`New name: ${text}`));
        const nomeInput = new ModifiableTextInput("nome", "edit_nome", (text) => updateField("first_name", text));
        const cognomeInput = new ModifiableTextInput("cognome", "edit_cognome", (text) => updateField("last_name", text));
        const emailInput = new ModifiableTextInput("email", "edit_email", (text) => updateField("email", text));
        const usernameInput = new ModifiableTextInput("username", "edit_username", (text) => updateField("username", text));
        </script>
</body>