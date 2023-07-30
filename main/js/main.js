async function fetchwg() {
    formdata = new FormData();
    formdata.append('token', 'C7tbg4lvgvWNisrPsuRZ1EqA75E3Fc');

    await fetch('api/', {method: 'POST', body: formdata})
        .then(response => {
            if (!response.ok) throw Error(`fetch error`);
            return response.json();
        })
        .then(data => {list = data})
        .catch(err => console.error(`Error: ${err}`));

    document.querySelectorAll('.div-table-row').forEach(element => {element.remove()})

    if (Object.keys(list.wg0.peers).length < 2) return 0;
    
    Object.keys(list.wg0.peers).forEach(peer => {
        divs = `
        <div class="table-line">
            <div class="table-cell">
                <span>${peer}</span>
            </div>
            <div class="table-cell">
                <span>lukasbyr.fr</span>
            </div>
            <div class="table-cell">
                <span>1m 34s</span>
            </div>
            <div class="table-cell">
                <span>432 Mo/s</span>
            </div>
            <div class="table-cell">
                <span>345 Ko/s</span>
            </div>
            <div class="table-cell">
                <span>${list.wg0.peers[peer].allowedIps}</span>
            </div>
        </div>
        `;
        document.querySelector('.table-body').insertAdjacentHTML('beforeend', divs);
    })

}

async function main() {
    list = {};
    document.querySelector('.table-commande-btn').addEventListener('click', fetchwg);
}

(async () => main())();