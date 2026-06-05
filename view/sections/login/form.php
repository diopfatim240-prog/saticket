<div class="login login-v1">
    <div class="login-container">
        <div class="login-header">
            <div class="brand">
                <span class="logo"></span> <b>SATICKET</b> CONNEXION
                <small>BIENVENUE SUR SATICKET </small>
            </div>
            <div class="icon">
                <i class="fa fa-lock"></i>
            </div>
        </div>
        <div class="login-body">
            <div class="login-content">
                <form action="controller/UtilisateursController.php" method="POST" id="loginForm" class="margin-bottom-0">
                    <div class="form-group m-b-20">
                        <input type="email" id="email" name="email" class="form-control form-control-lg inverse-mode" placeholder="Email Address" required />
                    </div>
                    <p class="error-message"></p>
                    <div class="form-group m-b-20">
                        <input type="password" id="password" name="password" class="form-control form-control-lg inverse-mode" placeholder="Password" required />
                    </div>
                    <div class="checkbox checkbox-css m-b-20">
                        <input type="checkbox" id="remember_checkbox" name="remember" /> 
                        <label for="remember_checkbox">
                            Remember Me
                        </label>
                    </div>
                    <div class="login-buttons m-b-20">
                        <button type="submit" id="btnSubmit" name="frmLogin" class="btn btn-success btn-block btn-lg">Connexion</button>
                    </div>
                    
                    <div class="text-center text-muted">
                        Pas encore de compte ? <a href="javascript:;" data-toggle="modal" data-target="#modalRegister" class="text-success font-weight-bold">Créer un compte</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalRegister" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content bg-dark text-white border-0" style="border-radius: 6px;">
            <div class="modal-header border-0 b-b-1">
                <h5 class="modal-title text-success font-weight-bold"><i class="fa fa-user-plus"></i> Inscription SaTicket</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="controller/UtilisateursController.php" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label class="text-muted">Nom Complet</label>
                        <input type="text" name="nom" class="form-control form-control-lg inverse-mode text-white bg-dark border-secondary" placeholder="Ex: Fatim Diop" required />
                    </div>
                    <div class="form-group">
                        <label class="text-muted">Numéro de Téléphone</label>
                        <input type="text" name="telephone" class="form-control form-control-lg inverse-mode text-white bg-dark border-secondary" placeholder="Ex: 771234567" required />
                    </div>
                    <div class="form-group">
                        <label class="text-muted">Adresse Email</label>
                        <input type="email" name="email" class="form-control form-control-lg inverse-mode text-white bg-dark border-secondary" placeholder="Email" required />
                    </div>
                    <div class="form-group">
                        <label class="text-muted">Mot de passe</label>
                        <input type="password" name="password" class="form-control form-control-lg inverse-mode text-white bg-dark border-secondary" placeholder="Password" required />
                    </div>
                    <div class="form-group">
                        <label class="text-muted">Confirmez le mot de passe</label>
                        <input type="password" name="password_confirm" class="form-control form-control-lg inverse-mode text-white bg-dark border-secondary" placeholder="Confirmez le mot de passe" required />
                    </div>
                </div>
                <div class="form-group">
                    <label class="text-muted">Vous êtes :</label>
                    <select name="role" class="form-control form-control-lg inverse-mode text-white bg-dark border-secondary" required>
                        <option value="acheteur">Acheteur</option>
                        <option value="organisateur">Organisateur</option>
                    </select>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
                    <button type="submit" name="btnRegister" class="btn btn-success px-4">S'inscrire</button>

                </div>
            </form>
        </div>
    </div>
</div>