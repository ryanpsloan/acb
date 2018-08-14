<?php
/**
 * Author: Ryan Sloan
 * ryan@paydayinc.com
 * This page processes the request to download the .csv file createDoc.php updates and prepares.
 *
 */
session_start();
if(isset($_SESSION['isolvedFileName'])) {
    $file = $_SESSION['isolvedFileName'];

    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    }

}

?>