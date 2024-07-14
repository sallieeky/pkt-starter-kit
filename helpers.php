<?php

if (!function_exists('check_all_version')) {
    /**
     * Check all version of laravel, php, and composer
     *
     * @return array
     */
    function check_all_version()
    {
        $laravel = shell_exec('php artisan --version');
        $php = shell_exec('php -v');
        $composer = shell_exec('composer -V');

        return [
            'laravel' => $laravel,
            'php' => $php,
            'composer' => $composer,
        ];
    }
}
