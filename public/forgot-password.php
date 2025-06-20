<?php
session_start();

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    $redirect = $_SESSION['role'] === 'garage' ? 'garage-dashboard.php' :
               ($_SESSION['role'] === 'admin' ? 'admin-dashboard.php' : 'customer-dashboard.php');
    header("Location: $redirect");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Garage.lk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .password-reset-container {
            max-width: 450px;
            margin: 50px auto;
        }
        .password-reset-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .password-reset-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .password-reset-header img {
            width: 150px;
            margin-bottom: 20px;
        }
        .form-floating {
            margin-bottom: 1rem;
        }
        .form-floating input {
            border-radius: 8px;
        }
        .btn-reset {
            height: 48px;
            font-size: 1.1rem;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="password-reset-container">
            <div class="password-reset-header">
                <a href="index.php">
                    <img src="assets/images/Logo.svg" alt="Garage.lk">
                </a>
                <h2>Forgot Your Password?</h2>
                <p class="text-muted">Enter your email address to receive a password reset code.</p>
            </div>
            
            <div class="card password-reset-card">
                <div class="card-body p-4">
                    <form id="requestResetForm" action="api/auth/request-password-reset.php" method="POST">
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                            <label for="email">Email address</label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 btn-reset mb-3">Send Reset Code</button>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="mb-0">Remember your password? <a href="login.php" class="text-decoration-none">Login</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.getElementById('requestResetForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            Swal.fire({
                title: 'Sending code...',
                html: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const formData = new FormData(this);
                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    await Swal.fire({
                        icon: 'success',
                        title: 'Code Sent!',
                        text: data.message,
                        confirmButtonColor: '#28a745',
                        timer: 2000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });
                    // Redirect to a page where user can enter the reset code and new password
                    window.location.href = 'reset-password.php?email=' + encodeURIComponent(document.getElementById('email').value);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Request Failed',
                        text: data.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            } catch (error) {
                console.error('Password reset request error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An unexpected error occurred. Please try again later.',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    </script>
</body>
</html> 