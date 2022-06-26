<?php

/**
 * @var $connection PDO
 */

require_once('../../config/koneksi.php');

/**
 * Get input data POST
 */
$idKeluar = $_POST['id_keluar'] ?? '';
$tanggalKeluar= $_POST['tanggal_keluar'] ?? '';
$kuantitas = $_POST['kuantitas'] ?? '';
//$totalHarga = $_POST['total_Harga'] ?? '';
$idPembeli = $_POST['id_pembeli'] ?? '';
$idObat = $_POST['id_obat'] ?? '';
$isOk = false;



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
    if($replys['data']['stok'] >= $kuantitas ) {
//        var_dump($replys['data']);
//        die;
        $hargaJual = $replys['data']['harga_jual'];
        $totalHarga = $hargaJual * $kuantitas;


        $query = "INSERT INTO barang_keluar (id_keluar, tanggal_keluar, kuantitas, total_harga, id_pembeli, id_obat) VALUES (:id_keluar, :tanggal_keluar, :kuantitas, :total_harga, :id_pembeli, :id_obat)";
        $statement = $connection->prepare($query);
        /**
         * Bind params
         */
        $statement->bindValue(":id_keluar", $idKeluar);
        $statement->bindValue(":tanggal_keluar", $tanggalKeluar);
        $statement->bindValue(":kuantitas", $kuantitas);
        $statement->bindValue(":total_harga", $totalHarga);
        $statement->bindValue(":id_pembeli", $idPembeli);
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
        if(!$isOk){
            $reply['error'] = $statement->errorInfo();
            http_response_code(400);
        }

    } else {
        echo"Obat yang di beli tidak mempunyai stok yang cukup";
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
$getResult = "SELECT * FROM barang_keluar WHERE id_keluar = :id_keluar";
$stm = $connection->prepare($getResult);
$stm->bindValue(':id_keluar', $lastId);
$stm->execute();
$result = $stm->fetch(PDO::FETCH_ASSOC);


/**
 * Show output to client
 * Set status info true
 */
$reply['data'] = $result;
$reply['status'] = $isOk;
echo json_encode($reply);
