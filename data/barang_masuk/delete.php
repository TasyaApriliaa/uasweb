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
$idMasuk = $res['id_masuk'] ?? '';


/**
 *
 * Cek apakah id_masuk tersedia
 */
try{
    $queryCheck = "SELECT * FROM barang_masuk where id_masuk = :id_masuk";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_masuk', $idMasuk);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan dengan id masuk '.$idMasuk;
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
    $queryCheck = "DELETE FROM barang_masuk where id_masuk = :id_masuk";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_masuk', $idMasuk);
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