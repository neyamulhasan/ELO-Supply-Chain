# ELO Supply Chain - Directory Structure

```
ELO-Supply-Chain/
│
├── 📄 README.md                          [Project overview & requirements]
├── 📄 INSTALLATION.md                    [Setup & installation guide]
├── 📄 QUICKSTART.md                      [Quick start guide]
├── 📄 BUILD_SUMMARY.md                   [Build completion summary]
│
├── 📄 index.php                          [Home page]
├── 📄 login.php                          [User login page]
├── 📄 logout.php                         [Session logout handler]
├── 📄 setup.php                          [Database initialization - RUN ONCE]
│
├── 📁 config/
│   └── 📄 config.php                     [Database & site configuration]
│
├── 📁 includes/
│   ├── 📄 Database.php                   [PDO database connection class]
│   ├── 📄 functions.php                  [Helper functions & utilities]
│   └── 📄 header.php                     [Common header component]
│
├── 📁 admin/                             [ADMIN PANEL]
│   ├── 📄 dashboard.php                  [Admin dashboard]
│   ├── 📄 products.php                   [Product management (CRUD)]
│   ├── 📄 purchase-requests.php          [Purchase request approval]
│   ├── 📄 sellers.php                    [Seller account management]
│   └── 📄 reports.php                    [Analytics & reports]
│
├── 📁 seller/                            [SELLER PANEL]
│   ├── 📄 dashboard.php                  [Seller dashboard]
│   ├── 📄 products.php                   [Browse products]
│   ├── 📄 request-product.php            [Submit purchase requests]
│   └── 📄 purchase-history.php           [View purchase history]
│
├── 📁 assets/
│   └── 🎨 styles.css                     [Global CSS styles]
│
└── 📁 uploads/                           [Product images storage]
    └── (empty - populated at runtime)
```

## File Count Summary

| Category | Count | Files |
|----------|-------|-------|
| Configuration | 1 | config.php |
| Includes | 3 | Database.php, functions.php, header.php |
| Root Pages | 4 | index.php, login.php, logout.php, setup.php |
| Admin Pages | 5 | dashboard, products, purchase-requests, sellers, reports |
| Seller Pages | 4 | dashboard, products, request-product, purchase-history |
| Assets | 1 | styles.css |
| Documentation | 4 | README.md, INSTALLATION.md, QUICKSTART.md, BUILD_SUMMARY.md |
| **Total** | **22** | **PHP files + Assets + Docs** |

---

## Quick Navigation

### For Administrators
- Dashboard: `admin/dashboard.php`
- Manage Products: `admin/products.php`
- Review Requests: `admin/purchase-requests.php`
- Manage Sellers: `admin/sellers.php`
- View Reports: `admin/reports.php`

### For Sellers
- Dashboard: `seller/dashboard.php`
- Browse Products: `seller/products.php`
- Submit Requests: `seller/request-product.php`
- Purchase History: `seller/purchase-history.php`

### Core Pages
- Home: `index.php`
- Login: `login.php`
- Logout: `logout.php`
- Setup: `setup.php` (run once to initialize DB)

---

## Configuration Files

```
config/config.php
├── Database Configuration (MySQL)
├── Site Configuration (URLs, Names)
├── Session Configuration
└── Error Reporting Settings
```

---

## Helper Functions (functions.php)

```
includes/functions.php
├── Authentication Functions
│   ├── isLoggedIn()
│   ├── requireLogin()
│   ├── requireAdmin()
│   ├── requireSeller()
│   └── getCurrentUser()
├── Password Functions
│   ├── hashPassword()
│   └── verifyPassword()
├── Flash Messages
│   ├── redirect()
│   ├── getFlashMessage()
├── Input Handling
│   ├── sanitize()
│   └── isValidEmail()
└── File Operations
    ├── uploadFile()
    └── deleteFile()
```

---

## Database Classes (Database.php)

```
Database Connection (PDO)
├── __construct() - Initialize connection
├── getConnection() - Get PDO instance
├── execute() - Run query with parameters
├── fetch() - Get single record
├── fetchAll() - Get multiple records
└── lastInsertId() - Get last inserted ID
```

---

## Styling Structure (styles.css)

```
styles.css (1,000+ lines)
├── Global Styles & Variables
├── Navigation & Header
├── Container & Layout
├── Alerts & Messages
├── Buttons (5 variants)
├── Forms & Inputs
├── Tables
├── Sidebar Navigation
├── Dashboard Cards
├── Product Grid
├── Badges
├── Login Page
├── Home Page
├── Utilities
└── Responsive Design (3 breakpoints)
```

---

## Key Design Patterns

### MVC-style Architecture
- Models: Database queries in functions
- Views: PHP templates with HTML
- Controllers: Page logic and routing

### Security Pattern
- All queries use prepared statements
- All outputs sanitized with htmlspecialchars()
- Sessions managed safely
- File uploads validated

### Responsive Design
- Mobile-first CSS
- Flexbox for navigation
- CSS Grid for layouts
- Media queries for tablets & phones

---

## Database Relationships

```
users (1) ──────→ (many) purchase_requests
                        └──→ (1) products
```

---

## Access Control Matrix

| Page | Admin | Seller | Guest |
|------|-------|--------|-------|
| index.php | ✓ | ✓ | ✓ |
| login.php | ✓ | ✓ | ✓ |
| admin/* | ✓ | ✗ | ✗ |
| seller/* | ✗ | ✓ | ✗ |

---

## Environment Setup

```
Project: ELO Supply Chain
Language: PHP 7.4+
Database: MySQL 5.7+
Server: Apache (XAMPP)
Location: /opt/lampp/htdocs/ELO-Supply-Chain/
URL: http://localhost/ELO-Supply-Chain/
```

---

For detailed information about each file, check:
- **README.md** - Project overview
- **INSTALLATION.md** - Setup instructions
- **QUICKSTART.md** - Getting started
- **BUILD_SUMMARY.md** - Build details
