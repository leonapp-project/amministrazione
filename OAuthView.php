<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage OAuth Key</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.2/css/bulma.min.css">
    <script src="http://code.jquery.com/jquery-3.0.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>
    <script async defer src="https://kit.fontawesome.com/af303d99ee.js" crossorigin="anonymous"></script>
    <script async src="src/js/OAuthModify.js"></script>
</head>

<body>
    <!-- navbar -->
    <?php
    require_once "navbar.php";
    ?>
    <!-- first section with the title "Gestisci la chiave OAuth", under it the go back button -->
    <section class="section">
        <div class="container">
            <h1 class="title">Gestisci la chiave OAuth</h1>
            <h3 class="subtitle">
                <div class="back-link">
                    <a href="/sistema.php">
                        <span class="icon">
                            <i class="fas fa-arrow-left"></i>
                        </span>
                        Torna alla pagina principale
                    </a>
                </div>
            </h3>
            <!-- small description of the page -->
            <div class="content">
                <p>
                    Questa pagina ti permette di gestire la chiave OAuth per un'applicazione esterna.
                    Puoi modificare il nome della chiave, la data di scadenza e i permessi che la chiave può avere.
                    Per maggiori informazioni sui permessi, consulta
                    <a target="_blank"
                        href="https://github.com/leonapp-project/amministrazione/blob/main/README.md#permessi-oauth">questa
                        pagina</a>.
                </p>
            </div>
        </div>
    </section>
    <!-- section that displays the key info such as the key itself (with next to it a copy button that copies to clipboard), key expiration with under buttons that increase or decrease the expiration -->
    <section class="section">
        <div class="container">
            <h1 class="title">Informazioni chiave</h1>
            <div class="box">
                <div class="columns">
                    <div class="column">
                    <label class="label">Chiave</label>
                        <div class="field has-addons">
                            <div class="control has-icons-right is-expanded">
                                <input class="input" type="text" id="key" value="1234567890" readonly>
                                <span class="icon is-small is-right">
                                    <i class="fas fa-copy"></i>
                                </span>
                            </div>
                            <div class="control">
                                <a class="button is-primary" onclick="renewOKey();">
                                    <span class="icon is-small is-right">
                                        <i class="fa-solid fa-repeat"></i>
                                    </span>
                                </a>
                            </div>
                        </div>

                    </div>
                    <div class="column">
                        <div class="field">
                            <label class="label">Scadenza</label>
                            <div class="control">
                                <input class="input" type="date" id="expiration" value="2021-12-31">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- section that displays the permission checkboxes -->
    <section class="section">
        <div class="container">
            <h1 class="title">Permessi della chiave OAuth</h1>
            <h2 class="subtitle is-5">Consulta <a href="https://github.com/leonapp-project/amministrazione"
                    target="_blank">github.com/leonapp-project/amministrazione</a> per maggiori informazioni
            </h2>
            <div class="box">
                <div id="permissions"></div>
            </div>
        </div>
    </section>
    <!-- section that displays the permission json -->
    <section class="section">
        <div class="container">
            <h1 class="title">Permessi JSON</h1>
            <div class="box">
                <pre id="permission-json"></pre>
            </div>
        </div>
    </section>


    <script>

    </script>
</body>