# ELO Supply Chain - Installation & Setup Guide

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- XAMPP or equivalent (Apache + MySQL + PHP)
- Web server running on localhost

## Installation Steps

### 1. **Database Setup**

Navigate to your project and run the setup script:

```
http://localhost/ELO-Supply-Chain/setup.php
```

This will:
- Create the `elo_supply_chain` database
- Create all required tables
- Create a default admin account

### 2. **Default Admin Credentials**

After setup, you can login with:

```
Email: admin@elo.com
Password: admin123
```

**⚠️ Important:** Change the admin password after first login!

### 3. **Configuration**

Edit `config/config.php` if using non-standard database credentials:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'elo_supply_chain');
```

## Project Structure

```
ELO-Supply-Chain/
├── config/
│   └── config.php           # Database & site configuration
├── includes/
│   ├── Database.php         # Database connection class
│   ├── functions.php        # Helper functions
│   └── header.php           # Common header
├── admin/
│   ├── dashboard.php        # Admin dashboard
│   ├── products.php         # Product management
│   ├── purchase-requests.php # Purchase approval
│   ├── sellers.php          # Seller management
│   └── reports.php          # Analytics & reports
├── seller/
│   ├── dashboard.php        # Seller dashboard
│   ├── products.php         # Browse products
│   ├── request-product.php  # Submit purchase request
│   └── purchase-history.php # Purchase history
├── assets/
│   └── styles.css           # Global styles
├── uploads/                 # Product images
├── index.php                # Homepage
├── login.php                # Login page
├── logout.php               # Logout handler
├── setup.php                # Database setup (run once)
└── README.md                # Documentation
```

## Features

### Admin Features
✓ Product Management (CRUD)
✓ Product Image Upload
✓ Stock Management
✓ Base Price Setting
✓ Seller Account Approval/Rejection
✓ Purchase Request Review & Approval
✓ Stock Deduction on Approval
✓ Comprehensive Reports
✓ Analytics Dashboard

### Seller Features
✓ View Available Products
✓ Check Stock Levels
✓ Submit Purchase Requests
✓ Track Purchase History
✓ Filter Requests by Status
✓ View Seller Dashboard with Statistics

## Key Workflows

### 1. **Product Management (Admin)**
1. Go to Admin Dashboard → Products
2. Click "Add New Product"
3. Fill in product details
4. Upload product image (optional)
5. Set base price and stock quantity
6. Save product

### 2. **Seller Approval (Admin)**
1. Go to Admin Dashboard → Manage Sellers
2. View pending seller applications
3. Click "Approve" to enable seller access
4. Or reject the seller account

### 3. **Purchase Request Flow**
1. Seller browses products
2. Clicks "Request Product"
3. Enters desired quantity
4. Submits request (goes to "Pending")
5. Admin reviews in "Purchase Requests"
6. Admin approves → Stock decreases automatically
7. Seller can view approved purchase in history

## Security Features

✓ Password hashing with bcrypt
✓ Prepared statements (PDO) to prevent SQL injection
✓ Session-based authentication
✓ Role-based access control
✓ File upload validation
✓ Input sanitization
✓ CSRF protection ready (can be added)

## Database Schema

### users table
- id: INT (Primary Key)
- name: VARCHAR(255)
- email: VARCHAR(255) - Unique
- password: VARCHAR(255) - Bcrypt hashed
- role: ENUM('admin', 'seller')
- status: ENUM('pending', 'approved', 'rejected')
- created_at: DATETIME
- updated_at: DATETIME

### products table
- id: INT (Primary Key)
- name: VARCHAR(255)
- description: TEXT
- base_price: DECIMAL(10,2)
- stock_quantity: INT
- image: VARCHAR(255)
- created_at: DATETIME
- updated_at: DATETIME

### purchase_requests table
- id: INT (Primary Key)
- seller_id: INT (Foreign Key → users.id)
- product_id: INT (Foreign Key → products.id)
- quantity: INT
- status: ENUM('pending', 'approved', 'rejected')
- created_at: DATETIME
- updated_at: DATETIME

## Future Enhancements

- [ ] Multi-warehouse support
- [ ] Seller credit/balance system
- [ ] Commission tracking
- [ ] REST API for mobile apps
- [ ] Export reports (CSV/PDF)
- [ ] Email notifications
- [ ] Two-factor authentication
- [ ] Activity audit logs
- [ ] Multi-language support
- [ ] SMS notifications

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in `config/config.php`
- Ensure database was created via setup.php

### Upload Not Working
- Check `uploads/` folder has write permissions (755)
- Ensure file size is within limits
- Only JPG, PNG, GIF files are allowed

### Login Issues
- Clear browser cookies/cache
- Verify account status is "approved"
- Check email and password are correct

## Support

For issues or questions, refer to the main README.md or modify code as needed.

---

**Version:** 1.0
**Last Updated:** February 2026
