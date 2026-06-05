<div id="header" class="header navbar-default">
			<div class="navbar-header">
				<a href="index.php" class="navbar-brand"><span class="navbar-logo"></span> <b>Saticket</b></a>
				<button type="button" class="navbar-toggle" data-click="sidebar-toggled">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
			</div>
			<ul class="navbar-nav navbar-right">
				<li class="navbar-form">
					<form action="" method="POST" name="search">
						<div class="form-group">
							<input type="text" class="form-control" placeholder="Rechercher un ticket ou événement..." />
							<button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
						</div>
					</form>
				</li>
				<li class="dropdown">
					<a href="#" data-toggle="dropdown" class="dropdown-toggle f-s-14">
						<i class="fa fa-bell"></i>
						<span class="label">5</span>
					</a>
					<div class="dropdown-menu media-list dropdown-menu-right">
						<div class="dropdown-header">NOTIFICATIONS (5)</div>
						<a href="javascript:;" class="dropdown-item media">
							<div class="media-left">
								<i class="fa fa-bug media-object bg-silver-darker"></i>
							</div>
							<div class="media-body">
								<h6 class="media-heading">Alerte : Stock tickets bas <i class="fa fa-exclamation-circle text-danger"></i></h6>
								<div class="text-muted f-s-10">Il y a 3 minutes</div>
							</div>
						</a>
						<a href="javascript:;" class="dropdown-item media">
							<div class="media-left">
								<img src="../assets/img/user/user-1.jpg" class="media-object" alt="" />
								<i class="fab fa-whatsapp text-success media-object-icon"></i>
							</div>
							<div class="media-body">
								<h6 class="media-heading">Moussa Diop</h6>
								<p>Demande de remboursement pour le ticket VIP #458.</p>
								<div class="text-muted f-s-10">Il y a 25 minutes</div>
							</div>
						</a>
						<a href="javascript:;" class="dropdown-item media">
							<div class="media-left">
								<img src="../assets/img/user/user-2.jpg" class="media-object" alt="" />
								<i class="fab fa-whatsapp text-success media-object-icon"></i>
							</div>
							<div class="media-body">
								<h6 class="media-heading">Aminata Ndiaye</h6>
								<p>Problème lors du paiement Wave sur le concert de Wally Seck.</p>
								<div class="text-muted f-s-10">Il y a 35 minutes</div>
							</div>
						</a>
						<a href="javascript:;" class="dropdown-item media">
							<div class="media-left">
								<i class="fa fa-plus media-object bg-silver-darker"></i>
							</div>
							<div class="media-body">
								<h6 class="media-heading"> Nouvel organisateur inscrit</h6>
								<div class="text-muted f-s-10">Il y a 1 heure</div>
							</div>
						</a>
						<a href="javascript:;" class="dropdown-item media">
							<div class="media-left">
								<i class="fa fa-envelope media-object bg-silver-darker"></i>
								<i class="fab fa-google text-warning media-object-icon f-s-14"></i>
							</div>
							<div class="media-body">
								<h6 class="media-heading"> Email de contact reçu</h6>
								<div class="text-muted f-s-10">Il y a 2 heures</div>
							</div>
						</a>
						<div class="dropdown-footer text-center">
							<a href="javascript:;">Voir toutes les notifications</a>
						</div>
					</div>
				</li>
				<li class="dropdown navbar-user">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<img src="../assets/img/user/user-13.jpg" alt="" /> 
						<span class="d-none d-md-inline"><?php echo isset($_SESSION['nom']) ? htmlspecialchars($_SESSION['nom']) : 'Utilisateur'; ?></span> <b class="caret"></b>
					</a>
					<div class="dropdown-menu dropdown-menu-right">
						<div class="dropdown-header">
							<strong><?php echo isset($_SESSION['nom']) ? htmlspecialchars($_SESSION['nom']) : 'Utilisateur'; ?></strong><br>
							<small><?php echo isset($_SESSION['email']) ? htmlspecialchars($_SESSION['email']) : ''; ?></small>
						</div>
						<div class="dropdown-divider"></div>
						<a href="view/pages/admin/profil.php" class="dropdown-item"><i class="fa fa-user"></i> Mon Profil</a>
						<a href="javascript:;" class="dropdown-item"><i class="fa fa-envelope"></i> <span class="badge badge-danger pull-right">2</span> Messages</a>
						<a href="javascript:;" class="dropdown-item"><i class="fa fa-calendar"></i> Calendrier</a>
						<a href="javascript:;" class="dropdown-item"><i class="fa fa-cog"></i> Paramètres</a>
						<div class="dropdown-divider"></div>
						<a href="logout.php" class="dropdown-item text-danger"><i class="fa fa-sign-out-alt"></i> Déconnexion</a>
					</div>
				</li>
			</ul>
			</div>
		```