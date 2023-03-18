<?php
// Path: auth.php
/*
accessi
id, type, timestamp, ip, website, path, unique
OAuth
id, type, grade, key, expiration, access_to, commento
OAuth_map
id, OAuth, email, username, password
*/
ini_set('display_errors', -1);

require_once('utils.php');
checkSetUnique();

$username = $_POST['username'];
$password = $_POST['password'];

if (!isset($username) || !isset($password)) {
    setcookie('error', 'blank fields!', time() + 3600, '/');
    header('Location: index.php');
    exit('Incorrect password!');
}
//password is password sha256
$password = hash('sha256', $password.$username);

require_once('mysqli.php');
//check if the user exists
$sql = "SELECT OAuth FROM OAuth_map WHERE username = ? AND password = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("ss", $username, $password);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    setcookie('error', 'incorrect password!', time() + 3600, '/');
    header('Location: index.php');
    exit('Incorrect password!');
}
$row = $result->fetch_assoc();
$OAuth = $row['OAuth'];

//check if the OAuth key is valid
$sql = "SELECT * FROM OAuth WHERE okey = ?";
$stmt = $mysqli->prepare($sql);
$stmt->bind_param("s", $OAuth);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 0) {
    setcookie('error', 'OAuth key not valid!', time() + 3600, '/');
    header('Location: index.php');
    exit('OAuth key not valid!');
}
$row = $result->fetch_assoc();
$grade = $row['grade'];
$expiration = $row['expiration'];
$access_to = $row['access_to'];
$access_to = json_decode($access_to, true);

//check if the OAuth key is expired
if ($expiration < time()) {
    setcookie('error', 'OAuth key expired!', time() + 3600, '/');
    header('Location: index.php');
    exit('OAuth key expired!');
}

//set the cookie OAuth_key to OAuth
setcookie('OAuth_key', $OAuth, time() + 3600, '/');

logLogin();

header('Location: home.php');
exit('Logged in!');
?>