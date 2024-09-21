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

// Ambil ID Barang dari parameter URL
if (isset($_GET['id_barang'])) {
    $id_barang = $_GET['id_barang'];

    // Ambil data barang berdasarkan ID
    $sql = "SELECT * FROM barang WHERE id_barang = '$id_barang'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $barang = $result->fetch_assoc();
    } else {
        $message = "ID Barang tidak ditemukan.";
        $status = "error";
    }
} else {
    $message = "ID Barang tidak diset.";
    $status = "error";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_barang = $_POST['nama_barang'];
    $harga_barang = $_POST['harga_barang'];
    $stok_barang = $_POST['stok_barang'];

    // Foto
    $foto_barang = $_FILES['foto_barang']['name'];
    if ($foto_barang) {
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
                // Update foto_barang
                $foto_update = $foto_barang;
            } else {
                $message = "Sorry, there was an error uploading your file.";
                $status = "error";
            }
        }
    } else {
        // Jika foto tidak diupload, gunakan foto yang lama
        $foto_update = $barang['foto_barang'];
    }

    // Update data barang
    $sql = "UPDATE barang SET nama_barang='$nama_barang', harga_barang='$harga_barang', stok_barang='$stok_barang', foto_barang='$foto_update' WHERE id_barang='$id_barang'";
    if ($conn->query($sql) === TRUE) {
        $message = "Berhasil mengupdate data barang.";
        $status = "success";
        
        // Redirect ke admin
        header("Location: admin.php");
        exit();
    } else {
        $message = "Error: " . $conn->error;
        $status = "error";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Barang - SB Admin</title>
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
                <h2>Edit Barang</h2>

                <!-- Alert messages -->
                <?php if ($message): ?>
                    <script>alert("<?php echo $message; ?>");</script>
                <?php endif; ?>

                <!-- Display success/error -->
                <?php if ($status === "success"): ?>
                    <div class="alert alert-success" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php elseif ($status === "error"): ?>
                    <div class="alert alert-danger" role="alert">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <!-- Edit form -->
                <?php if ($barang): ?>
                <form action="edit_barang.php?id_barang=<?php echo htmlspecialchars($id_barang); ?>" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang:</label>
                        <input type="text" class="form-control" id="nama_barang" name="nama_barang" value="<?php echo htmlspecialchars($barang['nama_barang']); ?>" required>
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
                        <input type="number" class="form-control" id="harga_barang" name="harga_barang" value="<?php echo htmlspecialchars($barang['harga_barang']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="stok_barang">Stok:</label>
                        <input type="number" class="form-control" id="stok_barang" name="stok_barang" value="<?php echo htmlspecialchars($barang['stok_barang']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="foto_barang">Foto Barang:</label>
                        <input type="file" class="form-control-file" id="foto_barang" name="foto_barang">
                        <!-- Display current image -->
                        <?php if ($barang['foto_barang']): ?>
                            <img src="uploads/<?php echo htmlspecialchars($barang['foto_barang']); ?>" class="img-thumbnail mt-3" style="max-width: 200px;">
                        <?php endif; ?>
                    </div>
                    <!-- Update button -->
                    <button type="submit" class="btn btn-primary">Update Barang</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Script section -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>

</body>
</html>