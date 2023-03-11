<?php
//Path: api/downloadTicketsBought.php
/* MySQL tables
utenti
id, first_name, last_name, email, last_login, date_joined, class_number, class_section, can_buy_tickets, username

OAuth
id, type, grade, okey, expiration, access_to, commento

acquisti
id, user_id, date_purchased, ticket_type


*/
/* 
this is an API api/downloadTicketsBought.php?
OAuth: 32 chars
mode: string (JSON (default) or CSV)
then one of the two:
- id: int (id of the student) 
- email: string (email of the student, optional if id not set)
The OAuth grade must be less than or equal to 2. It must not be expired (expiration is a timestamp).
The access_to, which is a JSON, should have the key/permission administration.studenti.getTicketsBought set to true or
any of the roots set to true for example administration.studenti.* or administration.* or *.

The output must be like this:
{
    "exit": "success",
    "data": {
        "09/2023": 1,
        "10/2023": 2,
        "11/2023": 1,
        "12/2023": 0,
    }
}
so it displays for each month the number of tickets bought, starting from the month where the first ticket was bought.

Alternatively, the CSV mode must be like this:
"Nome", NAME_OF_THE_STUDENT
"Cognome", LAST_NAME_OF_THE_STUDENT
"Email", EMAIL_OF_THE_STUDENT
"Classe", CLASS_OF_THE_STUDENT
"Sezione", SECTION_OF_THE_STUDENT
"emesso il", CURRENT_TIMESTAMP as day/month/year, CURRENT TIMESTAMP as hour:minute:second
"", "", ""
"mese", "biglietti acquistati"
"09/2023", 1
"10/2023", 2
"11/2023", 1
"12/2023", 0

*/
//display errors
//ini_set('display_errors', -1);

//import once the database connection
require_once 'mysqli.php';

//get the OAuth token
$OAuth = $_GET['OAuth'];
//if the OAuth token is not set, get it from the cookie
if (!isset($OAuth)) {
    $OAuth = $_COOKIE['OAuth_key'];
}

//get the id or the email
$id = $_GET['id'];
$email = $_GET['email'];

//check if the OAuth token is valid
$stmt = $mysqli->prepare("SELECT * FROM OAuth WHERE okey = ?");
$stmt->bind_param("s", $OAuth);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}
//the OAuth token is valid
$OAuth = $result->fetch_assoc();
if ($OAuth['grade'] > 2) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}
//check if the current timestamp is greater than the expiration timestamp
if (time() > strtotime($OAuth['expiration'])) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token expired"));
    exit();
}
//check if the access_to is valid
$access_to = json_decode($OAuth['access_to'], true);
if (!isset($access_to['administration.studenti.getTicketsBought']) && !isset($access_to['administration.studenti.*']) && !isset($access_to['administration.*']) && !isset($access_to['*'])) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}

//check if the id is set
if (!isset($id)) {
    //check if the email is set
    if (!isset($email)) {
        //the id and the email are not set
        echo json_encode(array("exit" => "error", "error" => "id and email not set"));
        exit();
    }
    //get the id from the email
    $stmt = $mysqli->prepare("SELECT id FROM utenti WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        //the email is not valid
        echo json_encode(array("exit" => "error", "error" => "email not valid"));
        exit();
    }
    //the email is valid
    $id = $result->fetch_assoc()['id'];
} else {
    //check if the id is valid
    $stmt = $mysqli->prepare("SELECT * FROM utenti WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        //the id is not valid
        echo json_encode(array("exit" => "error", "error" => "id not valid"));
        exit();
    }
    //the id is valid
    $id = $result->fetch_assoc()['id'];
} 

//get the mode
$mode = $_GET['mode'];
if (!isset($mode)) {
    //the mode is not set
    $mode = "JSON";
}

//get the data grouping by month
$stmt = $mysqli->prepare("SELECT DATE_FORMAT(date_purchased, '%m/%Y') AS month, COUNT(*) AS tickets FROM acquisti WHERE user_id = ? GROUP BY month");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

//check if the mode is JSON
if ($mode == "JSON") {
    //the mode is JSON
    //create the array
    $data = array();
    //loop through the result
    while ($row = $result->fetch_assoc()) {
        //add the row to the array
        $data[$row['month']] = $row['tickets'];
    }
    //output the data
    echo json_encode(array("exit" => "success", "data" => $data));
} else {
    //the mode is CSV
    //get the user data and then force the user to download the CSV file
    $stmt = $mysqli->prepare("SELECT first_name, last_name, email, class_section, class_number FROM utenti WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultt = $stmt->get_result();
    $user = $resultt->fetch_assoc();
    //create the CSV file
    $csv = "Nome, " . $user['first_name'] . "\n";
    $csv .= "Cognome, " . $user['last_name'] . "\n";
    $csv .= "Email, " . $user['email'] . "\n";
    $csv .= "Classe, " . $user['class_number'] . "\n";
    $csv .= "Sezione, " . $user['class_section'] . "\n";
    $csv .= "emesso il, " . date("d/m/Y") . ", " . date("H:i:s") . "\n";
    $csv .= ", , \n";
    $csv .= "mese, biglietti acquistati\n";
    //loop through the result
    while ($row = $result->fetch_assoc()) {
        //add the row to the CSV file
        $csv .= $row['month'] . ", " . "FOCACCINA_X" . $row['tickets'] . "\n";
    }
    //output the CSV file
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="biglietti_acquistati_' . $user['first_name'] . "_" . $user['last_name'] . "_" . date("d-m-Y") . '.csv"');
    echo $csv;

}


//generate a sample URL:
//http://localhost/api/downloadTicketsBought.php?OAuth=12345678901234567890123456789012&mode=JSON&id=1
?>