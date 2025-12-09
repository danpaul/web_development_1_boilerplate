<?php

// NOTE - for demo/exercise purposes only. For the assignment, use routing and MVC, and proper password hashing/authentication!!!

// start the session
session_start();

// Simple hardcoded credentials for demo
$valid_username = "admin";
$valid_password = "password123";
// password hash generated using `password_hash() function: password_hash("password123", PASSWORD_DEFAULT);
// this string contains the hash algorithm, cost, and salt used to create the hash
// normally this would be saved in the database and retrieved during login
$hashed_password = '$2y$12$bt8BLHOZy7twnOXYB4w9/OHEHngBHdxFg2FFoW1LetdtKWyzL81vi';

$message = "";

// Handle login form submission
if (isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    if (
        $username === $valid_username &&
        // use password_verify to check the password against the hash
        password_verify($password, $hashed_password)
    ) {
        $_SESSION['logged_in'] = true;
        $_SESSION['username'] = $username; // Normally store the user ID as the primary identifier
    } else {
        $message = "<div class='alert alert-danger'>Invalid username or password.</div>";
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: session_demo.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PHP Login Demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <?php echo $message; ?>

                <?php if (!isset($_SESSION['logged_in'])): ?>
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h4 class="text-center mb-3">Login</h4>
                            <form method="POST">
                                <div class="mb-3">
                                    <label class="form-label">Username</label>
                                    <input type="text" name="username" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Password</label>
                                    <input type="password" name="password" class="form-control" required>
                                </div>
                                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                            </form>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card shadow-sm">
                        <div class="card-body text-center">
                            <h4>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h4>
                            <a href="?logout=true" class="btn btn-danger mt-3 w-100">Logout</a>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>

</html>