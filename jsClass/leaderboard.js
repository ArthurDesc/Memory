class Leaderboard {
    constructor(apiUrl) {
        this.apiUrl = apiUrl; // URL de l'API ou de l'endroit où récupérer les scores
        this.scores = []; // Tableau pour stocker les scores des joueurs
    }

    // Méthode pour récupérer les scores des joueurs depuis le serveur
    async fetchScores() {
        try {
            const response = await fetch(this.apiUrl);
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            this.scores = await response.json(); // Parse les scores au format JSON
        } catch (error) {
            console.error("Erreur lors de la récupération des scores:", error);
        }
    }

    // Méthode pour afficher le leaderboard dans un tableau HTML
    displayScores(containerId) {
        const container = document.getElementById(containerId);

        // Vider le contenu précédent du conteneur
        container.innerHTML = '';

        // Créer la table et l'en-tête
        const table = document.createElement('table');
        const thead = document.createElement('thead');
        const tbody = document.createElement('tbody');

        thead.innerHTML = `
            <tr>
                <th>Pseudo</th>
                <th>Score Moyen</th>
            </tr>
        `;

        // Remplir le tableau avec les scores
        this.scores.forEach(player => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${player.pseudo}</td>
                <td>${player.average_score}</td>
            `;
            tbody.appendChild(row);
        });

        table.appendChild(thead);
        table.appendChild(tbody);
        container.appendChild(table);
    }

    // Méthode pour initialiser le leaderboard (récupération + affichage)
    async init(containerId) {
        await this.fetchScores();
        this.displayScores(containerId);
    }
}

// Utilisation de la classe Leaderboard
document.addEventListener('DOMContentLoaded', () => {
    const leaderboard = new Leaderboard('http://localhost/api/get_scores.php'); // Remplace par ton URL
    leaderboard.init('leaderboard-container'); // ID de l'élément HTML où afficher le tableau
});
