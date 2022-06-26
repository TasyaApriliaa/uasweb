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

$idMasuk = $formData['id_masuk'] ?? '';
$tanggalMasuk = $formData['tanggal_masuk'] ?? '';
$kuantitas = $formData['kuantitas'] ?? '';
$totaBeli = $formData['total_beli'] ?? '';
$idSupplier = $formData['id_supplier'] ?? '';
$idObat = $formData['id_obat'] ?? '';
$kuantitas_lama = $formData['kuantitas_lama'] ?? '';





/**
 * METHOD OK
 * Validation OK
 * Check if data is exist
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
        $reply['error'] = 'Data tidak ditemukan di id masuk '.$idMasuk;
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
    $getObat = "SELECT * FROM obat WHERE id_obat = :id_obat";
    $stmobat = $connection->prepare($getObat);
    $stmobat->bindValue(':id_obat', $idObat);
    $stmobat->execute();
    $obat = $stmobat->fetch(PDO::FETCH_ASSOC);

    $replys['data'] = $obat;
    if($replys['data'] ) {
//        var_dump($replys['data']);
//        die;
        $hargaBeli = $replys['data']['harga_beli'];
        $stok = $replys['data']['stok'];
        $totalBeli = $hargaBeli * $kuantitas;
        $stoklama = $stok - $kuantitas_lama;
        $stokTerbaru = $stoklama + $kuantitas;
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

        $fields = [];
        $query = "UPDATE barang_masuk SET tanggal_masuk = :tanggal_masuk, kuantitas = :kuantitas, total_beli = :total_beli, id_supplier = :id_supplier, id_obat = :id_obat  WHERE id_masuk = :id_masuk";
        $statement = $connection->prepare($query);
        /**
         * Bind params
         */
        $statement->bindValue(":id_masuk", $idMasuk);
        $statement->bindValue(":tanggal_masuk", $tanggalMasuk);
        $statement->bindValue(":kuantitas", $kuantitas);
        $statement->bindValue(":total_beli", $totaBeli);
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
    }
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}


/**
 * Show output to client
 */
$reply['status'] = $isOk;
echo json_encode($reply);