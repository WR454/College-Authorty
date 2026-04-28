<?php
session_start();
include('schedule_data.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

$schedule = load_schedule();
$timeSlots = get_schedule_time_slots();
$message = '';
$messageType = 'success';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        $message = 'تعذر حفظ التعديلات بسبب فشل التحقق من الطلب.';
        $messageType = 'error';
    } else {
        $submittedSchedule = $_POST['schedule'] ?? [];

        if (save_schedule($submittedSchedule)) {
            $schedule = load_schedule();
            $message = 'تم حفظ الجدول بنجاح.';
        } else {
            $message = 'تعذر حفظ الجدول. تأكد من صلاحيات الكتابة على مجلد المشروع.';
            $messageType = 'error';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الجدول</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
            min-height: 100vh;
            margin: 0;
            padding: 30px 15px;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: min(95%, 900px);
            margin: 0 auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: center;
        }
        th {
            background-color: #0d6efd;
            color: white;
        }
        input[type="text"] {
            width: 100%;
            box-sizing: border-box;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
            text-align: center;
        }
        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 24px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }
        .btn-primary {
            background-color: #0d6efd;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .message {
            margin-top: 15px;
            padding: 12px;
            border-radius: 6px;
        }
        .message.success {
            background-color: #d1e7dd;
            color: #0f5132;
        }
        .message.error {
            background-color: #f8d7da;
            color: #842029;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>إدارة الجدول</h1>
    <p>عدّل المواد مباشرة ثم احفظ التغييرات ليتم تحديث صفحة الجدول للطلاب.</p>

    <?php if ($message !== '') { ?>
    <div class="message <?php echo htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8'); ?>">
        <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
    </div>
    <?php } ?>

    <form method="POST" action="manage_schedule.php">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
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
                    <?php foreach ($subjects as $index => $subject) { ?>
                    <td>
                        <input type="text" name="schedule[<?php echo htmlspecialchars($day, ENT_QUOTES, 'UTF-8'); ?>][<?php echo (int) $index; ?>]" value="<?php echo htmlspecialchars($subject, ENT_QUOTES, 'UTF-8'); ?>">
                    </td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <div class="actions">
            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
            <a href="schedule.php" class="btn btn-secondary">معاينة الجدول</a>
            <a href="admin_dashboard.php" class="btn btn-secondary">العودة للوحة التحكم</a>
        </div>
    </form>
</div>
</body>
</html>