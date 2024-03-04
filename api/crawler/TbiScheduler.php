<?php
header('Content-Type: application/json');
set_time_limit(0);
ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'On');

$path = getcwd();
require_once($path.'/_init.php');
chdir('../');
$path = getcwd();
require_once($path.'/config/config.php');
require_once($path.'/config/database.php');
require_once($path.'/util/util.php');
require_once($path.'/autoload.php');

$db = new Database();

$sql = "SELECT * FROM tb_scheduler WHERE status IN ('processing','failed') AND end_time < NOW()";
$result = $db->query($sql);

if ($result !== false) {
    if (is_array($result) && count($result) > 0) {
        foreach ($result as $tmp) {
            $irr_info = '('.$tmp['irr_ip'].':'.$tmp['irr_chn'].')';
            
            echo "-----------------------------\n";
            echo "irr info : ".$irr_info."\n";
            echo "-----------------------------\n";

            if (funcIrrValve($tmp['irr_ip'], $tmp['irr_channel'], 'c') === true) {
                $status = 'finish';
                //echo "hahaha1";
                $db->query("INSERT INTO tb_log SET type='irr', message='자동관수 중지 성공 $irr_info', created_at=NOW()");
                //echo "자동관수 중지 성공";
                echo "자동관수 중지 성공\n";
                //echo "hahaha1_1";
                $db->query("INSERT INTO tb_log2 SET irr_id='".$tmp['irr_id']."', irr_nm='".$tmp['irr_nm']."', irr_ip='".$tmp['irr_ip']."', irr_chn='".$tmp['irr_chn']."', message='관수 중지 요청(자동)', result='Y', created_at=NOW()");
                //echo "hahaha1_2";
            }
            else {
                $status = 'failed';
                //echo "hahaha2";
                // 밑은 휴대폰으로 메시지 전송(디버깅 중일 땐 OFF)
                // foreach ($hp_array as $hp) {
                //     funcSendSms($hp, "관수제어기 중지 오류\n- NAME : ".$tmp['irr_nm']."");
                //     echo "관수제어기 중지 오류\n- NAME : ".$tmp['irr_nm']." to ".$hp."\n";
                //     //echo "hahaha3";
                // }

                echo "관수제어기 중지 오류\n- NAME : ".$tmp['irr_nm']."\n";

                //echo "hahaha4";
                $db->query("INSERT INTO tb_log SET type='irr', message='자동관수 중지 실패 $irr_info', created_at=NOW()");
                //echo "hahaha4_1";
                $db->query("INSERT INTO tb_log2 SET irr_id='".$tmp['irr_id']."', irr_nm='".$tmp['irr_nm']."', irr_ip='".$tmp['irr_ip']."', irr_chn='".$tmp['irr_chn']."', message='관수 중지 요청(자동)', result='N', created_at=NOW()");
                //echo "hahaha4_2";
            }
            //echo "hahaha5";
            $db->query("UPDATE tb_scheduler SET status='".$status."' WHERE id='".$tmp['id']."'");
            //echo "hahaha6";
            unset($tmp);
        //	usleep(500000);
        }
    } else {
        echo "'processing' 중이거나 'failed'된 data가 없습니다.";
    }
    if (isset($result) && is_object($result)) $result->close();
    unset($sql, $result);
    //if (is_object($db)) $db->close();
    if (isset($db) && is_object($db)) {
        $db->closeConnection();
    } 
} else {
    echo "Error in database query.";
}

?>
