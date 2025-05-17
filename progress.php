<?php
session_start();
include 'db.php';
if (!isset($_SESSION['user_id'])) header("Location: login.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $activity = $_POST['activity'];
    $duration = $_POST['duration'];
    $date = $_POST['date'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO progress (user_id, activity, duration, date) VALUES ($user_id, '$activity', $duration, '$date')";
    $conn->query($sql);
}
?>

<h3>Tambah Progress</h3>
<form method="post">
    <input type="text" name="activity" placeholder="Nama olahraga" required><br>
    <input type="number" name="duration" placeholder="Durasi (menit)" required><br>
    <input type="date" name="date" required><br>
    <button type="submit">Simpan Progress</button>
</form>

<h3>Riwayat Progress</h3>
<?php
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT * FROM progress WHERE user_id=$user_id ORDER BY date DESC");

while ($row = $result->fetch_assoc()) {
    echo "{$row['date']}: {$row['activity']} - {$row['duration']} menit<br>";
}
?>
