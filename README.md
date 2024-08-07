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

<img src="/art/Home.png" alt="Home">

## Installation

For stable package install using this command to get the latest version. **(RECOMMENDED)**
```bash
composer require pkt/starter-kit --dev 
```

For earlier package install using this command to get the specific version.
```bash
composer require pkt/starter-kit:version --dev 

# Example

composer require pkt/starter-kit:v2.5.1 --dev 
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

# or

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

# Backend
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

### Scheduling using cron job

By default there isn't job that register, you need to register it manually for your specific job. (**RECOMMENDED**) you can register a job from laravel scheduller.

```cmd
crontab -e
* * * * * cd /var/www/html && php artisan schedule:run >> /dev/null 2>&1
```

To see if it's already registered or see all registered cron job, you can use this command
```cmd
crontab -l
``` 

To register your laravel schedular, you can add your action or command on `app/Console/Kernel.php`, for example.


```php
use Pkt\StarterKit\Actions\DeleteUnusedMedia;

/**
 * Define the application's command schedule.
 */
protected function schedule(Schedule $schedule): void
{
    ...

    $schedule->call(new DeleteUnusedMedia)->monthlyOn(1, '00:00');
    
    # Or

    $schedule->command('backup:clean')->daily()->at('01:00');
    
    ...
}
```

## Testing
You can find test case in `tests` folder on your base project directory. This starter kit already setup for testing using [Pest](https://pestphp.com/).

Run test case using this command
```cmd
./vendor/bin/pest

#or

php artisan test
```

or for specific file only
```cmd
./vendor/bin/pest tests/Feature/ExampleTest.php
```

## Developing This Package

For developing this package install using.
```bash
composer require pkt/starter-kit:dev-dev --dev 
```

If you wanna fix or adding something on specific version, you need to make a branch from the existing version using standard branch name. For the better version development, you need to make 2 branch from existing version such as `version-main` and `version-dev`. Make `version-main` branch from existing version, then make `version-dev` from `version-main`.
```bash
git checkout -b version-main version
git checkout -b version-dev

# Example

git checkout -b v2.4.1-main v2.4.1
git checkout -b v2.4.1-dev
```

Then you can develop a new feature or fixing bug from the `version-dev` branch then you can merge to `version-main` then you can make a new tag from `version-main` branch. In that example, the new version should be `v2.4.1.1` to indicate that `v2.4.1` have a new update, so the application that installed with version `v2.4.1` can update a new version safely by using command.

```bash
composer update pkt/starter-kit:v2.4.1.1
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
    <a href="https://github.com/laravel/echo">
        <img src="/art/LaravelEcho.svg" alt="Laravel Echo" width="200">
    </a>
    <a href="https://pestphp.com/">
        <img src="/art/Pest.jpeg" alt="PEST" width="200">
    </a>
    <a href="https://docs.docker.com/">
        <img src="/art/Docker.png" alt="Docker" width="200">
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
    <a href="https://inertiajs.com/">
        <img src="/art/Intertiajs.png" alt="InertiaJS" width="200">
    </a>
    <a href="https://inertiajs.com/">
        <img src="/art/VueJs.png" alt="VueJS" width="200" height="100">
    </a>
</p>

## Authors

<a href="https://github.com/sallieeky/pkt-starter-kit/graphs/contributors">
  <img src="https://contrib.rocks/image?repo=sallieeky/pkt-starter-kit" />
</a>