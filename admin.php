<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: form_login.php");
    exit();
}
?>
<?php
include 'db.php';

// Ambil data dari tabel komplain
$sql = "SELECT * FROM komplain ORDER BY tanggal DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tiket Komplain</title>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
    </style>
</head>
<body>
    <h2>Daftar Tiket Komplain</h2>
    <table>
        <tr>
            <th>ID Tiket</th>
            <th>Tanggal</th>
            <th>Jenis Komplain</th>
            <th>Keterangan</th>
            <th>User</th>
            <th>Cabang</th>
            <th>Teknisi</th>
            <th>Status</th>
            <th>File</th>
            <th>Aksi</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id_tiket']); ?></td>
            <td><?php echo htmlspecialchars($row['tanggal']); ?></td>
            <td><?php echo htmlspecialchars($row['jenis_komplain']); ?></td>
            <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
            <td><?php echo htmlspecialchars($row['user_komplain']); ?></td>
            <td><?php echo htmlspecialchars($row['cabang']); ?></td>
            <td><?php echo htmlspecialchars($row['teknisi']); ?></td>
            <td><?php echo htmlspecialchars($row['status']); ?></td>
			<td>
				<?php 
				var_dump($row['file']); // Debugging
				if (!empty($row['file'])) { ?>
					<a href="uploads/<?php echo htmlspecialchars($row['file']); ?>" target="_blank">Lihat</a>
				<?php } else { echo "-"; } ?>
			</td>
            <td>
                <a href="edit.php?id=<?php echo $row['id_tiket']; ?>">Edit</a> |
                <a href="delete.php?id=<?php echo $row['id_tiket']; ?>" onclick="return confirm('Hapus tiket ini?');">Hapus</a>
            </td>
        </tr>
        <?php } ?>
    </table>
</body>
</html>
<?php
$conn->close();
?>
