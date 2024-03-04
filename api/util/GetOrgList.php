<?php

namespace Api\Util;

use Api\Models\Tbo;


chdir('../');
$path = getcwd();

//require_once($path.'/vendor/autoload.php');
require_once($path.'/Models/Tbo.php');
require_once($path . '/config/config.php');

try {

    $userId = (isset($_SESSION['user']) && $_SESSION['user']) ? $_SESSION['user']['id'] : 0;

    $orgModel = new Tbo();
    $orgList = $orgModel::getOrg($userId);

    header('Content-Type: application/json');
    //echo json_encode(['data' => $orgList]);
    echo json_encode(['data' => $orgList], JSON_UNESCAPED_UNICODE);
} catch (\Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}