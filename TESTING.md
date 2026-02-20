# ELO Supply Chain - Testing & Verification Checklist

## 🧪 Complete Testing Checklist

---

## Phase 1: Setup & Installation

### Database Setup
- [ ] Visit `http://localhost/ELO-Supply-Chain/setup.php`
- [ ] Database created successfully
- [ ] Tables created (users, products, purchase_requests)
- [ ] Default admin account created
- [ ] No errors or warnings displayed

### Configuration
- [ ] `config/config.php` has correct DB credentials
- [ ] `SITE_URL` configured properly
- [ ] `UPLOADS_DIR` writable (chmod 755)

---

## Phase 2: Authentication Testing

### Admin Login
- [ ] Can login with admin@elo.com / admin123
- [ ] Redirects to admin/dashboard.php
- [ ] Session created properly
- [ ] Username displayed in navbar

### Logout
- [ ] Can click logout from any admin page
- [ ] Session destroyed
- [ ] Redirects to login page
- [ ] Cannot access admin pages without login

### Access Control
- [ ] Admin cannot access /seller/* pages directly
- [ ] Seller cannot access /admin/* pages
- [ ] Non-logged users redirected to login
- [ ] Changing URL directly triggers redirect

---

## Phase 3: Admin Dashboard Testing

### Dashboard Display
- [ ] Dashboard loads without errors
- [ ] Statistics cards display correctly
- [ ] Shows total products count
- [ ] Shows total sellers count
- [ ] Shows approved sellers count
- [ ] Shows pending sellers count
- [ ] Shows pending purchase requests count

### Sidebar Navigation
- [ ] All menu items visible
- [ ] "Dashboard" link works
- [ ] "Products" link works
- [ ] "Purchase Requests" link works
- [ ] "Manage Sellers" link works
- [ ] "Reports" link works
- [ ] Active menu item highlighted

---

## Phase 4: Product Management Testing

### Add Product
- [ ] Can navigate to Products → Add New Product
- [ ] Form displays all fields correctly
- [ ] Can enter product name
- [ ] Can enter description
- [ ] Can enter base price
- [ ] Can enter stock quantity
- [ ] Can upload product image (JPG, PNG, GIF)
- [ ] Image uploaded to /uploads/ directory
- [ ] Product saved to database
- [ ] Redirect back to product list with success message
- [ ] Image appears in product list

### Edit Product
- [ ] Can click Edit button on product
- [ ] Form pre-filled with product data
- [ ] Can update product name
- [ ] Can update description
- [ ] Can update base price
- [ ] Can update stock quantity
- [ ] Can replace product image
- [ ] Changes saved to database
- [ ] Redirect back to product list with success message

### Delete Product
- [ ] Can click Delete button on product
- [ ] Confirmation dialog appears
- [ ] Product removed from database
- [ ] Image file deleted from /uploads/
- [ ] Product list updated

### Product List Display
- [ ] All products displayed in table
- [ ] Shows product name
- [ ] Shows base price
- [ ] Shows stock quantity
- [ ] Shows stock value (price × quantity)
- [ ] Shows product image thumbnail
- [ ] Edit button present for each product
- [ ] Delete button present for each product

---

## Phase 5: Seller Management Testing

### Seller List
- [ ] Can navigate to Manage Sellers
- [ ] Displays all sellers
- [ ] Shows seller name
- [ ] Shows seller email
- [ ] Shows seller status (pending/approved/rejected)
- [ ] Shows purchase count for each seller
- [ ] Shows date seller joined

### Seller Approval
- [ ] Can filter sellers by status
- [ ] Pending sellers show Approve & Reject buttons
- [ ] Can click Approve on pending seller
- [ ] Seller status changes to "approved"
- [ ] Seller can now login

### Seller Rejection
- [ ] Can click Reject on pending seller
- [ ] Seller status changes to "rejected"
- [ ] Rejected seller cannot login

### Delete Seller
- [ ] Can delete seller account
- [ ] Seller's purchase requests removed (cascade delete)
- [ ] Seller cannot login after deletion

---

## Phase 6: Purchase Requests Testing

### Submit Request (as Seller)
- [ ] Login as approved seller
- [ ] Navigate to Products
- [ ] Browse product list
- [ ] See products with images and prices
- [ ] Click "Request Product" on available product
- [ ] Form shows product details
- [ ] Can enter quantity (1 to stock quantity)
- [ ] Real-time total calculation works
- [ ] Can submit request
- [ ] Redirect to purchase history with success message
- [ ] Request status shows "pending"

### View Purchase History
- [ ] Can see all own purchase requests
- [ ] Shows product name
- [ ] Shows quantity requested
- [ ] Shows base price
- [ ] Shows total cost
- [ ] Shows request status
- [ ] Shows request date

### Admin Review Requests
- [ ] Can navigate to Purchase Requests
- [ ] Shows pending requests
- [ ] Displays seller information
- [ ] Shows product details
- [ ] Shows quantity
- [ ] Shows request date
- [ ] Filter by status works (Pending/Approved/Rejected)

### Approve Request
- [ ] Admin can click Approve
- [ ] Request status changes to "approved"
- [ ] Stock quantity decreases by requested amount
- [ ] Product stock cannot go below zero
- [ ] Seller sees approved status in history

### Reject Request
- [ ] Admin can click Reject
- [ ] Request status changes to "rejected"
- [ ] Stock quantity unchanged
- [ ] Seller sees rejected status in history

---

## Phase 7: Reports Testing

### Product Report
- [ ] Can navigate to Reports
- [ ] Shows product performance table
- [ ] Lists all products
- [ ] Shows base price
- [ ] Shows current stock
- [ ] Shows total requests
- [ ] Shows units sold (from approved requests)
- [ ] Shows revenue calculation
- [ ] Data is accurate

### Seller Report
- [ ] Shows seller performance table
- [ ] Lists all sellers
- [ ] Shows seller status
- [ ] Shows total requests made
- [ ] Shows units purchased
- [ ] Shows total amount spent
- [ ] Calculations are accurate

---

## Phase 8: UI/UX Testing

### Responsive Design
- [ ] Page looks good on desktop (1920x1080)
- [ ] Page looks good on tablet (768px width)
- [ ] Page looks good on mobile (360px width)
- [ ] Sidebar collapses on mobile
- [ ] Navigation works on all devices
- [ ] Buttons are touchable on mobile (min 44px)
- [ ] Tables scroll horizontally on mobile

### Visual Elements
- [ ] Navigation bar displays properly
- [ ] Sidebar is visible and functional
- [ ] Form inputs are properly styled
- [ ] Buttons have hover effects
- [ ] Alerts display correctly
- [ ] Colors are consistent
- [ ] Fonts are readable
- [ ] Images load properly

### User Feedback
- [ ] Success messages appear after actions
- [ ] Error messages appear when needed
- [ ] Confirmation dialogs appear for dangerous actions
- [ ] Loading states visible if needed
- [ ] No JavaScript errors in console

---

## Phase 9: Security Testing

### Input Validation
- [ ] Cannot enter negative prices
- [ ] Cannot enter negative quantities
- [ ] Cannot upload non-image files
- [ ] Email validation works
- [ ] Special characters handled correctly

### SQL Injection
- [ ] Cannot inject SQL through forms
- [ ] All queries use prepared statements
- [ ] No database errors exposed to users

### File Upload
- [ ] Cannot upload .php or executable files
- [ ] Cannot upload oversized files
- [ ] Files saved with unique names
- [ ] Uploaded files accessible from web

### Session Security
- [ ] Session timeout works after inactivity
- [ ] Cannot access other user's data
- [ ] Cannot modify URL to bypass access control

---

## Phase 10: Database Testing

### Data Integrity
- [ ] Foreign key constraints work
- [ ] Cannot delete product with active requests (optional)
- [ ] Timestamps auto-update on modification
- [ ] Status fields only allow enum values
- [ ] Prices are decimal, not integer

### Data Accuracy
- [ ] Stock calculations are accurate
- [ ] Revenue calculations are correct
- [ ] Purchase request counts match
- [ ] Seller statistics are accurate

---

## Phase 11: Performance Testing

### Page Load Times
- [ ] Admin dashboard loads < 2 seconds
- [ ] Product list loads < 2 seconds
- [ ] Reports generate < 3 seconds
- [ ] Images load and display

### Database Queries
- [ ] Queries are optimized (no N+1)
- [ ] Database connections close properly
- [ ] No memory leaks apparent

---

## Phase 12: Cross-browser Testing

- [ ] Works in Chrome
- [ ] Works in Firefox
- [ ] Works in Safari
- [ ] Works in Edge
- [ ] Responsive on mobile browsers

---

## Test Scenarios

### Scenario 1: Complete Purchase Flow
1. ✓ Admin adds product with stock
2. ✓ Admin approves seller
3. ✓ Seller browses products
4. ✓ Seller requests product
5. ✓ Admin reviews request
6. ✓ Admin approves request
7. ✓ Stock decreases
8. ✓ Seller sees approved status
9. ✓ Admin sees updated stock

### Scenario 2: Stock Management
1. ✓ Add product with 100 units
2. ✓ Seller requests 30 units
3. ✓ Admin approves
4. ✓ Stock now shows 70
5. ✓ Another seller requests 50 units
6. ✓ Admin approves
7. ✓ Stock now shows 20
8. ✓ Another request for 25 units
9. ✓ Admin cannot approve (insufficient stock)
10. ✓ Stock remains at 20

### Scenario 3: Multiple Users
1. ✓ Create 3 sellers
2. ✓ Approve all sellers
3. ✓ All can login independently
4. ✓ Cannot see other seller's data
5. ✓ Admin can see all data

---

## Bug Tracking

| # | Issue | Status | Notes |
|---|-------|--------|-------|
| 1 | | | |
| 2 | | | |
| 3 | | | |

---

## Sign-off

- [ ] All tests passed
- [ ] No critical bugs
- [ ] No security issues
- [ ] Ready for production
- [ ] User documentation complete
- [ ] Installation guide complete

**Tested By:** ________________  
**Date:** ________________  
**Version:** 1.0  

---

## Known Issues / Future Improvements

1. Seller self-registration form (planned)
2. Email notifications (planned)
3. Payment processing (planned)
4. API endpoints (planned)
5. Audit logging (planned)

---

For any issues found, create a bug report with:
- Steps to reproduce
- Expected behavior
- Actual behavior
- Browser/OS information
- Screenshots if applicable
