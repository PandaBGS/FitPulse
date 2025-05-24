<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

$errors = [];
$success = '';

// Handle Schedule Workout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['schedule_workout'])) {
    $workout_date = $_POST['schedule_date'] ?? '';
    $workout_type = trim($_POST['schedule_exercise'] ?? '');
    $workout_time = '00:00'; // Default time if not provided in index page

    if (!$workout_date || !$workout_type) {
        $errors[] = 'Tanggal dan jenis latihan harus diisi.';
    } else {
        $schedules = readData(SCHEDULES_FILE);
        $new_schedule = [
            'id' => uniqid(),
            'user_id' => $user_id,
            'workout_date' => $workout_date,
            'workout_time' => $workout_time,
            'workout_type' => $workout_type,
            'notes' => 'Scheduled from homepage',
            'created_at' => date('c'),
            'updated_at' => date('c')
        ];
        $schedules[] = $new_schedule;
        writeData(SCHEDULES_FILE, $schedules);
        $success = 'Jadwal latihan berhasil ditambahkan.';
    }
}

// Handle Log Your Workout form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['log_workout'])) {
    $workout_name = trim($_POST['log_workout_name'] ?? '');
    $duration = trim($_POST['log_duration'] ?? '');
    $log_date = date('Y-m-d'); // Log date is today

    if (!$workout_name || !$duration) {
        $errors[] = 'Nama latihan dan durasi harus diisi.';
    } else {
        $progress_logs = readData(PROGRESS_FILE);
        $new_log = [
            'id' => uniqid(),
            'user_id' => $user_id,
            'log_date' => $log_date,
            'progress' => "Latihan $workout_name selama $duration telah dilakukan.",
            'created_at' => date('c')
        ];
        $progress_logs[] = $new_log;
        writeData(PROGRESS_FILE, $progress_logs);
        $success = 'Workout berhasil dicatat.';
    }
}

// Fetch user's recent workouts from progress.json
$recent_workouts = readData(PROGRESS_FILE);
$user_recent_workouts = array_filter($recent_workouts, function ($log) use ($user_id) {
    return $log['user_id'] === $user_id;
});

// Sort recent workouts by creation date, newest first
usort($user_recent_workouts, function ($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});

// Take top 2 recent workouts for display
$display_recent_workouts = array_slice($user_recent_workouts, 0, 2);

// Fetch user's recommended workouts from recommendations.json
$recommendations_all = readData(RECOMMENDATIONS_FILE);
$user_recommendations = array_filter($recommendations_all, function ($rec) use ($user_id) {
    return $rec['user_id'] === $user_id;
});
// Get unique recommendations for display
$unique_recommendations = [];
foreach ($user_recommendations as $rec) {
    $unique_recommendations[$rec['recommended_workout']] = $rec;
}
$display_recommendations = array_values($unique_recommendations);
// Take top 3 recommendations for display, if fewer, show what's available
$display_recommendations = array_slice($display_recommendations, 0, 3);

// Fallback for recommended workouts if none generated/manual
if (empty($display_recommendations)) {
    $display_recommendations = [
        ['recommended_workout' => 'HIIT Workout', 'reason' => '20 mins'],
        ['recommended_workout' => 'Yoga Flow', 'reason' => '45 mins'],
        ['recommended_workout' => 'Lower Body Training', 'reason' => '40 mins']
    ];
}

?>

<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>FitPulse</title>
    <link href="https://fonts.googleapis.com/css2?family=Exo+2:wght@900&family=Roboto:wght@700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
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

    <div class="bg-[#acd696b3] w-full py-6 flex flex-col items-center">
        <p class="welcome-text">
            Welcome to <span class="fitpulse-text">FITPULSE</span>
        </p>
        <p class="subtitle-text">Your Rythm to a Healthier Life</p>
    </div>

    <main class="max-w-[1440px] mx-auto px-[170px] py-[60px] space-y-16">
        <div class="flex justify-between gap-[40px] items-start">
            <div class="flex items-start space-x-3">
                <img
                    alt="User profile picture placeholder"
                    class="rounded-full mt-1"
                    height="40"
                    src="https://placehold.co/40x40/7ea98d/white/png?text=<?= strtoupper(substr($username, 0, 2)) ?>"
                    width="40"
                />
                <div class="text-xs leading-tight">
                    <p class="font-semibold"><?= htmlspecialchars($username) ?></p>
                    <p class="text-[#a0c4db]">Member</p>
                </div>
            </div>
             <a href="profile.php"
                class="bg-[#acd696] text-white text-xs font-semibold py-1.5 px-4 rounded hover:bg-[#6b8e6f] transition mt-1"
            >
                Edit Profile
            </a>
        </div>

        <?php if ($errors): ?>
            <div class="mb-4 text-red-600 bg-red-100 p-3 rounded">
                <ul class="list-disc list-inside">
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

        <div class="flex items-start gap-12">
            <h2 class="font-semibold text-sm w-1/3 text-center mt-2">
                Schedule Your Workout
            </h2>
            <form class="flex flex-col space-y-4 w-2/3" method="post" action="index.php">
                <input type="hidden" name="schedule_workout" value="1">
                <div class="relative">
                    <input
                        type="date"
                        name="schedule_date"
                        placeholder="Select date"
                        class="w-full rounded-md py-2 px-3 text-xs text-black placeholder:text-black/50"
                        value="<?= htmlspecialchars($_POST['schedule_date'] ?? date('Y-m-d')) ?>"
                        required
                    />
                    <i class="fas fa-calendar-alt absolute right-3 top-2.5 text-black text-xs"></i>
                </div>
                <input
                    type="text"
                    name="schedule_exercise"
                    placeholder="What's the Exercise ?"
                    class="w-full rounded-md py-2 px-3 text-xs text-black placeholder:text-black/50"
                    value="<?= htmlspecialchars($_POST['schedule_exercise'] ?? '') ?>"
                    required
                />
                <button
                    type="submit"
                    class="bg-white text-[#3a9ccf] text-xs font-semibold rounded-md py-1.5"
                >
                    Save Schedule
                </button>
            </form>
        </div>

         <div class="flex justify-center gap-4 mt-8">
            <a href="recommendations.php" class="bg-[#acd696] text-white text-xs font-semibold py-1.5 px-4 rounded hover:bg-[#6b8e6f] transition">
                Go to Recommendations
            </a>
            <a href="schedule.php" class="bg-[#acd696] text-white text-xs font-semibold py-1.5 px-4 rounded hover:bg-[#6b8e6f] transition">
                Go to Schedules
            </a>
        </div>

        <section>
            <h2 class="text-center font-semibold text-sm mb-3">Recommended Workouts</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 text-center text-xs text-[#a0c4db]">
                <?php if (!empty($display_recommendations)): ?>
                    <?php foreach ($display_recommendations as $rec): ?>
                        <div class="bg-[#7dbbd9]/40 rounded-lg p-4">
                            <?= htmlspecialchars($rec['recommended_workout']) ?><br />
                            <?php if (!empty($rec['reason'])): ?>
                                <?= htmlspecialchars($rec['reason']) ?>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-gray-400">No recommendations available yet.</div>
                <?php endif; ?>
            </div>
        </section>

        <section>
            <h2 class="text-sm font-semibold mb-3">Log Your Workout</h2>
            <form class="flex flex-col md:flex-row md:items-center gap-3" method="post" action="index.php">
                <input type="hidden" name="log_workout" value="1">
                <input
                    type="text"
                    name="log_workout_name"
                    placeholder="Workout Name"
                    class="flex-1 rounded-md py-2 px-3 text-xs text-black placeholder:text-black/50"
                    value="<?= htmlspecialchars($_POST['log_workout_name'] ?? '') ?>"
                    required
                />
                <input
                    type="text"
                    name="log_duration"
                    placeholder="Duration (e.g. 30 mins)"
                    class="flex-1 rounded-md py-2 px-3 text-xs text-black placeholder:text-black/50"
                    value="<?= htmlspecialchars($_POST['log_duration'] ?? '') ?>"
                    required
                />
                <div class="flex gap-2">
                    <button
                        type="reset"
                        class="bg-white border border-gray-300 text-gray-700 text-xs rounded-md py-1.5 px-4"
                    >Cancel</button>
                    <button
                        type="submit"
                        class="bg-[#acd696] text-white text-xs font-semibold py-1.5 px-4 rounded"
                    >Save Workout</button>
                </div>
            </form>
        </section>

        <section>
            <h2 class="text-sm font-semibold mb-3">Recent Workouts</h2>
            <div class="flex flex-wrap gap-6 text-xs text-center">
                <?php if (!empty($display_recent_workouts)): ?>
                    <?php foreach ($display_recent_workouts as $log): ?>
                        <div>
                            <img src="https://placehold.co/40x40/7dbbd9/white/png?text=<?= urlencode(strtoupper(substr(explode(' ', $log['progress'])[1], 0, 1))) ?>" alt="Workout Icon" class="mx-auto" />
                            <p><?= htmlspecialchars(explode(' pada ', $log['progress'])[0]) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-gray-400">No recent workouts logged.</p>
                <?php endif; ?>
            </div>
        </section>

        <section>
            <h2 class="text-sm font-semibold mb-3 text-center">Community Updates</h2>
            <div class="bg-[#7dbbd9]/40 p-4 rounded-md text-xs text-center">
                <p class="mb-2">No updates available</p>
                <p class="text-[#ffffff]/70">Stay tuned for social and fitness news!</p>
            </div>
        </section>

        <footer class="text-center text-xs text-white/70 pt-10 border-t border-white/20">
            <p class="mb-2">© FitPulse 2025</p>
            <div class="flex justify-center gap-4">
                <a href="#" class="hover:underline">Privacy Policy</a>
                <a href="#" class="hover:underline">Terms of Service</a>
            </div>
        </footer>
    </main>
</body>
</html>
