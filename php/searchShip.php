<?php
    require 'ShipInfo.php';
    $name = $_GET['name'];
    $kancolle = ShipInfo::GetKancolleInfo($name);
    $azurLane = ShipInfo::GetAzurLaneInfo($name);
    $warshipGirls = ShipInfo::GetWarshipGirlsInfo($name);
    echo(json_encode(array(
        $kancolle[0], $azurLane[0], $warshipGirls[0],
        $kancolle[1], $azurLane[1], $warshipGirls[1],
        $kancolle[2], $azurLane[2], $warshipGirls[2],
        $kancolle[3], $azurLane[3], $warshipGirls[3],
        $kancolle[4], $azurLane[4], $warshipGirls[4])));
?>