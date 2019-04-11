<?php
class ShipInfo
{
    private static function GetContext() {
        return stream_context_create([
            "http" => [
                "method" => "GET",
                "header" => "User-Agent: Mozilla/5.0 horizon.zirk.eu\r\n"
            ]
        ]);
    }

    private static function IsInArray($elem, $array) {
        foreach ($array as $e) {
            if ($e[0] === $elem) {
                return true;
            }
        }
        return false;
    }

    public static function GetAllKancolleShips() {
        $context = ShipInfo::GetContext();
        $content =  explode("List of destroyers",
                        explode("Ship Type Identification",
                            file_get_contents("https://kancolle.fandom.com/wiki/Ship?action=raw", false, $context)
                        )[0]
                    )[1];
        preg_match_all('/\[\[([^\]]+)\]\]/', $content, $matches);
        $arr = array();
        foreach ($matches[1] as $elem) {
            if (substr($elem, 0, 7) !== "List of" && substr($elem, 0, 9) !== "Auxiliary") {
                // Some ships are in this format: [[U-511|U-511/Ro-500]]
                // So we remove the second "U-511" to pick only Ro-500
                // We also need to pay attention because some elements are dupplicated
                if (strpos($elem, "|") !== false) {
                    $allName = explode("|", $elem);
                    $refName = str_replace("'", "", $allName[0]);
                    if (strpos($allName[1], "/") !== false) {
                        $aliasName = explode("/", $allName[1]);
                    } else {
                        $aliasName = array($allName[1]);
                    }
                    foreach ($aliasName as $a) {
                        if (!ShipInfo::IsInArray($a, $arr) && $a !== $refName) {
                            array_push($arr, array(str_replace("'", "", $a), str_replace("'", "", $refName)));
                        }
                    }
                    if (!ShipInfo::IsInArray($a, $refName)) {
                        array_push($arr, array(str_replace("'", "", $refName)));
                    }
                } else if (!ShipInfo::IsInArray($elem, $arr)) {
                    array_push($arr, array(str_replace("'", "", $elem)));
                }
            }
        }
        return ($arr);
    }

    public static function GetAllAzurLaneShips() {
        $context = ShipInfo::GetContext();
        $content = file_get_contents("https://azurlane.koumakan.jp/List_of_Ships", false, $context);
        preg_match_all('/<a href="\/[^"]+" title="([^"]+)">[0-9]+<\/a>/', $content, $matches); // Backslash aren't properly detected, I don't know why
        $arr = array();
        foreach ($matches[1] as $elem) {
            if (!ShipInfo::IsInArray($elem, $arr)) {
                array_push($arr, array(str_replace("'", "", $elem)));
            }
        }
        return ($arr);
    }

    private static function RemoveUnwantedHtml($html) {
        return preg_replace("/<a [^>]+>([^<]+)+<\/a>/", "$1", $html);
    }

    public static function GetKancolleInfo($name) {
        $context = ShipInfo::GetContext();
        // base URL is the character page in the Wikia, url is to the gallery
        $json = json_decode(file_get_contents("https://kancolle.wikia.com/api/v1/Search/List?query=" . urlencode($name) . "&limit=1", false, $context));
        $baseUrl = $json->items[0]->url;
        $url = $baseUrl . "/Gallery";
        $kancolleName = str_replace(" ", "_", $json->items[0]->title);
        $kancolle = file_get_contents($url, false, $context);
        preg_match_all('/img src="([^"]+)"/', $kancolle, $matches);
        $kancolleImage = $matches[1][1]; // Character image
        $kancolleMain = file_get_contents($baseUrl, false, $context);
        // Get URL to audio file
        preg_match('/https:\/\/vignette\.wikia\.nocookie\.net\/kancolle\/images\/[^\/]+\/[^\/]+\/' . $kancolleName . '-Library\.ogg/', $kancolleMain, $matches);
        $kancolleAudio = $matches[0]; // Character intro voiceline
        // Description is right after the URL
        $library = explode($matches[0], $kancolleMain)[1];
        $kancolleJp = ShipInfo::RemoveUnwantedHtml(explode('</td>', explode('class="shipquote-ja">', $library)[1])[0]);
        $kancolleEn = ShipInfo::RemoveUnwantedHtml(explode('</td>', explode('class="shipquote-en">', $library)[1])[0]);
        return(array($kancolleImage, $kancolleAudio, $kancolleJp, $kancolleEn));
    }

    public static function GetAzurLaneInfo($name) {
        $context = ShipInfo::GetContext();
        // url is the character page in the wikia
        $azurName = str_replace(" ", "_", json_decode(file_get_contents("https://azurlane.koumakan.jp/w/api.php?action=opensearch&search=" . urlencode($name) . "&limit=1", false, $context))[1][0]);
        $url = "https://azurlane.koumakan.jp/" . $azurName;
        $azurLane = file_get_contents($url, false, $context);
        preg_match('/src="(\/w\/images\/thumb\/[^\/]+\/[^\/]+\/[^\/]+\/[0-9]+px-' . $azurName . '.png)/', $azurLane, $matches);
        $azurLaneImage = "https://azurlane.koumakan.jp" . $matches[1]; // Character image
        preg_match('/https:\/\/azurlane.koumakan.jp\/w\/images\/[^\/]+\/[^\/]+\/' . $azurName . '_SelfIntroJP\.ogg/', $azurLane, $matches);
        if (count($matches) === 0) {
            preg_match('/https:\/\/azurlane.koumakan.jp\/w\/images\/[^\/]+\/[^\/]+\/' . $azurName . '_SelfIntroCN\.ogg/', $azurLane, $matches);
        }
        $azurLaneAudio = $matches[0]; // Character intro voiceline
        $library = explode('Self Introduction', $azurLane);
        // There is two "Self Introduction", one in chinese, one in japanese.
        // By default we take the japanese one but if it's empty we fallback on the chinese one
        // (this is mostly the case for ships that only are in the chinese version, like 33)
        $libraryFirst = explode('<td>', $library[1]);
        $librarySecond = explode('<td>', $library[2]);
        $azurLaneJp = ShipInfo::RemoveUnwantedHtml(explode('</td>', $librarySecond[1])[0]);
        if (trim($azurLaneJp) === "")
            $azurLaneJp = ShipInfo::RemoveUnwantedHtml(explode('</td>', $libraryFirst[1])[0]);
        $azurLaneEn = ShipInfo::RemoveUnwantedHtml(explode('</td>', $librarySecond[2])[0]);
        if (trim($azurLaneEn) === "")
            $azurLaneEn = ShipInfo::RemoveUnwantedHtml(explode('</td>', $libraryFirst[2])[0]);
        return(array($azurLaneImage, $azurLaneAudio, $azurLaneJp, $azurLaneEn));
    }
}
?>
