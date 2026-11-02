<?php
session_start();

if (!isset($_SESSION["user_id"])) {
  
    $_SESSION["redirect_after_login"] = basename($_SERVER["PHP_SELF"]);

    header("Location: login.html");
    exit;
}
?>