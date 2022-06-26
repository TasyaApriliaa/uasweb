<?php

require_once('../../config/koneksi.php');

session_start();
if( $_SESSION['username']){
    session_unset();
    session_destroy();

    $reply['data'] = "Logout Berhasil!";
    echo json_encode($reply);

} else{
    $reply['data'] = "Session Tidak Terdaftar!";
    json_encode($reply);
}




