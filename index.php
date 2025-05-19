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
<body class="bg-blue-500 text-white">

    <div class="bg-[#A1D6A5] p-6 text-center">
        <h1 class="text-4xl font-bold">Welcome to <span class="text-blue-500">FITPULSE</span></h1>
        <p class="text-lg">Your rhythm to a healthier life</p>
    </div>

    <div class="max-w-4xl mx-auto py-8">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center">
                <div class="w-16 h-16 bg-gray-300 rounded-full"></div>
                <h2 class="ml-4 text-lg">John Doe</h2>
                <button class="ml-2 bg-green-400 text-blue-500 py-1 px-3 rounded">Edit Profile</button>
            </div>
        </div>

        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Schedule Your Workout</h2>
            <input type="date" class="mr-2 rounded p-2" placeholder="Select date">
            <input type="text" class="mr-2 rounded p-2" placeholder="What's the Exercise?">
            <button class="bg-green-400 text-blue-500 py-2 px-4 rounded">Save</button>
        </div>

        <h2 class="text-2xl font-bold mb-4">Recommended Workouts</h2>
        <div class="grid grid-cols-3 gap-4 mb-8">
            <div class="bg-blue-300 p-4 rounded">
                <h3 class="font-bold">HIIT Workout</h3>
                <p>30 mins</p>
            </div>
            <div class="bg-blue-300 p-4 rounded">
                <h3 class="font-bold">Yoga Flow</h3>
                <p>45 mins</p>
            </div>
            <div class="bg-blue-300 p-4 rounded">
                <h3 class="font-bold">Strength Training</h3>
                <p>60 mins</p>
            </div>
        </div>

        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-4">Log Your Workout</h2>
            <input type="text" class="mb-2 w-full rounded p-2" placeholder="Workout Type (e.g. Running, Weightlifting)">
            <input type="text" class="mb-2 w-full rounded p-2" placeholder="Duration (e.g. 45 mins)">
            <div class="flex justify-between">
                <button class="bg-gray-500 text-white py-2 px-4 rounded">Cancel</button>
                <button class="bg-green-400 text-blue-500 py-2 px-4 rounded">Save Workout</button>
            </div>
        </div>

        <h2 class="text-2xl font-bold mb-4">Recent Workouts</h2>
        <div class="flex justify-between mb-8">
            <div class="bg-blue-300 p-4 rounded w-1/3">
                <h3 class="font-bold">Strength Training</h3>
                <p>60 mins</p>
            </div>
            <div class="bg-blue-300 p-4 rounded w-1/3">
                <h3 class="font-bold">Running</h3>
                <p>45 mins</p>
            </div>
        </div>

        <h2 class="text-2xl font-bold mb-4">Community Updates</h2>
        <div class="bg-blue-300 p-4 rounded mb-8">
            <h3 class="font-bold">FitnessFanatic</h3>
            <p>Finished a great workout today! Feeling pumped 💪</p>
        </div>
        
        <footer class="text-center text-sm">
            <p>© Fitpulse 2025 <span class="mx-2">|</span> <a href="#" class="text-green-400">Privacy Policy</a> <span class="mx-2">|</span> <a href="#" class="text-green-400">Terms of Service</a></p>
        </footer>
    </div>

</body>
</html>

</body>
</html>
