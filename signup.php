<?php
session_start();

$register_error = "";
$register_success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $db = "lunch_box";
    $user = "root";
    $pass = "";

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $name = trim($_POST['name']);
    $age = intval($_POST['age']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $user_type = trim($_POST['user_type']); // New field

    if ($password !== $confirm_password) {
        $register_error = "Passwords do not match!";
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $register_error = "Email already registered!";
        } else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert into DB with user_type
            $stmt = $conn->prepare("INSERT INTO users (user_name, age, email, password, user_type) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sisss", $name, $age, $email, $hashed_password, $user_type);

            if ($stmt->execute()) {
                $register_success = "Registration successful! You can now login.";
            } else {
                $register_error = "Error: " . $stmt->error;
            }
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Sign Up - Lunch Box</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
    body {
      font-family: Arial, sans-serif;
      background: white;
      color: black;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .register-container {
      background: #ffffff;
      padding: 30px;
      border-radius: 12px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.2);
      border: 1px solid #ddd;
    }
    .register-container h2 {
      text-align: center;
      color: #111;
      margin-bottom: 20px;
    }
    .form-group {
      margin-bottom: 15px;
    }
    label {
      display: block;
      margin-bottom: 5px;
      color: #333;
    }
    input, select {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      background: #f9f9f9;
      color: black;
    }
    .btn {
      display: block;
      width: 100%;
      padding: 12px;
      background: linear-gradient(90deg, #22c55e, #38bdf8);
      border: none;
      color: white;
      font-weight: bold;
      border-radius: 6px;
      cursor: pointer;
      font-size: 16px;
    }
    .btn:hover {
      opacity: 0.9;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 10px;
    }
    .success {
      color: green;
      text-align: center;
      margin-bottom: 10px;
    }
    .login-link {
      text-align: center;
      margin-top: 15px;
      color: #333;
    }
    .login-link a {
      color: #22c55e;
      text-decoration: none;
      font-weight: bold;
    }
</style>
<script>
function validateForm() {
    let pass = document.getElementById("password").value;
    let confirmPass = document.getElementById("confirm_password").value;
    if (pass !== confirmPass) {
        alert("Passwords do not match!");
        return false;
    }
    return true;
}
</script>
</head>
<body>
  <div class="register-container">
    <h2>Sign Up</h2>
    <?php if ($register_error): ?>
      <p class="error"><?php echo $register_error; ?></p>
    <?php endif; ?>
    <?php if ($register_success): ?>
      <p class="success"><?php echo $register_success; ?></p>
    <?php endif; ?>
    <form method="POST" action="" onsubmit="return validateForm()">
      <div class="form-group">
        <label>Name</label>
        <input type="text" name="name" required placeholder="Enter your name">
      </div>
      <div class="form-group">
        <label>Age</label>
        <input type="number" name="age" required placeholder="Enter your age">
      </div>
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required placeholder="Enter your email">
      </div>
      <div class="form-group">
        <label>User Type</label>
        <select name="user_type" required>
          <option value="">-- Select Type --</option>
          <option value="Student">Student</option>
          <option value="College">College</option>
          <option value="University">University</option>
          <option value="Adult">Adult</option>
        </select>
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" id="password" required placeholder="Enter your password">
      </div>
      <div class="form-group">
        <label>Confirm Password</label>
        <input type="password" name="confirm_password" id="confirm_password" required placeholder="Confirm your password">
      </div>
      <button type="submit" class="btn">Register</button>
    </form>
    <div class="login-link">
      Already have an account? <a href="login.php">Login</a>
    </div>
  </div>
</body>
</html>
