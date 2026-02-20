# ELO Supply Chain

## Private B2B Inventory & Seller Management System

---

## 1. Project Overview

**ELO Supply Chain** is a private B2B inventory and distribution management platform built using PHP and MySQL.

This system is **NOT** a public e-commerce website.

It is a centralized admin-controlled dashboard where approved sellers can:

* View available products
* View base prices
* Request/purchase stock
* Track their purchase history

Sellers will sell products externally (e.g., Facebook, local markets, e-commerce platforms).
The platform only manages inventory, pricing control, and seller access.

---

## 2. Business Model

### Core Concept

* Admin imports products (e.g., from China).
* Admin sets a fixed **Base Price** (minimum guaranteed price).
* Sellers log in and view available stock.
* Sellers sell products externally at any price above the base price.
* Sellers request/purchase stock from the system.
* Stock is reduced after admin confirmation.

The platform does NOT:

* Handle end customers
* Process public orders
* Provide public registration
* Include payment gateways (optional future feature)

---

## 3. User Roles

### 3.1 Admin (Super Admin)

Full system control.

Permissions:

* Secure login
* Create, update, delete products
* Upload product images
* Set base price per product
* Manage stock quantity
* Approve or reject seller accounts
* View seller purchase history
* View reports (stock movement, seller activity)
* Confirm or reject seller purchase requests

---

### 3.2 Seller (Approved Only)

Limited access.

Permissions:

* Secure login
* View product list
* View product details (name, description, base price, stock)
* Submit purchase request
* View own purchase history

Restrictions:

* Cannot change base price
* Cannot modify product details
* Cannot access other sellers’ data
* Cannot register without admin approval

---

## 4. Functional Requirements

### 4.1 Authentication System

* Role-based login (Admin / Seller)
* Password hashing (bcrypt)
* Session-based authentication
* Access control middleware
* No public sign-up

---

### 4.2 Product Management (Admin)

Admin can:

* Add new product
* Edit product
* Delete product
* Upload product image
* Update stock quantity
* Set base price

Each product must contain:

* Product ID
* Name
* Description
* Base Price
* Stock Quantity
* Image
* Created Date

---

### 4.3 Seller Approval Workflow

1. Seller account is created (manually or request-based).
2. Default status = Pending.
3. Admin reviews and:

   * Approves → Seller can login.
   * Rejects → Seller cannot access system.

---

### 4.4 Purchase Request System

Seller submits:

* Product
* Quantity

Admin:

* Reviews request
* Approves or rejects

If approved:

* Stock decreases automatically
* Purchase record is stored

---

### 4.5 Stock Management Logic

Rules:

* Stock cannot go below zero.
* Purchase cannot exceed available stock.
* Stock reduces only after admin approval.
* Every stock movement must be recorded.

---

### 4.6 Reporting Module (Admin)

Admin can view:

* Total products
* Total sellers
* Total stock value
* Seller purchase history
* Product-wise movement report

---

## 5. Non-Functional Requirements

* Clean architecture (MVC preferred)
* Secure coding practices
* Prepared statements (PDO or MySQLi)
* Proper database normalization
* Scalable structure
* Responsive dashboard UI
* Secure file upload handling

---

## 6. Technical Stack

Backend:

* PHP (Core PHP or MVC structure)

Database:

* MySQL

Frontend:

* HTML5
* CSS3
* Basic JS (optional)

Security:

* Password hashing (bcrypt)
* Prepared statements
* Session protection
* Role-based access control

---

## 7. Database Schema (Initial Design)

### users

| Field      | Type                                  |
| ---------- | ------------------------------------- |
| id         | INT (PK)                              |
| name       | VARCHAR                               |
| email      | VARCHAR (unique)                      |
| password   | VARCHAR                               |
| role       | ENUM('admin','seller')                |
| status     | ENUM('pending','approved','rejected') |
| created_at | DATETIME                              |

---

### products

| Field          | Type     |
| -------------- | -------- |
| id             | INT (PK) |
| name           | VARCHAR  |
| description    | TEXT     |
| base_price     | DECIMAL  |
| stock_quantity | INT      |
| image          | VARCHAR  |
| created_at     | DATETIME |

---

### purchase_requests

| Field      | Type                                  |
| ---------- | ------------------------------------- |
| id         | INT (PK)                              |
| seller_id  | INT (FK users.id)                     |
| product_id | INT (FK products.id)                  |
| quantity   | INT                                   |
| status     | ENUM('pending','approved','rejected') |
| created_at | DATETIME                              |

---

## 8. Folder Structure (Suggested)

```
/elo-supply-chain
│
├── /config
├── /includes
├── /admin
├── /seller
├── /uploads
├── /assets
├── index.php
├── login.php
└── logout.php
```

---

## 9. Future Expansion Possibilities

* Multi-warehouse support
* Seller balance/credit system
* Commission tracking
* REST API for mobile app
* Export reports (CSV/PDF)
* Role-based multi-admin system
* Activity logs

---

## 10. System Philosophy

ELO Supply Chain is designed as a **centralized distribution control system**.

It focuses on:

* Inventory accuracy
* Margin protection
* Seller control
* Structured growth

It is not an online shop.
It is infrastructure for scalable distribution.
