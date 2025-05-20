<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['texto'])) {
    $texto = urlencode($_POST['texto']);
    $qrURL = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=$texto";
    header('Content-Type: image/png');
    header('Content-Disposition: attachment; filename="codigo_qr.png"');
    echo file_get_contents($qrURL);
    exit;
}
?>
