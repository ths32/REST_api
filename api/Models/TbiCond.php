<?php

namespace Api\Models;
use Api\Models\Tbi;

class TbiCond
{
	private static function getDBConn()
    {
        try {
            return new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
        } catch (\Exception $e) {
            return null;
        }
    }

    // TEST DONE
	// part of function irr 
	public static function getByImei($imei)
    {
		$pdo = self::getDBConn();
            if (!$pdo) return ['error' => 'Failed to connect to the database.'];
        try {
            $query = "SELECT * FROM tbi_cond WHERE imei = :imei ORDER BY id ASC";
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
            if (!$pdo) return ['error' => 'Failed to connect to the database.'];

            $query = "SELECT s.device, o.name AS org_name, a.name AS area_nm, i.name AS irr_nm, s.irr_chn, ic.v1, ic.v2
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


	//관수 조건 확인.(배포시 주석 그대로)
    // part of function mqtt 
    // TEST DONE
	public static function checkIrrCond($imei)
	{
		$pdo = self::getDBConn();
            if (!$pdo) return ['error' => 'Failed to connect to the database.'];
        try {
            $query = "SELECT * FROM tbi_cond WHERE imei = :imei ORDER BY v1 ASC";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':imei', $imei);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
	}

   
}