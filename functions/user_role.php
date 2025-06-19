<?php
// Fungsi cek apakah user memiliki role tertentu
function hasRole($allowed_roles = []) {
    if (!isset($_SESSION['role'])) return false;
    return in_array($_SESSION['role'], $allowed_roles);
}
