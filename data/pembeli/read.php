<?php
/**
 * @var $connection PDO
 */

require_once('../../config/koneksi.php');

try{
    /**
     * Prepare query limit 50 rows
     */
    $statement = $connection->prepare("select * from pembeli order by id_pembeli desc");
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