## Food Delivery Web Application (PHP + MySQL)

### Requirements
- XAMPP (Apache + MySQL)
- PHP 8+ recommended

### Installation (XAMPP)
1. Copy the `food_delivery` folder into your XAMPP `htdocs` directory.
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin (`http://localhost/phpmyadmin`).
4. Import SQL:
   - Click Import and select `food_delivery/sql/food_delivery.sql`.
5. Configure DB (if needed):
   - Edit `food_delivery/config/db.php` to match your MySQL credentials.
6. Visit the app:
   - `http://localhost/food_delivery/`

### Default Accounts
- Admin: `admin@food.local` / `admin123`
- Customer: `john@example.com` / `customer123`
- Delivery: `rider1@food.local` / `delivery123`
- Restaurants: `pizza@food.local` / `rest123`, `burger@food.local` / `rest123` (approved)

### Modules
- Customer: register/login, browse restaurants, add to cart, checkout (COD), track orders.
- Delivery: login, assigned orders, update status, history.
- Restaurant: login/register, manage menu, manage orders, assign delivery.
- Admin: login, approve restaurants, manage orders, view users.

### Notes
- Uses Bootstrap 5 and Tailwind via CDN.
- Passwords are hashed (`password_hash`).
- Sessions manage authentication. Ensure cookies are enabled.

