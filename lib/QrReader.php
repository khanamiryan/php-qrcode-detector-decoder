<?php

namespace Zxing;

use Zxing\Common\HybridBinarizer;

final class QrReader
{
    const SOURCE_TYPE_FILE     = 'file';
    const SOURCE_TYPE_BLOB     = 'blob';
    const SOURCE_TYPE_RESOURCE = 'resource';
    public $result;

    public function __construct($imgsource, $sourcetype = QrReader::SOURCE_TYPE_FILE, $isUseImagickIfAvailable = true)
    {
        try {
            $time = microtime(true);
            switch ($sourcetype) {
                case QrReader::SOURCE_TYPE_FILE:
                    if ($isUseImagickIfAvailable && extension_loaded('imagick')) {
                        $im = new \Imagick();
                        $im->readImage($imgsource);
                        $im->rotateImage('#00000000', 180);
                        $im->cropImage(500, 500, 0, 0);
//                        $im->writeImage('test.jpg');
                    } else {
                        $image = file_get_contents($imgsource);
                        $im    = imagecreatefromstring($image);
                    }
                    break;

                case QrReader::SOURCE_TYPE_BLOB:
                    if ($isUseImagickIfAvailable && extension_loaded('imagick')) {
                        $im = new \Imagick();
                        $im->readimageblob($imgsource);
                    } else {
                        $im = imagecreatefromstring($imgsource);
                    }
                    break;

                case QrReader::SOURCE_TYPE_RESOURCE:
                    $im = $imgsource;
                    if ($isUseImagickIfAvailable && extension_loaded('imagick')) {
                        $isUseImagickIfAvailable = true;
                    } else {
                        $isUseImagickIfAvailable = false;
                    }
                    break;
            }
            if ($isUseImagickIfAvailable && extension_loaded('imagick')) {
                $width  = $im->getImageWidth();
                $height = $im->getImageHeight();
                $source = new IMagickLuminanceSource($im, $width, $height);
            } else {
                $width  = imagesx($im);
                $height = imagesy($im);
                $source = new GDLuminanceSource($im, $width, $height);
            }
            $histo  = new HybridBinarizer($source);
            $bitmap = new BinaryBitmap($histo);
            $reader = new \Zxing\Qrcode\QRCodeReader();

            echo 'init ', microtime(true) - $time, PHP_EOL;

            $this->result = $reader->decode($bitmap);
        } catch (\Zxing\NotFoundException $er) {
            $this->result = false;
        } catch (\Zxing\FormatException $er) {
            $this->result = false;
        } catch (\Zxing\ChecksumException $er) {
            $this->result = false;
        }
    }

    public function decode()
    {
        return $this->text();
    }

    public function text()
    {
        if (method_exists($this->result, 'toString')) {
            return ($this->result->toString());
        }

        return $this->result;
    }
}
