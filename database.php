<!-- Christhoper Marcelino Mamahit -->
<!-- BFLP IT 24 -->
 
 <?php

function getDBConnection()
{
    $server_name = '127.0.0.1';
    $username = 'root';
    $password = 'root';
    $db_name = 'bflp_bootcamp';

    try {
        $conn = new mysqli($server_name, $username, $password, $db_name);
        return $conn;
    } catch(Exception $_) {
        $_SESSION['alert-failed'] = "Terjadi kesalahan sistem. Koneksi database gagal";
        return null;
    }
}
