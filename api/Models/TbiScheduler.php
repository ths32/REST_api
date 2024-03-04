<?php

namespace Api\Models;
use \PDO;

class TbiScheduler {
    private static function getDBConn()
    {
        try {
            return new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
        } catch (\Exception $e) {
            return null;
        }
    }

    // ======== '관수 내역' 게시판 ================================================================================================================
	
    // org 0 : admin // org 1 : children // org 2 : isdc
    // view org 1 in admin mode : getIrrigation('start_date','end_date',0,1) // getIrrigation(0,1)
    // view org 1 in account children : getIrrigation('start_date','end_date',1,1) // getIrrigation(1,1)
    // TEST DONE
    public static function getIrrigation($start_date = null, $end_date = null, $org_for_flow_rates = 0, $org_for_rows = 0)
    {
        try {
            $pdo = self::getDBConn();
            if (!$pdo) return ['error' => 'Failed to connect to the database.'];
            if ($start_date == null || $end_date == null) {
                $start_date = date('Y-m-d', strtotime('-7 Day'));
                $end_date = date('Y-m-d');
            }
            if ($org_for_flow_rates !== 0) {
                $org_for_rows = $org_for_flow_rates;
            }
            if ($org_for_rows === null || $org_for_rows < $org_for_flow_rates) {
                $org_for_rows = $org_for_flow_rates;
            }    
            $query = "SELECT ROW_NUMBER() OVER (ORDER BY s.reg_date) AS scheduler_id, o.name AS org_nm,
                    a.name AS area_nm, s.sensor_device, i.name AS irr_nm, s.irr_chn, s.start_time, s.end_time,
                    CASE WHEN :org_for_flow_rates = 0 THEN s.flow_rate1 END AS flow_rate1,
                    CASE WHEN :org_for_flow_rates = 0 THEN s.flow_rate2 END AS flow_rate2
                    FROM
                        tbi_scheduler s
                    LEFT JOIN
                        tbo o ON s.org = o.id
                    LEFT JOIN
                        tba a ON s.area = a.id
                    JOIN
                        tbi i ON s.irr_id = i.id
                    WHERE
                        s.reg_date BETWEEN :start_date AND :end_date
                        AND (s.org = :org_for_rows OR :org_for_rows = 0)";

            //if ($user_org !== null && $user_org > 0) $query .= " AND s.org = :user_org";

            $query .= " ORDER BY s.reg_date DESC";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':start_date', $start_date);
            $stmt->bindParam(':end_date', $end_date);
            $stmt->bindParam(':org_for_rows', $org_for_rows);
            $stmt->bindParam(':org_for_flow_rates', $org_for_flow_rates);
            //if ($user_org !== null) $stmt->bindParam(':user_org', $user_org);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
                return ['error' => $e->getMessage()];
        }
    }
    // ===================================================================================================================================================


	// irrOperate
    // TEST DONE 
	public static function irrOperate()
    {
        $pdo = self::getDBConn();
        if (!$pdo) return ['error' => 'Failed to connect to the database.'];
        try {
            // if (!class_exists('PDO')) {
            //     throw new \Exception('PDO class not found. Check your PHP configuration.');
            // }
            $query = "SELECT * FROM tbi_scheduler WHERE status != 'finish'";
            $stmt = $pdo->prepare($query);
            if (!$stmt) return ['error' => 'Failed to prepare the statement.'];    
            $stmt->execute();
            //if ($stmt->rowCount() === 0) return ['message' => 'No rows found.'];
            $result =  $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($result === false) {
                return ['error' => 'Failed to fetch records.'];
            }
    
            if (count($result) === 0) {
                return ['message' => 'No rows found.'];
            }
            return $result;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

}
