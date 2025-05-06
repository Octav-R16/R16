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
    $status = 'Pending'; // Default status for new complaints


    // Pastikan folder uploads ada
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Proses upload file jika ada
    $file_name = '';
    if (!empty($_FILES['file_input']['name']) && $_FILES['file_input']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['file_input']['tmp_name'];
        $file_name = basename($_FILES['file_input']['name']); // Gunakan nama asli file
        $target_file = $upload_dir . $file_name;

        // Cegah overwrite dengan menambahkan angka jika file sudah ada
        $counter = 1;
        $file_info = pathinfo($file_name);
        while (file_exists($target_file)) {
            $file_name = $file_info['filename'] . "_$counter." . $file_info['extension'];
            $target_file = $upload_dir . $file_name;
            $counter++;
        }

        // Simpan file ke folder uploads/
        if (!move_uploaded_file($file_tmp, $target_file)) {
            die("Gagal mengupload file.");
        }
    }

    // Masukkan data ke dalam tabel komplain
		// Set timezone to UTC+07:00 (Bangkok, Hanoi, Jakarta)
		$tz = new DateTimeZone('Asia/Bangkok');
		$now = new DateTime('now', $tz);
		$start_at = $now->format('Y-m-d H:i:s');

		// Masukkan data ke dalam tabel komplain
		$sql = "INSERT INTO komplain (id_tiket, tanggal, jenis_komplain, keterangan, user_komplain, cabang, id_chat, file, start_at, status)
				VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
		$stmt = $conn->prepare($sql);
		if (!$stmt) {
			die("Error saat mempersiapkan query: " . $conn->error);
		}

		$stmt->bind_param('ssssssssss', $id_tiket, $tanggal, $jenis_komplain, $keterangan, $user_komplain, $cabang, $id_chat, $file_name, $start_at, $status);

    if ($stmt->execute()) {
        // Jika berhasil, kirim notifikasi ke HR menggunakan API Fonnte
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
                'target' => '6281223542690,62895361639529,6281324240316', // Kirim ke beberapa nomor
                'message' => 'Bro, ada komplain baru nih! 👇' . "\n\n" . 
                            '🛒 Toko     : ' . $cabang . "\n" . 
                            '📌 Jenis    : ' . $jenis_komplain . "\n" . 
                            '📝 Keterangan : "' . $keterangan . '"' . "\n\n" .
                            'Status     : Pending' . "\n\n" .  // Menambahkan status dalam notifikasi
                            'Cek detailnya di: https://tascominimart.co.id/tiket/proses_komplain.php?search=' . urlencode($id_tiket),
                'countryCode' => '62',
            ),
            CURLOPT_HTTPHEADER => array(
                'Authorization: SPRpUpcR5FU9eyYuEcnH' // Token API Anda
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
    
        // Redirect ke index.php setelah berhasil
        echo "<script>alert('Data berhasil disimpan dan notifikasi telah dikirim.'); window.location.href = 'index.php';</script>";
    } else {
        echo "<script>alert('Terjadi kesalahan: " . $stmt->error . "');</script>";
    }
    
    $stmt->close();    
}
?>