<?php
// Activer les erreurs pour le debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connexion à la base de données
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rokhaya_consulting";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
  die("Échec de la connexion : " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Suivi de votre demande</title>
  <style>
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

  <h2>Suivi de votre demande</h2>
  <form method="POST">
    <input type="email" name="email" placeholder="Entrez votre email" required>
    <br>
    <button type="submit">Afficher le suivi</button>
  </form>

<?php
// Si un email est soumis, traiter la demande
if (isset($_POST['email'])) {
  $email = $conn->real_escape_string($_POST['email']);
  $sql = "SELECT nom, prenom, statut, etape FROM demandes_visa WHERE email='$email' ORDER BY id DESC LIMIT 1";
  $result = $conn->query($sql);

  if ($result->num_rows > 0) {
    $demande = $result->fetch_assoc();
    $etape = (int)$demande['etape'];

    $etapes = [
      "Dossier reçu",
      "Paiement confirmé",
      "Documents vérifiés",
      "Transmis à l’ambassade",
      "En attente de réponse",
      "Visa approuvé / refusé"
    ];

    echo "<p><strong>Nom :</strong> " . htmlspecialchars($demande['prenom'] . " " . $demande['nom']) . "</p>";
    echo "<p><strong>Statut actuel :</strong> " . htmlspecialchars($demande['statut']) . "</p>";

    echo "<ul class='timeline'>";
    foreach ($etapes as $index => $nomEtape) {
      if ($index + 1 < $etape) {
  echo "<li class='completed'>$nomEtape</li>";
} elseif ($index + 1 == $etape) {
  // Si l'étape est la dernière, on le considère comme complétée
  if ($etape == count($etapes)) {
    echo "<li class='completed'>$nomEtape</li>";
  } else {
    echo "<li class='pending'>$nomEtape</li>";
  }
} else {
  echo "<li class='upcoming'>$nomEtape</li>";
}

    }
    echo "</ul>";
  } else {
    echo "<p style='color: red;'>Aucune demande trouvée avec cet email.</p>";
  }
}
?>

</body>
</html>
