<?php
    require 'ShipInfo.php';
    $name = $_GET['name'];
    $kancolle = ShipInfo::GetKancolleInfo($name);
    $azurLane = ShipInfo::GetAzurLaneInfo($name);
    echo(json_encode(array($kancolle[0], $azurLane[0], $kancolle[1], $azurLane[1], $kancolle[2], $azurLane[2])));
?>