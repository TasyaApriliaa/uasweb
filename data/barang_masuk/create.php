<?php

/**
 * @var $connection PDO
 */

require_once('../../config/koneksi.php');

/**
 * Get input data POST
 */
$tanggalMasuk = $_POST['tanggal_masuk'] ?? '';
$kuantitas = $_POST['kuantitas'] ?? '';
$idSupplier = $_POST['id_supplier'] ?? '';
$idObat = $_POST['id_obat'] ?? '';
$isOk = false;

//var_dump($umur);
//die();

/**
 * Method OK
 * Validation OK
 * Prepare query
 */
try{
    $getObat = "SELECT * FROM obat WHERE id_obat = :id_obat";
    $stmobat = $connection->prepare($getObat);
    $stmobat->bindValue(':id_obat', $idObat);
    $stmobat->execute();
    $obat = $stmobat->fetch(PDO::FETCH_ASSOC);

    $replys['data'] = $obat;
    if($replys['data'] ){
//        var_dump($replys['data']);
//        die;
        $hargaBeli = $replys['data']['harga_beli'];
        $stok = $replys['data']['stok'];
        $totalBeli = $hargaBeli * $kuantitas;
        $stokTerbaru = $stok + $kuantitas;
        $updateStok = "UPDATE obat SET  stok = :stok  WHERE id_obat = :id_obat";
        $statement = $connection->prepare($updateStok);
        /**
         * Bind params
         */
        $statement->bindValue(":id_obat", $idObat);
        $statement->bindValue(":stok", $stokTerbaru);

        /**
         * Execute query
         */
        $statement->execute();

        $query = "INSERT INTO barang_masuk ( tanggal_masuk, kuantitas, total_beli, id_supplier, id_obat) VALUES ( :tanggal_masuk, :kuantitas, :total_beli, :id_supplier, :id_obat)";
        $statement = $connection->prepare($query);
        /**
         * Bind params
         */
        $statement->bindValue( ":tanggal_masuk", $tanggalMasuk);
        $statement->bindValue(":kuantitas", $kuantitas);
        $statement->bindValue(":total_beli", $totalBeli);
        $statement->bindValue(":id_supplier", $idSupplier);
        $statement->bindValue(":id_obat", $idObat);

        /**
         * Execute query
         */
        $isOk = $statement->execute();
        /**
         * If not OK, add error info
         * HTTP Status code 400: Bad request
         * @see https://developer.mozilla.org/en-US/docs/Web/HTTP/Status#client_error_responses
         */

        if (!$isOk) {
            $reply['error'] = $statement->errorInfo();
            http_response_code(400);
        }
    } else {
        echo"Obat Tidak Tersedia, Silahkan Input di Daftar Obat Terlebih Dahulu!";
    }



}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}



/*
 * Get last data
 */
$lastId = $connection->lastInsertId();
$getResult = "SELECT * FROM barang_masuk WHERE id_masuk = :id_masuk";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id_masuk', $lastId);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);


/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $result;
$reply['status'] = $isOk;
echo json_encode($reply);
