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
                foreach (explode("|", $elem) as $e) {
                    if (strpos($e, "/") !== false) {
                        array_push($arr, explode("/", $elem)[1]);
                    } else {
                        array_push($arr, $e);
                    }
                }
            }
        }
        return ($arr);
    }

    public static function GetAllAzurLaneShips() {
        $context = ShipInfo::GetContext();
        $content = file_get_contents("https://azurlane.koumakan.jp/List_of_Ships", false, $context);
        preg_match_all('/\[\\\"Name\\\"\] = [^"]+"([^\"]+)/', $content, $matches); // Backslash aren't properly detected, I don't know why
        $arr = array();
        foreach ($matches[1] as $elem) {
            $e = substr($elem, 0, strlen($elem) - 1);
            if (substr($e, strlen($e) - 3, 3) !== "Kai" && !in_array($e, $arr))
                array_push($arr, $e);
        }
        return ($arr);
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
        // English description is right after the URL
        $kancolleText = explode('</td>',
                            explode('class="shipquote-en">',
                                explode($matches[0], $kancolleMain)[1]
                            )[1]
                        )[0]; // Character intro text
        return(array($kancolleImage, $kancolleAudio, $kancolleText));
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
        $azurLaneAudio = $matches[0]; // Character intro voiceline
        $azurLaneText = explode('</td>',
                            explode('<td>',
                                explode('Self Introduction',
                                    explode($matches[0], $azurLane)[1]
                                )[1]
                            )[2]
                        )[0]; // Character intro text
        return(array($azurLaneImage, $azurLaneAudio, $azurLaneText));
    }
}
?>
