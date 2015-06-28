<?php
function arraycopy($srcArray,$srcPos,$destArray, $destPos, $length){//System.arraycopy

    $srcArrayToCopy = array_slice($srcArray,$srcPos,$length);
    array_splice($destArray,$destPos,$length,$srcArrayToCopy);
    return $destArray;
}


function overflow32($value) {
    $value = $value % 4294967296;
    if ($value > 2147483647) {
        return $value - 4294967296;
    } elseif ($value < -2147483648) {
        return $value + 4294967296;
    } else {
        return $value;
    } // if
}  // overflow32()

function hashCode( $s )
{
    $h = 0;
    $len = strlen($s);
    for($i = 0; $i < $len; $i++)
    {
        $h = overflow32(31 * $h + ord($s[$i]));
    }

    return $h;
}


function numberOfTrailingZeros($i) {
    if ($i == 0) return 32;
    (int)$num = 0;
    while (($i & 1) == 0) {
        $i >>= 1;
        $num ++;
    }
    return $num;
}
function intval32bits($value)
{

    $value = ($value & 0xFFFFFFFF);

    if ($value & 0x80000000)
        $value = -((~$value & 0xFFFFFFFF) + 1);

    return $value;
}

function uRShift($a, $b)
{

    if($b == 0) return $a;
    return ($a >> $b) & ~(1<<(8*PHP_INT_SIZE-1)>>($b-1));
}
/*
function sdvig3($num,$count=1){//>>> 32 bit
    $s = decbin($num);

    $sarray  = str_split($s,1);
    $sarray = array_slice($sarray,-32);//32bit

    for($i=0;$i<=1;$i++) {
        array_pop($sarray);
        array_unshift($sarray, '0');
    }
    return bindec(implode($sarray));
}
*/

function sdvig3($a,$b) {
    return $a>>$b;
    if ($a >= 0) {
        return bindec(decbin($a>>$b)); //simply right shift for positive number
    }

    $bin = decbin($a>>$b);

    $bin = substr($bin, $b); // zero fill on the left side

    $o = bindec($bin);
    return $o;
}

function floatToIntBits($float_val)
{
    $int = unpack('i', pack('f', $float_val));
    return $int[1];
}

function fill_array($index,$count,$value){
    if($count<=0){
        return array(0);
    }else {
        return array_fill($index, $count, $value);
    }
}