<?php
namespace Api\Models;

header('Content-Type: application/json');
$path = getcwd();
// $path : /home/tbs/api
require_once($path.'/config/config.php');
require_once($path.'/config/database.php');
// ---------------------------------------------
//require_once($path . '/autoload.php');
// CALL METHODS in the classes
require_once($path . '/Models/Tbi.php');
require_once($path . '/Models/TbiCond.php');
require_once($path . '/Models/Tba.php');
require_once($path . '/Models/TbiScheduler.php');
require_once($path . '/Models/Tbm.php');
require_once($path . '/Models/Mqtt.php');
require_once($path . '/Models/Tbo.php');
require_once($path . '/Models/Tbs.php');
require_once($path . '/Models/TbsData.php');
require_once($path . '/Models/TbwData.php');
require_once($path . '/Models/TbiLog.php');
require_once($path . '/Models/CommonLog.php');
require_once($path . '/Models/TbsLog.php');

try {
    //echo $path;
    //$result = Tbi::getIrrigation(); // test done
    //$result = Tbi::findIrrigationById(2,true); // test done
    //$result = Tbi::countIrrigationByName('2722404351(성남 탄천 P턴)'); // test done
    //$result = Tbi::irrView(1); // test done
    //$result = Tbi::irrView(); // test done
    // ---------------------------------------------------------------------------------------
    //$result = TbiCond::irrView();
    //$result = TbiCond::getByImei('864718066823867');
    //$result = TbiCond::checkIrrCond('864718066803810');
    // --------------------------------------------------------------------
    //$currentDateTime = date('Y-m-d H:i:s');
    //echo $currentDateTime;
    // ---------------------------------------------------
    //$result = TbiScheduler::getIrrigation(0);
    //$result = TbiScheduler::getIrrigation('2023-11-01','2023-11-30',0);
    //$result = TbiScheduler::getIrrigation('2023-11-01','2023-11-30',1,1);
    //$result = TbiScheduler::irrOperate();
    // --------------------------------------------------------------------------------
    //$result = Tba::getArea(2);
    //$result = Tba::getFilePathById(9,false);
    //$result = Tba::getAreaWithColumns(['id','name']);
    //$result = Tba::getAreaWithColumns(['id','org','name']);
    // -----------------------------------------------------------
    //$result = Tbm::getById('admin',true);
    //$result = Tbm::getByNameHp('시스템','010-0000-0000');
    //$result = Tbm::getActiveMembers();
    //$result = Tbm::getByIdHp('system','010-0000-0000');
    // ----------------------------------------------------------------
    //$result = Mqtt::getCntCurrentSensor('864718066791080');
    //$result = Mqtt::checkImei('864718066791080');
    // -----------------------------------------------------------------------
    //$result = Tbo::getIdName();
    //$result = Tbo::countOrganizationsByName('성남도시개발공사');
    //$result = Tbo::getOrg(2);
    //$result = Tbo::getOrg2();
    //$result = Tbo::findOrganizationById(2);
    // ---------------------------------------------------------------------------------------------
    //$result = TbsData::getByImei('864718066845258');
    //$result = TbsData::getByImei('864718066845324');
    //$result = TbsData::getByColImeiDate('temp','864718066803786','2023-10-01','2023-10-31');
    //$result = TbsData::getByColImeiDate('conduc','864718066791080','2023-10-01','2023-10-31');
    //$result = TbsData::getDataListByImeiDate(['864718066791080'],'2023-11-01','2023-11-30');
    //$result = TbsData::getDataListByImeiDate(['864718066803844','864718066875776'],'2023-11-01','2023-11-30');
    // 'STGS71C61000017' -> org : 1, area : 13 // 'STGS71C61000065' -> org 1, area : 13, // 'STGS71C61000047' -> org 1, area : 13
    //$result = TbsData::getDataListByImeiDate2(1,13,['STGS71C61000017','STGS71C61000065'],'2023-11-01','2023-11-30');
    //$result = TbsData::getDataListByImeiDate2(1,13,['STGS71C61000062','STGS71C61000017','STGS71C61000052'],'2023-11-01','2023-11-30');
    //$result = TbsData::getDataListByImeiDate2(2,16,['STGS71C61000038','STGS71C61000031','STGS71C61000033'],'2023-11-01','2023-11-30');
    // ---------------------------------------------------------------------------------------------------------------------------------------
    //$result = TbwData::getCurrentWeather(0);
    //$result = TbwData::getCurrentWeather_test(0);
    //$result = TbwData::sumPcpByOrgAndDate(0);
    //$result = TbwData::sumPcpByOrgAndDate_test(0);
    // ----------------------------------------------------------
    //$result = Tbs::getOrg();
    //$result = Tbs::getArea();
    //$result = Tbs::getIrr();
    //$result = Tbs::getDevice(1);
    //$result = Tbs::getDevice(8);
    //$result = Tbs::getDevice2(2);
    //$result = Tbs::getDevice2(8);
    //$result = Tbs::getSensorData(1,13,true);
    //$result = Tbs::getSensorData(1,13,false);
    //$result = Tbs::getSensorData(2,16,true);
    // '864718066845274' -> device num 27 , org : 1  // '864718066829906' -> org : 2
    //$result = Tbs::getByImei('864718066845274',true);
    //$result = Tbs::getByImei('864718066845274',false);
    //$result = Tbs::getByImei('864718066829906');
    //$result = Tbs::irrView(); // admin
    //$result = Tbs::irrView(2);
    //$result = Tbs::getOrderedSensorsByDevice();
    //$result = Tbs::getSensor('864718066829906');
    //$result = Tbs::getSensor('864718066845274');
    //$result = Tbs::getSensor('12345');
    //$result = Tbs::isDuplicateSensor('864718066829906','STGS71C61000029');
    //$result = Tbs::isDuplicateSensor('864718066829906_1','STGS71C61000029');
    //$result = Tbs::isDuplicateSensor('864718066845373_2','STGS71C61000033');
    // ex1 : (2,1) // ex2: (2,9) // ex. etc: (1,5), (1,10) ...
    //$result = Tbs::isDuplicateIrrChannel(2,1);
    //$result = Tbs::isDuplicateIrrChannel(2,9);
    //$result = Tbs::isDuplicateIrrChannel(1,5);
    //$result = Tbs::isDuplicateIrrChannel(1,10);
    // '계측기 설정' 게시판 (configSensor)
    // '가상 데이터' 게시판 일부 (virtualData) // (imei, device)
    //$result = Tbs::virtualData('864718066829906','STGS71C61000029');
    //$result = Tbs::virtualData('864718066829906');
    //$result = Tbs::virtualData(null,'STGS71C61000029');
    // ------------------------------------------------------------------------------------
    //$result = TbiLog::irrLog(2,1);
    //$result = TbiLog::irrLog(2);
    //$result = TbiLog::irrLog(null,1);
    //$result = TbiLog::irrLog(1,10);
    //$result = TbiLog::irrLog(1);
    //$result = TbiLog::irrLog(null,10);
    //$result = TbiLog::irrLog(null,15);
    // ----------------------------------------------------------------
    //$result = TbsLog::sensorLog('STGS71C61000063');
    //$result = TbsLog::sensorLog('device','STGS71C61000063');
    //$result = TbsLog::sensorLog('imei','864718066845258');
    //$result = TbsLog::sensorLog('device','864718066845258');
    //$result = TbsLog::sensorLog('imei','864718066845183');
    //$result = TbsLog::sensorLog('device','STGS71C61000054');
    //$result = TbsLog::sensorLog(null,'STGS71C61000054');
    // -------------------------------------------------------------------
    //$result = CommonLog::commonLog();
    // --------------------------------------------------------------
    // memberList : '관리자 정보' 게시판 
    //$result = Tbm::memberList('admin','name','관리자');
    //$result = Tbm::memberList('admin',null,null);
    //$result = Tbm::memberList('admin','name',null);
    //$result = Tbm::memberList('isdc','name',null);
    //$result = Tbm::memberList('isdc',null,null);
    //$result = Tbm::memberList('isdc','id',null);
    //$result = Tbm::memberList('children','id',null);
    //$result = Tbm::memberList('children','name',null);
    //$result = Tbm::memberList('children',null,null);
    //$result = Tbm::memberList('children','id','테스트');
    //$result = Tbm::memberList('children','id','children');
    //$result = Tbm::memberList('children','name','children');
    //$result = Tbm::memberList('children','name','테스트');
    //$result = Tbm::memberList('admin',null,'테스트');
    //$result = Tbm::memberList('admin',null,'관리자');
    //$result = Tbm::memberList('admin','name','관리자');
    //$result = Tbm::memberList('admin','id','관리자');
    //$result = Tbm::memberList('admin','name','테스트');
    // ---------------------------------------------------------------
    //$result = Tba::getAreaList();
    // -----------------------------------


    // ========= TEST DONE BY HERE ==================================================
    
    // TO DO  // join statements included -------------------------------
   
    $result = Tbs::getSensorListExcel();
    //$result = Tbs::getSensorWithIrrigationByImei($imei);
    //$result = Tbs::getAllSensorsWithIrrigationOrderedByDate();
    //$result = Tbs::getSensorWithOrgAndAreaByImei($imei);
    //$result = Tbs::getIrrOperate();


    

    //echo json_encode(['success' => true, 'data' => $result]);
    echo json_encode(['data' => $result], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    //echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    echo json_encode(['error' => $e->getMessage()]);
}

?>
