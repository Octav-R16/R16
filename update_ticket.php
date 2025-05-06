<?php
include 'db.php';

function sendMessage($chat_id, $message) {
    $token = "7540390967:AAHe3SyuSz1WzqhbdspzMcRa5Ol7cCo6BVo"; 
    $url = "https://api.telegram.org/bot$token/sendMessage";

    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'Markdown'
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_tiket = $_POST['ticket_id'];
    $teknisi = $_POST['teknisi'];
    $status = $_POST['status'];
    $id_chat = $_POST['id_chat'] ?? '';
    $ket = $_POST['ket'] ?? null;

    // Ambil data awal dari komplain
    $query = "SELECT jenis_komplain, keterangan, cabang, file, start_at FROM komplain WHERE id_tiket = '$id_tiket'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $jenis_komplain = $row['jenis_komplain'];
        $keterangan = $row['keterangan'];
        $cabang = $row['cabang'];
        $file = $row['file'];
        $start_at = $row['start_at'];
    } else {
        $jenis_komplain = "Tidak ditemukan";
        $cabang = "Tidak ditemukan";
        $file = null;
        $start_at = null;
    }

    // Ambil file_path lama
    $query_file = "SELECT file_path FROM komplain WHERE id_tiket = '$id_tiket'";
    $result_file = $conn->query($query_file);
    $file_path = '';
    if ($result_file->num_rows > 0) {
        $row_file = $result_file->fetch_assoc();
        $file_path = $row_file['file_path'];
    }

    // Cek jika ada file baru yang diupload
    if (!empty($_FILES['file']['name']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $file_tmp = $_FILES['file']['tmp_name'];
        $original_name = basename($_FILES['file']['name']);
        $clean_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original_name);
        $file_path = time() . "_" . $clean_name;
        $target_file = $upload_dir . $file_path;

        $counter = 1;
        $file_info = pathinfo($file_path);
        while (file_exists($target_file)) {
            $file_path = time() . "_" . $file_info['filename'] . "_$counter." . $file_info['extension'];
            $target_file = $upload_dir . $file_path;
            $counter++;
        }

        if (!move_uploaded_file($file_tmp, $target_file)) {
            die("Gagal mengunggah file.");
        }
    }

    // Inisialisasi variabel tambahan
    $end_date = null;
    $calculate_date = null;

    if (strtolower($status) === 'completed') {
        date_default_timezone_set('Asia/Jakarta');
        $end_date = date('Y-m-d H:i:s');

        if (!empty($start_at)) {
            try {
                $start = new DateTime($start_at);
                $end = new DateTime($end_date);
                $diffInSeconds = $end->getTimestamp() - $start->getTimestamp();

                $hours = floor($diffInSeconds / 3600);
                $minutes = floor(($diffInSeconds % 3600) / 60);
                $calculate_date = sprintf('%02d:%02d', $hours, $minutes);
            } catch (Exception $e) {
                $calculate_date = "00:00";
            }
        } else {
            $calculate_date = "00:00";
        }
    }

    // Buat query update tergantung apakah end_date & calculate_date perlu diupdate
    if ($end_date !== null && $calculate_date !== null) {
        $sql = "UPDATE komplain SET teknisi = ?, status = ?, ket = ?, file_path = ?, end_date = ?, calculate_date = ? WHERE id_tiket = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssssss', $teknisi, $status, $ket, $file_path, $end_date, $calculate_date, $id_tiket);
    } else {
        $sql = "UPDATE komplain SET teknisi = ?, status = ?, ket = ?, file_path = ? WHERE id_tiket = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $teknisi, $status, $ket, $file_path, $id_tiket);
    }

    if ($stmt->execute()) {
        $message = "*Update Tiket Perbaikan*\n\n"
            . "*ID Tiket :* `$id_tiket`\n"
            . "*Jenis Komplain :* `$jenis_komplain`\n"
            . "*Keterangan :* `$keterangan`\n"
            . "*Cabang :* `$cabang`\n"
            . "*Teknisi :* `$teknisi`\n"
            . "*Status :* `$status`\n";

        if (!empty($file_path)) {
            $message .= "📎 *File Path:* [Klik di sini](https://tascominimart.co.id/tiket/uploads/$file_path)\n";
        }

        if ($calculate_date !== null) {
            $message .= "*Durasi Waktu:* `$calculate_date`\n";
        }

        $message .= "\nTiket berhasil diperbarui.";

        sendMessage($id_chat, $message);

        echo "<script>
            alert('Tiket berhasil diperbarui & notifikasi dikirim.');
            window.location.href = 'proses_komplain.php';
        </script>";
    } else {
        echo "<script>
            alert('Terjadi kesalahan: " . $stmt->error . "');
            window.location.href = 'proses_komplain.php';
        </script>";
    }
}

$conn->close();
?>
