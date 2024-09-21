<?php
// Koneksi ke database
$host = 'localhost'; // Nama host
$dbname = 'data'; // Nama database
$username_db = 'root'; // Username database
$password_db = ''; // Password database

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username_db, $password_db);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi ke database gagal: " . $e->getMessage());
}

// Proses saat form pendaftaran disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Mengambil data dari form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validasi sederhana untuk memastikan data diisi
    if (!empty($username) && !empty($password)) {
        // Hashing password menggunakan password_hash()
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // SQL untuk memasukkan data user baru
        $sql = "INSERT INTO user (username, password) VALUES (:username, :password)";
        $stmt = $conn->prepare($sql);

        // Bind parameter
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $hashed_password);

        // Eksekusi query
        if ($stmt->execute()) {
            // Redirect ke dashboard setelah berhasil mendaftar
            header('Location: dashboard.php');
            exit();
        } else {
            echo "Pendaftaran gagal. Silakan coba lagi.";
        }
    } else {
        echo "Username dan password tidak boleh kosong!";
    }
}
?>

<!-- Form Pendaftaran -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Akun</title>
</head>
<body>
    <h2>Form Pendaftaran</h2>
    <form action="" method="POST">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required><br><br>

        <input type="submit" value="Daftar">
    </form>
</body>
</html>
