<style>
  /* Style spécifique pour le bouton de connexion dans le header */
  .btn-login {
    background: #00acac;
    color: #fff;
    padding: 8px 25px;
    margin-left: 20px;
    border-radius: 50px;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s;
    text-decoration: none;
    white-space: nowrap;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .btn-login:hover {
    background: #008a8a;
    color: #fff;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0, 172, 172, 0.3);
    transform: translateY(-1px);
  }

  /* Ajustement pour les petits écrans (tablettes et mobiles) */
  @media (max-width: 1200px) {
    .btn-login {
      margin-left: 10px;
      padding: 6px 15px;
      font-size: 13px;
    }
  }

  @media (max-width: 575px) {
    .btn-login {
      padding: 5px 12px;
      font-size: 12px;
    }
    .sitename {
      font-size: 20px;
    }
  }
</style>

<header id="header" class="header d-flex align-items-center position-relative">
  <div class="container-fluid position-relative d-flex align-items-center justify-content-between">

    <a href="index.php" class="logo d-flex align-items-center">
      <h1 class="sitename">saticket</h1>
    </a>

    <nav id="navmenu" class="navmenu">
      <ul>
        <li><a href="#hero" class="active">Accueil</a></li>
        <li><a href="#about">À propos</a></li>
        <li><a href="#services">Services</a></li>
        <li><a href="#features">Fonctionnalités</a></li>
        <li><a href="#team">Équipe</a></li>
        <li class="menu"><a href="#"><span>Événements</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
          <ul>
            <li><a href="#">Concerts</a></li>
            <li class="menu"><a href="#"><span>Sports</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
              <ul>
                <li><a href="#">Lutte traditionnelle</a></li>
                <li><a href="#">Football</a></li>
                <li><a href="#">Boxe</a></li>
                <li><a href="#">Arts martiaux</a></li>
                <li><a href="#">Autres sports</a></li>
              </ul>
            </li>
            <li><a href="#">Soirées & Galas</a></li>
            <li><a href="#">Festivals</a></li>
            <li><a href="#">Conférences</a></li>
          </ul>
        </li>
        <li><a href="#contact">Contact</a></li>
      </ul>
      <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
    </nav>

    <!-- Bouton Se Connecter -->
    <a href="/saticket/login.php" class="btn-login">
      Se connecter
    </a>

  </div>
</header>