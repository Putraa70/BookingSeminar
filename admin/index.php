<?php
require_once "../auth/auth_check.php";
checkAuth(['admin']);
require_once "../config/database.php";
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Dashboard Admin - Booking Seminar</title>
  <link href="../assets/css/style.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
</head>
<body>
<?php include "../includes/admin_header.php"; ?>
<?php include "../includes/admin_sidebar.php"; ?>

<main id="mainContent" class="content">
  <div class="container-fluid">

    <div class="row mb-5">
      <!-- Bagian ATAS: Jadwal Seminar Sudah Terisi -->
      <div class="col-12 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-primary text-white">
            <i class="bi bi-calendar-event me-2"></i> Jadwal Seminar Sudah Terisi
          </div>
          <div class="card-body p-2">
            <div class="table-responsive">
              <table class="table table-striped table-sm align-middle mb-0">
                <thead>
                  <tr>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Peserta</th>
                    <th>Judul</th>
                    <th>Pembimbing</th>
                    <th>Penguji</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                $sql = "
                  SELECT s.tanggal, 
                         CONCAT(s.waktu_mulai, ' - ', s.waktu_selesai) AS jam, 
                         u.nama AS peserta,
                         s.judul,
                         (SELECT dp.nama FROM mahasiswa_dosen md
                            JOIN users dp ON md.id_dosen = dp.id
                            WHERE md.id_mahasiswa = u.id AND md.jenis_dosen = 'pembimbing' LIMIT 1) AS pembimbing,
                         (SELECT du.nama FROM mahasiswa_dosen md
                            JOIN users du ON md.id_dosen = du.id
                            WHERE md.id_mahasiswa = u.id AND md.jenis_dosen = 'penguji' LIMIT 1) AS penguji
                    FROM seminars s
                    LEFT JOIN users u ON s.id_user = u.id
                   WHERE s.status = 'approved'
                   ORDER BY s.tanggal ASC, s.waktu_mulai ASC
                ";
                $stmt = $pdo->query($sql);
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if(empty($rows)) {
                  echo '<tr><td colspan="6" class="text-center text-muted">Belum ada seminar terjadwal.</td></tr>';
                } else {
                  foreach($rows as $row) {
                    echo "<tr>
                      <td>{$row['tanggal']}</td>
                      <td>{$row['jam']}</td>
                      <td>{$row['peserta']}</td>
                      <td>{$row['judul']}</td>
                      <td>{$row['pembimbing']}</td>
                      <td>{$row['penguji']}</td>
                    </tr>";
                  }
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>

      <!-- Bagian BAWAH: Jadwal Seminar Belum Terisi (7 hari ke depan, 4 slot per hari) -->
      <div class="col-12 mb-4">
        <div class="card shadow-sm">
          <div class="card-header bg-warning text-dark">
            <i class="bi bi-calendar-x me-2"></i> Jadwal Seminar Belum Terisi (7 hari ke depan)
          </div>
          <div class="card-body p-2">
            <div class="table-responsive">
              <table class="table table-striped table-sm align-middle mb-0">
                <thead>
                  <tr>
                    <th>Tanggal</th>
                    <th>Jam Mulai</th>
                  </tr>
                </thead>
                <tbody>
                <?php
                // Slot waktu 7 hari ke depan, jam: 08:00, 10:00, 13:00, 15:00
                $today = date('Y-m-d');
                $slots = [];
                for($i = 0; $i < 7; $i++) {
                  $tgl = date('Y-m-d', strtotime("+$i days"));
                  foreach(['08:00:00','10:00:00','13:00:00','15:00:00'] as $jam) {
                    $slots[] = ['tanggal'=>$tgl, 'waktu_mulai'=>$jam];
                  }
                }
                // Query seminar yang sudah approve
                $booked = [];
                $stmt2 = $pdo->query("SELECT tanggal, waktu_mulai FROM seminars WHERE status='approved'");
                while($row = $stmt2->fetch(PDO::FETCH_ASSOC)) {
                  $booked[$row['tanggal'].'_'.$row['waktu_mulai']] = true;
                }
                $emptyFound = false;
                foreach($slots as $s) {
                  $key = $s['tanggal'].'_'.$s['waktu_mulai'];
                  if(empty($booked[$key])) {
                    $emptyFound = true;
                    echo "<tr>
                      <td>{$s['tanggal']}</td>
                      <td>{$s['waktu_mulai']}</td>
                    </tr>";
                  }
                }
                if(!$emptyFound) {
                  echo '<tr><td colspan="2" class="text-center text-muted">Semua slot sudah terisi.</td></tr>';
                }
                ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
</main>


<?php include "../includes/footer.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
