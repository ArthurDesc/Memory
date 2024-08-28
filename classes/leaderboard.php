<?php

class Leaderboard {
    private $scores = [];

    public function __construct() {
        // Récupérer les scores de la base de données (ou d'un fichier)
        $this->loadScores();
    }

    private function loadScores() {
        // Ici vous chargeriez les scores depuis une base de données ou un fichier
    }

    public function addScore($playerName, $score) {
        $this->scores[] = ['name' => $playerName, 'score' => $score];
        $this->saveScores();
    }

    private function saveScores() {
        // Sauvegarder les scores dans la base de données ou un fichier
    }

    public function getTopScores($limit = 10) {
        // Trier les scores et retourner les meilleurs
        usort($this->scores, function($a, $b) {
            return $b['score'] - $a['score'];
        });

        return array_slice($this->scores, 0, $limit);
    }
}
?>
