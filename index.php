<?php

require __DIR__ . "/vendor/autoload.php";





$dir = scandir('qrcodes');
$ignoredFiles = array(
	'.',
	'..',
	'.DS_Store'
);
foreach($dir as $file) {
    if(in_array($file, $ignoredFiles)) continue;

    print $file;
    print ' --- ';
    $qrcode = new QrReader('qrcodes/'.$file);
    print $text = $qrcode->text();
    print "<br/>";
}
