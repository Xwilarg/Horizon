<?php
    require 'ShipInfo.php';
    echo(json_encode(array(ShipInfo::GetAllKancolleShips(), ShipInfo::GetAllAzurLaneShips())));
?>