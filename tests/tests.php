<?php
    require dirname(__FILE__) . '/../php/ShipInfo.php';

    class tests extends PHPUnit\Framework\TestCase
    { }
    // We do that so PHP Unit load the empty class but still do the other tests

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
    $arr = array("Fumizuki", "Akagi", "Ryuujou", "Kisaragi", "Akatsuki", "I-19", "Warspite", "Ark Royal", "Akashi", "Zuikaku"); // We try with a lot of random ships
    foreach ($arr as $elem) {
        CheckShipExist($elem);
    }
?>