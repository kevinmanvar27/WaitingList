# WaitingList

WaitingList is a restaurant waiting list management system built with Laravel. It allows restaurant owners to manage their waiting customers digitally, track wait times, and provide a better customer experience. The system features both an admin panel for restaurant management and a mobile-friendly interface for customers.

## Features

### For Restaurant Owners
- **Admin Dashboard**: Comprehensive dashboard with statistics and analytics
- **Waiting List Management**: Add, remove, and manage customers on the waiting list
- **Customer Tracking**: Track customers by name, phone number, and party size
- **Status Management**: Mark customers as "waiting" or "dine-in"
- **Restaurant Information**: Manage restaurant details, operating hours, and contact information
- **Subscription Management**: Handle premium subscriptions and payment processing
- **Transaction History**: View all transactions and payment records
- **Settings Configuration**: Customize application settings and preferences

### For Customers
- **Digital Waiting List**: Join the waiting list without physical tickets
- **Real-time Updates**: Get real-time updates on wait times
- **Mobile-Friendly Interface**: Access the system from any mobile device
- **Google Authentication**: Easy login with Google account
- **OTP Verification**: Secure access with one-time password verification

### Technical Features
- **API-First Design**: RESTful API for mobile and web applications
- **Admin Panel**: Comprehensive admin interface for management
- **Database Management**: MySQL database with Eloquent ORM
- **Authentication**: Sanctum-based authentication with multiple methods
- **Payment Integration**: Razorpay integration for subscription payments
- **Documentation**: Swagger/OpenAPI documentation for API endpoints

## Tech Stack

- **Backend**: Laravel 12.x (PHP 8.2+)
- **Frontend**: Blade templates with JavaScript
- **Database**: MySQL
- **API Documentation**: Swagger UI via L5-Swagger
- **Authentication**: Laravel Sanctum
- **Payment Processing**: Razorpay
- **Social Login**: Google OAuth via Laravel Socialite

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/your-username/waitinglist.git
   cd waitinglist
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install Node dependencies** (if any):
   ```bash
   npm install
   ```

4. **Set up environment file**:
   ```bash
   cp .env.example .env
   ```

5. **Generate application key**:
   ```bash
   php artisan key:generate
   ```

6. **Configure your database** in the `.env` file:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=waitinglist
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

7. **Run database migrations and seeders**:
   ```bash
   php artisan migrate --seed
   ```

8. **Start the development server**:
   ```bash
   php artisan serve
   ```

## Usage

### Admin Panel
- Access the admin panel at `http://localhost:8000/admin`
- Default admin credentials (from seeder):
  - Email: `admin@example.com`
  - Password: `password`

### API Endpoints
- Base API URL: `http://localhost:8000/api`
- API Documentation: `http://localhost:8000/api/documentation`

### For Restaurant Owners
1. Log in to the admin panel
2. Create or manage your restaurant information
3. Monitor the waiting list in real-time
4. Update customer statuses as they are seated
5. View analytics and transaction history

### For Customers
1. Access the mobile-friendly interface
2. Join the waiting list by providing name and phone number
3. Receive updates on wait times
4. Get notified when table is ready

## Development

### Running Tests
```bash
php artisan test
```

### Code Quality
```bash
./vendor/bin/pint  # Code formatting
```

### API Documentation
The API is documented using Swagger. Access it at:
`http://localhost:8000/api/documentation`

To regenerate documentation:
```bash
php artisan l5-swagger:generate
```

## Project Structure

```
app/                 # Application logic
  ├── Http/          # Controllers, middleware, requests
  ├── Models/        # Eloquent models
  ├── Console/       # Artisan commands
  └── ...
database/            # Migrations and seeders
resources/           # Views, assets
routes/              # Web and API routes
tests/               # Test files
```

## Key Components

### Models
- `Restaurant`: Restaurant information and management
- `RestaurantUser`: Customers on the waiting list
- `User`: Admin and restaurant owner accounts
- `SubscriptionPlan`: Subscription plan definitions
- `UserSubscription`: User subscription records
- `Transaction`: Payment transaction records

### Controllers
- API Controllers for mobile app integration
- Web Controllers for admin panel functionality

### Commands
- `UpdateExpiredSubscriptions`: Updates expired subscriptions
- `UpdateWaitingCounts`: Updates restaurant waiting counts

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a pull request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Support

For support, please open an issue on the GitHub repository or contact the development team.