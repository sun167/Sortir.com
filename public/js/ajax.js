let buttons = Array.from(document.getElementsByClassName('btn-inscire'));
let participantID = document.querySelector('.participantID').value;
let sortieID;
buttons.forEach(button =>
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
                if (estInscritContainer.innerHTML === "x") {
                    estInscritContainer.innerHTML = "";
                } else {
                    estInscritContainer.innerHTML = "x";
                }

            });
    })
);

