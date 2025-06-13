# Garage.lk - Sri Lanka's Premier Garage Management Platform

Garage.lk is a comprehensive web platform designed to connect vehicle owners in Sri Lanka with verified garages and genuine spare parts. The platform aims to streamline the vehicle maintenance process while ensuring quality service and authentic parts.

## Features

- **Garage Management**
  - Garage registration and verification system
  - Document verification (BRN, NIC, Utility bills)
  - Service management and pricing
  - Location-based garage search

- **Booking System**
  - Online appointment scheduling
  - Service tracking
  - Customer reviews and ratings

- **Spare Parts Management**
  - Online spare parts store
  - Inventory management
  - Order processing

- **User Management**
  - Customer accounts
  - Garage owner accounts
  - Admin dashboard

## Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Libraries**: Bootstrap 5, Google Maps API
- **Additional**: jQuery, AJAX

## Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/garage.lk.git
```

2. Create a MySQL database and import the schema:
```bash
mysql -u your_username -p < database/schema.sql
```

3. Configure the database connection:
- Copy `src/config/database.example.php` to `src/config/database.php`
- Update the database credentials in `database.php`

4. Set up the web server:
- Point your web server to the `public` directory
- Ensure the `uploads` directory is writable
- Configure URL rewriting if needed

5. Get required API keys:
- Google Maps API key for location services
- Update the key in `public/garage-registration.php`

## Directory Structure

```
garage.lk/
├── database/
│   └── schema.sql
├── public/
│   ├── admin/
│   ├── api/
│   ├── uploads/
│   └── index.php
├── src/
│   ├── config/
│   ├── controllers/
│   ├── models/
│   └── routes/
└── README.md
```

## Security Considerations

- All user passwords are hashed
- File uploads are validated
- SQL injection prevention using prepared statements
- XSS protection
- CSRF protection

## Contributing

1. Fork the repository
2. Create your feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

For support, email support@garage.lk or visit our website at https://garage.lk

## Acknowledgments

- All garage owners and mechanics in Sri Lanka
- The open-source community
- Our dedicated development team 