<?php
class Card {
    private $id;
    private $imagePath;
    private $isFaceUp;

    // Chemin vers l'image du recto par défaut
    private $defaultImagePath = './assets/pictures/cards/recto.png';


    public function __construct($id, $imagePath) {
        $this->id = $id;
        $this->imagePath = $imagePath;
        $this->isFaceUp = false; // Au début, la carte est face cachée
    }
    
    // Méthode pour obtenir l'image à afficher (recto ou verso)
    public function getImage() {
        if ($this->isFaceUp) {
            return $this->imagePath;
        } else {
            return $this->defaultImagePath;
        }
    }

    // Méthode pour retourner la carte (afficher ou cacher l'image réelle)
    public function flip() {
        $this->isFaceUp = !$this->isFaceUp;
    }

    public function getId() {
        return $this->id;
    }
}
?>
