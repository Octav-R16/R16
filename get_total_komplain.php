<?php
include 'db.php'; // Koneksi ke database

// Ambil filter dari POST
$start  = $_POST['start'] ?? '';
$end    = $_POST['end'] ?? '';
$cabang = $_POST['cabang'] ?? 'all';
$status = $_POST['status'] ?? 'all';
$usergroup = strtolower(trim($_POST['user_group'] ?? ''));

// Siapkan array kondisi WHERE
$where = [];
$whereTotal = [];

// Filter tanggal
if (!empty($start) && !empty($end)) {
    $startEsc = $conn->real_escape_string($start);
    $endEsc = $conn->real_escape_string($end);
    $where[] = "tanggal BETWEEN '$startEsc' AND '$endEsc'";
    $whereTotal[] = "tanggal BETWEEN '$startEsc' AND '$endEsc'";
}

// Filter cabang
if ($cabang !== 'all') {
    $cabangEsc = $conn->real_escape_string($cabang);
    $where[] = "cabang = '$cabangEsc'";
    $whereTotal[] = "cabang = '$cabangEsc'";
}

// Filter berdasarkan UserGroup yang dipilih yang cocok dengan jenis_komplain
if (!empty($usergroup)) {
    $usergroupEsc = $conn->real_escape_string($usergroup);
    $where[] = "LOWER(jenis_komplain) LIKE '%$usergroupEsc%'";
    $whereTotal[] = "LOWER(jenis_komplain) LIKE '%$usergroupEsc%'";
}

// Filter status (jika bukan 'all')
if ($status !== 'all') {
    $statusEsc = $conn->real_escape_string($status);
    $where[] = "status = '$statusEsc'";
}

// === Hitung total komplain ===
$whereSQLTotal = count($whereTotal) ? 'WHERE ' . implode(' AND ', $whereTotal) : '';
$queryTotal = "SELECT COUNT(*) AS total FROM komplain $whereSQLTotal";
$resultTotal = $conn->query($queryTotal);
$dataTotal = $resultTotal->fetch_assoc();
$total = $dataTotal['total'] ?? 0;

// === Hitung komplain selesai ===
if ($status === 'all' || $status === 'completed') {
    $whereSelesai = $whereTotal;
    $whereSelesai[] = "status = 'completed'";
    $whereSQLSelesai = 'WHERE ' . implode(' AND ', $whereSelesai);
    $querySelesai = "SELECT COUNT(*) AS selesai FROM komplain $whereSQLSelesai";
    $resultSelesai = $conn->query($querySelesai);
    $dataSelesai = $resultSelesai->fetch_assoc();
    $selesai = $dataSelesai['selesai'] ?? 0;
} else {
    $selesai = 0;
}

// === Persentase selesai ===
$persentase = ($total > 0) ? round(($selesai / $total) * 100) : 0;

// === Query Komplain Berjalan (status: on progress) berdasarkan filter ===
if ($status === 'all' || $status === 'on progress') {
    $whereBerjalan = $whereTotal;
    $whereBerjalan[] = "status = 'on progress'";  // Filter status "on progress"
    $whereSQLBerjalan = count($whereBerjalan) ? 'WHERE ' . implode(' AND ', $whereBerjalan) : '';
    $queryBerjalan = "SELECT COUNT(*) AS `on_progress` FROM komplain $whereSQLBerjalan";
    $resultBerjalan = $conn->query($queryBerjalan);
    $dataBerjalan = $resultBerjalan->fetch_assoc();
    $berjalan = $dataBerjalan['on_progress'] ?? 0;
} else {
    $berjalan = 0;
}

// === Query Komplain Pending ===
if ($status === 'all' || $status === 'pending') {
    $wherePending = $whereTotal;
    $wherePending[] = "status = 'pending'";
    $whereSQLPending = count($wherePending) ? 'WHERE ' . implode(' AND ', $wherePending) : '';
    $queryPending = "SELECT COUNT(*) AS pending FROM komplain $whereSQLPending";
    $resultPending = $conn->query($queryPending);
    $dataPending = $resultPending->fetch_assoc();
    $pending = $dataPending['pending'] ?? 0;
} else {
    $pending = 0;
}

// === Query Komplain Tolak (Cancelled) ===
if ($status === 'all' || $status === 'cancelled') {
    $whereTolak = $whereTotal;
    $whereTolak[] = "LOWER(TRIM(status)) = 'cancelled'";
    $whereSQLTolak = 'WHERE ' . implode(' AND ', $whereTolak);
    $queryTolak = "SELECT COUNT(*) AS cancelled FROM komplain $whereSQLTolak";
    $resultTolak = $conn->query($queryTolak);
    $dataTolak = $resultTolak ? $resultTolak->fetch_assoc() : [];
    $tolak = $dataTolak['cancelled'] ?? 0;
} else {
    $tolak = 0;
}

// === Output JSON lengkap ===
echo json_encode([
    'total' => $total,
    'selesai' => $selesai,
    'persentase' => $persentase,
    'berjalan' => $berjalan,
    'pending' => $pending,
    'tolak' => $tolak
]);

?>
