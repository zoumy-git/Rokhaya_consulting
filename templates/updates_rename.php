<?php
// Connexion à la base
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rokhaya_consulting";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connexion échouée : " . $conn->connect_error);
}

// 1. Mettre toutes les étapes à 1 par défaut
$conn->query("UPDATE demandes_visa SET etape = 1");

// 2. Paiement confirmé → étape 2
$conn->query("UPDATE demandes_visa 
    SET etape = 2 
    WHERE statut = 'en attente' 
    AND type_visa IS NOT NULL 
    AND type_visa != ''");

// 3. Transmis à l’ambassade → étape 4
$conn->query("UPDATE demandes_visa 
    SET etape = 4 
    WHERE statut = 'en attente' 
    AND type_visa IS NOT NULL 
    AND type_visa != '' 
    AND (motif IS NULL OR motif = '')");

// 4. En attente de réponse → étape 5
$conn->query("UPDATE demandes_visa 
    SET etape = 5 
    WHERE statut = 'en attente' 
    AND motif IS NOT NULL 
    AND motif != ''");

// 5. Visa approuvé / refusé → étape 6
$conn->query("UPDATE demandes_visa 
    SET etape = 6 
    WHERE statut = 'accepté' OR statut = 'refusé'");

echo "Les étapes ont été mises à jour avec succès.";
$conn->close();

include("update_etapes.php");

?>
