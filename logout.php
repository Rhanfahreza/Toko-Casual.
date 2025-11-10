<?php
// FILE: logout.php

// 1. Selalu mulai session, bahkan saat menghapusnya
session_start();

// 2. Kosongkan semua data di dalam session
session_unset();

// 3. Hancurkan session-nya di server
session_destroy();

// 4. Lempar pengguna kembali ke halaman login
header('Location: login.php');
exit;
?>