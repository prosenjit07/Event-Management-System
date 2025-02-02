<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - Event Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container py-4">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h3>Edit Event</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form method="POST" action="index.php?route=events/edit&id=<?php echo htmlspecialchars($event['id']); ?>">
                            <div class="mb-3">
                                <label for="name" class="form-label">Event Name</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                    value="<?php echo htmlspecialchars($event['name']); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" 
                                    rows="3" required><?php echo htmlspecialchars($event['description']); ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="date" class="form-label">Event Date</label>
                                <input type="datetime-local" class="form-control" id="date" name="date" 
                                    value="<?php echo date('Y-m-d\TH:i', strtotime($event['date'])); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="capacity" class="form-label">Maximum Capacity</label>
                                <input type="number" class="form-control" id="capacity" name="capacity" 
                                    value="<?php echo htmlspecialchars($event['capacity']); ?>" min="1" required>
                            </div>
                            <div class="d-flex justify-content-between">
                                <a href="index.php?route=events" class="btn btn-secondary">Back to List</a>
                                <button type="submit" class="btn btn-primary">Update Event</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
