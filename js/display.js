let shipName = new URLSearchParams(window.location.search).get('shipName');
if (shipName !== "")
    document.getElementById('title').innerHTML = shipName;

let http = new XMLHttpRequest();
http.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
        let json = JSON.parse(this.responseText);
        document.getElementById("imageKancolle").innerHTML = '<img src="' + json[0] + '" width="300"/>';
        document.getElementById("imageAzurLane").innerHTML = '<img src="' + json[1] + '" width="300"/>';
        document.getElementById("audioIntroKancolle").innerHTML = '<audio controls src="' + json[2] + '"></audio>';
        document.getElementById("audioIntroAzurLane").innerHTML = '<audio controls src="' + json[3] + '"></audio>';
        document.getElementById("textIntroKancolleJp").innerHTML = json[4];
        document.getElementById("textIntroAzurLaneJp").innerHTML = json[5];
        document.getElementById("textIntroKancolleEn").innerHTML = json[6];
        document.getElementById("textIntroAzurLaneEn").innerHTML = json[7];
        document.getElementById("textIntroKancolleNote").innerHTML = json[8];
        document.getElementById("textIntroAzurLaneNote").innerHTML = json[9];
    }
}
http.open("GET", "php/searchShip.php?name=" + shipName, true);
http.send();