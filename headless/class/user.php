<?php
namespace api;

use PDO;
use PDOException;

class User {

    private PDO $pdo;
    private string $table = 'users';

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create(string $unique_id, string $name, string $username, string $email, string $password): string {
        
        $create_date_time = date('Y-m-d H:i:s');
        $update_date_time = null;
        $password = $this->password_encrypt($password);
        $is_active = 1;
        $email_confirmation = 0;

        $param = ['username', 'email'];
        foreach($param as $p){
            if($this->duplicate_check($p, $$p)){
                $response = ['status' => 'error', 'message' => "$p is duplicate"];
                return json_encode($response, JSON_UNESCAPED_UNICODE);
            }
        }

        try {
            $sql = "INSERT INTO {$this->table} (unique_id, is_active, name, username, email, email_confirmation, password, create_date_time, update_date_time) 
                    VALUES (:unique_id, :is_active, :name, :username, :email, :email_confirmation, :password, :create_date_time, :update_date_time)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                ':unique_id' => $unique_id,
                ':is_active' => $is_active,
                ':name' => $name,
                ':username' => $username,
                ':email' => $email,
                ':email_confirmation' => $email_confirmation,
                ':password' => $password,
                ':create_date_time' => $create_date_time,
                ':update_date_time' => $update_date_time,
            ]);
            $response =['status' => 'success', 'message' => 'user created'];
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function update(string $unique_id, array $data): string {

        if(!$this->exist($unique_id)){
            $response = ['status' => 'error', 'message' => 'User Does Not Exist'];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        $allowedFields = ['is_active', 'name', 'username', 'email', 'password', 'unique_id'];
        $setClauses = [];
        $binds = [
            ':unique_id' => $unique_id,
            ':update_date_time' => date('Y-m-d H:i:s')
        ];
        foreach ($data as $param => $value) {
            
            if($param === 'unique_id') continue;

            if (!in_array($param, $allowedFields, true)) {
                $response = ['status' => 'error', 'message' => "Invalid parameter: $param"];
                return json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            if (in_array($param, ['username', 'email'])) {
                if($this->duplicate_check($param, $value, $unique_id)){
                    $response = ['status' => 'error', 'message' => "$param is duplicate"];
                    return json_encode($response, JSON_UNESCAPED_UNICODE);
                }
            }
            if ($param === 'password') {
                $value = $this->password_encrypt($value);
                $passwordvalidationSql = "SELECT COUNT(*) FROM {$this->table} WHERE password = :password AND unique_id = :unique_id";
                $param1 = [":password" => $value, ":unique_id" => $unique_id];
                $validationStmt = $this->pdo->prepare($passwordvalidationSql);
                $validationStmt->execute($param1);
                if($validationStmt->fetchColumn() > 0){
                    $response = ['status' => 'error', 'message' => 'New password is same as old password'];
                    return json_encode($response, JSON_UNESCAPED_UNICODE);
                }
            }
            $setClauses[] = "$param = :$param";
            $binds[":$param"] = $value;
        }
        if (empty($setClauses)) {
            $response = ['status' => 'error', 'message' => 'No valid parameters provided for update'];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        $sql = "UPDATE {$this->table} SET update_date_time = :update_date_time, " . implode(', ', $setClauses) . " WHERE unique_id = :unique_id";
        try {
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($binds);
            $response = ['status' => 'success', 'message' => 'user updated'];
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function delete(string $unique_id): string {

        if(!$this->exist($unique_id)){
            $response = ['status' => 'error', 'message' => 'User Does Not Exist'];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }
        try {
            $sql = "DELETE FROM {$this->table} WHERE unique_id = :unique_id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':unique_id' => $unique_id]);
            $response = ['status' => 'success', 'message' => 'user deleted'];
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function getinfo(string $unique_id): string {

        if(!$this->exist($unique_id)){
            $response = ['status' => 'error', 'message' => 'User Does Not Exist'];
            return json_encode($response, JSON_UNESCAPED_UNICODE);
        }

        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE unique_id = :unique_id");
            $stmt->execute([':unique_id' => $unique_id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                $response = ['status' => 'success', 'data' => $data];
            } else {
                $response = ['status' => 'error', 'message' => 'record not found'];
            }
        } catch (PDOException $e) {
            $response = ['status' => 'error', 'message' => $e->getMessage()];
        }
        return json_encode($response, JSON_UNESCAPED_UNICODE);
    }

    public function exist(string $user_id){
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE unique_id = :id");
        $stmt->execute([':id' => $user_id]);
        return ($stmt->fetchColumn() > 0) ? true : false;
    }
    public function authenticate($username, $password){
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE (username = :username OR email = :email)");
        $stmt->execute([':username' => $username, ':email' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user && password_verify($password, $user['password'])){
            $session_id = $this->generateSessionCode();
            return json_encode(['status' => 'success', 'session_id' => $session_id, 'user_id' => $user['unique_id']], JSON_UNESCAPED_UNICODE);
        } else {
            return json_encode(['status' => 'error', 'message' => 'Invalid username or password'], JSON_UNESCAPED_UNICODE);
        }
    }
    public function forgot_password($username, $password){
        $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE (username = :username OR email = :email)");
        $stmt->execute([':username' => $username, ':email' => $username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if($user){
            $new_password = $this->password_encrypt($password);
            $stmt = $this->pdo->prepare("UPDATE {$this->table} SET password = :password WHERE unique_id = :unique_id");
            try{
                $this->pdo->beginTransaction();
                $stmt->execute([':password' => $new_password, ':unique_id' => $user['unique_id']]);
                $this->pdo->commit();
                return json_encode(['status' =>'success','message' => 'Password has been reset successfully'], JSON_UNESCAPED_UNICODE);
            }
            catch(PDOException $e){
                $this->pdo->rollBack();
                return json_encode(['status' => 'error','message' => $e->getMessage()], JSON_UNESCAPED_UNICODE);
            }
        } else {
            return json_encode(['status' => 'error','message' => 'User does not exist'], JSON_UNESCAPED_UNICODE);
        }
    }
    public function username_duplicate_check($username){
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE username = :username";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username]);
        return ($stmt->fetchColumn() > 0) ? true : false;
    }
    public function email_duplicate_check($email){
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = :email";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':email' => $email]);
        return ($stmt->fetchColumn() > 0) ? true : false;
    }
    public function username_or_email_exists($username){
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE (username = :username OR email = :email)";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([':username' => $username, ':email' => $username]);
        return ($stmt->fetchColumn() > 0) ? true : false;
    }
    
    private function generateSessionCode() {
        $timestamp = time();
        $randomNumber = rand(100000, 999000);
        $uniqueCode = substr(md5($timestamp . $randomNumber), 0, 7);
        return $uniqueCode;
    }
    
    private function duplicate_check($param, $value, $unique_id = null){
        $validationSql = "SELECT COUNT(*) FROM {$this->table} WHERE $param = :$param";
        $param = [":$param" => $value];
        if($unique_id) {
            $validationSql .= " AND unique_id != :unique_id";
            $param[":unique_id"] = $unique_id;
        }
        $validationStmt = $this->pdo->prepare($validationSql);
        $validationStmt->execute($param);
        return ($validationStmt->fetchColumn() > 0) ? true : false;
    }    
    private function password_encrypt($password){
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
