<?php
session_start();
include('schedule_data.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$schedule = load_schedule();
$timeSlots = get_schedule_time_slots();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>جدول المحاضرات</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .logout-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #dc3545;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>جدول المحاضرات الأسبوعي</h2>
    <p>مرحباً، <?php echo htmlspecialchars($_SESSION['username'] ?? '', ENT_QUOTES, 'UTF-8'); ?></p>
    <table>
        <thead>
            <tr>
                <th>اليوم</th>
                <?php foreach ($timeSlots as $timeSlot) { ?>
                <th><?php echo htmlspecialchars($timeSlot, ENT_QUOTES, 'UTF-8'); ?></th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($schedule as $day => $subjects) { ?>
            <tr>
                <td><?php echo htmlspecialchars($day, ENT_QUOTES, 'UTF-8'); ?></td>
                <?php foreach ($subjects as $subject) { ?>
                <td><?php echo htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'); ?></td>
                <?php } ?>
            </tr>
            <?php } ?>
        </tbody>
    </table>
    <a href="logout.php" class="logout-btn">تسجيل الخروج</a>
</div>

</body>
</html>