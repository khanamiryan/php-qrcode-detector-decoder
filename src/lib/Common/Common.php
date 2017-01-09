<?php
/**
 * Created by PhpStorm.
 * User: jelle
 * Date: 2-1-2017
 * Time: 11:16
 */
namespace  Dijkma\QRReader\lib\Common;


class Common{

    public function overflow($v){
        switch(PHP_INT_SIZE) {
            case 4:
                return $this->overflow32($v);
                break;
            case 8:
                return $this->overflow64($v);
                break;
            default:
                return $this->overflow64($v);
        }
    }

    public function overflow32($v){
        $v = $v % 4294967296;
        if ($v > 2147483647) return $v - 4294967296;
        elseif ($v < -2147483648) return $v + 4294967296;
        else return $v;
    }

    //There is no need to overflow 64 bits to 32 bit
    public function overflow64($v) {
        return $v;
    }

    function arraycopy($srcArray,$srcPos,$destArray, $destPos, $length){
        $srcArrayToCopy = array_slice($srcArray,$srcPos,$length);
        array_splice($destArray,$destPos,$length,$srcArrayToCopy);
        return $destArray;
    }

    public function uRShift($a, $b){

        if($b == 0) return $a;
        return ($a >> $b) & ~(1<<(8*PHP_INT_SIZE-1)>>($b-1));
    }

    public function hashCode($s){
        $h = 0;
        $len = strlen($s);
        for($i = 0; $i < $len; $i++)
        {
            $h = overflow(31 * $h + ord($s[$i]));
        }

        return $h;
    }

    public function numberOfTrailingZeros($i){
        if ($i == 0) return 32;
        $num = 0;
        while (($i & 1) == 0) {
            $i >>= 1;
            $num++;
        }
        return $num;
    }

    public function intval($value){
        $value = ($value & 0xFFFFFFFF);

        if ($value & 0x80000000)
            $value = -((~$value & 0xFFFFFFFF) + 1);

        return $value;
    }

    public function sdvig3($a,$b){

        if ($a >= 0) {
            return bindec(decbin($a>>$b)); //simply right shift for positive number
        }

        $bin = decbin($a>>$b);

        $bin = substr($bin, $b); // zero fill on the left side

        $o = bindec($bin);
        return $o;
    }

    public function floatToIntBits($float_val){
        $int = unpack('i', pack('f', $float_val));
        return $int[1];
    }

    public function fill_array($index,$count,$value){
        if($count<=0){
            return array(0);
        }else {
            return array_fill($index, $count, $value);
        }
    }
}