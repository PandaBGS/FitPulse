<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// Remove manual adding of progress logs by user
// Progress logs will be added automatically from schedule confirmation

// Fetch activity logs for the user
$logs_all = readData(PROGRESS_FILE);
$logs = array_filter($logs_all, function ($log) use ($user_id) {
    return $log['user_id'] === $user_id;
});

// Sort logs by log_date descending
usort($logs, function ($a, $b) {
    return strcmp($b['log_date'], $a['log_date']);
});

// Fetch activity logs for the user
$logs_all = readData(PROGRESS_FILE);
$logs = array_filter($logs_all, function ($log) use ($user_id) {
    return $log['user_id'] === $user_id;
});

// Sort logs by log_date descending
usort($logs, function ($a, $b) {
    return strcmp($b['log_date'], $a['log_date']);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Pelacakan Progres - Fitpulse</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans text-gray-800 min-h-screen flex flex-col">
    <header class="bg-blue-600 text-white p-4 shadow-md">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-2xl font-bold">Fitpulse</h1>
            <nav>
                <ul class="flex space-x-6">
                    <li><a href="index.php" class="hover:underline">Beranda</a></li>
                    <li><a href="schedule.php" class="hover:underline">Jadwal</a></li>
                    <li><a href="recommendations.php" class="hover:underline">Rekomendasi</a></li>
                    <li><a href="progress.php" class="hover:underline font-semibold underline">Progres</a></li>
                    <li><a href="profile.php" class="hover:underline">Profil</a></li>
                    <li><a href="logout.php" class="hover:underline">Keluar</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mx-auto flex-grow p-6 max-w-4xl">
        <h2 class="text-3xl font-semibold mb-6">Pelacakan Progres Latihan</h2>

        <?php if ($errors): ?>
            <div class="mb-4 text-red-600">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><i class="fas fa-exclamation-circle mr-2"></i><?=htmlspecialchars($error)?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($success): ?>
            <div class="mb-4 text-green-600">
                <i class="fas fa-check-circle mr-2"></i><?=htmlspecialchars($success)?>
            </div>
        <?php endif; ?>

        <form method="POST" action="progress.php" class="mb-8 bg-white p-6 rounded shadow">
            <div>
                <label for="log_date" class="block mb-1 font-semibold">Tanggal</label>
                <input type="date" id="log_date" name="log_date" required class="w-full border border-gray-300 rounded px-3 py-2" value="<?=htmlspecialchars($_POST['log_date'] ?? '')?>" />
            </div>
            <div class="mt-4">
                <label for="progress" class="block mb-1 font-semibold">Deskripsi Progres</label>
                <textarea id="progress" name="progress" rows="4" required class="w-full border border-gray-300 rounded px-3 py-2"><?=htmlspecialchars($_POST['progress'] ?? '')?></textarea>
            </div>
            <button type="submit" class="mt-4 bg-green-600 text-white py-2 px-6 rounded hover:bg-green-700 transition">Tambah Progres</button>
        </form>

        <section>
            <h3 class="text-2xl font-semibold mb-4">Riwayat Progres</h3>
            <?php if (count($logs) === 0): ?>
                <p class="text-gray-600">Belum ada progres latihan yang dicatat.</p>
            <?php else: ?>
                <ul class="space-y-4">
                    <?php foreach ($logs as $log): ?>
                        <li class="bg-white p-4 rounded shadow">
                            <p class="text-gray-700"><?=nl2br(htmlspecialchars($log['progress']))?></p>
                            <p class="text-sm text-gray-500 mt-2">Tanggal: <?=htmlspecialchars($log['log_date'])?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
    <footer class="bg-gray-200 text-center p-4 text-sm text-gray-600">
        &copy; 2024 Fitpulse. All rights reserved.
    </footer>
</body>
</html>
