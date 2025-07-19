<?php
session_start();
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rokhaya_consulting";
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Connexion échouée : " . $conn->connect_error);
}

$message = "";

// Traitement de la connexion
if (isset($_POST['connexion'])) {
  $email = $_POST['email'] ?? '';
  $password = $_POST['mot_de_passe'] ?? '';

  $stmt = $conn->prepare("SELECT id, nom, mot_de_passe, role FROM admins WHERE email = ?");
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['mot_de_passe'])) {
      $_SESSION['admin_id'] = $admin['id'];
      $_SESSION['admin_nom'] = $admin['nom'];
      $_SESSION['admin_role'] = $admin['role'];

      if ($admin['role'] === 'admin') {
        header("Location: dashboard_stats.php");
      } else {
        header("Location: reservation.php");
      }
      exit;
    } else {
      $message = "❌ Mot de passe incorrect. <a href='reintialliser.php' style='color:#007BFF;'>Réinitialiser ici</a>";
    }
  } else {
    $message = "❌ Aucun compte trouvé avec cet email.";
  }

  $stmt->close();
}

// Traitement de l'inscription
if (isset($_POST['inscription'])) {
  $nom = trim($_POST['nom'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $password = $_POST['mot_de_passe'] ?? '';

  if ($nom && $email && $password) {
    // Vérifier si l'email existe déjà
    $check = $conn->prepare("SELECT id FROM admins WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
      $message = "❌ Cette adresse email est déjà utilisée.";
    } else {
      $mot_de_passe_hache = password_hash($password, PASSWORD_DEFAULT);
      $insert = $conn->prepare("INSERT INTO admins (nom, email, mot_de_passe, date_inscription, role) VALUES (?, ?, ?, NOW(), 'utilisateur')");
      $insert->bind_param("sss", $nom, $email, $mot_de_passe_hache);

      if ($insert->execute()) {
        $message = "✅ Inscription réussie. Vous pouvez maintenant vous connecter.";
      } else {
        $message = "❌ Erreur lors de l'inscription.";
      }

      $insert->close();
    }

    $check->close();
  } else {
    $message = "❌ Tous les champs sont obligatoires.";
  }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Connexion / Inscription</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5f7fa;
      margin: 0;
      padding: 0;
    }
    header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 15px 30px;
      background-color: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    header img {
      height: 50px;
      margin-right: 10px;
    }
    header h1 {
      margin: 0;
      font-size: 1.5rem;
      color: #007BFF;
    }
    nav ul {
      list-style: none;
      display: flex;
      gap: 20px;
      margin: 0;
      padding: 0;
    }
    nav ul li a {
      text-decoration: none;
      font-weight: bold;
      color: #007BFF;
    }
    .container {
      max-width: 400px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #007BFF;
    }
    .message {
      padding: 10px;
      margin-bottom: 20px;
      border-radius: 5px;
      font-weight: 600;
      text-align: center;
    }
    .message.success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .message.error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
    input, button {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 15px;
    }
    button {
      background-color: #007BFF;
      color: white;
      border: none;
      font-weight: bold;
      cursor: pointer;
    }
    .toggle {
      background: none;
      border: none;
      color: #007BFF;
      cursor: pointer;
      text-decoration: underline;
      margin-top: 10px;
    }
    .hidden {
      display: none;
    }
  </style>
</head>
<body>

<header>
  <div style="display: flex; align-items: center;">
    <img src="images/IMG_3184.PNG" alt="Logo" />
    <h1>Rokhaya Consulting Group</h1>
  </div>
  <nav>
    <ul>
      <li><a href="base.html">Accueil</a></li>
      <li><a href="reservation.php">Réservation</a></li>
      <li><a href="suivi.php">Suivi</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="dashboard_stats.php">Dashboard</a></li>
       <?php if (isset($_SESSION['admin'])): ?>
      <li><a href="logout.php" style="color: red;">Déconnexion</a></li>
    <?php else: ?>
      <li><a href="login.php">Connexion</a></li>
    <?php endif; ?>
    </ul>
  </nav>
</header>

<div class="container">
  <h2 id="form-title">Connexion</h2>

  <?php if (!empty($message)) : ?>
    <div class="message <?= strpos($message, '❌') !== false ? 'error' : 'success' ?>">
      <?= $message ?>
    </div>
  <?php endif; ?>

  <!-- Formulaire Connexion -->
  <form method="POST" id="login-form">
    <input type="email" name="email" placeholder="Votre adresse email" required />
    <input type="password" name="mot_de_passe" placeholder="Mot de passe" required />
    <button type="submit" name="connexion">Se connecter</button>
  </form>

  <!-- Formulaire Inscription -->
  <form method="POST" id="register-form" class="hidden">
    <input type="text" name="nom" placeholder="Votre nom complet" required />
    <input type="email" name="email" placeholder="Votre adresse email" required />
    <input type="password" name="mot_de_passe" placeholder="Créer un mot de passe" required />
    <button type="submit" name="inscription">S'inscrire</button>
  </form>

  <button class="toggle" onclick="toggleForm()">Vous n'avez pas de compte ? Inscrivez-vous</button>
</div>


<script>
  const loginForm = document.getElementById("login-form");
  const registerForm = document.getElementById("register-form");
  const title = document.getElementById("form-title");
  const toggleBtn = document.querySelector(".toggle");

  function toggleForm() {
    loginForm.classList.toggle("hidden");
    registerForm.classList.toggle("hidden");

    if (loginForm.classList.contains("hidden")) {
      title.innerText = "Inscription";
      toggleBtn.innerText = "Vous avez déjà un compte ? Connectez-vous";
    } else {
      title.innerText = "Connexion";
      toggleBtn.innerText = "Vous n'avez pas de compte ? Inscrivez-vous";
    }
  }
</script>

</body>
</html>
