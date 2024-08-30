<?php
class Card {
    private $id;
    private $imagePath;
    private $isFaceUp;

    // Chemin vers l'image du recto par défaut
    private $defaultImagePath = './assets/pictures/cards/recto.png';

    // Constructeur pour initialiser une carte avec un ID et un chemin d'image
    public function __construct($id, $imagePath) {
        $this->id = $id;
        $this->imagePath = $imagePath;
        $this->isFaceUp = false; // Au début, la carte est face cachée
    }
    
    // Méthode pour obtenir l'image à afficher (recto ou verso)
    public function getImage() {
        // Retourne l'image de la carte si elle est retournée, sinon le recto par défaut
        return $this->isFaceUp ? $this->imagePath : $this->defaultImagePath;
    }

    // Méthode pour retourner la carte (afficher ou cacher l'image réelle)
    public function flip() {
        $this->isFaceUp = !$this->isFaceUp;
    }

    // Obtenir l'ID de la carte
    public function getId() {
        return $this->id;
    }

    // Vérifier si la carte est face visible
    public function isFaceUp() {
        return $this->isFaceUp;
    }
}
?>
