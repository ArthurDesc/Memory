<?php
class Structure {
    private $cards = [];
    private $totalPairs;
    private $cardPath = './assets/pictures/cards/recto.png';
    
    public function __construct($totalPairs) {
        $this->totalPairs = $totalPairs;
        $this->initializeGame();
    }
    
    private function initializeGame() {
        // Générer les cartes avec des images recto
        for ($i = 1; $i <= $this->totalPairs; $i++) {
            // Créer deux instances pour chaque paire
            $this->cards[] = new Card($i, $this->cardPath . "card$i.png");
            $this->cards[] = new Card($i, $this->cardPath . "card$i.png");
        }
        shuffle($this->cards); // Mélanger les cartes
    }
    
    public function getCards() {
        return $this->cards;
    }
}
?>
