<?php
require_once '../includes/db_connection.php'; // Include your database connection

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT id, username, password_hash FROM users WHERE username = :username");
    $stmt->bindValue(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // Optional: Set cookies for "remember me"
        if ($_POST['remember']) {
            $expiration = time() + 7 * 24 * 60 * 60; // 7 days in seconds
            setcookie('user_id', $user['id'], $expiration);
            setcookie('username', $user['username'], $expiration);
        }

        header('Location: ../chat/index.php');
        exit();
    } else {
        $error_message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<link rel="stylesheet" href="../css/styles.css">
    <title>Login</title>
</head>
<body>
    <div class="login_container">
        <h2>Login</h2>
        <?php if (isset($error_message)) { ?>
            <p><?php echo $error_message; ?></p>
        <?php } ?>
        <form method="POST" action="login.php">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
