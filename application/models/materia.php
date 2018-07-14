<?php

class MateriaModel {

    private $PDO;
    private $POROTO;

    public function __construct($poroto) {
        $this->PDO = $poroto->PDO;
        $this->POROTO = $poroto;
    }

    public function getMateriaById($idmateria) {
        $sql = "SELECT * from materias where idmateria = :idmateria";
        $params = array(":idmateria" => $idmateria);
        $this->PDO->execute($sql, "MateriaModel/getMateriaById", $params);
        $result = $this->PDO->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

}
