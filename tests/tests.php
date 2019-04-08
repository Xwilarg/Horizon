<?php
    require dirname(__FILE__) . '/../php/ShipInfo.php';
    function CheckShipExist($name) {
        $kancolle = ShipInfo::GetKancolleInfo($name);
        $azurLane = ShipInfo::GetAzurLaneInfo($name);
        foreach ($kancolle as $elem) {
            PHPUnit\Framework\Assert::assertNotNull($elem, "Kancolle, missing data for " . $name);
        }
        foreach ($azurLane as $elem) {
            PHPUnit\Framework\Assert::assertNotNull($elem, "Azur Lane, missing data for " . $name);
        }
    }
    CheckShipExist("Fumizuki");
    CheckShipExist("Akagi");
?>