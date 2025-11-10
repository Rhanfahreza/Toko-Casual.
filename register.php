<?php
// FILE: register.php (VERSI TEMA BARU)

session_start();
$error_message = '';

if (isset($_SESSION['user_nama'])) {
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $file_path = 'users.json';

    $users = [];
    if (file_exists($file_path)) {
        $json_data = file_get_contents($file_path);
        $users = json_decode($json_data, true);
    }

    if (isset($users[$email])) {
        $error_message = 'Email ini sudah terdaftar!';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $users[$email] = [
            'nama' => $nama,
            'password' => $hashed_password
        ];
        file_put_contents($file_path, json_encode($users, JSON_PRETTY_PRINT));
        
        $_SESSION['register_success'] = 'Akun berhasil dibuat! Silakan login.';
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Toko Casuals</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Nunito Sans', sans-serif; background-color: #FAF8F5; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; transition: background-color 0.3s ease, color 0.3s ease; }
        .register-box { background: #fff; padding: 40px; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border: 1px solid #F0EAE4; width: 350px; text-align: center; transition: background-color 0.3s ease, border-color 0.3s ease; }
        .logo { font-size: 1.8rem; font-weight: 700; color: #5D4037; text-decoration: none; margin-bottom: 20px; display: block; }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; color: #4B4B4B; }
        .form-group input { 
            width: 100%; padding: 12px; border: 1px solid #DCD3CB; border-radius: 8px; 
            box-sizing: border-box; font-family: 'Nunito Sans', sans-serif;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: #A98A74;
            box-shadow: 0 0 0 3px rgba(169, 138, 116, 0.2);
        }
        .btn-register { 
            display: block; width: 100%; padding: 12px; background-color: #A98A74; 
            color: #ffffff; border: none; border-radius: 8px; font-weight: 700; 
            text-transform: uppercase; cursor: pointer; transition: background-color 0.3s ease; 
        }
        .btn-register:hover { background-color: #937460; }
        .error { color: #e74c3c; font-weight: 600; margin-top: 15px; }
        .login-link { margin-top: 20px; font-size: 0.9rem; }
        .login-link a { color: #A98A74; font-weight: 600; }

        /* CSS DARK MODE */
        body.dark-mode { background-color: #22272E; }
        body.dark-mode .register-box { background-color: #2D333B; border-color: #444C56; }
        body.dark-mode .logo { color: #E6EDF3; }
        body.dark-mode .form-group label { color: #ADBAC7; }
        body.dark-mode .form-group input { 
            background-color: #22272E; 
            border-color: #444C56; 
            color: #E6EDF3;
        }
        body.dark-mode .login-link { color: #ADBAC7; }
    </style>
</head>
<body>
    <div class="register-box">
        <a href="#" class="logo">BUAT AKUN</a>
        <form method="POST" action="register.php">
            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" id="nama" name="nama" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn-register">Daftar</button>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>
        <p class="login-link">Sudah punya akun? <a href="login.php">Login di sini</a></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script>
        $(document).ready(function () {
            if (localStorage.getItem('theme') === 'dark') {
                $('body').addClass('dark-mode');
            }
        });
    </script>
</body>
</html>