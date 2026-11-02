<?php

header("Content-Type: application/json; charset=UTF-8");

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$address = trim($_POST["address"] ?? "");

$errors = [];

if ($name === "") {
    $errors["name"] = "يرجى إدخال اسم الطالب.";
}

if ($email === "") {
    $errors["email"] = "يرجى إدخال البريد الإلكتروني.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "يرجى إدخال بريد إلكتروني صحيح.";
}

if ($phone === "") {
    $errors["phone"] = "يرجى إدخال رقم الهاتف.";
}

if ($address === "") {
    $errors["address"] = "يرجى إدخال العنوان.";
}

echo json_encode([
    "valid" => empty($errors),
    "errors" => $errors
], JSON_UNESCAPED_UNICODE);