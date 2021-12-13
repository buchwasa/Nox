<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
}

$username = $_SESSION["username"];
if (isset($_POST["change-password"])) {
    require("./config/config.php");

    $mysql = new mysqli(MYSQL_IP, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
    $retrieve = $mysql->prepare("SELECT * FROM tUser WHERE Username=?");
    $retrieve->bind_param("s", $username);
    $retrieve->execute();
    $result = $retrieve->get_result();

    $passwordHash = $result->fetch_assoc()["Password"];
    $oldPassword = $_POST["old-password"];
    $newPassword = $_POST["new-password"];
    $verified = password_verify($oldPassword, $passwordHash);
    if ($verified) {
        $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $changePassword = $mysql->prepare("UPDATE tUser SET Password=? WHERE Username=? AND Password=?");
        $changePassword->bind_param("sss", $newPasswordHash, $username, $passwordHash);
        $changePassword->execute();
        header("Location: logout.php");
    } else {
        echo "Incorrect password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./assets/index.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Outfit">
    <title>Nox | Settings</title>
</head>
<body>
<ul id="navbar">
    <li><a href="timeline.php">TIMELINE</a></li>
    <li><a href="#">SETTINGS</a></li>
    <li><a href="logout.php">LOGOUT</a></li>
</ul>
<form id="settings" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
    <input type="password" name="old-password" placeholder="OLD PASSWORD" required>
    <input type="password" name="new-password" placeholder="NEW PASSWORD" required>
    <input type="submit" value="CHANGE PASSWORD" name="change-password">
</form>
</body>
</html>
