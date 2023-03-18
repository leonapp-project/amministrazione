<?php
/* Path: api/updateWhitelist.php
This is an API to update the whitelist.
MySQL tables
user_whitelist
id, email, first_name, last_name, class_section, class_number

OAuth: 32 chars
.csv file given as "csv_file" in the POST request

The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp).
The access_to, which is a JSON, should have the key/permission administration.studenti.updateWhitelist set to true or
any of the roots set to true for example administration.studenti.* or administration.* or *.

Sample file
"Informazioni cronologiche","Nome utente","Nome","Cognome","Di che classe 6?","Di che sezione s31?"
"2023/03/18 3:28:24 PM CET","niccolo.pagano@studentilicei.leonexiii.it","Niccolo","Pagano","4","Classico"
"2023/03/18 3:29:38 PM CET","niccolo.pagano@studentilicei.leonexiii.it","Niccolo","Pagano","3","Classico"

*/
//errors
ini_set('display_errors', 1);

//import once the database connection
require_once 'mysqli.php';
header('Content-Type: application/json');

require_once 'utils.php';
$permission_needed = "administration.studenti.updateWhitelist";
if (!checkOAuthPermissionFor($permission_needed)) {
    echo json_encode(array("exit" => "error", 'error' => "Invalid OAuth key for \"$permission_needed\". Contact the administrator to resolve this issue."));
    exit;
}

//get the csv file
$csv_file = $_FILES['csv_file'];
if (!isset($csv_file)) {
    echo json_encode(array("exit" => "error", "error" => "No csv file given"));
    exit;
}

//check if the file is a csv file
if ($csv_file['type'] != "text/csv") {
    echo json_encode(array("exit" => "error", "error" => "The file is not a csv file"));
    exit;
}

//check if the file is empty
if ($csv_file['size'] == 0) {
    echo json_encode(array("exit" => "error", "error" => "The file is empty"));
    exit;
}

//check if the file is too big
if ($csv_file['size'] > 1000000) {
    echo json_encode(array("exit" => "error", "error" => "The file is too big"));
    exit;
}

// parse email, first_name, last_name, class_section, class_number from the csv file
$csv = array_map('str_getcsv', file($csv_file['tmp_name']));
$csv = array_slice($csv, 1); // remove the first row
$csv = array_map(function ($row) {
    //map class section from Classico to cla, Scientifico A to sca, Scientifico B to scb, Scientifico C to scc, Sportivo to spo
    $row[5] = strtolower($row[5]);
    if ($row[5] == "classico") {
        $row[5] = "cla";
    } else if ($row[5] == "scientifico a") {
        $row[5] = "sca";
    } else if ($row[5] == "scientifico b") {
        $row[5] = "scb";
    } else if ($row[5] == "scientifico c") {
        $row[5] = "scc";
    } else if ($row[5] == "sportivo") {
        $row[5] = "spo";
    }
    return array(
        'email' => $row[1],
        'first_name' => $row[2],
        'last_name' => $row[3],
        'class_section' => $row[5],
        'class_number' => $row[4]
    );
}, $csv);

// now insert in the database all the fields specifying in the SQL that if the email is already present, update the other fields
$stmt = $mysqli->prepare("INSERT INTO user_whitelist (email, first_name, last_name, class_section, class_number) VALUES (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE first_name = VALUES(first_name), last_name = VALUES(last_name), class_section = VALUES(class_section), class_number = VALUES(class_number)");
foreach ($csv as $row) {
    $stmt->bind_param("sssss", $row['email'], $row['first_name'], $row['last_name'], $row['class_section'], $row['class_number']);
    $stmt->execute();
}
$stmt->close();

$updates = $mysqli->affected_rows;
$mysqli->close();

echo json_encode(array("exit" => "success", "updates" => $updates));
?>