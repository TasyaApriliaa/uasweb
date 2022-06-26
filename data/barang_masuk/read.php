<?php
/**
 * @var $connection PDO
 */

require_once('../../config/koneksi.php');

try{
    /**
     * Prepare query limit 50 rows
     */
    $statement = $connection->prepare("select bm.id_masuk, bm.tanggal_masuk, o.nama_obat, o.gambar_obat, bm.kuantitas, o.harga_beli, bm.total_beli, s.nama_supplier, s.alamat from barang_masuk bm INNER join obat o on o.id_obat = bm.id_obat INNER join supplier s on s.id_supplier = bm.id_supplier;");
    $isOk = $statement->execute();
    $results = $statement->fetchAll(PDO::FETCH_ASSOC);
    $reply['data'] = $results;
}catch (Exception $exception){
    $reply['error'] = $exception->getMessage();
    echo json_encode($reply);
    http_response_code(400);
    exit(0);
}

if(!$isOk){
    $reply['error'] = $statement->errorInfo();
    http_response_code(400);
}
/*
 * Query OK
 * set status == true
 * Output JSON
 */
$reply['status'] = true;
echo json_encode($reply);