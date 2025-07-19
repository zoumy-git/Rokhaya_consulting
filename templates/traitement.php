<?php
// Activer les erreurs pour le debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Dossier de destination
$dossier = "demandes/";
if (!is_dir($dossier)) {
    mkdir($dossier, 0777, true);
}

// Vérifie si les champs obligatoires sont présents
if (
    isset($_POST['nom'], $_POST['prenom'], $_POST['dob'], $_POST['tel'], $_POST['email']) &&
    isset($_FILES['passeport']) && isset($_FILES['photo_identite'])
) {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $dob = $_POST['dob'];
    $tel = $_POST['tel'];
    $email = $_POST['email'];

    // Nom du dossier utilisateur
    $folderName = $dossier . $nom . "_" . $prenom . "_" . time() . "/";
    mkdir($folderName, 0777, true);

    // Fonction pour sauvegarder les fichiers
    function saveFile($file, $folder) {
        if ($file['error'] === 0) {
            $destination = $folder . basename($file['name']);
            move_uploaded_file($file['tmp_name'], $destination);
        }
    }
// Téléversement de la preuve de paiement
$paiement = $_FILES['paiement']['name'];
$tmp_paiement = $_FILES['paiement']['tmp_name'];
$chemin_paiement = "demandes/" . $paiement;
move_uploaded_file($tmp_paiement, $chemin_paiement);

    // Sauvegarder les fichiers principaux
    saveFile($_FILES['passeport'], $folderName);
    saveFile($_FILES['photo_identite'], $folderName);

    // Sauvegarder tous les fichiers supplémentaires
    $fichiers_sup = ['certificat', 'attestation', 'releve', 'solde', 'invitation', 'hebergement', 'registre', 'ninea'];

    foreach ($fichiers_sup as $fichier) {
        if (isset($_FILES[$fichier])) {
            saveFile($_FILES[$fichier], $folderName);
        }
    }

    // Sauvegarde des infos dans un fichier texte
    $contenu = "Nom : $nom\nPrénom : $prenom\nDate de naissance : $dob\nTéléphone : $tel\nEmail : $email\nDate : " . date("Y-m-d H:i:s");
    file_put_contents($folderName . "infos.txt", $contenu);

    echo "success";
} else {
    echo "Tous les champs obligatoires ne sont pas remplis.";
}
?>
