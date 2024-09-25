# Advert Marketplace Website

A CRUD (Create, Read, Update, Delete) Marketplace website developed using PHP and the Symfony framework. This application allows users to create, manage, and view adverts in a secure and user-friendly environment.

## Features

- User Registration and Authentication
- Create, Edit, and Delete Adverts
- View Adverts (for both registered and non-registered users)
- Admin Dashboard for user and advert management
- Moderator capabilities for advert moderation
- Category Management
- Search and Filter Adverts
- Pagination for advert listings

## Technologies Used

- PHP
- Symfony Framework
- Doctrine ORM
- Twig Template Engine
- MySQL Database
- EasyAdmin Bundle
- KnpPaginator Bundle
- Bootstrap CSS and JS
- Symfony Security Bundle

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/praiseOjay/Marketplace_website.git
   ```

2. Install dependencies:
   ```
   composer install
   ```

3. Configure the database in the `.env` file:
   ```
   DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name
   ```

4. Create the database and run migrations:
   ```
   php bin/console doctrine:database:create
   php bin/console doctrine:migrations:migrate
   ```

5. Start the Symfony development server:
   ```
   symfony server:start
   ```

## Usage

- Visit `http://localhost:8000` in your web browser to access the application
- Register a new account or log in with existing credentials
- Create, view, edit, or delete adverts based on your user role
- Access the admin dashboard at `/admin` (requires admin or moderator role)

## User Roles

- **User**: Can create, edit, and delete their own adverts
- **Moderator**: Can edit, delete, or publish any user's advert
- **Admin**: Full access to user management and all advert operations
- **Super Admin**: Highest level of access, can promote users to other roles

## Security

- Form login authentication
- CSRF protection
- Role-based access control
- Password hashing

## Contributing

Contributions to improve the application are welcome. Please feel free to submit issues or pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details.

## Contact

Praise Ojerinola - Ojerinolapraise@gmail.com

Project Link: https://github.com/praiseOjay/Marketplace_website.git
