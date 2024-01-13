<?php
//errors
$OAuth = $_COOKIE['OAuth_key'];
//check if the OAuth token is valid
require_once 'mysqli.php';
$stmt = $mysqli->prepare("SELECT * FROM OAuth WHERE okey = ?");
$stmt->bind_param("s", $OAuth);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
  //the OAuth token is not valid
  header("Location: index.php");
  exit();
}
//the OAuth token is valid
$OAuth = $result->fetch_assoc();
if ($OAuth['grade'] > 1) {
  //the OAuth token is not valid
  header("Location: index.php");
  exit();
}
//check if the current timestamp is greater than the expiration timestamp
if (time() > strtotime($OAuth['expiration'])) {
  //the OAuth token is not valid
  header("Location: index.php");
  exit();
}
//now check if the OAuth satifsfies every permission needed
$access_to = json_decode($OAuth['access_to'], true);
function checkOAuthPermissionFor($permission, $OAuth = null, $grade=2) {
  require 'mysqli.php';
  global $mysqli;
  //retrieve the OAuth from GET or POST
  if(isset($_GET['OAuth']) && empty($OAuth)) {
      $OAuth = $_GET['OAuth'];
  } else {
      $OAuth = $_COOKIE['OAuth_key'];
  }
  //check if the OAuth key is valid
  $stmt = $mysqli->prepare("SELECT * FROM OAuth WHERE okey = ?");
  $stmt->bind_param("s", $OAuth);
  $stmt->execute();
  $result = $stmt->get_result();
  if($result->num_rows == 0) {
      return false;
  }
  //check for the grade
  $row = $result->fetch_assoc();
  if($row['grade'] > $grade) {
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
?>

<style>
  @media screen and (max-width: 1023px) {
    .navbar-center {
      text-align: center;
    }

    .navbar-right {
      margin-left: auto;
    }
  }
</style>
<!-- Navbar -->
<nav class="navbar is-transparent">
  <div class="navbar-brand">
    <a class="navbar-item" href="#">
      LEONAPP - AMMINISTRAZIONE
    </a>
    <div class="navbar-burger burger" data-target="navbarExampleTransparentExample">
      <span></span>
      <span></span>
      <span></span>
    </div>
  </div>

  <div id="navbarExampleTransparentExample" class="navbar-menu">
    <div class="navbar-center navbar-start">
      <?php
      $permissions_needed = "administration.view.home";
      if (!checkOAuthPermissionFor($permissions_needed)) {
      } else {
        echo '<a class="navbar-item" href="/home.php">
        Focaccine
      </a>';
      }

      $permissions_needed = "administration.view.sistema";
      if (!checkOAuthPermissionFor($permissions_needed)) {
      } else {
        echo '
      <a class="navbar-item" href="/sistema.php">
        Gestione
      </a>
      ';
      }
      ?>
    </div>


    <div class="navbar-right navbar-end">
      <div class="navbar-item">
        <div class="buttons">
          <a class="button is-danger" href="/logout.php">
            <span class="icon">
              <i class="fas fa-sign-out-alt"></i>
            </span>
            <span>Log out</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</nav>

<script>
  // Get all "navbar-burger" elements
  const navbarBurgers = Array.from(document.querySelectorAll('.navbar-burger'));

  // Add a click event listener to each "navbar-burger" element
  navbarBurgers.forEach((navbarBurger) => {
    navbarBurger.addEventListener('click', () => {
      // Toggle the "is-active" class on both the "navbar-burger" and the "navbar-menu"
      navbarBurger.classList.toggle('is-active');
      const navbarMenu = document.getElementById(navbarBurger.dataset.target);
      navbarMenu.classList.toggle('is-active');
    });
  });
</script>