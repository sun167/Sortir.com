let buttonsInscrire = Array.from(document.getElementsByClassName('btn-inscrire'));
let participantID = document.querySelector('.participantID').value;
let sortieID;
buttonsInscrire.forEach(button =>
    button.addEventListener('click', function (event) {
        event.preventDefault();
        sortieID = button.querySelector('.sortieID').value;
        axios.get(inscriptionBtnURL, {
            params: {
                'sortieID': sortieID,
                'participantID': participantID
            }
        })
            .then(function (response) {
                console.log(response.data);
                let nbInscritContainer = document.getElementById("inscription-" + sortieID);
                let estInscritContainer = document.getElementById("estInscrit-" + sortieID);
                nbInscritContainer.innerHTML = response.data;
                estInscritContainer.innerHTML = "x";
                button.style.display = "none";
            });
    })
);
let buttonsDesister = Array.from(document.getElementsByClassName('btn-desister'));
buttonsDesister.forEach(button =>
    button.addEventListener('click', function (event) {
        event.preventDefault();
        sortieID = button.querySelector('.sortieID').value;
        axios.get(desisterBtnURL, {
            params: {
                'sortieID': sortieID,
                'participantID': participantID
            }
        })
            .then(function (response) {
                    console.log(response.data);
                    let nbInscritContainer = document.getElementById("inscription-" + sortieID);
                    let estInscritContainer = document.getElementById("estInscrit-" + sortieID);

                    nbInscritContainer.innerHTML = response.data;
                    estInscritContainer.innerHTML = "";
                    button.style.display = "none";
                }
            );
    })
);