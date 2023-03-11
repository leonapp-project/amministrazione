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
      <a class="navbar-item" href="/home.php">
        Area focaccine
      </a>
      <a class="navbar-item" href="/sistema.php">
        Sistema
      </a>
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
