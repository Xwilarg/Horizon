<?php
    $options = [
        "http" => [
            "method" => "GET",
            "header" => "User-Agent: Mozilla/5.0 commits.zirk.eu\r\n"
        ]
    ];
    $context = stream_context_create($options);
    $url = json_decode(file_get_contents("https://kancolle.wikia.com/api/v1/Search/List?query=" . urlencode($_GET['name']) . "&limit=1", false, $context))->items[0]->url . "/Gallery";
    $kancolle = file_get_contents($url, false, $context);
    preg_match_all('/img src="([^"]+)"/', $kancolle, $matches);
    $kancolleImage = $matches[1][1];
    $url = "https://azurlane.koumakan.jp/" . json_decode(file_get_contents("https://azurlane.koumakan.jp/w/api.php?action=opensearch&search=" . urlencode($_GET['name']) . "&limit=1", false, $context))[1][0];
    $azurLane = file_get_contents($url, false, $context);
    preg_match('/src="(\/w\/images\/thumb\/[^\/]+\/[^\/]+\/[^\/]+\/600px-[^"]+)/', $azurLane, $matches);
    $azurLaneImage = "https://azurlane.koumakan.jp" . $matches[1];
    echo(json_encode(array($kancolleImage, $azurLaneImage)));
?>