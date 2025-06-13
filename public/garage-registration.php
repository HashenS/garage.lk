<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Garage Registration - Garage.lk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .required-field::after {
            content: " *";
            color: red;
        }
        .preview-image {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Garage Registration</h2>
        
        <form id="garageRegistrationForm" enctype="multipart/form-data">
            <input type="hidden" name="action" value="register">
            <input type="hidden" name="user_id" value="1"> <!-- This should be set dynamically based on logged-in user -->

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label required-field">Business Name</label>
                        <input type="text" class="form-control" name="business_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required-field">Business Registration Number (BRN)</label>
                        <input type="text" class="form-control" name="brn" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required-field">Owner's Name</label>
                        <input type="text" class="form-control" name="owner_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required-field">Owner's NIC</label>
                        <input type="text" class="form-control" name="owner_nic" required>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label required-field">Address</label>
                        <textarea class="form-control" name="address" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required-field">Phone Number</label>
                        <input type="tel" class="form-control" name="phone" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required-field">Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required-field">Location</label>
                        <div id="map" style="height: 200px;" class="mb-2"></div>
                        <input type="hidden" name="latitude" id="latitude" required>
                        <input type="hidden" name="longitude" id="longitude" required>
                    </div>
                </div>
            </div>

            <h4 class="mt-4 mb-3">Required Documents</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label required-field">Business Registration Document</label>
                        <input type="file" class="form-control" name="brn" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div id="brnPreview" class="preview-image"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required-field">Owner's NIC</label>
                        <input type="file" class="form-control" name="nic" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div id="nicPreview" class="preview-image"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label required-field">Utility Bill</label>
                        <input type="file" class="form-control" name="utility_bill" accept=".pdf,.jpg,.jpeg,.png" required>
                        <div id="utilityBillPreview" class="preview-image"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label required-field">Garage Photos</label>
                        <input type="file" class="form-control" name="garage_photo" accept=".jpg,.jpeg,.png" required>
                        <div id="garagePhotoPreview" class="preview-image"></div>
                    </div>
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Register Garage</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_GOOGLE_MAPS_API_KEY&callback=initMap" async defer></script>
    <script>
        // Initialize Google Maps
        function initMap() {
            const map = new google.maps.Map(document.getElementById('map'), {
                center: { lat: 6.9271, lng: 79.8612 }, // Colombo coordinates
                zoom: 13
            });

            const marker = new google.maps.Marker({
                map: map,
                draggable: true
            });

            // Update coordinates when marker is dragged
            marker.addListener('dragend', function() {
                document.getElementById('latitude').value = marker.getPosition().lat();
                document.getElementById('longitude').value = marker.getPosition().lng();
            });

            // Set initial marker position
            marker.setPosition(map.getCenter());
            document.getElementById('latitude').value = map.getCenter().lat();
            document.getElementById('longitude').value = map.getCenter().lng();
        }

        // Preview uploaded images
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function(e) {
                const preview = document.getElementById(this.name + 'Preview');
                const file = this.files[0];
                
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        preview.innerHTML = `<img src="${e.target.result}" class="img-fluid">`;
                    }
                    reader.readAsDataURL(file);
                }
            });
        });

        // Handle form submission
        document.getElementById('garageRegistrationForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('api/garage_routes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Garage registered successfully!');
                    window.location.href = 'dashboard.php';
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while registering the garage.');
            });
        });
    </script>
</body>
</html> 