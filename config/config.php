<?php

const MYSQL_IP = "127.0.0.1";
const MYSQL_USERNAME = "buchwasa_public";
const MYSQL_PASSWORD = "";
const MYSQL_DATABASE = "buchwasa_nox";

function createSession(string $username, string $passwordHash): void
{
    $_SESSION["username"] = $username;
    $_SESSION["token"] = $passwordHash;
}
