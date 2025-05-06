<?php
include 'db.php';

$startDate = $_GET['start_date'] ?? '';
$endDate = $_GET['end_date'] ?? '';
$cabang = $_GET['cabang'] ?? 'all';
$status = $_GET['status'] ?? 'all';

// Base query
$query = "SELECT COUNT(*) AS total FROM komplain WHERE 1=1";

// Filter tanggal
if (!empty($startDate) && !empty($endDate)) {
    $query .= " AND tanggal BETWEEN '$startDate' AND '$endDate'";
}

// Filter cabang
if ($cabang !== 'all') {
    $query .= " AND cabang = '" . $conn->real_escape_string($cabang) . "'";
}

// Filter status
if ($status !== 'all') {
    $query .= " AND status = '" . $conn->real_escape_string($status) . "'";
}

$result = $conn->query($query);
$data = $result->fetch_assoc();

echo $data['total'];
?>
