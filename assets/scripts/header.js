let sidenav = document.getElementById("mySidenav");
let openBtn = document.getElementById("openBtn");
function openNav(){
    sidenav.classList.toggle("active")
}
openBtn.onclick = openNav;



// closeBtn.onclick = closeNav;
//
// /* Set the width of the side navigation to 250px */
// function openNav() {
//     sidenav.classList.add("active");
// }
//
// /* Set the width of the side navigation to 0 */
// function closeNav() {
//     sidenav.classList.remove("active");
// }