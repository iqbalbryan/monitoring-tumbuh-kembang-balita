<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@0,500;0,600;1,400&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #d9d9d9;
            padding: 24px;
        }

        .card {
            background: #fff;
            border-radius: 16px;
            display: flex;
            flex-direction: row;
            width: 100%;
            max-width: 900px;
            min-height: 520px;
            overflow: hidden;
        }

        .form-side {
            flex: 1;
            padding: 60px 52px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            font-family: 'Fraunces', serif;
            font-size: 32px;
            font-weight: 600;
            color: #1a1a1a;
            letter-spacing: -0.5px;
            margin-bottom: 36px;
            text-align: center;
        }

        .alert {
            border-radius: 8px;
            padding: 11px 14px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        .alert-danger {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #bbf7d0;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-size: 15px;
            font-weight: 400;
            color: #1a1a1a;
            margin-bottom: 8px;
        }

        .form-group input {
            width: 100%;
            height: 50px;
            background: #f0f0f0;
            border: none;
            border-radius: 8px;
            padding: 0 16px;
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            color: #1a1a1a;
            outline: none;
            transition: box-shadow 0.2s;
        }

        .form-group input:focus {
            box-shadow: 0 0 0 3px rgba(79, 108, 151, 0.25);
        }

        .btn-submit {
            display: block;
            width: 220px;
            height: 52px;
            background: #4f6c97;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-family: 'DM Sans', sans-serif;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            margin: 32px auto 0;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-submit:hover { background: #3d5578; }
        .btn-submit:active { transform: scale(0.98); }

        .footer-text {
            margin-top: 24px;
            text-align: center;
            font-size: 13px;
            color: #999;
        }

        .footer-text a {
            color: #4f6c97;
            text-decoration: none;
            font-weight: 500;
        }

        .footer-text a:hover { text-decoration: underline; }

        .divider {
            width: 1px;
            background: #e8e8e8;
            margin: 48px 0;
        }

        .logo-side {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px;
        }

        .logo-box {
            width: 100%;
            max-width: 360px;
            aspect-ratio: 4/3;
            background: #ffffff;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .logo-box img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .logo-placeholder {
            font-family: 'Fraunces', serif;
            font-size: 26px;
            color: #888;
        }

        @media (max-width: 640px) {
            .divider { display: none; }
            .card { flex-direction: column; }
            .logo-side { padding: 32px 40px 48px; }
            .logo-box { max-width: 100%; aspect-ratio: 16/7; }
        }
    </style>
</head>
<body>

<div class="card">

    <div class="form-side">
        <h1 class="form-title">Login</h1>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <form action="/login" method="POST">
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="identitas">Email atau NIK</label>
                <input type="text" id="identitas" name="identitas" autocomplete="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit" class="btn-submit">Masuk</button>
        </form>
        <p class="footer-text" style="margin-top: 12px;">
            <a href="/" style="color: #999; font-size: 12px;">← Kembali ke Beranda</a>
        </p>
    </div>

    <div class="divider"></div>

    <div class="logo-side">
        <div class="logo-box">
            <img src="<?= base_url('assets/img/logo-posyandu.png') ?>" alt="Logo Posyandu">
        </div>
    </div>

</div>

</body>
</html>