<?php
// Tu peux rendre ces variables dynamiques plus tard
$montant = 50000; // FCFA
$num_wave = "221778889900"; // Numéro Wave
$num_om = "*144#"; // USSD Orange Money Sénégal
$message = "Paiement visa Rokhaya Consulting - Montant : {$montant} FCFA";
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Choix de paiement</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f4f6f9;
      padding: 40px;
      text-align: center;
    }
    h1 {
      color: #007BFF;
    }
    .options {
      margin-top: 30px;
    }
    .btn-choice {
      padding: 14px 28px;
      font-size: 16px;
      margin: 10px;
      border-radius: 8px;
      border: none;
      font-weight: bold;
      cursor: pointer;
      color: white;
      transition: 0.3s ease;
    }
    .btn-choice.wave {
      background-color: #00b5ff;
    }
    .btn-choice.om {
      background-color: #ff7900;
    }
    .btn-choice:hover {
      opacity: 0.9;
    }
    #result {
      margin-top: 30px;
    }
    .btn-pay {
      margin-top: 20px;
      display: inline-block;
      padding: 12px 24px;
      font-size: 16px;
      background-color: #28a745;
      color: white;
      border-radius: 8px;
      text-decoration: none;
      font-weight: bold;
    }
  </style>
</head>
<body>

  <h1>Paiement Visa - <?= number_format($montant, 0, ',', ' ') ?> FCFA</h1>
  <p>Veuillez choisir votre méthode de paiement :</p>

  <div class="options">
    <button class="btn-choice wave" onclick="payer('wave')">📲 Payer via Wave</button>
    <button class="btn-choice om" onclick="payer('orange')">📞 Payer via Orange Money</button>
  </div>

  <div id="result"></div>

  <script>
    function payer(methode) {
      const montant = "<?= $montant ?>";
      const message = encodeURIComponent("<?= $message ?>");
      const numeroWave = "<?= $num_wave ?>";
      const ussdOM = "<?= $num_om ?>";

      const resultDiv = document.getElementById('result');
      resultDiv.innerHTML = "";

      if (methode === 'wave') {
        resultDiv.innerHTML = `
          <p>Vous allez être redirigé vers Wave via WhatsApp pour effectuer votre paiement.</p>
          <a class="btn-pay" href="https://wa.me/${numeroWave}?text=${message}" target="_blank">📲 Payer maintenant avec Wave</a>
        `;
      } else if (methode === 'orange') {
        resultDiv.innerHTML = `
          <p>Composez le code USSD suivant sur votre téléphone pour payer :</p>
          <a class="btn-pay" href="tel:${ussdOM}">📞 *144#</a>
        `;
      }
    }
  </script>

</body>
</html>
