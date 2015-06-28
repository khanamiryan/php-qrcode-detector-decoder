# php-qrcode-detector-decoder
Php library to detect and decode QR-codes


Ported from ZXing library

How to use:
--------------
    include_once('./lib/QrReader.php');
    $qrcode = new QrReader('path/to_image');
    print $text = $qrcode->text();
