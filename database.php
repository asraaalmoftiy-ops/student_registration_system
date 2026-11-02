<?php

$host = "localhost";
$dbname = "student_registration_system";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );


} catch (PDOException $e) {
    error_log($e->getMessage());
    die("تعذّر الاتصال بقاعدة البيانات، يرجى المحاولة لاحقًا.");
}

?>