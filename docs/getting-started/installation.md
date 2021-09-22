---
sidebar_position: 2
---

# Installation

## Requirements

* PHP >= 8.0
* Laravel >= 8.0
* Composer 2

## Install Evo

Open your terminal/cmd, move to your Laravel 8+ project directory, then run following command:

```bash
cd /path/to/your/laravel-8-app

composer require emsifa/evo
```

## Publishing Assets

If you want to use Swagger UI feature, you need to publish assets and config file by running these commands:

```bash
php artisan vendor:publish --tag evo-config   
php artisan vendor:publish --tag evo-assets   
```

If you want to modify Swagger UI view file, you need to publish view file by running command below:

```bash
php artisan vendor:publish --tag evo-views   
```