<?php
session_start();

// If already logged in, redirect to home
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $host = "localhost";
    $db = "lunch_box";
    $user = "root";
    $pass = "";

    $conn = new mysqli($host, $user, $pass, $db);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Fetch user from database
    $stmt = $conn->prepare("SELECT id, user_name, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $user_name, $hashed_password);
        $stmt->fetch();

        // Verify password
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $user_name;
            header("Location: index.php");
            exit();
        } else {
            $login_error = "Invalid email or password.";
        }
    } else {
        $login_error = "No account found with this email.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Lunch Box</title>
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
    .login-container {
      background: #ffffff;
      padding: 30px;
      border-radius: 12px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.2);
      border: 1px solid #ddd;
    }
    .login-container h2 {
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
    input[type="email"], input[type="password"] {
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
    .register-link {
      text-align: center;
      margin-top: 15px;
      color: #333;
    }
    .register-link a {
      color: #22c55e;
      text-decoration: none;
      font-weight: bold;
    }
</style>
</head>
<body>
  <div class="login-container">
    <h2>Login</h2>
    <?php if ($login_error): ?>
      <p class="error"><?php echo $login_error; ?></p>
    <?php endif; ?>
    <form method="POST" action="">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required placeholder="Enter your email">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required placeholder="Enter your password">
      </div>
      <button type="submit" class="btn">Login</button>
    </form>
    <div class="register-link">
      Don't have an account? <a href="register.php">Register</a>
    </div>
  </div>
</body>
</html>
