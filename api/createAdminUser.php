<?php
/* Path: api/createAdminUser.php
MySQL tables
utenti
id, first_name, last_name, email, last_login, date_joined, class_branch, class_section, can_buy_tickets, username
OAuth
id, type, grade, okey, expiration, access_to, commento
OAuth_map
id, OAuth, email, username, password

This api creates a new admin user.
OAuth: 32 chars
email: string (optional)
username: string (optional)
password: string (optional)

The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp).
The access_to, which is a JSON, should have the key/permission administration.admins.create set to true or

any of the roots set to true for example administration.admins.* or administration.* or *.
*/
//import once the database connection
require_once 'mysqli.php';
header('Content-Type: application/json');

//check the permissiojn using utils
require_once 'utils.php';
if (!checkOAuthPermissionFor("administration.admins.create")) {
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}

//get the parameters
$email = $_REQUEST['email'];
$username = $_REQUEST['username'];
$password = $_REQUEST['password'];

//if username is not set, set it to a random name
if (!isset($username)) {
    $username = "admin_" . rand(100000, 999999);
}

//if password is not set, set it to a random password
if (!isset($password)) {
    $password = rand(100000, 999999);
}

//set password to sha256(password + username)


//check if the email is valid
if (isset($email)) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(array("exit" => "error", "error" => "The email is not valid"));
        exit();
    }
}

//check if the username is valid
if (isset($username)) {
    if (!preg_match("/^[a-zA-Z0-9_]{1,32}$/", $username)) {
        echo json_encode(array("exit" => "error", "error" => "The username is not valid"));
        exit();
    }
}

//check if the password is valid
if (isset($password)) {
    if (!preg_match("/^[a-zA-Z0-9_]{1,32}$/", $password)) {
        echo json_encode(array("exit" => "error", "error" => "The password is not valid"));
        exit();
    }
}

//check if the email is already in use
if (isset($email)) {
    $sql = "SELECT * FROM OAuth_map WHERE email = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(array("exit" => "error", "error" => "The email is already in use"));
        exit();
    }
}

//check if the username is already in use
if(isset($username)) {
    $sql = "SELECT * FROM OAuth_map WHERE username = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        echo json_encode(array("exit" => "error", "error" => "The username is already in use"));
        exit();
    }
}
$password = hash("sha256", $password . $username);

//generate a random OAuth key which is a random string of 32 chars
$oauth = bin2hex(random_bytes(16));

//create the user
$sql = "INSERT INTO OAuth_map (email, username, password, OAuth) VALUES (?, ?, ?, ?)";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ssss", $email, $username, $password, $oauth);
$stmt->execute();

//return the OAuth key
echo json_encode(array("exit" => "success", "data" => array("email" => $email, "username" => $username, "password" => $password)));
exit();
?>
