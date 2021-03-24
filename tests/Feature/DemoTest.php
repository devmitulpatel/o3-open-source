<?php
function calcAddress($base=1200, $rowlb=0,   $collb=0,  $rowub=2,    $colub=2,    $elementsize=1)
{


echo "For array a[$rowlb:$rowub,$collb:$colub] with element size $elementsize";
echo "\n";

    $j=$collb;
$i=$rowlb;


$a=$base;
$n=$rowub;
$b=(($rowlb*$n)+$collb)* $elementsize;


for ($x=0;$x<100;$x++){
    if( $j<=$rowub && $i<=$colub) {
        $c=((($j*$n)+$i)*$elementsize);
        $value = $a - $b + $c;
        $value=$base;
        $base=$base+$elementsize;
        echo "a[" . implode(',', [$i, $j]) . "] address = ".$value ;
        echo "\n";
    }else{
        break;
    }
    if($j<=$rowub)$j++;
    if($i<=$colub && $j>$rowub) {
        $j=$rowlb;
        $i++;
    }
}
}

calcAddress(1200, 0,   0,  2,    2,    1);
calcAddress(100, 1,1,2,2,2);
calcAddress(100, 2, 3, 4,5,4);
calcAddress(100, -1, -1, 1, 2, 8);

