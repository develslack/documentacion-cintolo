<?php
// view_pdf.php
if (isset($_GET['file'])) {
    $file = $_GET['file'];

    // Validamos que el archivo exista y sea un PDF por seguridad
    if (file_exists($file) && strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'pdf') {
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($file) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        readfile($file);
        exit;
    }
}
echo "Archivo no encontrado.";
?>
