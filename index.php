<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل الدخول - TVTC</title>
    <style>
        :root {
            --primary: #0f5c4d;
            --primary-dark: #0a3f35;
            --accent: #d4a017;
            --surface: rgba(255, 255, 255, 0.96);
            --text: #16302b;
            --muted: #5e6e68;
            --border: rgba(15, 92, 77, 0.15);
            --shadow: 0 25px 60px rgba(10, 63, 53, 0.22);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 24px;
            background:
                radial-gradient(circle at top right, rgba(212, 160, 23, 0.25), transparent 28%),
                radial-gradient(circle at bottom left, rgba(15, 92, 77, 0.22), transparent 32%),
                linear-gradient(135deg, #eef4ef 0%, #dfece5 100%);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text);
        }

        .login-shell {
            width: min(100%, 980px);
            display: grid;
            grid-template-columns: 1.1fr 0.9fr;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 28px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }

        .brand-panel {
            padding: 48px;
            background: linear-gradient(160deg, var(--primary-dark) 0%, var(--primary) 100%);
            color: #f8fbfa;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 24px;
        }

        .brand-panel h1 {
            margin: 0;
            font-size: clamp(2rem, 4vw, 3.1rem);
            line-height: 1.2;
        }

        .brand-panel p {
            margin: 0;
            line-height: 1.8;
            color: rgba(248, 251, 250, 0.85);
        }

        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            width: fit-content;
            padding: 10px 16px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.18);
            font-size: 14px;
        }

        .brand-panel img {
            width: min(100%, 240px);
            height: auto;
            filter: drop-shadow(0 10px 18px rgba(0, 0, 0, 0.2));
        }

        .form-panel {
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-panel h2 {
            margin: 0 0 10px;
            font-size: 1.9rem;
        }

        .form-panel p {
            margin: 0 0 28px;
            color: var(--muted);
        }

        .input-group {
            margin-bottom: 18px;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .input-group input {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--border);
            border-radius: 14px;
            background: #fbfdfc;
            color: var(--text);
            font-size: 15px;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(15, 92, 77, 0.12);
        }

        .submit-btn {
            width: 100%;
            border: none;
            border-radius: 14px;
            padding: 15px 18px;
            background: linear-gradient(135deg, var(--primary) 0%, #177663 100%);
            color: #fff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .submit-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 14px 30px rgba(15, 92, 77, 0.25);
        }

        .helper-text {
            margin-top: 18px;
            font-size: 13px;
            color: var(--muted);
            text-align: center;
        }

        .highlight {
            color: var(--accent);
            font-weight: 700;
        }

        @media (max-width: 820px) {
            .login-shell {
                grid-template-columns: 1fr;
            }

            .brand-panel,
            .form-panel {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>
    <main class="login-shell">
        <section class="brand-panel">
            <div>
                <span class="brand-badge">بوابة المؤسسة التدريبية</span>
                <h1>منصة TVTC لإدارة الجداول وحسابات الطلاب</h1>
                <p>وصول سريع وآمن إلى لوحة المسؤول وجدول الطالب من خلال واجهة دخول موحدة وواضحة.</p>
            </div>
            <div>
                <img src="logo.png" alt="شعار المؤسسة">
                <p>نقطة الدخول الرسمية إلى <span class="highlight">نظام نبضة الويب</span>.</p>
            </div>
        </section>

        <section class="form-panel">
            <h2>تسجيل الدخول</h2>
            <p>أدخل بيانات الحساب للانتقال إلى الجدول أو لوحة الإدارة بحسب الصلاحية.</p>
            <form action="check_login.php" method="POST">
                <div class="input-group">
                    <label for="username">اسم المستخدم</label>
                    <input id="username" type="text" name="username" placeholder="اكتب اسم المستخدم" required>
                </div>
                <div class="input-group">
                    <label for="password">كلمة المرور</label>
                    <input id="password" type="password" name="password" placeholder="اكتب كلمة المرور" required>
                </div>
                <button type="submit" class="submit-btn">دخول</button>
            </form>
            <div class="helper-text">يتم توجيهك تلقائياً إلى الصفحة المناسبة بعد التحقق من الصلاحية.</div>
        </section>
    </main>
</body>
</html>