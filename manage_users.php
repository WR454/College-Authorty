<?php
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: index.php");
    exit();
}

include('db_config.php');

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$message = '';
$messageType = 'success';
$search = trim($_GET['search'] ?? '');
$editUserId = (int) ($_GET['edit'] ?? 0);
$editStudent = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $submittedToken = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $submittedToken)) {
        $message = 'فشل التحقق من الطلب. أعد المحاولة.';
        $messageType = 'error';
    } else {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($username === '' || $fullName === '' || $password === '') {
                $message = 'جميع حقول إضافة الطالب مطلوبة.';
                $messageType = 'error';
            } else {
                $checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? LIMIT 1");

                if ($checkStmt) {
                    mysqli_stmt_bind_param($checkStmt, 's', $username);
                    mysqli_stmt_execute($checkStmt);
                    $checkResult = mysqli_stmt_get_result($checkStmt);
                    $existingUser = mysqli_fetch_assoc($checkResult);
                    mysqli_stmt_close($checkStmt);
                } else {
                    $existingUser = ['id' => 0];
                }

                if (!empty($existingUser)) {
                    $message = 'اسم المستخدم مستخدم مسبقاً.';
                    $messageType = 'error';
                } else {
                    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                    $role = 10;
                    $insertStmt = mysqli_prepare($conn, "INSERT INTO users (username, full_name, password, role) VALUES (?, ?, ?, ?)");

                    if ($insertStmt) {
                        mysqli_stmt_bind_param($insertStmt, 'sssi', $username, $fullName, $hashedPassword, $role);

                        if (mysqli_stmt_execute($insertStmt)) {
                            $message = 'تمت إضافة الطالب بنجاح.';
                        } else {
                            $message = 'تعذر إضافة الطالب. تحقق من بنية جدول users.';
                            $messageType = 'error';
                        }

                        mysqli_stmt_close($insertStmt);
                    } else {
                        $message = 'تعذر تجهيز عملية إضافة الطالب.';
                        $messageType = 'error';
                    }
                }
            }
        }

        if ($action === 'delete') {
            $userId = (int) ($_POST['user_id'] ?? 0);

            if ($userId <= 0) {
                $message = 'معرّف المستخدم غير صالح.';
                $messageType = 'error';
            } else {
                $deleteStmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ? AND role = 10 LIMIT 1");

                if ($deleteStmt) {
                    mysqli_stmt_bind_param($deleteStmt, 'i', $userId);
                    mysqli_stmt_execute($deleteStmt);
                    $deletedRows = mysqli_stmt_affected_rows($deleteStmt);
                    mysqli_stmt_close($deleteStmt);

                    if ($deletedRows > 0) {
                        $message = 'تم حذف الطالب بنجاح.';
                    } else {
                        $message = 'لم يتم العثور على الطالب المطلوب أو لا يمكن حذف هذا الحساب.';
                        $messageType = 'error';
                    }
                } else {
                    $message = 'تعذر تجهيز عملية حذف الطالب.';
                    $messageType = 'error';
                }
            }
        }

        if ($action === 'update') {
            $userId = (int) ($_POST['user_id'] ?? 0);
            $username = trim($_POST['username'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $password = $_POST['password'] ?? '';

            if ($userId <= 0 || $username === '' || $fullName === '') {
                $message = 'بيانات التحديث غير مكتملة.';
                $messageType = 'error';
            } else {
                $checkStmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ? AND id <> ? LIMIT 1");

                if ($checkStmt) {
                    mysqli_stmt_bind_param($checkStmt, 'si', $username, $userId);
                    mysqli_stmt_execute($checkStmt);
                    $checkResult = mysqli_stmt_get_result($checkStmt);
                    $existingUser = mysqli_fetch_assoc($checkResult);
                    mysqli_stmt_close($checkStmt);
                } else {
                    $existingUser = ['id' => 0];
                }

                if (!empty($existingUser)) {
                    $message = 'اسم المستخدم مستخدم من حساب آخر.';
                    $messageType = 'error';
                    $editUserId = $userId;
                } else {
                    if ($password !== '') {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $updateStmt = mysqli_prepare($conn, "UPDATE users SET username = ?, full_name = ?, password = ? WHERE id = ? AND role = 10 LIMIT 1");

                        if ($updateStmt) {
                            mysqli_stmt_bind_param($updateStmt, 'sssi', $username, $fullName, $hashedPassword, $userId);
                        }
                    } else {
                        $updateStmt = mysqli_prepare($conn, "UPDATE users SET username = ?, full_name = ? WHERE id = ? AND role = 10 LIMIT 1");

                        if ($updateStmt) {
                            mysqli_stmt_bind_param($updateStmt, 'ssi', $username, $fullName, $userId);
                        }
                    }

                    if (!empty($updateStmt)) {
                        if (mysqli_stmt_execute($updateStmt)) {
                            if (mysqli_stmt_affected_rows($updateStmt) >= 0) {
                                $message = 'تم تحديث بيانات الطالب بنجاح.';
                                $editUserId = 0;
                            }
                        } else {
                            $message = 'تعذر تحديث بيانات الطالب.';
                            $messageType = 'error';
                            $editUserId = $userId;
                        }

                        mysqli_stmt_close($updateStmt);
                    } else {
                        $message = 'تعذر تجهيز عملية تحديث الطالب.';
                        $messageType = 'error';
                        $editUserId = $userId;
                    }
                }
            }
        }
    }
}

if ($editUserId > 0) {
    $editStmt = mysqli_prepare($conn, "SELECT id, username, full_name FROM users WHERE id = ? AND role = 10 LIMIT 1");

    if ($editStmt) {
        mysqli_stmt_bind_param($editStmt, 'i', $editUserId);
        mysqli_stmt_execute($editStmt);
        $editResult = mysqli_stmt_get_result($editStmt);
        $editStudent = mysqli_fetch_assoc($editResult);
        mysqli_stmt_close($editStmt);

        if (!$editStudent) {
            $editUserId = 0;
        }
    }
}

if ($search !== '') {
    $query = '%' . $search . '%';
    $listStmt = mysqli_prepare($conn, "SELECT id, username, full_name FROM users WHERE role = 10 AND (username LIKE ? OR full_name LIKE ?) ORDER BY id DESC");
    mysqli_stmt_bind_param($listStmt, 'ss', $query, $query);
} else {
    $listStmt = mysqli_prepare($conn, "SELECT id, username, full_name FROM users WHERE role = 10 ORDER BY id DESC");
}

$students = [];

if ($listStmt) {
    mysqli_stmt_execute($listStmt);
    $result = mysqli_stmt_get_result($listStmt);

    while ($row = mysqli_fetch_assoc($result)) {
        $students[] = $row;
    }

    mysqli_stmt_close($listStmt);
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة الطلاب</title>
    <style>
        body {
            margin: 0;
            padding: 30px 15px;
            background: #f3f6f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #1f2d3d;
        }
        .container {
            width: min(1100px, 100%);
            margin: 0 auto;
        }
        .panel {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            padding: 24px;
            margin-bottom: 24px;
        }
        .toolbar {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: center;
            flex-wrap: wrap;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        input {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d7dde3;
            border-radius: 10px;
            box-sizing: border-box;
        }
        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            overflow: hidden;
        }
        th, td {
            border-bottom: 1px solid #e8edf2;
            padding: 14px 12px;
            text-align: center;
        }
        th {
            background-color: #198754;
            color: white;
        }
        .btn {
            display: inline-block;
            border: none;
            border-radius: 10px;
            padding: 11px 18px;
            cursor: pointer;
            text-decoration: none;
            color: white;
            font-size: 14px;
        }
        .btn-primary { background-color: #198754; }
        .btn-secondary { background-color: #6c757d; }
        .btn-danger { background-color: #dc3545; }
        .message {
            padding: 12px 14px;
            border-radius: 10px;
            margin-bottom: 16px;
        }
        .message.success {
            background: #d1e7dd;
            color: #0f5132;
        }
        .message.error {
            background: #f8d7da;
            color: #842029;
        }
        .actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .search-form {
            display: flex;
            gap: 10px;
            align-items: end;
            flex-wrap: wrap;
        }
        .search-form .field {
            min-width: 260px;
            flex: 1;
        }
        .empty-state {
            text-align: center;
            color: #6c757d;
            padding: 24px 0 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="panel">
            <div class="toolbar">
                <div>
                    <h2 style="margin: 0;">إدارة الطلاب</h2>
                    <p style="margin: 8px 0 0; color: #6c757d;">إضافة حسابات طلاب، البحث عنها، وحذفها عند الحاجة.</p>
                </div>
                <div class="actions">
                    <a href="admin_dashboard.php" class="btn btn-secondary">العودة للوحة التحكم</a>
                    <a href="schedule.php" class="btn btn-secondary">معاينة الجدول</a>
                </div>
            </div>
        </div>

        <div class="panel">
            <h3 style="margin-top: 0;">إضافة طالب جديد</h3>
            <?php if ($message !== '') { ?>
            <div class="message <?php echo htmlspecialchars($messageType, ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php } ?>
            <form method="POST" action="manage_users.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action" value="add">
                <div class="grid">
                    <div>
                        <label for="username">اسم المستخدم</label>
                        <input id="username" type="text" name="username" required>
                    </div>
                    <div>
                        <label for="full_name">الاسم الكامل</label>
                        <input id="full_name" type="text" name="full_name" required>
                    </div>
                    <div>
                        <label for="password">كلمة المرور</label>
                        <input id="password" type="password" name="password" required>
                    </div>
                </div>
                <div class="actions" style="margin-top: 16px; justify-content: flex-start;">
                    <button type="submit" class="btn btn-primary">إضافة الطالب</button>
                </div>
            </form>
        </div>

        <?php if ($editStudent) { ?>
        <div class="panel">
            <h3 style="margin-top: 0;">تعديل بيانات الطالب</h3>
            <p style="margin-top: 0; color: #6c757d;">يمكنك تحديث الاسم واسم المستخدم، وترك كلمة المرور فارغة إذا لم ترغب بتغييرها.</p>
            <form method="POST" action="manage_users.php<?php echo $search !== '' ? '?search=' . urlencode($search) . '&edit=' . (int) $editStudent['id'] : '?edit=' . (int) $editStudent['id']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="user_id" value="<?php echo (int) $editStudent['id']; ?>">
                <div class="grid">
                    <div>
                        <label for="edit_username">اسم المستخدم</label>
                        <input id="edit_username" type="text" name="username" value="<?php echo htmlspecialchars($editStudent['username'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div>
                        <label for="edit_full_name">الاسم الكامل</label>
                        <input id="edit_full_name" type="text" name="full_name" value="<?php echo htmlspecialchars($editStudent['full_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div>
                        <label for="edit_password">كلمة المرور الجديدة</label>
                        <input id="edit_password" type="password" name="password" placeholder="اتركها فارغة بدون تغيير">
                    </div>
                </div>
                <div class="actions" style="margin-top: 16px; justify-content: flex-start;">
                    <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                    <a href="manage_users.php<?php echo $search !== '' ? '?search=' . urlencode($search) : ''; ?>" class="btn btn-secondary">إلغاء</a>
                </div>
            </form>
        </div>
        <?php } ?>

        <div class="panel">
            <div class="toolbar">
                <h3 style="margin: 0;">قائمة الطلاب المسجلين</h3>
                <form method="GET" action="manage_users.php" class="search-form">
                    <div class="field">
                        <label for="search">بحث بالاسم أو اسم المستخدم</label>
                        <input id="search" type="text" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">بحث</button>
                    <a href="manage_users.php" class="btn btn-secondary">إعادة تعيين</a>
                </form>
            </div>

            <?php if (count($students) === 0) { ?>
            <div class="empty-state">لا توجد نتائج مطابقة حالياً.</div>
            <?php } else { ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>اسم المستخدم</th>
                    <th>الاسم الكامل</th>
                    <th>التعديل</th>
                    <th>الإجراء</th>
                </tr>
                <?php foreach ($students as $row) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td><?php echo htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                    <td>
                        <a href="manage_users.php?edit=<?php echo (int) $row['id']; ?><?php echo $search !== '' ? '&search=' . urlencode($search) : ''; ?>" class="btn btn-secondary">تعديل</a>
                    </td>
                    <td>
                        <form method="POST" action="manage_users.php<?php echo $search !== '' ? '?search=' . urlencode($search) : ''; ?>" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطالب؟');" style="margin: 0;">
                            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8'); ?>">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="user_id" value="<?php echo (int) $row['id']; ?>">
                            <button type="submit" class="btn btn-danger">حذف</button>
                        </form>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <?php } ?>
        </div>
    </div>
</body>
</html>