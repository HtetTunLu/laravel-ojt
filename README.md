Environment

```zsh
- Laravel 10 
- Laravel-admin 1.8
- MySQL 8.0.20
```

Run commands

```zsh
composer require encore/laravel-admin:1.*

php artisan vendor:publish --provider="Encore\Admin\AdminServiceProvider"

php artisan admin:install

php artisan migrate

php artisan db:seed --class=AdminMenuSeeder

- Open http://localhost/admin/ in browser,use username admin and password admin to login.

```

Change Custom Files

```zsh
- Replace all files inside of requirements folder

    - requirements/AdminController.php -> Vendor/encore/laravel-admin/src/Controllers/AdminController.php

    - requirements/Builder.php -> Vendor/encore/laravel-admin/src/Form/Builder.php

    - requirements/Form.php -> Vendor/encore/laravel-admin/src/Form.php

    - requirements/SoftDeletes.php -> Vendor/laravel/framework/src/Illuminate/Database/Eloquent/SoftDeletes.php

    - requirements/UserController.php -> Vendor/encore/laravel-admin/src/Controllers/UserController.php

```
