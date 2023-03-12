<?php

error_reporting(-1);
ini_set('display_errors', 'On');
/* DATABASES STRUCTURES

utenti
id, mail, nome, cognome, sessionid, ultimo_accesso, classe, sezione, indirizzo, is_abilitato

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
id, user_id, action, administration_OAuth, ip, timestamp, uniq

sent_emails
id, type, recipient, extra_data

*/

//insert OAuth key
require_once 'mysqli.php';
global $mysqli;

function insertOAuthKey($type, $grade, $okey, $expiration, $access_to, $commento) {
    echo "type: $type, grade: $grade, okey: $okey, expiration: $expiration, access_to: $access_to, commento: $commento";
    require_once 'mysqli.php';
    global $mysqli;
    $stmt = $mysqli->prepare("INSERT INTO OAuth (type, grade, okey, expiration, access_to, commento) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $type, $grade, $okey, $expiration, $access_to, $commento);
    $stmt->execute();
}
//given the okey, update the expiration
function renewOAuthKey($okey, $expiration) {
    require_once 'mysqli.php';
    global $mysqli;
    $stmt = $mysqli->prepare("UPDATE OAuth SET expiration = ? WHERE okey = ?");
    $stmt->bind_param("ss", $expiration, $okey);
    $stmt->execute();
}
function logLogin() {
    require_once 'mysqli.php';
    global $mysqli;
    if (isset($_COOKIE['OAuth_key'])) {
        $stmt = $mysqli->prepare("INSERT INTO accessi (type, timestamp, ip, website, path, auth_key, uniq) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $type, $timestamp, $ip, $website, $path, $auth_key, $unique);
        $type = "administration_login";
        $timestamp = date("Y-m-d H:i:s");
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        echo $ip;
        $website = $_SERVER['SERVER_NAME'];
        $path = $_SERVER['REQUEST_URI'];
        $unique = $_COOKIE['unique'];
        $auth_key = $_COOKIE['OAuth_key'];
        $stmt->execute();
    }
}

function generateRandomSessionID() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < 32; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function checkAuthentication() {
    if (isLoggedIn()) {
        //updateLastLogin();
    } else {
        header("Location: https://www.leonapp.it");
        exit();
    }
}

//make a function checkSetUnique() that checks if the unique cookie is set, if not set it to a random string
function checkSetUnique() {
    if (!isset($_COOKIE['unique'])) {
        $unique = generateRandomSessionID();
        //set the cookie for also the main domain leonapp.it
        setcookie("unique", $unique, time() + (86400 * 30), "/", ".leonapp.it");
        //setcookie("unique", $unique, time() + (86400 * 30), "/");
        $_COOKIE['unique'] = $unique;
    }
}

?>