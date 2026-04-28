<?php
$conn = mysqli_connect("localhost", "root", "", "nabdh_db");

if ($conn) {
    echo "تم الاتصال بنجاح!";
} else {
    echo "فشل الاتصال: " . mysqli_connect_error();
}
?>