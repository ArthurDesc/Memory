<?php
class Card {
    private $id;
    private $imagePath;
    
    public function __construct($id, $imagePath) {
        $this->id = $id;
        $this->imagePath = $imagePath;
    }
    
    public function getImage() {
        return $this->imagePath;
    }
}
?>
