<?php
/* Path: api/utils.php
This has a lot of functions that are used in the administration panel.
utenti
id, first_name, last_name, email, last_login, date_joined, class_number, class_section, can_buy_tickets, username
tickets
id, ticket_code, status, date_created, date_used, block_id, scanned_by
acquisti
id, user_id, date_purchased, ticket_type
consumazioni
id, ticket_code, ticket_type, ticket_id, date_scanned, scanner_id
scanners
id, scan_pin, display_name, count
OAuth
id, type, grade, okey, expiration, access_to, commento
accessi
id, type, timestamp, ip, website, path, unique
scaricabili
id, type, display_name, path, OAuth_type
downloads
id, file_id, OAuth_id, timestamp, ip
abilitazioni
id, user_id, action, administration_OAuth, ip, timestamp, unique
sent_emails
id, type, recipient, extra_data
*/

/* checkOAuthPermissionFor($permission)
Checks if the OAuth key has the permission to do the action.
$permission is like administration.OAuth.create
*/
function checkOAuthPermissionFor($permission, $OAuth = null, $grade = 2)
{
    require 'mysqli.php';
    global $mysqli;
    //retrieve the OAuth from GET or POST
    if (isset($_GET['OAuth']) && empty($OAuth)) {
        $OAuth = $_GET['OAuth'];
    } else {
        $OAuth = $_COOKIE['OAuth_key'];
    }
    //check if the OAuth key is valid
    $stmt = $mysqli->prepare("SELECT * FROM OAuth WHERE okey = ?");
    $stmt->bind_param("s", $OAuth);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 0) {
        return false;
    }
    //check for the grade
    $row = $result->fetch_assoc();
    if ($row['grade'] > $grade) {
        return false;
    }
    //check if expired
    if (time() > strtotime($row['expiration'])) {
        return false;
    }
    //check if the OAuth key has the permission to do the action
    //or any of the permission's roots like administration.OAuth.* or administration.* or *
    $access_to = json_decode($row['access_to'], true);
    //check if the base is present, example administration.OAuth.view
    if (isset($access_to[$permission]) && $access_to[$permission] == true) {
        return true;
    }
    //now check the parent, example administration.OAuth.*
    $permission = explode(".", $permission);
    $permission = $permission[0] . "." . $permission[1] . ".*";
    if (isset($access_to[$permission]) && $access_to[$permission] == true) {
        return true;
    }
    //now check the parent, example administration.*
    $permission = explode(".", $permission);
    $permission = $permission[0] . ".*";
    if (isset($access_to[$permission]) && $access_to[$permission] == true) {
        return true;
    }
    //now check the parent, example *
    $permission = "*";
    if (isset($access_to[$permission]) && $access_to[$permission] == true) {
        return true;
    }
    return false;
}