<?php

namespace Dijkma\QRReader;

use Dijkma\QRReader\lib\Luminance\GDLuminanceSource;
use \Imagick;
use Dijkma\QRReader\lib\BinaryBitmap;
use Dijkma\QRReader\lib\Binarizer\HybridBinarizer;
use Dijkma\QRReader\lib\Luminance\IMagickLuminanceSource;
use Dijkma\QRReader\lib\Qrcode\QRCodeReader;

class QrReader{

    const SOURCE_TYPE_FILE = 'file';
    const SOURCE_TYPE_BLOB = 'blob';
    const SOURCE_TYPE_RESOURCE = 'resource';

    public $result;

    public function __construct($imgsource, $sourcetype = QrReader::SOURCE_TYPE_FILE){

        //check if imagick is available. We prefert it over the GD lib.
        $imagick = extension_loaded('imagick');
        //To make sure there is a image handling libary. we check if GD is available.
        $gd = extension_loaded('gd');

        if($imagick) {
            $im = $this->handleImagick($imgsource, $sourcetype);
            $width = $im->getImageWidth();
            $height = $im->getImageHeight();

            $source = new IMagickLuminanceSource($im, $width, $height);
        }
        elseif($gd) {
            $im = $this->handleGD($imgsource, $sourcetype);
            $width = imagesx($im);
            $height = imagesy($im);

            $source = new GDLuminanceSource($im, $width, $height);
        }
        else{
            throw new \Exception('No image libarie available. MAke sure GD or Imagick is installed');
        }

        $histo = new HybridBinarizer($source);
        $bitmap = new BinaryBitmap($histo);
        $reader = new QRCodeReader();

        $this->result = $reader->decode($bitmap);
    }

    public function text(){
        if(method_exists($this->result,'toString')) {
            return  ($this->result->toString());
        }else{
            return $this->result;
        }
    }

    public function decode(){
        $this->text();
    }

    /**
     * @param $imgsource string. The image source
     * @param $sourcetype string. What kind of image
     * @return bool|Imagick
     * @throws \Exception
     */
    private function handleImagick($imgsource, $sourcetype){
        $im = new Imagick();
        switch($sourcetype) {
            case QrReader::SOURCE_TYPE_FILE:
                return $im->readImage($imgsource);
                break;

            case QrReader::SOURCE_TYPE_BLOB:
                return $im->readimageblob($imgsource);
                break;

            case QrReader::SOURCE_TYPE_RESOURCE:
                return $im = $imgsource;
                break;
        }
        throw new \Exception('Imagick is not able to handle the image.');
    }

    /**
     * @param $imgsource string. The image source
     * @param $sourcetype string. What kind of image
     * @return resource|string
     * @throws \Exception
     */
    private function handleGD($imgsource, $sourcetype){
        switch($sourcetype) {
            case QrReader::SOURCE_TYPE_FILE:
                return imagecreatefromstring(file_get_contents($imgsource));
                break;

            case QrReader::SOURCE_TYPE_BLOB:
                return imagecreatefromstring($imgsource);
                break;

            case QrReader::SOURCE_TYPE_RESOURCE:
                return $im = $imgsource;
                break;
        }
        throw new \Exception('GD is not able to handle the image.');
    }
}