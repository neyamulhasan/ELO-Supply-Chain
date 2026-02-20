# ELO Supply Chain - Quick Start Guide

## 🚀 Get Started in 5 Minutes

### Step 1: Run Setup
Visit: `http://localhost/ELO-Supply-Chain/setup.php`

✓ Database created
✓ Tables initialized  
✓ Admin account created

### Step 2: Login as Admin
- **URL:** `http://localhost/ELO-Supply-Chain/login.php`
- **Email:** `admin@elo.com`
- **Password:** `admin123`

### Step 3: Add a Product
1. Click "Products" in sidebar
2. Click "Add New Product"
3. Fill in details:
   - Name: "Wireless Headphones"
   - Price: 45.99
   - Stock: 100
   - Upload image (optional)
4. Click "Save Product"

### Step 4: Create a Test Seller
Manually insert a seller into database or:
```sql
INSERT INTO users (name, email, password, role, status) 
VALUES ('Test Seller', 'seller@test.com', '$2y$10$...hashed_password...', 'seller', 'approved');
```

Or create seller through custom form (to be added later)

### Step 5: Test Seller Features
1. Logout
2. Login as seller with test account
3. Go to "Products" → Browse all products
4. Click "Request Product"
5. Enter quantity and submit

### Step 6: Approve Request (as Admin)
1. Login as admin
2. Go to "Purchase Requests"
3. Review pending request
4. Click "Approve"
5. Stock automatically decreases
6. Seller sees approved status in history

---

## 📊 Common Tasks

### Add More Sellers
**Option 1:** Create sellers manually via SQL
```sql
INSERT INTO users (name, email, password, role, status) 
VALUES ('Seller Name', 'seller@example.com', [hashed_password], 'seller', 'pending');
```

**Option 2:** Create seller registration form (extension)

Then approve in Admin → Manage Sellers

### Manage Stock
Admin → Products → Click "Edit" → Update "Stock Quantity"

### View Reports
Admin → Reports
- See product performance
- See seller purchasing history
- Track revenue and units sold

### Monitor Purchase Requests
Admin → Purchase Requests
- Filter by Pending/Approved/Rejected
- Approve to update stock
- Reject if issues

---

## 🎯 Key URLs

| Page | URL | User |
|------|-----|------|
| Home | /index.php | Anyone |
| Login | /login.php | Anyone |
| Admin Dashboard | /admin/dashboard.php | Admin |
| Admin Products | /admin/products.php | Admin |
| Admin Requests | /admin/purchase-requests.php | Admin |
| Admin Sellers | /admin/sellers.php | Admin |
| Admin Reports | /admin/reports.php | Admin |
| Seller Dashboard | /seller/dashboard.php | Seller |
| Seller Products | /seller/products.php | Seller |
| Seller History | /seller/purchase-history.php | Seller |

---

## ⚙️ Configuration

Edit `config/config.php`:

```php
// Database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'elo_supply_chain');

// Site
define('SITE_NAME', 'ELO Supply Chain');
define('SITE_URL', 'http://localhost/ELO-Supply-Chain/');

// Session timeout (1 hour)
define('SESSION_TIMEOUT', 3600);
```

---

## 🔒 Security Tips

1. **Change admin password immediately** after first login
2. **Use strong passwords** for all user accounts
3. **Backup database regularly**
4. **Validate file uploads** (images only)
5. **Use HTTPS** in production
6. **Never expose DB credentials** in code
7. **Implement CSRF tokens** for production

---

## 🐛 Testing Checklist

- [ ] Setup runs without errors
- [ ] Admin can login
- [ ] Can add/edit/delete products
- [ ] Can upload product images
- [ ] Can approve/reject sellers
- [ ] Seller can browse products
- [ ] Seller can submit purchase request
- [ ] Stock decreases on approval
- [ ] Reports show correct data
- [ ] Logout works
- [ ] Responsive on mobile

---

## 📝 Next Steps

1. **Create seller registration form** - Allow self-signup
2. **Add email notifications** - Notify sellers on approval
3. **Implement payment gateway** - Accept payments
4. **Add audit logs** - Track all actions
5. **Create REST API** - For mobile app
6. **Add search/filters** - Better product discovery

---

For full documentation, see **INSTALLATION.md** and **README.md**
