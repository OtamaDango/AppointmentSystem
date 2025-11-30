# Appointment System

## Setup Instructions

1. Clone the repository:
    ```bash
    git clone https://github.com/OtamaDango/AppointmentSystem.git
    cd AppointmentSystem
    ```
2. Install dependencies:
    ```bash
    composer install
    ```
3. Copy .env and configure database credentials:
    ```bash
    cp .env.example .env
    ```
4. Generate application key:
    ```bash
    php artisan key:generate
    ```
5. Run migrations:
    ```bash
    php artisan migrate
    ```
6. Start the server:
   ```bash
   php artisan serve
   ```
## Database Schema

### Users

- id, name, email, password, created_at, updated_at

### Officers

- id, name, email, phone, created_at, updated_at

### Appointments

- id, user_id, officer_id, date, start_time, end_time, description, created_at, updated_at

### Activities

- id, appointment_id, activity, created_at, updated_at
