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

$message = "";
$status = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = $_POST['nama_barang'];
    $harga_barang = $_POST['harga_barang'];
    $kategori_barang = $_POST['kategori_barang']; // Mengambil nilai kategori

    // Generate ID Barang
    $kode_awal = "SP"; // Ubah kode awal menjadi SP secara statis

    // Ambil nomor urut terakhir untuk kode 'SP'
    $sql = "SELECT MAX(CAST(SUBSTRING(id_barang, 3) AS UNSIGNED)) AS max_urutan FROM barang WHERE id_barang LIKE '$kode_awal%'";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $nomor_urut = $row['max_urutan'] + 1;
    $id_barang = $kode_awal . str_pad($nomor_urut, 3, '0', STR_PAD_LEFT);

    // Foto
    $foto_barang = $_FILES['foto_barang']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($foto_barang);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    // Check if image file is a actual image or fake image
    $check = getimagesize($_FILES['foto_barang']['tmp_name']);
    if ($check === false) {
        $message = "File is not an image.";
        $status = "error";
        $uploadOk = 0;
    }
    
    // Check file size (limit 5MB)
    if ($_FILES['foto_barang']['size'] > 5000000) {
        $message = "Sorry, your file is too large.";
        $status = "error";
        $uploadOk = 0;
    }
    
    // Allow certain file formats
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        $message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $status = "error";
        $uploadOk = 0;
    }
    
    if ($uploadOk == 0) {
        $message = "Sorry, your file was not uploaded.";
        $status = "error";
    } else {
        if (move_uploaded_file($_FILES['foto_barang']['tmp_name'], $target_file)) {
            $sql = "INSERT INTO barang (id_barang, nama_barang, harga_barang, kategori_barang, foto_barang) 
                    VALUES ('$id_barang', '$nama_barang', '$harga_barang', '$kategori_barang', '$foto_barang')";
            if ($conn->query($sql) === TRUE) {
                $message = "Berhasil menambah data barang";
                $status = "success";
            } else {
                $message = "Error: " . $sql . "<br>" . $conn->error;
                $status = "error";
            }
        } else {
            $message = "Data gagal ditambahkan";
            $status = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Barang - SB Admin</title>
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

        <!-- Main content -->
        <div class="col-md-10 content">
            <div class="container mt-5">
                <h2>Tambah Barang</h2>
                
                <!-- Alert message -->
                <?php if ($message): ?>
                    <script>alert("<?php echo $message; ?>");</script>
                <?php endif; ?>

                <!-- Form add item -->
                <form action="tambahbarang.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang:</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" required>
                    </div>
                    <div class="form-group">
                        <label for="kategori_barang">Kategori Barang:</label>
                        <select class="form-control" id="kategori_barang" name="kategori_barang" required>
                            <option value="Android">Android</option>
                            <option value="iPhone">iPhone</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="harga_barang">Harga:</label>
                        <input type="number" class="form-control" id="harga_barang" name="harga_barang" required>
                    </div>
                    <div class="form-group">
                        <label for="stok_barang">Stok:</label>
                        <input type="number" class="form-control" id="harga_barang" name="stok_barang" required>
                    </div>
                    <div class="form-group">
                        <label for="foto_barang">Foto Barang:</label>
                        <input type="file" class="form-control-file" id="foto_barang" name="foto_barang" required>
                    </div>
                    <!-- Submit button -->
                    <button type="submit" class="btn btn-primary">Tambah Barang</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Script section -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>

<?php
$conn->close(); // Close connection
?>