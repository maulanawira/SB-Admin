<?php
require('fpdf/fpdf.php'); // Sesuaikan path dengan lokasi file FPDF Anda

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

// Membuat instance FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Set font
$pdf->SetFont('Arial', 'B', 12);

// Tambahkan judul
$pdf->Cell(0, 10, 'Laporan Produk', 0, 1, 'C');

// Set font untuk tabel
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(10, 10, 'No', 1);
$pdf->Cell(40, 10, 'ID Produk', 1);
$pdf->Cell(60, 10, 'Nama Produk', 1);
$pdf->Cell(30, 10, 'Kategori', 1);
$pdf->Cell(30, 10, 'Harga', 1);
$pdf->Cell(20, 10, 'Stok', 1);
$pdf->Ln();

// Isi tabel
$pdf->SetFont('Arial', '', 10);
if ($result->num_rows > 0) {
    $no = 1;
    while ($row = $result->fetch_assoc()) {
        $pdf->Cell(10, 10, $no, 1);
        $pdf->Cell(40, 10, $row["id_barang"], 1);
        $pdf->Cell(60, 10, $row["nama_barang"], 1);
        $pdf->Cell(30, 10, $row["kategori_barang"], 1);
        $pdf->Cell(30, 10, 'Rp. ' . number_format($row["harga_barang"], 0, ',', '.'), 1);
        $pdf->Cell(20, 10, $row["stok_barang"], 1);
        $pdf->Ln();
        $no++;
    }
} else {
    $pdf->Cell(0, 10, 'No data found', 1, 1, 'C');
}

// Output PDF
$pdf->Output('I', 'report.pdf');

// Tutup koneksi database
$conn->close();
?>