<?php
session_start();

// Check if user is logged in and is a customer
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/config/database.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT first_name, last_name, email, phone, email_notifications, sms_notifications FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard'; // Default to dashboard

$page_title = "Customer Dashboard - Garage.lk";
switch ($current_tab) {
    case 'appointments':
        $page_title = "My Appointments - Garage.lk";
        break;
    case 'vehicles':
        $page_title = "My Vehicles - Garage.lk";
        break;
    case 'messages':
        $page_title = "Messages - Garage.lk";
        break;
    case 'profile':
        $page_title = "Profile - Garage.lk";
        break;
    case 'settings':
        $page_title = "Settings - Garage.lk";
        break;
    default:
        $page_title = "Customer Dashboard - Garage.lk";
        break;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #cd1e25;
            --primary-dark: #a8181e;
            --secondary: #000000;
            --background: #fff;
        }
        .sidebar {
            min-height: 100vh;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .sidebar .nav-link {
            color: #333;
            padding: 0.8rem 1rem;
            border-radius: 0.5rem;
            margin: 0.2rem 0;
            display: flex;
            align-items: center;
            transition: background-color 0.2s, color 0.2s, transform 0.2s;
        }
        .sidebar .nav-link i {
            min-width: 1.25rem;
            display: inline-block !important; /* Ensure it's always rendered */
            opacity: 1 !important; /* Ensure it's never transparent */
            visibility: visible !important; /* Ensure it's never hidden */
            color: #000; /* Default color for unselected icons */
            transition: transform 0.2s ease-in-out; /* Add transition for icon animation */
        }
        .sidebar .nav-link:hover {
            background-color: #f8f9fa;
        }
        .sidebar .nav-link.active {
            background-color: var(--primary);
            color: white;
        }
        .sidebar .nav-link.active i {
            color: white; /* Active icon color */
            transform: scale(1.1);
        }
        .dropdown > a.dropdown-toggle, 
        .dropdown > a.dropdown-toggle strong, 
        .dropdown > a.dropdown-toggle i {
            color: var(--primary) !important;
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
        .btn-primary {
            background: var(--primary) !important;
            border-color: var(--primary) !important;
            color: #fff !important;
        }
        .btn-primary:hover {
            background: var(--primary-dark) !important;
            border-color: var(--primary-dark) !important;
            color: #fff !important;
        }
        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
        }
        .btn-outline-primary:hover {
            background: var(--primary);
            color: #fff;
        }
        .dropdown-toggle, .dropdown-toggle i {
            color: var(--primary) !important;
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
                            <a href="customer-dashboard.php?tab=dashboard" class="nav-link <?php echo ($current_tab == 'dashboard') ? 'active' : ''; ?>">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="customer-dashboard.php?tab=appointments" class="nav-link <?php echo ($current_tab == 'appointments') ? 'active' : ''; ?>">
                                <i class="bi bi-calendar-check me-2"></i>
                                My Appointments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="customer-dashboard.php?tab=vehicles" class="nav-link <?php echo ($current_tab == 'vehicles') ? 'active' : ''; ?>">
                                <i class="bi bi-truck me-2"></i>
                                My Vehicles
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="customer-dashboard.php?tab=messages" class="nav-link <?php echo ($current_tab == 'messages') ? 'active' : ''; ?>">
                                <i class="bi bi-chat-dots me-2"></i>
                                Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="customer-dashboard.php?tab=profile" class="nav-link <?php echo ($current_tab == 'profile') ? 'active' : ''; ?>">
                                <i class="bi bi-person-circle me-2"></i>
                                Profile
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="customer-dashboard.php?tab=settings" class="nav-link <?php echo ($current_tab == 'settings') ? 'active' : ''; ?>">
                                <i class="bi bi-gear me-2"></i>
                                Settings
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-danger" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4 me-2 text-danger"></i>
                            <strong><?php echo htmlspecialchars($_SESSION['name']); ?></strong>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser">
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="api/auth/logout.php">Sign out</a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <?php if ($current_tab == 'dashboard'): ?>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
                    <button class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> Book New Appointment
                    </button>
                </div>

                <!-- Quick Stats -->
                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Active Appointments</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">0</div>
                                    <span class="text-muted">Upcoming</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Registered Vehicles</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">0</div>
                                    <span class="text-muted">Vehicles</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Total Services</h5>
                                <div class="d-flex align-items-center">
                                    <div class="display-4 me-3">0</div>
                                    <span class="text-muted">Completed</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="card dashboard-card">
                    <div class="card-header bg-transparent">
                        <h5 class="card-title mb-0">Recent Activity</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x fs-1 text-muted"></i>
                            <p class="mt-2">No recent activity to display</p>
                        </div>
                    </div>
                </div>
                <?php elseif ($current_tab == 'appointments'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h2">My Appointments</h1>
                    </div>
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <p>This is where your appointments will be displayed.</p>
                        </div>
                    </div>
                <?php elseif ($current_tab == 'vehicles'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h2">My Vehicles</h1>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addVehicleModal">
                            <i class="bi bi-plus-lg me-2"></i> Add New Vehicle
                        </button>
                    </div>
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div id="vehicles-list">
                                <!-- Vehicles will be loaded here via AJAX -->
                                <div class="text-center py-4">
                                    <i class="bi bi-car-front fs-1 text-muted"></i>
                                    <p class="mt-2">No vehicles registered yet.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Add Vehicle Modal -->
                    <div class="modal fade" id="addVehicleModal" tabindex="-1" aria-labelledby="addVehicleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="addVehicleModalLabel">Add New Vehicle</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form id="add-vehicle-form">
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label for="make" class="form-label">Make</label>
                                            <input type="text" class="form-control" id="make" name="make" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="model" class="form-label">Model</label>
                                            <input type="text" class="form-control" id="model" name="model" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="year" class="form-label">Year</label>
                                            <input type="number" class="form-control" id="year" name="year" min="1900" max="<?php echo date('Y'); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="license_plate" class="form-label">License Plate</label>
                                            <input type="text" class="form-control" id="license_plate" name="license_plate" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="vin" class="form-label">VIN (Optional)</label>
                                            <input type="text" class="form-control" id="vin" name="vin" maxlength="17">
                                        </div>
                                        <div class="mb-3">
                                            <label for="color" class="form-label">Color (Optional)</label>
                                            <input type="text" class="form-control" id="color" name="color">
                                        </div>
                                        <div id="add-vehicle-alert-placeholder" class="mt-3"></div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Add Vehicle</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php elseif ($current_tab == 'messages'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h2">Messages</h1>
                    </div>
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <p>This is where your messages will be displayed.</p>
                        </div>
                    </div>
                <?php elseif ($current_tab == 'profile'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h2">Profile</h1>
                    </div>
                    <!-- Alert Placeholder -->
                    <div id="alert-placeholder" class="mt-3"></div>

                    <!-- Profile Form -->
                    <div class="card dashboard-card">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">Personal Information</h5>
                        </div>
                        <div class="card-body">
                            <?php if ($user) { ?>
                                <form id="profile-form" action="api/customer-profile-update.php" method="POST">
                                    <div class="mb-3">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email address</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="phone" class="form-label">Phone Number</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                </form>
                            <?php } else { ?>
                                <div class="alert alert-danger">User data not found.</div>
                            <?php } ?>
                        </div>
                    </div>
                <?php elseif ($current_tab == 'settings'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h2">Settings</h1>
                    </div>
                    <div class="card dashboard-card mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">Change Password</h5>
                        </div>
                        <div class="card-body">
                            <form id="change-password-form">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_new_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_new_password" name="confirm_new_password" required>
                                </div>
                                <div id="change-password-alert-placeholder" class="mt-3"></div>
                                <button type="submit" class="btn btn-primary">Change Password</button>
                            </form>
                        </div>
                    </div>

                    <div class="card dashboard-card mb-4">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">Notification Preferences</h5>
                        </div>
                        <div class="card-body">
                            <form id="notification-preferences-form">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="email_notifications" name="email_notifications" value="1" <?php echo ($user['email_notifications'] ?? 1) ? 'checked' : ''; ?> >
                                    <label class="form-check-label" for="email_notifications">
                                        Receive email notifications for appointments and updates.
                                    </label>
                                </div>
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="sms_notifications" name="sms_notifications" value="1" <?php echo ($user['sms_notifications'] ?? 0) ? 'checked' : ''; ?> >
                                    <label class="form-check-label" for="sms_notifications">
                                        Receive SMS notifications for critical alerts.
                                    </label>
                                </div>
                                <div id="notification-alert-placeholder" class="mt-3"></div>
                                <button type="submit" class="btn btn-primary">Save Preferences</button>
                            </form>
                        </div>
                    </div>

                    <div class="card dashboard-card">
                        <div class="card-header bg-transparent">
                            <h5 class="card-title mb-0">Other Settings</h5>
                        </div>
                        <div class="card-body">
                            <p>Additional settings and account management options will be made available in future updates.</p>
                            <button type="button" class="btn btn-outline-danger" id="delete-account-button">Delete Account</button>
                            <div id="delete-account-alert-placeholder" class="mt-3"></div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Function to load vehicles
            function loadVehicles() {
                $.ajax({
                    url: 'api/get-vehicles.php',
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        const vehiclesList = $('#vehicles-list');
                        vehiclesList.empty(); // Clear existing list
                        if (response.success && response.vehicles.length > 0) {
                            response.vehicles.forEach(function(vehicle) {
                                vehiclesList.append(
                                    `<div class="card mb-3">
                                        <div class="card-body">
                                            <h5 class="card-title">${vehicle.make} ${vehicle.model} (${vehicle.year})</h5>
                                            <p class="card-text mb-1"><strong>License Plate:</strong> ${vehicle.license_plate}</p>
                                            <p class="card-text mb-1"><strong>Color:</strong> ${vehicle.color || 'N/A'}</p>
                                            <p class="card-text"><strong>VIN:</strong> ${vehicle.vin || 'N/A'}</p>
                                        </div>
                                    </div>`
                                );
                            });
                        } else {
                            vehiclesList.html(
                                `<div class="text-center py-4">
                                    <i class="bi bi-car-front fs-1 text-muted"></i>
                                    <p class="mt-2">No vehicles registered yet.</p>
                                </div>`
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#vehicles-list').html(
                            `<div class="alert alert-danger">Error loading vehicles: ${error}</div>`
                        );
                        console.error("AJAX error loading vehicles: ", status, error, xhr);
                    }
                });
            }

            // Load vehicles when the page loads if on the vehicles tab
            if ('<?php echo $current_tab; ?>' == 'vehicles') {
                loadVehicles();
            }

            // Event listener for tab change to load vehicles
            $('a[href="customer-dashboard.php?tab=vehicles"]').on('click', function(e) {
                // This click handler will only be relevant if we switch to client-side tab loading
                // For now, the page reloads, so the above check handles it.
                // If you implement client-side tab switching, enable this:
                // loadVehicles();
            });

            $('#profile-form').on('submit', function(e) {
                e.preventDefault(); // Prevent default form submission

                const form = $(this);
                const formData = form.serialize(); // Serialize form data
                const alertPlaceholder = $('#alert-placeholder');

                // Clear previous alerts
                alertPlaceholder.empty();

                $.ajax({
                    url: form.attr('action'),
                    type: form.attr('method'),
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alertPlaceholder.html(
                                '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                    response.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>'
                            );
                            // Update the name in the sidebar if successful
                            const newFirstName = $('#first_name').val();
                            const newLastName = $('#last_name').val();
                            $('#dropdownUser strong').text(newFirstName + ' ' + newLastName);
                        } else {
                            alertPlaceholder.html(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                    response.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        alertPlaceholder.html(
                            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                'An error occurred: ' + error +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>'
                        );
                        console.error("AJAX error: ", status, error, xhr);
                    }
                });
            });

            // Handle Add Vehicle Form Submission
            $('#add-vehicle-form').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const formData = form.serialize();
                const alertPlaceholder = $('#add-vehicle-alert-placeholder');

                alertPlaceholder.empty();

                $.ajax({
                    url: 'api/add-vehicle.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alertPlaceholder.html(
                                '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                    response.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>'
                            );
                            form[0].reset(); // Clear the form
                            $('#addVehicleModal').modal('hide'); // Hide the modal
                            loadVehicles(); // Reload vehicles list
                        } else {
                            alertPlaceholder.html(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                    response.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        alertPlaceholder.html(
                            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                'An error occurred: ' + error +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>'
                        );
                        console.error("AJAX error adding vehicle: ", status, error, xhr);
                    }
                });
            });

            $('#change-password-form').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const formData = form.serialize();
                const alertPlaceholder = $('#change-password-alert-placeholder');

                alertPlaceholder.empty();

                $.ajax({
                    url: 'api/change-password.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alertPlaceholder.html(
                                '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                    response.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>'
                            );
                        } else {
                            alertPlaceholder.html(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                    response.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        alertPlaceholder.html(
                            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                'An error occurred: ' + error +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>'
                        );
                        console.error("AJAX error: ", status, error, xhr);
                    }
                });
            });

            $('#notification-preferences-form').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                const formData = form.serialize();
                const alertPlaceholder = $('#notification-alert-placeholder');

                alertPlaceholder.empty();

                $.ajax({
                    url: 'api/update-notification-preferences.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alertPlaceholder.html(
                                '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                    response.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>'
                            );
                        } else {
                            alertPlaceholder.html(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                    response.message +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>'
                            );
                        }
                    },
                    error: function(xhr, status, error) {
                        alertPlaceholder.html(
                            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                'An error occurred: ' + error +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>'
                        );
                        console.error("AJAX error: ", status, error, xhr);
                    }
                });
            });

            // Handle Account Deletion
            $('#delete-account-button').on('click', function(e) {
                if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
                    const alertPlaceholder = $('#delete-account-alert-placeholder');
                    alertPlaceholder.empty();

                    $.ajax({
                        url: 'api/delete-account.php',
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alertPlaceholder.html(
                                    '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                                        response.message +
                                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                    '</div>'
                                );
                                // Redirect to login or homepage after successful deletion
                                window.location.href = 'login.php?status=account_deleted';
                            } else {
                                alertPlaceholder.html(
                                    '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                        response.message +
                                        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                    '</div>'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            alertPlaceholder.html(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                    'An error occurred: ' + error +
                                    '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>'
                            );
                            console.error("AJAX error deleting account: ", status, error, xhr);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html> 