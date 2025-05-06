<?php
include 'db.php';

header('Content-Type: application/json');

$start = $_POST['start'] ?? null;
$end = $_POST['end'] ?? null;
$cabang = $_POST['cabang'] ?? 'all';
$status = $_POST['status'] ?? 'all';
$user_group = $_POST['user_group'] ?? '';

$params = [];
$whereClauses = [];

// Filter by date range if provided
if ($start && $end) {
    $whereClauses[] = "tanggal BETWEEN ? AND ?";
    $params[] = $start;
    $params[] = $end;
}

// Filter by cabang if not 'all'
if ($cabang !== 'all') {
    $whereClauses[] = "cabang = ?";
    $params[] = $cabang;
}

// Filter by status if not 'all'
if ($status !== 'all') {
    $whereClauses[] = "status = ?";
    $params[] = $status;
}

if ($user_group !== '') {
    // Define mapping of user_group to jenis_komplain prefix
    $mapping = [
        'it' => 'IT - ',
        'buyer' => 'Buyer - ',
        'accounting' => 'Accounting - ',
        'op' => 'Op - ',
        'hr' => 'HR - ',
        'gudang' => 'Gudang - ',
        'ceo' => 'CEO - ',
        'audit' => 'Audit - ',
    ];

    if (array_key_exists(strtolower($user_group), $mapping)) {
        $prefix = $mapping[strtolower($user_group)];
        $whereClauses[] = "jenis_komplain LIKE ?";
        $params[] = $prefix . '%';
    }
}

$whereSql = '';
if (count($whereClauses) > 0) {
    $whereSql = 'WHERE ' . implode(' AND ', $whereClauses);
}

// Get pagination parameters
$page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
$limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
$offset = ($page - 1) * $limit;

// First, get total count for pagination
$countSql = "SELECT COUNT(*) as total FROM komplain $whereSql";
$countStmt = $conn->prepare($countSql);
if ($countStmt === false) {
    echo json_encode(['error' => 'Failed to prepare count statement']);
    exit;
}
if (count($params) > 0) {
    $types = str_repeat('s', count($params));
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$countResult = $countStmt->get_result();
$totalRow = $countResult->fetch_assoc();
$total = $totalRow['total'] ?? 0;
$countStmt->close();

// Now, get paginated data
$sql = "SELECT id_tiket, tanggal, cabang, jenis_komplain, teknisi, status, file_path
        FROM komplain
        $whereSql
        ORDER BY id_tiket DESC
        LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare statement']);
    exit;
}

// Bind parameters dynamically including limit and offset
$bindParams = $params;
$bindTypes = str_repeat('s', count($params)) . 'ii';
$bindParams[] = $limit;
$bindParams[] = $offset;

$stmt->bind_param($bindTypes, ...$bindParams);

$stmt->execute();
$result = $stmt->get_result();

$complaints = [];
while ($row = $result->fetch_assoc()) {
    $complaints[] = $row;
}

echo json_encode(['complaints' => $complaints, 'total' => $total]);
?>
