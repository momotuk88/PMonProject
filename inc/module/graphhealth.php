<?php
if (!defined('PONMONITOR')){
	die('Hacking attempt!');
}
$id = (isset($_GET['id']) ? (int)$_GET['id'] : null);
if($id){
$sqlalldevice = $db->SimpleWhile('SELECT * FROM `monitoring` WHERE deviceid = '.$id.' AND datetime  >= curdate() ORDER BY datetime ASC');
if(count($sqlalldevice)){
	foreach($sqlalldevice as $mon){
		if($mon['types']=='com1'){
			$temp = unserialize($mon['values']);
			$clocks = new DateTime($mon['datetime']);
			$DATA['x'][] = date_format($clocks, 'H:i');
			$DATA[0][] = (!empty($temp['cpu'])?$temp['cpu']:0);	
			$DATA[1][] = (!empty($temp['temp'])?$temp['temp']:0);
			$DATA[2][] = 0;
			$DATA[3][] = 0;
		}
	}
if(is_array($DATA)){
// Задаем изменяемые значения #######################################
// Размер изображения
$W=600;
$H=250;
// Отступы
$MB=30;  // Нижний
$ML=8;   // Левый 
$M=15;    // Верхний и правый отступы.
         // Они меньше, так как там нет текста
// Ширина одного символа
$LW = imagefontwidth(1);
// Подсчитаем количество элементов (точек) на графике
$count = count($DATA[0]);
if (count($DATA[1])>$count) $count=count($DATA[1]);
if (count($DATA[2])>$count) $count=count($DATA[2]);
#if (count($DATA[3])>$count) $count=count($DATA[3]);
if ($count==0) $count=1;
// Сглаживаем графики ###############################################
if ($_GET["smooth"]==1) {
    // Добавим по две точки справа и слева от графиков. Значения в
    // этих точках примем равными крайним. Например, точка если
    // y[0]=16 и y[n]=17, то y[1]=16 и y[-2]=16 и y[n+1]=17 и y[n+2]=17
    // Такое добавление точек необходимо для сглаживания точек
    // в краях графика
    for ($j=0;$j<3;$j++) {
        $DATA[$j][-1]=$DATA[$j][-2]=$DATA[$j][0];
        $DATA[$j][$count]=$DATA[$j][$count+1]=$DATA[$j][$count-1];
    }
    // Сглаживание графики методом усреднения соседних значений
    for ($i=0;$i<$count;$i++) {
        for ($j=0;$j<3;$j++) {
            $DATA[$j][$i]=($DATA[$j][$i-1]+$DATA[$j][$i-2]+
                           $DATA[$j][$i]+$DATA[$j][$i+1]+
                           $DATA[$j][$i+2])/5;
            }
        }
    }
// Подсчитаем максимальное значение
$max=0;
for ($i=0;$i<$count;$i++) {
    $max=$max<$DATA[0][$i]?$DATA[0][$i]:$max;
    $max=$max<$DATA[1][$i]?$DATA[1][$i]:$max;
    $max=$max<$DATA[2][$i]?$DATA[2][$i]:$max;
    #$max=$max<$DATA[3][$i]?$DATA[3][$i]:$max;
}
// Увеличим максимальное значение на 10% (для того, чтобы столбик
// соответствующий максимальному значение не упирался в в границу
// графика
$max = intval($max+($max/3));
// Количество подписей и горизонтальных линий
// сетки по оси Y.
$county=10;
// Работа с изображением ############################################
// Создадим изображение
$im=imagecreate($W,$H);
$bg[0]=imagecolorallocate($im,255,255,255); # ФОН НАВКОЛО ГРАФІКА
$bg[1]=imagecolorallocate($im,254,253,255); // Цвет задней грани графика (светло-серый)
$bg[2]=imagecolorallocate($im,206,223,239); // Цвет левой грани графика (серый)
$c=imagecolorallocate($im,250,252,254);		# ФОН ГРАФІКА
$text=imagecolorallocate($im,0,0,0);// Цвет текста (темно-серый)
// Цвета для линий графиков
$bar[0]=imagecolorallocate($im,0,148,255);
$bar[1]=imagecolorallocate($im,255,106,0);
$bar[2]=imagecolorallocate($im,239,239,239);
#$bar[3]=imagecolorallocate($im,239,239,239);
$text_width=15;
// Вывод подписей по оси Y
for ($i=0;$i<=$county;$i++) {
    $strl=strlen(($max/$county)*$i)*$LW;
    if ($strl>$text_width) $text_width=$strl;
}
// Подравняем левую границу с учетом ширины подписей по оси Y
$ML+= $text_width;
// Посчитаем реальные размеры графика (за вычетом подписей и
// отступов)
$RW = $W-$ML-$M;
$RH = $H-$MB-$M;
// Посчитаем координаты нуля
$X0 = $ML;
$Y0 =$H-$MB;
$step=$RH/$county;
// Вывод главной рамки графика
imagefilledrectangle($im, $X0, $Y0-$RH, $X0+$RW, $Y0, $bg[1]);
imagerectangle($im, $X0, $Y0, $X0+$RW, $Y0-$RH, $c);
// Вывод сетки по оси Y
for ($i=1;$i<=$county;$i++) {
    $y=$Y0-$step*$i;
    imageline($im,$X0,$y,$X0+$RW,$y,$c);
    imageline($im,$X0,$y,$X0-($ML-$text_width)/4,$y,$text);
}
// Вывод сетки по оси X
// Вывод изменяемой сетки
for ($i=0;$i<$count;$i++) {
    imageline($im,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count),$Y0,$c);
    imageline($im,$X0+$i*($RW/$count),$Y0,$X0+$i*($RW/$count),$Y0-$RH,$c);
}
// Вывод линий графика
$dx=($RW/$count)/2;
$pi=$Y0-($RH/$max*$DATA[0][0]);
$po=$Y0-($RH/$max*$DATA[1][0]);
$pu=$Y0-($RH/$max*$DATA[2][0]);
#$pz=$Y0-($RH/$max*$DATA[3][0]);
$px=intval($X0+$dx);
for ($i=1;$i<$count;$i++) {
    $x=intval($X0+$i*($RW/$count)+$dx);

    $y=$Y0-($RH/$max*$DATA[0][$i]);
    imageline($im,$px,$pi,$x,$y,$bar[0]);
    $pi=$y;

    $y=$Y0-($RH/$max*$DATA[1][$i]);
    imageline($im,$px,$po,$x,$y,$bar[1]);
    $po=$y;
	
    #$y=$Y0-($RH/$max*$DATA[3][$i]);
    #imageline($im,$px,$pz,$x,$y,$bar[3]);
    #$pz=$y;

    $y=$Y0-($RH/$max*$DATA[2][$i]);
    imageline($im,$px,$pu,$x,$y,$bar[2]);
    $pu=$y;
    $px=$x;
}
// Уменьшение и пересчет координат
$ML-=$text_width;
// Вывод подписей по оси Y
for ($i=1;$i<=$county;$i++) {
    $str=($max/$county)*$i;
    imagestring($im,2, $X0-strlen($str)*$LW-$ML/4-2,$Y0-$step*$i-
                 imagefontheight(2)/2,$str,$text);
}
// Вывод подписей по оси X
$prev = 100000;
$twidth=$LW*strlen($DATA["x"][0])+16;
$i=$X0+$RW;
while ($i>$X0) {
    if ($prev-$twidth>$i) {
        $drawx=$i-($RW/$count)/2;
        if ($drawx>$X0) {
            $str=$DATA["x"][round(($i-$X0)/($RW/$count))-1];
            imageline($im,$drawx,$Y0,$i-($RW/$count)/2,$Y0+5,$text);
            imagestring($im,2, $drawx-(strlen($str)*$LW)/2, $Y0+10,$str,$text);
            }
        $prev=$i;
        }
    $i-=$RW/$count;
}
header("Content-Type: image/png");
ImagePNG($im);
imagedestroy($im);
die;
}
}
}
die;
?>