<?php

require_once "auth.php";
require_once "database.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: view_students.php");
    exit;
}

$id = filter_input(INPUT_POST, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    die("رقم الطالب غير صحيح.");
}

try {

    $sql = "SELECT profile_picture FROM students WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":id" => $id]);

    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        header("Location: view_students.php?deleted=0");
        exit;
    }

    $image_path = $student["profile_picture"];

    $sql = "DELETE FROM students WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([":id" => $id]);

    if ($stmt->rowCount() > 0) {

        if (!empty($image_path)) {

            $uploads_directory = realpath(__DIR__ . "/uploads");

            $full_image_path = realpath(
                __DIR__ . "/" . ltrim($image_path, "/\\")
            );

            if (
                $uploads_directory !== false &&
                $full_image_path !== false &&
                is_file($full_image_path) &&
                strpos(
                    $full_image_path,
                    $uploads_directory . DIRECTORY_SEPARATOR
                ) === 0
            ) {

                if (!unlink($full_image_path)) {
                    error_log(
                        "تعذّر حذف صورة الطالب: " . $full_image_path
                    );
                }
            }
        }

        header("Location: view_students.php?deleted=1");
        exit;

    } else {

        header("Location: view_students.php?deleted=0");
        exit;
    }

} catch (PDOException $e) {

    error_log("حدث خطأ أثناء حذف الطالب: " . $e->getMessage());
    die("تعذّر حذف الطالب، يرجى المحاولة مرة أخرى.");
}
?>