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

var currSelected = -1;
var max = 0;
var allElems;

function selectMouse(name) {
    document.getElementById("input").value = name;
    document.getElementById("form").submit();
}

function displayAutocomplete() {
    let value = document.getElementById("input").value.toLowerCase();
    if (value == "") {
        max = 0;
        document.getElementById("autocomplete-all").innerHTML = '';
    }
    else {
        let res = "";
        let index = 0;
        allElems = []
        json.forEach(elem => {
            if (index < 5 && elem.toLowerCase().startsWith(value)) {
                if (index === currSelected)
                    res += '<div id="autocomplete-elem-selected"';
                else
                    res += '<div id="autocomplete-elem"';
                res += 'onclick="selectMouse(\'' + elem + '\')">' + elem + '</div>';
                index++;
                allElems.push(elem);
            }
        });
        max = index;
        document.getElementById("autocomplete-all").innerHTML = res;
    }
}

document.getElementById("input").addEventListener("input", function(e) {
    if (json !== null) {
        currSelected = -1;
        displayAutocomplete();
    }
});

document.getElementById("input").addEventListener("keydown", function(e) {
    if (json !== null) {
        if (e.keyCode == 13) { // Enter
            if (currSelected == -1) {
                if (max == 0)
                    e.preventDefault();
                else
                    document.getElementById("input").value = allElems[0];
            }
            else
                document.getElementById("input").value = allElems[currSelected];
        }
        else if (e.keyCode == 27) { // Escape
            currSelected = -1;
            displayAutocomplete();
        }
        else if (e.keyCode == 38) { // Up
            currSelected--;
            if (currSelected < 0)
                currSelected = 0;
            displayAutocomplete();
        }
        else if (e.keyCode == 40) { // Down
            currSelected++;
            if (currSelected >= max)
                currSelected = max - 1;
            displayAutocomplete();
        }
    }
});