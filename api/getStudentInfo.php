<?php
//Path: api/getStudentInfo.php
/* MySQL tables
utenti
id, first_name, last_name, email, last_login, date_joined, class_branch, class_section, can_buy_tickets, username

OAuth
id, type, grade, okey, expiration, access_to, commento
*/
/* 
this is an API api/getStudentData.php?
OAuth: 32 chars
then one of the two:
- id: int (id of the student) 
- email
The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp). 
The access_to, which is a JSON, should have the key/permission administration.studenti.getInfo set to true or
any of the roots set to true for example administration.studenti.* or administration.* or *.
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

//get the id or the email
$id = $_GET['id'];
$email = $_GET['email'];

//check if the OAuth token is valid
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
if ($OAuth['grade'] > 1) {
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
if (!isset($access_to['administration.studenti.getInfo']) && !isset($access_to['administration.*']) && !isset($access_to['administration.studenti.*']) && !isset($access_to['*'])) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not allowed to administration.studenti.getList"));
    exit();
}

//check if the id or the email is set
if (!isset($id) && !isset($email)) {
    echo json_encode(array("error" => "id or email not set"));
    exit();
}

//check if the id is set
if (isset($id)) {
    //check if the id is valid
    $stmt = $mysqli->prepare("SELECT * FROM utenti WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(array("error" => "id not valid"));
        exit();
    }
    //get the data
    $data = $result->fetch_assoc();
    //return the data
    echo json_encode(array("exit" => "success", "data" => $data));
    exit();
} else {
    //check if the email is valid
    $stmt = $mysqli->prepare("SELECT * FROM utenti WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        echo json_encode(array("error" => "email not valid"));
        exit();
    }
    //get the data
    $data = $result->fetch_assoc();
    //return the data
    echo json_encode(array("exit" => "success", "data" => $data));
    exit();
}
/*
URL: https://amministrazione.leonapp.it/api/getStudentInfo.php?email=niccolo.pagano@studentilicei.leonexiii.it
example of return:
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
?>