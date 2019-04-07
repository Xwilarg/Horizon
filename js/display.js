let http = new XMLHttpRequest();
http.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
        console.log(this.responseText);
    }
}
const urlParams = new URLSearchParams(window.location.search);
http.open("GET", "php/shipInfo.php?name=" + urlParams.get('shipName'), true);
http.send();