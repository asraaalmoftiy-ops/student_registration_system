<?php

session_start();

require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.html");
    exit;
}

$login = trim($_POST["login"] ?? "");
$password = $_POST["password"] ?? "";

if ($login === "" || $password === "") {

    header("Location: login.html?error=empty");
    exit;
}

try {

    $sql = "SELECT * FROM users
            WHERE username = :username
            OR email = :email";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":username" => $login,
        ":email" => $login
    ]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user["password"])) {

        session_regenerate_id(true);

        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];

      
        $redirect = $_SESSION["redirect_after_login"] ?? "view_students.php";
        unset($_SESSION["redirect_after_login"]);

     
        $allowed_pages = [
            "view_students.php",
            "add_student.php",
            "update_student.php",
        ];

        if (!in_array($redirect, $allowed_pages, true)) {
            $redirect = "view_students.php";
        }

        header("Location: " . $redirect);
        exit;

    } else {

        header("Location: login.html?error=invalid");
        exit;
    }

} catch (PDOException $e) {

    error_log($e->getMessage());

    header("Location: login.html?error=server");
    exit;
}