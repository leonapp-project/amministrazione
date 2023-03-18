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
  <link rel="stylesheet" href="src/css/home.css">
  <script src="https://kit.fontawesome.com/af303d99ee.js" crossorigin="anonymous"></script>
  <!-- import jquery -->
  <script src="https://code.jquery.com/jquery-3.6.3.min.js"
    integrity="sha256-pvPw+upLPUjgMXY0G+8O0xUf+/Im1MZjXxxgOcBQBXU=" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/notify/0.4.2/notify.min.js"></script>
  <script src="src/js/tableForHome.js"></script>
  <script src="src/js/ticketsForHome.js"></script>

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
        <div class="container">
          <button id="download-button" class="button is-primary is-outlined">
            Lista blocchi acquistati
            <span class="icon">
              <i class="fas fa-caret-down"></i>
            </span>
          </button>
          <div id="download-popover" class="popover is-hidden">
            <div class="popover-content">
              <div class="field">
                <label class="label">Mese</label>
                <div class="control">
                  <div class="select">
                    <select id="month-select">
                      <option value="1">Gennaio</option>
                      <option value="2">Febbraio</option>
                      <option value="3">Marzo</option>
                      <option value="4">Aprile</option>
                      <option value="5">Maggio</option>
                      <option value="6">Giugno</option>
                      <option value="7">Luglio</option>
                      <option value="8">Agosto</option>
                      <option value="9">Settembre</option>
                      <option value="10">Ottobre</option>
                      <option value="11">Novembre</option>
                      <option value="12">Dicembre</option>
                    </select>
                  </div>
                </div>
              </div>
              <div class="field">
                <label class="label">Anno</label>
                <div class="control">
                  <div class="select">
                    <select id="year-select">
                      <?php
                      $currentYear = date("Y");
                      for ($i = $currentYear; $i >= $currentYear - 3; $i--) {
                        echo "<option value=\"$i\">$i</option>";
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="field is-grouped">
                <div class="control">
                  <a id="download-csv-button" href="#" class="button is-success">
                    Scarica
                  </a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="section" style="margin-top: -80px;">

    <!-- Table -->
    <div class="table-container">
      <table class="table is-fullwidth is-striped is-hoverable is-responsive">
        <thead>
          <tr>
            <th onclick="sortTable('nome')">Nome
              <span class="icon" id="nome-icon">
                <i class="fas fa-chevron-up"></i>
              </span>
            </th>
            <th onclick="sortTable('cognome')">Cognome<span class="icon" id="cognome-icon">
              </span></< /th>
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
    <nav class="pagination is-centered is-fullwidth" role="navigation" aria-label="pagination">
      <a class="pagination-previous">Previous</a>
      <a class="pagination-next">Next page</a>
      <ul class="pagination-list">
        <li><a class="pagination-link" id="pagination-start" aria-label="Goto page 1">1</a></li>
        <li><span class="pagination-ellipsis">&hellip;</span></li>
        <li><a class="pagination-link" id="pagination-link-previous" aria-label="Goto page 45">45</a></li>
        <li><a class="pagination-link is-current" id="pagination-link-current" aria-label="Page 46"
            aria-current="page">46</a></li>
        <li><a class="pagination-link" id="pagination-link-next" aria-label="Goto page 47">47</a></li>
        <li><span class="pagination-ellipsis">&hellip;</span></li>
        <li><a class="pagination-link" id="pagination-end" aria-label="Goto page 86">86</a></li>
      </ul>
    </nav>
  </section>
</body>

</html>