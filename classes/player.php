<?php

class Player {
    public $name;
    public $score;

    public function __construct($name) {
        $this->name = $name;
        $this->score = 0;
    }

    public function updateScore($points) {
        $this->score += $points;
    }
}
?>
