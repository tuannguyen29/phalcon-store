<?php

$router = $di->getRouter();

// Define your routes here
$router->add(
   'show/detail',
   [
        'controller' => 'Products',
        'action'     => 'show',
   ]
);

$router->add('get/detail/{param1}/?{param2}', array(
    'controller' => 'Products',
    'action'     => 'get',
    'lang'       => 1
));

$router->add(
   'admin',
   [
        'module'     => 'admin',
        'controller' => 'Admin',
        'action'     => 'admin',
   ]
);

$router->handle();
