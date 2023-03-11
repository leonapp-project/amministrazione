<?php
// Path: logout.php

//to logout, just remove the cookie OAUTH_key 
setcookie("OAuth_key", "", time()-3600);

//redirect to index page
header("Location: index.php");

?>