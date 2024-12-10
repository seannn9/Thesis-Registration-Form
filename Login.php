<?php 
session_start();

$serverName = "DESKTOP-5QTREIB\SQLEXPRESS";
$connectionOptions = [
    "Database" => "WEBAPP",
    "Uid" => "",
    "PWD" => "",
];
$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn == false) {
    die(print_r(sqlsrv_errors(), true));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $passwordHash = md5($password);

    $sql = "SELECT USERNAME, PASSWORD FROM ADMIN WHERE USERNAME = ?";
    $params = [$username];
    $result = sqlsrv_query($conn, $sql, $params);
    
    if ($result == false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $invalidPassErr = "";
    $adminNfErr = "";

    $admin = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);
    if ($admin) {
        if ($admin['PASSWORD'] === $passwordHash) {
            $_SESSION['user_logged_in'] = true;
            header("Location: SelectReports.php");
            exit;
        } else {
            $invalidPassErr = "Invalid password. Please Try again.";
        }
    } else {
        $adminNfErr = "Admin not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./styles/login.css">
    <title>Login</title>
</head>
<body>
    <div class="login-container">
        <img style="scale: 0.65" src="./images/dlsud-with-name.png" alt="DLSUD Logo">
        <h1>Admin Login</h1>
        <hr class="divider">
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="text" id="username" name="username" placeholder="Username" required><br>
            <input type="password" id="password" name="password" placeholder="Password" required><br>
            <button type="submit" name="submit">Login</button>
        </form>
        <?php if (!empty($invalidPassErr)): ?>
            <p><?php echo $invalidPassErr; ?></p>
        <?php endif;?>
        <?php if (!empty($adminNfErr)): ?>
            <p><?php echo $adminNfErr; ?></p>
        <?php endif; ?>
    </div>
</body>
</html>
