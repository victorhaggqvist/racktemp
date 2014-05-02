<?php
require_once('lib/config.inc');
require_once(LIB_PATH.'/head.inc');

define('PCHART_PATH','lib/pChart/');
require_once(PCHART_PATH.'class/pData.class.php');
require_once(PCHART_PATH.'class/pDraw.class.php');
require_once(PCHART_PATH.'class/pImage.class.php');

/* Create and populate the pData object */
$MyData = new pData();

$sensCont = new Snilius\SensorController();
$sensors = $sensCont->getSensors();
foreach ($sensors as $sensor) {
  $sn = new Snilius\Sensor($sensor['name']);
  $list=$sn->getTempList('hour');
  $MyData->addPoints($list,$sensor['name']);
}

$clockPoints = array();

for ($i = 5; $i <= 60; $i+=5) {
  if ($i<10)
    $clockPoints[]='0'.$i;
  else
    $clockPoints[]=$i;
}

$MyData->addPoints($clockPoints,"Labels");
$MyData->setSerieDescription("Labels","Months");
$MyData->setAbscissa("Labels");


/* Create the pChart object */
$myPicture = new pImage(300,185,$MyData);

/* Turn of Antialiasing */
$myPicture->Antialias = false;

/* Write the chart title */
//$myPicture->setFontProperties(array("FontName"=>PCHART_PATH."fonts/Forgotte.ttf","FontSize"=>11));
//$myPicture->drawText(150,35,"Last hour",array("FontSize"=>20,"Align"=>TEXT_ALIGN_BOTTOMMIDDLE));

/* Set the default font */
$myPicture->setFontProperties(array("FontName"=>PCHART_PATH."fonts/pf_arma_five.ttf","FontSize"=>6));

/* Define the chart area */
$myPicture->setGraphArea(25,5,290,150);

/* Draw the scale */
$scaleSettings = array("XMargin"=>10,"YMargin"=>10,"Floating"=>false,"GridR"=>200,"GridG"=>200,"GridB"=>200,"CycleBackground"=>false);
$myPicture->drawScale($scaleSettings);

//$myPicture->drawSplineChart();

/* Turn on Antialiasing */
//$myPicture->Antialias = TRUE;

/* Draw the line chart */
$myPicture->drawLineChart();

/* Write the chart legend */
$myPicture->drawLegend(40,170,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));

/* Render the picture (choose the best way) */
$myPicture->Stroke();
?>