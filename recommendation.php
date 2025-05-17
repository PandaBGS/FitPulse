<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT activity, COUNT(*) AS total FROM progress WHERE user_id=$user_id GROUP BY activity ORDER BY total DESC LIMIT 1");

$rekomendasi = "Lari";
if ($row = $result->fetch_assoc()) {
    $rekomendasi = $row['activity'];
}
?>

<h3>Rekomendasi Olahraga Anda</h3>
<p>Berdasarkan aktivitas sebelumnya, kami rekomendasikan: <strong><?= $rekomendasi ?></strong></p>
