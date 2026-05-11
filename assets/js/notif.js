function bukaNotif() {
    var modal = document.getElementById("notifModal");
    if (modal) { modal.style.display = "block"; }
}

function tutupNotif() {
    var modal = document.getElementById("notifModal");
    if (modal) { modal.style.display = "none"; }
}

window.onclick = function(event) {
    var modal = document.getElementById("notifModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
