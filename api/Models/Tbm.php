<?php

namespace Api\Models;
use \PDO;

class Tbm
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

    // getById => memberList
    // memberForm with $limit false
    // TEST DONE
	public static function getById($memberId, $limit = true)
    {
        try {
            $pdo = self::getDBConn();
            if (!$pdo) return self::dbConnErr();
            $query = "SELECT id, name, hp, org FROM tbm WHERE id = :member_id";
            if ($limit) $query .= " LIMIT 1";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':member_id', $memberId);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // TEST DONE // 추후 비번 변경 등과 결합
	public static function getByNameHp($memberName, $hp)
    {
        try {
            $pdo = self::getDBConn();
            if (!$pdo) return self::dbConnErr();
            $query = "SELECT id, hp FROM tbm WHERE name = :member_name AND hp = :hp AND del = 'N'";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':member_name', $memberName);
            $stmt->bindParam(':hp', $hp);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // getActiveMembers => memberList
    // TEST DONE
	public static function getActiveMembers()
    {
        try {
            $pdo = self::getDBConn();
            if (!$pdo) return self::dbConnErr();
            $query = "SELECT * FROM tbm WHERE del = 'N' ORDER BY org ASC, created_at DESC";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // TEST DONE // 추후 비번 변경 등과 결합
	public static function getByIdHp($memberId, $hp)
    {
        try {
            $pdo = self::getDBConn();
            if (!$pdo) return self::dbConnErr();
            $query = "SELECT name, hp FROM tbm WHERE id = :member_id AND hp = :hp AND del = 'N'";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':member_id', $memberId);
            $stmt->bindParam(':hp', $hp);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    // -----------------------------------------------------------------------------------------------------
    
    // TEST DONE
    // '관리자 정보' 게시판 // 관리자 모드, 비관리자 모드 구분할 것
    // 검색 시 해당 메소드 호출
    // memberList
    public static function memberList($user,$input_type = null,$input = null)
    {
        try {
            $pdo = self::getDBConn();
            if (!$pdo) return self::dbConnErr();
            $query = "SELECT ROW_NUMBER() OVER (ORDER BY m.org DESC, m.created_at ASC) AS num, 
                        m.id, m.name, CASE WHEN m.org = 0 THEN '관리자' ELSE o.name END AS org, m.hp 
                        FROM tbm m 
                            LEFT JOIN tbo o 
                                ON m.org = o.id WHERE m.del = 'N' AND 1=1";
            // 관리자 계정이 아닌 경우, 그 계정 정보만 표시
            // 한 번 로그인 하면 이 값은 고정
            if ($user != 'admin' && $user != 'system') 
            {
                $query .= " AND m.id = :id";
                //echo "user : " .$user."\n"; 
                //echo "cond1\n";
            }
            // 선택 - 아이디 검색
            if ($input_type != null && $input_type == 'id'  && $input !== null) 
            {
                $query .= " AND m.id = :id2";
                //echo "cond2\n";
            }
            // 선택 - 이름 검색
            if ($input_type != null && $input_type == 'name' && $input !== null) 
            {
                $query .= " AND m.name = :name";
                //echo "cond3\n";
            }
            $query .= " ORDER BY m.org ASC, m.created_at DESC";
            //echo "-----------------------------------------------------------------------------------------------------\n";
            //echo $query."\n";
            //echo "===========================================================================================================================================\n";
            $stmt = $pdo->prepare($query);
            if ($user != 'admin' && $user != 'system') $stmt->bindParam(':id', $user);
            if ($input_type != null && $input_type == 'id' && $input !== null) $stmt->bindParam(':id2', $input);
            if ($input_type != null && $input_type == 'name' && $input !== null) $stmt->bindParam(':name', $input);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}