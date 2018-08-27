console.log("password_confirm.js");
var passwordNode;
var confirmNode;
var buttonNode;
var focusOnConfirm;

function init() { // onload by register.html
    passwordNode = document.getElementById("password");
    confirmNode = document.getElementById("confirm");
    buttonNode = document.getElementById("submit");

    buttonNode.setAttribute('disabled', 'disabled');

    focusOnConfirm = false;

    confirmNode.onfocus =
        function (e) {
            //console.log('Focus');
            focusOnConfirm = true;
        }

    confirmNode.onblur =
        function (e) {
            //console.log('Blur');
            focusOnConfirm = false;
        }

    confirmNode.onkeyup =
        function (e) {
            //console.log("Key up");
            if (!focusOnConfirm) {
                return ;
            }
            if(passwordNode.value =="" || passwordNode.value == null){
                confirmNode.style.borderColor = "#fff";
                passwordNode.style.borderColor = "#fff";
                buttonNode.setAttribute('disabled', 'disabled');
            } else if (passwordNode.value===confirmNode.value) {
                confirmNode.style.borderColor = "#0f0";
                passwordNode.style.borderColor = "#0f0";
                buttonNode.removeAttribute('disabled');
            } else {
                confirmNode.style.borderColor = "#f00";
                passwordNode.style.borderColor = "#f00";
                buttonNode.setAttribute('disabled', 'disabled');
            }
        }
}
