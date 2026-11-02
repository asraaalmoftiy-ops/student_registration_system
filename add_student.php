<?php

require_once "auth.php";
require_once "database.php";

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $address = trim($_POST["address"] ?? "");

   
    $profile_picture = null;

   
    if (
        $name === "" ||
        $email === "" ||
        $phone === "" ||
        $address === ""
    ) {
        $message = "يرجى تعبئة جميع الحقول.";
        $message_type = "error";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        
        $message = "يرجى إدخال بريد إلكتروني صحيح.";
        $message_type = "error";

    } else {

        
        if (
            isset($_FILES["profile_picture"]) &&
            $_FILES["profile_picture"]["error"] !== UPLOAD_ERR_NO_FILE
        ) {

            $image = $_FILES["profile_picture"];

            if ($image["error"] !== UPLOAD_ERR_OK) {

                $message = "حدث خطأ أثناء رفع الصورة.";
                $message_type = "error";

            } elseif ($image["size"] > 2 * 1024 * 1024) {

                $message = "يجب ألا يتجاوز حجم الصورة 2 ميغابايت.";
                $message_type = "error";

            } else {

              
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime_type = $finfo->file($image["tmp_name"]);

                $allowed_types = [
                    "image/jpeg" => "jpg",
                    "image/png"  => "png",
                    "image/webp" => "webp"
                ];

                if (!isset($allowed_types[$mime_type])) {

                    $message = "يُسمح فقط بالصور من نوع JPG وPNG وWebP.";
                    $message_type = "error";

                } else {

             
                    $upload_directory = __DIR__ . "/uploads/";

                    if (!is_dir($upload_directory)) {
                        mkdir($upload_directory, 0755, true);
                    }

                   
                    $image_name = bin2hex(random_bytes(16))
                                . "."
                                . $allowed_types[$mime_type];

                    $destination = $upload_directory . $image_name;

                    if (
                        move_uploaded_file(
                            $image["tmp_name"],
                            $destination
                        )
                    ) {

                        $profile_picture = "uploads/" . $image_name;

                    } else {

                        $message = "تعذّر حفظ الصورة.";
                        $message_type = "error";
                    }
                }
            }
        }


        if ($message === "") {

            try {

                $sql = "INSERT INTO students
                        (name, email, phone, address, profile_picture)
                        VALUES
                        (:name, :email, :phone, :address, :profile_picture)";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":name" => $name,
                    ":email" => $email,
                    ":phone" => $phone,
                    ":address" => $address,
                    ":profile_picture" => $profile_picture
                ]);

                $message = "تمت إضافة الطالب بنجاح.";
                $message_type = "success";

            } catch (PDOException $e) {

                $message = "تعذّرت إضافة الطالب، يرجى المحاولة مرة أخرى.";
                $message_type = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إضافة طالب</title>

    
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
                <li class="nav-item"><a class="nav-link" href="logout.php">تسجيل الخروج</a></li>
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
                    <h1 class="h3 fw-bold page-title">إضافة طالب جديد</h1>
                </header>

                
                <?php if ($message !== ""): ?>
                    <div class="alert <?= $message_type === 'success' ? 'alert-success' : 'alert-danger' ?> text-center" role="alert">
                        <?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

             
                <div id="validation-message" class="alert alert-danger text-center" role="alert"></div>

             
                <main >
                    <form
                        method="POST"
                        action="add_student.php"
                        enctype="multipart/form-data"
                        novalidate
                    >

                        <div class="row g-3">

                            <div class="col-12 col-md-6">
                                <label for="name" class="form-label fw-semibold" >اسم الطالب</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    value="<?= htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                    placeholder="إسراء"
                                >
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="email" class="form-label fw-semibold">البريد الإلكتروني</label>
                                <input
                                    type="email"
                                    class="form-control"
                                    id="email"
                                    name="email"
                                    value="<?= htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                    placeholder="esraa@gmail.com"

                                >
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="phone" class="form-label fw-semibold">رقم الهاتف</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="phone"
                                    name="phone"
                                    value="<?= htmlspecialchars($phone ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    required
                                    placeholder="09xx xxx xxx"

                                >
                            </div>

                            <div class="col-12 col-md-6">
                                <label for="profile_picture" class="form-label fw-semibold">صورة الطالب (اختيارية)</label>
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
                                    rows="1"
                                    required
                                    placeholder="حمص الرستن"

                                ><?= htmlspecialchars($address ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                            </div>

                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-submit w-100">إضافة الطالب</button>
                        </div>

                    </form>
                </main>

               

            </div>
        </div>
    </div>

    <script src="theme.js"></script>
</body>

</html>