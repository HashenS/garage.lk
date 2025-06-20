<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage.lk - Find Trusted Garages in Sri Lanka</title>
    <link rel="icon" type="image/svg+xml" href="assets/icon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #cd1e25;
            --primary-dark: #a8181e;
            --secondary: #000000;
            --background: #fff;
        }
        .navbar {
            background: #fff !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .navbar-brand img {
            height: 44px;
        }
        .navbar-nav .nav-link {
            color: #222 !important;
            font-weight: 500;
            margin-right: 10px;
            transition: color 0.2s, border-bottom 0.2s;
            border-bottom: 2px solid transparent;
        }
        .navbar-nav .nav-link:hover,
        .navbar-nav .nav-link:focus {
            color: var(--primary) !important;
            border-bottom: 2px solid var(--primary);
            background: transparent;
        }
        .navbar-nav .nav-link.active {
            color: var(--primary) !important;
            border-bottom: 2px solid var(--primary);
            background: transparent;
        }
        .btn-outline-light {
            color: var(--primary);
            border-color: var(--primary);
            background: #fff;
        }
        .btn-outline-light:hover {
            color: #fff;
            background: var(--primary);
            border-color: var(--primary);
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
        .feature-card {
            border: none;
            border-radius: 10px;
            transition: transform 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.08);
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(205, 30, 37, 0.12);
        }
        .feature-card .bi {
            color: var(--primary);
        }
        .hero-section {
            background: linear-gradient(rgba(205, 30, 37, 0.85), rgba(0, 0, 0, 0.6)), url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
        }
        .search-box {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .testimonial-card {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin: 10px 0;
        }
        .footer {
            background: var(--secondary);
            color: #fff;
        }
        .footer a {
            color: #fff;
        }
        .footer a:hover {
            color: var(--primary);
        }
        .navbar-logo {
            height: 44px;
            max-height: 44px;
            width: auto;
            max-width: 180px;
            margin-right: 10px;
            display: inline-block;
            vertical-align: middle;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/Garagelk/public/">
                <img src="assets/images/Logo.svg" alt="Garage.lk Logo" class="navbar-logo">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#find-garage">Find Garage</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#spare-parts">Spare Parts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php
                            $dashboard_link = 'customer-dashboard.php';
                            if (isset($_SESSION['role'])) {
                                if ($_SESSION['role'] === 'garage') {
                                    $dashboard_link = 'garage-dashboard.php';
                                } elseif ($_SESSION['role'] === 'admin') {
                                    $dashboard_link = 'admin-dashboard.php';
                                }
                            }
                        ?>
                        <a href="<?php echo $dashboard_link; ?>" class="btn btn-primary">Profile</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline-light me-2">Login</a>
                        <a href="register.php" class="btn btn-primary">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <h1 class="display-4 mb-4">Find Trusted Garages Near You</h1>
            <p class="lead mb-5">Connect with verified garages and get quality service for your vehicle</p>
            
            <div class="search-box mx-auto" style="max-width: 600px;">
                <form action="search.php" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <input type="text" class="form-control" placeholder="Search Garage Name" name="garage_name">
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="province-select" name="province">
                                <option value="">Select Province</option>
                                <option value="Western">Western</option>
                                <option value="Central">Central</option>
                                <option value="Southern">Southern</option>
                                <option value="Northern">Northern</option>
                                <option value="Eastern">Eastern</option>
                                <option value="North Central">North Central</option>
                                <option value="North Western">North Western</option>
                                <option value="Uva">Uva</option>
                                <option value="Sabaragamuwa">Sabaragamuwa</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="city-select" name="city" disabled>
                                <option value="">Select City</option>
                            </select>
                        </div>
                        <div class="col-md-12 mt-3">
                            <button type="submit" class="btn btn-primary w-100">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-5" id="features">
        <div class="container">
            <h2 class="text-center mb-5">Why Choose Garage.lk?</h2>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-shield-check display-4 mb-3"></i>
                            <h5 class="card-title">Verified Garages</h5>
                            <p class="card-text">All garages are verified and trusted by our team</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-calendar-check display-4 mb-3"></i>
                            <h5 class="card-title">Easy Booking</h5>
                            <p class="card-text">Book appointments online with just a few clicks</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card feature-card h-100">
                        <div class="card-body text-center">
                            <i class="bi bi-star display-4 mb-3"></i>
                            <h5 class="card-title">Quality Service</h5>
                            <p class="card-text">Get the best service from experienced mechanics</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">How It Works</h2>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background: var(--primary); color: #fff;">
                            <span class="h4 mb-0">1</span>
                        </div>
                        <h5>Search</h5>
                        <p>Find garages near your location</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background: var(--primary); color: #fff;">
                            <span class="h4 mb-0">2</span>
                        </div>
                        <h5>Compare</h5>
                        <p>Compare prices and reviews</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background: var(--primary); color: #fff;">
                            <span class="h4 mb-0">3</span>
                        </div>
                        <h5>Book</h5>
                        <p>Book your appointment online</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="text-center">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px; background: var(--primary); color: #fff;">
                            <span class="h4 mb-0">4</span>
                        </div>
                        <h5>Service</h5>
                        <p>Get your vehicle serviced</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">What Our Customers Say</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <img src="assets/images/user1.jpg" alt="User" class="rounded-circle me-3" width="50">
                            <div>
                                <h6 class="mb-0">Kamal Perera</h6>
                                <small class="text-muted">Colombo</small>
                            </div>
                        </div>
                        <p class="mb-0">"Great service! Found a reliable garage near my home. The booking process was smooth and the service was excellent."</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <img src="assets/images/user2.jpg" alt="User" class="rounded-circle me-3" width="50">
                            <div>
                                <h6 class="mb-0">Nimal Silva</h6>
                                <small class="text-muted">Kandy</small>
                            </div>
                        </div>
                        <p class="mb-0">"The platform helped me find a garage that specialized in my car model. Very satisfied with the service."</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center mb-3">
                            <img src="assets/images/user3.jpg" alt="User" class="rounded-circle me-3" width="50">
                            <div>
                                <h6 class="mb-0">Priya Fernando</h6>
                                <small class="text-muted">Galle</small>
                            </div>
                        </div>
                        <p class="mb-0">"Easy to use and reliable. The garage I found through Garage.lk provided excellent service at a reasonable price."</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="py-5" style="background: var(--primary); color: #fff;">
        <div class="container text-center">
            <h2 class="mb-4">Are You a Garage Owner?</h2>
            <p class="lead mb-4">Join our platform and grow your business</p>
            <a href="garage-registration.php" class="btn btn-light btn-lg">Register Your Garage</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>About Garage.lk</h5>
                    <p>Connecting vehicle owners with trusted garages across Sri Lanka.</p>
                </div>
                <div class="col-md-4">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white">Find Garage</a></li>
                        <li><a href="#" class="text-white">Services</a></li>
                        <li><a href="#" class="text-white">Spare Parts</a></li>
                        <li><a href="#" class="text-white">About Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Contact Us</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-telephone"></i> +94 11 234 5678</li>
                        <li><i class="bi bi-envelope"></i> info@garage.lk</li>
                        <li><i class="bi bi-geo-alt"></i> Colombo, Sri Lanka</li>
                    </ul>
                </div>
            </div>
            <hr class="my-4">
            <div class="text-center">
                <p class="mb-0">&copy; 2024 Garage.lk. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const citiesByProvince = {
            "Western": ["Colombo", "Gampaha", "Kalutara"],
            "Central": ["Kandy", "Matale", "Nuwara Eliya"],
            "Southern": ["Galle", "Matara", "Hambantota"],
            "Northern": ["Jaffna", "Kilinochchi", "Mannar", "Mullaitivu", "Vavuniya"],
            "Eastern": ["Batticaloa", "Ampara", "Trincomalee"],
            "North Central": ["Anuradhapura", "Polonnaruwa"],
            "North Western": ["Kurunegala", "Puttalam"],
            "Uva": ["Badulla", "Monaragala"],
            "Sabaragamuwa": ["Kegalle", "Ratnapura"]
        };

        const provinceSelect = document.getElementById('province-select');
        const citySelect = document.getElementById('city-select');

        provinceSelect.addEventListener('change', function() {
            const selectedProvince = this.value;
            citySelect.innerHTML = '<option value="">Select City</option>'; // Clear existing options
            citySelect.disabled = true; // Disable city select by default

            if (selectedProvince && citiesByProvince[selectedProvince]) {
                citiesByProvince[selectedProvince].forEach(city => {
                    const option = document.createElement('option');
                    option.value = city;
                    option.textContent = city;
                    citySelect.appendChild(option);
                });
                citySelect.disabled = false; // Enable city select if province is selected
            }
        });
    </script>
</body>
</html> 