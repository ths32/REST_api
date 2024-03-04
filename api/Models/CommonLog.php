<?php

namespace Api\Models;

// '시스템 메시지' 게시판
class CommonLog
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

	// '시스템 메시지' 게시판
    // type : 'irr'
	// CommonLog
	public static function commonLog()
	{
		$pdo = self::getDBConn();
        if (!$pdo) return self::dbConnErr();
		try {
			$query = "SELECT ROW_NUMBER() OVER (ORDER BY id) AS num, 
                        CASE WHEN type = 'irr' THEN '관수' ELSE 'N/A' END AS type, 
                            message, created_at 
                                FROM tb_log WHERE type = 'irr' ORDER BY id DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
		} catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
	}

}