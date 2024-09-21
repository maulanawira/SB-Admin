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

if (isset($_GET['id_barang'])) {
    $id_barang = $_GET['id_barang'];
    
    // Cek apakah ID Barang ada di database
    $sql = "SELECT * FROM barang WHERE id_barang = '$id_barang'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        // Hapus barang dari database
        $sql = "DELETE FROM barang WHERE id_barang = '$id_barang'";
        if ($conn->query($sql) === TRUE) {
            $message = "Berhasil menghapus data barang.";
            $status = "success";
        } else {
            $message = "Error: " . $conn->error;
            $status = "error";
        }
    } else {
        $message = "ID Barang tidak ditemukan.";
        $status = "error";
    }
} else {
    $message = "ID Barang tidak diset.";
    $status = "error";
}

header("Location: admin.php?message=" . urlencode($message) . "&status=" . urlencode($status));
exit();
?>