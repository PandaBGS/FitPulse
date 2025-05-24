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

// Handle form submission to add new schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['confirm_done'])) {
    $workout_date = $_POST['workout_date'] ?? '';
    $workout_time = $_POST['workout_time'] ?? '';
    $workout_type = trim($_POST['workout_type'] ?? '');
    $notes = trim($_POST['notes'] ?? '');

    if (!$workout_date || !$workout_time || !$workout_type) {
        $errors[] = 'Tanggal, waktu, dan jenis latihan harus diisi.';
    } else {
        $schedules = readData(SCHEDULES_FILE);
        $new_schedule = [
            'id' => uniqid(),
            'user_id' => $user_id,
            'workout_date' => $workout_date,
            'workout_time' => $workout_time,
            'workout_type' => $workout_type,
            'notes' => $notes,
            'created_at' => date('c'),
            'updated_at' => date('c')
        ];
        $schedules[] = $new_schedule;
        writeData(SCHEDULES_FILE, $schedules);
        $success = 'Jadwal latihan berhasil ditambahkan.';
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_done'])) {
    $schedule_id = $_POST['schedule_id'] ?? '';
    if ($schedule_id) {
        $schedules_all = readData(SCHEDULES_FILE);
        $progress_all = readData(PROGRESS_FILE);
        foreach ($schedules_all as &$schedule) {
            if ($schedule['id'] === $schedule_id && $schedule['user_id'] === $user_id) {
                // Mark as done by adding to progress
                $progress_all[] = [
                    'id' => uniqid(),
                    'user_id' => $user_id,
                    'log_date' => $schedule['workout_date'],
                    'progress' => "Latihan {$schedule['workout_type']} pada {$schedule['workout_date']} pukul {$schedule['workout_time']} telah dilakukan.",
                    'created_at' => date('c')
                ];
                writeData(PROGRESS_FILE, $progress_all);
                break;
            }
        }
    }
}

$schedules_all = readData(SCHEDULES_FILE);
$schedules = array_filter($schedules_all, function ($schedule) use ($user_id) {
    return $schedule['user_id'] === $user_id;
});

// Handle deletion of schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_schedule_id'])) {
    $delete_id = $_POST['delete_schedule_id'];
    $schedules_all = array_filter($schedules_all, function ($schedule) use ($delete_id) {
        return $schedule['id'] !== $delete_id;
    });
    writeData(SCHEDULES_FILE, array_values($schedules_all));
    header('Location: schedule.php');
    exit;
}

// Sort schedules by date and time
usort($schedules, function ($a, $b) {
    $date_cmp = strcmp($a['workout_date'], $b['workout_date']);
    if ($date_cmp === 0) {
        return strcmp($a['workout_time'], $b['workout_time']);
    }
    return $date_cmp;
});
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Jadwal Latihan - Fitpulse</title>
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
            Jadwal Latihan
        </p>
        <p class="subtitle-text">Atur Waktu Latihanmu</p>
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

        <form method="POST" action="schedule.php" class="mb-8 bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-semibold mb-4 text-gray-800">Tambah Jadwal Baru</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label for="workout_date" class="block mb-1 font-semibold">Tanggal</label>
                    <input type="date" id="workout_date" name="workout_date" required class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800" value="<?=htmlspecialchars($_POST['workout_date'] ?? '')?>" />
                </div>
                <div>
                    <label for="workout_time" class="block mb-1 font-semibold">Waktu</label>
                    <input type="time" id="workout_time" name="workout_time" required class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800" value="<?=htmlspecialchars($_POST['workout_time'] ?? '')?>" />
                </div>
                <div>
                    <label for="workout_type" class="block mb-1 font-semibold">Jenis Latihan</label>
                    <input type="text" id="workout_type" name="workout_type" required placeholder="Contoh: Lari, Yoga, Angkat Beban" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800" value="<?=htmlspecialchars($_POST['workout_type'] ?? '')?>" />
                </div>
            </div>
            <div class="mt-4">
                <label for="notes" class="block mb-1 font-semibold">Catatan (opsional)</label>
                <textarea id="notes" name="notes" rows="3" class="w-full border border-gray-300 rounded px-3 py-2 text-gray-800"><?=htmlspecialchars($_POST['notes'] ?? '')?></textarea>
            </div>
            <button type="submit" class="mt-4 bg-[#acd696] text-white py-2 px-6 rounded hover:bg-[#6b8e6f] transition">Tambah Jadwal</button>
        </form>

        <section>
            <h3 class="text-2xl font-semibold mb-4 text-gray-800">Jadwal Anda</h3>
            <?php if (count($schedules) === 0): ?>
                <p class="text-gray-600">Belum ada jadwal latihan yang dibuat.</p>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white rounded shadow overflow-hidden">
                        <thead class="bg-[#7dbbd9] text-white">
                            <tr>
                                <th class="text-left px-4 py-2">Tanggal</th>
                                <th class="text-left px-4 py-2">Waktu</th>
                                <th class="text-left px-4 py-2">Jenis Latihan</th>
                                <th class="text-left px-4 py-2">Catatan</th>
                                <th class="text-left px-4 py-2">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($schedules as $schedule): ?>
                                <tr class="border-b border-gray-200">
                                    <td class="px-4 py-2 text-gray-800"><?=htmlspecialchars($schedule['workout_date'])?></td>
                                    <td class="px-4 py-2 text-gray-800"><?=htmlspecialchars($schedule['workout_time'])?></td>
                                    <td class="px-4 py-2 text-gray-800"><?=htmlspecialchars($schedule['workout_type'])?></td>
                                    <td class="px-4 py-2 text-gray-800"><?=htmlspecialchars($schedule['notes'])?></td>
                                    <td class="px-4 py-2 flex items-center space-x-2">
                                        <button onclick="confirmDone('<?=htmlspecialchars($schedule['id'])?>')" class="bg-green-600 text-white py-1 px-3 rounded hover:bg-green-700 transition">Selesai</button>
                                        <form method="POST" action="schedule.php" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jadwal latihan ini?');">
                                            <input type="hidden" name="delete_schedule_id" value="<?=htmlspecialchars($schedule['id'])?>" />
                                            <button type="submit" class="bg-red-600 text-white py-1 px-3 rounded hover:bg-red-700 transition">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>
    </main>
    <form method="POST" action="schedule.php" class="mt-6" id="confirmDoneForm">
        <input type="hidden" name="schedule_id" id="schedule_id" value="" />
        <input type="hidden" name="confirm_done" value="1" />
    </form>
    <script>
        function confirmDone(scheduleId) {
            if (confirm('Apakah Anda sudah melakukan latihan ini? Ini akan dicatat sebagai progres.')) {
                document.getElementById('schedule_id').value = scheduleId;
                document.getElementById('confirmDoneForm').submit();
            }
        }
    </script>
    <footer class="bg-gray-200 text-center p-4 text-sm text-gray-600 mt-auto">
        &copy; 2024 Fitpulse. All rights reserved.
    </footer>
</body>
</html>
