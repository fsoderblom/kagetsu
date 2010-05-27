<?php 
include('inc/init.inc.php');
include('inc/objects.class.php');

$o = new objects($db);

$pixels = $o->getObjectCount();
$pixelsize = $o->getPixelSize();

$imgw = $o->getImageWidth();
$imgh = $o->getImageHeight();

$im = imagecreatetruecolor($imgw, $imgh);

$white      = imagecolorallocate($im, 255,255,255);
$green      = imagecolorallocate($im, 15, 50, 15);
$aqua       = imagecolorallocate($im, 0, 255, 255);
$black      = imagecolorallocate($im, 0, 0, 0);
$blue       = imagecolorallocate($im, 0,0,255);
$fuchsia    = imagecolorallocate($im, 255,0,255);
$gray       = imagecolorallocate($im, 128,128,128);
$lime       = imagecolorallocate($im, 0,100,0);
$maroon     = imagecolorallocate($im, 128,0,0);
$navy       = imagecolorallocate($im, 0,0,128);
$olive      = imagecolorallocate($im, 128,128,0);
$purple     = imagecolorallocate($im, 128,0,128);
$red        = imagecolorallocate($im, 255,0,0);
$silver     = imagecolorallocate($im, 192,192,192);
$teal       = imagecolorallocate($im, 0,128,128);
$yellow     = imagecolorallocate($im, 250,250,50);
$orange     = imagecolorallocate($im, 255,165,0);

imagefill($im, 0, 0, $green);

for($i=0; $i<$imgw; $i+=$pixelsize)
{
    imageline($im, $i-1, 0, $i-1, $imgw, $gray);
    imageline($im, 0, $i-1, $imgh, $i-1, $gray);
}

if($rs = $o->getObjectStatuses())
{
	$i=0;
	
	while(!$rs->EOF)
	{
		if($pos = $o->getObjectXY($i))
		{
			switch($rs->getColumn("status"))
			{
				case "998": 
					imagefill($im, $pos[0],$pos[1], $yellow);
//				    imageline($im, $pos[0]+1, $pos[1]+1, $pos[0]+2, $pos[1]+1, $black);
					break;
				case "999": 
					imagefill($im, $pos[0],$pos[1], $red);
//				    imageline($im, $pos[0]+1, $pos[1]+1, $pos[0]+2, $pos[1]+1, $white);
					break;
				default: 
					imagefill($im, $pos[0],$pos[1], $lime);
//				    imageline($im, $pos[0]+1, $pos[1]+1, $pos[0]+2, $pos[1]+1, $yellow);
					break;
			}
		}
		
		$i++;		
		$rs->nextRow();
	}
}


header("Content-type: image/gif");
imagepng($im);
imagedestroy($im);


?>