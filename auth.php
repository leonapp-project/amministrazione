<?php
// Path: auth.php
/*
accessi
id, type, timestamp, ip, website, path, unique
OAuth
id, type, grade, key, expiration, access_to, commento

*/
ini_set('display_errors', -1);

if ( !isset($_POST['password']) ) {
    setcookie('error', 'void', time() + 3600, '/');
    header('Location: index.php');
    exit('Please fill both the username and password fields!');
}

$master_password = "test";

if($_POST['password'] !== $master_password) {
    setcookie('error', 'incorrect', time() + 3600, '/');
    header('Location: index.php');
    exit('Incorrect password!');
}

//now log the login to the table accessi
require_once('utils.php');

checkSetUnique();

//if the OAuth key is not set, generate a new one
$to_set = false;
if (!isset($_COOKIE['OAuth_key'])) {
    $OAuth_key = generateRandomSessionID();
    setcookie("OAuth_key", $OAuth_key, time() + (86400 * 30), "/");
    $_COOKIE['OAuth_key'] = $OAuth_key;
    $to_set = true;
}
$OAuth_key = $_COOKIE['OAuth_key'];
//insert the OAuth key in the database using insertOAuthKey()
//but before set all the varuiables learyly
$type = "administration";
$grade = 0;
$expiration = date("Y-m-d H:i:s", strtotime("+1 month"));

//make a dictionary access_to_dict with key "administration_generic" and value "administration_generic"
$access_to_dict = array("administration.*" => True);
$access_to = json_encode($access_to_dict);

if($to_set == true) {
    $commento = "Nessun commento.";
    insertOAuthKey($type, $grade, $OAuth_key, $expiration, $access_to, $commento);
} else {
    renewOAuthKey($OAuth_key, $expiration);
}

logLogin();

header('Location: home.php');
exit('Logged in!');
?>