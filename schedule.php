<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $activity = $_POST['activity'];
    $date = $_POST['date'];
    $time = $_POST['time'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO schedules (user_id, activity, date, time) VALUES ($user_id, '$activity', '$date', '$time')";
    $conn->query($sql);
}
?>

<h3>Tambah Jadwal</h3>
<form method="post">
    <input type="text" name="activity" placeholder="Nama olahraga" required><br>
    <input type="date" name="date" required><br>
    <input type="time" name="time" required><br>
    <button type="submit">Simpan Jadwal</button>
</form>

<h3>Jadwal Anda</h3>
<?php
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM schedules WHERE user_id=$user_id ORDER BY date, time");

while ($row = $result->fetch_assoc()) {
    echo "{$row['activity']} - {$row['date']} {$row['time']}<br>";
}
?>
