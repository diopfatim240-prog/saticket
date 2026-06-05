<div id="sidebar" class="sidebar">
    <div data-scrollbar="true" data-height="100%">
        <ul class="nav">
            <li class="nav-profile">
                <a href="javascript:;" data-toggle="nav-profile">
                    <div class="cover with-shadow"></div>
                    <div class="image"><img src="../assets/img/user/user-13.jpg" alt="" /></div>
                    <div class="info">
                        <b class="caret pull-right"></b>
                        <?php echo isset($_SESSION['nom']) ? htmlspecialchars($_SESSION['nom']) : 'Utilisateur'; ?>
                        <small><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?></small>
                    </div>
                </a>
            </li>
            <li>
                <ul class="nav nav-profile">
                    <li><a href="#"><i class="fa fa-user"></i> Mon Profil</a></li>
                    <li><a href="/saticket/controller/UtilisateursController.php?logout=1" class="text-danger">
                        <i class="fa fa-sign-out-alt"></i> Déconnexion
                    </a></li>
                </ul>
            </li>
        </ul>

        <ul class="nav">
            <li class="nav-header">Navigation principale</li>

            <?php $role = isset($_SESSION['role']) ? strtolower(trim($_SESSION['role'])) : ''; ?>

            <!-- ══════════ ADMIN ══════════ -->
            <?php if ($role === 'admin'): ?>

                <li>
                    <a href="/saticket/admin.php">
                        <i class="fa fa-tachometer"></i>
                        <span>Tableau de bord</span>
                    </a>
                </li>

                <li class="has-sub">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-users"></i>
                        <span>Utilisateurs</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="/saticket/view/pages/admin/utilisateurs/liste.php">Liste des utilisateurs</a></li>
                    </ul>
                </li>

                <li class="has-sub">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-calendar"></i>
                        <span>Événements</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="/saticket/view/pages/admin/evenements/liste.php">📅 Liste des événements</a></li>
                    </ul>
                </li>

                <li class="has-sub">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-barcode"></i>
                        <span>Billets / Tickets</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="/saticket/view/pages/admin/tickets/liste.php">🎫 Liste des billets</a></li>
                    </ul>
                </li>

                <li class="has-sub">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-shopping-cart"></i>
                        <span>Commandes</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="/saticket/view/pages/admin/commandes/liste.php">🛒 Toutes les commandes</a></li>
                    </ul>
                </li>

                <li class="has-sub">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-percent"></i>
                        <span>Promotions</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="/saticket/view/pages/admin/promotions/liste.php">🏷 Liste des promotions</a></li>
                    </ul>
                </li>

                <li class="has-sub">
                    <a href="javascript:;">
                        <b class="caret"></b>
                        <i class="fa fa-credit-card"></i>
                        <span>Paiements</span>
                    </a>
                    <ul class="sub-menu">
                        <li><a href="/saticket/view/pages/admin/paiement/liste.php">💳 Liste des paiements</a></li>
                    </ul>
                </li>

                <li>
                    <a href="/saticket/view/pages/admin/commentaire/liste.php">
                        <i class="fa fa-comments"></i>
                        <span>Commentaires</span>
                    </a>
                </li>

            <?php endif; ?>

            <!-- ══════════ ORGANISATEUR ══════════ -->
            <?php if ($role === 'organisateur'): ?>

                <li>
                    <a href="/saticket/view/pages/organisateur/mes_evenements.php">
                        <i class="fa fa-calendar"></i>
                        <span>Mes Événements</span>
                    </a>
                </li>

                <li>
                    <a href="/saticket/view/pages/organisateur/mes_commentaires.php">
                        <i class="fa fa-comments"></i>
                        <span>Mes Commentaires</span>
                    </a>
                </li>

            <?php endif; ?>

            <!-- ══════════ ACHETEUR ══════════ -->
            <?php if ($role === 'acheteur'): ?>

                <li>
                    <a href="/saticket/view/pages/acheteur/parcourir_evenements.php">
                        <i class="fa fa-search"></i>
                        <span>Découvrir Événements</span>
                    </a>
                </li>

                <li>
                    <a href="/saticket/view/pages/acheteur/mes_achats.php">
                        <i class="fa fa-barcode"></i>
                        <span>Mes Billets 🎫</span>
                    </a>
                </li>

                <li>
                    <a href="/saticket/view/pages/acheteur/mes_commentaires.php">
                        <i class="fa fa-comments"></i>
                        <span>Mes Commentaires</span>
                    </a>
                </li>

            <?php endif; ?>

            <li>
                <a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify">
                    <i class="fa fa-angle-double-left"></i>
                </a>
            </li>
        </ul>
    </div>
</div>
<div class="sidebar-bg"></div>