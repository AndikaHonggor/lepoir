<?php
require_once 'config/database.php';
require_once 'config/functions.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['admin_id'])) {
    redirect('admin/dashboard.php');
}

$error = '';
$pengaturan = fetchSingleData("SELECT * FROM pengaturan LIMIT 1");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    // Validasi input
    if (empty($email) || empty($password)) {
        $error = 'Email dan password tidak boleh kosong!';
    } else {
        // Cek admin di database
        $query = "SELECT * FROM admin WHERE email = '$email'";
        $result = executeQuery($query);
        
        if ($result && $result->num_rows > 0) {
            $admin = $result->fetch_assoc();
            
            // Verifikasi password
            if (verifyPassword($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_email'] = $admin['email'];
                $_SESSION['admin_name'] = $admin['nama'];
                redirect('admin/dashboard.php');
            } else {
                $error = 'Email atau password salah!';
            }
        } else {
            $error = 'Email atau password salah!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Lepoir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background: linear-gradient(135deg, #0d6efd 0%, #04419c 100%); min-height: 100vh;">
    <div class="login-container">
        <div class="login-card">
            <div class="text-center mb-3">
                <?php if (!empty($pengaturan['logo'])): ?>
                    <img src="<?php echo htmlspecialchars($pengaturan['logo']); ?>" alt="Logo perusahaan" class="login-logo">
                <?php else: ?>
                    <i class="bi bi-droplet-half text-primary" style="font-size: 2.5rem;"></i>
                <?php endif; ?>
            </div>
            <h2>Admin Login</h2>
            <p class="text-muted">Masuk ke dashboard Lepoir</p>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle me-2"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn btn-primary-custom btn-modern w-100">
                    <i class="bi bi-door-open me-2"></i> Masuk
                </button>
            </form>
            
            <hr class="my-4" style="border-color: var(--card-border);">
            
            <!-- <p class="text-center text-muted small mb-0">
                Default Email: <strong>admin@asterwear.com</strong><br>
                Default Password: <strong>admin123</strong>
            </p> -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
