# canadian-sme
# Event-App
# Laravel SetUp
composer install
php artisan migrate:fresh --seed
php artisan optimize:clear

# Run Model with Migration & Resource Controller together
php artisan make:model Post -m -r

# Run individual Seeder
php artisan db:seed --class=AdminSeeder
