<?php

/**
 * @var $connection PDO
 */

require_once('../../config/koneksi.php');

/**
 * Get input data POST
 */
$idPembeli = $_POST['id_pembeli'] ?? '';
$namaPembeli = $_POST['nama_pembeli'] ?? '';
$alamat = $_POST['alamat'] ?? '';
$noHp = $_POST['no_hp'] ?? '';
$jenisKelamin = $_POST['jenis_kelamin'] ?? '';
$tanggalLahir = $_POST['tanggal_lahir'] ?? '';

//var_dump($umur);
//die();

/**
 * Method OK
 * Validation OK
 * Prepare query
 */
try{
    $query = "INSERT INTO pembeli (id_pembeli, nama_pembeli, alamat, no_hp, jenis_kelamin, tanggal_lahir) VALUES (:id_pembeli, :nama_pembeli, :alamat, :no_hp, :jenis_kelamin, :tanggal_lahir)";
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

/*
 * Get last data
 */
$lastId = $connection->lastInsertId();
$getResult = "SELECT * FROM pembeli WHERE id_pembeli = :id_pembeli";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id_pembeli', $lastId);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);


/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $result;
$reply['status'] = $isOk;
echo json_encode($reply);
