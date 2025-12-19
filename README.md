# Multi Purpose Booking System

A comprehensive and flexible web application designed for managing resource and facility bookings. This system allows users to browse available spaces or resources, book time slots, manage their appointments, and access invoices. It features a powerful admin panel built with Filament for managing the entire platform.

![Laravel](https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3.6-4e56a6?style=for-the-badge&logo=livewire&logoColor=white)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-3.0-F28D15?style=for-the-badge&logo=filament&logoColor=white)

## Features

### Front-End (User Application)
-   **Resource Discovery**: Browse a list of available spaces, rooms, or resources with detailed descriptions and images.
-   **Booking Engine**: 
    -   Interactive calendar and time slot selection.
    -   Support for different slot durations (minutes or hours).
    -   Add-on services selection during booking.
-   **User Authentication**: Secure login and registration with phone verification.
-   **Dashboard**: Manage your bookings, view history, and update profile information.
-   **Invoicing**: Auto-generated PDF invoices for bookings (`laravel-dompdf`), available for view and download.
-   **Responsive Design**: Fully responsive UI built with Tailwind CSS, supporting Dark Mode.
-   **Contact & Static Pages**: Integrated contact form and dynamic content pages managed via the admin panel.

### Back-End (Admin Panel)
-   **Powered by Filament**: A robust and user-friendly admin interface.
-   **Dashboard**: Key metrics and overview of bookings and revenue.
-   **Resource Management**: Create and edit listings (Studios, Rooms, Courts, etc.), including pricing and availability.
-   **Booking Management**: View, approve, or cancel bookings.
-   **User Management**: Manage registered users and their details.
-   **Content Management**: Manage dynamic pages and site settings.
-   **Financials**: Track payments and generate reports/invoices.

## Tech Stack

-   **Backend**: Laravel 12.0
-   **Frontend**: Livewire 3.6, Blade Templates
-   **Styling**: Tailwind CSS 4.0
-   **Admin Panel**: Filament 3.0
-   **Build Tool**: Vite 7.0
-   **Database**: MySQL / SQLite (configurable)
-   **PDF Generation**: barryvdh/laravel-dompdf

## Requirements

-   PHP >= 8.2
-   Composer
-   Node.js & NPM
-   MySQL (or SQLite)

## Installation

Follow these steps to set up the project locally:

1.  **Clone the repository**
    ```bash
    git clone <repository-url>
    cd studio-booking
    ```

2.  **Install PHP dependencies**
    ```bash
    composer install
    ```

3.  **Install Node.js dependencies**
    ```bash
    npm install
    ```

4.  **Environment Setup**
    Copy the example environment file and configure your database settings:
    ```bash
    cp .env.example .env
    ```
    Open `.env` and set your database credentials (`DB_DATABASE`, `DB_USERNAME`, etc.).

5.  **Generate Application Key**
    ```bash
    php artisan key:generate
    ```

6.  **Run Migrations & Seed Database**
    Create the database tables and populate them with initial data:
    ```bash
    php artisan migrate --seed
    ```

7.  **Link Storage**
    Ensure uploaded files are accessible publicly:
    ```bash
    php artisan storage:link
    ```

8.  **Build Assets**
    Compile the frontend assets:
    ```bash
    npm run build
    ```
    *Or for development:*
    ```bash
    npm run dev
    ```

9.  **Serve Application**
    Start the local development server:
    ```bash
    php artisan serve
    ```

    The application will be accessible at `http://127.0.0.1:8000`.

## Usage

### User Portal
Visit the home page at `http://127.0.0.1:8000` to browse available resources. 
-   **Register/Login** to start booking.
-   Navigate to **"My Bookings"** to see your past and upcoming reservations.

### Admin Panel
Access the admin panel at `http://127.0.0.1:8000/admin`.
-   **Default Login** (if seeded):
    -   Email: `admin@example.com` (Check `DatabaseSeeder.php` for exact credentials if different)
    -   Password: `password`

## Project Structure

-   `app/Livewire`: Contains the frontend logic components (Booking flow, Listings, Auth).
-   `app/Filament`: Contains the Admin Panel resources and configuration.
-   `app/Models`: Eloquent models (Studio, Booking, User, Invoice, etc.).
-   `resources/views`: Blade templates.
-   `routes/web.php`: Web routes definition.

## Contributing

1.  Fork the repository.
2.  Create a new feature branch (`git checkout -b feature/amazing-feature`).
3.  Commit your changes (`git commit -m 'Add some amazing feature'`).
4.  Push to the branch (`git push origin feature/amazing-feature`).
5.  Open a Pull Request.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT). This project follows the same license.
