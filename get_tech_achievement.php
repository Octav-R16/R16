<?php
session_start();
include 'db.php'; // Koneksi ke database



$usergroup = strtolower(trim($_POST['user_group'] ?? $_SESSION['UserGroup'] ?? ''));

// Ambil filter dari POST (optional)
$start  = $_POST['start'] ?? '';
$end    = $_POST['end'] ?? '';
$cabang = $_POST['cabang'] ?? 'all';
$status = $_POST['status'] ?? 'all';

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

if (!empty($status) && $status !== 'all') {
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

// Query untuk menghitung jumlah komplain per jenis_komplain dan teknisi sesuai filter dan usergroup
$query = "
    SELECT 
        jenis_komplain,
        teknisi,
        COUNT(*) AS count
    FROM komplain
    $whereSQL
    GROUP BY jenis_komplain, teknisi
    ORDER BY jenis_komplain, teknisi
";

$result = $conn->query($query);

$jenisKomplainSet = [];
$teknisiSet = [];
$dataMap = []; // [jenis_komplain][teknisi] = count

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $jenis = $row['jenis_komplain'];
        $teknisiStr = $row['teknisi'];
        $count = (int)$row['count'];

        if (!in_array($jenis, $jenisKomplainSet)) {
            $jenisKomplainSet[] = $jenis;
        }

        // Split teknisi string by comma or space and trim each name
        $teknisiList = preg_split('/[\s,]+/', $teknisiStr, -1, PREG_SPLIT_NO_EMPTY);
        foreach ($teknisiList as $teknisi) {
            if (!in_array($teknisi, $teknisiSet)) {
                $teknisiSet[] = $teknisi;
            }
            if (!isset($dataMap[$jenis][$teknisi])) {
                $dataMap[$jenis][$teknisi] = 0;
            }
            $dataMap[$jenis][$teknisi] += $count;
        }
    }
}

$datasets = [];
$colors = [
    '#4361ee', '#4cc9f0', '#7209b7', '#f72585', '#adb5bd',
    '#ff6f61', '#6b5b95', '#88b04b', '#f7cac9', '#92a8d1'
];

// Swap axes: labels = teknisi, datasets = jenis_komplain
$labels = $teknisiSet;
foreach ($jenisKomplainSet as $index => $jenis) {
    $data = [];
    foreach ($labels as $teknisi) {
        $data[] = $dataMap[$jenis][$teknisi] ?? 0;
    }
    $datasets[] = [
        'label' => $jenis,
        'data' => $data,
        'backgroundColor' => $colors[$index % count($colors)],
        'borderRadius' => 5
    ];
}

echo json_encode([
    'labels' => $labels,
    'datasets' => $datasets
]);
?>
