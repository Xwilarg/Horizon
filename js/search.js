let http = new XMLHttpRequest();
http.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
        let json = JSON.parse(this.responseText);
    }
}
const urlParams = new URLSearchParams(window.location.search);
http.open("GET", "php/getAllShips.php", true);
http.send();