function onglet(ongletButtons, ongletButton, onglet, toggle) {
    // On initialise l'index à 0
    let index = 0;

    // On actualise l'index par l'identifiant (data) du bouton
    index = ongletButton.getAttribute("data-onglet");
    
    // On log la valeur de l'index
    console.log(index);
    
    if(ongletButton.classList.contains('active')){
        if(toggle === true){
            for(let i = 0; i < ongletButtons.length; i++) {
                ongletButtons[i].classList.remove('active');
            }
        
            for(let j = 0; j < onglet.length; j++){
                onglet[j].classList.remove('active');
            }
        } else {
            return;
        }
    } else {
        ongletButton.classList.add('active');

        for(let i = 0; i < ongletButtons.length; i++) {
            if(ongletButtons[i].getAttribute("data-onglet") != index){
                ongletButtons[i].classList.remove('active');
            }
        }
    
        for(let j = 0; j < onglet.length; j++){
            if(onglet[j].getAttribute("data-onglet") == index){
                onglet[j].classList.add('active');
            } else {
                onglet[j].classList.remove('active');
            }
        }
    }
}
  

// On boucle sur l'enssemble des boutons
document.querySelectorAll(".onglet-button").forEach(ogltBtn => {
    // On attend un click sur le bouton
    ogltBtn.addEventListener('click', () => {
        // On appel notre fonction
        onglet(document.querySelectorAll(".onglet-button"), ogltBtn, document.querySelectorAll(".onglet-display"));
    });
});