<?php
namespace Api\Util;
use Api\Models\Tba;

chdir('../');
$path = getcwd();
require_once($path.'/Models/Tba.php');
require_once($path . '/config/config.php');

try {

    $userOrg = (isset($_SESSION['user']) && $_SESSION['user']) ? $_SESSION['user']['org'] : 0;

    $areaModel = new Tba();
    $areaList = $areaModel::getArea($userOrg);

    header('Content-Type: application/json');
    //echo json_encode(['data' => $orgList]);
    echo json_encode(['data' => $areaList], JSON_UNESCAPED_UNICODE);
} catch (\Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
