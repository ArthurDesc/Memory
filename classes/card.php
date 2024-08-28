<?php

class Card {
    public $id;
    public $image;

    public function __construct($id, $image) {
        $this->id = $id;
        $this->image = $image;
    }
}
?>
