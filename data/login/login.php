<?php
/**
 * @var $connection PDO
 */

require_once('../../config/koneksi.php');

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

session_start();
try{
    if(isset($_POST['username'], $_POST['password'])){

        $query = "SELECT username, password FROM login WHERE username=:username AND password=:password";
        $statement = $connection->prepare($query);
        $statement->bindParam(':username', $username);
        $statement->bindParam(':password', $password);
        $isOk = $statement->execute();

        if($row = $statement->fetch()){
            $_SESSION['username'] = $row['username'];
            $result = "Login Berhasil!";
        }

        if(!$isOk){
            $reply['error'] = $statement->errorInfo();
            http_response_code(400);
        }
    }
} catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

$reply['data'] = $result;
$reply['status'] = $isOk;
echo json_encode($reply);


