<?php
//Connect to database www, user leonapp, password "" using stmt
$mysqli = new mysqli("localhost", "leonapp", "", "www");
if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}
?>