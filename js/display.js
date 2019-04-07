let http = new XMLHttpRequest();
http.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
        let json = JSON.parse(this.responseText);
        document.getElementById("imageKancolle").innerHTML = '<img src="' + json[0] + '" width="300"/>';
        document.getElementById("imageAzurLane").innerHTML = '<img src="' + json[1] + '" width="300"/>';
        document.getElementById("audioIntroKancolle").innerHTML = '<audio controls src="' + json[2] + '"></audio>';
        document.getElementById("textIntroKancolle").innerHTML = json[3];
    }
}
const urlParams = new URLSearchParams(window.location.search);
http.open("GET", "php/shipInfo.php?name=" + urlParams.get('shipName'), true);
http.send();