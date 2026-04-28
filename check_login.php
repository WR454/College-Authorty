<?php
session_start();
include('db_config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user === '' || $pass === '') {
        echo "<script>alert('يرجى إدخال اسم المستخدم وكلمة المرور'); window.location='index.php';</script>";
        exit();
    }

    $stmt = mysqli_prepare($conn, "SELECT id, username, password, role FROM users WHERE username = ? LIMIT 1");

    if (!$stmt) {
        http_response_code(500);
        exit('فشل تجهيز استعلام تسجيل الدخول.');
    }

    mysqli_stmt_bind_param($stmt, 's', $user);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    $isValidPassword = false;

    if ($row) {
        $storedPassword = $row['password'];
        $isValidPassword = password_verify($pass, $storedPassword) || hash_equals((string) $storedPassword, $pass);
    }

    if ($row && $isValidPassword) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role'] = $row['role'];

        if ($_SESSION['role'] == 1) {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: schedule.php");
        }
        exit();
    }

    echo "<script>alert('خطأ في اسم المستخدم أو كلمة المرور'); window.location='index.php';</script>";
}
?>