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
## Populate Sample Data

1. seed the database
- After setting up the project, you can populate the database with sample data for testing:
    ```bash
    php artisan db:seed
    ```
2. Optional: Reset database and seed from scratch
- This drops all tables, re-runs migrations, and seeds fresh sample data.
   ```bash
   php artisan migrate:fresh --seed
    ```

## Database Schema

### Posts

- post_id, name, status, created_at, updated_at

### Visitors

- visitor_id, name, email, mobileno, status, created_at, updated_at

### Officers

- officer_id, post_id, name, status, created_at, updated_at, WorkStartTime, WorkEndTime

### Appointments

- appointment_id, visitor_id, officer_id, date, StartTime, EndTime, AddedOn, LastUpdatedOn, created_at, updated_at

### Activities

- activities_id, officer_id, appointment_id, type, start_date, start_time, end_time, end_date, status, created_at, updated_at
