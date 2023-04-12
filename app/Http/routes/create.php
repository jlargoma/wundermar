<?php

///> php app/Http/routes/create.php

$fileResult = '<?php
    /*
    |--------------------------------------------------------------------------
    | Application Routes
    |--------------------------------------------------------------------------
    |
    | IMPORTANT...!!!
    | File created by console: > php app/Http/routes/create.php
    | The changes in this file will be removed
    | No change this file
    |
    */
    ?>';
$file = file_get_contents(dirname(__FILE__).'/routes_main.php');
$file2 = file_get_contents(dirname(__FILE__).'/routes-admin.php');
$file3 = file_get_contents(dirname(__FILE__).'/routes-superAdmin.php');
$file4 = file_get_contents(dirname(__FILE__).'/routes-api.php');


$fileResult .= $file.$file2.$file3.$file4;
$fileResult = str_replace('?><?php','',$fileResult);
//  include_once 'routes-forfaits.php';
//  include_once 'routes-admin.php';
//  include_once 'routes-superAdmin.php';

file_put_contents(dirname(dirname(__FILE__)).'/routes.php', $fileResult);


/* CONSOLE COMMANDS 
 
/opt/plesk/php/7.2/bin/php artisan cache:clear
/opt/plesk/php/7.2/bin/php artisan config:clear
/opt/plesk/php/7.2/bin/php artisan route:clear
/opt/plesk/php/7.2/bin/php artisan config:cache
/opt/plesk/php/7.2/bin/php artisan route:cache
/opt/plesk/php/7.2/bin/php artisan optimize
 * 
 * 
 /opt/plesk/php/7.2/bin/php artisan cache:clear
/opt/plesk/php/7.2/bin/php artisan config:cache
/opt/plesk/php/7.2/bin/php artisan route:cache
/opt/plesk/php/7.2/bin/php artisan optimize
  
 * 
 * 
php app/Http/routes/create.php
php artisan route:clear
 * 
php artisan config:clear

php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan optimize
 * 
 * 
 * 
php artisan route:clear
php artisan route:cache
 * 
 */
  