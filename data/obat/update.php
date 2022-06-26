<?php

/**
 * @var $connection PDO
 */

require_once('../../config/koneksi.php');

/*
 * Validate http method
 */
//if($_SERVER['REQUEST_METHOD'] !== 'PATCH'){
//    header('Content-Type: application/json');
//    http_response_code(400);
//    $reply['error'] = 'PATCH method required';
//    echo json_encode($reply);
//    exit();
//}

//$formData = [];
//parse_str(file_get_contents('php://input'), $formData);

$idObat = $_POST['id_obat'] ?? '';
$namaObat = $_POST['nama_obat'] ?? '';
$gambarObat = $_FILES['gambar_obat']['name'] ?? '';
$hargaJual =$_POST['harga_jual'] ?? '';
$hargaBeli = $_POST['harga_beli'] ?? '';
$stok = $_POST['stok'] ?? '';

//var_dump($idObat);
//die();

/**
 * METHOD OK
 * Validation OK
 * Check if data is exist
 */

try{

    $queryCheck = "SELECT * FROM obat where id_obat = :id_obat";
    $statement = $connection->prepare($queryCheck);
    $statement->bindValue(':id_obat', $idObat);
    $statement->execute();
    $row = $statement->rowCount();
    /**
     * Jika data tidak ditemukan
     * rowcount == 0
     */
    if($row === 0){
        $reply['error'] = 'Data tidak ditemukan di id obat '.$idObat;
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
if($gambarObat != "") {
    $ekstensi_diperbolehkan = array('png','jpg'); //ekstensi file gambar yang bisa diupload
    $x = explode('.', $gambarObat); //memisahkan nama file dengan ekstensi yang diupload
    $ekstensi = strtolower(end($x));
    $file_tmp = $_FILES['gambar_obat']['tmp_name'];
    $angka_acak     = rand(1,999);
    $nama_gambar_baru = $angka_acak.'-'.$gambarObat; //menggabungkan angka acak dengan nama file sebenarnya
    if(in_array($ekstensi, $ekstensi_diperbolehkan) === true)  {
        move_uploaded_file($file_tmp, 'gambar/'.$nama_gambar_baru); //memindah file gambar ke folder gambar
        // jalankan query INSERT untuk menambah data ke database pastikan sesuai urutan (id tidak perlu karena dibikin otomatis)
        try{
            $fields = [];
            $query = "UPDATE obat SET  nama_obat = :nama_obat, gambar_obat = :gambar_obat, harga_jual = :harga_jual, harga_beli = :harga_beli, stok = :stok  WHERE id_obat = :id_obat";
            $statement = $connection->prepare($query);
            /**
             * Bind params
             */
            $statement->bindValue(":id_obat", $idObat);
            $statement->bindValue(":nama_obat", $namaObat);
            $statement->bindValue(":gambar_obat", "$gambarObat");
            $statement->bindValue(":harga_jual", $hargaJual);
            $statement->bindValue(":harga_beli", $hargaBeli);
            $statement->bindValue(":stok", $stok);

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

    } else {
        //jika file ekstensi tidak jpg dan png maka alert ini yang tampil
        echo "Ekstensi gambar yang boleh hanya jpg atau png";
    }
}



/**
 * Show output to client
 */
$reply['status'] = $isOk;
echo json_encode($reply);