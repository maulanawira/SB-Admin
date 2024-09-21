<?php
// Memulai session
session_start();

// Jika pengguna belum login, redirect ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Meng-include file koneksi ke database
include 'koneksi.php';

// Mendapatkan jumlah produk yang tersedia di toko
$sql_jumlah_produk = "SELECT COUNT(*) AS jumlah_produk FROM barang";
$result_jumlah_produk = $conn->query($sql_jumlah_produk);
$jumlah_produk = 0;
if ($result_jumlah_produk->num_rows > 0) {
    $row = $result_jumlah_produk->fetch_assoc();
    $jumlah_produk = $row['jumlah_produk'];
}

// Mendapatkan total penjualan
$sql_total_penjualan = "SELECT SUM(total_harga) AS total_penjualan FROM penjualan";
$result_total_penjualan = $conn->query($sql_total_penjualan);
$total_penjualan = 0;
if ($result_total_penjualan->num_rows > 0) {
    $row = $result_total_penjualan->fetch_assoc();
    $total_penjualan = $row['total_penjualan'];
}

// Mendapatkan total produk terjual
$sql_total_produk_terjual = "SELECT SUM(produk_terjual) AS total_produk_terjual FROM penjualan";
$result_total_produk_terjual = $conn->query($sql_total_produk_terjual);
$total_produk_terjual = 0;
if ($result_total_produk_terjual->num_rows > 0) {
    $row = $result_total_produk_terjual->fetch_assoc();
    $total_produk_terjual = $row['total_produk_terjual'];
}

// Mendapatkan data penjualan per produk
$sql_penjualan_per_produk = "
    SELECT barang.nama_barang, barang.kategori_barang, SUM(penjualan.produk_terjual) AS total_terjual 
    FROM penjualan
    JOIN barang ON penjualan.id_barang = barang.id_barang
    GROUP BY penjualan.id_barang";
$result_penjualan_per_produk = $conn->query($sql_penjualan_per_produk);

// Mendapatkan data penjualan per kategori
$sql_penjualan_per_kategori = "
    SELECT barang.kategori_barang, SUM(penjualan.produk_terjual) AS total_terjual 
    FROM penjualan
    JOIN barang ON penjualan.id_barang = barang.id_barang
    GROUP BY barang.kategori_barang";
$result_penjualan_per_kategori = $conn->query($sql_penjualan_per_kategori);

// Menyiapkan data untuk chart
$kategori_data = [];
$jumlah_data = [];
if ($result_penjualan_per_kategori->num_rows > 0) {
    while ($row = $result_penjualan_per_kategori->fetch_assoc()) {
        $kategori_data[] = $row["kategori_barang"];
        $jumlah_data[] = $row["total_terjual"];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SB Admin</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styledashboard.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Gaya khusus untuk canvas pie chart */
        #pieChart {
            width: 100% !important;
            max-width: 400px; /* Sesuaikan ukuran maksimal sesuai kebutuhan */
            height: auto !important;
            margin: auto; /* Tengah-kan canvas jika diinginkan */
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
         <div class="col-md-2 sidebar" style="background-color: blue;">
            <h3 class="text-white text-center py-3">SB Admin</h3>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active text-white" href="dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active text-white" href="admin.php">Product</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="index.php">Logout</a>
                </li>
            </ul>
        </div>

        <!-- Konten Utama -->
        <div class="col-md-10 content">
            <div class="dashboard-header">
                <h2 class="dashboard-title">Dashboard Penjualan</h2>
            </div>

            <!-- Kartu Statistik -->
            <div class="row my-4">
                <div class="col-md-4">
                    <div class="card text-white bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total Penjualan</h5>
                            <p class="card-text">Rp. <?php echo number_format($total_penjualan, 0, ',', '.'); ?></p>
                      </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Variasi</h5>
                            <p class="card-text"><?php echo $jumlah_produk; ?> Produk</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Total Produk Terjual</h5>
                            <p class="card-text"><?php echo number_format($total_produk_terjual); ?> Produk Terjual</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tombol Generate Report-->
            <div class="row mb-4">
                <div class="col-md-12 text-right">
                    <form method="GET" action="generate_report2.php" class="d-inline">
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                        <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">
                        <input type="hidden" name="direction" value="<?php echo htmlspecialchars($direction); ?>">
                        <button class="btn btn-secondary" type="submit">Generate Report</button>
                    </form>
                  
                </div>
            </div>

            <!-- Tabel Penjualan Per Produk -->
            <div class="card mb-4">
                <div class="card-header">
                    Penjualan Per Produk
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Total Terjual</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if ($result_penjualan_per_produk->num_rows > 0) {
                                while ($row = $result_penjualan_per_produk->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row["nama_barang"] . "</td>";
                                    echo "<td>" . $row["kategori_barang"] . "</td>";
                                    echo "<td>" . $row["total_terjual"] . "</td>";
                                    echo "</tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>Tidak ada data penjualan.</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pie Chart -->
            <div class="card mb-4">
                <div class="card-header">
                    Jumlah Produk Terjual Berdasarkan Kategori
                </div>
                <div class="card-body">
                    <canvas id="pieChart"></canvas>
                </div>
            </div>


<!-- jQuery & Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

<script>
    // Ambil data PHP ke dalam JavaScript
    var kategoriData = <?php echo json_encode($kategori_data); ?>;
    var jumlahData = <?php echo json_encode($jumlah_data); ?>;
    
    // Konfigurasi Pie Chart
    var ctx = document.getElementById('pieChart').getContext('2d');
    var pieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: kategoriData,
            datasets: [{
                label: 'Jumlah Produk Terjual',
                data: jumlahData,
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                    'rgba(54, 162, 235, 0.2)',
                    'rgba(255, 206, 86, 0.2)',
                    'rgba(75, 192, 192, 0.2)',
                    'rgba(153, 102, 255, 0.2)',
                    'rgba(255, 159, 64, 0.2)'
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // Membolehkan chart berubah ukuran
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    callbacks: {
                        label: function(tooltipItem) {
                            return tooltipItem.label + ': ' + tooltipItem.raw;
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>

<?php
// Tutup koneksi database
$conn->close();
?>
