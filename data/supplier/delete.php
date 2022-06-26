<?php

/**
 * @var $connection PDO
 */

require_once('../../config/koneksi.php');

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'DELETE'){
    http_response_code(400);
    $reply['error'] = 'DELETE method required';
    echo json_encode($reply);
    exit();
}

/**
 * Get input data from RAW data
 */
$data = file_get_contents('php://input');
$res = [];
parse_str($data, $res);
$idSupplier = $res['id_supplier'] ?? '';


/**
 *
 * Cek apakah kode_pasien tersedia
 */
try{
    $queryCheck = "SELECT * FROM supplier where id_supplier = :id_supplier";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_supplier', $idSupplier);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan di id supplier '.$idSupplier;
        echo json_encode($reply);
        http_response_code(400);
        exit(0);
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/**
 * Hapus data
 */
try{
    $queryCheck = "DELETE FROM supplier where id_supplier = :id_supplier";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_supplier', $idSupplier);
    if(!$statement->execute()){
        $reply['error'] = $statement->errorInfo();
        echo json_encode($reply);
        http_response_code(400);
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

/*
 * Send output
 */
$reply['status'] = true;
echo json_encode($reply);