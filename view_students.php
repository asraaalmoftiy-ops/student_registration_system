<?php

require_once "auth.php";
require_once "database.php";

$search = trim($_GET["search"] ?? "");


$students_per_page = 10;


$page = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);

if (!$page || $page < 1) {
    $page = 1;
}


$offset = ($page - 1) * $students_per_page;


$count_sql = "SELECT COUNT(*) FROM students
              WHERE name LIKE :search
              OR email LIKE :search
              OR phone LIKE :search
              OR address LIKE :search";

$count_stmt = $pdo->prepare($count_sql);

$count_stmt->execute([
    ":search" => "%" . $search . "%"
]);

$total_students = (int) $count_stmt->fetchColumn();


$total_pages = (int) ceil($total_students / $students_per_page);

$sql = "SELECT * FROM students
        WHERE name LIKE :search
        OR email LIKE :search
        OR phone LIKE :search
        OR address LIKE :search
        ORDER BY id ASC
        LIMIT :limit OFFSET :offset";

$stmt = $pdo->prepare($sql);

$stmt->bindValue(":search", "%" . $search . "%", PDO::PARAM_STR);
$stmt->bindValue(":limit", $students_per_page, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);

$stmt->execute();

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>قائمة الطلاب</title>

   
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="style.css">
    <style>
         .student-photo {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 0.5rem;
        }
    </style>
    
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
                 
                <li class="nav-item"><a class="nav-link text" href="logout.php">تسجيل الخروج</a></li>
                
            </li>
        <li class="nav-item">
<button  id="theme-toggle" class="nav-link text" role="button"></button>
        </li>
        </div>
    </div>
</nav>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-11">

                <header class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
                    <h1 class="h3 fw-bold page-title mb-0">قائمة الطلاب</h1>
                   
                </header>

                <main class="card list-card p-4">

                   
                    <form action="view_students.php" method="GET" class="row g-2 mb-3">

                        <div class="col-12 col-md-8">
                            <input
                                type="text"
                                class="form-control"
                                name="search"
                                placeholder="ابحث عن طالب..."
                                value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"
                            >
                        </div>

                        <div class="col-6 col-md-2">
                         <button type="submit" class="btn btn-danger w-100">بحث</button>
                        </div>

                        <div class="col-6 col-md-2">
                            <a href="view_students.php" class="btn btn-secondary w-100">إلغاء البحث</a>
                        </div>

                    </form>

                   
                    <?php if (isset($_GET["deleted"])): ?>

                        <?php if ($_GET["deleted"] === "1"): ?>

                            <div class="alert alert-success text-center" role="alert">
                                تم حذف الطالب بنجاح.
                            </div>

                        <?php else: ?>

                            <div class="alert alert-danger text-center" role="alert">
                                لم يتم العثور على الطالب أو تعذر حذفه.
                            </div>

                        <?php endif; ?>

                    <?php endif; ?>

                   
                    <?php if (count($students) > 0): ?>

                        <div class="table-responsive table-wrapper">

                            <table class="table table-hover table-bordered align-middle text-center mb-0">

                                <thead class="table-primary">
                                    <tr>
                                        <th>الرقم</th>
                                        <th>الصورة</th>
                                        <th>الاسم</th>
                                        <th>البريد الإلكتروني</th>
                                        <th>رقم الهاتف</th>
                                        <th>العنوان</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>

                                <tbody>

                                    <?php foreach ($students as $student): ?>

                                        <tr>

                                           
                                            <td>
                                                <?= htmlspecialchars((string) $student['id'], ENT_QUOTES, 'UTF-8') ?>
                                            </td>

                                            <td>
                                                <?php if (!empty($student['profile_picture'])): ?>

                                                    <img
                                                        src="<?= htmlspecialchars($student['profile_picture'], ENT_QUOTES, 'UTF-8') ?>"
                                                        alt="صورة الطالب"
                                                        class="student-photo"
                                                    >

                                                <?php else: ?>

                                                    <span class="text-muted small">لا توجد صورة</span>

                                                <?php endif; ?>
                                            </td>

                                           
                                            <td>
                                                <?= htmlspecialchars($student['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </td>

                                           
                                            <td>
                                                <?= htmlspecialchars($student['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </td>

                                           
                                            <td>
                                                <?= htmlspecialchars($student['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </td>

                                          
                                            <td>
                                                <?= htmlspecialchars($student['address'] ?? '', ENT_QUOTES, 'UTF-8') ?>
                                            </td>

                                           
                                            <td>
                                                <div class="d-flex flex-wrap justify-content-center gap-2">

                                                    <a href="update_student.php?id=<?= (int) $student['id'] ?>"
                                                       class="btn btn btn-secondary btn-sm">
                                                        تعديل
                                                    </a>

                                                    <form
                                                        action="delete_student.php"
                                                        method="POST"
                                                        class="d-inline"
                                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب؟');"
                                                    >

                                                        <input
                                                            type="hidden"
                                                            name="id"
                                                            value="<?= (int) $student['id'] ?>"
                                                        >

                                                        <button type="submit" class=" btn-danger btn-sm">حذف</button>

                                                    </form>

                                                </div>
                                            </td>

                                        </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                    <?php else: ?>

                       
                        <div class="alert alert-info text-center mb-0" role="alert">
                            لا يوجد طلاب لعرضهم.
                        </div>

                    <?php endif; ?>

                    
                    <?php if ($total_pages > 1): ?>

                        <nav class="mt-4" aria-label="تنقل بين الصفحات">
                            <ul class="pagination justify-content-center flex-wrap mb-0">

                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">السابق</a>
                                    </li>
                                <?php endif; ?>

                                <?php for ($i = 1; $i <= $total_pages; $i++): ?>

                                    <?php if ($i === $page): ?>
                                        <li class="page-item active" aria-current="page">
                                            <span class="page-link"><?= $i ?></span>
                                        </li>
                                    <?php else: ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                                        </li>
                                    <?php endif; ?>

                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">التالي</a>
                                    </li>
                                <?php endif; ?>

                            </ul>
                        </nav>

                    <?php endif; ?>

                </main>

                <div class="text-center mt-4">
                    <a href="add_student.php" class="btn btn-primary px-4">إضافة طالب جديد</a>
                </div>

            </div>
        </div>
    </div>
 <script src="theme.js"></script>
</body>

</html>