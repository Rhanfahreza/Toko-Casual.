<?php
// FILE: login.php (VERSI TEMA BARU)

session_start(); 
$error_message = '';
$success_message = ''; 

// Cek pesan sukses dari registrasi
if (isset($_SESSION['register_success'])) {
    $success_message = $_SESSION['register_success'];
    unset($_SESSION['register_success']); 
}

// Cek jika sudah login
if (isset($_SESSION['user_nama'])) {
    header('Location: index.php'); 
    exit;
}

// Cek jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $users = [];
    $file_path = 'users.json';
    if (file_exists($file_path)) {
        $json_data = file_get_contents($file_path);
        $users = json_decode($json_data, true);
    }

    // Verifikasi pengguna
    if (isset($users[$email]) && password_verify($password, $users[$email]['password'])) {
        $_SESSION['user_nama'] = $users[$email]['nama'];
        $_SESSION['user_email'] = $email;
        header('Location: index.php'); 
        exit;
    } else {
        $error_message = 'Email atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toko Casuals</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body { 
            font-family: 'Nunito Sans', sans-serif; 
            background-color: #FAF8F5; /* Krem */
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            margin: 0;
            padding: 15px;
            box-sizing: border-box;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        .login-box { 
            background: #fff; 
            padding: 40px 30px; 
            border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            border: 1px solid #F0EAE4; 
            width: 100%;
            max-width: 380px; 
            text-align: center; 
            transition: background-color 0.3s ease, border-color 0.3s ease;
            box-sizing: border-box;
        }
        .logo { 
            font-size: 1.8rem; 
            font-weight: 700; 
            color: #5D4037; /* Coklat tua */
            text-decoration: none; 
            margin-bottom: 20px; 
            display: block; 
        }
        .form-group { margin-bottom: 15px; text-align: left; }
        .form-group label { 
            display: block; 
            margin-bottom: 5px; 
            font-weight: 600; 
            color: #4B4B4B; 
        }
        .form-group input { 
            width: 100%; 
            padding: 12px; 
            border: 1px solid #DCD3CB; /* Border coklat muda */
            border-radius: 8px; 
            box-sizing: border-box; 
            font-family: 'Nunito Sans', sans-serif;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-group input:focus {
            outline: none;
            border-color: #A98A74;
            box-shadow: 0 0 0 3px rgba(169, 138, 116, 0.2);
        }
        .btn-login { 
            display: block; 
            width: 100%; 
            padding: 12px; 
            background-color: #A98A74; /* Coklat tan */
            color: #ffffff; 
            border: none; 
            border-radius: 8px; 
            font-weight: 700; 
            text-transform: uppercase; 
            cursor: pointer; 
            transition: background-color 0.3s ease; 
        }
        .btn-login:hover { background-color: #937460; } /* Coklat lebih tua */
        .error { color: #e74c3c; font-weight: 600; margin-top: 15px; }
        .success { color: #27ae60; font-weight: 600; margin-top: 15px; }
        .register-link { margin-top: 20px; font-size: 0.9rem; }
        .register-link a { color: #A98A74; font-weight: 600; }

        /* CSS DARK MODE */
        body.dark-mode { background-color: #22272E; }
        body.dark-mode .login-box { background-color: #2D333B; border-color: #444C56; }
        body.dark-mode .logo { color: #E6EDF3; }
        body.dark-mode .form-group label { color: #ADBAC7; }
        body.dark-mode .form-group input { 
            background-color: #22272E; 
            border-color: #444C56; 
            color: #E6EDF3;
        }
        body.dark-mode .register-link { color: #ADBAC7; }
        body.dark-mode .swal2-popup {
            background-color: #2D333B;
            color: #E6EDF3;
        }
        body.dark-mode .swal2-title, body.dark-mode .swal2-html-container {
            color: #E6EDF3 !important;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <a href="index.php" class="logo">CASUALS.</a>
        
        <?php if (!empty($success_message)): ?>
            <p class="success"><?php echo $success_message; ?></p>
        <?php endif; ?>

        <form method="POST" action="login.php">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="penus@gmail.com" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" value="penus" required>
            </div>
            <button type="submit" class="btn-login">Login</button>
            <?php if (!empty($error_message)): ?>
                <p class="error"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>
        
        <p class="register-link">Belum punya akun? <a href="register.php">Buat akun di sini</a></p>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            if (localStorage.getItem('theme') === 'dark') {
                $('body').addClass('dark-mode');
            }

            // Tampilkan alert penawaran login instan
            Swal.fire({
                title: 'Halo!',
                text: 'Selamat datang di Toko Casuals. Untuk memudahkan, Anda bisa langsung masuk menggunakan akun demo (penus) yang sudah kami siapkan otomatis.',
                icon: 'info',
                confirmButtonText: 'Login Sekarang',
                confirmButtonColor: '#A98A74',
                customClass: {
                    popup: localStorage.getItem('theme') === 'dark' ? 'dark-mode' : ''
                }
            });
        });
    </script>
</body>
</html>