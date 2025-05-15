# QuickBuy PHP Backend

This is the backend API for the QuickBuy e-commerce platform. It provides RESTful endpoints for authentication, product management, cart operations, order processing, and admin functions.

## Features

### Authentication & User Management
- Secure login for customers, sellers, and admins
- User registration for customers and sellers
- Logout functionality

### Customer Features
- Browse items
- Add/remove items from cart
- Place orders
- View order status
- View business pages
- View item details

### Seller Features
- Create, update, and delete items
- Browse orders for their items
- Add categories to items
- Comment on reviews
- View sales reports

### Admin Features
- Approve businesses
- Manage user accounts
- View and remove reported items
- Delete user and business accounts
- Create admin accounts with permissions

### Additional Features
- Public browsing for non-authenticated users

## Installation

1. Clone the repository
2. Configure your database settings in `config/database.php`
3. Import the database schema from `database/schema.sql`
4. Optionally seed the database with sample data using `php database/seed.php`
5. Configure your web server to point to the `public` directory

## API Documentation

### Authentication Endpoints
- `POST /api/auth/login` - Login
- `POST /api/auth/register` - Register
- `POST /api/auth/logout` - Logout

### Customer Endpoints
- `GET /api/items` - Browse items
- `GET /api/items/{id}` - View item details
- `GET /api/categories` - Get categories
- `POST /api/cart/add` - Add item to cart
- `POST /api/cart/remove` - Remove item from cart
- `GET /api/cart` - View cart
- `POST /api/orders` - Place order
- `GET /api/orders` - View orders
- `GET /api/orders/{id}` - View order details

### Seller Endpoints
- `POST /api/seller/items` - Create item
- `PUT /api/seller/items/{id}` - Update item
- `DELETE /api/seller/items/{id}` - Delete item
- `GET /api/seller/orders` - View orders
- `POST /api/seller/reviews/{id}/comments` - Comment on review
- `GET /api/seller/reports/top-sellers` - View top sellers report

### Admin Endpoints
- `GET /api/admin/businesses/pending` - View pending businesses
- `POST /api/admin/businesses/{id}/approve` - Approve business
- `GET /api/admin/users` - View users
- `DELETE /api/admin/users/{id}` - Delete user
- `GET /api/admin/items/reported` - View reported items
- `DELETE /api/admin/items/{id}` - Delete item
- `POST /api/admin/admins` - Create admin

## Security

- JWT-based authentication
- Password hashing
- Role-based access control
- Input validation

## License

This project is licensed under the MIT License.
