<?php
/*
// Path: api/enableBuyTickets.php
// Compare this snippet from api/getStudentList.php:
//

This snippet will change the can_buy_tickets value of the student with the id passed in the URL to the value passed in the URL.

*/
//import once the database connection
require_once 'mysqli.php';
header('Content-Type: application/json');

//get the OAuth token
$OAuth = $_GET['OAuth'];
//if the OAuth token is not set, get it from the cookie
if (!isset($OAuth)) {
    $OAuth = $_COOKIE['OAuth_key'];
}

//get the student id
$student_id = $_GET['student_id'];
//get the value to set
$can_buy_tickets = $_GET['can_buy_tickets'];

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
if ($OAuth['grade'] > 0) {
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

//check if the access_to is valid, if it has the permission of administration.students.enableBuyTickets or administration.* or *
$access_to = json_decode($OAuth['access_to'], true);
if (!isset($access_to['administration.students.enableBuyTickets']) && !isset($access_to['administration.studenti.*']) && !isset($access_to['administration.*']) && !isset($access_to['*'])) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not allowed to administration.students.enableBuyTickets"));
    exit();
}

//check if the student id is valid
$stmt = $mysqli->prepare("SELECT * FROM utenti WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    //the student id is not valid
    echo json_encode(array("exit" => "error", "error" => "Student id not valid"));
    exit();
}

//check if the value to set is valid
if ($can_buy_tickets !== "true" && $can_buy_tickets !== "false") {
    //the value to set is not valid
    echo json_encode(array("exit" => "error", "error" => "Value to set not valid"));
    exit();
}
//convert the value to set to an integer
$can_buy_tickets = $can_buy_tickets === "true" ? 1 : 0;

//set the value
$stmt = $mysqli->prepare("UPDATE utenti SET can_buy_tickets = ? WHERE id = ?");
$stmt->bind_param("ii", $can_buy_tickets, $student_id);
$stmt->execute();

//return the success message
echo json_encode(array("exit" => "success", "message" => "Student can_buy_tickets value changed"));
exit();
?>
