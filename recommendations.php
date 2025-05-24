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

// Fetch user profile data for recommendation logic (though not strictly used in current generateRecommendations)
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

// Merge manual and generated recommendations, ensuring uniqueness by workout type
$merged_recommendations = [];
foreach ($recommendations_manual as $rec) {
    $merged_recommendations[strtolower($rec['recommended_workout'])] = $rec;
}
foreach ($recommendations_generated as $rec) {
    // Only add if not already present from manual, or if generated provides a better reason
    $workout_key = strtolower($rec['recommended_workout']);
    if (!isset($merged_recommendations[$workout_key]) || (isset($merged_recommendations[$workout_key]) && strpos($merged_recommendations[$workout_key]['reason'], 'Anda sering melakukan latihan ini') === false)) {
        $merged_recommendations[$workout_key] = $rec;
    }
}
$recommendations = array_values($merged_recommendations);


// Sort recommendations by created_at descending (manual recommendations might not have 'created_at' if pulled from a default list)
usort($recommendations, function ($a, $b) {
    $a_time = isset($a['created_at']) ? strtotime($a['created_at']) : 0;
    $b_time = isset($b['created_at']) ? strtotime($b['created_at']) : 0;
    return $b_time - $a_time;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Rekomendasi Latihan - Fitpulse</title>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@900&family=Roboto:wght@700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
        }
        .welcome-text {
            font-family: 'Roboto', sans-serif;
            font-weight: 700;
            font-size: 40px;
            line-height: 48px;
            letter-spacing: 0%;
            text-align: center;
            color: rgba(255 255 255 / 0.9);
        }
        .fitpulse-text {
            font-family: 'Exo 2', sans-serif;
            font-weight: 900;
            font-style: italic;
            font-size: 40px;
            line-height: 48px;
            letter-spacing: 0.07em; /* 7% letter spacing */
            text-align: center;
            color: rgba(255 255 255 / 0.9);
        }
        .subtitle-text {
            font-size: 16px;
            line-height: 1.5;
            color: rgba(255 255 255 / 0.8);
            text-align: center;
        }
    </style>
</head>
<body class="bg-[#3CA7CB] text-white min-h-screen flex flex-col">
    <div class="bg-[#3CA7CB] h-6 w-full"></div>

    <header class="bg-[#acd696b3] w-full py-6 flex flex-col items-center shadow-md relative">
        <p class="welcome-text">
            Rekomendasi Latihan
        </p>
        <p class="subtitle-text">Temukan Latihan Baru Untukmu</p>
        <a href="index.php" class="absolute top-4 left-4 bg-white text-[#3CA7CB] py-1.5 px-4 rounded-md text-sm font-semibold hover:bg-gray-100 transition">
            <i class="fas fa-arrow-left mr-2"></i>Kembali ke Beranda
        </a>
    </header>

    <main class="container mx-auto flex-grow p-6 max-w-4xl text-gray-800">
        <?php if ($errors): ?>
            <div class="mb-4 text-red-600 bg-red-100 p-3 rounded">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><i class="fas fa-exclamation-circle mr-2"></i><?=htmlspecialchars($error)?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif ($success): ?>
            <div class="mb-4 text-green-600 bg-green-100 p-3 rounded">
                <i class="fas fa-check-circle mr-2"></i><?=htmlspecialchars($success)?>
            </div>
        <?php endif; ?>

        <form method="POST" action="recommendations.php" class="mb-8 bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Tambah Rekomendasi Sendiri</h2>
            <div>
                <label for="recommended_workout" class="block mb-1 font-semibold">Jenis Latihan yang Direkomendasikan</label>
                <input type="text" id="recommended_workout" name="recommended_workout" required placeholder="Contoh: Pilates, HIIT, Bersepeda" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800" value="<?=htmlspecialchars($_POST['recommended_workout'] ?? '')?>" />
            </div>
            <div class="mt-4">
                <label for="reason" class="block mb-1 font-semibold">Alasan (opsional)</label>
                <textarea id="reason" name="reason" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800"><?=htmlspecialchars($_POST['reason'] ?? '')?></textarea>
            </div>
            <button type="submit" class="mt-4 bg-[#acd696] text-white py-2 px-6 rounded hover:bg-[#6b8e6f] transition">Tambah Rekomendasi</button>
        </form>

        <section>
            <h3 class="text-2xl font-semibold mb-4 text-gray-800">Rekomendasi Anda</h3>
            <?php if (count($recommendations) === 0): ?>
                <p class="text-gray-600">Belum ada rekomendasi latihan yang dibuat.</p>
            <?php else: ?>
                <ul class="space-y-4">
                    <?php foreach ($recommendations as $rec): ?>
                        <li class="bg-white p-4 rounded shadow">
                            <h4 class="text-xl font-semibold text-gray-800"><?=htmlspecialchars($rec['recommended_workout'])?></h4>
                            <?php if ($rec['reason']): ?>
                                <p class="text-gray-700 mt-1"><?=htmlspecialchars($rec['reason'])?></p>
                            <?php endif; ?>
                            <p class="text-sm text-gray-500 mt-2">Ditambahkan pada <?=htmlspecialchars(isset($rec['created_at']) ? date('d M Y H:i', strtotime($rec['created_at'])) : 'Tanggal tidak tersedia')?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
    <footer class="bg-gray-200 text-center p-4 text-sm text-gray-600 mt-auto">
        &copy; 2024 Fitpulse. All rights reserved.
    </footer>
</body>
</html>
