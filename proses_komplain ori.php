<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: form_login.php");
    exit();
}
?>
<?php
include 'db.php';

// Fungsi untuk mengamankan input
function sanitize_input($input) {
    return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
}

// Periksa apakah data dikirim melalui metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $id_tiket = sanitize_input($_POST['id_tiket'] ?? '');
    $tanggal = sanitize_input($_POST['tanggal'] ?? '');
    $jenis_komplain = sanitize_input($_POST['jenis_komplain'] ?? '');
    $keterangan = sanitize_input($_POST['keterangan'] ?? '');
    $user_komplain = sanitize_input($_POST['user_komplain'] ?? '');
    $cabang = sanitize_input($_POST['cabang'] ?? '');
    $id_chat = sanitize_input($_POST['id_chat'] ?? '');

    // Pastikan folder uploads ada
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Proses upload file jika ada
    $file_name = '';
    if (!empty($_FILES['file_input']['name']) && $_FILES['file_input']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['file_input']['tmp_name'];
        $file_ext = strtolower(pathinfo($_FILES['file_input']['name'], PATHINFO_EXTENSION));
        $file_name = time() . '_' . uniqid() . '.' . $file_ext; // Nama unik agar tidak bentrok
        $target_file = $upload_dir . $file_name;

        // Validasi tipe file
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        if (!in_array($file_ext, $allowed_types)) {
            die("Tipe file tidak diizinkan. Hanya file JPG, PNG, GIF, dan PDF yang diperbolehkan.");
        }

        // Simpan file ke folder uploads/
        if (!move_uploaded_file($file_tmp, $target_file)) {
            die("Gagal mengupload file.");
        }
    }

    // Masukkan data ke dalam tabel komplain
    $sql = "INSERT INTO komplain (id_tiket, tanggal, jenis_komplain, keterangan, user_komplain, cabang, id_chat, file)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Error saat mempersiapkan query: " . $conn->error);
    }

    $stmt->bind_param('ssssssss', $id_tiket, $tanggal, $jenis_komplain, $keterangan, $user_komplain, $cabang, $id_chat, $file_name);

    if ($stmt->execute()) {
        echo "<script>alert('Data berhasil disimpan.');</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}

// Ambil kata kunci pencarian dari GET
$search = $conn->real_escape_string($_GET['search'] ?? '');

// Tentukan jumlah data per halaman
$limit = 10;
$current_page = max((int)($_GET['page'] ?? 1), 1);
$offset = ($current_page - 1) * $limit;

// Buat query dasar
$sql_base = "SELECT * FROM komplain WHERE 1=1";

// Tambahkan kondisi pencarian jika ada kata kunci
if (!empty($search)) {
    $sql_base .= " AND (id_tiket LIKE '%$search%' 
                    OR tanggal LIKE '%$search%' 
                    OR jenis_komplain LIKE '%$search%' 
                    OR keterangan LIKE '%$search%' 
                    OR user_komplain LIKE '%$search%' 
                    OR cabang LIKE '%$search%' 
                    OR status LIKE '%$search%')";
}

// Hitung total data
$total_query = "SELECT COUNT(*) AS total FROM ($sql_base) AS subquery";
$total_result = $conn->query($total_query);
$total_data = $total_result->fetch_assoc()['total'] ?? 0;

// Hitung total halaman
$total_pages = ceil($total_data / $limit);

// Tambahkan LIMIT dan OFFSET untuk data yang ditampilkan
$sql = $sql_base . " LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

if (!$result) {
    die("Terjadi kesalahan saat mengambil data: " . $conn->error);
}
?>

<?php
// Kirim pesan ke HR
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://api.fonnte.com/send',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array(
        'target' => '6281223542690','62895361639529','6281324240316', // Kirim ke kedua nomor
        'message' => 'Dear HR, berikut permohonan cuti dari',
        'countryCode' => '62',
    ),
    CURLOPT_HTTPHEADER => array(
        'Authorization: MFWeP@p92_26G12WzcPK' // Token API Anda
    ),
));
$response = curl_exec($curl);
curl_close($curl);
echo $response;
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
            background-image: url('uploads/rm222batch2-mind-03.jpg');
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
    <h2 class="text-center text-4xl font-bold text-gray-800 mb-6">Daftar Tiket Komplain</h2>
    <div class="mb-6 hidden md:block">
        <div class="flex flex-wrap gap-4 items-center">
            <!-- Input Pencarian -->
            <form method="GET" action="" class="flex items-center gap-2">
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Masukkan kata kunci..." class="mt-1 block w-full max-w-sm rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Cari</button>
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

<!-- Pagination -->
<nav class="hidden md:block flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4" aria-label="Table navigation">
    <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
        Showing
        <span class="font-semibold text-gray-900 dark:text-white">
            <?php echo (($current_page - 1) * $limit) + 1; ?>
        </span>
        to
        <span class="font-semibold text-gray-900 dark:text-white">
            <?php echo min($current_page * $limit, $total_data); ?>
        </span>
        of
        <span class="font-semibold text-gray-900 dark:text-white">
            <?php echo $total_data; ?>
        </span>
    </span>
    <ul class="inline-flex items-stretch -space-x-px">
        <!-- Tombol Previous -->
        <li>
            <a href="?page=<?php echo max($current_page - 1, 1); ?>&search=<?php echo urlencode($search); ?>" 
               class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white <?php echo $current_page <= 1 ? 'pointer-events-none opacity-50' : ''; ?>">
                <span class="sr-only">Previous</span>
                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
        </li>

        <!-- Tombol Halaman -->
        <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
            <li>
                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                   class="flex items-center justify-center text-sm py-2 px-3 leading-tight <?php echo $i == $current_page ? 'z-10 text-primary-600 bg-primary-50 border border-primary-300 hover:bg-primary-100 hover:text-primary-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white'; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php } ?>

        <!-- Tombol Next -->
        <li>
            <a href="?page=<?php echo min($current_page + 1, $total_pages); ?>&search=<?php echo urlencode($search); ?>" 
               class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white <?php echo $current_page >= $total_pages ? 'pointer-events-none opacity-50' : ''; ?>">
                <span class="sr-only">Next</span>
                <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
            </a>
        </li>
    </ul>
</nav>



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
    <div class="mb-6 md:hidden">
        <div class="flex flex-wrap gap-4 items-center">
            <!-- Input Pencarian -->
            <form method="GET" action="" class="flex items-center gap-2">
                <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Masukkan kata kunci..." class="mt-1 block w-full max-w-sm rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Cari</button>
            </form>
        </div>
    </div>
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
    <!-- Pagination -->
    <nav class="md:hidden flex flex-col md:flex-row justify-between items-start md:items-center space-y-3 md:space-y-0 p-4" aria-label="Table navigation">
        <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
            Showing
            <span class="font-semibold text-gray-900 dark:text-white">
                <?php echo (($current_page - 1) * $limit) + 1; ?>
            </span>
            to
            <span class="font-semibold text-gray-900 dark:text-white">
                <?php echo min($current_page * $limit, $total_data); ?>
            </span>
            of
            <span class="font-semibold text-gray-900 dark:text-white">
                <?php echo $total_data; ?>
            </span>
        </span>
        <ul class="inline-flex items-stretch -space-x-px">
            <!-- Tombol Previous -->
            <li>
                <a href="?page=<?php echo max($current_page - 1, 1); ?>&search=<?php echo urlencode($search); ?>" 
                class="flex items-center justify-center h-full py-1.5 px-3 ml-0 text-gray-500 bg-white rounded-l-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white <?php echo $current_page <= 1 ? 'pointer-events-none opacity-50' : ''; ?>">
                    <span class="sr-only">Previous</span>
                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            </li>

            <!-- Tombol Halaman -->
            <?php for ($i = 1; $i <= $total_pages; $i++) { ?>
                <li>
                    <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>" 
                    class="flex items-center justify-center text-sm py-2 px-3 leading-tight <?php echo $i == $current_page ? 'z-10 text-primary-600 bg-primary-50 border border-primary-300 hover:bg-primary-100 hover:text-primary-700 dark:border-gray-700 dark:bg-gray-700 dark:text-white' : 'text-gray-500 bg-white border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white'; ?>">
                        <?php echo $i; ?>
                    </a>
                </li>
            <?php } ?>

            <!-- Tombol Next -->
            <li>
                <a href="?page=<?php echo min($current_page + 1, $total_pages); ?>&search=<?php echo urlencode($search); ?>" 
                class="flex items-center justify-center h-full py-1.5 px-3 leading-tight text-gray-500 bg-white rounded-r-lg border border-gray-300 hover:bg-gray-100 hover:text-gray-700 dark:bg-gray-800 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white <?php echo $current_page >= $total_pages ? 'pointer-events-none opacity-50' : ''; ?>">
                    <span class="sr-only">Next</span>
                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            </li>
        </ul>
    </nav>
</div>
<?php } ?>

            <!-- Tambahkan Tombol Kembali di Bawah Tabel -->
    <div class="mt-4 justify-end flex">
        <a href="index.php" class="bg-red-500 text-white px-4 py-1.5 rounded-lg hover:bg-red-600">
            Kembali
        </a>
    </div>
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
            <div class="flex justify-end">
                <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mr-2" onclick="closeModal()">Cancel</button>
                <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg hover:bg-green-600">Update</button>
            </div>
        </form>
    </div>
</div>
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
</body>



</html>
<?php
// Tutup koneksi database jika masih aktif
if (isset($conn) && $conn instanceof mysqli) {
    $conn->close();
}
?>
