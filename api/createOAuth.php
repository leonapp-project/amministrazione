<?php
//Path: api/createOAuth.php
/* MySQL tables
utenti
id, first_name, last_name, email, last_login, date_joined, class_branch, class_section, can_buy_tickets, username

OAuth
id, type, grade, okey, expiration, access_to, commento

this is an API api/createOAuth.php?
type: string (the type of the OAuth key)
grade: int (the grade of the OAuth key)
expiration: string (the expiration date of the OAuth key)
access_to: json string (the access to of the OAuth key)
commento: string (the comment of the OAuth key)

The OAuth grade must be less than or equal to 0. It must not be expired (expiration is a timestamp).
The access_to, which is a JSON, should have the key/permission administration.OAuth.createKey set to true or
any of the roots set to true for example administration.OAuth.* or administration.* or *.
*/
//import once the database connection

//display errors

require_once 'mysqli.php';
header('Content-Type: application/json');

//get the OAuth token
$OAuth = $_GET['OAuth'];
//if the OAuth token is not set, get it from the cookie
if (!isset($OAuth)) {
    $OAuth = $_COOKIE['OAuth_key'];
}

//get the type
$type = $_GET['type'];
//get the grade
$grade = $_GET['grade'];
//get the expiration
$expiration = $_GET['expiration'];
//get the access_to
$access_to = $_GET['access_to'];
//get the commento
$commento = $_GET['commento'];

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
//check if the access_to is valid
$access_to = json_decode($OAuth['access_to'], true);
if (!isset($access_to['administration.OAuth.createKey']) && !isset($access_to['administration.*']) && !isset($access_to['administration.OAuth.*']) && !isset($access_to['*'])) {
    //the OAuth token is not valid
    echo json_encode(array("exit" => "error", "error" => "OAuth token not allowed to administration.OAuth.createKey"));
    exit();
}

//create the OAuth key
$OAuth_key = bin2hex(random_bytes(16));
//insert the OAuth key
$access_to = $_GET['access_to'];

$stmt = $mysqli->prepare("INSERT INTO OAuth (type, grade, okey, expiration, access_to, commento) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sissss", $type, $grade, $OAuth_key, $expiration, $access_to, $commento);
$stmt->execute();
//check if the OAuth key has been created
if ($stmt->affected_rows === 0) {
    //the OAuth key has not been created
    echo json_encode(array("exit" => "error", "error" => "OAuth key not created"));
    exit();
}
//get the id of the OAuth key
$id = $mysqli->insert_id;
//return the OAuth key
echo json_encode(array("exit" => "success", "id" => $id, "okey" => $OAuth_key));

//Create an example URL:
//http://localhost/api/createOAuth.php?OAuth=1a2b3c4d5e6f7g8h9i0j1k2l3m4n5o6p&type=example&grade=0&expiration=2020-01-01 00:00:00&access_to={"administration.OAuth.createKey":true}&commento=example
?>
