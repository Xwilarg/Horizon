let http = new XMLHttpRequest();
http.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
        console.log(this.responseText);
        let json = JSON.parse(this.responseText);
        document.getElementById("imageKancolle").innerHTML = '<img src="' + json[0] + '" width="300">';
        document.getElementById("imageAzurLane").innerHTML = '<img src="' + json[1] + '" width="300">';
    }
}
const urlParams = new URLSearchParams(window.location.search);
http.open("GET", "php/shipInfo.php?name=" + urlParams.get('shipName'), true);
http.send();