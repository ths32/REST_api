<?php

namespace Api\Models;
use Api\Models\Tbs;

class Tbi
{
	private static function getDBConn()
    {
        try {
            return new \PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_DATABASE_NAME . ";charset=utf8", DB_USERNAME, DB_PASSWORD);
        } catch (\Exception $e) {
            return null;
        }
    }

    // '관수제어기 정보' 게시판(관리자 모드)
    // 관수 정보
    // irrList // TEST DONE
	public static function getIrrigation()
	{
		$pdo = self::getDBConn();
		if (!$pdo) return ['error' => 'Failed to connect to the database.'];
		try {
			$query = "SELECT * FROM tbi";
			$query .= " ORDER BY name ASC";
            $stmt = $pdo->prepare($query);
			$stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\Exception $e){
			return ['error' => $e->getMessage()];
		}
	}

    // '관수제어기 정보' 게시판 (with $limitOne false) 
    // $limitOne false -> 관리자 모드 // id + $limitOne true -> 각 계정의 '관수제어기 정보'
    // irrForm with $limitOne false // TEST DONE
	public static function findIrrigationById($id, $limitOne = true)
    {
		$pdo = self::getDBConn();
		if (!$pdo) return ['error' => 'Failed to connect to the database.'];
        try {
            $query = "SELECT * FROM tbi WHERE id = :id";
			if ($limitOne) {
                $query .= " LIMIT 1";
            }
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // TEST DONE
	public static function countIrrigationByName($name)
    {
		$pdo = self::getDBConn();
		if (!$pdo) return ['error' => 'Failed to connect to the database.'];
        try {
            $query = "SELECT COUNT(*) FROM tbi WHERE name = :name";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->execute();
            return $stmt->fetchColumn();
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

            $query = "SELECT s.device, o.name AS org_nm, a.name AS area_nm, i.name AS irr_nm, s.irr_chn, ic.v1, ic.v2
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



}