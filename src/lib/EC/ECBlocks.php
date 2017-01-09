<?php
/**
 * Created by PhpStorm.
 * User: jelle
 * Date: 2-1-2017
 * Time: 12:14
 */

namespace Dijkma\QRReader\lib\EC;

/**
 * <p>Encapsulates a set of error-correction blocks in one symbol version. Most versions will
 * use blocks of differing sizes within one version, so, this encapsulates the parameters for
 * each set of blocks. It also holds the number of error-correction codewords per block since it
 * will be the same across all blocks within one version.</p>
 */
final class ECBlocks{
    private $ecCodewordsPerBlock;
    private $ecBlocks;

    /**
     * ECBlocks constructor.
     * @param $ecCodewordsPerBlock int
     * @param $ecBlocks array
     */
    function __construct($ecCodewordsPerBlock, $ecBlocks)
    {
        $this->ecCodewordsPerBlock = $ecCodewordsPerBlock;
        $this->ecBlocks = $ecBlocks;
    }

    public function getECCodewordsPerBlock()
    {
        return $this->ecCodewordsPerBlock;
    }

    public function getNumBlocks()
    {
        $total = 0;
        foreach ($this->ecBlocks as $ecBlock) {
            $total += $ecBlock->getCount();
        }
        return $total;
    }

    public function getTotalECCodewords()
    {
        return $this->ecCodewordsPerBlock * $this->getNumBlocks();
    }

    public function getECBlocks()
    {
        return $this->ecBlocks;
    }
}