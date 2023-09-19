## About H.M.D. App Store (hmd-app-store)
This is a project that utilizes the [app-store-scraper](https://github.com/facundoolano/app-store-scraper) Node.js module to fetch and display data from the iTunes App Store.

## Installation
Prerequisites: 
- PHP | Composer | Node

### Clone the repository
git clone https://github.com/schoonover-aric/hmd-app-store.git

### Navigate to the project directory
cd to project directory

### Install dependencies (must have NPM and Composer installed)
npm install

composer install

## Database setup
This aplication uses a SQLite database. You'll need a 'database.sqlite' file in your root/database directory, but Laravel will create it for you if it doesn't exist. Run 'php artisan migrate' for database migrations.

## Configuration
Your .env file should contain two database entries ('DB_CONNECTION=sqlite' and 'DB_DATABASE=database.sqlite')

## Build & Run Instructions
I've created a build script (composer build) that runs composer install, npm install, npm run dev, and php artisan migrate.

I also added a start script (composer start) that you can use to run 'php artisan serve'.

__________________________________________________________________________________________________________________________


<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
