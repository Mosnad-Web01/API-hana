<?php

 use Core\Database;
 use Core\Container;
 use Core\App;

// $container = new Container();
// $container->bind('Core\Database',  function () {
//     $config = require base_path('config.php');
//     return new Database($config);
// });
//
// App::setContainer($container);

$container = new Container();
App::setContainer($container);

// تسجيل خدمة Database باستخدام App::bind
App::bind('Core/Database', function () {
    $config = require base_path('config.php');
    return new Database($config);
});
