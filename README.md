# Coordinator Plugin

Plugin to manage supervisors on XGuard ERP

<br>

## Installation with composer

Use the following commands to install

```bash
composer require xguard/coordinator
php artisan migrate
php artisan vendor:publish --provider="Xguard\Coordinator\CoordinatorServiceProvider" --force
```
Use the following command to create an admin. It will prompt you for an existing email from users table.

```bash
php artisan coordinator:create-admin
```
You can now go to the **/coordinator** path to use the package. You must first login to access this url. 

<br>

## Develpment 

**Follow these steps to make modifications to the package**

**1:** Firstly, clone the xguard-coordinator repo inside your package folder at root level. 
Create a "package" folder if you don't have one.


**2:** Then, add line of code in the psr-4 of your root composer.json
```
"psr-4": {
    //...
    "Xguard\\Coordinator\\": "package/xguard-coordinator/src/"
},
```
**3:** Add the coordinator plugin service provider to the config/app.php

```php
return [
    //...
    "providers" => [
        //...
        Xguard\Coordinator\CoordinatorServiceProvider::class,
    ]
];

```


**4:** run this command
```bash
composer dump-autoload 
```

**5:** Navigate to the xguard-coordinator package folder in your command line and perform the following commands:
```bash
composer install
npm install
npm run dev
```

**6:** Return to the root folder in the command line and publish the package with the following command:
```bash
php artisan vendor:publish --provider="Xguard\Coordinator\CoordinatorServiceProvider" --force
```

**7:** To run package migrations
```bash
php artisan migrate --path=package/xguard-coordinator/src/database/migrations
```

**8:** To run seeder for testing
```bash
php artisan db:seed --class="Xguard\Coordinator\database\seeds\EmployeeSeeder"
```

<br>
