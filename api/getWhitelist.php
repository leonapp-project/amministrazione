<?php
/* Path: api/getWhitelist.php
MySQL tables
user_whitelist
id, email, first_name, last_name, class_section, class_number
OAuth
id, type, grade, okey, expiration, access_to, commento

This is an API to get the full whitelist.
OAuth: 32 chars
The OAuth grade must be less than or equal to 1. It must not be expired (expiration is a timestamp).
The access_to, which is a JSON, should have the key/permission administration.studenti.getWhitelist set to true or
any of the roots set to true for example administration.studenti.* or administration.* or *.
*/

require_once 'mysqli.php';
header('Content-Type: application/json');

require_once 'utils.php';
if(!checkOAuthPermissionFor("administration.studenti.getWhitelist")) {
    echo json_encode(array("exit" => "error", "error" => "OAuth token not valid"));
    exit();
}

//retrieve the whitelist from the database
$sql = "SELECT email, first_name, last_name, class_section, class_number FROM user_whitelist";
$stmt = $mysqli->prepare($sql);
$stmt->execute();
$result = $stmt->get_result();

//return the whitelist
echo json_encode(array("exit" => "success", "data" => $result->fetch_all(MYSQLI_ASSOC)));
exit();

/*
example URL call below
https://www.studentilicei.it/api/getWhitelist.php

example return below
{"exit":"success","data":[{"email":"niccolo.pagano@studentilicei.leonexiii.it","first_name":"Niccolo","last_name":"Pagano","class_section":"cla","class_number":3}]}
*/
?>