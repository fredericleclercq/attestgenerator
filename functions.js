document.addEventListener("DOMContentLoaded", function () {



    let canvas = document.querySelector("#canvas_signature");
    let signaturePad = new SignaturePad(canvas);

    let formulaire = document.getElementById('formulaire');

    formulaire.addEventListener('submit', function (e) {
        e.preventDefault();

        let r1 = document.getElementById("r1");
        let r2 = document.getElementById("r2");
        let r3 = document.getElementById("r3");
        let r4 = document.getElementById("r4");
        let r5 = document.getElementById("r5");
        let r6 = document.getElementById("r6");
        let r7 = document.getElementById("r7");

        if (r1.checked == false && r2.checked == false && r3.checked == false && r4.checked == false  &&  r5.checked == false && r6.checked == false && r7.checked == false) {
            document.getElementById("error_sign").classList.add('alert');
            document.getElementById("error_sign").classList.add('alert-danger');
            document.querySelector("#error_sign").innerHTML = 'Merci de choisir une raison de déplacement';
        }
        else {

            if (signaturePad.isEmpty()) {
                document.getElementById("error_sign").classList.add('alert');
                document.getElementById("error_sign").classList.add('alert-danger');
                document.querySelector("#error_sign").innerHTML = 'Merci de signer le document';
            }
            else {
                document.getElementById('data_signature').innerHTML = signaturePad.toDataURL()
                formulaire.submit();
            }
        }

    });

    document.getElementById('reset_signature').addEventListener('click', function (e) {
        e.preventDefault();
        signaturePad.clear();
    });

});