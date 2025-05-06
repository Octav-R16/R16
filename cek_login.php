<?php
session_start();
include "db.php"; // file ini berisi koneksi ke database

$userid = $_POST['userid'];
$password = $_POST['password'];

$query = "SELECT * FROM user_info WHERE UserID = ? AND User_Password = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ss", $userid, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $_SESSION['UserID'] = $row['UserID'];
    $_SESSION['UserGroup'] = $row['User_Group'];
    header("Location: proses_komplain.php");
    exit;
} else {
    header("Location: login.php?error=1");
    exit;
}
?>
