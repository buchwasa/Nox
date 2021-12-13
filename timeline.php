<?php
declare(strict_types=1);

session_start();

if (!isset($_SESSION["username"])) {
    header("Location: login.php");
}

require("./config/config.php");
$mysql = new mysqli(MYSQL_IP, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);

$username = $_SESSION["username"];
$password = $_SESSION["token"];
if (isset($_POST["submit"])) {
    $retrieve = $mysql->prepare("SELECT UserID FROM tUser WHERE username=? AND password=?");
    $retrieve->bind_param("ss", $username, $password);
    $retrieve->execute();
    $result = $retrieve->get_result();
    $cachedRows = $result->fetch_assoc();

    $post = $mysql->prepare("INSERT INTO tPost(UserID, Content) VALUES(?, ?)");
    $post->bind_param("is", $cachedRows["UserID"], $_POST["post"]);
    $post->execute();
}

if(isset($_GET["id"])) {
    $delete = $mysql->prepare("DELETE FROM tPost WHERE PostID=?");
    $delete->bind_param("s", $_GET["id"]);
    $delete->execute();
    header("Location: timeline.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="./assets/index.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Outfit">
    <title>Nox | Timeline</title>
</head>
<body>
<ul id="navbar">
    <li><a href="#">TIMELINE</a></li>
    <li><a href="settings.php">SETTINGS</a></li>
    <li><a href="logout.php">LOGOUT</a></li>
</ul>
<div id="dummydiv"></div>
<form id="newpost" action="<?= $_SERVER['PHP_SELF'] ?>" method="post">
    <textarea name="post"  placeholder="New Post..." id="post" maxlength="200" required></textarea>
    <input type="submit" value="POST" id="submit" name="submit">
</form>
<div id="posts">
    <?php
    $posts = $mysql->prepare("SELECT * FROM tPost INNER JOIN tUser ON tUser.UserID = tPost.UserID ORDER BY PostID DESC");
    $posts->execute();
    $result = $posts->get_result();
    while ($row = $result->fetch_assoc()) {
        $delete = "";
        $id = $row["PostID"];
        $username = $row["Username"];
        if ($username === $_SESSION["username"]) {
            $delete .= "<a href='timeline.php?id=$id'>DELETE</a>";
        }
        $postContent = $row["Content"];
        echo "<div>
                <h3>$username $delete</h3>
                <p>$postContent</p>
              </div>";
    }
    ?>
</div>
</body>
