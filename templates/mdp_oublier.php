<?php
$conn = new mysqli("localhost", "root", "", "rokhaya_consulting");
if ($conn->connect_error) die("Erreur : " . $conn->connect_error);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';

    $stmt = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($user = $res->fetch_assoc()) {
        $token = bin2hex(random_bytes(16));
        $stmt = $conn->prepare("UPDATE utilisateurs SET reset_token = ?, reset_at = NOW() WHERE email = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();

        $message = "✅ Lien de réinitialisation : <a href='templates/reinitialiser.php?token=$token'>Cliquez ici</a>";
    } else {
        $message = "❌ Aucun compte trouvé avec cet email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Mot de passe oublié</title>
    <style>
        body {
            font-family: Arial;
            background: #f3f3f3;
            display: flex;
            justify-content: center;
            padding-top: 60px;
        }
        .container {
            background: white;
            padding: 25px 30px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
            width: 400px;
        }
        h2 { margin-bottom: 20px; }
        input[type="email"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
        }
        button {
            padding: 10px 15px;
            background-color: #27ae60;
            color: white;
            border: none;
            cursor: pointer;
        }
        .message {
            margin-bottom: 10px;
            color: #c0392b;
        }
        .message a {
            color: #2980b9;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Mot de passe oublié</h2>
    <?php if (!empty($message)): ?>
        <div class="message"><?= $message ?></div>
    <?php endif; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Votre email" required>
        <button type="submit">Envoyer le lien</button>
    </form>
</div>
</body>
</html>