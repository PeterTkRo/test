<?php
    /**
     * Created by PhpStorm.
     * User: user
     * Date: 17.06.2020
     * Time: 16:05
     */
    namespace api;

    include '../includes.php';

    $core = new Core();
    $auth = $core->checkAuth();
    $returnData['Auth'] =  $auth[1];
    // todo get id
    if ($core->checkAuth()[0]) {
        $returnData['Currency'] = $core->getAllCurrencies();
    }
    echo json_encode($returnData);