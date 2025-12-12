# Billing Management System

A comprehensive GST-compliant billing management system built with Laravel 12 and Vuetify 3.

## Features

- **User Authentication**: Secure registration and login with Laravel Sanctum
- **Party Management**: Add and manage customer/supplier details with GSTIN validation
- **Product Management**: Maintain product catalog with HSN codes and GST rates
- **GST Bill Generation**: Create GST-compliant invoices with automatic tax calculations
- **E-Way Bill Generation**: Generate e-way bills for goods transportation (required for invoices > Rs. 50,000)
- **PDF Export**: Download professional PDF invoices and e-way bills
- **Dashboard**: View business statistics and recent activity
- **Settings**: Configure business details, bill preferences, and bank information

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Vue 3 + Vuetify 3
- **Build Tool**: Vite
- **Database**: SQLite (default) / MySQL / PostgreSQL
- **PDF Generation**: DomPDF
- **Authentication**: Laravel Sanctum

## Requirements

- PHP 8.2 or higher
- Composer
- Node.js 18 or higher
- npm or yarn

## Installation

### 1. Clone the repository

```bash
cd /path/to/your/webserver
git clone <repository-url> billing-software
cd billing-software
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node.js dependencies

```bash
npm install
```

### 4. Environment Configuration

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure Database

Edit `.env` file to configure your database:

**For SQLite (default):**
```env
DB_CONNECTION=sqlite
```

**For MySQL:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=billing_software
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 6. Run Migrations

```bash
php artisan migrate
```

### 7. Build Frontend Assets

**For Development:**
```bash
npm run dev
```

**For Production:**
```bash
npm run build
```

### 8. Start the Application

**Using Laravel's built-in server:**
```bash
php artisan serve
```

The application will be available at `http://localhost:8000`

**Using XAMPP/Apache:**
Configure your virtual host to point to the `public` directory.

## Project Structure

```
billing-software/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/           # API Controllers
│   ├── Models/                # Eloquent Models
│   └── Policies/              # Authorization Policies
├── database/
│   └── migrations/            # Database Migrations
├── resources/
│   ├── js/
│   │   ├── components/        # Vue Components
│   │   ├── plugins/           # Vuetify Configuration
│   │   ├── router/            # Vue Router
│   │   ├── stores/            # Pinia Stores
│   │   ├── views/             # Vue Views
│   │   └── App.vue            # Main App Component
│   └── views/
│       ├── app.blade.php      # Main Blade Template
│       └── pdf/               # PDF Templates
├── routes/
│   ├── api.php                # API Routes
│   └── web.php                # Web Routes
└── ...
```

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login user
- `POST /api/logout` - Logout user
- `GET /api/user` - Get current user

### Parties
- `GET /api/parties` - List parties (paginated)
- `GET /api/parties/all` - List all parties
- `POST /api/parties` - Create party
- `GET /api/parties/{id}` - Get party
- `PUT /api/parties/{id}` - Update party
- `DELETE /api/parties/{id}` - Delete party

### Products
- `GET /api/products` - List products (paginated)
- `GET /api/products/all` - List all products
- `POST /api/products` - Create product
- `GET /api/products/{id}` - Get product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

### Bills
- `GET /api/bills` - List bills (paginated)
- `GET /api/bills/stats` - Get bill statistics
- `POST /api/bills` - Create bill
- `GET /api/bills/{id}` - Get bill
- `PUT /api/bills/{id}` - Update bill
- `DELETE /api/bills/{id}` - Delete bill
- `GET /api/bills/{id}/pdf` - Download bill PDF
- `POST /api/bills/{id}/eway-bill` - Generate e-way bill

### E-Way Bills
- `GET /api/eway-bills` - List e-way bills
- `GET /api/eway-bills/{id}` - Get e-way bill
- `GET /api/eway-bills/{id}/pdf` - Download e-way bill PDF

### Settings
- `GET /api/settings` - Get settings
- `PUT /api/settings/business` - Update business settings
- `PUT /api/settings/account` - Update account settings
- `PUT /api/settings/password` - Change password
- `PUT /api/settings/bill` - Update bill settings

## GST Compliance

The system supports:
- **GST Rates**: 0%, 5%, 12%, 18%, 28%
- **HSN Codes**: 4-8 digit codes for products
- **GSTIN Validation**: 15-character format validation
- **Intra-State vs Inter-State**: Automatic CGST/SGST or IGST calculation
- **E-Way Bill**: Required for goods worth > Rs. 50,000

## Production Deployment

### 1. Configure Environment

```bash
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### 2. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
npm run build
```

### 3. Set Permissions

```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 4. Configure Web Server

**Apache (.htaccess in public/):**
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

**Nginx:**
```nginx
location / {
    try_files $uri $uri/ /index.php?$query_string;
}
```

## Testing

Run the test suite:

```bash
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues and feature requests, please open an issue on the repository.
