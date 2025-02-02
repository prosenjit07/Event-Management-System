# Event Management System

A simple web-based event management system built with PHP and MySQL that allows users to create, manage, and view events, as well as handle attendee registrations.

## Features

- User Authentication (Login/Registration)
- Event Management (CRUD operations)
- Attendee Registration System
- Event Dashboard with filtering and sorting
- Event Reports Generation (CSV)
- Responsive Design using Bootstrap
- AJAX-powered interactions
- JSON API endpoints

## Technical Stack

- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5.1
- HTML5/CSS3
- JavaScript (ES6+)

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/event-management-system.git
```

2. Import the database schema:
- Create a new MySQL database
- Import the `database.sql` file

3. Configure database connection:
- Update `config/database.php` with your credentials

4. Server Requirements:
- PHP 7.4 or higher
- MySQL 5.7 or higher
- PDO PHP Extension
- mod_rewrite enabled

## Project Structure

```
event-management-system/
├── config/
│   └── database.php
├── controllers/
│   ├── AuthController.php
│   ├── EventController.php
│   └── MainController.php
├── views/
│   ├── auth/
│   │   ├── login.php
│   │   └── register.php
│   ├── events/
│   │   ├── create.php
│   │   ├── edit.php
│   │   └── list.php
│   └── home.php
├── assets/
│   ├── css/
│   └── js/
├── api/
│   └── events.php
├── database.sql
├── index.php
└── README.md
```

## API Endpoints

- `GET /api/events` - List all events
- `GET /api/events/{id}` - Get specific event details
- `POST /api/events` - Create new event
- `PUT /api/events/{id}` - Update event
- `DELETE /api/events/{id}` - Delete event

## Test Credentials

Admin User:
- Email: admin@example.com
- Password: admin123

Regular User:
- Email: user@example.com
- Password: user123

## Security Features

- Password Hashing using bcrypt
- Prepared Statements for SQL queries
- CSRF Protection
- Input Validation
- Session Management

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a new Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
