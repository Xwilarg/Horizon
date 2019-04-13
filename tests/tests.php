<?php
    require dirname(__FILE__) . '/../php/ShipInfo.php';

    class tests extends PHPUnit\Framework\TestCase
    { }
    // We do that so PHP Unit load the empty class but still do the other tests

    function CheckKancolleExist($name) {
        $kancolle = ShipInfo::GetKancolleInfo($name);
        foreach ($kancolle as $elem) {
            PHPUnit\Framework\Assert::assertNotNull($elem, "Kancolle, missing data for " . $name);
        }
    }

    function CheckAzurLaneExist($name) {
        $azurLane = ShipInfo::GetAzurLaneInfo($name);
        foreach ($azurLane as $elem) {
            PHPUnit\Framework\Assert::assertNotNull($elem, "Azur Lane, missing data for " . $name);
        }
    }

    function CheckShipExist($name) {
        CheckKancolleExist($name);
        CheckAzurLaneExist($name);
    }

    // We try with a lot of random ships
    $arr = array("Fumizuki", "Akagi", "Ryuujou", "Kisaragi", "Akatsuki", "I-19", "Warspite", "Ark Royal", "Akashi", "Zuikaku");
    foreach ($arr as $elem) {
        CheckShipExist($elem);
    }

    $arr = array("33", "Unicorn", "U-557", "Blanc", "Eldridge", "Köln");
    foreach ($arr as $elem) {
        CheckAzurLaneExist($elem);
    }

    $arr = array("Taigei", "I-168", "Kikuzuki", "Zuihou", "Etorofu", "Shinyou");
    foreach ($arr as $elem) {
        CheckKancolleExist($elem);
    }
?>