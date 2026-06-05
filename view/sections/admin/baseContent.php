<div id="sidebar" class="sidebar">
    <div data-scrollbar="true" data-height="100%">
        <ul class="nav">
            <li class="nav-profile">
                <a href="javascript:;" data-toggle="nav-profile">
                    <div class="cover with-shadow"></div>
                    <div class="image">
                        <img src="../assets/img/user/user-13.jpg" alt="" />
                    </div>
                    <div class="info">
                        <b class="caret pull-right"></b><?php echo isset($_SESSION['nom']) ? htmlspecialchars($_SESSION['nom']) : 'Utilisateur'; ?>
                        <small><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : 'Administrateur SaTicket'; ?></small>
                    </div>
                </a>
            </li>
            <li>
                <ul class="nav nav-profile">
                   
                    <li><a href="logout.php" class="text-danger"><i class="fa fa-sign-out-alt"></i> Déconnexion</a></li>
                </ul>
            </li>
        </ul>
        <ul class="nav">
            <li class="nav-header">Navigation principale</li>
            <li class="has-sub active">
                <a href="javascript:;">
                    <b class="caret"></b>
                    <i class="fa fa-th-large"></i>
                    <span>Tableau de Bord</span>
                </a>
               
            </li>
            <li class="has-sub">
                <a href="javascript:;">
                    <span class="badge pull-right">10</span>
                    <i class="fa fa-calendar-check"></i>
                    <span>Événements</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="listeEvenements">Tous les événements</a></li>
                    <li><a href="ajoutEvenements">Ajouter un événement</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="javascript:;">
                    <b class="caret"></b>
                    <i class="fa fa-ticket-alt"></i>
                    <span>Billetterie</span> 
                </a>
                <ul class="sub-menu">
                    <li><a href="listeTickets">Tous les tickets</a></li>
                    <li><a href="ajoutTickets">Nouveau ticket</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="javascript:;">
                    <b class="caret"></b>
                    <i class="fa fa-shopping-cart"></i>
                    <span>Commandes</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="listeCommandes">Toutes les commandes</a></li>
                    <li><a href="ajoutCommandes">Nouvelle commande</a></li>
                </ul>
            </li>
            <li>
                <a href="clients.php">
                    <div class="icon-img">
                        <img src="../assets/img/logo/logo-bs4.png" alt="" />
                    </div>
                   
                </a>
            </li>
            <li class="has-sub">
                <a href="javascript:;">
                    <b class="caret"></b>
                    <i class="fa fa-users"></i>
                    <span>Utilisateurs</span> 
                </a>
                <ul class="sub-menu">
                    <li><a href="listeUtilisateurs">Liste des utilisateurs</a></li>
                    <li><a href="ajoutUtilisateurs">Ajouter un utilisateur</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="javascript:;">
                    <b class="caret"></b>
                    <i class="fa fa-money-bill-wave"></i>
                    <span>Paiements</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="listePaiement">Tous les paiements</a></li>
                    <li><a href="ajoutPaiement">Nouveau paiement</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="javascript:;">
                    <b class="caret"></b>
                    <i class="fa fa-gift"></i>
                    <span>Promotions</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="listePromotions">Toutes les promotions</a></li>
                    <li><a href="ajoutPromotions">Nouvelle promotion</a></li>
                </ul>
            </li>
            <li class="has-sub">
                <a href="javascript:;">
                    <b class="caret"></b>
                    <i class="fa fa-comments"></i>
                    <span>Commentaires</span>
                </a>
                <ul class="sub-menu">
                    <li><a href="listeCommentaire">Tous les commentaires</a></li>
                    <li><a href="modererCommentaire">Modération</a></li>
                </ul>
            </li>
          
            <li class="has-sub">
                <a href="javascript:;">
                    <b class="caret"></b>
                    <i class="fa fa-key"></i>
                    
                </a>
                <ul class="sub-menu">
                    <li><a href="logout.php">Déconnexion</a></li>
                    <li><a href="lock_screen.php">Verrouiller l'écran</a></li>
                </ul>
            </li>
            <li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
        </ul>
        </div>
</div>

<div class="sidebar-bg"></div>
<div id="content" class="content">
    <ol class="breadcrumb float-xl-right">
        <li class="breadcrumb-item"><a href="javascript:;">Accueil</a></li>
        <li class="breadcrumb-item"><a href="javascript:;">Tableau de Bord</a></li>
        <li class="breadcrumb-item active">Statistiques SaTicket</li>
        
    </ol>
    <h1 class="page-header mb-3">Tableau de Bord SaTicket</h1>

    <div class="d-sm-flex align-items-center mb-3">
        <a href="#" class="btn btn-inverse mr-2 text-truncate" id="daterange-filter">
            <i class="fa fa-calendar fa-fw text-white-transparent-5 ml-n1"></i> 
            <span>Aujourd'hui, <?php echo date('d M Y'); ?></span>
            <b class="caret"></b>
        </a>
        <div class="text-muted f-w-600 mt-2 mt-sm-0">comparé à <span id="daterange-prev-date">la semaine dernière</span></div>
    </div>

    <div class="row">
        <div class="col-xl-6">
            <div class="card border-0 mb-3 overflow-hidden bg-dark text-white">
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-7 col-lg-8">
                            <div class="mb-3 text-grey"><b>CHIFFRE D'AFFAIRES TOTAL</b></div>
                            <div class="d-flex mb-1">
                                <h2 class="mb-0"><span data-animation="number" data-value="12455900">0</span> FCFA</h2>
                            </div>
                            <div class="mb-3 text-grey"><i class="fa fa-caret-up"></i> 15.21% vs dernier événement</div>
                            <hr class="bg-white-transparent-2" />
                            <div class="row text-truncate">
                                <div class="col-6">
                                    <div class="f-s-12 text-grey">Tickets vendus</div>
                                    <div class="f-s-18 m-b-5 f-w-600">5,420</div>
                                    <div class="progress progress-xs bg-dark-darker"><div class="progress-bar bg-teal" style="width: 75%"></div></div>
                                </div>
                                <div class="col-6">
                                    <div class="f-s-12 text-grey">Panier moyen</div>
                                    <div class="f-s-18 m-b-5 f-w-600">2,500 FCFA</div>
                                    <div class="progress progress-xs bg-dark-darker"><div class="progress-bar" style="width: 55%"></div></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-5 col-lg-4 align-items-center d-flex justify-content-center">
                            <img src="../assets/img/svg/img-1.svg" height="150px" class="d-none d-lg-block" />
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6">
            <div class="row">
                <div class="col-sm-6">
                    <div class="card border-0 mb-3 bg-dark text-white">
                        <div class="card-body">
                            <div class="mb-3 text-grey"><b>TAUX DE REMPLISSAGE</b></div>
                            <h2 class="text-white mb-0">68.5%</h2>
                            <div class="mb-4 text-grey"><i class="fa fa-caret-up"></i> 4.5% vs mois dernier</div>
                            <div class="d-flex mb-2">
                                <i class="fa fa-circle text-red f-s-8 mr-2"></i> VIP <span class="ml-auto f-w-600">95.2%</span>
                            </div>
                            <div class="d-flex">
                                <i class="fa fa-circle text-warning f-s-8 mr-2"></i> Annulations <span class="ml-auto f-w-600">1.2%</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card border-0 mb-3 bg-dark text-white">
                        <div class="card-body">
                            <div class="mb-3 text-grey"><b>PAIEMENTS MOBILE</b></div>
                            <h2 class="text-white mb-0">Orange/Wave</h2>
                            <div class="mb-4 text-grey">85% des transactions</div>
                            <div class="d-flex mb-2">
                                <i class="fa fa-circle text-teal f-s-8 mr-2"></i> Orange Money <span class="ml-auto f-w-600">45%</span>
                            </div>
                            <div class="d-flex">
                                <i class="fa fa-circle text-blue f-s-8 mr-2"></i> Wave <span class="ml-auto f-w-600">40%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-8 col-lg-6">
            <div class="card border-0 mb-3 bg-dark text-white">
                <div class="card-body">
                    <div class="mb-3 text-grey"><b>ANALYSE DES SPECTATEURS (SCAN QR)</b></div>
                    <div class="row">
                        <div class="col-4"><h3>12.5K</h3><div class="text-grey f-s-11">Nouveaux Clients</div></div>
                        <div class="col-4"><h3>8.2K</h3><div class="text-grey f-s-11">Présents au Stade</div></div>
                        <div class="col-4"><h3>45K</h3><div class="text-grey f-s-11">Consultations App</div></div>
                    </div>
                </div>
                <div class="card-body p-0"><div id="visitors-line-chart" style="height: 269px"></div></div>
            </div>
        </div>
        <div class="col-xl-4 col-lg-6">
            <div class="card border-0 mb-3 bg-dark text-white">
                <div class="card-body">
                    <div class="mb-2 text-grey"><b>VENTES PAR RÉGION (SÉNÉGAL)</b></div>
                    <div id="visitors-map" class="mb-2" style="height: 200px"></div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="mr-2">Dakar</div><div class="ml-auto text-grey">65.8%</div>
                    </div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="mr-2">Thiès / Mbour</div><div class="ml-auto text-grey">14.2%</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="mr-2">Autres</div><div class="ml-auto text-grey">20%</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4">
            <div class="card border-0 mb-3 bg-dark-darker text-white">
                <div class="card-body">
                    <div class="mb-3 text-grey"><b>RESEAUX SOCIAUX</b></div>
                    <h3>8,554,780 FCFA</h3>
                    <div class="text-grey mb-3">Ventes générées</div>
                    <div class="widget-list inverse-mode">
                        <div class="widget-list-item">
                            <i class="fab fa-facebook-f bg-blue text-white p-2 mr-3"></i> Facebook Ads <span class="ml-auto">4.5M</span>
                        </div>
                        <div class="widget-list-item">
                            <i class="fab fa-whatsapp bg-success text-white p-2 mr-3"></i> WhatsApp <span class="ml-auto">1.7M</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 mb-3 bg-dark text-white">
                <div class="card-body">
                    <div class="mb-3 text-grey"><b>TOP ÉVÉNEMENTS</b></div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="mr-3"><b>1</b></div>
                        <div>Lutte : Grand Combat<br/><small class="text-grey">Stade L.S. Senghor</small></div>
                        <div class="ml-auto text-center">12.5K<br/><small>tickets</small></div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="mr-3"><b>2</b></div>
                        <div>Concert Wally Seck<br/><small class="text-grey">Dakar Arena</small></div>
                        <div class="ml-auto text-center">9.5K<br/><small>tickets</small></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card border-0 mb-3 bg-dark text-white">
                <div class="card-body">
                    <div class="mb-3 text-grey"><b>CAMPAGNE SMS</b></div>
                    <div class="row align-items-center">
                        <div class="col-4"><img src="../assets/img/svg/img-2.svg" class="mw-100" /></div>
                        <div class="col-8">
                            <div class="text-truncate">Alerte Combat National</div>
                            <div class="progress progress-xs my-2"><div class="progress-bar bg-indigo" style="width: 85%"></div></div>
                            <small class="text-grey">Taux de clic: 57.5%</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
