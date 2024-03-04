<?php
header('Content-Type: application/json');

chdir('../');
$path = getcwd();
require_once($path.'/config/config.php');
require_once($path.'/config/database.php');
require_once($path.'/util/util.php');

//$result = null;

try {

    $date1 = date('Y-m-d H:i', strtotime('-9 minutes'));
    $date2 = date('Y-m-d H:i');
    $device = [];

    $db = new Database();

    $sql = "
        SELECT imei, device, device_time 
        FROM tbs
        WHERE org IN (1,2) AND area > 0 AND deleted_at IS NULL 
        ORDER BY device ASC";
    $result = $db->query($sql);

    if ($result !== false) {
        foreach ($result as $tmp) {
            $device_time = funcDeviceTimeFormat($tmp['device_time']);

            if ($date1 <= $device_time && $date2 >= $device_time) {
                //
            } else {
                $device[] = $tmp['device'];
            }
    
            unset($device_time);
        }
       
    } else {
        echo json_encode(['data' => []]);
    }
    if (is_object($result)) $result->close();

    #############################################################################
    
    if (count($device) > 0) {
		$hp = [];
		$sql2 = "SELECT id, name, hp FROM tbm WHERE org=0 AND del='N'";

        $result2 = $db->query($sql2);

        if ($result2 !== false) {
            foreach ($result2 as $tmp2) {
                $hp[] = $tmp2['hp'];
            }
        } else {
            echo json_encode(['error' => 'Error in the second query']);
        }

		if (is_object($result2)) $result2->close();

        //echo "hahaha3";

		$msg = "계측기 ".$date1." 데이터 누락\n";
		//$msg .= implode("\n", $device);

        //echo "hahaha4";
		for ($i = 0; $i < count($device); $i++) {
			$msg .= ' - '.$device[$i]."\n";
		}
	
		for ($i = 0; $i < count($hp); $i++) {
            echo "다음 휴대폰 번호로 데이터 누락된 센서 목록 전송 : " . $hp[$i] . "\n";
			funcSendLms($hp[$i], $msg);
			sleep(3);
		}
        //echo "hahaha5";
	}

    ###################################################################################

} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
} 
?>
