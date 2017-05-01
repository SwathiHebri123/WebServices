<?php

class DBOperations{

	 private $host = 'localhost';
	 private $user = 'root';
	 private $db = 'learn2crack_login_register';
	 private $pass = '';
	 private $conn;

public function __construct() {

	$this -> conn = new PDO("mysql:host=".$this -> host.";dbname=".$this -> db, $this -> user, $this -> pass);

}


 public function insertData($name,$email,$password){

 	$unique_id = uniqid('', true);
    $hash = $this->getHash($password);
    $encrypted_password = $hash["encrypted"];
	$salt = $hash["salt"];

 	$query = $this ->conn ->prepare("Insert Into users (name1, email, encrypted_password, salt, unique_id) Values(:name1, :email, :encrypted_password, :salt, :unique_id)");
 	$query->execute(array(':name1' => $name, ':email' => $email, ':encrypted_password' => $encrypted_password, ':salt' => $salt, ':unique_id' => $unique_id));

    if ($query) {
        
        return true;

    } else {

        return false;

    }
 }
 
 
 public function deleteData($name){

 	//$unique_id = uniqid('', true);
    //$hash = $this->getHash($password);
    //$encrypted_password = $hash["encrypted"];
	//$salt = $hash["salt"];

 	$query = $this ->conn ->prepare("DELETE FROM users WHERE name1 = :name");
 	$query->execute(array(':name1' => $name));

    if ($query) {
        
        return true;

    } else {

        return false;

    }
 }


 public function checkLogin($email, $password) {

    $sql = 'SELECT * FROM users WHERE email = :email';
    $query = $this -> conn -> prepare($sql);
    $query -> execute(array(':email' => $email));
    $data = $query -> fetchObject();
    $salt = $data -> salt;
    $db_encrypted_password = $data -> encrypted_password;

    if ($this -> verifyHash($password.$salt,$db_encrypted_password) ) {


        $user["name1"] = $data -> name1;
        $user["email"] = $data -> email;
        $user["unique_id"] = $data -> unique_id;
        return $user;

    } else {

        return false;
    }

 }


 public function changePassword($email, $password){


    $hash = $this -> getHash($password);
    $encrypted_password = $hash["encrypted"];
    $salt = $hash["salt"];

    $sql = 'UPDATE users SET encrypted_password = :encrypted_password, salt = :salt WHERE email = :email';
    $query = $this -> conn -> prepare($sql);
    $query -> execute(array(':email' => $email, ':encrypted_password' => $encrypted_password, ':salt' => $salt));

    if ($query) {
        
        return true;

    } else {

        return false;

    }

 }

 public function checkUserExist($email){

    $sql = 'SELECT COUNT(*) from users WHERE email =:email';
    $query = $this -> conn -> prepare($sql);
    $query -> execute(array('email' => $email));

    if($query){

        $row_count = $query -> fetchColumn();

        if ($row_count == 0){

            return false;

        } else {

            return true;

        }
    } else {

        return false;
    }
 }

 public function getHash($password) {

     $salt = sha1(rand());
     $salt = substr($salt, 0, 10);
     $encrypted = password_hash($password.$salt, PASSWORD_DEFAULT);
     $hash = array("salt" => $salt, "encrypted" => $encrypted);

     return $hash;

}



public function verifyHash($password, $hash) {

    return password_verify ($password, $hash);
}
}




