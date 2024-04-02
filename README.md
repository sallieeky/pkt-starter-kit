<p align="center"><img src="/art/logo-long.png" alt="Logo Pupuk Kaltim"></p>

<p align="center">
    <a href="https://packagist.org/packages/pkt/starter-kit">
        <img src="https://img.shields.io/packagist/dt/pkt/starter-kit" alt="Total Downloads">
    </a>
    <a href="https://packagist.org/packages/pkt/starter-kit">
        <img src="https://img.shields.io/packagist/v/pkt/starter-kit" alt="Latest Stable Version">
    </a>
    <a href="https://github.com/sallieeky/pkt-starter-kit/pulse" alt="Activity">
        <img src="https://img.shields.io/github/commit-activity/m/sallieeky/pkt-starter-kit" />
    </a>
    <a href="https://packagist.org/packages/pkt/starter-kit">
        <img src="https://img.shields.io/packagist/l/pkt/starter-kit" alt="License">
    </a>
</p>

# PKT Starter Kit

Welcome to the Pupuk Kaltim Laravel Starter Kit – your gateway to streamlined Laravel development imbued with the distinctive essence of Pupuk Kaltim's expertise. Crafted meticulously to align with the industry-leading practices and standards, this plugin/library serves as your companion in building robust Laravel applications infused with the hallmark quality of Pupuk Kaltim.

## Installation

For stable package install using <strong>(RECOMMENDED)</strong>
```bash
composer require pkt/starter-kit --dev 
```

For development this package install using
```bash
composer require pkt/starter-kit:dev-dev --dev 
```


## How To Use

Using vue
```bash
php artisan pkt:install vue
```

Using react <strong>(NOT READY)</strong>
```bash
php artisan pkt:install react
```

## Full Installation/Setup Project

1.  Installing Laravel
```bash
composer create-project laravel/laravel:^10.0 <project-name>
```

2.  Move to Project Directory
```bash
cd <project-name>
```

3.  Include Starter Kit to Project
```bash
composer require pkt/starter-kit --dev 
```

4.  Install Starter Kit

```bash
php artisan pkt:install vue

#or

php artisan pkt:install react
```

5.  Setup/Edit environment variable (.env)
```bash
.env
```

6.  Run Migration and Seeder
```bash
php artisan migrate:fresh --seed
```

7.  Run project
```bash
# Frontend
npm run dev

#backend
php artisan serve
```


## Included Library
<p align="left">
    <a href="https://js.devexpress.com/">
        <img src="/art/DevExtreme.png" alt="Dev Extreme" width="200">
    </a>
    <a href="https://github.com/laravel/breeze">
        <img src="/art/LaravelBreeze.png" alt="Laravel Breeze" width="200">
    </a>
    <a href="https://github.com/spatie/laravel-permission">
        <img src="/art/SpatieRolePermission.png" alt="Spatie Role and Permission" width="200">
    </a>
    <a href="https://element-plus.org/en-US/">
        <img src="/art/ElementPlus.png" alt="Element Plus" width="200">
    </a>
    <a href="https://www.amcharts.com/docs/v5/">
        <img src="/art/Amcharts.png" alt="Amcharts" width="200">
    </a>
    <a href="https://tailwindcss.com/">
        <img src="/art/TailwindCSS.png" alt="Tailwind CSS" width="200">
    </a>
</p>

## Authors

- [@sallieeky](https://www.github.com/sallieeky)
- [@ardmgtm](https://www.github.com/ardmgtm)
- [@sallixie](https://www.github.com/sallixie)
