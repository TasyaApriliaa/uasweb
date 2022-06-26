<?php

/**
 * @var $connection PDO
 */

require_once('../../config/koneksi.php');

/*
 * Validate http method
 */
if($_SERVER['REQUEST_METHOD'] !== 'PATCH'){
    header('Content-Type: application/json');
    http_response_code(400);
    $reply['error'] = 'PATCH method required';
    echo json_encode($reply);
    exit();
}
/**
 * Get input data PATCH
 */
$formData = [];
parse_str(file_get_contents('php://input'), $formData);

$idSupplier = $formData['id_supplier'] ?? '';
$namaSupplier = $formData['nama_supplier'] ?? '';
$alamat = $formData['alamat'] ?? '';
$noHp = $formData['no_hp'] ?? '';
$jenisKelamin = $formData['jenis_kelamin'] ?? '';
$tanggalLahir = $formData['tanggal_lahir'] ?? '';



/**
 * METHOD OK
 * Validation OK
 * Check if data is exist
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
 * Prepare query
 */
try{
    $fields = [];
    $query = "UPDATE supplier SET nama_supplier = :nama_supplier, alamat = :alamat, no_hp = :no_hp, jenis_kelamin = :jenis_kelamin, tanggal_lahir = :tanggal_lahir  WHERE id_supplier = :id_supplier";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id_supplier", $idSupplier);
    $statement->bindValue( ":nama_supplier", $namaSupplier);
    $statement->bindValue(":alamat", $alamat);
    $statement->bindValue(":no_hp", $noHp);
    $statement->bindValue(":jenis_kelamin", $jenisKelamin);
    $statement->bindValue(":tanggal_lahir", $tanggalLahir);

    /**
     * Execute query
     */
    $isOk = $statement->execute();
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}
/**
 * If not OK, add error info
 * HTTP Status code 400: Bad request
 * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
 */
if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}

/**
 * Show output to client
 */
$reply['status'] = $isOk;
echo json_encode($reply);