<?php
// ============================================================
// Dinamic - Koneksi database & helper keamanan
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ------------------------------------------------------------
// Konfigurasi koneksi (ubah sesuai environment)
// ------------------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_NAME', 'db_stockbarang_local');
define('DB_USER', 'root');
define('DB_PASS', '');

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $exception) {
            // Detail error masuk ke log server, pengguna hanya melihat pesan umum
            error_log('Koneksi database gagal: ' . $exception->getMessage());
            die('Koneksi database gagal. Pastikan MySQL berjalan dan database telah diimpor.');
        }
        return $this->conn;
    }
}

// ------------------------------------------------------------
// Helper keamanan
// ------------------------------------------------------------

/** Escape output agar aman dari XSS. */
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

/** Ambil (atau buat) CSRF token untuk sesi ini. */
function csrf_token()
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Hidden input CSRF untuk dipakai di dalam <form>. */
function csrf_field()
{
    return '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/** Tolak request POST yang tidak membawa CSRF token yang sah. */
function verify_csrf()
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(403);
        die('Permintaan tidak valid: CSRF token tidak cocok.');
    }
}
