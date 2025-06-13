<?php
session_start();

// Check if user is logged in and is a garage owner
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'garage') {
    header('Location: login.php');
    exit;
}

// Check verification status
$verification_status = $_SESSION['verification_status'] ?? 'pending';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Dashboard - Garage.lk</title>
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
        .verification-banner {
            background-color: #fff3cd;
            border-color: #ffecb5;
            color: #664d03;
        }
        .verification-banner.verified {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
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
                                <i class="bi bi-calendar-check me-2"></i>
                                Appointments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-tools me-2"></i>
                                Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-chat-dots me-2"></i>
                                Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-graph-up me-2"></i>
                                Analytics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="bi bi-gear me-2"></i>
                                Settings
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4 me-2"></i>
                            <strong><?php echo htmlspecialchars($_SESSION['business_name']); ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
                            <li><a class="dropdown-item" href="#">Profile</a></li>
                            <li><a class="dropdown-item" href="#">Business Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="api/auth/logout.php">Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <?php if ($verification_status !== 'verified'): ?>
                <div class="alert verification-banner alert-dismissible fade show mb-4" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <div>
                            <strong>Verification Status: <?php echo ucfirst($verification_status); ?></strong>
                            <p class="mb-0">Your garage listing is currently under review. We'll notify you once the verification is complete.</p>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <?php endif; ?>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2">Welcome, <?php echo htmlspecialchars($_SESSION['business_name']); ?>!</h1>
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Add New Service
                    </button>
                </div>

                <!-- Quick Stats -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Today's Appointments</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">0</div>
                                    <span class="text-muted">Scheduled</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Pending Requests</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">0</div>
                                    <span class="text-muted">Requests</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Services</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">0</div>
                                    <span class="text-muted">Available</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Revenue (This Month)</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">₨0</div>
                                    <span class="text-muted">LKR</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Today's Schedule -->
                <div class="card dashboard-card mb-4">
                    <div class="card-header bg-transparent d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Today's Schedule</h5>
                        <button class="btn btn-sm btn-outline-primary">View All</button>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="mt-2">No appointments scheduled for today</p>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card dashboard-card">
                            <div class="card-header bg-transparent">
                                <h5 class="card-title mb-0">Recent Reviews</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center py-4">
                                    <i class="bi bi-star fs-1 text-muted"></i>
                                    <p class="mt-2">No reviews yet</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card dashboard-card">
                            <div class="card-header bg-transparent">
                                <h5 class="card-title mb-0">Recent Messages</h5>
                            </div>
                            <div class="card-body">
                                <div class="text-center py-4">
                                    <i class="bi bi-chat-dots fs-1 text-muted"></i>
                                    <p class="mt-2">No new messages</p>
                                </div>
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

        // Handle Add New Service button
        document.querySelector('button.btn-primary').addEventListener('click', function() {
            Swal.fire({
                title: 'Add New Service',
                html: `
                    <form id="addServiceForm" class="text-start">
                        <div class="mb-3">
                            <label for="serviceName" class="form-label">Service Name</label>
                            <input type="text" class="form-control" id="serviceName" required>
                        </div>
                        <div class="mb-3">
                            <label for="serviceDescription" class="form-label">Description</label>
                            <textarea class="form-control" id="serviceDescription" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="servicePrice" class="form-label">Price (LKR)</label>
                            <input type="number" class="form-control" id="servicePrice" required>
                        </div>
                        <div class="mb-3">
                            <label for="serviceDuration" class="form-label">Duration (minutes)</label>
                            <input type="number" class="form-control" id="serviceDuration" required>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Add Service',
                cancelButtonText: 'Cancel',
                focusConfirm: false,
                preConfirm: () => {
                    const form = document.getElementById('addServiceForm');
                    if (!form.checkValidity()) {
                        form.reportValidity();
                        return false;
                    }
                    return {
                        name: document.getElementById('serviceName').value,
                        description: document.getElementById('serviceDescription').value,
                        price: document.getElementById('servicePrice').value,
                        duration: document.getElementById('serviceDuration').value
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Here you would typically make an API call to save the service
                    Swal.fire({
                        title: 'Success!',
                        text: 'Service has been added successfully',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        });

        // Handle View All buttons
        document.querySelectorAll('.btn-outline-primary').forEach(button => {
            button.addEventListener('click', function() {
                const title = this.closest('.card').querySelector('.card-title').textContent;
                Swal.fire({
                    title: title,
                    text: 'This feature will be available soon!',
                    icon: 'info'
                });
            });
        });

        // Show welcome message on page load
        window.addEventListener('load', function() {
            const businessName = <?php echo json_encode($_SESSION['business_name']); ?>;
            Swal.fire({
                title: `Welcome back, ${businessName}!`,
                text: 'You\'re logged in to your garage dashboard',
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