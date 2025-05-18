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

// Helper function to generate workout recommendations based on user's schedules
function generateRecommendations($user_id) {
    $schedules_all = readData(SCHEDULES_FILE);
    $user_schedules = array_filter($schedules_all, function ($schedule) use ($user_id) {
        return $schedule['user_id'] === $user_id;
    });

    $workout_counts = [];
    foreach ($user_schedules as $schedule) {
        $type = strtolower(trim($schedule['workout_type']));
        if ($type) {
            if (!isset($workout_counts[$type])) {
                $workout_counts[$type] = 0;
            }
            $workout_counts[$type]++;
        }
    }

    arsort($workout_counts);

    $recommendations = [];
    foreach ($workout_counts as $workout => $count) {
        $recommendations[] = [
            'recommended_workout' => ucfirst($workout),
            'reason' => "Anda sering melakukan latihan ini ($count kali)."
        ];
    }

    // If no schedules, provide default recommendations
    if (empty($recommendations)) {
        $recommendations = [
            ['recommended_workout' => 'Yoga', 'reason' => 'Latihan yang baik untuk fleksibilitas dan relaksasi.'],
            ['recommended_workout' => 'Lari', 'reason' => 'Latihan kardio yang meningkatkan kebugaran jantung.'],
            ['recommended_workout' => 'Angkat Beban', 'reason' => 'Meningkatkan kekuatan otot dan metabolisme.']
        ];
    }

    return $recommendations;
}

// Fetch user profile data for recommendation logic
$users = readData(USERS_FILE);
$user = null;
foreach ($users as $u) {
    if ($u['id'] === $user_id) {
        $user = $u;
        break;
    }
}

$fitness_level = $user['fitness_level'] ?? 'beginner';
$preferences = $user['preferences'] ?? '';

// Handle adding a new recommendation (for demo purposes)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recommended_workout = trim($_POST['recommended_workout'] ?? '');
    $reason = trim($_POST['reason'] ?? '');

    if (!$recommended_workout) {
        $errors[] = 'Jenis latihan yang direkomendasikan harus diisi.';
    } else {
        $recommendations_all = readData(RECOMMENDATIONS_FILE);
        $new_recommendation = [
            'id' => uniqid(),
            'user_id' => $user_id,
            'recommended_workout' => $recommended_workout,
            'reason' => $reason,
            'created_at' => date('c')
        ];
        $recommendations_all[] = $new_recommendation;
        writeData(RECOMMENDATIONS_FILE, $recommendations_all);
        $success = 'Rekomendasi latihan berhasil ditambahkan.';
    }
}

// Fetch existing recommendations for the user
$recommendations_all = readData(RECOMMENDATIONS_FILE);
$recommendations_manual = array_filter($recommendations_all, function ($rec) use ($user_id) {
    return $rec['user_id'] === $user_id;
});

// Generate recommendations based on schedules
$recommendations_generated = generateRecommendations($user_id);

// Merge manual and generated recommendations
$recommendations = array_merge($recommendations_manual, $recommendations_generated);

// Sort recommendations by created_at descending, manual recommendations first
usort($recommendations, function ($a, $b) {
    $a_time = $a['created_at'] ?? '0000-00-00T00:00:00';
    $b_time = $b['created_at'] ?? '0000-00-00T00:00:00';
    return strcmp($b_time, $a_time);
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Rekomendasi Latihan - Fitpulse</title>
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
                    <li><a href="recommendations.php" class="hover:underline font-semibold underline">Rekomendasi</a></li>
                    <li><a href="progress.php" class="hover:underline">Progres</a></li>
                    <li><a href="profile.php" class="hover:underline">Profil</a></li>
                    <li><a href="logout.php" class="hover:underline">Keluar</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container mx-auto flex-grow p-6 max-w-4xl">
        <h2 class="text-3xl font-semibold mb-6">Rekomendasi Latihan</h2>

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

        <form method="POST" action="recommendations.php" class="mb-8 bg-white p-6 rounded shadow">
            <div>
                <label for="recommended_workout" class="block mb-1 font-semibold">Jenis Latihan yang Direkomendasikan</label>
                <input type="text" id="recommended_workout" name="recommended_workout" required placeholder="Contoh: Pilates, HIIT, Bersepeda" class="w-full border border-gray-300 rounded px-3 py-2" value="<?=htmlspecialchars($_POST['recommended_workout'] ?? '')?>" />
            </div>
            <div class="mt-4">
                <label for="reason" class="block mb-1 font-semibold">Alasan (opsional)</label>
                <textarea id="reason" name="reason" rows="3" class="w-full border border-gray-300 rounded px-3 py-2"><?=htmlspecialchars($_POST['reason'] ?? '')?></textarea>
            </div>
            <button type="submit" class="mt-4 bg-yellow-500 text-white py-2 px-6 rounded hover:bg-yellow-600 transition">Tambah Rekomendasi</button>
        </form>

        <section>
            <h3 class="text-2xl font-semibold mb-4">Rekomendasi Anda</h3>
            <?php if (count($recommendations) === 0): ?>
                <p class="text-gray-600">Belum ada rekomendasi latihan yang dibuat.</p>
            <?php else: ?>
                <ul class="space-y-4">
                    <?php foreach ($recommendations as $rec): ?>
                        <li class="bg-white p-4 rounded shadow">
                            <h4 class="text-xl font-semibold"><?=htmlspecialchars($rec['recommended_workout'])?></h4>
                            <?php if ($rec['reason']): ?>
                                <p class="text-gray-700 mt-1"><?=htmlspecialchars($rec['reason'])?></p>
                            <?php endif; ?>
                            <p class="text-sm text-gray-500 mt-2">Ditambahkan pada <?=htmlspecialchars($rec['created_at'])?></p>
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
