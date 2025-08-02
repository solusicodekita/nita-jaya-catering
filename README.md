## INSTALASI LARAVEL FROM GITHUB

- <code>git@github.com:solusicodekita/nita-jaya-catering.git</code>(from url github)
- <code>cd nita-jaya-catering</code>(your name project)
- <code>composer install</code> or <code>composer update</code>
- <code>cp .env.example .env</code> configuration your file .env in your project Laravel
- <code>php artisan key:generate</code>
- <code>php artisan migrate</code> or <code>php artisan migrate --seed</code> or <code>php artisan migrate:refresh --seed</code>
- <code>php artisan db:seed --class=(Name Seeder)</code>
