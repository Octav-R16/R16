<?php
session_start();
if (!isset($_SESSION['UserID'])) {
    header("Location: login.php");
    exit;
}

include 'db.php';

$UserGroup = $_SESSION['UserGroup'] ?? '';
$search = $conn->real_escape_string($_GET['search'] ?? '');
$tanggal_filter = $conn->real_escape_string($_GET['tanggal'] ?? '');
$tanggal_dari = $_GET['tanggal_dari'] ?? null;
$tanggal_sampai = $_GET['tanggal_sampai'] ?? null;

$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10;
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($current_page - 1) * $limit;

// Base SQL
$sql_base_count = "SELECT COUNT(*) AS total FROM komplain WHERE 1=1";
$sql = "SELECT * FROM komplain WHERE 1=1";

// Filter berdasarkan usergroup dari session
if (!empty($UserGroup)) {
    $sql_base_count .= " AND jenis_komplain LIKE '$UserGroup%'";
    $sql .= " AND jenis_komplain LIKE '$UserGroup%'";
}
// Tambahkan kondisi pencarian jika ada kata kunci
if (!empty($search)) {
    $sql_base_count .= " AND (id_tiket LIKE '%$search%' 
                    OR tanggal LIKE '%$search%' 
                    OR jenis_komplain LIKE '%$search%' 
                    OR keterangan LIKE '%$search%' 
                    OR user_komplain LIKE '%$search%' 
                    OR cabang LIKE '%$search%' 
                    OR teknisi LIKE '%$search%' 
                    OR status LIKE '%$search%')";
}

// Jalankan query perhitungan total
$total_result = $conn->query($sql_base_count);
$total_data = $total_result->fetch_assoc()['total'] ?? 0;

// Hitung total halaman
$total_pages = ceil($total_data / $limit);

// Query untuk mengambil data (dengan ORDER BY dan paginasi)
$sql = "SELECT * FROM komplain WHERE 1=1 AND jenis_komplain like '$UserGroup%'";

if (!empty($search)) {
    $sql .= " AND (id_tiket LIKE '%$search%' 
                OR tanggal LIKE '%$search%' 
                OR jenis_komplain LIKE '%$search%' 
                OR keterangan LIKE '%$search%' 
                OR user_komplain LIKE '%$search%' 
                OR cabang LIKE '%$search%' 
                OR teknisi LIKE '%$search%'
                OR status LIKE '%$search%')";
}

$sql .= " ORDER BY id_tiket DESC LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

if (!$result) {
    die("Terjadi kesalahan saat mengambil data: " . $conn->error);
}


// Ambil kata kunci pencarian dan tanggal filter dari GET
$search = $conn->real_escape_string($_GET['search'] ?? '');
$tanggal_filter = $conn->real_escape_string($_GET['tanggal'] ?? '');
$tanggal_dari = $_GET['tanggal_dari'] ?? null;
$tanggal_sampai = $_GET['tanggal_sampai'] ?? null;

// Tentukan jumlah data per halaman
$limit = isset($_GET['limit']) ? (int) $_GET['limit'] : 10; // Default 10
$current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($current_page - 1) * $limit;

// Buat query dasar
$sql_base_count = "SELECT COUNT(*) AS total FROM komplain WHERE 1=1";
$sql = "SELECT * FROM komplain WHERE 1=1 AND jenis_komplain like '$UserGroup%'";

// Tambahkan filter pencarian
if (!empty($search)) {
    $like_search = "%$search%";
    $sql_base_count .= " AND (id_tiket LIKE '$like_search' 
                        OR tanggal LIKE '$like_search' 
                        OR jenis_komplain LIKE '$like_search' 
                        OR keterangan LIKE '$like_search' 
                        OR user_komplain LIKE '$like_search' 
                        OR cabang LIKE '$like_search' 
                        OR teknisi LIKE '$like_search' 
                        OR status LIKE '$like_search')";
    $sql .= " AND (id_tiket LIKE '$like_search' 
                OR tanggal LIKE '$like_search' 
                OR jenis_komplain LIKE '$like_search' 
                OR keterangan LIKE '$like_search' 
                OR user_komplain LIKE '$like_search' 
                OR cabang LIKE '$like_search' 
                OR teknisi LIKE '$like_search'
                OR status LIKE '$like_search')";
}

// Tambahkan filter tanggal rentang (jika ada)
if ($tanggal_dari && $tanggal_sampai) {
    $sql_base_count .= " AND tanggal BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";
    $sql .= " AND tanggal BETWEEN '$tanggal_dari' AND '$tanggal_sampai'";
} elseif (!empty($tanggal_filter)) {
    // Jika hanya filter satu tanggal
    $sql_base_count .= " AND tanggal = '$tanggal_filter'";
    $sql .= " AND tanggal = '$tanggal_filter'";
}

// Tambahkan sorting dan pagination
$sql .= " ORDER BY id_tiket DESC LIMIT $limit OFFSET $offset";

// Jalankan query total data
$total_result = $conn->query($sql_base_count);
$total_data = $total_result->fetch_assoc()['total'] ?? 0;

// Hitung total halaman
$total_pages = ceil($total_data / $limit);

// Jalankan query utama untuk ambil data
$result = $conn->query($sql);
if (!$result) {
    die("Terjadi kesalahan saat mengambil data: " . $conn->error);
}

// Helper untuk membangun query string dengan mempertahankan parameter yang sudah ada
function build_query($params = []) {
    $query = $_GET;
    foreach ($params as $key => $value) {
        $query[$key] = $value;
    }
    return http_build_query($query);
}

?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tiket Komplain</title>
    
    <!-- Font Awesome CDN untuk ikon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Tambahkan Tailwind CSS dan Flowbite -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.css" />

    <style>
        /* body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; } */
        img { max-width: 50px; height: auto; cursor: pointer; }
        .icon { font-size: 24px; margin-right: 5px; }
        .form-container { width: 50%; margin-top: 20px; padding: 10px; border: 1px solid #ddd; }
        .form-container input, .form-container select { width: 100%; padding: 8px; margin: 5px 0; }
        .hidden { display: none; }

            /* Background Image */
        body {
            background-image: url('rm222batch2-mind-03.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* Overlay Effect */
        .bg-black {
            background-color: rgba(0, 0, 0, 0.5);
        }
    </style>

<script>
function getJenisKomplainByUserGroup($userGroup) {
    $allTypes = [
        'IT - STOCK',
        'IT - HARDWARE',
        'IT - SOFTWARE',
        'HRD - CUTI',
        'HRD - KPI',
        'OPERATIONAL - GUDANG',
        'OPERATIONAL - TOKO'
    ];
    
    switch ($userGroup) {
        case 'IT':
            return array_filter($allTypes, function($type) {
                return strpos($type, 'IT -') === 0;
            });
        case 'HRD':
            return array_filter($allTypes, function($type) {
                return strpos($type, 'HRD -') === 0;
            });
        case 'OPERATIONAL':
            return array_filter($allTypes, function($type) {
                return strpos($type, 'OPERATIONAL -') === 0;
            });
        default:
            return $allTypes; // Jika tidak ada kecocokan, tampilkan semua
    }
}

        function showImage(src) {
            const newWindow = window.open("", "_blank");
            newWindow.document.write("<img src='" + src + "' style='max-width:100%; height:auto;'>");
        }

        function takeTicket(id, teknisi, status) {
            document.getElementById("ticket_id").value = id;
            document.getElementById("teknisi").value = teknisi;
            document.getElementById("status").value = status;
            document.getElementById("form-container").classList.remove("hidden");
        }

        function searchTable() {
        // Ambil input pencarian untuk Desktop
        const desktopInput = document.getElementById("search");
        const filter = desktopInput?.value.toLowerCase() || "";

        // Pencarian untuk Desktop View
        const desktopTable = document.querySelector("table tbody");
        if (desktopTable) {
            const desktopRows = desktopTable.getElementsByTagName("tr");
            for (let i = 0; i < desktopRows.length; i++) {
                const cells = desktopRows[i].getElementsByTagName("td");
                let match = false;

                // Periksa setiap kolom dalam baris
                for (let j = 0; j < cells.length; j++) {
                    if (cells[j]) {
                        const textValue = cells[j].textContent || cells[j].innerText;
                        if (textValue.toLowerCase().indexOf(filter) > -1) {
                            match = true;
                            break;
                        }
                    }
                }

                // Tampilkan atau sembunyikan baris berdasarkan hasil pencarian
                desktopRows[i].style.display = match ? "" : "none";
            }
        }
    }

    function searchCards() {
        // Ambil input pencarian untuk Mobile
        const mobileInput = document.getElementById("mobileSearch");
        const filter = mobileInput?.value.toLowerCase() || "";

        // Pencarian untuk Mobile View
        const mobileCards = document.querySelectorAll(".mobile-card");
        if (mobileCards) {
            mobileCards.forEach((card) => {
                const textContent = card.textContent || card.innerText;
                card.style.display = textContent.toLowerCase().indexOf(filter) > -1 ? "" : "none";
            });
        }
    }

    function clearSearchOnResize() {
        const desktopInput = document.getElementById("search");
        const mobileInput = document.getElementById("mobileSearch");

        // Deteksi ukuran layar
        if (window.innerWidth >= 768) {
            // Jika masuk ke Desktop View, clear input Mobile View
            if (mobileInput) {
                mobileInput.value = "";
                searchCards(); // Reset pencarian Mobile View
            }
        } else {
            // Jika masuk ke Mobile View, clear input Desktop View
            if (desktopInput) {
                desktopInput.value = "";
                searchTable(); // Reset pencarian Desktop View
            }
        }
    }

    // Jalankan fungsi saat halaman dimuat
    document.addEventListener("DOMContentLoaded", clearSearchOnResize);

    // Jalankan fungsi saat ukuran layar berubah
    window.addEventListener("resize", clearSearchOnResize);
    
            // Pencarian untuk Mobile View
            const mobileCards = document.querySelectorAll(".mobile-card");
        if (mobileCards) {
            mobileCards.forEach((card) => {
                const textContent = card.textContent || card.innerText;
                card.style.display = textContent.toLowerCase().indexOf(filter) > -1 ? "" : "none";
            });
        }
    
        // Tambahkan event listener untuk sinkronisasi input pencarian
        document.addEventListener("DOMContentLoaded", function () {
        const desktopInput = document.getElementById("search");
        const mobileInput = document.getElementById("mobileSearch");

        if (desktopInput && mobileInput) {
            desktopInput.addEventListener("input", searchTable);
            mobileInput.addEventListener("input", searchTable);
        }
    });
</script>

<script>
    function clearSearchOnResize() {
        const desktopInput = document.getElementById("search");
        const mobileInput = document.getElementById("mobileSearch");

        // Deteksi ukuran layar
        if (window.innerWidth >= 768) {
            // Jika masuk ke Desktop View, clear input Mobile View
            if (mobileInput) mobileInput.value = "";
        } else {
            // Jika masuk ke Mobile View, clear input Desktop View
            if (desktopInput) desktopInput.value = "";
        }
    }

    // Jalankan fungsi saat halaman dimuat
    document.addEventListener("DOMContentLoaded", clearSearchOnResize);

    // Jalankan fungsi saat ukuran layar berubah
    window.addEventListener("resize", clearSearchOnResize);
</script>

<script>
    let currentPage = 1;
    const totalPages = 10; // Ganti dengan jumlah halaman sebenarnya

    function navigatePage(direction) {
        if (direction === 'prev' && currentPage > 1) {
            currentPage--;
        } else if (direction === 'next' && currentPage < totalPages) {
            currentPage++;
        }
        document.getElementById('page-info').textContent = `Page ${currentPage} of ${totalPages}`;
        // Tambahkan logika untuk memuat data halaman baru di sini
    }
</script>

<script>
    function navigateMobilePage(direction) {
        const currentPage = <?php echo $current_page; ?>;
        const totalPages = <?php echo $total_pages; ?>;
        let newPage = currentPage;

        if (direction === 'prev' && currentPage > 1) {
            newPage--;
        } else if (direction === 'next' && currentPage < totalPages) {
            newPage++;
        }

        // Redirect ke halaman baru dengan parameter page
        const searchParam = new URLSearchParams(window.location.search);
        searchParam.set('page', newPage);
        window.location.search = searchParam.toString();
    }
</script>

</head>
<body class="relative flex items-center justify-center min-h-screen p-8 bg-cover bg-center bg-no-repeat">
<div class="container mx-auto p-6">
    
    <?php if (isset($_GET['message']) && $_GET['message'] === 'login_success'): ?>
        <div id="loginSuccess" class="mb-4 p-3 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r text-sm flex items-center animate-fade-in">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <span class="flex-1">Login berhasil! Selamat datang, <?= htmlspecialchars($_SESSION['UserID']) ?> 👋</span>
            <button onclick="dismissLoginSuccess()" class="ml-2 text-green-500 hover:text-green-700 focus:outline-none">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
        <script>
            function dismissLoginSuccess() {
                const alert = document.getElementById('loginSuccess');
                if (alert) {
                    alert.classList.add('opacity-0', 'transition', 'duration-300');
                    setTimeout(() => alert.remove(), 300);
                }
            }
        
            // Auto-hide setelah 5 detik
            window.addEventListener('DOMContentLoaded', () => {
                const alert = document.getElementById('loginSuccess');
                if (alert) {
                    setTimeout(() => {
                        alert.classList.add('opacity-0', 'transition', 'duration-300');
                        setTimeout(() => alert.remove(), 300);
                    }, 5000);
                }
            });
        </script>
    <?php endif; ?>

    
    <h2 class="text-center text-4xl font-bold text-gray-800 mb-6">Daftar Tiket Komplain</h2>

    <div class="mb-6 w-full">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <!-- Input Pencarian -->
        <form method="GET" action="" class="flex flex-wrap items-center gap-2 w-full md:w-auto">
            <input
                type="text"
                id="search"
                name="search"
                value="<?= htmlspecialchars($search); ?>"
                placeholder="Masukkan kata kunci..."
                class="w-full md:w-[240px] rounded-md border border-gray-300 shadow-sm px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-800 dark:text-white dark:border-gray-700">
            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm transition">
                Cari
            </button>
        </form>

        <!-- Filter by Date Range -->
        <form method="GET" class="flex flex-wrap items-center gap-2 w-full md:w-auto bg-white dark:bg-gray-900 px-3 py-2 rounded-xl shadow-sm">

            <label for="tanggal_dari" class="text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal:</label>

            <input
                type="date"
                id="tanggal_dari"
                name="tanggal_dari"
                value="<?= htmlspecialchars($_GET['tanggal_dari'] ?? '') ?>"
                class="w-full sm:w-[140px] bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-sm text-gray-800 dark:text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">

            <span class="text-sm text-gray-600 dark:text-gray-400">sampai</span>

            <input
                type="date"
                id="tanggal_sampai"
                name="tanggal_sampai"
                value="<?= htmlspecialchars($_GET['tanggal_sampai'] ?? '') ?>"
                class="w-full sm:w-[140px] bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-700 text-sm text-gray-800 dark:text-white rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">

            <button
                type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 text-sm rounded-lg transition duration-150">
                Filter
            </button>

            <!-- Hidden Fields -->
            <input type="hidden" name="search" value="<?= htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="limit" value="<?= htmlspecialchars($limit); ?>">
        </form>
    </div>
</div>


    
<!-- Desktop View -->
<div class="hidden md:block overflow-x-auto bg-white shadow-2xl rounded-3xl p-6">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
        <thead class="bg-gray-100 text-xs text-gray-700 uppercase dark:bg-gray-700 dark:text-gray-400">
            <tr>
                <th scope="col" class="px-4 py-3">ID Tiket</th>
                <th scope="col" class="px-4 py-3">Tanggal</th>
                <th scope="col" class="px-4 py-3">Jenis Komplain</th>
                <th scope="col" class="px-4 py-3">Keterangan</th>
                <th scope="col" class="px-4 py-3">User</th>
                <th scope="col" class="px-4 py-3">Cabang</th>
                <th scope="col" class="px-4 py-3">Teknisi</th>
                <th scope="col" class="px-4 py-3">Status</th>
                <th scope="col" class="px-4 py-3">File</th>
                <th scope="col" class="px-4 py-3">Keterangan Pembatalan</th>
                <th scope="col" class="px-4 py-3">Start At</th>
                <th scope="col" class="px-4 py-3">End At</th>
                <th scope="col" class="px-4 py-3">Durasi Waktu</th>
                <th scope="col" class="px-4 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            <?php if ($result && $result->num_rows > 0) { while ($row = $result->fetch_assoc()) { ?>
            <tr class="border-b dark:border-gray-700 hover:bg-gray-50">
                <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                    <?php echo htmlspecialchars($row['id_tiket']); ?>
                </th>
                <td class="px-4 py-3"><?php echo htmlspecialchars($row['tanggal']); ?></td>
                <td class="px-4 py-3"><?php echo htmlspecialchars($row['jenis_komplain']); ?></td>
                <td class="px-4 py-3"><?php echo htmlspecialchars($row['keterangan']); ?></td>
                <td class="px-4 py-3"><?php echo htmlspecialchars($row['user_komplain']); ?></td>
                <td class="px-4 py-3"><?php echo htmlspecialchars($row['cabang']); ?></td>
                <td class="px-4 py-3"><?php echo !empty($row['teknisi']) ? htmlspecialchars($row['teknisi']) : 'Belum Ada'; ?></td>
                <td class="px-4 py-3">
                    <span class="<?php 
                        echo !empty($row['status']) && $row['status'] === 'Pending' ? 'text-orange-500' : 
                            (!empty($row['status']) && $row['status'] === 'Cancelled' ? 'text-red-500' : 
                            (!empty($row['status']) && $row['status'] === 'On Progress' ? 'text-yellow-500' : 
                            (!empty($row['status']) && $row['status'] === 'Completed' ? 'text-green-500' : 'text-gray-500')));
                    ?>">
                        <?php echo !empty($row['status']) ? htmlspecialchars($row['status']) : 'Pending'; ?>
                    </span>
                </td>
                <td class="px-4 py-3">
                    <?php if (!empty($row['file'])) {
                        $file_path = "uploads/" . htmlspecialchars($row['file']);
                        echo file_exists($file_path) ? "<a href='$file_path' target='_blank' class='text-blue-500 hover:underline'>Lihat File</a>" : "<span class='text-red-500'>File tidak ditemukan</span>";
                    } else {
                        echo "<span class='text-gray-500'>Tidak ada file</span>";
                    } ?>
                </td>
                <td class="px-4 py-3">
                    <?php echo !empty($row['ket']) ? htmlspecialchars($row['ket']) : ''; ?>
                </td>
                <td class="px-4 py-3">
                    <?php echo !empty($row['start_at']) ? htmlspecialchars($row['start_at']) : ''; ?>
                </td>
                <td class="px-4 py-3">
                    <?php echo !empty($row['end_date']) ? htmlspecialchars($row['end_date']) : ''; ?>
                </td>
                <td class="px-4 py-3">
                    <?php echo !empty($row['calculate_date']) ? htmlspecialchars($row['calculate_date']) : ''; ?>
                </td>
                <td class="px-4 py-3 flex items-center justify-start">
                    <?php if (!empty($row['status']) && $row['status'] === 'Completed') { ?>
                        <span class="text-green-500 flex items-center">
                            <i class="fas fa-check-circle text-xl mr-2"></i> Sukses
                        </span>
                    <?php } elseif (!empty($row['status']) && $row['status'] === 'Cancelled') { ?>
                        <span class="text-red-500 flex items-center">
                            <i class="fas fa-times-circle text-xl mr-2"></i> Dibatalkan
                        </span>
                    <?php } else { ?>
                        <button class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600" 
                            onclick="openModal('<?php echo $row['id_tiket']; ?>', '<?php echo $row['teknisi']; ?>', '<?php echo $row['status']; ?>', '<?php echo $row['id_chat']; ?>')">
                            Update Tiket
                        </button>
                    <?php } ?>
                </td>
            </tr>
            <?php } } else { ?>
            <tr>
                <td colspan="10" class="px-4 py-3 text-center text-gray-500">Tidak ada data</td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<!-- Tombol Export di desktop view -->
<div class="flex justify-between mt-4 hidden sm:flex">
    <!-- Tombol untuk mengarah ke index.php (Kiri) -->
    <a href="index.php"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition duration-200 ease-in-out">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M4 4v16c0 .55.45 1 1 1h14a1 1 0 001-1V4M16 2v4M8 2v4M4 10h16"/>
        </svg>
        Form Komplain
    </a>
    
    <!-- Tombol Export ke Excel (Kanan) -->
    <a href="export_excel.php?search=<?php echo urlencode($_GET['search'] ?? ''); ?>&tanggal_dari=<?php echo urlencode($_GET['tanggal_dari'] ?? ''); ?>&tanggal_sampai=<?php echo urlencode($_GET['tanggal_sampai'] ?? ''); ?>&usergroup=<?php echo urlencode($_GET['usergroup'] ?? ''); ?>"
       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition duration-200 ease-in-out"
       target="_blank">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M4 4v16c0 .55.45 1 1 1h14a1 1 0 001-1V4M16 2v4M8 2v4M4 10h16"/>
        </svg>
        Export Excel
    </a>
</div>

<!-- Show Data -->
<form method="GET" class="flex items-center gap-2 hidden sm:flex justify-center">
    <label for="limit" class="text-sm font-medium text-gray-900 dark:text-white">Show data:</label>
    <div>
        <select id="limit" name="limit" onchange="this.form.submit()"
            class="justify-center bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-1 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
            <?php
            $options = [10, 25, 50, 100];
            foreach ($options as $opt) {
                $selected = $limit == $opt ? 'selected' : '';
                echo "<option value=\"$opt\" $selected>$opt</option>";
            }
            ?>
        </select>
    </div>

    <!-- Pertahankan semua parameter filter -->
    <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="tanggal_dari" value="<?= htmlspecialchars($_GET['tanggal_dari'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="tanggal_sampai" value="<?= htmlspecialchars($_GET['tanggal_sampai'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="usergroup" value="<?= htmlspecialchars($_GET['usergroup'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? '1', ENT_QUOTES, 'UTF-8'); ?>">
</form>

<!-- Info Halaman -->
<div class="flex items-center justify-center mt-4 hidden sm:flex">
    <span class="text-sm text-gray-700 dark:text-gray-400">
        Halaman <span class="font-semibold text-gray-900 dark:text-white"><?= $current_page; ?></span>
        dari <span class="font-semibold text-gray-900 dark:text-white"><?= $total_pages; ?></span>
    </span>
</div>

<!-- Pagination -->
<div class="flex items-center justify-center hidden sm:flex">
    <div class="inline-flex items-center -space-x-px text-sm rtl:space-x-reverse">
        <!-- Tombol First & Previous -->
        <?php if ($current_page > 1): ?>
            <a href="?<?= build_query(['page' => 1]) ?>"
               class="flex items-center justify-center px-3 h-8 ms-0 leading-tight text-gray-500 bg-white border border-e-0 border-gray-300 rounded-s-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                First
            </a>
            <a href="?<?= build_query(['page' => $current_page - 1]) ?>"
               class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                Prev
            </a>
        <?php endif; ?>

        <!-- Nomor Halaman -->
        <?php
        $range = 2;
        for ($i = max(1, $current_page - $range); $i <= min($total_pages, $current_page + $range); $i++):
            $active = $i === $current_page;
        ?>
            <a href="?<?= build_query(['page' => $i]) ?>"
               class="flex items-center justify-center px-3 h-8 leading-tight border border-gray-300 <?= $active ? 'bg-blue-50 text-blue-600 hover:bg-blue-100 hover:text-blue-700 dark:bg-gray-700 dark:text-white' : 'bg-white text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white'; ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <!-- Tombol Next & Last -->
        <?php if ($current_page < $total_pages): ?>
            <a href="?<?= build_query(['page' => $current_page + 1]) ?>"
               class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                Next
            </a>
            <a href="?<?= build_query(['page' => $total_pages]) ?>"
               class="flex items-center justify-center px-3 h-8 leading-tight text-gray-500 bg-white border border-gray-300 rounded-e-lg hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
                Last
            </a>
        <?php endif; ?>
    </div>
</div>

<div class="w-full flex justify-center items-center mt-6">
    <a href="logout.php"
       class="justify-center inline-flex items-center gap-2 text-white bg-red-600 hover:bg-red-700 
              focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm 
              px-5 py-2.5 text-center dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800 
              transition duration-200">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 
                     2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"></path>
        </svg>
        Logout
    </a>
</div>




    <?php 
    // Debug: Cek apakah ada data
    if (!$result || $result->num_rows == 0) {
        echo "<p class='text-center text-gray-500'>Tidak ada data ditemukan.</p>";
        echo "<div class='mb-6 md:hidden'>";
        echo "<div class='flex flex-wrap gap-4 items-center'>";
        echo "<form method='GET' action='' class='flex items-center gap-2'>";
        echo "<input type='text' id='search' name='search' value='" . htmlspecialchars($search) . "' placeholder='Masukkan kata kunci...' class='mt-1 block w-full max-w-sm rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500'>";
        echo "<button type='submit' class='bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600'>Cari</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
    } else {
    ?>

<!-- Mobile View -->
<div class="md:hidden w-full space-y-4" id="mobileContainer">
    <?php 
    $result->data_seek(0); 
    while ($row = $result->fetch_assoc()) { 
    ?>
    <div class="bg-white border border-gray-200 rounded-lg shadow-md p-4 mobile-card">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-semibold text-gray-800">
                Tiket: #<?php echo htmlspecialchars($row['id_tiket'] ?? 'N/A'); ?>
            </h3>
            <span class="text-sm text-gray-600">
                <?php echo htmlspecialchars($row['tanggal'] ?? 'Tidak ada tanggal'); ?>
            </span>
        </div>
        <p class="text-sm text-gray-700"><strong>Jenis:</strong> <?php echo htmlspecialchars($row['jenis_komplain'] ?? '-'); ?></p>
        <p class="text-sm text-gray-700"><strong>Keterangan:</strong> <?php echo htmlspecialchars($row['keterangan'] ?? '-'); ?></p>
        <p class="text-sm text-gray-700"><strong>User:</strong> <?php echo htmlspecialchars($row['user_komplain'] ?? '-'); ?></p>
        <p class="text-sm text-gray-700"><strong>Cabang:</strong> <?php echo htmlspecialchars($row['cabang'] ?? '-'); ?></p>
        <p class="text-sm text-gray-700"><strong>Teknisi:</strong> <?php echo !empty($row['teknisi']) ? htmlspecialchars($row['teknisi']) : 'Belum Ada'; ?></p>
        <p class="text-sm text-gray-700">
            <strong>Status:</strong> 
            <span class="<?php 
                echo !empty($row['status']) && $row['status'] === 'Pending' ? 'text-orange-500' : 
                    (!empty($row['status']) && $row['status'] === 'Cancelled' ? 'text-red-500' : 
                    (!empty($row['status']) && $row['status'] === 'On Progress' ? 'text-yellow-500' : 
                    (!empty($row['status']) && $row['status'] === 'Completed' ? 'text-green-500' : 'text-gray-500'))); 
            ?>">
                <?php echo !empty($row['status']) ? htmlspecialchars($row['status']) : 'Pending'; ?>
            </span>
        </p>
        <p class="text-sm text-gray-700">
            <strong>File:</strong> 
            <?php if (!empty($row['file'])) {
                $file_path = "uploads/" . htmlspecialchars($row['file']);
                echo file_exists($file_path) ? "<a href='$file_path' target='_blank' class='text-blue-500 hover:underline'>Lihat File</a>" : "<span class='text-red-500'>File tidak ditemukan</span>";
            } else {
                echo "<span class='text-gray-500'>Tidak ada file</span>";
            } ?>
        </p>
        <p class="text-sm text-gray-700"><strong>Keterangan Pembatalan:</strong> <?php echo !empty($row['ket']) ? htmlspecialchars($row['ket']) : '-'; ?></p> <!-- Kolom Baru -->
        <div class="mt-4">
            <?php if (!empty($row['status']) && $row['status'] === 'Completed') { ?>
                <span class="text-green-500 flex items-center">
                    <i class="fas fa-check-circle text-xl mr-2"></i> Sukses
                </span>
            <?php } elseif (!empty($row['status']) && $row['status'] === 'Cancelled') { ?>
                <span class="text-red-500 flex items-center">
                    <i class="fas fa-times-circle text-xl mr-2"></i> Dibatalkan
                </span>
            <?php } else { ?>
                <button class="w-full bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600" 
                    onclick="openModal('<?php echo $row['id_tiket']; ?>', '<?php echo $row['teknisi']; ?>', '<?php echo $row['status']; ?>', '<?php echo $row['id_chat']; ?>')">
                    Update Tiket
                </button>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
    
<!-- Tombol Index di Kiri dan Export di Kanan di Mobile View -->
<div class="flex justify-between mt-4 md:hidden">
    <!-- Tombol untuk mengarah ke index.php (Kiri) -->
    <a href="index.php"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition duration-200 ease-in-out">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M4 4v16c0 .55.45 1 1 1h14a1 1 0 001-1V4M16 2v4M8 2v4M4 10h16"/>
        </svg>
        Form Komplain
    </a>
    <!-- Tombol Export ke Excel (Kanan) -->
    <a href="export_excel.php?search=&limit=100&page=1"
       class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded-lg shadow-md transition duration-200 ease-in-out">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M4 4v16c0 .55.45 1 1 1h14a1 1 0 001-1V4M16 2v4M8 2v4M4 10h16"/>
        </svg>
        Export Excel
    </a>
</div>


    <!-- Show Data (Responsive) -->
<form method="GET" class="flex flex-col sm:flex-row sm:items-center sm:gap-2 gap-2 mt-4">
    <div class="flex items-center gap-2">
        <label for="limit" class="text-sm font-medium text-gray-900 dark:text-white">Show:</label>
        <select id="limit" name="limit" onchange="this.form.submit()"
            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 w-full p-1 dark:bg-gray-800 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">
            <?php
            $options = [10, 25, 50, 100];
            foreach ($options as $opt) {
                $selected = $limit == $opt ? 'selected' : '';
                echo "<option value=\"$opt\" $selected>$opt</option>";
            }
            ?>
        </select>
    </div>

    <!-- Pertahankan semua filter -->
    <input type="hidden" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="tanggal_dari" value="<?= htmlspecialchars($_GET['tanggal_dari'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="tanggal_sampai" value="<?= htmlspecialchars($_GET['tanggal_sampai'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="usergroup" value="<?= htmlspecialchars($_GET['usergroup'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
    <input type="hidden" name="page" value="<?= htmlspecialchars($_GET['page'] ?? '1', ENT_QUOTES, 'UTF-8'); ?>">
</form>

<!-- Info Halaman -->
<div class="flex items-center justify-center mt-4 text-sm text-gray-700 dark:text-gray-400">
    Halaman <span class="font-semibold text-gray-900 dark:text-white mx-1"><?= $current_page; ?></span>
    dari <span class="font-semibold text-gray-900 dark:text-white ml-1"><?= $total_pages; ?></span>
</div>

<!-- Pagination Responsive -->
<div class="flex justify-center mt-2 overflow-x-auto">
    <div class="inline-flex items-center gap-1 text-sm rtl:space-x-reverse">
        <?php if ($current_page > 1): ?>
            <a href="?<?= build_query(['page' => 1]) ?>" class="px-3 py-1 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600">First</a>
            <a href="?<?= build_query(['page' => $current_page - 1]) ?>" class="px-3 py-1 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600">Prev</a>
        <?php endif; ?>

        <?php
        $range = 2;
        for ($i = max(1, $current_page - $range); $i <= min($total_pages, $current_page + $range); $i++):
            $active = $i === $current_page;
        ?>
            <a href="?<?= build_query(['page' => $i]) ?>"
               class="px-3 py-1 rounded-lg border <?= $active ? 'bg-blue-500 text-white dark:bg-blue-600' : 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' ?> hover:bg-blue-100 dark:hover:bg-gray-700">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if ($current_page < $total_pages): ?>
            <a href="?<?= build_query(['page' => $current_page + 1]) ?>" class="px-3 py-1 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600">Next</a>
            <a href="?<?= build_query(['page' => $total_pages]) ?>" class="px-3 py-1 rounded-lg bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600">Last</a>
        <?php endif; ?>
    </div>
</div>
</div>
<div class="flex justify-center p-4 sm:p-6 md:hidden">
    <a href="logout.php"
       class="flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg 
              shadow-lg hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-300 
              dark:bg-red-500 dark:hover:bg-red-600 dark:focus:ring-red-800 transition-all duration-300">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 
                     2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"></path>
        </svg>
        Logout
    </a>
</div>

<?php } ?>

            <!-- Tambahkan Tombol Kembali di Bawah Tabel -->
    <!--<div class="mt-4 justify-end flex">-->
    <!--    <a href="index.php" class="bg-red-500 text-white px-4 py-1.5 rounded-lg hover:bg-red-600">-->
    <!--        Kembali-->
    <!--    </a>-->
    <!--</div>-->
    <?php include 'footer.php'; ?>
</div>
<!-- Modal Update Tiket -->
<div id="updateModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white rounded-lg shadow-lg w-96 p-6 relative">
        <!-- Icon Close -->
        <button type="button" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700" onclick="closeModal()">
            <i class="fas fa-times text-xl"></i> <!-- Font Awesome Icon -->
        </button>

        <h2 class="text-lg font-semibold text-gray-800 mb-4">Update Tiket</h2>
        <form action="update_ticket.php" method="post" enctype="multipart/form-data">
            <input type="hidden" id="modal_ticket_id" name="ticket_id">
            <input type="hidden" id="modal_id_chat" name="id_chat" value="">
            <div class="mb-4">
                <label for="modal_teknisi" class="block text-sm font-medium text-gray-700">Teknisi:</label>
                <input type="text" id="modal_teknisi" name="teknisi" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
            </div>
            <div class="mb-4">
                <label for="modal_status" class="block text-sm font-medium text-gray-700">Status:</label>
                <select id="modal_status" name="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" required>
                    <option value="Pending">Pending</option>
                    <option value="Cancelled">Cancelled</option>
                    <option value="On Progress">On Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>
            <div class="mb-4">
                <label for="modal_file" class="block text-sm font-medium text-gray-700">Upload Bukti Selesai:</label>
                <input type="file" id="modal_file" name="file" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="mb-4" id="cancelledReasonContainer" style="display: none;">
                <label for="modal_ket" class="block text-sm font-medium text-gray-700">Alasan Pembatalan:</label>
                <textarea id="modal_ket" name="ket" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>
            <div class="flex justify-end">
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mr-2" onclick="closeModal()">Cancel</button>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">Update</button>
            </div>
        </form>
    </div>
</div>
<script>
    function validateForm(event) {
        // Ambil elemen form
        const teknisi = document.getElementById("modal_teknisi").value.trim();
        const status = document.getElementById("modal_status").value.trim();
        const file = document.getElementById("modal_file").value.trim();
        const ket = document.getElementById("modal_ket").value.trim();
        const cancelledReasonContainer = document.getElementById("cancelledReasonContainer");

        // Validasi field Teknisi
        if (!teknisi) {
            alert("Field Teknisi harus diisi.");
            event.preventDefault(); // Mencegah pengiriman form
            return false;
        }

        // Validasi field Status
        if (!status) {
            alert("Field Status harus diisi.");
            event.preventDefault();
            return false;
        }

        // Validasi field File (hanya jika tidak ada file yang sudah diunggah sebelumnya)
        if (!file && cancelledReasonContainer.style.display === "none") {
            alert("Field Upload Bukti Selesai harus diisi.");
            event.preventDefault();
            return false;
        }

        // Validasi field Keterangan Pembatalan jika status adalah Cancelled
        if (status === "Cancelled" && !ket) {
            alert("Field Alasan Pembatalan harus diisi jika status adalah Cancelled.");
            event.preventDefault();
            return false;
        }

        // Jika semua validasi lolos, form akan dikirim
        return true;
    }
</script>
    <script>
        function openModal(ticketId, teknisi, status, idChat) {
        document.getElementById("modal_ticket_id").value = ticketId;
        document.getElementById("modal_teknisi").value = teknisi;
        document.getElementById("modal_status").value = status;
        document.getElementById("modal_id_chat").value = idChat; // Isi id_chat
        document.getElementById("updateModal").classList.remove("hidden");
    }

        function closeModal() {
            document.getElementById("updateModal").classList.add("hidden");
        }
    </script>
    <script>
        document.getElementById("modal_status").addEventListener("change", function () {
            const status = this.value;
            const cancelledReasonContainer = document.getElementById("cancelledReasonContainer");

            if (status === "Cancelled") {
                cancelledReasonContainer.style.display = "block";
            } else {
                cancelledReasonContainer.style.display = "none";
            }
        });
</script>
</body>



</html>
<?php
// Tutup koneksi database jika masih aktif
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
