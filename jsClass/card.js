// card.js
class Card {
    constructor(id, imagePath) {
        this.id = id;
        this.imagePath = imagePath;
        this.isFaceUp = false;
    }

    flip() {
        this.isFaceUp = !this.isFaceUp;
    }

    getImage() {
        return this.isFaceUp ? this.imagePath : './assets/pictures/cards/recto.png';
    }

    getId() {
        return this.id;
    }
}

// Exporter la classe Card si tu veux utiliser l'import dans d'autres fichiers JavaScript
export { Card };
