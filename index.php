<?php
if ($_SERVER["HTTPS"] != "on") {
    header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
    exit();
}
require_once('utils.php');
checkSetUnique();
?>
<!DOCTYPE html>
<html lang="it">
<head>
  <meta charset="UTF-8">
  <title>Leonapp - Amministrazione</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.3/css/bulma.min.css">
  <style>
    body {
      background: linear-gradient(rgba(0, 0, 0, 0.2), rgba(0, 0, 0, 0.2)), url('src/img/lake-mountains.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: 'Helvetica Neue', sans-serif;
    }
    .title {
      font-size: 4rem;
      text-align: center;
      margin-top: 2rem;
      position: relative;
    }
    .title:before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: -1;
      background-color: rgba(255, 255, 255, 0.8);
      mix-blend-mode: multiply;
      transform: skewX(-15deg);
    }
    .subtitle {
      font-size: 2rem;
      text-align: center;
      margin-bottom: 3rem;
      position: relative;
      color: white;
    }
    .subtitle:before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      z-index: -1;
      background-color: rgba(255, 255, 255, 0.8);
      mix-blend-mode: multiply;
      transform: skewX(-15deg);
    }
    .login-box {
      background-color: #fff;
      border-radius: 10px;
      padding: 2rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.15);
      max-width: 400px;
      margin: 0 auto;
    }
    .login-button {
      border-radius: 25px;
    }
    #wrong-password-notification {
      display: none;
      position: fixed;
      bottom: 2rem;
      right: 2rem;
    }
    
  </style>
</head>
<body>
  <section class="hero is-fullheight">
    <div class="hero-body">
      <div class="container has-text-centered">
        <h1 class="title has-text-primary">Leonapp</h1>
        
        <h2 class="subtitle">Amministrazione</h2>
        <div class="login-box">
          <form method="post" action="auth.php">
          <div class="field">
              <label class="label">Username</label>
              <div class="control">
                <input class="input login-button" type="text" name="username" required>
              </div>
            </div>
            <div class="field">
              <label class="label">Password</label>
              <div class="control">
                <input class="input login-button" type="password" name="password" required>
              </div>
            </div>
            <div class="field">
              <div class="control">
                <button class="button is-primary is-fullwidth login-button">Login</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </section>
  <div id="wrong-password-notification" class="notification is-danger">
    <button class="delete"></button>
    Errore: la password inserita &egrave; errata. 
  </div>
  <?php
    if (isset($_COOKIE['error'])) {
        echo '<script>document.querySelector("#wrong-password-notification").style.display = "block";</script>';
    }
    //remove the cookie
    setcookie('error', '', time() - 3600, '/');
    ?>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const notification = document.querySelector('#wrong-password-notification');
      setTimeout(() => {
        notification.style.display = 'none';
      }, 10000);
      document.querySelector('.delete').addEventListener('click', function() {
        notification.style.display = 'none';
      });
    });
  </script>

</body>
</html>
