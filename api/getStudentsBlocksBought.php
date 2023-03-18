<?php
/* Path: api/getStudentsBlocksBought.php 
MySQL tables
utenti
id, first_name, last_name, email, last_login, date_joined, class_number, class_section, can_buy_tickets, username

OAuth
id, type, grade, okey, expiration, access_to, commento

acquisti
id, user_id, date_purchased, ticket_type

this api is api/getStudentsBlocksBought.php?
OAuth: 32 chars
block_type: String (default: "FOCACCINA") --> "FOCACCINA"
month: int
year: int
mode: string (JSON (default) or CSV)

The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp).
The access_to, which is a JSON, should have the key/permission administration.studenti.getAllBlocksBought set to true or
any of the roots set to true for example administration.studenti.* or administration.* or *.

All the data should be ordered first by class_number and class_section and then by last_name and first_name.

Sample response for JSON:
{
    "status": "success",
    "data": [
        {
            "id": 1,
            "first_name": "Mario",
            "last_name": "Rossi",
            "class_number": 1,
            "class_section": "CLA",
            "blocks_bought": 2
        },
        {
            "id": 2,
            "first_name": "Luigi",
            "last_name": "Verdi",
            "class_number": 1,
            "class_section": "CLA",
            "blocks_bought": 1
        },
        {
            "id": 3,
            "first_name": "Giovanni",
            "last_name": "Bianchi",
            "class_number": 1,
            "class_section": "SCA",
            "blocks_bought": 0
        }
    ]
}

alternatively for CSV
"Questa tabella mostra i blocchi acquistati da ogni studente nel mese di 2023-10"
"emesso il", CURRENT_TIMESTAMP as day/month/year, CURRENT TIMESTAMP as hour:minute:second
"", "", ""
"Nome", "Cognome", "Classe", "Sezione", "blocchi acquistati"
"Mario", "Rossi", "1", "CLA", FOCACCINA_X2
"Luigi", "Verdi", "1", "CLA", FOCCACCINA_X1
*/

//import once the database connection
require_once 'mysqli.php';

require_once 'utils.php';
$permission_needed = "administration.studenti.getAllBlocksBought";
if (!checkOAuthPermissionFor($permission_needed)) {
    echo json_encode(array("exit" => "error", 'error' => "Invalid OAuth key for \"$permission_needed\". Contact the administrator to resolve this issue."));
    exit;
}

//get the OAuth token
$OAuth = $_GET['OAuth'];

//get the block type
$block_type = $_GET['block_type'];
if (!isset($block_type)) {
    $block_type = "FOCACCINA";
}

//get the month
$month = $_GET['month'];
if (!isset($month)) {
    $month = date("m");
}

//get the year
$year = $_GET['year'];
if (!isset($year)) {
    $year = date("Y");
}

//get the mode
$mode = $_GET['mode'];
if (!isset($mode)) {
    $mode = "JSON";
}

//get the data excluding those who have bought 0 blocks
$query = "SELECT utenti.id, utenti.first_name, utenti.last_name, utenti.class_number, utenti.class_section, COUNT(acquisti.ticket_type) AS blocks_bought FROM utenti LEFT JOIN acquisti ON utenti.id = acquisti.user_id AND acquisti.ticket_type = ? AND MONTH(acquisti.date_purchased) = ? AND YEAR(acquisti.date_purchased) = ? GROUP BY utenti.id HAVING blocks_bought > 0 ORDER BY utenti.class_number, utenti.class_section, utenti.last_name, utenti.first_name";
$stmt = $mysqli->prepare($query);
$stmt->bind_param("sii", $block_type, $month, $year);
$stmt->execute();
$result = $stmt->get_result();
$data = array();
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

//close the connection
$mysqli->close();

//return the data
if ($mode == "JSON") {
    echo json_encode(array("status" => "success", "data" => $data));
} else if ($mode == "CSV") {
    $filename = "blocchi acquistati " . $month . '-' . $year . ".csv";
    // Make the user download the CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename='.$filename);

    // Open a file pointer to the output stream
    $output = fopen('php://output', 'w');

    // Write the CSV header
    fputcsv($output, array("", "", ""));
    fputcsv($output, array("Questa tabella mostra i blocchi acquistati di focaccine da ogni studente nel mese di $year-$month"));
    fputcsv($output, array("emesso il", date("Y-m-d"), date("H:i:s")));
    fputcsv($output, array("", "", ""));
    fputcsv($output, array("Nome", "Cognome", "Classe", "Sezione", "blocchi acquistati"));

    // Write the CSV data
    foreach ($data as $row) {
        fputcsv($output, array($row['first_name'], $row['last_name'], $row['class_number'], strtoupper($row['class_section']), "FOCACCINA_X".$row['blocks_bought']));

        // Write a line break after each row
        fwrite($output, PHP_EOL);
    }

    // Close the file pointer
    fclose($output);
}


//generate sample URL to test: http://localhost/api/getStudentsBlocksBought.php?OAuth=12345678901234567890123456789012&block_type=FOCACCINA&month=10&year=2023&mode=JSON
?>