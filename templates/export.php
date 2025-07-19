<?php
// Connexion à la base de données
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'rokhaya_consulting';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Définir les en-têtes pour le téléchargement du fichier CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=demandes_export.csv');

// Ouvrir la sortie en écriture
$output = fopen("php://output", "w");

// Écrire les en-têtes du fichier CSV
fputcsv($output, ['Nom', 'Prénom', 'Email', 'Pays', 'Type de visa', 'Statut', 'Date']);

// Requête SQL pour récupérer les données
$sql = "SELECT nom, prenom, email, pays, type_visa, statut, date_demande FROM demandes";
$result = $conn->query($sql);

// Ajouter les lignes au fichier
while ($row = $result->fetch_assoc()) {
    fputcsv($output, $row);
}

fclose($output);
exit;
?>
