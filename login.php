<?php
session_start();
require_once 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $errors[] = 'Username dan password harus diisi.';
    } else {
        $users = readData(USERS_FILE);
        $user = null;
        foreach ($users as $u) {
            if ($u['username'] === $username) {
                $user = $u;
                break;
            }
        }

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header('Location: index.php');
            exit;
        } else {
            $errors[] = 'Username atau password salah.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Masuk - Fitpulse</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
    function togglePassword(button) {
      const input = document.getElementById("password");
      if (input.type === "password") {
        input.type = "text";
        button.textContent = "Hide";
      } else {
        input.type = "password";
        button.textContent = "Show";
      }
    }
    </script>
</head>
<body class="bg-[#3db7d4] min-h-screen flex items-center justify-center font-sans">
  <div class="w-full max-w-md flex flex-col items-center px-6">

    <h1 class="text-4xl font-extrabold mb-8 text-lime-100 tracking-wider">FITPULSE</h1>

    <?php if ($errors): ?>
      <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg w-full" role="alert">
        <ul class="list-disc list-inside text-sm">
          <?php foreach ($errors as $error): ?>
            <li><?=htmlspecialchars($error)?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="POST" action="login.php" class="w-full space-y-5">

      <input type="text" id="username" name="username" placeholder="enter email address"
        class="w-full px-5 py-3 rounded-full text-sm bg-white focus:outline-none focus:ring-2 focus:ring-lime-300"
        value="<?=htmlspecialchars($_POST['username'] ?? '')?>" required />

      <div class="relative">
        <input type="password" id="password" name="password" placeholder="enter password"
        class="w-full px-5 py-3 rounded-full text-sm bg-white focus:outline-none focus:ring-2 focus:ring-lime-300 pr-16"
        required />

        <button type="button" onclick="togglePassword(this)"
        class="absolute right-4 top-1/2 transform -translate-y-1/2 text-sm text-lime-600 hover:underline focus:outline-none">
        Show
        </button>
      </div>

      <button type="submit"
        class="w-full bg-white text-black py-3 rounded-full font-semibold hover:bg-gray-200 transition">Log
        in</button>

      <p class="text-center text-sm">
        Don't have an account?
        <a href="register.php" class="text-lime-100 font-semibold hover:underline">Sign Up</a>
      </p>

      <!-- <button type="button"
        class="w-full bg-white text-black py-3 rounded-full font-semibold hover:bg-gray-200 transition">
        Sign in with Google
      </button> -->
    </form>

    <p class="mt-6 text-sm text-lime-100 hover:underline cursor-pointer">Forgot Your Password?</p>
  </div>
</body>

</body>
</html>
