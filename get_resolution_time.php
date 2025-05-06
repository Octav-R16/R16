<?php
include 'db.php'; // koneksi database

$usergroup = strtolower(trim($_POST['user_group'] ?? $_SESSION['UserGroup'] ?? ''));

// Ambil filter dari POST
$start  = $_POST['start'] ?? '';
$end    = $_POST['end'] ?? '';
$cabang = $_POST['cabang'] ?? 'all';
$status = $_POST['status'] ?? 'all';
$usergroup = strtolower(trim($_POST['user_group'] ?? ''));

// Inisialisasi response kosong
$response = [
    'kurang_1_jam' => 0,
    'antara_1_4_jam' => 0,
    'antara_4_8_jam' => 0,
    'antara_8_24_jam' => 0,
    'lebih_24_jam' => 0
];

// Buat WHERE dinamis
$where = [];
$params = [];
$types = "";

// Filter tanggal
if (!empty($start) && !empty($end)) {
    $where[] = "tanggal BETWEEN ? AND ?";
    $params[] = $start;
    $params[] = $end;
    $types .= "ss";
}

// Filter cabang (kecuali "all")
if ($cabang !== 'all') {
    $where[] = "cabang = ?";
    $params[] = $cabang;
    $types .= "s";
}

// Filter user_group ke jenis_komplain
if (!empty($usergroup)) {
    $where[] = "LOWER(jenis_komplain) LIKE ?";
    $params[] = "%$usergroup%";
    $types .= "s";
}

// Filter status (kecuali "all")
if ($status !== 'all') {
    $where[] = "status = ?";
    $params[] = $status;
    $types .= "s";
}

// Gabungkan WHERE
$whereSql = count($where) ? "WHERE " . implode(' AND ', $where) : "";

// Query ambil data
$sql = "
    SELECT 
        SUM(CASE WHEN TIME_TO_SEC(calculate_date) < 3600 THEN 1 ELSE 0 END) AS kurang_1_jam,
        SUM(CASE WHEN TIME_TO_SEC(calculate_date) BETWEEN 3600 AND 14400 THEN 1 ELSE 0 END) AS antara_1_4_jam,
        SUM(CASE WHEN TIME_TO_SEC(calculate_date) BETWEEN 14401 AND 28800 THEN 1 ELSE 0 END) AS antara_4_8_jam,
        SUM(CASE WHEN TIME_TO_SEC(calculate_date) BETWEEN 28801 AND 86400 THEN 1 ELSE 0 END) AS antara_8_24_jam,
        SUM(CASE WHEN TIME_TO_SEC(calculate_date) > 86400 THEN 1 ELSE 0 END) AS lebih_24_jam
    FROM komplain
    $whereSql
";

// Debug cek query? aktifkan saat perlu
// echo $sql; print_r($params);

$stmt = $conn->prepare($sql);

if ($stmt) {
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $row = $result->fetch_assoc()) {
        $response = array_map('intval', $row);
    }
}

echo json_encode($response);
?>
