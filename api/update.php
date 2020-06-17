<?php
    /**
     * Created by PhpStorm.
     * User: user
     * Date: 17.06.2020
     * Time: 16:56
     */
    namespace api;
    include '../includes.php';

    $core = new Core();
    $auth = $core->checkAuth();
    echo $auth[1] . PHP_EOL;
    if ($core->checkAuth()[0]) {
        $core->updateData();
    }