<?php
session_start();
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Dashboard des Statistiques - Rokhaya Consulting Group</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      background-color: #f5f7fb;
    }
    header {
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 30px;
      background-color: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    header img {
      height: 50px;
    }
    nav ul {
      display: flex;
      gap: 20px;
      list-style: none;
    }
    nav a {
      text-decoration: none;
      color: #007BFF;
      font-weight: bold;
    }
    h2 {
      text-align: center;
      margin: 30px 0 10px;
      color: #333;
    }
    .dashboard {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      padding: 0 40px 40px;
    }
    .card {
      background: white;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
      text-align: center;
    }
    .card h3 {
      margin: 0;
      font-size: 1.2rem;
      color: #666;
    }
    .card p {
      font-size: 2rem;
      margin: 10px 0 0;
      color: #007BFF;
      font-weight: bold;
    }
    canvas {
      max-width: 100%;
      height: auto;
    }
    .table-section {
      padding: 0 40px 40px;
    }
    .table-scroll {
      max-height: 400px;
      overflow-y: auto;
      border: 1px solid #ddd;
      border-radius: 10px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 8px;
      border: 1px solid #ddd;
      text-align: center;
    }
    thead {
      background-color: #007BFF;
      color: white;
    }
    .popup-message {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 30px;
      border-radius: 8px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      z-index: 1000;
      text-align: center;
    }
    .popup-message p {
      font-size: 1.2rem;
      margin-bottom: 20px;
      color: green;
    }
    .popup-message button {
      padding: 10px 20px;
      background-color: #007BFF;
      color: white;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }
    td, th {
  width: 33.33%;
}

  </style>
</head>
<body>
<header>
  <div style="display: flex; align-items: center;">
    <img src="images/IMG_3184.PNG" alt="Logo Rokhaya">
    <div>
      <h1 style="margin: 0 0 5px 15px; font-size: 1.6rem;">Rokhaya Consulting Group</h1>
      <p style="margin: 0 0 0 15px; font-size: 0.85rem; color: #555;">Études & Voyages Internationaux</p>
    </div>
  </div>
  <nav>
    <ul>
      <li><a href="base.html">Accueil</a></li>
      <li><a href="contact.html">Contact</a></li>
      <li><a href="suivi.php">Suivi</a></li>
      <li><a href="dashboard_stats.php">Statistiques</a></li>
      <li><a href="dashboard_stats.php" style="text-decoration: none; color: #007BFF; font-weight: 600;">Dashboard</a></li>
      
       <?php if (isset($_SESSION['admin'])): ?>
      <li><a href="logout.php" style="color: red;">Déconnexion</a></li>
    <?php else: ?>
      <li><a href="auth.php">Connexion</a></li>
    <?php endif; ?>

    </ul>
  </nav>
</header>

<h2>Dashboard Statistiques Visa</h2>


<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rokhaya_consulting";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Erreur de connexion : " . $conn->connect_error);
}

$total = $accepte = $refuse = $attente = 0;
$sql = "SELECT statut, COUNT(*) as count FROM demandes_visa GROUP BY statut";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $total += $row['count'];
    $statut = strtolower(trim($row['statut']));
    if (strpos($statut, 'approuv') !== false) {
        $accepte += $row['count'];
    } elseif (strpos($statut, 'refus') !== false) {
        $refuse += $row['count'];
    } else {
        $attente += $row['count'];
    }
}

$pays_data = [];
$sqlPays = "SELECT pays, COUNT(*) as count FROM demandes_visa GROUP BY pays";
$resultPays = $conn->query($sqlPays);
while ($row = $resultPays->fetch_assoc()) {
    $pays_data[$row['pays']] = $row['count'];
}

$motifs_data = [];
$sqlMotifs = "SELECT motif_refus, COUNT(*) as count FROM demandes_visa WHERE LOWER(statut) LIKE '%refus%' GROUP BY motif_refus";
$resultMotifs = $conn->query($sqlMotifs);
while ($row = $resultMotifs->fetch_assoc()) {
    $motif = $row['motif_refus'] ?: 'Non spécifié';
    $motifs_data[$motif] = $row['count'];
}

$mois_demandes = array_fill(1, 12, 0);
$sqlEvolution = "SELECT MONTH(date_demande) as mois, COUNT(*) as total FROM demandes_visa WHERE YEAR(date_demande) = 2024 GROUP BY mois";
$resultEvolution = $conn->query($sqlEvolution);
while ($row = $resultEvolution->fetch_assoc()) {
    $mois_demandes[(int)$row['mois']] = (int)$row['total'];
}

$demandes = [];
$sqlDemandes = "SELECT pays, statut, date_demande FROM demandes_visa";
$resultDemandes = $conn->query($sqlDemandes);
while ($row = $resultDemandes->fetch_assoc()) {
    $demandes[] = $row;
}
?>


<div class="dashboard">
  <div class="card"><h3>Total demandes</h3><p><?php echo $total; ?></p></div>
  <div class="card"><h3>Acceptées</h3><p><?php echo $accepte; ?></p></div>
  <div class="card"><h3>Refusées</h3><p><?php echo $refuse; ?></p></div>
  <div class="card"><h3>En attente</h3><p><?php echo $attente; ?></p></div>
</div>

<div class="dashboard">
  <div class="card"><h3>Statut des demandes</h3><canvas id="chartStatut"></canvas></div>
  <div class="card"><h3>Demandes par pays</h3><canvas id="chartPays"></canvas></div>
  <div class="card"><h3>Motifs de refus</h3><canvas id="chartMotifs"></canvas></div>
</div>


<div class="table-section">
  <div style="display: flex; gap: 20px; align-items: flex-start; flex-wrap: nowrap;">

    <!-- Bloc gauche : Courbe -->
    <div style="flex: 1.5; background: white; padding: 40px; border-radius: 5px; box-shadow: 0 4px 5px rgba(0,0,0,0.05); height: 100%;">
      <h3 style="text-align:center;">Évolution des demandes (2024)</h3>
      <canvas id="chartEvolution" style="width: 100%; height: 400px;"></canvas>
    </div>

    <!-- Bloc droit : Filtres + Tableau dans le même fond blanc -->
    <div style="flex: 1.5;">
        
      <div style="background: white; padding: 20px; border-radius: 5px; box-shadow: 0 4px 5px rgba(0,0,0,0.05); display: flex; flex-direction: column; gap: 20px;">
          <h3 style="text-align:center;">Tableau des demandes</h3>
     <div style="background: white; padding: 15px; border-radius: 5px; box-shadow: 0 4px 5px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
  
     <!-- Menu déroulant Pays -->
  <select id="filtre-tableau-pays" style="padding: 5px;">
    <option value="">Tous les pays</option>
    <?php foreach(array_keys($pays_data) as $p): ?>
      <option value="<?= htmlspecialchars($p) ?>"><?= htmlspecialchars($p) ?></option>
    <?php endforeach; ?>
  </select>

  <!-- Menu déroulant Statut -->
  <select id="filtre-tableau-statut" style="padding: 5px;">
    <option value="">Tous les statuts</option>
    <option value="accepté">Accepté</option>
    <option value="refusé">Refusé</option>
    <option value="en attente">En attente</option>
  </select>

  <input type="date" id="filtre-tableau-date" style="padding: 5px;">

    <!-- ✅ Bouton Réinitialiser -->

  <!-- ✅ Bouton Filtrer -->
  <button onclick="filtrerTableau()" style="padding: 6px 12px; background-color: #007BFF; color: white; border: none; border-radius: 5px; cursor: pointer;">🔍 Filtrer</button>

  <!-- ✅ Bouton Exporter -->
  <button onclick="exporterExcel()" style="margin-left: auto; padding: 6px 12px; background-color: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">📥 Exporter Excel</button>
</div>


        <!-- Tableau -->
        <div>
          <div class="table-scroll">
            <table id="table-demandes">
              <thead>
                <tr>
                  <th>Pays</th>
                  <th>Statut</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
               <?php foreach ($demandes as $d): ?>
            <tr>
                <td><?php echo $d['pays']; ?></td>
                <td><?php echo $d['statut']; ?></td>
                <td><?php echo date('Y-m-d', strtotime($d['date_demande'])); ?></td>
            </tr>
          <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>

      </div> <!-- fin du fond blanc -->
    </div>

  </div>
</div>

<div class="popup-message" id="popup-message">
  <p>Exportation terminée avec succès ✔️</p>
  <button onclick="fermerPopup()">OK</button>
</div>

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
<script>
const stats = {
  total: <?php echo $total; ?>,
  accepte: <?php echo $accepte; ?>,
  refuse: <?php echo $refuse; ?>,
  attente: <?php echo $attente; ?>,
  pays: <?php echo json_encode($pays_data); ?>,
  motifs: <?php echo json_encode($motifs_data); ?>,
  evolution: <?php echo json_encode(array_values($mois_demandes)); ?>
};

new Chart(document.getElementById("chartStatut"), {
  type: 'doughnut',
  data: {
    labels: ['Visa approuvé', 'Visa refusé', 'En attente de réponse'],
    datasets: [{
      data: [stats.accepte, stats.refuse, stats.attente],
      backgroundColor: ['#28a745', '#dc3545', '#ffc107']
    }]
  }
});



new Chart(document.getElementById("chartPays"), {
  type: 'bar',
  data: {
    labels: Object.keys(stats.pays),
    datasets: [{ label: 'Nombre de demandes', data: Object.values(stats.pays), backgroundColor: '#007BFF' }]
  }
});

new Chart(document.getElementById("chartMotifs"), {
  type: 'bar',
  data: {
    labels: Object.keys(stats.motifs),
    datasets: [{ label: 'Motifs de refus', data: Object.values(stats.motifs), backgroundColor: '#dc3545' }]
  },
  options: { indexAxis: 'y' }
});

new Chart(document.getElementById("chartEvolution"), {
  type: 'line',
  data: {
    labels: ['Janv', 'Févr', 'Mars', 'Avril', 'Mai', 'Juin', 'Juil', 'Août', 'Sept', 'Oct', 'Nov', 'Déc'],
    datasets: [{
      label: 'Demandes 2024',
      data: stats.evolution,
      borderColor: '#007BFF',
      backgroundColor: 'rgba(0, 123, 255, 0.2)',
      tension: 0.3
    }]
  },
  options: {
    responsive: true,
    scales: { y: { beginAtZero: true } }
  }
});

function exporterExcel() {
  let csv = "Pays,Statut\n";
  const rows = document.querySelectorAll("#table-demandes tbody tr");
  rows.forEach(row => {
    const cols = row.querySelectorAll("td");
    if (cols.length >= 2) {
      const pays = cols[0].textContent.trim();
      const statut = cols[1].textContent.trim();
      csv += `${pays},${statut}\n`;
    }
  });
  const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
  const link = document.createElement("a");
  link.href = URL.createObjectURL(blob);
  link.download = "demandes_visa.csv";
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);
  document.getElementById("popup-message").style.display = "block";
}

function fermerPopup() {
  document.getElementById("popup-message").style.display = "none";
}

function filtrerTableau() {
  const filtrePays = document.getElementById("filtre-tableau-pays").value.toLowerCase();
  const filtreStatut = document.getElementById("filtre-tableau-statut").value.toLowerCase();
  const filtreDate = document.getElementById("filtre-tableau-date").value;
  const lignes = document.querySelectorAll("#table-demandes tbody tr");

  lignes.forEach(row => {
    const pays = row.cells[0].textContent.toLowerCase();
    const statut = row.cells[1].textContent.toLowerCase();
    const date = row.cells[2].textContent.toLowerCase();

    const matchPays = !filtrePays || pays === filtrePays;
    const matchStatut = !filtreStatut || statut === filtreStatut;
    const matchDate = !filtreDate || date === filtreDate;

    if (matchPays && matchStatut) {
      row.style.display = ""; // Affiche la ligne
    } else {
      row.style.display = "none"; // Cache la ligne
    }
  });
}

</script>
</body>
</html>

