<?php
// Fungsi untuk format tanggal Indonesia (contoh)
function formatTanggalIndo($tanggal) {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    $tgl = date('d', strtotime($tanggal));
    $bln = $bulan[(int)date('m', strtotime($tanggal))];
    $thn = date('Y', strtotime($tanggal));
    return "$tgl $bln $thn";
}

// Fungsi redirect dengan pesan (flash message sederhana)
function redirectWithMessage($url, $msg) {
    $_SESSION['flash_message'] = $msg;
    header("Location: $url");
    exit;
}

// Fungsi tampilkan pesan flash jika ada
function showFlashMessage() {
    if (!empty($_SESSION['flash_message'])) {
        echo '<div class="alert alert-info">'.htmlspecialchars($_SESSION['flash_message']).'</div>';
        unset($_SESSION['flash_message']);
    }
}
