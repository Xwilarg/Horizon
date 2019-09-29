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
        $content =  explode("List_of_coastal_defense_ships_by_upgraded_maximum_stats",
                        explode("Fleet_of_Fog",
                            file_get_contents("https://kancolle.fandom.com/wiki/Ship", false, $context)
                        )[0]
                    )[1];
        preg_match_all('/<a href="\/wiki\/([^"]+)" title="[^"]+">([^<]+)<\/a>/', $content, $matches);
        $arr = array();
        for ($i = 0; $i < count($matches[0]); $i++) {
            $shipCmp = $matches[1][$i];
            if (substr($shipCmp, 0, 7) !== "List_of" && substr($shipCmp, 0, 9) !== "Category:") {
                $shipName = $matches[2][$i];
                // Some ships are in this format: Hibiki/Verniy
                if (strpos($shipName, "/") !== false) {
                    $names = explode("/", $shipName);
                    if (!ShipInfo::IsInArray($names[1], $arr)) {
                        array_push($arr, array(str_replace("'", "", $names[1]), str_replace("'", "", $names[0])));
                    }
                    if (!ShipInfo::IsInArray($names[0], $arr)) {
                        array_push($arr, array(str_replace("'", "", $names[0])));
                    }
                } else {
                    if (!ShipInfo::IsInArray($shipName, $arr)) {
                        array_push($arr, array(str_replace("'", "", $shipName)));
                    }
                }
            }
        }
        return ($arr);
    }

    public static function GetAllAzurLaneShips() {
        $context = ShipInfo::GetContext();
        $content = file_get_contents("https://azurlane.koumakan.jp/List_of_Ships", false, $context);
        preg_match_all('/"><a href="\/[^"]+" title="([^"]+)">(Collab|Plan)?[0-9]+<\/a>/', $content, $matches); // Backslash aren't properly detected, I don't know why
        $arr = array();
        foreach ($matches[1] as $elem) {
            if (!ShipInfo::IsInArray($elem, $arr)) {
                array_push($arr, array(str_replace("'", "", $elem)));
            }
        }
        return ($arr);
    }

    public static function GetAllWarshipGirlsShips() {
        $context = ShipInfo::GetContext();
        $content = file_get_contents("https://shipgirlfriends.moe/ShipList", false, $context);
        preg_match_all('/"en":"([^"]+)"/', $content, $matches); // Backslash aren't properly detected, I don't know why
        $arr = array();
        foreach ($matches[1] as $elem) {
            if (!ShipInfo::IsInArray($elem, $arr)) {
                array_push($arr, array(str_replace("ō", "ou", str_replace("ū", "uu", $elem))));
            }
        }
        return ($arr);
    }

    private static function RemoveUnwantedHtml($html) {
        return preg_replace("/<a [^>]+>([^<]+)+<\/a>/", "$1", $html);
    }

    private static function CompareRawString($s1, $s2) {
        return strtolower(preg_replace("/[^A-Za-z0-9]/", '', $s1)) === strtolower(preg_replace("/[^A-Za-z0-9]/", '', $s2));
    }

    public static function GetKancolleInfo($name) {
        $context = ShipInfo::GetContext();
        // base URL is the character page in the Wikia, url is to the gallery
        $json = json_decode(file_get_contents("https://kancolle.fandom.com/api/v1/Search/List?query=" . urlencode($name) . "&limit=1", false, $context));
        $baseUrl = $json->items[0]->url;
        $url = $baseUrl . "/Gallery";
        $kancolleName = str_replace(" ", "_", $json->items[0]->title);
        if (!ShipInfo::CompareRawString($kancolleName, $name))
            return (array(null, null, null, null));
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
        $kancolleNote = ShipInfo::RemoveUnwantedHtml(explode('</td>', explode('<td>', explode('class="shipquote-en">', $library)[1])[1])[0]);
        return(array($kancolleImage, $kancolleAudio, $kancolleJp, $kancolleEn, $kancolleNote));
    }

    public static function GetAzurLaneInfo($name) {
        $context = ShipInfo::GetContext();
        // url is the character page in the wikia
        $azurName = json_decode(file_get_contents("https://azurlane.koumakan.jp/w/api.php?action=opensearch&search=" . urlencode($name) . "&limit=1", false, $context))[1][0];
        if (!ShipInfo::CompareRawString($azurName, $name))
            return (array(null, null, null, null));
        $encodeName = urlencode($name);
        $encodeName = ucfirst(str_replace("+", "_", $encodeName));
        $url = "https://azurlane.koumakan.jp/" . $encodeName;
        $azurLane = file_get_contents($url, false, $context);
        preg_match('/src="(\/w\/images\/thumb\/[^\/]+\/[^\/]+\/[^\/]+\/[0-9]+px-' . $encodeName . '.png)/', $azurLane, $matches);
        $azurLaneImage = "https://azurlane.koumakan.jp" . $matches[1]; // Character image
        $azurLane = file_get_contents($url . "/Quotes", false, $context);
        preg_match('/https:\/\/azurlane.koumakan.jp\/w\/images\/[^\/]+\/[^\/]+\/' . $encodeName . '_SelfIntroJP\.ogg/', $azurLane, $matches);
        if (count($matches) === 0) {
            preg_match('/https:\/\/azurlane.koumakan.jp\/w\/images\/[^\/]+\/[^\/]+\/' . $encodeName . '_SelfIntroCN\.ogg/', $azurLane, $matches);
        }
        $azurLaneAudio = $matches[0]; // Character intro voiceline
        $azurLane = file_get_contents($url . "/Quotes?action=raw", false, $context);
        $library = explode('Server', $azurLane);
        // There is two "Self Introduction", one in chinese, one in japanese.
        // By default we take the japanese one but if it's empty we fallback on the chinese one
        // (this is mostly the case for ships that only are in the chinese version, like 33)
        $libraryCn = explode('SelfIntro = ', $library[1]);
        $libraryJp = explode('SelfIntro = ', $library[2]);
        $libraryTradCn = explode('SelfIntroTL = ', $library[1]);
        $libraryTradJp = explode('SelfIntroTL = ', $library[2]);
        $libraryNoteCn = explode('SelfIntroNote = ', $library[1]);
        $azurLaneJp = explode('|', $libraryJp[1])[0];
        if (trim($azurLaneJp) === "") {
            $azurLaneJp = explode('|', $libraryCn[1])[0];
            $azurLaneEn = explode('|', $libraryTradCn[1])[0];
        } else {
            $azurLaneEn = explode('|', $libraryTradJp[1])[0];
            if (trim($azurLaneEn) === "")
                $azurLaneEn = explode('|', $libraryTradCn[1])[0];
        }
        $azurLaneNote = explode('|', $libraryNoteCn[1])[0];
        return(array($azurLaneImage, $azurLaneAudio, $azurLaneJp, $azurLaneEn, $azurLaneNote));
    }

    public static function GetWarshipGirlsInfo($name) {
        $name = str_replace("ou", "ō", str_replace("uu", "ū", $name));
        $context = ShipInfo::GetContext();
        if (strpos($name, " ") !== false) {
            $ships = json_decode(file_get_contents("https://shipgirlfriends.moe/hmdata/ship/list?search=" . urlencode(explode(" ", $name)[0]), false, $context))->data->ships;
            for ($i = 0; $i < count($ships); $i++) {
                if ($ships[$i]->name->en === $name) {
                    $id = $ships[$i]->picId;
                    break;
                }
            }
        } else {
            $id = json_decode(file_get_contents("https://shipgirlfriends.moe/hmdata/ship/list?search=" . urlencode($name), false, $context))->data->ships[0]->picId;
        }
        $content = file_get_contents("https://shipgirlfriends.moe/ship/" . $id, false, $context);
        preg_match('/2c65d87b>zh<\/div>([^<]+)<\/div>/', $content, $match);
        return(array("https://image.shipgirlfriends.moe/images/ships/large/L_NORMAL_" . $id . ".png", "", $match[1], "", ""));
    }
}
?>
