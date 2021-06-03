window.onload = init;

function init() {
    let buttons = Array.from(document.getElementsByClassName('button_inscription'));
    let sortie_id = document.getElementById('sortie_id').value;

    console.log(buttons);
    console.log(sortie_id);

    buttons.forEach(function (elem,idx) {
        elem.addEventListener('click',function () {
            let data = {'sortie_id' : sortie_id, 'inscription' : elem.value};
            console.log(data);
            fetch("ajax-inscription",{method : 'POST', body : JSON.stringify(data)}).then(function (response) {
                return response.json()
            }).then(function (data) {
                console.log(data)
                document.getElementById('nb_dispo').innerHTML = data.nbinscription;
            });
        });
    });
}