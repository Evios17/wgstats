// Quand on clique sur le bouton de déconnection
document.querySelector("#logout").addEventListener("click", () => {
    document.cookie = "id=" + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
    document.cookie = "username=" + "=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";

    window.location.href = "../login/";
})