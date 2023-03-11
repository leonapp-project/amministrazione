//define the variable for each search parameter
var nome = "";
var cognome = "";
var classe = "";
var sezione = "";

var sort_order = "ASC";
var sort_field = "nome";

//fetch the data from the API api/getStudentList.php
function updateTable() {
  fetch("api/getStudentList.php?nome=" + nome + "&cognome=" + cognome + "&classe=" + 
  classe + "&sezione=" + sezione + "&sortBy=" + sort_field + 
  "&sort=" + sort_order + "&limit=30&startBy=0")
    .then(response => response.json())
    .then(data => {
      //if the data is not empty, display the data
      console.log(data);
      if (data.results > 0) {
        //clear the table
        document.getElementById("table-body").innerHTML = "";
        //for each entry in the data, create a new row in the table
        data.students.forEach(function(entry){
          console.log(entry)
          //create the new row
          var row = document.createElement("tr");
          //create the cells
          var nome = document.createElement("td");
          var cognome = document.createElement("td");
          var classe = document.createElement("td");
          var sezione = document.createElement("td");
          var stato = document.createElement("td");
          var impostazioni = document.createElement("td");
          //set the content of the cells
          nome.innerHTML = entry.first_name;
          cognome.innerHTML = entry.last_name;
          classe.innerHTML = entry.class_number;
          sezione.innerHTML = entry.class_section.toUpperCase();
          //stato.innerHTML = entry.can_buy_tickets;
          // to stato make a checkbox  <input type="checkbox" onChange="updatePermission(entry.id)">
          if(entry.can_buy_tickets == 1) {
            stato.innerHTML = "<input type='checkbox' value=1 checked onChange='updatePermission(" + entry.id + ", false)' checked>";
          } else if(entry.can_buy_tickets == 0) {
            stato.innerHTML = "<input type='checkbox' value=1 onChange='updatePermission(" + entry.id + ", true)'>";
          }
          //impostazioni.innerHTML = "<a href='student.php?id=" + entry.id + "'>Impostazioni</a>";
          //center the icon horizontally and vertically
          impostazioni.innerHTML = "<a href='student.php?id=" + entry.id + "'> <span class='icon is-small'><i class='fas fa-cog'></i></span> </a>";

          //append the cells to the row
          row.appendChild(nome);
          row.appendChild(cognome);
          row.appendChild(classe);
          row.appendChild(sezione);
          row.appendChild(stato);
          row.appendChild(impostazioni);
          //append the row to the table
          document.getElementById("table-body").appendChild(row);
        });
      } else {
        //if the data is empty, display the error message
        document.getElementById("table-body").innerHTML = "<tr><td colspan='6'>Nessuno studente trovato</td></tr>";
      }
    });
}

function sortTable(field) {
  //if the sort_field is different from the field clicked, set the sort_order to ASC
  if (sort_field != field) {
    sort_order = "ASC";
  } else {
    //if the sort_field is the same as the field clicked, change the sort_order
    if (sort_order == "ASC") {
      sort_order = "DESC";
    } else {
      sort_order = "ASC";
    }
  } 
  //set the sort_field to the field clicked
  sort_field = field;
  //if field==nome, change the chevron icon to the correct direction
  if (field == "nome") {
    if (sort_order == "ASC") {
      document.getElementById("nome-icon").innerHTML = "<i class='fas fa-chevron-up'></i>";
    } else {
      document.getElementById("nome-icon").innerHTML = "<i class='fas fa-chevron-down'></i>";
    }
    //remove the icon from cognome
    document.getElementById("cognome-icon").innerHTML = "";
  }
  if(field == "cognome") {
    if (sort_order == "ASC") {
      document.getElementById("cognome-icon").innerHTML = "<i class='fas fa-chevron-up'></i>";
    } else {
      document.getElementById("cognome-icon").innerHTML = "<i class='fas fa-chevron-down'></i>";
    }
    //remove the icon from nome
    document.getElementById("nome-icon").innerHTML = "";
  }

  updateTable();
}
function searchTable(field, value) {
  console.log(field, value);
  //set the search_parameter variable to the value
  switch (field) {
    case "nome":
      nome = value;
      break;
    case "cognome":
      cognome = value;
      break;
    case "classe":
      classe = value;
      break;
    case "sezione":
      sezione = value;
      break;
  }
  updateTable();
}
function updatePermission(id, value) {
  console.log(id, value);
  fetch("api/enableBuyTickets.php?student_id=" + id + "&can_buy_tickets=" + value)
    .then(response => response.json())
    .then(data => {
      console.log(data);
      updateTable();
      if(data.exit !== "success") {
        $.notify("Errore durante l'abilitazione del studente", "error");
        return;
      }
      //show a Bulma notification green that says "Studente abilitato correttamente"
      $.notify.defaults({globalPosition: 'bottom right'})

      if(value==true) {
        $.notify("Studente abilitato con successo", "success");
      } else {
        $.notify("Studente disabilitato con successo", "info");
      }
    });
}
updateTable();
