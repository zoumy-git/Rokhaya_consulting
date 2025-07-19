<?php
$conn = new mysqli("localhost", "root", "", "rokhaya_consulting");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$message = '';
$token = $_GET['token'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🎯 Étape 1 : Formulaire d’email
    if (isset($_POST['email'])) {
        $email = $_POST['email'];

        $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result(); 

        if ($res && $res->num_rows > 0) {
            // 🔐 Génération du token
            $token = bin2hex(random_bytes(16));
            $stmt = $conn->prepare("UPDATE utilisateurs SET reset_token = ?, reset_at = NOW() WHERE email = ?");
            $stmt->bind_param("ss", $token, $email);
            $stmt->execute();

            // 📬 Construction de l’email
            $lien = "http://localhost/rokhaya_consulting/templates/reinitialiser.php?token=$token";
            $sujet = "🔐 Réinitialisation de votre mot de passe - Rokhaya Consulting";

            $message_email = "
Bonjour,

Vous avez demandé la réinitialisation de votre mot de passe.

👉 Cliquez ici pour choisir un nouveau mot de passe :
$lien

⏳ Ce lien expire dans 30 minutes.

Si vous n’avez pas fait cette demande, ignorez ce message.

Cordialement,
Rokhaya Consulting
";

            $headers = "From: contact@rokhaya-consulting.com\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($email, $sujet, $message_email, $headers)) {
                $message = "✅ Un lien de réinitialisation vous a été envoyé par mail.";
            } else {
                $message = "❌ Erreur lors de l'envoi de l'e-mail.";
            }
        } else {
            $message = "❌ Aucun compte trouvé avec cet email.";
        }

    // 🔐 Étape 2 : Soumission du nouveau mot de passe
    } elseif (isset($_POST['nouveau_mdp']) && !empty($token)) {
        $stmt = $conn->prepare("SELECT reset_at FROM utilisateurs WHERE reset_token = ?");
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($row = $res->fetch_assoc()) {
            $reset_at = strtotime($row['reset_at']);
            if (time() - $reset_at > 1800) {
                $message = "❌ Lien expiré.";
            } else {
                $new_pwd = password_hash($_POST['nouveau_mdp'], PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ?, reset_token = NULL, reset_at = NULL WHERE reset_token = ?");
                $stmt->bind_param("ss", $new_pwd, $token);
                $stmt->execute();
                $message = "✅ Mot de passe mis à jour.";
                $token = ''; // empêche d'afficher encore le formulaire
            }
        } else {
            $message = "❌ Lien invalide.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Réinitialiser mot de passe</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      padding: 40px;
      text-align: center;
    }
    .container {
      background: white;
      padding: 30px;
      display: inline-block;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      margin-bottom: 40px;
      width: 400px;
    }
    h2 {
      color: #007BFF;
      margin-bottom: 20px;
    }
    input[type="email"], input[type="password"] {
      padding: 12px;
      width: 100%;
      margin-bottom: 20px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      background: #007BFF;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }
    .message {
      margin-bottom: 15px;
      color: #c0392b;
    }
    .message a {
      color: #007BFF;
    }
    body {
      font-family: Arial, sans-serif;
      background: #f5f5f5;
      padding: 40px;
      text-align: center;
    }
    form {
      background: white;
      padding: 30px;
      display: inline-block;
      border-radius: 8px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      margin-bottom: 40px;
    }
    input[type="email"] {
      padding: 12px;
      width: 300px;
      margin-bottom: 20px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }
    button {
      background: #007BFF;
      color: white;
      padding: 12px 20px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-weight: bold;
    }
    h2 {
      color: #007BFF;
    }
    .timeline {
      margin: 40px auto;
      max-width: 600px;
      padding: 0;
      list-style-type: none;
    }
    .timeline li {
      background: #e9ecef;
      margin-bottom: 15px;
      padding: 15px 20px;
      border-radius: 6px;
      text-align: left;
      position: relative;
    }
    .timeline li::before {
      content: "✔";
      position: absolute;
      left: -30px;
      top: 50%;
      transform: translateY(-50%);
    }
    .timeline li.pending::before {
      content: "⏳";
      color: orange;
    }
    .timeline li.upcoming::before {
      content: "⏺";
      color: #aaa;
    }
    .timeline li.completed {
      background-color: #d4edda;
    }
    .timeline li.pending {
      background-color: #fff3cd;
    }
    .timeline li.upcoming {
      background-color: #f8f9fa;
    }
  </style>
</head>
<body>
<body>

<header style="display: flex; align-items: center; justify-content: space-between; padding: 10px 30px; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
  <div style="display: flex; align-items: center;">
    <img src="images/IMG_3184.PNG" alt="Logo Rokhaya" style="height: 50px; margin-right: 15px;" />
    <div>
      <h1 style="margin: 0; font-size: 1.8rem; color: #222;">Rokhaya Consulting Group</h1>
      <p style="margin: 0; font-size: 0.85rem; color: #555;">Un accompagnement fiable pour vos études et voyages à l'international</p>
    </div>
  </div>
  <nav>
     <ul style="display: flex; list-style: none; margin: 0; padding: 0; gap: 25px;">
       <li><a href="base.html" style="text-decoration: none; color: #007BFF; font-weight: 600;">Accueil</a></li>
      <li><a href="#destinations" style="text-decoration: none; color: #007BFF; font-weight: 600;">Destinations</a></li>
      <li><a href="#contact" style="text-decoration: none; color: #007BFF; font-weight: 600;">Contact</a></li>
      <li><a href="suivi.php" style="text-decoration: none; color: #007BFF; font-weight: 600;">Suivi</a></li>
      <li><a href="dashboard_stats.php" style="text-decoration: none; color: #007BFF; font-weight: 600;">Statistiques</a></li>
 <?php if (isset($_SESSION['admin'])): ?>
      <li><a href="logout.php" style="color: red;">Déconnexion</a></li>
    <?php else: ?>
      <li><a href="auth.php">Connexion</a></li>
    <?php endif; ?>
    </ul>
  </nav>
</header>


  </style>
</head>
<body>

<div class="container">
  <h2>Réinitialisation du mot de passe</h2>
  <?php if (!empty($message)): ?>
    <div class="message"><?= $message ?></div>
  <?php endif; ?>

  <?php if (empty($token)): ?>
    <!-- Formulaire Email -->
    <form method="POST">
      <input type="email" name="email" placeholder="Entrez votre adresse email" required>
      <button type="submit">Envoyer le lien</button>
    </form>
  <?php elseif (empty($message) || str_starts_with($message, "✅") === false): ?>
    <!-- Formulaire Nouveau mot de passe -->
    <form method="POST">
      <input type="password" name="nouveau_mdp" placeholder="Nouveau mot de passe" required>
      <button type="submit">Réinitialiser</button>
    </form>
  <?php endif; ?>
</div>

</body>
</html>