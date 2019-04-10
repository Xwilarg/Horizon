var json = null;

let http = new XMLHttpRequest();
http.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
        json = JSON.parse(this.responseText);
    }
}
http.open("GET", "php/getAllShips.php", true);
http.send();

var currSelected = -1;
var max = 0;
var allElems;

function selectMouse(name) {
    document.getElementById("input").value = name;
    document.getElementById("form").submit();
}

function addAutocomplete(name, strongName, isSelected) {
    if (isSelected)
        res = '<div id="autocomplete-elem-selected"';
    else
        res = '<div id="autocomplete-elem"';
    res += 'onclick="selectMouse(\'' + name + '\')">' + strongName + '</div>';
    return res;
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
        let valLength = value.length;
        allElems = []
        validElems = {}
        json.forEach(elem => { // Prepare autocomplete by taking all shipname that are contained in the user input
            let lowerElem = elem.toLowerCase();
            if (lowerElem.includes(value)) {
                // We calculate where to place <strong></strong> so the user can easily see wha match his input
                let startIndex = lowerElem.indexOf(value);
                let endBoldLength = startIndex + valLength;
                let finalValue = elem.substr(0, startIndex) + "<strong>" + elem.substr(startIndex, valLength) + "</strong>" + elem.substr(endBoldLength, elem.length - endBoldLength);
                validElems[elem] = finalValue;
            }
        });
        for (let elem in validElems) { // At first we display all names at start with the user input
            if (index < 5 && elem.toLowerCase().startsWith(value)) {
                res += addAutocomplete(elem, validElems[elem], index === currSelected)
                index++;
                allElems.push(elem);
                delete validElems[elem];
            }
        }
        if (index < 5) { // If there is still some room left, we display the ship that have match in the middle
            for (let elem in validElems) {
                if (index < 5) {
                    res += addAutocomplete(elem, validElems[elem], index === currSelected)
                    index++;
                    allElems.push(elem);
                }
            }
        }
        max = index;
        document.getElementById("autocomplete-all").innerHTML = res;
    }
}

document.getElementById("input").addEventListener("input", function() {
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
            else
                displayAutocomplete();
        }
        else if (e.keyCode == 40) { // Down
            currSelected++;
            if (currSelected >= max)
                currSelected = max - 1;
            else
                displayAutocomplete();
        }
    }
});