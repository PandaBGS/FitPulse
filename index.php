<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['username'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Fitpulse - Perencanaan Olahraga</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-600 to-purple-700 font-sans min-h-screen flex flex-col">
    <header class="bg-gradient-to-r from-indigo-700 to-purple-800 text-white p-6 shadow-lg">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="text-3xl font-extrabold">Fitpulse</h1>
            <nav>
                <ul class="flex space-x-8 text-lg font-semibold">
                    <li><a href="index.php" class="hover:underline underline decoration-yellow-400 decoration-4">Beranda</a></li>
                    <li><a href="schedule.php" class="hover:underline">Jadwal</a></li>
                    <li><a href="recommendations.php" class="hover:underline">Rekomendasi</a></li>
                    <li><a href="progress.php" class="hover:underline">Progres</a></li>
                    <li><a href="profile.php" class="hover:underline">Profil</a></li>
                    <li><a href="logout.php" class="hover:underline">Keluar</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mx-auto flex-grow p-10 max-w-5xl">
        <section class="max-w-5xl mx-auto">
            <h2 class="text-4xl font-extrabold mb-6 text-white">Selamat datang, <?=htmlspecialchars($username)?>!</h2>
            <p class="text-xl text-gray-300 mb-10 max-w-3xl">
                Gunakan menu di atas untuk mengakses fitur seperti perencanaan jadwal latihan, rekomendasi olahraga, pelacakan progres, dan pengelolaan profil pengguna.
            </p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <a href="schedule.php" class="block p-8 bg-white rounded-3xl shadow-lg hover:shadow-2xl transition text-center">
                    <i class="fas fa-calendar-alt text-6xl text-blue-600 mb-6"></i>
                    <h3 class="text-2xl font-extrabold mb-3">Jadwal Latihan</h3>
                    <p class="text-lg text-gray-700">Rencanakan dan kelola jadwal latihan Anda.</p>
                </a>
                <a href="recommendations.php" class="block p-8 bg-white rounded-3xl shadow-lg hover:shadow-2xl transition text-center">
                    <i class="fas fa-lightbulb text-6xl text-yellow-500 mb-6"></i>
                    <h3 class="text-2xl font-extrabold mb-3">Rekomendasi Latihan</h3>
                    <p class="text-lg text-gray-700">Dapatkan saran latihan yang sesuai dengan profil Anda.</p>
                </a>
                <a href="progress.php" class="block p-8 bg-white rounded-3xl shadow-lg hover:shadow-2xl transition text-center">
                    <i class="fas fa-chart-line text-6xl text-green-600 mb-6"></i>
                    <h3 class="text-2xl font-extrabold mb-3">Pelacakan Progres</h3>
                    <p class="text-lg text-gray-700">Pantau perkembangan latihan Anda dari waktu ke waktu.</p>
                </a>
                <a href="profile.php" class="block p-8 bg-white rounded-3xl shadow-lg hover:shadow-2xl transition text-center">
                    <i class="fas fa-user-cog text-6xl text-gray-700 mb-6"></i>
                    <h3 class="text-2xl font-extrabold mb-3">Profil Pengguna</h3>
                    <p class="text-lg text-gray-700">Kelola data pribadi dan preferensi olahraga Anda.</p>
                </a>
            </div>
        </section>
    </main>
    <footer class="bg-indigo-900 text-center p-6 text-sm text-gray-400">
        &copy; 2024 Fitpulse. All rights reserved.
    </footer>
</body>
</html>
