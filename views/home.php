<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .container { padding-top: 2rem; }
        .event-card { transition: transform 0.2s; }
        .event-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <?php include 'views/components/navbar.php'; ?>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Welcome to Event Management</h1>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="index.php?route=events/create" class="btn btn-primary">Create Event</a>
            <?php endif; ?>
        </div>

        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Upcoming Events</h5>
                        <div id="upcoming-events" class="row"></div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">My Events</h5>
                        <div id="my-events"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">My Registrations</h5>
                        <div id="my-registrations"></div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        fetchUpcomingEvents();
        <?php if (isset($_SESSION['user_id'])): ?>
        fetchMyEvents();
        fetchMyRegistrations();
        <?php endif; ?>
    });

    async function fetchUpcomingEvents() {
        try {
            const response = await fetch('index.php?route=api/events/upcoming');
            const data = await response.json();
            
            if (data.success) {
                displayUpcomingEvents(data.events);
            }
        } catch (error) {
            console.error('Error:', error);
        }
    }

    function displayUpcomingEvents(events) {
        const container = document.getElementById('upcoming-events');
        if (events.length === 0) {
            container.innerHTML = '<p class="col-12 text-center">No upcoming events.</p>';
            return;
        }

        container.innerHTML = events.map(event => `
            <div class="col-md-4 mb-3">
                <div class="card event-card h-100">
                    <div class="card-body">
                        <h5 class="card-title">${event.name}</h5>
                        <p class="card-text">${event.description}</p>
                        <p class="text-muted">
                            <small>Date: ${new Date(event.date).toLocaleString()}</small><br>
                            <small>Available: ${event.capacity - event.registered_count} spots</small>
                        </p>
                        <a href="index.php?route=events/view&id=${event.id}" 
                           class="btn btn-outline-primary btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        `).join('');
    }

    // Additional functions for logged-in users
    async function fetchMyEvents() {
        const response = await fetch('index.php?route=api/events/my-events');
        const data = await response.json();
        if (data.success) {
            displayMyEvents(data.events);
        }
    }

    async function fetchMyRegistrations() {
        const response = await fetch('index.php?route=api/events/my-registrations');
        const data = await response.json();
        if (data.success) {
            displayMyRegistrations(data.registrations);
        }
    }

    // Display functions for my events and registrations...
    </script>
</body>
</html>