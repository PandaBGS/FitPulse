<?php
session_start();
require_once 'config.php'; // Include config.php for readData/writeData and file paths

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$errors = [];
$success = '';

// Load user data
$users = readData(USERS_FILE);
$user_data = null;
$user_index = -1;
foreach ($users as $index => $u) {
    if ($u['id'] === $user_id) {
        $user_data = $u;
        $user_index = $index;
        break;
    }
}

// If user data not found (shouldn't happen if session is set), redirect
if (!$user_data) {
    header('Location: logout.php'); // Or an error page
    exit;
}

// Handle form submission to update profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_data['username'] = trim($_POST['name'] ?? $user_data['username']); // Using 'name' field for username
    $user_data['email'] = trim($_POST['email'] ?? $user_data['email']);
    $user_data['fitness_level'] = $_POST['fitness_level'] ?? $user_data['fitness_level'];
    $user_data['preferences'] = json_encode([ // Storing other details in 'preferences' for simplicity with current schema
        'age' => $_POST['age'] ?? '',
        'gender' => $_POST['gender'] ?? '',
        'weight' => $_POST['weight'] ?? '',
        'height' => $_POST['height'] ?? '',
        'phone' => $_POST['phone'] ?? ''
    ]);
    $user_data['updated_at'] = date('c');

    // Password handling (only update if provided and valid)
    if (!empty($_POST['password'])) {
        // In a real application, you'd also want to ask for current password before changing
        $new_password = $_POST['password'];
        // You might want to add password strength validation here
        $user_data['password_hash'] = password_hash($new_password, PASSWORD_DEFAULT);
    }

    // Update the user data in the array
    $users[$user_index] = $user_data;
    writeData(USERS_FILE, $users); // Save updated data to JSON file

    // Update session username if it changed
    $_SESSION['username'] = $user_data['username'];

    $success = 'Profil berhasil diperbarui.';

    // In a database scenario, you would execute an UPDATE query here:
    // $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, password_hash = ?, fitness_level = ?, preferences = ?, updated_at = NOW() WHERE id = ?");
    // $stmt->execute([$user_data['username'], $user_data['email'], $user_data['password_hash'], $user_data['fitness_level'], $user_data['preferences'], $user_id]);
}

// Extract details from preferences for form display
$preferences_decoded = json_decode($user_data['preferences'] ?? '{}', true);
$age_display = $preferences_decoded['age'] ?? '';
$gender_display = $preferences_decoded['gender'] ?? '';
$weight_display = $preferences_decoded['weight'] ?? '';
$height_display = $preferences_decoded['height'] ?? '';
$phone_display = $preferences_decoded['phone'] ?? '';

$name_display = htmlspecialchars($user_data['username'] ?? 'Username');
$email_display = htmlspecialchars($user_data['email'] ?? '');
$fitness_level_display = htmlspecialchars($user_data['fitness_level'] ?? 'beginner');

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Fitpulse Profile</title>
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #3BAFDA;
    }

    header {
      background-color: #A1D6A5;
      padding: 30px 0;
      text-align: center;
      color: white;
    }

    header h1 {
      margin: 0;
      font-size: 2.5rem;
    }

    header p {
      margin-top: 5px;
      font-size: 1rem;
      color: #e6f5e7;
    }

    .container {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: 40px;
      gap: 50px;
    }

    .profile-pic-section {
      text-align: center;
      color: white;
    }

    .profile-pic {
      width: 180px;
      height: 180px;
      background-color: #A7D8EB;
      border-radius: 50%;
      margin-bottom: 20px;
      display: inline-block;
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 4rem;
      font-weight: bold;
      color: #3BAFDA;
    }

    .form-section {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
      width: 400px;
    }

    .form-section input,
    .form-section select {
      width: 100%;
      padding: 10px;
      margin: 8px 0 16px 0;
      border: 1px solid #ccc;
      border-radius: 8px;
    }

    .form-section label {
      font-weight: 600;
    }

    .fitness-level {
      margin-top: 20px;
    }

    .fitness-level > label {
      font-weight: bold;
      font-size: 16px;
      margin-bottom: 10px;
      display: block;
    }

    .fitness-options {
      display: flex;
      gap: 15px;
      flex-wrap: wrap;
    }

    .fitness-card {
      display: inline-block;
      padding: 12px 20px;
      border: 2px solid #3BAFDA;
      border-radius: 12px;
      cursor: pointer;
      font-weight: 600;
      color: #3BAFDA;
      background-color: white;
      transition: all 0.3s ease;
      user-select: none;
    }

    input[type="radio"]:checked + .fitness-card {
      background-color: #3BAFDA;
      color: white;
    }

    .save-button {
      width: 100%;
      padding: 12px;
      background-color: #93D7AE;
      color: white;
      font-size: 1rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background-color 0.3s ease;
    }

    .save-button:hover {
      background-color: #7cc89c;
    }

    footer {
      text-align: center;
      margin-top: 50px;
      color: white;
    }

    footer a {
      color: white;
      text-decoration: none;
      margin: 0 10px;
    }

    footer a:hover {
      text-decoration: underline;
    }

    .back-button {
      position: absolute;
      top: 20px;
      left: 20px;
      background-color: white;
      color: #3BAFDA;
      padding: 10px 16px;
      border-radius: 8px;
      border: 2px solid #3BAFDA;
      text-decoration: none;
      font-weight: bold;
      z-index: 1000;
      transition: background-color 0.2s, color 0.2s;
    }

    .back-button:hover {
      background-color: #3BAFDA;
      color: white;
    }
  </style>
</head>
<body>

  <header>
    <h1>Welcome to <strong>FITPULSE</strong></h1>
    <p>Your rhythm to a healthier life</p>
  </header>

  <div class="container">
    <div class="profile-pic-section">
      <div class="profile-pic"><?= strtoupper(substr($name_display, 0, 2)) ?></div>
      <h2><?= $name_display ?></h2>
      <button class="save-button" style="width: auto; padding: 8px 16px; background-color: white; color: #3BAFDA; border: 2px solid #3BAFDA;">Customize Profile Picture</button>
    </div>

    <a href="index.php" class="back-button">← Back to Home</a>

    <form class="form-section" method="POST" action="">
      <?php if ($success): ?>
        <div style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center;">
          <?= htmlspecialchars($success) ?>
        </div>
      <?php endif; ?>

      <label>Name</label>
      <input type="text" name="name" value="<?= $name_display ?>" placeholder="Enter your name" required />

      <label>Age</label>
      <input type="number" name="age" value="<?= htmlspecialchars($age_display) ?>" placeholder="Enter your age" />

      <label>Gender</label>
      <select name="gender">
        <option value="Man" <?= ($gender_display == 'Man') ? 'selected' : '' ?>>Man</option>
        <option value="Woman" <?= ($gender_display == 'Woman') ? 'selected' : '' ?>>Woman</option>
        <option value="Other" <?= ($gender_display == 'Other') ? 'selected' : '' ?>>Other</option>
      </select>

      <label>Weight (kg)</label>
      <input type="number" name="weight" value="<?= htmlspecialchars($weight_display) ?>" placeholder="Enter your weight" />

      <label>Height (cm)</label>
      <input type="number" name="height" value="<?= htmlspecialchars($height_display) ?>" placeholder="Enter your height" />

      <label>E-Mail</label>
      <input type="email" name="email" value="<?= $email_display ?>" placeholder="Enter your e-mail" required />

      <label>Password</label>
      <input type="password" name="password" value="" placeholder="Leave blank to keep current password" />

      <label>Phone Number (Optional)</label>
      <input type="text" name="phone" value="<?= htmlspecialchars($phone_display) ?>" placeholder="+62" />

    <div class="fitness-level">
      <label>Fitness Level</label>
      <div class="fitness-options">
        <input type="radio" name="fitness_level" id="beginner" value="beginner" <?= ($fitness_level_display == 'beginner') ? 'checked' : '' ?> hidden>
        <label for="beginner" class="fitness-card">Beginner</label>

        <input type="radio" name="fitness_level" id="intermediate" value="intermediate" <?= ($fitness_level_display == 'intermediate') ? 'checked' : '' ?> hidden>
        <label for="intermediate" class="fitness-card">Intermediate</label>

        <input type="radio" name="fitness_level" id="advanced" value="advanced" <?= ($fitness_level_display == 'advanced') ? 'checked' : '' ?> hidden>
        <label for="advanced" class="fitness-card">Advanced</label>
      </div>
    </div>

      <button type="submit" class="save-button">Save Changes</button>
    </form>
  </div>

  <footer>
    <p>&copy; Fitpulse 2025 &nbsp;&nbsp;|&nbsp;&nbsp; <a href="#">Privacy Policy</a> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="#">Terms of Service</a></p>
  </footer>

</body>
</html>
