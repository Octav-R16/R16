<?php
header("Content-Type: application/json");

include 'db.php';

try {
    // Query untuk mendapatkan ID terakhir dari tabel komplain
    $query = "SELECT id_tiket FROM komplain ORDER BY id_tiket DESC LIMIT 1";
    
    // Eksekusi query dengan prepared statement
    if ($stmt = $conn->prepare($query)) {
        $stmt->execute();
        $stmt->store_result();
        
        // Jika ada data dalam hasil query
        if ($stmt->num_rows > 0) {
            $stmt->bind_result($last_id);
            $stmt->fetch();
            $last_id = intval($last_id) + 1; // Tambahkan 1 untuk ID berikutnya
        } else {
            $last_id = 1; // ID pertama jika tabel kosong
        }

        // Mengirimkan response dalam format JSON
        echo json_encode(["id_tiket" => $last_id]);

        // Menutup statement
        $stmt->close();
    } else {
        throw new Exception("Failed to prepare the SQL statement.");
    }
} catch (Exception $e) {
    // Menangani kesalahan dan memberikan response error
    echo json_encode(["error" => "An error occurred: " . $e->getMessage()]);
}

// Menutup koneksi
$conn->close();
?>
