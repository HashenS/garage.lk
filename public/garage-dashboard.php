<?php
session_start();

// Check if user is logged in and is a garage owner
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'garage') {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . '/../src/config/database.php';

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT business_name, email, phone, address, verification_status, latitude, longitude, google_maps_link, photos FROM garages WHERE user_id = ?");
$stmt->execute([$user_id]);
$garage_info = $stmt->fetch();

if ($garage_info) {
    $_SESSION['business_name'] = $garage_info['business_name'];
    $_SESSION['verification_status'] = $garage_info['verification_status'];
}

$verification_status = $_SESSION['verification_status'] ?? 'pending';
$current_tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard'; // Default to dashboard

$page_title = "Garage Dashboard - Garage.lk";
switch ($current_tab) {
    case 'appointments':
        $page_title = "My Appointments - Garage.lk";
        break;
    case 'services':
        $page_title = "My Services - Garage.lk";
        break;
    case 'messages':
        $page_title = "Messages - Garage.lk";
        break;
    case 'analytics':
        $page_title = "Analytics - Garage.lk";
        break;
    case 'settings':
        $page_title = "Settings - Garage.lk";
        break;
    case 'garage':
        $page_title = "Garage Details - Garage.lk";
        break;
    default:
        $page_title = "Garage Dashboard - Garage.lk";
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
                            <a href="garage-dashboard.php?tab=dashboard" class="nav-link <?php echo ($current_tab == 'dashboard') ? 'active' : ''; ?>">
                                <i class="bi bi-speedometer2 me-2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="garage-dashboard.php?tab=garage" class="nav-link <?php echo ($current_tab == 'garage') ? 'active' : ''; ?>">
                                <i class="bi bi-shop me-2"></i>
                                Garage
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="garage-dashboard.php?tab=appointments" class="nav-link <?php echo ($current_tab == 'appointments') ? 'active' : ''; ?>">
                                <i class="bi bi-calendar-check me-2"></i>
                                Appointments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="garage-dashboard.php?tab=services" class="nav-link <?php echo ($current_tab == 'services') ? 'active' : ''; ?>">
                                <i class="bi bi-tools me-2"></i>
                                Services
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="garage-dashboard.php?tab=messages" class="nav-link <?php echo ($current_tab == 'messages') ? 'active' : ''; ?>">
                                <i class="bi bi-chat-dots me-2"></i>
                                Messages
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="garage-dashboard.php?tab=analytics" class="nav-link <?php echo ($current_tab == 'analytics') ? 'active' : ''; ?>">
                                <i class="bi bi-graph-up me-2"></i>
                                Analytics
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="garage-dashboard.php?tab=settings" class="nav-link <?php echo ($current_tab == 'settings') ? 'active' : ''; ?>">
                                <i class="bi bi-gear me-2"></i>
                                Settings
                            </a>
                        </li>
                    </ul>
                    <hr>
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle" id="dropdownUser" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle fs-4 me-2"></i>
                            <strong><?php echo htmlspecialchars($_SESSION['business_name'] ?? 'Garage Owner'); ?></strong>
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
                <?php if ($current_tab == 'dashboard'): ?>
                <!-- Verification Banner (removed as per user request) -->
                <!-- No longer displaying verification banner here -->

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h2">Welcome, <?php echo htmlspecialchars($_SESSION['business_name'] ?? 'Garage Owner'); ?>!</h1>
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
                <?php elseif ($current_tab == 'garage'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h2">Garage Verification</h1>
                    </div>
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <form id="garageDetailsForm" class="needs-validation" novalidate>
                                <!-- Alert Placeholder for messages -->
                                <div id="garage-details-alert-placeholder" class="mt-3 mb-3"></div>

                                <h5 class="mb-3">Business Information</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="businessName" class="form-label">Business Name</label>
                                        <input type="text" class="form-control" id="businessName" name="businessName" value="<?php echo htmlspecialchars($garage_info['business_name'] ?? ''); ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter your business name.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($garage_info['email'] ?? ''); ?>" readonly>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label">Phone</label>
                                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($garage_info['phone'] ?? ''); ?>" required>
                                        <div class="invalid-feedback">
                                            Please enter your phone number.
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="address" class="form-label">Address</label>
                                        <textarea class="form-control" id="address" name="address" rows="2" required><?php echo htmlspecialchars($garage_info['address'] ?? ''); ?></textarea>
                                        <div class="invalid-feedback">
                                            Please enter your garage address.
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h5 class="mb-3">Location Details</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="latitude" class="form-label">Latitude</label>
                                        <input type="text" class="form-control" id="latitude" name="latitude" value="<?php echo htmlspecialchars($garage_info['latitude'] ?? ''); ?>" placeholder="e.g., 6.9271" required>
                                        <div class="invalid-feedback">
                                            Please enter the latitude.
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="longitude" class="form-label">Longitude</label>
                                        <input type="text" class="form-control" id="longitude" name="longitude" value="<?php echo htmlspecialchars($garage_info['longitude'] ?? ''); ?>" placeholder="e.g., 79.8612" required>
                                        <div class="invalid-feedback">
                                            Please enter the longitude.
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label for="googleMapsLink" class="form-label">Google Maps Link (Optional)</label>
                                        <input type="url" class="form-control" id="googleMapsLink" name="googleMapsLink" value="<?php echo htmlspecialchars($garage_info['google_maps_link'] ?? ''); ?>" placeholder="https://maps.app.goo.gl/...">
                                        <div class="form-text">
                                            Provide a link to your garage's location on Google Maps.
                                        </div>
                                        <div class="invalid-feedback">
                                            Please enter a valid Google Maps link.
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <h5 class="mb-3">Upload Garage Photos</h5>
                                <div class="mb-3">
                                    <label for="garagePhotos" class="form-label">Select Photos (Max 5)</label>
                                    <input type="file" class="form-control" id="garagePhotos" name="garagePhotos[]" accept="image/*" multiple>
                                    <div class="form-text">Accepted formats: JPG, PNG. Max file size: 5MB per image.</div>
                                    <div class="invalid-feedback">
                                        Please upload at least one photo.
                                    </div>
                                </div>
                                <div id="photoPreview" class="mb-3 row row-cols-3 g-3"></div>

                                <hr class="my-4">

                                <div class="mb-3">
                                    <label for="verificationStatus" class="form-label">Verification Status</label>
                                    <input type="text" class="form-control" id="verificationStatus" value="<?php echo ucfirst(htmlspecialchars($garage_info['verification_status'] ?? 'N/A')); ?>" readonly>
                                </div>
                                <button type="submit" class="btn btn-primary" id="submitButton">Submit</button>
                            </form>
                        </div>
                    </div>
                <?php elseif ($current_tab == 'appointments'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h2">Appointments</h1>
                    </div>
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <p>This is where your appointments will be displayed.</p>
                        </div>
                    </div>
                <?php elseif ($current_tab == 'services'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h2">Services</h1>
                    </div>
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <p>This is where you can manage your services.</p>
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
                <?php elseif ($current_tab == 'analytics'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h2">Analytics</h1>
                    </div>
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <p>This is where your analytics will be displayed.</p>
                        </div>
                    </div>
                <?php elseif ($current_tab == 'settings'): ?>
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h2">Settings</h1>
                    </div>
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <p>This is where you can manage your settings.</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const garageDetailsForm = document.getElementById('garageDetailsForm');
            const alertPlaceholder = document.getElementById('garage-details-alert-placeholder');
            let selectedFiles = new DataTransfer(); // Use DataTransfer object to manage files
            const submitButton = document.getElementById('submitButton');
            const formFields = garageDetailsForm.querySelectorAll('input, textarea, select, button[type="submit"]');
            const verificationStatusInput = document.getElementById('verificationStatus');
            
            // Fetch garage_info values from PHP to JavaScript
            const initialGarageInfo = {
                business_name: <?php echo json_encode($garage_info['business_name'] ?? ''); ?>,
                address: <?php echo json_encode($garage_info['address'] ?? ''); ?>,
                phone: <?php echo json_encode($garage_info['phone'] ?? ''); ?>,
                latitude: <?php echo json_encode($garage_info['latitude'] ?? ''); ?>,
                longitude: <?php echo json_encode($garage_info['longitude'] ?? ''); ?>,
                verification_status: <?php echo json_encode($garage_info['verification_status'] ?? 'N/A'); ?>,
                photos: <?php echo json_encode(json_decode($garage_info['photos'] ?? '[]', true)); ?>
            };

            // Function to disable the form
            function disableForm() {
                formFields.forEach(field => {
                    if (field.id !== 'email') { // Keep email readonly but not disabled to allow its value to be sent
                        field.disabled = true;
                    }
                });
                submitButton.disabled = true;
            }

            // Function to enable the form
            function enableForm() {
                formFields.forEach(field => {
                    field.disabled = false;
                });
                submitButton.disabled = false;
            }

            // Check initial verification status on page load and disable form if pending or verified
            const currentVerificationStatus = initialGarageInfo.verification_status.toLowerCase();
            const isFormPreviouslySubmitted = (
                initialGarageInfo.business_name !== '' &&
                initialGarageInfo.address !== '' &&
                initialGarageInfo.phone !== '' &&
                initialGarageInfo.latitude !== '' &&
                initialGarageInfo.longitude !== ''
            );

            if (currentVerificationStatus === 'verified' || currentVerificationStatus === 'rejected' || (currentVerificationStatus === 'pending' && isFormPreviouslySubmitted)) {
                disableForm();
                showAlert('info', 'Your garage details are currently under review or already verified. The form is locked.', 'verification-pending-alert');
            }

            garageDetailsForm.addEventListener('submit', async function(event) {
                event.preventDefault();
                event.stopPropagation();

                // Clear previous alerts
                alertPlaceholder.innerHTML = '';

                if (garageDetailsForm.checkValidity() === false) {
                    garageDetailsForm.classList.add('was-validated');
                    return;
                }

                garageDetailsForm.classList.add('was-validated');
                disableForm(); // Disable form immediately on submission

                const formData = new FormData(garageDetailsForm);
                
                // Append selected files to formData
                for (let i = 0; i < selectedFiles.files.length; i++) {
                    formData.append('garagePhotos[]', selectedFiles.files[i]);
                }

                try {
                    const response = await fetch('../api/garage/update_details.php', {
                        method: 'POST',
                        body: formData // Send FormData directly
                    });

                    const result = await response.json();

                    if (result.success) {
                        let successMessage = result.message;
                        if (result.message.includes('Verification status is now pending')) {
                            successMessage = 'Your garage details have been submitted and are currently under review.';
                        }
                        Swal.fire({
                            title: 'Success!',
                            text: successMessage,
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            toast: true,
                            position: 'top-end'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        showAlert('danger', result.message || 'An unknown error occurred.');
                        enableForm(); // Re-enable form on error
                    }
                } catch (error) {
                    console.error('Error:', error);
                    showAlert('danger', 'Failed to update garage details. Please try again.');
                    enableForm(); // Re-enable form on network error
                }
            });

            const garagePhotosInput = document.getElementById('garagePhotos');
            const photoPreview = document.getElementById('photoPreview');

            // Function to display photos
            function displayPhotos(fileList) {
                photoPreview.innerHTML = ''; // Clear existing previews
                Array.from(fileList).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const colDiv = document.createElement('div');
                        colDiv.className = 'col position-relative';
                        colDiv.innerHTML = `
                            <img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 100px; object-fit: cover;" alt="Garage Photo">
                            <button type="button" class="btn-close position-absolute top-0 end-0 m-1" aria-label="Remove" data-index="${index}"></button>
                        `;
                        photoPreview.appendChild(colDiv);

                        // Add event listener for remove button
                        colDiv.querySelector('.btn-close').addEventListener('click', function() {
                            const indexToRemove = parseInt(this.dataset.index);
                            removePhoto(indexToRemove);
                        });
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Function to remove a photo
            function removePhoto(index) {
                if (selectedFiles.items.length > index) {
                    selectedFiles.items.remove(index);
                    garagePhotosInput.files = selectedFiles.files; // Update the input's FileList
                    displayPhotos(selectedFiles.files); // Re-render photos
                }
            }

            // Display existing photos on page load
            const existingPhotosJson = <?php echo json_encode(json_decode($garage_info['photos'] ?? '[]', true)); ?>;
            if (existingPhotosJson.length > 0) {
                // Fetch existing photos as Blobs and add to DataTransfer
                fetchExistingPhotosToDataTransfer(existingPhotosJson);
            } else {
                displayPhotos(selectedFiles.files); // Initialize with no photos
            }

            async function fetchExistingPhotosToDataTransfer(urls) {
                for (const url of urls) {
                    try {
                        const response = await fetch(url);
                        const blob = await response.blob();
                        const filename = url.substring(url.lastIndexOf('/') + 1);
                        selectedFiles.items.add(new File([blob], filename, { type: blob.type }));
                    } catch (error) {
                        console.error('Error fetching existing photo:', url, error);
                    }
                }
                garagePhotosInput.files = selectedFiles.files;
                displayPhotos(selectedFiles.files); // Display them from DataTransfer
            }

            garagePhotosInput.addEventListener('change', function() {
                selectedFiles = new DataTransfer(); // Reset for new selection
                const files = this.files;
                
                if (files.length > 5) {
                    showAlert('warning', 'You can upload a maximum of 5 photos.');
                    this.value = ''; // Clear selected files from input
                    displayPhotos(selectedFiles.files); // Clear previews
                    return;
                }

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    if (!file.type.startsWith('image/')) {
                        showAlert('warning', `File ${file.name} is not an image.`);
                        continue;
                    }
                    if (file.size > 5 * 1024 * 1024) { // 5MB
                        showAlert('warning', `File ${file.name} is larger than 5MB.`);
                        continue;
                    }
                    selectedFiles.items.add(file);
                }
                this.files = selectedFiles.files; // Update the input's FileList

                displayPhotos(selectedFiles.files); // Display from DataTransfer
            });

            function showAlert(type, message, id = null) {
                // Remove existing alert with the same ID if present
                if (id) {
                    const existingAlert = document.getElementById(id);
                    if (existingAlert) {
                        existingAlert.remove();
                    }
                }
                const alertDiv = document.createElement('div');
                alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
                if (id) {
                    alertDiv.id = id;
                }
                alertDiv.setAttribute('role', 'alert');
                alertDiv.innerHTML = `
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                alertPlaceholder.appendChild(alertDiv);
            }
        });
    </script>
</body>
</html> 