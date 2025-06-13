<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Garage.lk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Add SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .register-container {
            max-width: 600px;
            margin: 50px auto;
        }
        .register-card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header img {
            width: 150px;
            margin-bottom: 20px;
        }
        .role-selector {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        .role-option {
            flex: 1;
            text-align: center;
            padding: 15px;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .role-option:hover {
            border-color: #0d6efd;
        }
        .role-option.selected {
            border-color: #0d6efd;
            background-color: #e7f1ff;
        }
        .role-option i {
            font-size: 24px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="register-container">
            <div class="register-header">
                <a href="index.php">
                    <img src="assets/images/Logo.svg" alt="Garage.lk">
                </a>
                <h2>Create an Account</h2>
                <p class="text-muted">Join our community of vehicle owners and garage professionals</p>
            </div>
            
            <div class="card register-card">
                <div class="card-body p-4">
                    <form action="api/auth/register.php" method="POST" id="registerForm">
                        <input type="hidden" name="role" id="selectedRole" value="customer">
                        
                        <div class="role-selector">
                            <div class="role-option selected" data-role="customer">
                                <i class="bi bi-person"></i>
                                <h5>Customer</h5>
                                <p class="small text-muted">I want to find garages</p>
                            </div>
                            <div class="role-option" data-role="garage">
                                <i class="bi bi-tools"></i>
                                <h5>Garage Owner</h5>
                                <p class="small text-muted">I own a garage</p>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" class="form-control" name="first_name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" class="form-control" name="last_name" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" name="phone" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="terms.php">Terms and Conditions</a>
                            </label>
                        </div>
                        
                        <button type="submit" class="btn btn-primary w-100 mb-3">Create Account</button>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <p class="mb-0">Already have an account? <a href="login.php" class="text-decoration-none">Login</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Show error message function
        function showError(message) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: message,
                confirmButtonColor: '#dc3545'
            });
        }

        // Show success message function
        function showSuccess(message) {
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: message,
                confirmButtonColor: '#28a745'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'login.php';
                }
            });
        }

        // Handle role selection
        document.querySelectorAll('.role-option').forEach(option => {
            option.addEventListener('click', function() {
                // Remove selected class from all options
                document.querySelectorAll('.role-option').forEach(opt => {
                    opt.classList.remove('selected');
                });
                
                // Add selected class to clicked option
                this.classList.add('selected');
                
                // Update hidden input
                document.getElementById('selectedRole').value = this.dataset.role;
            });
        });

        // Form validation and submission
        document.getElementById('registerForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const password = this.querySelector('input[name="password"]').value;
            const confirmPassword = this.querySelector('input[name="confirm_password"]').value;
            
            if (password !== confirmPassword) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'Passwords do not match!',
                    confirmButtonColor: '#dc3545'
                });
                return;
            }

            // Show loading state
            Swal.fire({
                title: 'Creating your account...',
                html: 'Please wait while we set up your account...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const formData = new FormData(this);
                const response = await fetch('api/auth/register.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    // Show success message
                    await Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        confirmButtonColor: '#28a745',
                        allowOutsideClick: false,
                        timer: 2000,
                        timerProgressBar: true
                    });

                    // Redirect to appropriate dashboard
                    window.location.href = data.redirect;
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Registration Failed',
                        text: data.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            } catch (error) {
                console.error('Registration error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An unexpected error occurred. Please try again later.',
                    confirmButtonColor: '#dc3545'
                });
            }
        });

        // Show validation messages on input
        const passwordInput = document.querySelector('input[name="password"]');
        const confirmPasswordInput = document.querySelector('input[name="confirm_password"]');

        confirmPasswordInput.addEventListener('input', function() {
            if (this.value !== passwordInput.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });

        // Phone number validation
        const phoneInput = document.querySelector('input[name="phone"]');
        phoneInput.addEventListener('input', function() {
            // Remove any non-digit characters except + at the start for display
            this.value = this.value.replace(/[^\d+]/g, '');
            if (this.value.startsWith('+') && this.value.length > 1) {
                // Only allow one + at the start
                this.value = '+' + this.value.substring(1).replace(/\+/g, '');
            }
            
            // Check for valid Sri Lankan format
            const phoneRegex = /^(\+94|0)[1-9][0-9]{8}$/;
            if (!phoneRegex.test(this.value)) {
                this.setCustomValidity('Please enter a valid Sri Lankan phone number (e.g., 0771234567 or +94771234567)');
                
                // Add specific error messages
                if (this.value.length < 10) {
                    this.setCustomValidity('Phone number is too short. It should be 10 digits (e.g., 0771234567)');
                } else if (this.value.length > 12) {
                    this.setCustomValidity('Phone number is too long. Use format: 0771234567 or +94771234567');
                } else if (!this.value.match(/^(\+94|0)/)) {
                    this.setCustomValidity('Phone number must start with 0 or +94');
                }
            } else {
                this.setCustomValidity('');
            }
        });

        // Add placeholder and pattern to phone input
        phoneInput.setAttribute('placeholder', '0771234567 or +94771234567');
        phoneInput.setAttribute('pattern', '(\\+94|0)[1-9][0-9]{8}');
        phoneInput.setAttribute('title', 'Enter a valid Sri Lankan phone number (e.g., 0771234567 or +94771234567)');

        // Email validation
        const emailInput = document.querySelector('input[name="email"]');
        emailInput.addEventListener('input', function() {
            if (this.validity.typeMismatch) {
                this.setCustomValidity('Please enter a valid email address');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html> 