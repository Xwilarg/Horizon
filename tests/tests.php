<?php
    require dirname(__FILE__) . '/../php/ShipInfo.php';
    ShipInfo::Init();
    function CheckShipExist($name) {
        $kancolle = ShipInfo::GetKancolleInfo($name);
        $azurLane = ShipInfo::GetAzurLaneInfo($name);
        foreach ($kancolle as $elem) {
            PHPUnit\Framework\Assert::assertTrue($elem !== null);
        }
        foreach ($azurLane as $elem) {
            PHPUnit\Framework\Assert::assertTrue($elem !== null);
        }
    }
    CheckShipExist("Fumizuki");
    CheckShipExist("Akagi");
?>