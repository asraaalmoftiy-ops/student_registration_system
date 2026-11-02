<?php

require_once "auth.php";
require_once "database.php";

// الحصول على رقم الطالب من الرابط
$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

// التأكد من أن رقم الطالب صحيح
if ($id === false || $id === null || $id < 1) {
    die("رقم الطالب غير صحيح.");
}

// جلب بيانات الطالب من قاعدة البيانات
$sql = "SELECT * FROM students WHERE id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute([":id" => $id]);

$student = $stmt->fetch(PDO::FETCH_ASSOC);

// التأكد من وجود الطالب
if (!$student) {
    die("لم يتم العثور على الطالب.");
}

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // الحصول على البيانات المدخلة
    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");

    // الاحتفاظ بالصورة الحالية افتراضيًا
    $profile_picture = $student["profile_picture"] ?? null;

    $old_picture = $profile_picture;

    // التأكد من تعبئة الحقول المطلوبة
    if ($name === "" || $email === "" || $address === "") {

        $message = "يرجى تعبئة جميع الحقول المطلوبة.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        // التأكد من صحة البريد الإلكتروني
        $message = "يرجى إدخال بريد إلكتروني صحيح.";
        $message_type = "error";

    } else {

        $new_picture_path = null;

        try {

            // التحقق من وجود صورة جديدة
            if (
                isset($_FILES["profile_picture"]) &&
                $_FILES["profile_picture"]["error"] !== UPLOAD_ERR_NO_FILE
            ) {

                if ($_FILES["profile_picture"]["error"] !== UPLOAD_ERR_OK) {
                    throw new RuntimeException("حدث خطأ أثناء رفع الصورة.");
                }

                // تحديد الحجم الأقصى للصورة: 2 ميغابايت
                if ($_FILES["profile_picture"]["size"] > 2 * 1024 * 1024) {
                    throw new RuntimeException("يجب ألا يتجاوز حجم الصورة 2 ميغابايت.");
                }

                // التحقق من نوع الصورة باستخدام محتواها
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime_type = $finfo->file($_FILES["profile_picture"]["tmp_name"]);

                $allowed_types = [
                    "image/jpeg" => "jpg",
                    "image/png" => "png",
                    "image/webp" => "webp"
                ];

                if (!isset($allowed_types[$mime_type])) {
                    throw new RuntimeException(
                        "نوع الصورة غير مدعوم. استخدمي JPG أو PNG أو WebP."
                    );
                }

                // التأكد من أن الملف صورة فعلًا
                if (getimagesize($_FILES["profile_picture"]["tmp_name"]) === false) {
                    throw new RuntimeException("الملف المرفوع ليس صورة صالحة.");
                }

                // إنشاء مجلد الرفع عند عدم وجوده
                $upload_dir = __DIR__ . "/uploads/";

                if (!is_dir($upload_dir)) {
                    if (!mkdir($upload_dir, 0755, true) && !is_dir($upload_dir)) {
                        throw new RuntimeException("تعذر إنشاء مجلد الصور.");
                    }
                }

                // إنشاء اسم عشوائي للصورة
                $filename = bin2hex(random_bytes(16))
                    . "." . $allowed_types[$mime_type];

                $destination = $upload_dir . $filename;

                // نقل الصورة إلى مجلد uploads
                if (!move_uploaded_file(
                    $_FILES["profile_picture"]["tmp_name"],
                    $destination
                )) {
                    throw new RuntimeException("تعذر حفظ الصورة المرفوعة.");
                }

                $new_picture_path = "uploads/" . $filename;
                $profile_picture = $new_picture_path;
            }
                // تحديث بيانات الطالب
            $sql = "UPDATE students
                    SET name = :name,
                        email = :email,
                        phone = :phone,
                        address = :address,
                        profile_picture = :profile_picture
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":name" => $name,
                ":email" => $email,
                ":phone" => $phone,
                ":address" => $address,
                ":profile_picture" => $profile_picture,
                ":id" => $id
            ]);

            // جلب البيانات بعد التعديل
            $sql = "SELECT * FROM students WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([":id" => $id]);

            $student = $stmt->fetch(PDO::FETCH_ASSOC);

            // حذف الصورة القديمة بعد نجاح تحديث قاعدة البيانات
if ($new_picture_path !== null && !empty($old_picture)) {

    $uploads_directory = realpath(__DIR__ . "/uploads");

    $old_image_path = realpath(
        __DIR__ . "/" . ltrim($old_picture, "/\\")
    );

    // التأكد من أن الصورة القديمة موجودة داخل مجلد uploads
    if (
        $uploads_directory !== false &&
        $old_image_path !== false &&
        is_file($old_image_path) &&
        strpos(
            $old_image_path,
            $uploads_directory . DIRECTORY_SEPARATOR
        ) === 0
    ) {
        if (!unlink($old_image_path)) {
            error_log("تعذر حذف الصورة القديمة: " . $old_image_path);
        }
    }
}

            $message = "تم تعديل بيانات الطالب بنجاح.";
            $message_type = "success";

        } catch (RuntimeException $e) {

            // حذف الصورة الجديدة إذا رُفعت لكن لم تكتمل العملية
            if (
                $new_picture_path !== null &&
                isset($destination) &&
                is_file($destination)
            ) {
                unlink($destination);
            }

            $message = $e->getMessage();
            $message_type = "error";

        } catch (PDOException $e) {

            // حذف الصورة الجديدة إذا فشل تحديث قاعدة البيانات
            if (
                $new_picture_path !== null &&
                isset($destination) &&
                is_file($destination)
            ) {
                unlink($destination);
            }

            $message = "تعذّر تعديل بيانات الطالب، يرجى المحاولة مرة أخرى.";
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعديل بيانات الطالب</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">

   
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
                <li class="nav-item"><a class="nav-link text-" href="logout.php">تسجيل الخروج</a></li>
                <li class="nav-item">
                    <button  id="theme-toggle" class="nav-link text" role="button"></button>

                </li>
            </ul>
        </div>
    </div>
</nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">

                <header class="text-center mb-4">
                    <h1 class="h3 fw-bold page-title">تعديل بيانات الطالب</h1>
                </header>

              
                <?php if ($message !== ""): ?>
                    <div class="alert <?= $message_type === 'success' ? 'alert-success' : 'alert-danger' ?> text-center" role="alert">
                        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

              
                <main >
                    <form
                        method="POST"
                        action="update_student.php?id=<?= (int) $id ?>"
                        enctype="multipart/form-data"
                    >

                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label fw-semibold">اسم الطالب</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    value="<?= htmlspecialchars($student['name'], ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    value="<?= htmlspecialchars($student['email'], ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                >
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label fw-semibold">رقم الهاتف</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="phone"
                                    name="phone"
                                    value="<?= htmlspecialchars($student['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                >
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="profile_picture" class="form-label fw-semibold">تغيير الصورة الشخصية (اختياري)</label>
                                <input
                                    type="file"
                                    class="form-control"
                                    id="profile_picture"
                                    name="profile_picture"
                                    accept="image/jpeg,image/png,image/webp"
                                >
                                <div class="form-text">JPG أو PNG أو WebP، بحد أقصى 2 ميغابايت.</div>
                            </div>

                            <div class="col-12">
                                <label for="address" class="form-label fw-semibold">العنوان</label>
                                <textarea
                                    class="form-control"
                                    id="address"
                                    name="address"
                                    rows="3"
                                    required
                                ><?= htmlspecialchars($student['address'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                            </div>

                            <?php if (!empty($student["profile_picture"])): ?>
                                <div class="col-12 text-center">
                                    <p class="fw-semibold mb-2">الصورة الشخصية الحالية:</p>
                                    <img
                                        src="<?= htmlspecialchars($student["profile_picture"], ENT_QUOTES, 'UTF-8') ?>"
                                        alt="الصورة الشخصية للطالب"
                                        class="current-photo"
                                    >
                                </div>
                            <?php endif; ?>

                        </div>

                        <div class="mt-4">
                            <button type="submit" name="update" class="btn btn-primary btn-submit w-100">حفظ التعديلات</button>
                        </div>

                    </form>
                </main>

              
            </div>
        </div>
    </div>
 <script src="theme.js"></script>
</body>

</html>