<?php
declare(strict_types=1);

session_start();

if (isset($_SESSION["username"])) {
    header("Location: timeline.php");
}

if (isset($_POST["login"])) {
    require("./config/config.php");

    $mysql = new mysqli(MYSQL_IP, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
    $retrieve = $mysql->prepare("SELECT * FROM tUser WHERE username=?");
    $retrieve->bind_param("s", $_POST["username"]);
    $retrieve->execute();
    $result = $retrieve->get_result();

    $username = $_POST["username"];
    $password = $_POST["password"];
    if ($result->num_rows > 0) {
        $passwordHash = $result->fetch_assoc()["Password"];
        $verified = password_verify($password, $passwordHash);
        if ($verified) {
            createSession($username, $passwordHash);
            header("Location: timeline.php");
        } else {
            echo "Incorrect password";
        }
    } else {
        echo "Account doesn't exist";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./assets/index.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Outfit">
    <title>Nox | Login</title>
</head>
<body>
<ul id="navbar">
    <li><p>SIMPLE. SAFE. SECURE.</p></li>
</ul>
<form id="login" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
    <input type="text" id="username" name="username" placeholder="USERNAME" required>
    <input type="password" id="password" name="password" placeholder="PASSWORD" required>
    <input type="submit" value="LOGIN" id="submit" name="login">
</form>
</body>
</html>
