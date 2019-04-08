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

    public static function GetKancolleInfo($name) {
        $context = ShipInfo::GetContext();
        // base URL is the character page in the Wikia, url is to the gallery
        $baseUrl = json_decode(file_get_contents("https://kancolle.wikia.com/api/v1/Search/List?query=" . urlencode($name) . "&limit=1", false, $context))->items[0]->url;
        $url = $baseUrl . "/Gallery";
        $kancolle = file_get_contents($url, false, $context);
        preg_match_all('/img src="([^"]+)"/', $kancolle, $matches);
        $kancolleImage = $matches[1][1]; // Character image
        $kancolleMain = file_get_contents($baseUrl, false, $context);
        preg_match('/<span[^>]+><a href="([^"]+)".+<\/span> Library\n.+<\/span>\n.+shipquote-en\">(.+)/', $kancolleMain, $matches);
        $kancolleAudio = $matches[1]; // Character intro voiceline
        $kancolleText = $matches[2]; // Character intro text
        return(array($kancolleImage, $kancolleAudio, $kancolleText));
    }

    public static function GetAzurLaneInfo($name) {
        $context = ShipInfo::GetContext();
        // url is the character page in the wikia
        $azurName = json_decode(file_get_contents("https://azurlane.koumakan.jp/w/api.php?action=opensearch&search=" . urlencode($name) . "&limit=1", false, $context))[1][0];
        $url = "https://azurlane.koumakan.jp/" . $azurName;
        $azurLane = file_get_contents($url, false, $context);
        preg_match('/src="(\/w\/images\/thumb\/[^\/]+\/[^\/]+\/[^\/]+\/[0-9]+px-' . $azurName . '.png)/', $azurLane, $matches);
        $azurLaneImage = "https://azurlane.koumakan.jp" . $matches[1]; // Character image
        preg_match('/<td><a href="([^"]+)"[^>]+>Play<\/a>\n<\/td>\n<td> Self Introduction\n<\/td>\n<td>[^<]+<\/td>\n<td>([^\n]+)/', $azurLane, $matches);
        $azurLaneAudio = $matches[1]; // Character intro voiceline
        $azurLaneText = $matches[2]; // Character intro text
        return(array($azurLaneImage, $azurLaneAudio, $azurLaneText));
    }
}
?>
