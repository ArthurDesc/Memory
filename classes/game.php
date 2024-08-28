<?php

class Game {
    public $cards = [];
    public $pairsCount;

    public function __construct($pairsCount) {
        $this->pairsCount = $pairsCount;
        $this->initializeCards();
    }

    private function initializeCards() {
        // On suppose que vous avez un tableau d'images pour les cartes
        $images = ['img1.png', 'img2.png', 'img3.png']; // à étendre selon besoin

        // Mélange les images et sélectionne un nombre de paires correspondant à pairsCount
        shuffle($images);
        $selectedImages = array_slice($images, 0, $this->pairsCount);

        // Création des paires
        foreach ($selectedImages as $key => $image) {
            $this->cards[] = new Card($key, $image);
            $this->cards[] = new Card($key, $image);
        }

        // Mélange des cartes
        shuffle($this->cards);
    }

    public function getCards() {
        return $this->cards;
    }
}
?>
