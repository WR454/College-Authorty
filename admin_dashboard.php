<?php
session_start();

// التحقق من الصلاحيات
if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم | نبضة الويب</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .admin-card { transition: 0.3s; border: none; border-radius: 15px; }
        .admin-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .icon-box { font-size: 3rem; color: #007bff; }
    </style>
</head>
<body>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="fw-bold">لوحة تحكم المسؤول 🛠️</h1>
        <p class="text-muted">مرحباً بك يا <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>، يمكنك إدارة النظام من هنا.</p>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-4">
            <div class="card admin-card h-100 text-center p-4">
                <div class="icon-box mb-3">📅</div>
                <h4>إدارة الجدول</h4>
                <p class="text-muted">تحديث محاضرات الطلاب وأوقاتها.</p>
                <a href="manage_schedule.php" class="btn btn-outline-primary mt-auto">دخول الإدارة</a>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card admin-card h-100 text-center p-4">
                <div class="icon-box mb-3">👥</div>
                <h4>إدارة الطلاب</h4>
                <p class="text-muted">عرض قائمة الطلاب أو حذف حسابات.</p>
                <a href="manage_users.php" class="btn btn-outline-primary mt-auto">عرض الطلاب</a>
            </div>
        </div>

        <div class="col-md-6 col-lg-4">
            <div class="card admin-card h-100 text-center p-4">
                <div class="icon-box mb-3">⚙️</div>
                <h4>إعدادات الموقع</h4>
                <p class="text-muted">تعديل بيانات الموقع الأساسية.</p>
                <a href="#" class="btn btn-outline-secondary mt-auto">قريباً..</a>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <a href="schedule.php" class="btn btn-dark px-5">العودة لجدولي الشخصي</a>
        <a href="logout.php" class="btn btn-outline-danger px-5">تسجيل الخروج</a>
    </div>
</div>

</body>
</html>