<?php
    require 'ShipInfo.php';
    echo(json_encode(ShipInfo::GetAllShips()));
?>