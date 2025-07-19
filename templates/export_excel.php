<?php
require __DIR__ . '/../vendor/autoload.php'; // vérifie ce chemin selon l'emplacement de ton fichier

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Connexion à la base de données
$conn = new mysqli("localhost", "root", "", "rokhaya_consulting");

$pays = $_POST['pays'] ?? '';
$statut = $_POST['statut'] ?? '';

$sql = "SELECT nom, prenom, email, pays, statut, date_demande FROM demandes_visa WHERE 1";
if (!empty($pays))   $sql .= " AND pays='" . $conn->real_escape_string($pays) . "'";
if (!empty($statut)) $sql .= " AND statut='" . $conn->real_escape_string($statut) . "'";

$result = $conn->query($sql);

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->fromArray(['Nom', 'Prénom', 'Email', 'Pays', 'Statut', 'Date'], NULL, 'A1');
$rowIndex = 2;
while ($row = $result->fetch_assoc()) {
    $sheet->fromArray(array_values($row), NULL, 'A' . $rowIndex++);
}

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename=\"demandes_visa.xlsx\"');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;













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
    .scrollable-table {
      max-height: 300px;
      overflow-y: auto;
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
      <li><a href="destinations.html">Destinations</a></li>
      <li><a href="contact.html">Contact</a></li>
      <li><a href="suivi.php">Suivi</a></li>
      <li><a href="dashboard_stats.php">Statistiques</a></li>
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

$filtrePays = isset($_GET['pays']) ? $_GET['pays'] : '';
$filtreStatut = isset($_GET['statut']) ? $_GET['statut'] : '';

$total = 0;
$accepte = 0;
$refuse = 0;
$attente = 0;

$sql = "SELECT statut, COUNT(*) as count FROM demandes_visa GROUP BY statut";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $total += $row['count'];
    if ($row['statut'] === 'accepté') $accepte = $row['count'];
    if ($row['statut'] === 'refusé') $refuse = $row['count'];
    if ($row['statut'] === 'en attente') $attente = $row['count'];
}

$pays_data = [];
$sqlPays = "SELECT pays, COUNT(*) as count FROM demandes_visa GROUP BY pays";
$resultPays = $conn->query($sqlPays);
while ($row = $resultPays->fetch_assoc()) {
    $pays_data[$row['pays']] = $row['count'];
}

$motifs_data = [];
$sqlMotifs = "SELECT motif_refus, COUNT(*) as count FROM demandes_visa WHERE statut='refusé' GROUP BY motif_refus";
$resultMotifs = $conn->query($sqlMotifs);
while ($row = $resultMotifs->fetch_assoc()) {
    $motifs_data[$row['motif_refus']] = $row['count'];
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
  <div class="table-filters" style="display: flex; justify-content: space-between; margin-bottom: 20px;">
    <form method="GET" style="display: flex; gap: 10px;">
      <select name="pays">
        <option value="">Tous les pays</option>
        <?php
        $paysList = $conn->query("SELECT DISTINCT pays FROM demandes_visa");
        while ($p = $paysList->fetch_assoc()) {
          $selected = $filtrePays === $p['pays'] ? 'selected' : '';
          echo "<option value='{$p['pays']}' $selected>{$p['pays']}</option>";
        }
        ?>
      </select>
      <select name="statut">
        <option value="">Tous les statuts</option>
        <option value="accepté" <?= $filtreStatut === 'accepté' ? 'selected' : '' ?>>Accepté</option>
        <option value="refusé" <?= $filtreStatut === 'refusé' ? 'selected' : '' ?>>Refusé</option>
        <option value="en attente" <?= $filtreStatut === 'en attente' ? 'selected' : '' ?>>En attente</option>
      </select>
      <button type="submit">Filtrer</button>
    </form>
    <form method="POST" action="export_excel.php">
      <input type="hidden" name="pays" value="<?= htmlspecialchars($filtrePays) ?>">
      <input type="hidden" name="statut" value="<?= htmlspecialchars($filtreStatut) ?>">
      <button type="submit">📅 Exporter Excel</button>
    </form>
  </div>

  <div class="scrollable-table">
    <table>
      <thead>
        <tr>
          <th>Nom</th>
          <th>Prénom</th>
          <th>Email</th>
          <th>Pays</th>
          <th>Statut</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sqlTable = "SELECT nom, prenom, email, pays, statut, date_demande FROM demandes_visa WHERE 1";
        if (!empty($filtrePays)) $sqlTable .= " AND pays='" . $conn->real_escape_string($filtrePays) . "'";
        if (!empty($filtreStatut)) $sqlTable .= " AND statut='" . $conn->real_escape_string($filtreStatut) . "'";
        $res = $conn->query($sqlTable);
        while ($row = $res->fetch_assoc()) {
          echo "<tr>
            <td>{$row['nom']}</td>
            <td>{$row['prenom']}</td>
            <td>{$row['email']}</td>
            <td>{$row['pays']}</td>
            <td>{$row['statut']}</td>
            <td>{$row['date_demande']}</td>
          </tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</div>

<script>
const stats = {
  total: <?php echo $total; ?>,
  accepte: <?php echo $accepte; ?>,
  refuse: <?php echo $refuse; ?>,
  attente: <?php echo $attente; ?>,
  pays: <?php echo json_encode($pays_data); ?>,
  motifs: <?php echo json_encode($motifs_data); ?>
};

new Chart(document.getElementById("chartStatut"), {
  type: 'doughnut',
  data: {
    labels: ['Accepté', 'Refusé', 'En attente'],
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
    datasets: [{
      label: 'Nombre de demandes',
      data: Object.values(stats.pays),
      backgroundColor: '#007BFF'
    }]
  }
});

new Chart(document.getElementById("chartMotifs"), {
  type: 'bar',
  data: {
    labels: Object.keys(stats.motifs),
    datasets: [{
      label: 'Motifs de refus',
      data: Object.values(stats.motifs),
      backgroundColor: '#dc3545'
    }]
  },
  options: {
    indexAxis: 'y'
  }
});
</script>

</body>
</html>