<?php

namespace Api\Models;


class Tbs
{
	private static function getDBConn()
    {
        try {
            return new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
        } catch (\Exception $e) {
            return null;
        }
    }

	private static function dbConnErr() {
        return ['error' => 'Failed to connect to the database.'];
    }



	// TEST DONE
	public static function getOrg()
	{	
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
		try {	
			$query = "SELECT org FROM tbs";	
			$stmt = $pdo->prepare($query);
			$stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\Exception $e) {
			return ['error' => $e->getMessage()];
		}
	
	}

	// TEST DONE
	public static function getArea()
	{	
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
		try {
			$query = "SELECT area FROM tbs";	
			$stmt = $pdo->prepare($query);
			$stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\Exception $e) {
			return ['error' => $e->getMessage()];
		}
	
	}
	
	// TEST DONE
	public static function getIrr()
	{	
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
		try {
			$query = "SELECT irr FROM tbs";	
			$stmt = $pdo->prepare($query);
			$stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\Exception $e) {
			return ['error' => $e->getMessage()];
		}
	
	}

	// TEST DONE
	// func sensorData
	// org(not null) : 1, 2, 5, 8
	public static function getDevice($userOrg)
	{	
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
		try {
			$query = "SELECT imei, device, org, area FROM tbs";	
			if ($userOrg != null) $query .= " WHERE org = :user_org";
			$query .= " ORDER BY device ASC";
			$stmt = $pdo->prepare($query);
			if ($userOrg != null) $stmt->bindParam(':user_org', $userOrg);
			$stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\Exception $e) {
			return ['error' => $e->getMessage()];
		}
	
	}

	// TEST DONE
	// func sensorData
	public static function getDevice2($userOrg)
	{	
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
		try {
			$query = "SELECT imei, device, org, area FROM tbs WHERE irr > 0 AND irr_chn > 0";	
			if ($userOrg != null) $query .= " AND org = :user_org";
			$query .= " ORDER BY device ASC";
			$stmt = $pdo->prepare($query);
			if ($userOrg != null) $stmt->bindParam(':user_org', $userOrg);
			$stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\Exception $e) {
			return ['error' => $e->getMessage()];
		}
	
	}

	private static function getOrderCondition($useOrderCondition = true)
    {
        if ($useOrderCondition) {
            return "CASE WHEN humi <= 7 THEN '9' WHEN humi <= 40 AND humi >= 15 THEN '7' ELSE '8' END DESC,";
        }
        return '';
    }

	// TEST DONE
	// part of CommonController
	public static function getSensorData($org, $area, $useOrderCondition = true)
    {
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
        try {
            $orderCondition = self::getOrderCondition($useOrderCondition);
            $query = "SELECT * FROM tbs WHERE org = :org AND area = :area ORDER BY $orderCondition device_time ASC";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':org', $org);
            $stmt->bindParam(':area', $area);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

	// TEST DONE
	public static function getByImei($imei, $limit = true)
    {
		$pdo = self::getDBConn();
            if (!$pdo) return self::dbConnErr();
        try {
            $query = "SELECT * FROM tbs WHERE imei = :imei";
            if ($limit)  $query .= " LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':imei', $imei);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

	// '관수조건 확인' 게시판
    // irrView 
    // TEST DONE
    public static function irrView($user_org = null)
    {
        try {
            $pdo = self::getDBConn();
            if (!$pdo) return self::dbConnErr();

            $query = "SELECT s.device, o.name AS org_name, a.name AS area_name, i.name AS irr_name, s.irr_chn, ic.v1, ic.v2
                    FROM tbs s
                    JOIN tbo o ON s.org = o.id
                    JOIN tba a ON s.area = a.id
                    JOIN tbi i ON s.irr = i.id
                    LEFT JOIN (
                        SELECT imei, v1, v2, ROW_NUMBER() OVER (PARTITION BY imei ORDER BY created_at DESC) AS rn
                        FROM tbi_cond
                    ) ic ON s.imei = ic.imei AND ic.rn = 1
                    WHERE
                        s.irr > 0 AND s.irr_chn > 0";
            if ($user_org !== null) $query .= " AND s.org = :user_org";
            $query .= " ORDER BY o.name ASC, a.name ASC";
            $stmt = $pdo->prepare($query);
            if ($user_org !== null) $stmt->bindParam(':user_org', $user_org);
            $stmt->execute();

            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
	
	// TEST DONE
	// func sensorList
	public static function getOrderedSensorsByDevice() {
        $pdo = self::getDBConn();
        if (!$pdo) return self::dbConnErr();
        try {
            $query = "SELECT * FROM tbs ORDER BY device ASC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

	// TEST DONE
	// '계측기 등록' 게시판
	// 등록시 중복된 기기인지 여부 확인(SELECT)
	// sensorProcess
	// imei와 device는 한 쌍으로 유니크함 // 센서가 존재할 경우 imei == :imei 일 때 그 데이터 카운트값이 1임.
	// 한 쌍의 imei, device에서(둘다 제대로 입력했을 때) imei를 '!=' 처리하면 센서 데이터가 존재시 그 값은 0임('=' 처리하면 그 값은 1). 고로, 쿼리 결과를 > 0 하면(1 이상이면 중복) 그 값은 false임.
	// 즉, 센서 데이터가 중복이 아니면 쿼리 결과가 최종적으로 false가 뜸.
	// TEST1 : ('864718066829906','STGS71C61000029') // TEST2 : ('864718066845373','STGS71C61000033')
	// 원 개발자가 만들어놓은 쿼리 특성상(imei가 다를 때), imei는 같게 하고 device만 (조금) 다르게 하면 중복이 아닌것처럼 false가 뜸
	// 추후 당 메소드를 수정하거나 다른 메소드에 병합한다든가 할 수 있음.
	public static function isDuplicateSensor($imei, $device) {
        $pdo = self::getDBConn();
        if (!$pdo) return self::dbConnErr();
        try {
            $query = "SELECT COUNT(*) FROM tbs WHERE imei != :imei AND device = :device";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':imei', $imei);
            $stmt->bindParam(':device', $device);
            $stmt->execute();
			
            return $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

	// TEST DONE
	// 'irr'과 'irr_chn'은 한 쌍으로 존재 // 예1 : 2,1 // 예2: 2,9 // ex. (1,5), (1,10) ...
	// 'sensor' : 각 센서마다 최근 데이터 Row 1
	// sensorProcess
	public static function isDuplicateIrrChannel($irr, $irrChannel) {
        $pdo = self::getDBConn();
        if (!$pdo) return self::dbConnErr();
        try {
            $query = "SELECT COUNT(*) FROM tbs
                      	WHERE irr = :irr AND irr_chn = :irr_chn";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':irr', $irr);
            $stmt->bindParam(':irr_chn', $irrChannel);
            $stmt->execute();

            return $stmt->fetchColumn() != 1; // boolean
			//return $stmt->fetchAll(\PDO::FETCH_ASSOC); // count 
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
	
	// '수동 관수' 게시판
	// irrOperate
	public static function getIrrOperate()
	{
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
		try {
			$query = "SELECT s.*, o.*, a.*, i.*
						FROM tbs s
						LEFT JOIN tbo o ON s.org = o.id
						LEFT JOIN tba a ON s.area = a.id
						LEFT JOIN tbi i ON s.irr = i.id
						WHERE s.irr != ''
						ORDER BY o.name ASC, a.name ASC, s.device ASC";

			$stmt = $pdo->prepare($query);
			$stmt->execute();

			return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\Exception $e) {
			return ['error' => $e->getMessage()];
		}
	}

	// TEST DONE
	// func sensor
	// return response()->json(['error' => '이미 등록되어 있는 계측기명 입니다']
	// 나중에 COUNT(*) > 0 , '이미 등록되어 있는 계측기명 입니다' 출력
	public static function getSensor($imei)
	{	
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
		try {
			$query = "SELECT COUNT(*) FROM tbs WHERE imei = :imei";	
			$stmt = $pdo->prepare($query);
			$stmt->bindParam(':imei', $imei);
			$stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\Exception $e) {
			return ['error' => $e->getMessage()];
		}
	
	}

	// TEST DONE
	// '가상 데이터' 게시판 일부 
	// 완전히 해당 게시판을 구현하려면 다른 메소드와 결합 필요
	// part of func data
	public static function virtualData($imei = null, $device = null)
	{
		$pdo = self::getDBConn();
		if (!$pdo) return self::dbConnErr();
		try {
			$query = "SELECT org, area, irr, irr_chn FROM tbs WHERE 1=1";	
			$conditions = [];
			if ($imei !== null) $conditions[] = "imei = :imei";
			if ($device !== null) $conditions[] = "device = :device";
			if (!empty($conditions)) $query .= " AND " . implode(" AND ", $conditions);
			$stmt = $pdo->prepare($query);
			if ($imei !== null) $stmt->bindParam(':imei', $imei);
			if ($device !== null) $stmt->bindParam(':device', $device);
			$stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\Exception $e) {
			return ['error' => $e->getMessage()];
		}
	}


	// TEST DONE
	// '계측기 설정' 게시판 (SELECT ONLY)
	// ConfigController@sensor
	// sensorProcess 와 유사하지만 request & post 등의 액션은 다름
	// 나중에 이 메소드를 오버라이딩 또는 오버로딩하여 쓸 것
	public static function configSensor($imei,$device)
	{
		$pdo = self::getDBConn();
        if (!$pdo) return self::dbConnErr();
        try {
            $query = "SELECT COUNT(*) FROM tbs WHERE imei != :imei AND device = :device";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':imei', $imei);
            $stmt->bindParam(':device', $device);
            $stmt->execute();
			
            return $stmt->fetchColumn() != 0;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
	}


}
