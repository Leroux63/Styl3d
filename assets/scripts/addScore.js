let myFormRating= document.getElementById("ratingForm");
let btnFormScore = document.getElementById("addScore");
function openForm(){
    myFormRating.classList.toggle("active")

}
btnFormScore.onclick = openForm;
console.log(btnFormScore);