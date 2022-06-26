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

$idPembeli = $formData['id_pembeli'] ?? '';
$namaPembeli = $formData['nama_pembeli'] ?? '';
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

    $queryCheck = "SELECT * FROM pembeli where id_pembeli = :id_pembeli";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_pembeli', $idPembeli);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan di id pembeli '.$idPembeli;
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
    $query = "UPDATE pembeli SET nama_pembeli = :nama_pembeli, alamat = :alamat, no_hp = :no_hp, jenis_kelamin = :jenis_kelamin, tanggal_lahir = :tanggal_lahir  WHERE id_pembeli = :id_pembeli";
    $statement = $connection->prepare($query);
    /**
     * Bind params
     */
    $statement->bindValue(":id_pembeli", $idPembeli);
    $statement->bindValue( ":nama_pembeli", $namaPembeli);
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