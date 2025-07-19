<?php
session_start();
$connecte = isset($_SESSION['admin_id']) || isset($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Réservation de visa</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f5f7fa;
      margin: 0;
      padding: 0;
      color: #333;
    }
    header {
      background-color: #ffffff;
      padding: 20px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      margin: 0;
      padding: 0;
    }
    nav ul li a {
      color: #007BFF;
      text-decoration: none;
      font-weight: 600;
    }
    .container {
      max-width: 900px;
      margin: 40px auto;
      background-color: #fff;
      padding: 40px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      border-radius: 12px;
    }
    h1, h2 {
      text-align: center;
      color: #007BFF;
    }
    label {
      display: block;
      margin-top: 20px;
      font-weight: 600;
    }
    select, input[type="text"], input[type="email"], input[type="tel"], input[type="date"], input[type="file"], button {
      margin-top: 8px;
      padding: 12px;
      width: 100%;
      font-size: 16px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    button {
      background-color: #007BFF;
      color: white;
      font-weight: 600;
      border: none;
      margin-top: 30px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }
    button:hover {
      background-color: #0056b3;
    }
    #prix {
      margin-top: 20px;
      font-size: 18px;
      font-weight: 600;
      color: #007BFF;
    }
    #btn-reserver, #formulaire-complet {
      display: none;
    }
    #confirmation-message {
      display: none;
      margin-top: 30px;
      padding: 20px;
      font-weight: 600;
      text-align: center;
      border-radius: 10px;
    }
  </style>
</head>
<body>
  <header>
    <div style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
      <div style="display: flex; align-items: center;">
        <img src="images/IMG_3184.PNG" alt="Logo Rokhaya" style="height: 50px; margin-right: 15px;" />
        <div>
          <h1 style="margin: 0; font-size: 1.4rem; color: #222;">Rokhaya Consulting Group</h1>
          <p style="margin: 0; font-size: 0.85rem; color: #555;">Un accompagnement fiable pour vos études et voyages à l'international</p>
        </div>
      </div>
      <nav>
          <ul style="display: flex; list-style: none; margin: 0; padding: 0; gap: 25px;">
      <li><a href="base.html" style="text-decoration: none; color: #007BFF; font-weight: 600;">Accueil</a></li>
      <li><a href="destinations" style="text-decoration: none; color: #007BFF; font-weight: 600;">Destinations</a></li>
      <li><a href="contact" style="text-decoration: none; color: #007BFF; font-weight: 600;">Contact</a></li>
      <li><a href="suivi.php" style="text-decoration: none; color: #007BFF; font-weight: 600;">Suivi</a></li>
      <li><a href="dashboard_stats.php" style="text-decoration: none; color: #007BFF; font-weight: 600;">Statistiques</a></li>
     
      <?php if (isset($_SESSION['admin'])): ?>
      <li><a href="logout.php" style="color: red;">Déconnexion</a></li>
    <?php else: ?>
      <li><a href="auth.php">Connexion</a></li>
    <?php endif; ?>
    </ul>
      </nav>
    </div>
  </header>

  <div class="container">
    <h1>Réserver un visa</h1>

    <label for="pays">Choisissez le pays :</label>
    <select id="pays">
      <option value="">-- Sélectionnez un pays --</option>
      <option value="france">France</option>
      <option value="canada">Canada</option>
      <option value="espagne">Espagne</option>
      <option value="angleterre">Angleterre</option>
      <option value="luxembourg">Luxembourg</option>
      <option value="belgique">Belgique</option>
      <option value="usa">États-Unis</option>
    </select>

    <label for="typeVisa">Choisissez le type de visa :</label>
    <select id="typeVisa">
      <option value="">-- Sélectionnez un type --</option>
      <option value="etudiant">Étudiant</option>
      <option value="visiteur">Visiteur</option>
      <option value="commercant">Commerçant</option>
    </select>

    <div id="prix"></div>

    <?php if ($connecte): ?>
      <button id="btn-reserver">Réserver maintenant</button>

      <div id="formulaire-complet">
        <h2>Formulaire de demande de visa</h2>
        <form id="visa-form" method="post" enctype="multipart/form-data">
          <label for="nom">Nom complet :</label>
          <input type="text" name="nom" required>

          <label for="prenom">Prénom :</label>
          <input type="text" name="prenom" required>

          <label for="dob">Date de naissance :</label>
          <input type="date" name="dob" required>

          <label for="tel">Téléphone :</label>
          <input type="tel" name="tel" required>

          <label for="email">Adresse email :</label>
          <input type="email" name="email" required>

          <label for="passeport">Copie du passeport :</label>
          <input type="file" name="passeport" required>

          <label for="photo_identite">Photo d'identité :</label>
          <input type="file" name="photo_identite" required>

          <div id="docsSupp"></div>

          <p style="margin-top: 20px; font-weight: bold; color: #007BFF;">
            ⚠️ Cliquez ici pour effectuer le paiement avant d'envoyer votre demande :
            <br>
            <a href="paiement.php" target="_blank" style="color: green; font-weight: bold; text-decoration: underline;">💳 Payer maintenant</a>
          </p>

          <button type="submit">Envoyer la demande</button>
        </form>
      </div>
    <?php else: ?>
      <p style="margin-top: 20px; color: red; font-weight: 600;">
        ⚠️ Vous devez <a href="auth.php" style="color: #007BFF;">vous connecter ou vous inscrire</a> pour réserver un visa.
      </p>
    <?php endif; ?>

    <div id="confirmation-message"></div>
  </div>

  <script>
    const prix = {
      france: { etudiant: 50000, visiteur: 40000, commercant: 60000 },
      canada: { etudiant: 70000, visiteur: 55000, commercant: 75000 },
      usa: { etudiant: 80000, visiteur: 60000, commercant: 85000 },
      espagne: { etudiant: 55000, visiteur: 60000, commercant: 85000 },
      belgique: { etudiant: 80000, visiteur: 60000, commercant: 85000 },
      luxembourg: { etudiant: 55000, visiteur: 60000, commercant: 85000 }
    };

    const selectPays = document.getElementById('pays');
    const selectVisa = document.getElementById('typeVisa');
    const prixDiv = document.getElementById('prix');
    const btnReserver = document.getElementById('btn-reserver');
    const formulaire = document.getElementById('formulaire-complet');
    const docsSupp = document.getElementById('docsSupp');
    const visaForm = document.getElementById('visa-form');
    const confirmationMessage = document.getElementById('confirmation-message');

    function afficherPrix() {
      const pays = selectPays.value;
      const visa = selectVisa.value;
      if (pays && visa && prix[pays]) {
        prixDiv.innerHTML = `<strong>Prix du visa :</strong> ${prix[pays][visa].toLocaleString()} FCFA`;
        prixDiv.style.display = 'block';
        btnReserver.style.display = 'inline-block';
      } else {
        prixDiv.style.display = 'none';
        btnReserver.style.display = 'none';
        formulaire.style.display = 'none';
      }
    }

    function chargerDocsSupplementaires() {
      const typeVisa = selectVisa.value;
      docsSupp.innerHTML = "";

      if (typeVisa === "etudiant") {
        docsSupp.innerHTML = `
          <h3>📎 Documents requis supplémentaires</h3>
          <label for="certificat">Certificat de scolarité :</label>
          <input type="file" id="certificat" name="certificat" required>
          <label for="attestation">Lettre d'admission :</label>
          <input type="file" id="attestation" name="attestation" required>
          <label for="releve">Relevé bancaire (3 derniers mois) :</label>
          <input type="file" id="releve" name="releve" required>
          <label for="solde">Attestation de solde :</label>
          <input type="file" id="solde" name="solde" required>
        `;
      } else if (typeVisa === "visiteur") {
        docsSupp.innerHTML = `
          <h3>📎 Documents requis supplémentaires</h3>
          <label for="invitation">Lettre d’invitation :</label>
          <input type="file" id="invitation" name="invitation" required>
          <label for="hebergement">Justificatif d’hébergement :</label>
          <input type="file" id="hebergement" name="hebergement" required>
          <label for="releve">Relevé bancaire (3 derniers mois) :</label>
          <input type="file" id="releve" name="releve" required>
        `;
      } else if (typeVisa === "commercant") {
        docsSupp.innerHTML = `
          <h3>📎 Documents requis supplémentaires</h3>
          <label for="registre">Registre de commerce :</label>
          <input type="file" id="registre" name="registre" required>
          <label for="ninea">NINEA :</label>
          <input type="file" id="ninea" name="ninea" required>
          <label for="releve">Relevé bancaire (3 derniers mois) :</label>
          <input type="file" id="releve" name="releve" required>
          <label for="solde">Attestation de solde :</label>
          <input type="file" id="solde" name="solde" required>
        `;
      }
    }

    selectPays.addEventListener('change', afficherPrix);
    selectVisa.addEventListener('change', () => {
      afficherPrix();
      chargerDocsSupplementaires();
    });

    btnReserver.addEventListener('click', () => {
      formulaire.style.display = 'block';
      btnReserver.style.display = 'none';
    });

    visaForm.addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(visaForm);

      fetch('/rokhaya_consulting/templates/traitement.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.text())
      .then(data => {
        if (data.toLowerCase().includes('success')) {
          formulaire.style.display = 'none';
          confirmationMessage.style.display = 'block';
          confirmationMessage.style.backgroundColor = '#d4edda';
          confirmationMessage.style.color = '#155724';
          confirmationMessage.style.border = '1px solid #c3e6cb';
          confirmationMessage.textContent = '✅ Votre demande de visa a bien été envoyée. Merci pour votre confiance !';
          window.scrollTo({ top: 0, behavior: 'smooth' });
        } else {
          confirmationMessage.style.display = 'block';
          confirmationMessage.style.backgroundColor = '#f8d7da';
          confirmationMessage.style.color = '#721c24';
          confirmationMessage.style.border = '1px solid #f5c6cb';
          confirmationMessage.textContent = '❌ Une erreur est survenue : ' + data;
        }
      })
      .catch(error => {
        confirmationMessage.style.display = 'block';
        confirmationMessage.style.backgroundColor = '#f8d7da';
        confirmationMessage.style.color = '#721c24';
        confirmationMessage.style.border = '1px solid #f5c6cb';
        confirmationMessage.textContent = '❌ Erreur réseau ou serveur : ' + error;
      });
    });

  </script>
<footer style="background-color: #0A1F44; color: white; padding: 20px 0; font-family: Arial, sans-serif;">
  <div style="max-width: 1200px; margin: auto; display: flex; justify-content: space-between; align-items: flex-start; padding: 0 20px; flex-wrap: wrap;">
    <div style="flex: 1; min-width: 250px; margin-bottom: 10px;">
      <h3 style="margin-bottom: 10px;">À propos de nous</h3>
      <p style="margin: 0; font-size: 14px; line-height: 1.5;">
        Rokhaya Consulting Group est une agence spécialisée dans l’accompagnement pour l’obtention de visas pour l’espace Schengen, les USA, le Canada et bien d’autres destinations. Nous vous guidons dans toutes les étapes administratives avec professionnalisme et efficacité.
      </p>
    </div>
    <div style="flex: 1;"></div>
    <div style="min-width: 250px; text-align: right;">
      <h3 style="margin-bottom: 10px;">Suivez-nous</h3>
      <div style="display: flex; align-items: center; justify-content: flex-end; margin-bottom: 10px;">
        <img src="images/facebook.jpg" alt="Facebook" style="width: 24px; margin-right: 10px;">
        <a href="https://facebook.com" target="_blank" style="color: white; text-decoration: none;">Facebook</a>
      </div>
      <div style="display: flex; align-items: center; justify-content: flex-end; margin-bottom: 10px;">
        <img src="images/insta.jpg" alt="Instagram" style="width: 24px; margin-right: 10px;">
        <a href="https://instagram.com" target="_blank" style="color: white; text-decoration: none;">Instagram</a>
      </div>
      <div style="display: flex; align-items: center; justify-content: flex-end;">
        <img src="images/whatsapp.jpg" alt="WhatsApp" style="width: 24px; margin-right: 10px;">
        <a href="https://wa.me/123456789" target="_blank" style="color: white; text-decoration: none;">WhatsApp</a>
      </div>
    </div>
  </div>
  <p style="text-align: center; font-size: 12px; margin-top: 20px;">&copy; 2025 Rokhaya Consulting Group. Tous droits réservés.</p>
</footer>
<script>
  const faqItems = document.querySelectorAll('.faq-item');

  faqItems.forEach(item => {
    item.querySelector('.faq-question').addEventListener('click', () => {
      item.classList.toggle('active');

      // Fermer les autres
      faqItems.forEach(other => {
        if (other !== item) other.classList.remove('active');
      });
    });
  });
</script>
</body>
</html>




