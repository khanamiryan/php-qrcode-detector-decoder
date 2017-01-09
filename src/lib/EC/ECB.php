<?php
/**
 * Created by PhpStorm.
 * User: jelle
 * Date: 2-1-2017
 * Time: 12:12
 */

namespace Dijkma\QRReader\lib\EC;

/**
 * <p>Encapsualtes the parameters for one error-correction block in one symbol version.
 * This includes the number of data codewords, and the number of times a block with these
 * parameters is used consecutively in the QR code version's format.</p>
 */
class ECB{
    private $count;
    private $dataCodewords;

    function __construct($count, $dataCodewords){
        $this->count = $count;
        $this->dataCodewords = $dataCodewords;
    }

    public function getCount(){
        return $this->count;
    }

    public function getDataCodewords(){
        return $this->dataCodewords;
    }

    /**
     * @throws \Exception
     */
    public function toString(){
        throw new \Exception('Version ECB toString()');
        //  return parent::$versionNumber;
    }


}