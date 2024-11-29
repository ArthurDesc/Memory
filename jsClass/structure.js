import { Card } from './card.js'; // Importer la classe Card

class Structure {
    constructor(pairsCount) {
        this.pairsCount = pairsCount;
        this.cards = [];
        this.cardElements = [];
        this.flippedIndices = [];
        this.attempts = 0; // Nouvelle propriété pour suivre le nombre d'essais
        this.init();
    }

    init() {
        // Créer les cartes avec des identifiants et chemins d'image
        for (let i = 1; i <= this.pairsCount; i++) {
            const imagePath = `./assets/pictures/cards/carte${i}.png`;
            // Ajouter deux cartes pour chaque paire
            this.cards.push(new Card(i * 2 - 1, imagePath));
            this.cards.push(new Card(i * 2, imagePath));
        }

        // Mélanger les cartes
        this.shuffleCards();

        // Créer les éléments DOM pour chaque carte
        this.createCardElements();
    }

    shuffleCards() {
        this.cards.sort(() => Math.random() - 0.5);
    }

    createCardElements() {
        const cardContainer = document.getElementById('card-container');
        cardContainer.innerHTML = ''; // Réinitialiser le conteneur

        this.cards.forEach((card, index) => {
            const cardElement = document.createElement('div');
            cardElement.classList.add('card', 'recto'); // Par défaut, la carte est face cachée
            cardElement.dataset.index = index;

            const img = document.createElement('img');
            img.src = card.getImage(); // Initialement le recto
            img.alt = `Card ${card.getId()}`;
            cardElement.appendChild(img);

            cardElement.addEventListener('click', () => this.flipCard(index));

            cardContainer.appendChild(cardElement);
            this.cardElements.push(cardElement);
        });

        // Afficher le nombre d'essais
        this.updateAttemptsDisplay();
    }

    flipCard(index) {
        const card = this.cards[index];
        card.flip();
        const cardElement = this.cardElements[index];

        // Mettre à jour l'image de la carte
        cardElement.querySelector('img').src = card.getImage();

        // Mettre à jour la classe CSS
        if (card.isFaceUp) {
            cardElement.classList.remove('recto');
            cardElement.classList.add('verso');
        } else {
            cardElement.classList.remove('verso');
            cardElement.classList.add('recto');
        }

        // Ajouter la logique pour gérer les cartes retournées
        this.handleFlippedCards(index);
    }

    checkGameEnd() {
        const allCardsMatched = this.cards.every(card => card.isFaceUp);
        if (allCardsMatched) {
            this.saveAttemptsToDatabase();
        }
    }

    handleFlippedCards(index) {
        this.flippedIndices.push(index); // Ajouter l'index de la carte retournée à la liste

        if (this.flippedIndices.length === 2) { // Vérifier s'il y a deux cartes retournées
            // Incrémenter le nombre d'essais
            this.attempts++;
            this.updateAttemptsDisplay();
            const [firstIndex, secondIndex] = this.flippedIndices; // Récupérer les deux indices

            // Si les cartes correspondent
            if (this.cards[firstIndex].getImage() === this.cards[secondIndex].getImage()) {
                // Désactiver les clics sur les cartes correspondantes
                this.cardElements[firstIndex].style.pointerEvents = 'none';
                this.cardElements[secondIndex].style.pointerEvents = 'none';

                // Réinitialiser la liste des indices des cartes retournées
                this.flippedIndices = [];
                this.checkGameEnd();

            } else {
                // Si les cartes ne correspondent pas, les retourner après un délai
                setTimeout(() => {
                    this.cards[firstIndex].flip();
                    this.cards[secondIndex].flip();

                    // Réinitialiser les images et classes CSS
                    this.cardElements[firstIndex].querySelector('img').src = this.cards[firstIndex].getImage();
                    this.cardElements[secondIndex].querySelector('img').src = this.cards[secondIndex].getImage();

                    this.cardElements[firstIndex].classList.remove('verso');
                    this.cardElements[firstIndex].classList.add('recto');
                    this.cardElements[secondIndex].classList.remove('verso');
                    this.cardElements[secondIndex].classList.add('recto');

                    // Réinitialiser la liste des indices des cartes retournées
                    this.flippedIndices = [];
                }, 1000); // Délai de 1 seconde
            }
        }
    }

    saveAttemptsToDatabase() {
        fetch('/memory/saveAttempts.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ 
                attempts: this.attempts,
                pairsCount: this.pairsCount
            })
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.status !== 'success') {
                    throw new Error(data.message);
                }
            })
            .catch(error => {
                // Silently handle errors
            });
    }

    updateAttemptsDisplay() {
        const attemptsElement = document.getElementById('attempts-display');
        if (attemptsElement) {
            attemptsElement.textContent = `Nombre d'essais : ${this.attempts}`;
        }
    }

    // Méthode pour obtenir les cartes (pour affichage ou autre logique)
    getCards() {
        return this.cards;
    }
}

// Exporter la classe pour utilisation dans d'autres modules
export { Structure };
