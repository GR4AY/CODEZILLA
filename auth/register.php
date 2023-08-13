<?php
require_once '../includes/db_connection.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $birthday = $_POST['birthday'];

    if ($password === $confirm_password) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO users (username, password_hash, email, phone_number, birthday) VALUES (:username, :password_hash, :email, :phone_number, :birthday)";
        $stmt = $db->prepare($query);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':password_hash', $password_hash);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':phone_number', $phone_number);
        $stmt->bindValue(':birthday', $birthday);
        $stmt->execute();

        $_SESSION['user_id'] = $db->lastInsertId();
        $_SESSION['username'] = $username;

        header('Location: login.php');
        exit();
    } else {
        $error_message = "Passwords do not match.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="../css/styles.css">
    <title>Register</title>
</head>
<body>
    <div class="register_container">
        <h2>Register</h2>
        <?php if (isset($error_message)) { ?>
            <p><?php echo $error_message; ?></p>
        <?php } ?>
        <form method="POST" action="register.php">
            <input type="text" name="username" placeholder="Username" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <input type="password" name="confirm_password" placeholder="Confirm Password" required><br>
            <input type="email" name="email" placeholder="Email" required><br>
            <input type="tel" name="phone_number" placeholder="Phone Number" required><br>
            <input type="date" name="birthday" placeholder="Birthday" required><br>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
