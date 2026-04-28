<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "nabdh_db";

// إنشاء الاتصال
$conn = mysqli_connect($host, $username, $password, $dbname);

// التأكد من نجاح الاتصال
if (!$conn) {
    die("فشل الاتصال بقاعدة البيانات: " . mysqli_connect_error());
}

// ضبط الترميز للغة العربية
mysqli_set_charset($conn, "utf8mb4");
?>