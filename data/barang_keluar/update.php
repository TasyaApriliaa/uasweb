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

$idKeluar = $formData['id_keluar'] ?? '';
$tanggalKeluar= $formData['tanggal_keluar'] ?? '';
$kuantitas = $formData['kuantitas'] ?? '';
$totalHarga = $formData['total_Harga'] ?? '';
$idPembeli = $formData['id_pembeli'] ?? '';
$idObat = $formData['id_obat'] ?? '';
$kuantitas_lama = $formData['kuantitas_lama'] ?? '';



/**
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */

try{

    $queryCheck = "SELECT * FROM barang_keluar where id_keluar = :id_keluar";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_keluar', $idKeluar);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan di id keluar '.$idKeluar;
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
    $stok = $replys['data']['stok'];
    $stoklama = $stok + $kuantitas_lama;

    if($stoklama >= $kuantitas ) {
//        var_dump($replys['data']);
//        die;
        $hargaJual = $replys['data']['harga_jual'];
        $totalHarga = $hargaJual * $kuantitas;
        $stokTerbaru = $stoklama - $kuantitas;
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
        $query = "UPDATE barang_keluar SET tanggal_keluar = :tanggal_keluar , kuantitas = :kuantitas, total_harga = :total_harga, id_pembeli = :id_pembeli, id_obat = :id_obat  WHERE id_keluar = :id_keluar";
        $statementBarang = $connection->prepare($query);
        /**
         * Bind params
         */
        $statementBarang->bindValue(":id_keluar", $idKeluar);
        $statementBarang->bindValue(":tanggal_keluar", $tanggalKeluar);
        $statementBarang->bindValue(":kuantitas", $kuantitas);
        $statementBarang->bindValue(":total_harga", $totalHarga);
        $statementBarang->bindValue(":id_pembeli", $idPembeli);
        $statementBarang->bindValue(":id_obat", $idObat);

        /**
         * Execute query
         */
        $isOk = $statementBarang->execute();
//        var_dump($isOk);
//        die();
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