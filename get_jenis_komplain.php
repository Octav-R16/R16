<?php
session_start();
include 'db.php'; // Koneksi ke database


$usergroup = strtolower(trim($_POST['user_group'] ?? $_SESSION['UserGroup'] ?? ''));

// Ambil filter dari POST
$start  = $_POST['start'] ?? '';
$end    = $_POST['end'] ?? '';
$cabang = $_POST['cabang'] ?? 'all';
$status = $_POST['status'] ?? 'all';
$usergroup = strtolower(trim($_POST['user_group'] ?? ''));

// Validasi tanggal (optional)
$where = [];
if (!empty($start) && !empty($end)) {
    $startEsc = $conn->real_escape_string($start);
    $endEsc = $conn->real_escape_string($end);
    $where[] = "tanggal BETWEEN '$startEsc' AND '$endEsc'";
}

// Filter cabang
if ($cabang !== 'all') {
    $cabangEsc = $conn->real_escape_string($cabang);
    $where[] = "cabang = '$cabangEsc'";
}

// Filter status
if ($status !== 'all') {
    $statusEsc = $conn->real_escape_string($status);
    $where[] = "status = '$statusEsc'";
}

// Filter jenis_komplain berdasarkan usergroup prefix
if (!empty($usergroup)) {
    $usergroupEsc = $conn->real_escape_string($usergroup);
    // Filter jenis_komplain starting with usergroup prefix (case-insensitive)
    $where[] = "LOWER(jenis_komplain) LIKE '{$usergroupEsc} - %'";
}

$whereSQL = count($where) ? 'WHERE ' . implode(' AND ', $where) : '';

if (!empty($usergroup)) {
    $usergroupEsc = $conn->real_escape_string($usergroup);
    $query = "
        SELECT 
            TRIM(SUBSTRING(jenis_komplain, LENGTH('{$usergroupEsc} - ') + 1)) AS jenis_sub,
            COUNT(*) AS count
        FROM komplain
        $whereSQL
        GROUP BY jenis_sub
        ORDER BY count DESC
    ";
} else {
    $query = "
        SELECT 
            jenis_komplain AS jenis_sub,
            COUNT(*) AS count
        FROM komplain
        $whereSQL
        GROUP BY jenis_sub
        ORDER BY count DESC
    ";
}

$result = $conn->query($query);

$labels = [];
$data = [];

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $labels[] = $row['jenis_sub'];
        $data[] = (int)$row['count'];
    }
}

echo json_encode([
    'labels' => $labels,
    'data' => $data
]);
?>
