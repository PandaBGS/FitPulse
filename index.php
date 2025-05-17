<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<h2>Selamat datang di FitPulse!</h2>
<a href="schedule.php">🗓 Penjadwalan Olahraga</a><br>
<a href="progress.php">📊 Lihat Progress</a><br>
<a href="recommendation.php">🤖 Rekomendasi Olahraga</a><br>
<a href="notif.php">🔔 Notifikasi Pengingat</a><br>
<a href="logout.php">🚪 Logout</a>
