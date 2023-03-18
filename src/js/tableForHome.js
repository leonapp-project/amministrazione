//define the variable for each search parameter
var nome = "";
var cognome = "";
var classe = "";
var sezione = "";

var sort_order = "ASC";
var sort_field = "nome";


//set get page to the value of the page parameter in GET
var get_page = 1;
if (window.location.href.indexOf("page=") > -1) {
  get_page = window.location.href.split("page=")[1];
}

var start_by = (get_page-1)*15;

//fetch the data from the API api/getStudentList.php
function updateTable() {
  fetch("api/getStudentList.php?nome=" + nome + "&cognome=" + cognome + "&classe=" + 
  classe + "&sezione=" + sezione + "&sortBy=" + sort_field + 
  "&sort=" + sort_order + "&limit=15&startBy=" + start_by)
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
        updatePagination(data.results, data.total_students);
      } else {
        //if the data is empty, display the error message
        document.getElementById("table-body").innerHTML = "<tr><td colspan='6'>Nessuno studente trovato</td></tr>";
        start_by = 0;
        updatePagination(0, 1);
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
  //reset the pagination
  get_page = 1;
  start_by = 0;
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
  //reset the pagination
  get_page = 1;
  start_by = 0;
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

var pagination_previous, pagination_next;
var pagination_link_previous, pagination_link_next, pagination_link_current;
var pagination_start, pagination_end;
//onload, setup the pagination buttons
window.onload = function() {
  pagination_previous = document.getElementsByClassName("pagination-previous");
  pagination_next = document.getElementsByClassName("pagination-next");
  pagination_link_previous = document.getElementById("pagination-link-previous");
  pagination_link_next = document.getElementById("pagination-link-next");
  pagination_link_current = document.getElementById("pagination-link-current");
  pagination_start = document.getElementById("pagination-start");
  pagination_end = document.getElementById("pagination-end");
  //add the onclick event to the previous button
  pagination_previous[0].onclick = function() {
    get_page -= 1;
    start_by = (get_page-1)*15;
    updateTable();
  }
  //add the onclick event to the next button
  pagination_next[0].onclick = function() {
    get_page += 1;
    start_by = (get_page-1)*15;
    updateTable();
  }
  //add the onclick event to the previous link
  pagination_link_previous.onclick = function() {
    get_page -= 1;
    start_by = (get_page-1)*15;
    updateTable();
  }
  //add the onclick event to the next link
  pagination_link_next.onclick = function() {
    get_page += 1;
    start_by = (get_page-1)*15;
    updateTable();
  }
  //ad the onclick to the end link
  pagination_end.onclick = function() {
    //get the value of pagination-end
    get_page = parseInt(pagination_end.innerHTML);
    start_by = (get_page-1)*15;
    updateTable();
  }
  //add the onclick to the start link
  pagination_start.onclick = function() {
    get_page = 1;
    start_by = (get_page-1)*15;
    updateTable();
  }

}

//function updatePagination is called when the table is updated
// will update the values of the pagination buttons
// uses the global start_by variable and knows the pages are grouped by 15
//asks in results the total number of results from the database received (max 15) and
// asks in total the total number of results from the database
function updatePagination(results, total) {
  pagination_previous = document.getElementsByClassName("pagination-previous");
  pagination_next = document.getElementsByClassName("pagination-next");
  pagination_link_previous = document.getElementById("pagination-link-previous");
  pagination_link_next = document.getElementById("pagination-link-next");
  pagination_link_current = document.getElementById("pagination-link-current");
  pagination_start = document.getElementById("pagination-start");
  pagination_end = document.getElementById("pagination-end");
  //first of all calculate the maximum number of pages
  var max_pages = Math.ceil(total/15);
  //calculate the current page
  var current_page = Math.ceil(start_by/15)+1;
  //if the current page is the first page, disable the previous button
  if (current_page == 1) {
    pagination_previous[0].classList.add("is-disabled");
  }
  //if the current page is the last page, disable the next button
  if (current_page == max_pages) {
    pagination_next[0].classList.add("is-disabled");
  }
  if (current_page != 1) {
    pagination_previous[0].classList.remove("is-disabled");
    pagination_next[0].classList.remove("is-disabled");
  }
  //Set the end of the pagination
  pagination_end.innerHTML = max_pages;
  //set the current page
  pagination_link_current.innerHTML = current_page;
  //set the start of the pagination
  pagination_start.innerHTML = 1;
  //set the previous page
  pagination_link_previous.innerHTML = current_page-1;
  //set the next page
  pagination_link_next.innerHTML = current_page+1;
  //if the current page is the first page, hide the previous link
  if (current_page == 1) {
    pagination_link_previous.style.display = "none";
  }
  //if the current page is the last page, hide the next link
  if (current_page == max_pages) {
    pagination_link_next.style.display = "none";
  }
  //if the current page is not the first page, show the previous link
  if (current_page != 1) {
    pagination_link_previous.style.display = "inline";
  }
  //if the current page is not the last page, show the next link
  if (current_page != max_pages) {
    pagination_link_next.style.display = "inline";
  }
  //if the current page is the first page, disable the previous link
  if (current_page == 1) {
    pagination_previous.classList.add("is-disabled");
  }
  //if the current page is the last page, disable the next link
  if (current_page == max_pages) {
    pagination_end.classList.add("is-disabled");
  }
  //if the current page is not the first page, enable the previous link
  if (current_page != 1) {
    pagination_start.classList.remove("is-disabled");
  }
  //if the current page is not the last page, enable the next link
  if (current_page != max_pages) {
    pagination_end.classList.remove("is-disabled");
  }


}
updateTable();
