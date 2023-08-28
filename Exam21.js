let request = new XMLHttpRequest();

function requestData(gameId) { // fordert die Daten asynchron an
    "use strict";
    //ToDo - vervollständigen **************
    request.open("GET", "Exam21API.php?gameId=" + gameId);
    request.onreadystatechange = processData;
    request.send(null);
}

function processData() {
    "use strict";
    if (request.readyState === 4) { // Uebertragung = DONE
        if (request.status === 200) { // HTTP-Status = OK
            if (request.responseText != null)
                process(request.responseText);
                //ToDo - vervollständigen ************

            else console.error("Dokument ist leer");
        } else console.error("Uebertragung fehlgeschlagen");
    } // else; // Uebertragung laeuft noch
}

function process(data) {
    let obj = JSON.parse(data);

    let zusagen = document.getElementById("zusagen");

    zusagen.innerText = "Zusagen Spielerinnen: " + obj.playing;

}

function polldata() {
    window.setInterval(10000);
    let gameId = document.getElementById("spielId");

    if(gameId.value != null) {
        requestData(gameId.value);
    }
}
