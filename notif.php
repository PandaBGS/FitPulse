<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

$result = $conn->query("SELECT * FROM schedules WHERE user_id=$user_id AND date='$today'");

echo "<h3>Jadwal Hari Ini (" . $today . ")</h3>";
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "{$row['activity']} pada {$row['time']}<br>";
    }
} else {
    echo "Tidak ada jadwal olahraga hari ini.";
}
?>
