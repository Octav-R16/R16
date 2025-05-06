<?php
include 'db.php'; // Koneksi ke database

// Ambil filter dari POST
$start  = $_POST['start'] ?? '';
$end    = $_POST['end'] ?? '';
$cabang = $_POST['cabang'] ?? 'all';
$status = $_POST['status'] ?? 'all';
$usergroup = strtolower(trim($_POST['user_group'] ?? ''));

// Siapkan array untuk WHERE clause
$where = [];

// Filter cabang
if ($cabang !== 'all') {
    $cabangEsc = $conn->real_escape_string($cabang);
    $where[] = "cabang = '$cabangEsc'";
}

// Filter usergroup ke jenis_komplain
if (!empty($usergroup)) {
    $usergroupEsc = $conn->real_escape_string($usergroup);
    $where[] = "LOWER(jenis_komplain) LIKE '%$usergroupEsc%'";
}

// Filter status
if ($status !== 'all') {
    $statusEsc = $conn->real_escape_string($status);
    $where[] = "status = '$statusEsc'";
}

// Buat string WHERE tambahan (selain tanggal)
$whereSQL = count($where) ? 'AND ' . implode(' AND ', $where) : '';

// Siapkan filter tanggal aman
$startEsc = $conn->real_escape_string($start);
$endEsc   = $conn->real_escape_string($end);

// Jika filter tanggal kosong, ambil 6 bulan terakhir secara default
if (empty($start) || empty($end)) {
    $endDate = new DateTime(); // hari ini
    $startDate = (clone $endDate)->modify('-5 months')->modify('first day of this month');

    $start = $startDate->format('Y-m-01');
    $end   = $endDate->format('Y-m-t');
}

// Fungsi untuk generate array bulan antara dua tanggal
function getMonthsBetween($start, $end) {
    $startDate = new DateTime($start);
    $endDate = new DateTime($end);
    $endDate->modify('first day of next month');
    $interval = new DateInterval('P1M');
    $period = new DatePeriod($startDate, $interval, $endDate);
    $months = [];
    foreach ($period as $dt) {
        $months[] = $dt->format('Y-m');
    }
    return $months;
}

$months = getMonthsBetween($start, $end);

// Query untuk total komplain per bulan
$queryTotal = "
    SELECT DATE_FORMAT(tanggal, '%Y-%m') AS month, COUNT(*) AS total
    FROM komplain
    WHERE tanggal BETWEEN ? AND ?
    $whereSQL
    GROUP BY month
    ORDER BY month ASC
";

// Query untuk komplain selesai per bulan (status = 'completed')
$querySelesai = "
    SELECT DATE_FORMAT(tanggal, '%Y-%m') AS month, COUNT(*) AS selesai
    FROM komplain
    WHERE tanggal BETWEEN ? AND ?
    AND status = 'completed'
    $whereSQL
    GROUP BY month
    ORDER BY month ASC
";

// Eksekusi query total komplain
$stmtTotal = $conn->prepare($queryTotal);
$stmtTotal->bind_param('ss', $start, $end);
$stmtTotal->execute();
$resultTotal = $stmtTotal->get_result();

$totalData = [];
while ($row = $resultTotal->fetch_assoc()) {
    $totalData[$row['month']] = (int)$row['total'];
}
$stmtTotal->close();

// Eksekusi query selesai
$stmtSelesai = $conn->prepare($querySelesai);
$stmtSelesai->bind_param('ss', $start, $end);
$stmtSelesai->execute();
$resultSelesai = $stmtSelesai->get_result();

$selesaiData = [];
while ($row = $resultSelesai->fetch_assoc()) {
    $selesaiData[$row['month']] = (int)$row['selesai'];
}
$stmtSelesai->close();

// Susun data final untuk dikirim ke JS
$labels = [];
$totalArray = [];
$selesaiArray = [];

foreach ($months as $month) {
    $labels[] = date('M Y', strtotime($month . '-01'));
    $totalArray[] = $totalData[$month] ?? 0;
    $selesaiArray[] = $selesaiData[$month] ?? 0;
}

// Output JSON ke frontend
header('Content-Type: application/json');
echo json_encode([
    'labels' => $labels,
    'total' => $totalArray,
    'selesai' => $selesaiArray
]);
?>
