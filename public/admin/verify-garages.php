<?php
session_start();
// Add authentication check here
// if (!isset($_SESSION['admin_id'])) {
//     header('Location: login.php');
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Garages - Garage.lk Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .document-preview {
            max-width: 300px;
            max-height: 300px;
            margin: 10px 0;
        }
        .verification-card {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
        }
        .verification-card.pending {
            border-left: 4px solid #ffc107;
        }
        .verification-card.verified {
            border-left: 4px solid #198754;
        }
        .verification-card.rejected {
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h2>Garage Verification Panel</h2>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="#" data-status="pending">Pending</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-status="verified">Verified</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#" data-status="rejected">Rejected</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div id="garageList">
                            <!-- Garage verification cards will be loaded here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification Modal -->
    <div class="modal fade" id="verificationModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Verify Garage</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="verificationForm">
                        <input type="hidden" name="action" value="verify">
                        <input type="hidden" name="garage_id" id="modalGarageId">
                        <input type="hidden" name="admin_id" value="<?php echo $_SESSION['admin_id'] ?? 1; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Verification Status</label>
                            <select class="form-select" name="status" required>
                                <option value="verified">Verify</option>
                                <option value="rejected">Reject</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submitVerification">Submit</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentStatus = 'pending';
        const verificationModal = new bootstrap.Modal(document.getElementById('verificationModal'));

        // Load garages based on status
        function loadGarages(status) {
            currentStatus = status;
            fetch(`api/garage_routes.php?action=pending`)
                .then(response => response.json())
                .then(data => {
                    const garageList = document.getElementById('garageList');
                    garageList.innerHTML = '';

                    data.forEach(garage => {
                        const card = createGarageCard(garage);
                        garageList.appendChild(card);
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading garages');
                });
        }

        // Create garage verification card
        function createGarageCard(garage) {
            const card = document.createElement('div');
            card.className = `verification-card ${garage.verification_status}`;
            
            card.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <h5>${garage.business_name}</h5>
                        <p><strong>BRN:</strong> ${garage.business_registration_number}</p>
                        <p><strong>Owner:</strong> ${garage.owner_name}</p>
                        <p><strong>NIC:</strong> ${garage.owner_nic}</p>
                        <p><strong>Address:</strong> ${garage.address}</p>
                        <p><strong>Phone:</strong> ${garage.phone}</p>
                        <p><strong>Email:</strong> ${garage.email}</p>
                    </div>
                    <div class="col-md-4">
                        <h6>Documents</h6>
                        ${garage.documents.map(doc => `
                            <div class="mb-2">
                                <strong>${doc.document_type}:</strong>
                                <a href="${doc.file_path}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </div>
                        `).join('')}
                    </div>
                </div>
                <div class="mt-3">
                    <button class="btn btn-primary" onclick="openVerificationModal(${garage.id})">
                        Verify Garage
                    </button>
                </div>
            `;
            
            return card;
        }

        // Open verification modal
        function openVerificationModal(garageId) {
            document.getElementById('modalGarageId').value = garageId;
            verificationModal.show();
        }

        // Handle verification submission
        document.getElementById('submitVerification').addEventListener('click', function() {
            const form = document.getElementById('verificationForm');
            const formData = new FormData(form);
            
            fetch('api/garage_routes.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    verificationModal.hide();
                    loadGarages(currentStatus);
                    alert('Verification status updated successfully');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating verification status');
            });
        });

        // Handle tab clicks
        document.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const status = this.dataset.status;
                
                // Update active tab
                document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
                this.classList.add('active');
                
                loadGarages(status);
            });
        });

        // Load initial data
        loadGarages('pending');
    </script>
</body>
</html> 