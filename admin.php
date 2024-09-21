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

// Pencarian dan pengurutan data
$search = isset($_GET['search']) ? $_GET['search'] : '';
$order = isset($_GET['order']) ? $_GET['order'] : 'id_barang';
$direction = isset($_GET['direction']) ? $_GET['direction'] : 'ASC';

// Validasi kolom pengurutan
$allowed_orders = ['id_barang', 'nama_barang', 'harga_barang', 'kategori_barang', 'stok_barang'];
if (!in_array($order, $allowed_orders)) {
    $order = 'id_barang';
}
$direction = strtoupper($direction) === 'DESC' ? 'DESC' : 'ASC';

// Query database
$sql = "SELECT * FROM barang WHERE 
        id_barang LIKE '%$search%' OR 
        nama_barang LIKE '%$search%' OR 
        harga_barang LIKE '%$search%' OR 
        kategori_barang LIKE '%$search%' OR
        stok_barang LIKE '%$search%'
        ORDER BY $order $direction";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SB Admin</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styledashboard.css">
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
                <h2 class="dashboard-title">SB Admin</h2>
                <div class="actions-row">
                    <!-- Tombol Tambah Produk -->
                    <a href="tambahbarang.php" class="btn btn-success">Tambah Produk</a>

                    <!-- Form Pencarian -->
                    <form method="GET" action="" class="search-bar">
                        <div class="input-group">   
                            <input type="text" class="form-control" name="search" placeholder="Cari produk..." value="<?php echo htmlspecialchars($search); ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Cari</button>
                            </div>
                        </div>
                        <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">
                        <input type="hidden" name="direction" value="<?php echo htmlspecialchars($direction); ?>">
                    </form>
                </div>
               <!-- Tombol Generate Report -->
                <form method="GET" action="generate_report.php" class="mt-2 d-flex justify-content-end">
                    <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <input type="hidden" name="order" value="<?php echo htmlspecialchars($order); ?>">
                    <input type="hidden" name="direction" value="<?php echo htmlspecialchars($direction); ?>">
                    <button class="btn btn-secondary" type="submit">Generate Report</button>
                </form>
            </div>

            <!-- Tabel Produk -->
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>No</th>
                        <th>Thumbnail</th>
                        <th><a href="?search=<?php echo urlencode($search); ?>&order=id_barang&direction=<?php echo $direction === 'ASC' ? 'DESC' : 'ASC'; ?>">Id Produk <span class="sort-icon"><?php echo $order === 'id_barang' ? ($direction === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
                        <th><a href="?search=<?php echo urlencode($search); ?>&order=nama_barang&direction=<?php echo $direction === 'ASC' ? 'DESC' : 'ASC'; ?>">Produk <span class="sort-icon"><?php echo $order === 'nama_barang' ? ($direction === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
                        <th><a href="?search=<?php echo urlencode($search); ?>&order=kategori_barang&direction=<?php echo $direction === 'ASC' ? 'DESC' : 'ASC'; ?>">Kategori <span class="sort-icon"><?php echo $order === 'kategori_barang' ? ($direction === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
                        <th><a href="?search=<?php echo urlencode($search); ?>&order=harga_barang&direction=<?php echo $direction === 'ASC' ? 'DESC' : 'ASC'; ?>">Harga <span class="sort-icon"><?php echo $order === 'harga_barang' ? ($direction === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
                        <th><a href="?search=<?php echo urlencode($search); ?>&order=stok_barang&direction=<?php echo $direction === 'ASC' ? 'DESC' : 'ASC'; ?>">Stok <span class="sort-icon"><?php echo $order === 'stok_barang' ? ($direction === 'ASC' ? '▲' : '▼') : ''; ?></span></a></th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
    <?php
    if ($result->num_rows > 0) {
        $no = 1;
        while ($row = $result->fetch_assoc()) {
            // Format harga
            $harga_barang = number_format($row["harga_barang"], 0, ',', '.');

            echo "<tr>";
            echo "<td>" . $no . "</td>";
            echo "<td><img src='uploads/" . $row["foto_barang"] . "' class='thumbnail'></td>";
            echo "<td>" . $row["id_barang"] . "</td>";
            echo "<td>" . $row["nama_barang"] . "</td>";
            echo "<td>" . $row["kategori_barang"] . "</td>";
            echo "<td>Rp. " . $harga_barang . "</td>"; // Harga format Rupiah
            echo "<td>" . $row["stok_barang"] . "</td>";
            echo "<td>";
            echo "<a href='edit_barang.php?id_barang=" . $row["id_barang"] . "' class='btn btn-warning btn-sm'>Edit</a> ";
            echo "<a href='hapus_barang.php?id_barang=" . $row["id_barang"] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Apakah Anda yakin ingin menghapus produk ini?\")'>Hapus</a>";
            echo "</td>";
            echo "</tr>";
            $no++;
        }
    } else {
        echo "<tr><td colspan='8' class='text-center'>No data found</td></tr>";
    }
    ?>
</tbody>

                </table>
            </div>
        </div>
    </div>
</div>

<!-- jQuery & Bootstrap JS -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
// Tutup koneksi database
$conn->close();
?>
