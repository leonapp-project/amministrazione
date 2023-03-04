<!DOCTYPE html>
<html lang="it">

<head>
  <meta charset="UTF-8">
  <title>Errore 404 - Pagina non trovata</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bulma/0.9.3/css/bulma.min.css">
  <script src="https://kit.fontawesome.com/af303d99ee.js" crossorigin="anonymous"></script>
  <style>
    body {
      background-color: #f5f5f5;
      font-family: 'Helvetica Neue', sans-serif;
    }

    .error-message {
      margin-top: 3rem;
      text-align: center;
    }

    .resource-url {
      font-size: 1.2rem;
      color: #666;
      margin-top: 1rem;
    }

    .back-link {
      display: inline-block;
      margin-top: 1rem;
    }

    .back-link a {
      display: flex;
      align-items: center;
      color: #666;
      text-decoration: none;
      transition: color 0.2s ease-in-out;
    }

    .back-link a:hover {
      color: #3273dc;
    }

    .back-link a svg {
      width: 1.2rem;
      height: 1.2rem;
      margin-right: 0.5rem;
    }
  </style>
</head>

<body>
  <section class="hero is-fullheight">
    <div class="hero-body">
      <div class="container has-text-centered">
        <h1 class="title has-text-primary">Errore 404</h1>
        <h2 class="subtitle">Pagina non trovata</h2>
        <div class="error-message">
          <p>La pagina richiesta non può essere trovata.</p>
          <?php if (isset($_SERVER['REQUEST_URI'])): ?>
            <p class="resource-url">L'URL richiesto era:
              <?php echo $_SERVER['REQUEST_URI']; ?>
            </p>
          <?php endif; ?>
          <div class="back-link">
            <a href="/home.php">
              <span class="icon">
                <i class="fas fa-arrow-left"></i>
              </span>

              Torna alla pagina principale
            </a>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>

</html>