<?php
$conn = new mysqli("localhost", "root", "", "rokhaya_consulting");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$message = '';
$success = '';

// Vérifier le token
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();

    if (!$user) {
        $message = "❌ Lien invalide.";
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $pass = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';

        if ($pass !== $confirm) {
            $message = "❌ Les mots de passe ne correspondent pas.";
        } elseif (strlen($pass) < 6) {
            $message = "❌ Minimum 6 caractères.";
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE utilisateurs SET mot_de_passe = ?, reset_token = NULL WHERE id = ?");
            $stmt->bind_param("si", $hash, $user['id']);
            $stmt->execute();
            $success = "✅ Mot de passe réinitialisé. <a href='auth.php'>Se connecter</a>";
        }
    }
} else {
    $message = "❌ Lien invalide.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Réinitialiser le mot de passe</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f6f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .reset-box {
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
      width: 100%;
      max-width: 400px;
    }
    h2 {
      margin-bottom: 20px;
      color: #007BFF;
      text-align: center;
    }
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
    }
    input[type="submit"] {
      width: 100%;
      background: #007BFF;
      color: white;
      padding: 12px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      font-weight: bold;
    }
    .message {
      margin-top: 15px;
      color: red;
      text-align: center;
    }
    .success {
      color: green;
    }
  </style>
</head>
<body>
  <div class="reset-box">
    <h2>Réinitialiser votre mot de passe</h2>

    <?php if (!empty($message)): ?>
      <div class="message"><?= $message ?></div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="message success"><?= $success ?></div>
    <?php elseif (isset($user)): ?>
      <form method="POST">
        <input type="password" name="password" placeholder="Nouveau mot de passe" required>
        <input type="password" name="confirm" placeholder="Confirmer le mot de passe" required>
        <input type="submit" value="Réinitialiser">
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
