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

8.  For other development guide see [GUIDE.md](GUIDE.md)

## Configuration

Before you use between ldap or portal, first you need to have production server and ask to <strong>IT Infra</strong> to open your application host and port to get authorize to use ldap or portal

### Ldap
To enable ldap functionality, first you need to add this to the `.env` file and ask <strong>admin</strong> for the credential
```env
LDAP_ENABLE=<true|false>
LDAP_HOST=<ask admin>
LDAP_PORT=<ask admin>
LDAP_DN=<ask admin>
LDAP_PASS=<ask admin>
LDAP_TREE=<ask admin>
```

### Portal PKT
To enable portal functionality, first you need to add this to the `.env` file and ask <strong>admin</strong> for the credential
```env
ENABLE_SSO=<true|false>
DISABLE_INTERNAL_LOGIN=<true|false>
SSO_FULL_FEATURE=<true|false>
APPLICATION_NAME=pkt_starter_kit (your app name)
PORTAL_URL=https://aplikasi.pupukkaltim.com/
PORTAL_URL_LOGIN=https://aplikasi.pupukkaltim.com/login
PORTAL_URL_LOGOUT=https://aplikasi.pupukkaltim.com/logout
SSO_AUTH_TOKEN=<ask admin>
API_KEY_PORTAL=<ask admin>
AUTHORIZATION_TOKEN_PORTAL=<ask admin>
```

## Using Docker For Production

Configure image, container name, and exposed port in `docker-compose.yml` file
```yml
# version: '1.0'
services:
  app:
    build:
      context: .
      dockerfile: .docker/php/Dockerfile
    image: pkt-starter-kit (your app name)
    container_name: pkt-starter-kit (your app name)
    restart: always
    ports:
      - 44080:80 (change 44080 to any open port you want)
      - 44443:443 (change 44443 to any open port you want)
    volumes:
      - .:/var/www/html
```

1. Build docker and run
   ```cmd
   docker-compose up -d --build
   ```
2. To enter docker container
   ```cmd
   docker exec -it <container_name> bash
   ```
### SSL (HTTPS) 
If SSL is disabled, enable SSL using
```cmd
a2enmod ssl
```

### Worker using supervisor
Available supervisor worker located in `/etc/supervisor/conf.d/laravel-worker.conf`
```conf
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/html/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=8
redirect_stderr=true
stdout_logfile=/var/logs/supervisor/laravel-worker.log
stopwaitsecs=3600
```

By default supervisor service is disabled in docker so you need to start.
```cmd
service supervisor start
supervisorctl status
```

Or if you change the worker you need to reread and update supervisor service
```cmd
supervisorctl reread
supervisorctl update
```

Or if you want to stop supervisor service
```cmd
service supervisor stop
```


## Included Library
<p align="left">
    <a href="https://js.devexpress.com/">
        <img src="/art/DevExtreme.png" alt="Dev Extreme" width="200">
    </a>
    <a href="https://github.com/laravel/breeze">
        <img src="/art/LaravelBreeze.png" alt="Laravel Breeze" width="200">
    </a>
    <a href="https://reverb.laravel.com/">
        <img src="/art/LaravelReverb.png" alt="Laravel Reverb" width="200">
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

<a href="https://github.com/sallieeky/pkt-starter-kit/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=sallieeky/pkt-starter-kit" />
</a>