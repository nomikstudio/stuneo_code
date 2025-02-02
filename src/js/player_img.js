// Falls das Logo nicht geladen wird, wird ein Standardbild verwendet
    document.getElementById('playerStationLogo').onerror = function() {
        this.src = 'https://www.eclosio.ong/wp-content/uploads/2018/08/default.png';
    };