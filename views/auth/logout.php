<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logging Out - Event Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .logout-container {
            max-width: 400px;
            margin: 100px auto;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout-container">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center mb-4">Logging Out...</h3>
                    <p>You are being logged out. Please wait...</p>
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Redirect to login page after a brief delay
        setTimeout(() => {
            window.location.href = 'index.php?route=login';
        }, 1500);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>