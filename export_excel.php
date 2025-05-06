<?php
// Wajib baris pertama, tidak boleh ada spasi/baris kosong di atasnya
ob_start(); // Aktifkan output buffering

include 'db.php';

$search = $conn->real_escape_string($_GET['search'] ?? '');
$tanggal_dari = $_GET['tanggal_dari'] ?? null;
$tanggal_sampai = $_GET['tanggal_sampai'] ?? null;
$UserGroup = $_GET['usergroup'] ?? '';

// Format nama file
$today = date('d-m-Y_H-i-s');

// HEADER harus dikirim sebelum ada output apapun
header("Content-Type: application/vnd-ms-excel");
header("Content-Disposition: attachment; filename=report-komplain-$today.xls");

// Base SQL
$sql = "SELECT * FROM komplain WHERE 1=1";

if (!empty($UserGroup)) {
    $UserGroup = $conn->real_escape_string($UserGroup);
    $sql .= " AND jenis_komplain LIKE '$UserGroup%'";
}

if (!empty($search)) {
    $like_search = "%$search%";
    $sql .= " AND (
        id_tiket LIKE '$like_search' 
        OR tanggal LIKE '$like_search' 
        OR jenis_komplain LIKE '$like_search' 
        OR keterangan LIKE '$like_search' 
        OR user_komplain LIKE '$like_search' 
        OR cabang LIKE '$like_search' 
        OR teknisi LIKE '$like_search'
        OR status LIKE '$like_search'
    )";
}

if (!empty($tanggal_dari) && !empty($tanggal_sampai)) {
    $tanggal_dari = $conn->real_escape_string($tanggal_dari);
    $tanggal_sampai = $conn->real_escape_string($tanggal_sampai);
    $sql .= " AND tanggal BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";
}

$sql .= " ORDER BY id_tiket DESC";
$result = $conn->query($sql);

// Output ke Excel (sekarang aman)
echo "<table border='1'>
<tr style='background-color: #f2f2f2; font-weight: bold;'>
    <th>ID Tiket</th>
    <th>Tanggal</th>
    <th>Jenis Komplain</th>
    <th>Keterangan</th>
    <th>User</th>
    <th>Cabang</th>
    <th>Teknisi</th>
    <th>Status</th>
    <th>File</th>
    <th>Keterangan Pembatalan</th>
</tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
        <td>{$row['id_tiket']}</td>
        <td>{$row['tanggal']}</td>
        <td>{$row['jenis_komplain']}</td>
        <td>{$row['keterangan']}</td>
        <td>{$row['user_komplain']}</td>
        <td>{$row['cabang']}</td>
        <td>" . ($row['teknisi'] ?? 'Belum Ada') . "</td>
        <td>" . ($row['status'] ?? 'Pending') . "</td>
        <td>" . ($row['file'] ?? 'Tidak ada file') . "</td>
        <td>" . ($row['ket'] ?? '-') . "</td>
    </tr>";
}

echo "</table>";
$conn->close();

ob_end_flush(); // Kirim semua output yang dibuffer
?>
