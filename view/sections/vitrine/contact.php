    <!-- Contact Section -->
    <section id="contact" class="contact section">

      <div class="container section-title">
        <span class="description-title">Contact</span>
        <h2>Contactez-nous</h2>
        <p>Vous avez une question ou souhaitez organiser un événement ? Notre équipe est là pour vous accompagner.</p>
      </div>

      <div class="container">
        <div class="contact-wrapper">

          <!-- Infos de contact -->
          <div class="contact-info-panel">
            <div class="contact-info-header">
              <h3>Informations de Contact</h3>
              <p>Disponibles du lundi au samedi de 8h à 20h.</p>
            </div>
            <div class="contact-info-cards">
              <div class="info-card">
                <div class="icon-container"><i class="bi bi-pin-map-fill"></i></div>
                <div class="card-content"><h4>Notre Adresse</h4><p>Dakar, Plateau, Sénégal</p></div>
              </div>
              <div class="info-card">
                <div class="icon-container"><i class="bi bi-envelope-open"></i></div>
                <div class="card-content"><h4>Email</h4><p>contact@saticket.sn</p></div>
              </div>
              <div class="info-card">
                <div class="icon-container"><i class="bi bi-telephone-fill"></i></div>
                <div class="card-content"><h4>Téléphone / WhatsApp</h4><p>+221 77 000 00 00</p></div>
              </div>
              <div class="info-card">
                <div class="icon-container"><i class="bi bi-clock-history"></i></div>
                <div class="card-content"><h4>Horaires</h4><p>Lundi–Samedi : 8h – 20h</p></div>
              </div>
            </div>
            <div class="social-links-panel">
              <h5>Suivez-nous</h5>
              <div class="social-icons">
                <a href="#"><i class="bi bi-facebook"></i></a>
                <a href="#"><i class="bi bi-twitter-x"></i></a>
                <a href="#"><i class="bi bi-instagram"></i></a>
                <a href="#"><i class="bi bi-linkedin"></i></a>
                <a href="#"><i class="bi bi-youtube"></i></a>
              </div>
            </div>
          </div>

          <!-- Formulaire + carte -->
          <div class="contact-form-panel">
            <div class="map-container">
              <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15437.403403432!2d-17.4676861!3d14.7271893!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xec172f5b3c5f525%3A0x9d5d479dd9a5a5a!2sDakar!5e0!3m2!1sfr!2ssn!4v1714000000000"
                width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
            <div class="form-container">
              <h3>Envoyez-nous un message</h3>
              <p>Organisateur ou acheteur, décrivez votre besoin et nous vous répondons rapidement.</p>
              <form action="public/Visible/forms/contact.php" method="post" class="php-email-form">
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="nameInput" name="name" placeholder="Nom complet" required>
                  <label for="nameInput">Nom complet</label>
                </div>
                <div class="form-floating mb-3">
                  <input type="email" class="form-control" id="emailInput" name="email" placeholder="Adresse email" required>
                  <label for="emailInput">Adresse email</label>
                </div>
                <div class="form-floating mb-3">
                  <input type="text" class="form-control" id="subjectInput" name="subject" placeholder="Sujet" required>
                  <label for="subjectInput">Sujet (ex : organiser un événement)</label>
                </div>
                <div class="form-floating mb-3">
                  <textarea class="form-control" id="messageInput" name="message" style="height:150px" placeholder="Votre message" required></textarea>
                  <label for="messageInput">Votre message</label>
                </div>
                <div class="my-3">
                  <div class="loading">Envoi en cours...</div>
                  <div class="error-message"></div>
                  <div class="sent-message">Votre message a bien été envoyé. Merci !</div>
                </div>
                <div class="d-grid">
                  <button type="submit" class="btn-submit">Envoyer le message <i class="bi bi-send-fill ms-2"></i></button>
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </section>
