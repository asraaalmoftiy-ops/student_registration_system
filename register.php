<?php
require_once "database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";

    if ($username === "" || $email === "" || $password === "" || $confirm_password === "") {

        $message = "يرجى ملء جميع الحقول المطلوبة.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "يرجى إدخال بريد الإلكتروني صحيح.";

    } elseif ($password !== $confirm_password) {

        $message = "كلمتا المرور غير متطابقتين.";

    } else {

        try {
            $check_sql = "SELECT id FROM users WHERE username = :username OR email = :email";

            $check_stmt = $pdo->prepare($check_sql);

            $check_stmt->execute([
                ":username" => $username,
                ":email" => $email
            ]);

            if ($check_stmt->fetch()) {

                $message = "اسم المستخدم أو البريد الإلكتروني مستخدم بالفعل.";

            } else {

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, email, password)
                        VALUES (:username, :email, :password)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":username" => $username,
                    ":email" => $email,
                    ":password" => $hashed_password
                ]);

                $message = "تم إنشاء الحساب بنجاح!";
            }

        } catch (PDOException $e) {

            $message = "فشل إنشاء الحساب, يرجى المحاولة مرة أخرى.";

        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إنشاء حساب جديد</title>
</head>
<body>
<nav class="navbar navbar-expand-lg bg-body-tertiary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.html">نظام تسجيل الطلاب</a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="القائمة">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
               
                <li class="nav-item"><a class="nav-link" href="view_students.php">عرض الطلاب</a></li>
                <li class="nav-item"><a class="nav-link" href="add_student.php">إضافة طالب</a></li>
                <li class="nav-item"><a class="nav-link" href="login.html">تسجيل الدخول</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">تسجيل الخروج</a></li>
                <li class="nav-item">
                    <button id="theme-toggle" class="btn btn-outline-secondary btn-sm" type="button"></button>
                </li>
            </ul>
        </div>
    </div>
</nav>
    <h1>إنشاء حساب جديد</h1>
    <?php if ($message !== ""): ?>
    <p><?= htmlspecialchars($message, ENT_QUOTES, "UTF-8") ?></p>
    <?php endif; ?>

    <form action="register.php" method="POST">

        <label>اسم المستخدم:</label>
        <input type="text" name="username" required>
        <br><br>

        <label>البريد الإلكتروني:</label>
        <input type="email" name="email" required>
        <br><br>

        <label>كلمة المرور:</label>
        <input type="password" name="password" required>
        <br><br>

        <label>تأكيد كلمة المرور:</label>
        <input type="password" name="confirm_password" required>
        <br><br>

        <button type="submit">إنشاء حساب</button>

    </form>
 <script src="theme.js"></script>
</body>
</html>