<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$username = $_SESSION['username'];
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

    <!-- Top bar -->
    <div class="bg-[#3CA7CB] h-6 w-full"></div>

    <!-- Welcome section -->
    <div class="bg-[#acd696b3] w-full py-6 flex flex-col items-center">
        <p class="welcome-text">
            Welcome to <span class="fitpulse-text">FITPULSE</span>
        </p>
        <p class="subtitle-text">Your Ryhtm to a Healthier Life</p>
    </div>

</body>
</html>
    <main class="max-w-[1440px] mx-auto px-[170px] py-[60px] space-y-16">
        <!-- User Info and Edit Profile -->
        <div class="flex justify-between gap-[40px] items-start">
            <div class="flex items-start space-x-3">
                <img
                    alt="User profile picture placeholder"
                    class="rounded-full mt-1"
                    height="40"
                    src="https://placehold.co/40x40/7ea98d/white/png?text=JD"
                    width="40"
                />
                <div class="text-xs leading-tight">
                    <p class="font-semibold">John Doe</p>
                    <p class="text-[#a0c4db]">Member</p>
                </div>
            </div>
            <button
                class="bg-[#acd696] text-white text-xs font-semibold py-1.5 px-4 rounded hover:bg-[#6b8e6f] transition mt-1"
            >
                Edit Profile
            </button>
        </div>

        <!-- Schedule Workout -->
        <div class="flex items-start gap-12">
            <h2 class="font-semibold text-sm w-1/3 text-center mt-2">
                Schedule Your Workout
            </h2>
            <form class="flex flex-col space-y-4 w-2/3" method="post">
                <div class="relative">
                    <input
                        type="text"
                        placeholder="Select date"
                    class="w-full rounded-md py-2 px-3 text-xs text-black placeholder:text-black/50"
                    />
                    <i class="fas fa-calendar-alt absolute right-3 top-2.5 text-black text-xs"></i>
                </div>
                <input
                    type="text"
                    placeholder="What's the Exercise ?"
                    class="w-full rounded-md py-2 px-3 text-xs text-black placeholder:text-black/50"
                />
                <button
                    type="submit"
                    class="bg-white text-[#3a9ccf] text-xs font-semibold rounded-md py-1.5"
                >
                    Save
                </button>
            </form>
        </div>

        <!-- Recommended Workouts -->
        <section>
            <h2 class="text-center font-semibold text-sm mb-3">Recommended Workouts</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 text-center text-xs text-[#a0c4db]">
                <div class="bg-[#7dbbd9]/40 rounded-lg p-4">HIIT Workout<br />20 mins</div>
                <div class="bg-[#7dbbd9]/40 rounded-lg p-4">Yoga Flow<br />45 mins</div>
                <div class="bg-[#7dbbd9]/40 rounded-lg p-4">Lower Body Training<br />40 mins</div>
            </div>
        </section>

        <!-- Log Workout -->
        <section>
            <h2 class="text-sm font-semibold mb-3">Log Your Workout</h2>
            <form class="flex flex-col md:flex-row md:items-center gap-3">
                <input
                    type="text"
                    placeholder="Workout Name"
                    class="flex-1 rounded-md py-2 px-3 text-xs text-black placeholder:text-black/50"
                />
                <input
                    type="text"
                    placeholder="Duration (e.g. 30 mins)"
                    class="flex-1 rounded-md py-2 px-3 text-xs text-black placeholder:text-black/50"
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

        <!-- Recent Workouts -->
        <section>
            <h2 class="text-sm font-semibold mb-3">Recent Workouts</h2>
            <div class="flex flex-wrap gap-6 text-xs text-center">
                <div>
                    <img src="https://placehold.co/40x40" alt="Strength" class="mx-auto" />
                    <p>Strength Training</p>
                </div>
                <div>
                    <img src="https://placehold.co/40x40" alt="Running" class="mx-auto" />
                    <p>Running</p>
                </div>
            </div>
        </section>

        <!-- Community Updates -->
        <section>
            <h2 class="text-sm font-semibold mb-3 text-center">Community Updates</h2>
            <div class="bg-[#7dbbd9]/40 p-4 rounded-md text-xs text-center">
                <p class="mb-2">No updates available</p>
                <p class="text-[#ffffff]/70">Stay tuned for social and fitness news!</p>
            </div>
        </section>

        <!-- Footer -->
        <footer class="text-center text-xs text-white/70 pt-10 border-t border-white/20">
            <p class="mb-2">© FitPulse 2025</p>
            <div class="flex justify-center gap-4">
                <a href="#" class="hover:underline">Privacy Policy</a>
                <a href="#" class="hover:underline">Terms of Service</a>
            </div>
        </footer>
    </main>
