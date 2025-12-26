<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'] ?? '';
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($role === 'guest') {
        // Guest login
        $found = false;
        if (isset($_SESSION['guests'])) {
            foreach ($_SESSION['guests'] as $guest) {
                if ($guest['username'] === $username && password_verify($password, $guest['password'])) {
                    $_SESSION['username'] = $guest['username'];
                    $_SESSION['role'] = 'guest';
                    $found = true;
                    header("Location: guest-dashboard.php");
                    exit();
                }
            }
        }
        if (!$found) $error = "Guest not found. Please register first.";
    } else {
        // Staff/Admin login
        $users = [
            ['username'=>'admin','password'=>password_hash('admin123',PASSWORD_DEFAULT),'role'=>'admin'],
            ['username'=>'staff','password'=>password_hash('staff123',PASSWORD_DEFAULT),'role'=>'staff']
        ];
        $found = false;
        foreach ($users as $user) {
            if ($user['username']===$username && password_verify($password,$user['password']) && $user['role']===$role) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $role;
                $found = true;
                header("Location: {$role}-dashboard.php");
                exit();
            }
        }
        if (!$found) $error = "Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Hotel System</title>
<link rel="stylesheet" href="css/style.css">
<style>
.form-box { max-width:400px; margin:50px auto; padding:20px; border:1px solid #ddd; border-radius:10px; background:#f9f9f9;}
.form-box input, .form-box select, .form-box button { width:100%; padding:10px; margin:10px 0;}
button { background:#4CAF50; color:white; border:none; cursor:pointer;}
button:hover { background:#45a049; }
.error { color:red; }
</style>
</head>
<body>
<header><h1>Login</h1></header>
<main>
<div class="form-box">
<?php if($error) echo "<p class='error'>$error</p>"; ?>
<form method="POST" id="loginForm">
<label>Role</label>
<select name="role" id="roleSelect" required>
<option value="">Select Role</option>
<option value="guest">Guest</option>
<option value="staff">Staff</option>
<option value="admin">Admin</option>
</select>

<label>Username</label><input type="text" name="username" required>
<label>Password</label><input type="password" name="password" required>
<button type="submit">Login</button>
</form>
<p>New Guest? <a href="guest-register.php">Register here</a></p>
</div>
</main>

<script>
// Redirect new guests to registration automatically
document.getElementById("roleSelect").addEventListener("change", function() {
    if(this.value === "guest") {
        // Only redirect if no username entered yet
        var usernameField = document.querySelector('input[name="username"]');
        if(!usernameField.value) {
            window.location.href = "guest-register.php";
        }
    }
});
</script>
</body>
</html>
