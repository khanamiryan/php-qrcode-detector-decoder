<?php

include_once ('Reader.php');
require_once ('BinaryBitmap.php');
require_once ('common/detector/MathUtils.php');
require_once ('common/BitMatrix.php');
require_once ('common/BitSource.php');
require_once ('common/BitArray.php');
require_once ('BinaryBitmap.php');
include_once ('LuminanceSource.php');
include_once ('GDLuminanceSource.php');
include_once ('IMagickLuminanceSource.php');
include_once ('common/customFunctions.php');
include_once ('common/PerspectiveTransform.php');
include_once ('common/GridSampler.php');
include_once ('common/DefaultGridSampler.php');
include_once ('common/DetectorResult.php');
require_once ('common/reedsolomon/GenericGFPoly.php');
require_once ('common/reedsolomon/GenericGF.php');
include_once ('common/reedsolomon/ReedSolomonDecoder.php');
include_once ('common/reedsolomon/ReedSolomonException.php');

include_once ('qrcode/decoder/Decoder.php');
include_once ('ReaderException.php');
include_once ('NotFoundException.php');
include_once ('FormatException.php');
include_once ('ChecksumException.php');
include_once ('qrcode/detector/FinderPatternInfo.php');
include_once ('qrcode/detector/FinderPatternFinder.php');
include_once ('ResultPoint.php');
include_once ('qrcode/detector/FinderPattern.php');
include_once ('qrcode/detector/AlignmentPatternFinder.php');
include_once ('qrcode/detector/AlignmentPattern.php');
include_once ('qrcode/decoder/Version.php');
include_once ('qrcode/decoder/BitMatrixParser.php');
include_once ('qrcode/decoder/FormatInformation.php');
include_once ('qrcode/decoder/ErrorCorrectionLevel.php');
include_once ('qrcode/decoder/DataMask.php');
include_once ('qrcode/decoder/DataBlock.php');
include_once ('qrcode/decoder/DecodedBitStreamParser.php');
include_once ('qrcode/decoder/Mode.php');
include_once ('common/DecoderResult.php');
include_once ('Result.php');
include_once ('Binarizer.php');
include_once ('common/GlobalHistogramBinarizer.php');
include_once ('common/HybridBinarizer.php');


final class QrReader
{
    public $result;

    function __construct($filename)
    {

        try {

            if(extension_loaded('imagick')) {
                $im = new Imagick();
                $im->readImage($filename);
                $width = $im->getImageWidth();
                $height = $im->getImageHeight();
                $source = new \Zxing\IMagickLuminanceSource($im, $width, $height);
            }else {
                $image = file_get_contents($filename);
                $sizes = getimagesize($filename);
                $width = $sizes[0];
                $height = $sizes[1];
                $im = imagecreatefromstring($image);

                $source = new \Zxing\GDLuminanceSource($im, $width, $height);
            }
            $histo = new Zxing\Common\HybridBinarizer($source);
            $bitmap = new Zxing\BinaryBitmap($histo);
            $reader = new Zxing\Qrcode\QRCodeReader();

            $this->result = $reader->decode($bitmap);
        }catch (\Zxing\NotFoundException $er){
            $this->result = false;
        }catch( \Zxing\FormatException $er){
            $this->result = false;
        }catch( \Zxing\ChecksumException $er){
            $this->result = false;
        }
    }

    public function text()
    {
        if(method_exists($this->result,'toString')) {
            return  ($this->result->toString());
        }else{
            return $this->result;
        }
    }

}

