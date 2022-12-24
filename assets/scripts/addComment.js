let myFormComment= document.getElementById("formComment");
let btnForm = document.getElementById("addComment");
function openForm(){
    myFormComment.classList.toggle("active")

}
btnForm.onclick = openForm;
console.log(btnForm);