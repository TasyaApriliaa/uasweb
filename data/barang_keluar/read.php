<?php
/**
 * @var $connection PDO
 */

require_once('../../config/koneksi.php');

try{
    /**
     * Prepare query limit 50 rows
     */
    $statement = $connection->prepare("select bk.id_keluar, bk.tanggal_keluar, o.nama_obat, o.gambar_obat, bk.kuantitas, o.harga_jual, bk.total_harga, pm.nama_pembeli, pm.alamat from barang_keluar bk INNER join obat o on o.id_obat = bk.id_obat INNER join pembeli pm on pm.id_pembeli = bk.id_pembeli;");
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