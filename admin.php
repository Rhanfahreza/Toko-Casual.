<?php
session_start();

// Keamanan: Hanya admin@gmail.com yang boleh masuk
if (!isset($_SESSION['user_nama']) || !isset($_SESSION['user_email']) || $_SESSION['user_email'] !== 'admin@gmail.com') {
    header('Location: index.php');
    exit;
}

$file_path = 'users.json';
$users = [];
if (file_exists($file_path)) {
    $json_data = file_get_contents($file_path);
    $users = json_decode($json_data, true);
}

// Proses Hapus Pengguna (AJAX POST / GET)
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['email'])) {
    $target_email = $_GET['email'];
    
    // Jangan izinkan menghapus diri sendiri (admin)
    if ($target_email === $_SESSION['user_email']) {
        echo json_encode(['success' => false, 'message' => 'Anda tidak bisa menghapus akun admin yang sedang digunakan!']);
        exit;
    }

    if (isset($users[$target_email])) {
        unset($users[$target_email]);
        file_put_contents($file_path, json_encode($users, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true, 'message' => 'Akun berhasil dihapus!']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Akun tidak ditemukan!']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Toko Casuals</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body { 
            font-family: 'Nunito Sans', sans-serif; 
            background-color: #FAF8F5; 
            color: #4B4B4B; 
            margin: 0; 
            padding: 20px; 
            transition: background-color 0.3s ease, color 0.3s ease; 
        }
        .container { 
            max-width: 900px; 
            margin: 40px auto; 
            padding: 30px; 
            background: #fff; 
            border-radius: 10px; 
            border: 1px solid #F0EAE4; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.05); 
            transition: background-color 0.3s ease, border-color 0.3s ease; 
        }
        h1 { 
            text-align: center; 
            margin-bottom: 30px; 
            color: #5D4037; 
            font-weight: 700; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            gap: 10px; 
        }
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }
        .btn-kembali { 
            display: inline-block; 
            border: 2px solid #A98A74; 
            color: #A98A74; 
            padding: 8px 16px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 700; 
            transition: all 0.3s ease; 
        }
        .btn-kembali:hover { 
            background-color: #A98A74; 
            color: #ffffff; 
        }
        .search-wrapper {
            position: relative;
            flex-grow: 1;
            max-width: 300px;
        }
        .search-wrapper input {
            width: 100%;
            padding: 10px 15px 10px 35px;
            border: 1px solid #DCD3CB;
            border-radius: 8px;
            font-family: 'Nunito Sans', sans-serif;
            box-sizing: border-box;
        }
        .search-wrapper i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #A98A74;
        }
        .table-responsive {
            width: 100%;
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid #F0EAE4;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th, td { 
            padding: 15px; 
            text-align: left; 
            border-bottom: 1px solid #F0EAE4; 
            vertical-align: middle; 
        }
        th { 
            background-color: #FAF8F5; 
            font-weight: 700; 
            color: #5D4037; 
        }
        .btn-danger {
            background-color: #e74c3c;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-danger:hover {
            background-color: #c0392b;
        }
        .badge {
            background-color: #A98A74;
            color: white;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 700;
        }
        .badge.admin {
            background-color: #5D4037;
        }

        /* --- DARK MODE --- */
        body.dark-mode { background-color: #22272E; color: #ADBAC7; }
        body.dark-mode .container { background-color: #2D333B; border-color: #444C56; }
        body.dark-mode h1 { color: #E6EDF3; }
        body.dark-mode .btn-kembali { color: #ADBAC7; border-color: #ADBAC7; }
        body.dark-mode .btn-kembali:hover { background-color: #ADBAC7; color: #2D333B; }
        body.dark-mode .search-wrapper input { background-color: #22272E; border-color: #444C56; color: #E6EDF3; }
        body.dark-mode th { background-color: #22272E; color: #E6EDF3; }
        body.dark-mode td, body.dark-mode th, body.dark-mode .table-responsive { border-color: #444C56; }
        body.dark-mode .swal2-popup { background-color: #2D333B; color: #E6EDF3; }
        body.dark-mode .swal2-title, body.dark-mode .swal2-html-container { color: #E6EDF3 !important; }

        @media (max-width: 600px) {
            .header-actions {
                flex-direction: column;
                align-items: stretch;
            }
            .search-wrapper {
                max-width: 100%;
            }
            th, td {
                padding: 10px;
                font-size: 0.9rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="bi bi-shield-lock-fill"></i> Panel Administrator</h1>
        
        <div class="header-actions">
            <a href="index.php" class="btn-kembali">&larr; Kembali ke Toko</a>
            <div class="search-wrapper">
                <i class="bi bi-search"></i>
                <input type="text" id="search-user" placeholder="Cari nama atau email...">
            </div>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Peran</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="user-table-body">
                    <?php foreach ($users as $email => $user): ?>
                        <tr class="user-row" data-name="<?php echo htmlspecialchars(strtolower($user['nama'])); ?>" data-email="<?php echo htmlspecialchars(strtolower($email)); ?>">
                            <td><strong><?php echo htmlspecialchars($user['nama']); ?></strong></td>
                            <td><?php echo htmlspecialchars($email); ?></td>
                            <td>
                                <?php if ($email === 'admin@gmail.com'): ?>
                                    <span class="badge admin">Admin</span>
                                <?php else: ?>
                                    <span class="badge">User</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($email !== 'admin@gmail.com'): ?>
                                    <button class="btn-danger btn-delete-user" data-email="<?php echo htmlspecialchars($email); ?>">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                <?php else: ?>
                                    <span style="color: #999; font-size: 0.9rem;">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            // Cek tema dark mode
            if (localStorage.getItem('theme') === 'dark') {
                $('body').addClass('dark-mode');
            }

            // Real-time search
            $('#search-user').on('keyup', function () {
                var query = $(this).val().toLowerCase();
                $('.user-row').each(function () {
                    var name = $(this).data('name');
                    var email = $(this).data('email');
                    if (name.includes(query) || email.includes(query)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // AJAX Delete User
            $('.btn-delete-user').on('click', function () {
                var email = $(this).data('email');
                var row = $(this).closest('tr');

                Swal.fire({
                    title: 'Hapus Akun?',
                    text: 'Apakah Anda yakin ingin menghapus akun ' + email + '? Tindakan ini tidak dapat dibatalkan.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#e74c3c',
                    cancelButtonColor: '#A98A74',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: localStorage.getItem('theme') === 'dark' ? 'dark-mode' : ''
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'admin.php',
                            type: 'GET',
                            data: {
                                action: 'delete',
                                email: email
                            },
                            dataType: 'json',
                            success: function (response) {
                                if (response.success) {
                                    row.fadeOut(300, function () {
                                        $(this).remove();
                                    });
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: response.message,
                                        icon: 'success',
                                        confirmButtonColor: '#A98A74',
                                        customClass: {
                                            popup: localStorage.getItem('theme') === 'dark' ? 'dark-mode' : ''
                                        }
                                    });
                                } else {
                                    Swal.fire({
                                        title: 'Gagal!',
                                        text: response.message,
                                        icon: 'error',
                                        confirmButtonColor: '#A98A74',
                                        customClass: {
                                            popup: localStorage.getItem('theme') === 'dark' ? 'dark-mode' : ''
                                        }
                                    });
                                }
                            },
                            error: function () {
                                Swal.fire({
                                    title: 'Error!',
                                    text: 'Terjadi kesalahan pada server.',
                                    icon: 'error',
                                    confirmButtonColor: '#A98A74',
                                    customClass: {
                                        popup: localStorage.getItem('theme') === 'dark' ? 'dark-mode' : ''
                                    }
                                });
                            }
                        });
                    }
                });
            });
        });
    </script>
</body>
</html>
