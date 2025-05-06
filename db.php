<?php
$host = 'srv143.niagahoster.com';     // Host database
$port = '3306';          // Port MySQL
$dbname = 'n1572337_dbretail'; // Nama database
$username = 'n1572337_retail';      // Username database
$password = 'pilus123.';          // Password database (kosong sesuai konfigurasi Anda)

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $dbname, $port);

// Mengecek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}
?>
