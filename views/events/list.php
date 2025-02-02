<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events - Event Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-primary fw-bold">DASHBOARD PAGE</h2>
            <div>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <a href="index.php?route=events/report/all" 
       class="btn btn-success me-2">
        <i class="bi bi-download"></i> Download All Reports
    </a>
<?php endif; ?>
                <a href="index.php?route=events/create" class="btn btn-primary rounded-pill px-4 shadow-sm hover-shadow">
                    <i class="bi bi-plus-circle me-2"></i>Attendee Registration
                </a>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="mb-3">
                    <input type="text" id="searchInput" class="form-control" placeholder="Search events...">
                </div>

                <?php if (empty($events)): ?>
                    <p class="text-center">No events found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Date</th>
                                    <th>Capacity</th>
                                    <th>Registered</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($events as $event): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($event['name']); ?></td>
                                        <td><?php echo date('Y-m-d H:i', strtotime($event['date'])); ?></td>
                                        <td><?php echo htmlspecialchars($event['capacity']); ?></td>
                                        <td><?php echo htmlspecialchars($event['registered_count']); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <a href="index.php?route=events/edit&id=<?php echo $event['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary">Edit</a>
                                                <!-- <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                                    <a href="index.php?route=events/report&id=<?php echo $event['id']; ?>" 
                                                       class="btn btn-sm btn-outline-info">
                                                        <i class="bi bi-download"></i> Download Report
                                                    </a>
                                                <?php endif; ?> -->
                                                <button onclick="deleteEvent(<?php echo $event['id']; ?>)" 
                                                        class="btn btn-sm btn-outline-danger">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                    <a class="page-link" href="index.php?route=events&page=<?php echo $i; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    async function deleteEvent(id) {
        if (confirm('Are you sure you want to delete this event?')) {
            try {
                const response = await fetch(`index.php?route=events/delete&id=${id}`, {
                    method: 'POST'
                });
                const data = await response.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    alert('Failed to delete event');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred');
            }
        }
    }

    document.getElementById('searchInput').addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
    </script>
</body>
</html>