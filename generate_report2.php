<?php
require('fpdf/fpdf.php');

// Memulai session
session_start();

// Jika pengguna belum login, redirect ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Meng-include file koneksi ke database
include 'koneksi.php';

// Query untuk mendapatkan data statistik
$sql_total_penjualan = "SELECT SUM(total_harga) AS total_penjualan FROM penjualan";
$result_total_penjualan = $conn->query($sql_total_penjualan);
$total_penjualan = $result_total_penjualan->fetch_assoc()['total_penjualan'];

$sql_jumlah_produk = "SELECT COUNT(*) AS jumlah_produk FROM barang";
$result_jumlah_produk = $conn->query($sql_jumlah_produk);
$jumlah_produk = $result_jumlah_produk->fetch_assoc()['jumlah_produk'];

$sql_total_produk_terjual = "SELECT SUM(produk_terjual) AS total_produk_terjual FROM penjualan";
$result_total_produk_terjual = $conn->query($sql_total_produk_terjual);
$total_produk_terjual = $result_total_produk_terjual->fetch_assoc()['total_produk_terjual'];

// Membuat instance FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Set font
$pdf->SetFont('Arial', 'B', 12);

// Tambahkan judul
$pdf->Cell(0, 10, 'Laporan Dashboard Penjualan', 0, 1, 'C');

// Set font untuk card statistik
$pdf->SetFont('Arial', 'B', 10);
$pdf->Ln(10);

// Card Total Penjualan
$pdf->Cell(0, 10, 'Total Penjualan: Rp. ' . number_format($total_penjualan, 0, ',', '.'), 0, 1);
$pdf->Ln();

// Card Jumlah Produk
$pdf->Cell(0, 10, 'Jumlah Produk: ' . $jumlah_produk, 0, 1);
$pdf->Ln();

// Card Total Produk Terjual
$pdf->Cell(0, 10, 'Total Produk Terjual: ' . number_format($total_produk_terjual), 0, 1);
$pdf->Ln(10);

// Output PDF
$pdf->Output('I', 'report.pdf');

// Tutup koneksi database
$conn->close();
?>