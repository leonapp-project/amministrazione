<?php
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
    <script src="https://code.jquery.com/jquery-3.6.3.min.js" integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script> 
    <script src="src/js/tableForHome.js"></script>

  </head>
<body>
<?php
require "navbar.php";
?>
  <!-- Title -->
  <section class="section">
    <div class="container">
      <h1 class="title">
        Area focaccine
      </h1>
      <h2 class="subtitle">
        Studenti abilitati
      </h2>
      <div class="buttons">
        <a class="button is-primary" href="#">
          Inserisci nuovo studente
        </a>
      </div>

      <!-- Table -->
      <div class="table-container">
        <table class="table is-fullwidth is-striped is-hoverable is-responsive">
          <thead>
            <tr>
              <th onclick="sortTable('nome')">Nome 
                <span class="icon" id="nome-icon">
                  <i class="fas fa-chevron-up"></i>
                </span></th>
              <th onclick="sortTable('cognome')">Cognome<span class="icon" id="cognome-icon">
                </span></</th>
              <th>Classe</th>
              <th>Sezione</th>
              <th>Stato</th>
              <th>Impost.</th>
            </tr>
            <tr>
              <th>
                <input type="text" placeholder="Cerca per nome" oninput="searchTable('nome', this.value)">
              </th>
              <th>
                <input type="text" placeholder="Cerca per cognome" oninput="searchTable('cognome', this.value)">
              </th>
              <th>
                <input type="text" placeholder="Cerca per classe" oninput="searchTable('classe', this.value)">
              </th>
              <th>
                <input type="text" placeholder="Cerca per sezione" oninput="searchTable('sezione', this.value)">
              </th>
              <th></th>
              <th></th>
            </tr>
          </thead>
          <tbody id="table-body">
            <!-- table entries will be dynamically generated here -->
          </tbody>
        </table>
      </div>
    </div>
  </section>



</body>
</html>