var json = null;

let http = new XMLHttpRequest();
http.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
        json = JSON.parse(this.responseText);
    }
}
const urlParams = new URLSearchParams(window.location.search);
http.open("GET", "php/getAllShips.php", true);
http.send();

document.getElementById("input").addEventListener("input", function(e) {
    if (json !== null) {
        let value = document.getElementById("input").value.toLowerCase();
        if (value == "")
            document.getElementById("autocomplete-all").innerHTML = '';
        else {
            let res = "";
            let max = 5;
            json.forEach(elem => {
                if (max > 0 && elem.toLowerCase().startsWith(value)) {
                    res += '<div id="autocomplete-elem">' + elem + '</div>';
                    max--;
                }
            });
            document.getElementById("autocomplete-all").innerHTML = res;
        }
    }
});