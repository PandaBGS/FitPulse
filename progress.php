<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $_SESSION['name'] = $_POST['name'];
  $_SESSION['age'] = $_POST['age'];
  $_SESSION['gender'] = $_POST['gender'];
  $_SESSION['weight'] = $_POST['weight'];
  $_SESSION['height'] = $_POST['height'];
  $_SESSION['email'] = $_POST['email'];
  $_SESSION['password'] = $_POST['password'];
  $_SESSION['phone'] = $_POST['phone'];
  $_SESSION['fitness'] = $_POST['fitness'];
}

// Default value
$name_display = isset($_SESSION['name']) && $_SESSION['name'] != "" ? $_SESSION['name'] : "Username";
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
      <div class="profile-pic"></div>
      <h2><?= htmlspecialchars($name_display); ?></h2>
      <button class="save-button" style="width: auto; padding: 8px 16px; background-color: white; color: #3BAFDA; border: 2px solid #3BAFDA;">Customize Profile Picture</button>
    </div>

    <a href="index.php" class="back-button">← Back to Home</a>

    <form class="form-section" method="POST" action="">
      <label>Name</label>
      <input type="text" name="name" value="<?= $_SESSION['name'] ?? '' ?>" placeholder="Enter your name" />

      <label>Age</label>
      <input type="number" name="age" value="<?= $_SESSION['age'] ?? '' ?>" placeholder="Enter your age" />

      <label>Gender</label>
      <select name="gender">
        <option value="Man" <?= (($_SESSION['gender'] ?? '') == 'Man') ? 'selected' : '' ?>>Man</option>
        <option value="Woman" <?= (($_SESSION['gender'] ?? '') == 'Woman') ? 'selected' : '' ?>>Woman</option>
      </select>

      <label>Weight (kg)</label>
      <input type="number" name="weight" value="<?= $_SESSION['weight'] ?? '' ?>" placeholder="Enter your weight" />

      <label>Height (cm)</label>
      <input type="number" name="height" value="<?= $_SESSION['height'] ?? '' ?>" placeholder="Enter your height" />

      <label>E-Mail</label>
      <input type="email" name="email" value="<?= $_SESSION['email'] ?? '' ?>" placeholder="Enter your e-mail" />

      <label>Password</label>
      <input type="password" name="password" value="<?= $_SESSION['password'] ?? '' ?>" placeholder="Your password" />

      <label>Phone Number (Optional)</label>
      <input type="text" name="phone" value="<?= $_SESSION['phone'] ?? '' ?>" placeholder="+62" />

    <div class="fitness-level">
      <label>Fitness Level</label>
      <div class="fitness-options">
        <input type="radio" name="fitness_level" id="beginner" value="Beginner" hidden>
        <label for="beginner" class="fitness-card">Beginner</label>

        <input type="radio" name="fitness_level" id="intermediate" value="Intermediate" hidden>
        <label for="intermediate" class="fitness-card">Intermediate</label>

        <input type="radio" name="fitness_level" id="experienced" value="Experienced" hidden>
        <label for="experienced" class="fitness-card">Experienced</label>
      </div>
    </div>

      <button type="submit" class="save-button">Save</button>
    </form>
  </div>

  <footer>
    <p>&copy; Fitpulse 2025 &nbsp;&nbsp;|&nbsp;&nbsp; <a href="#">Privacy Policy</a> &nbsp;&nbsp;|&nbsp;&nbsp; <a href="#">Terms of Service</a></p>
  </footer>

</body>
</html>
