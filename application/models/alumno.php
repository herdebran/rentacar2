<?php

class AlumnoModel {

    private $PDO;

    public function __construct($poroto) {
        $this->PDO = $poroto->PDO;
    }

    public function getCarrerasOf($idpersona) {
        $sql = "select ac.idpersona, ac.idalumnocarrera, ac.idcarrera, c.nombre as carreranombre, c.descripcion,
            ac.idinstrumento, i.nombre as instrumentonombre, ac.idarea, a.nombre as areanombre
            from alumnocarrera ac
            inner join carreras c on (ac.idcarrera = c.idcarrera)
            inner join instrumentos i on (ac.idinstrumento = i.idinstrumento)
            left join areas a on (ac.idarea = a.idarea)
            where ac.idpersona=:idpersona and ac.estado > 0";
        $params = array(":idpersona" => $idpersona);
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "Persona/getPersonaById", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getMateriasOrigen($idalumnocarrera) {
        $sql = "SELECT  m.materiaCompleta as nombre, am.idalumnomateria, eam.descripcion as estado
                FROM alumnomateria am inner join viewmaterias m on (am.idmateria = m.idmateria and am.idcarrera = m.idcarrera)
                inner join estadoalumnomateria eam on (am.idestadoalumnomateria = eam.idestadoalumnomateria)
                where idalumnocarrera = :idalumnocarrera 
                and am.idestadoalumnomateria in (3,4,7)
                order by orden";
        $params = array(":idalumnocarrera" => $idalumnocarrera);
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "AlumnoModel/getMateriasAprobadas", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getMateriasDestino($idalumnocarrera) {
        $sql = "SELECT  m.materiaCompleta as nombre, am.idalumnomateria, eam.descripcion as estado
                FROM alumnomateria am inner join viewmaterias m on (am.idmateria = m.idmateria and am.idcarrera = m.idcarrera)
                inner join estadoalumnomateria eam on (am.idestadoalumnomateria = eam.idestadoalumnomateria)
                where idalumnocarrera = :idalumnocarrera 
                and am.idestadoalumnomateria not in (6,9)
                order by orden";
        $params = array(":idalumnocarrera" => $idalumnocarrera);
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "AlumnoModel/getMateriasAprobadas", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function insertarEquivalencia($idalumnocarrera, $idalumnomateria) {
        /* Obtengo idmateria origen */
        $sql = "select idmateria from alumnomateria where idalumnomateria = :idalumnomateria";
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "AlumnoModel/isertarEquivalencia", array(":idalumnomateria" => $idalumnomateria));
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        $idmateria = $result[0]["idmateria"];

        /* Obtengo idcarrera destino */
        $sql = "select idcarrera from alumnocarrera where idalumnocarrera = :idalumnocarrera";
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "AlumnoModel/isertarEquivalencia", array(":idalumnocarrera" => $idalumnocarrera));
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        $idcarrera = $result[0]["idcarrera"];

        /* Verifico idmateria en el plan de estudio de carrera destino */
        $sql = "select * from carreramateria where idcarrera = :idcarrera and idmateria = :idmateria";
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "AlumnoModel/isertarEquivalencia", array(":idcarrera" => $idcarrera, ":idmateria" => $idmateria));
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) == 0) {
            return array("ok" => false, "mensaje" => "La materia seleccionada no pertenece al plan de estudio de la carrera destino.");
        }

        $sql = "select * from alumnomateria where idalumnocarrera = :idalumnocarrera and idmateria = :idmateria and idestadoalumnomateria not in (6,9)";
        $this->PDO->prepare($sql);
        $params = array(":idalumnocarrera" => $idalumnocarrera, ":idmateria" => $idmateria);
        $this->PDO->execute($sql, "AlumnoModel/isertarEquivalencia", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        if (count($result) > 0) {
            return array("ok" => false, "mensaje" => "La materia seleccionada ya existe en la carrera destino.");
        }
        
        $sql = "insert into alumnomateria(idalumnocarrera,idpersona,idcarrera,idmateria,idcomision,aniocursada,idestadoalumnomateria,
                fechaaprobacion,usucrea,fechacrea,usumodi,fechamodi)
                select :idalumnocarrera as idalumnocarrera,idpersona,:idcarrera as idcarrera,idmateria,idcomision,aniocursada,
                4 as idestadoalumnomateria,fechaaprobacion,usucrea,fechacrea,usumodi,fechamodi from alumnomateria am
                where am.idalumnomateria=:idalumnomateria";
        $params = array(":idalumnocarrera" => $idalumnocarrera,
            ":idcarrera" => $idcarrera,
            ":idalumnomateria" => $idalumnomateria);
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "AlumnoModel/isertarEquivalencia", $params);

        $lastid = $this->PDO->lastInsertId();

        $sql = "insert into alumnomaterianota(idalumnomateria,idtipoexamen,idexamen,fechaexamen,notaexamen,libro,tomo,folio,
                    usucrea,fechacrea,usumodi,fechamodi)
                    select :lastid as idalumnomateria,idtipoexamen,idexamen,fechaexamen,notaexamen,libro,tomo,folio,
                    usucrea,fechacrea,usumodi,fechamodi from alumnomaterianota
                    where idalumnomateria=:idalumnomateria";
        $params = array(":lastid" => $lastid,
            ":idalumnomateria" => $idalumnomateria);
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "AlumnoModel/isertarEquivalencia", $params);
        return array("ok" => true, "mensaje" => "La materia se paso satisfactoriamente.");
    }

}
