<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Garage.lk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Add SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar .nav-link {
            color: #333;
            padding: 0.8rem 1rem;
            border-radius: 0.5rem;
            margin: 0.2rem 0;
        }
        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
        }
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        .main-content {
            padding: 2rem;
        }
        .dashboard-card {
            border-radius: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            transition: transform 0.2s;
        }
        .dashboard-card:hover {
            transform: translateY(-5px);
        }
        .status-badge {
            font-size: 0.875rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 bg-white sidebar">
                <div class="d-flex flex-column p-3">
                    <a href="index.php" class="text-center mb-4">
                        <img src="assets/images/Logo.svg" alt="Garage.lk" style="width: 120px;">
                    </a>
                    <ul class="nav nav-pills flex-column mb-auto">
                        <li class="nav-item">
                            <a href="#" class="nav-link active">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-shop me-2"></i>
                                Garage Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-people me-2"></i>
                                User Management
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-flag me-2"></i>
                                Reports
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-gear me-2"></i>
                                System Settings
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4 me-2"></i>
                            <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="api/auth/logout.php">Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2">Admin Dashboard</h1>
                    <div>
                        <button class="btn btn-outline-primary me-2">
                            <i class="bi bi-download"></i> Export Report
                        </button>
                        <button class="btn btn-primary">
                            <i class="bi bi-plus-lg"></i> Add Admin
                        </button>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">0</div>
                                    <span class="text-muted">Registered</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Pending Verifications</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">0</div>
                                    <span class="text-muted">Garages</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Active Services</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">0</div>
                                    <span class="text-muted">Services</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Revenue</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">₨0</div>
                                    <span class="text-muted">LKR</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pending Verifications -->
                <div class="card dashboard-card mb-4">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Pending Garage Verifications</h5>
                        <button class="btn btn-sm btn-outline-primary">View All</button>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4">
                            <i class="bi bi-clipboard-check fs-1 text-muted"></i>
                            <p class="mt-2">No pending verifications</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card dashboard-card">
                            <div class="card-header bg-transparent">
                                <h5 class="card-title mb-0">Recent Reports</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center py-4">
                                    <i class="bi bi-flag fs-1 text-muted"></i>
                                    <p class="mt-2">No recent reports</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card dashboard-card">
                            <div class="card-header bg-transparent">
                                <h5 class="card-title mb-0">System Status</h5>
                            </div>
                            <div class="card-body">
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Database Status
                                        <span class="badge bg-success status-badge">Connected</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Server Load
                                        <span class="badge bg-success status-badge">Normal</span>
                                    </li>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        Storage Usage
                                        <span class="badge bg-info status-badge">32%</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Handle logout
        document.querySelector('a[href="api/auth/logout.php"]').addEventListener('click', async function(e) {
            e.preventDefault();
            
            const result = await Swal.fire({
                title: 'Logout',
                text: 'Are you sure you want to logout?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, logout',
                cancelButtonText: 'Cancel'
            });

            if (result.isConfirmed) {
                try {
                    const response = await fetch('api/auth/logout.php');
                    const data = await response.json();

                    if (data.success) {
                        await Swal.fire({
                            title: 'Success!',
                            text: data.message,
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                        window.location.href = 'login.php';
                    } else {
                        throw new Error(data.message);
                    }
                } catch (error) {
                    Swal.fire({
                        title: 'Error!',
                        text: error.message || 'Something went wrong',
                        icon: 'error'
                    });
                }
            }
        });

        // Handle Add Admin button
        document.querySelector('button.btn-primary').addEventListener('click', function() {
            Swal.fire({
                title: 'Add New Admin',
                html: `
                    <form id="addAdminForm" class="text-start">
                        <div class="mb-3">
                            <label for="firstName" class="form-label">First Name</label>
                            <input type="text" class="form-control" id="firstName" required>
                        </div>
                        <div class="mb-3">
                            <label for="lastName" class="form-label">Last Name</label>
                            <input type="text" class="form-control" id="lastName" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPassword" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirmPassword" required>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add Admin',
                cancelButtonText: 'Cancel',
                focusConfirm: false,
                preConfirm: () => {
                    const form = document.getElementById('addAdminForm');
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return false;
                    }
                    const password = document.getElementById('password').value;
                    const confirmPassword = document.getElementById('confirmPassword').value;
                    if (password !== confirmPassword) {
                        Swal.showValidationMessage('Passwords do not match');
                        return false;
                    }
                    return {
                        firstName: document.getElementById('firstName').value,
                        lastName: document.getElementById('lastName').value,
                        email: document.getElementById('email').value,
                        password: password
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would typically make an API call to create the admin
                    Swal.fire({
                        title: 'Success!',
                        text: 'New admin has been added successfully',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        // Handle Export Report button
        document.querySelector('.btn-outline-primary').addEventListener('click', function() {
            Swal.fire({
                title: 'Export Report',
                html: `
                    <form id="exportForm" class="text-start">
                        <div class="mb-3">
                            <label class="form-label">Report Type</label>
                            <select class="form-select" id="reportType" required>
                                <option value="">Select report type</option>
                                <option value="users">Users Report</option>
                                <option value="garages">Garages Report</option>
                                <option value="services">Services Report</option>
                                <option value="revenue">Revenue Report</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Date Range</label>
                            <select class="form-select" id="dateRange" required>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="year">This Year</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Format</label>
                            <select class="form-select" id="format" required>
                                <option value="pdf">PDF</option>
                                <option value="excel">Excel</option>
                                <option value="csv">CSV</option>
                            </select>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Export',
                cancelButtonText: 'Cancel',
                focusConfirm: false,
                preConfirm: () => {
                    const form = document.getElementById('exportForm');
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return false;
                    }
                    return {
                        type: document.getElementById('reportType').value,
                        dateRange: document.getElementById('dateRange').value,
                        format: document.getElementById('format').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Processing...',
                        text: 'Generating your report',
                        timer: 2000,
                        timerProgressBar: true,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    }).then(() => {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Report has been generated and downloaded',
                            icon: 'success',
                            timer: 1500,
                            showConfirmButton: false
                        });
                    });
                }
            });
        });

        // Handle View All button
        document.querySelector('.btn-sm.btn-outline-primary').addEventListener('click', function() {
            Swal.fire({
                title: 'Pending Verifications',
                text: 'This feature will be available soon!',
                icon: 'info'
            });
        });

        // Show welcome message on page load
        window.addEventListener('load', function() {
            const adminName = <?php echo json_encode($_SESSION['name']); ?>;
            Swal.fire({
                title: `Welcome back, ${adminName}!`,
                text: 'You\'re logged in to the admin dashboard',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false,
                position: 'top-end',
                toast: true
            });
        });
    </script>
</body>
</html> 