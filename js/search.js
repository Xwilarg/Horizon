var didBegin = false;

var kancolleShips = [];
var azurLaneShips = [];

let http = new XMLHttpRequest();
http.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
        let json = JSON.parse(this.responseText);
        json[0].forEach(elem => {
            kancolleShips.push(elem);
        });
        json[1].forEach(elem => {
            azurLaneShips.push(elem);
        });
        didBegin = true;
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

function addAutocomplete(strongName, refName, isKancolle, isAzurLane, isSelected) {
    if (isSelected)
        res = '<div id="autocomplete-elem-selected"';
    else
        res = '<div id="autocomplete-elem"';
    console.log(refName);
    res += ' onclick="selectMouse(\'' + refName + '\')">' + strongName + '<game>' + ((isKancolle) ? ('<img id="helpImage" width="30" height="30" alt="KanColleLogo" src="img/KanColle.png">') : ('')) + ((isAzurLane) ? ('<img id="helpImage" width="30" height="30" alt="AzurLaneLogo" src="img/AzurLane.png">') : ('')) + '</game></div>';
    return res;
}

// validElems is the dictionary containing names
// value is the user input
// elem is the ship name to check
// nameRef is the name to call in case of search (for example for 'Ryuuhou', you'll search 'Taigei')
// kancolleValue is 1 for KanColle, 2 for AzurLane, 3 for both
function updateDictionary(validElems, value, elem, nameRef, kancolleValue) {
    let valLength = value.length;
    let lowerElem = elem.toLowerCase();
    if (lowerElem.includes(value)) {
        // We calculate where to place <strong></strong> so the user can easily see wha match his input
        let startIndex = lowerElem.indexOf(value);
        let endBoldLength = startIndex + valLength;
        let finalValue = elem.substr(0, startIndex) + "<strong>" + elem.substr(startIndex, valLength) + "</strong>" + elem.substr(endBoldLength, elem.length - endBoldLength);
        if (validElems[elem] == undefined)
            validElems[elem] = [ finalValue, nameRef.replace("&#39;", "%27"), kancolleValue ];
        else
            validElems[elem] = [ finalValue, nameRef.replace("&#39;", "%27"), kancolleValue + validElems[elem][1] ];
    }
}

function addElemToAutocomplete(elem, index) {
    let dictElem = validElems[elem];
    let kancolleValue = dictElem[2];
    return (addAutocomplete(dictElem[0], dictElem[1], kancolleValue != 2, kancolleValue != 1, index === currSelected));
}

function displayAutocomplete() {
    let value = document.getElementById("input").value.toLowerCase();
    if (value == "") {
        max = 0;
        document.getElementById("autocomplete-all").innerHTML = '';
    } else {
        let res = "";
        let index = 0;
        allElems = []
        validElems = {}
        kancolleShips.forEach(elem => { // Prepare autocomplete by taking all shipname that are contained in the user input
            updateDictionary(validElems, value, elem[0], (elem.length > 1) ? (elem[1]) : (elem[0]), 1);
        });
        azurLaneShips.forEach(elem => { // Prepare autocomplete by taking all shipname that are contained in the user input
            updateDictionary(validElems, value, elem[0], (elem.length > 1) ? (elem[1]) : (elem[0]), 2);
        });
        for (let elem in validElems) { // At first we display all names at start with the user input
            if (index < 5 && elem.toLowerCase().startsWith(value)) {
                res += addElemToAutocomplete(elem, index);
                index++;
                allElems.push(validElems[elem][1]);
                delete validElems[elem];
            }
        }
        if (index < 5) { // If there is still some room left, we display the ship that have match in the middle
            for (let elem in validElems) {
                if (index < 5) {
                    res += addElemToAutocomplete(elem, index);
                    index++;
                    allElems.push(validElems[elem][1]);
                }
            }
        }
        max = index;
        document.getElementById("autocomplete-all").innerHTML = res;
    }
}

document.getElementById("input").addEventListener("input", function() {
    if (didBegin) {
        currSelected = -1;
        displayAutocomplete();
    }
});

document.getElementById("input").addEventListener("keydown", function(e) {
    if (didBegin) {
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