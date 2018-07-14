<?php

class AlumnoMateriaModel {

    private $PDO;
    private $POROTO;
    
    public function __construct($poroto) {
        $this->PDO = $poroto->PDO;
         $this->POROTO = $poroto;
    }

    public function quitarCondicionalidad($idalumnomateria){
       $sql="update alumnomateria_condicionalidades set condicional=0 where idalumnomateria=:idalumnomateria";
       $params = array(":idalumnomateria" => $idalumnomateria);
       $this->PDO->prepare($sql);
       $this->PDO->execute($sql, "AlumnoMateriaModel/modificarCondicionalidades", $params);
       return array("ok" => true, "mensaje" => "Condicionalidad modificada.");
   } 
    
    public function quitarCondicionalidadSimultaneos($idalumnomateria){
       $sql="update alumnomateria_condicionalidades set condicionalregla6=0 where idalumnomateria=:idalumnomateria";
       $params = array(":idalumnomateria" => $idalumnomateria);
       $this->PDO->prepare($sql);
       $this->PDO->execute($sql, "AlumnoMateriaModel/quitarCondicionalidadSimultaneos", $params);
       return array("ok" => true, "mensaje" => "Condicionalidad modificada.");
   } 
    public function getAlumnoMateriaCondicionales(){
        //Obtiene alumnomateria condicionales siempre y cuando las carreras NO ESTEN FINALIZADAS.
        $sql = "SELECT  concat(p.apellido,' ',p.nombre) as alumno,am.idpersona,m.materiaCompleta as nombre, am.aniocursada,am.idalumnomateria, 
                eam.descripcion as estado,amc.condicional,am.idalumnocarrera,c.nombre as carrera,am.idcomision,am.idmateria,ac.idcarrera,
                concat(td.descripcion,' ',p.documentonro) as documento
                FROM alumnomateria am inner join alumnocarrera ac on am.idalumnocarrera=ac.idalumnocarrera 
                inner join viewmaterias m on (am.idmateria = m.idmateria and ac.idcarrera = m.idcarrera)
                inner join estadoalumnomateria eam on (am.idestadoalumnomateria = eam.idestadoalumnomateria)
                inner join personas p on am.idpersona=p.idpersona
                inner join tipodoc td on p.tipodoc=td.id
                left join alumnomateria_condicionalidades amc on am.idalumnomateria=amc.idalumnomateria
                inner join carreras c on ac.idcarrera=c.idcarrera
                where amc.condicional=1 and ac.estado in (1,3) order by am.idpersona,am.idalumnomateria
                ";
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "AlumnoMateriaModel/getAlumnoMateriaCondicionales");
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function getAlumnoMateriaCondicionalesSimultaneos(){
        //Obtiene alumnomateria condicionales simultaneos siempre y cuando las carreras NO ESTEN FINALIZADAS.
        $sql = "select am.idpersona,concat(p.apellido,' ',p.nombre) as alumno,concat(td.descripcion,' ',p.documentonro) as documento,
                ac.idalumnocarrera,c.nombre as carrera, vm.materiacompleta as materia ,am.idalumnomateria,
                amc.condicionalregla6,am.idcomision,am.idmateria,ac.idcarrera,am.idestadoalumnomateria,
                eam.descripcion as estado
                from alumnomateria am inner join alumnomateria_condicionalidades
                amc on am.idalumnomateria=amc.idalumnomateria
                inner join alumnocarrera ac on am.idalumnocarrera=ac.idalumnocarrera
                inner join carreras c on ac.idcarrera=c.idcarrera
                inner join personas p on am.idpersona=p.idpersona
                inner join tipodoc td on p.tipodoc=td.id
                inner join viewmaterias vm on ac.idcarrera=vm.idcarrera and am.idmateria=vm.idmateria
                inner join estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria
                where amc.condicionalregla6=1 and ac.estado in (1,3)
                order by am.idpersona,am.idalumnomateria
                ";
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "AlumnoMateriaModel/getAlumnoMateriaCondicionalesSimultaneos");
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function desaprobarCondicionales ($idAlumnoCarrera, $idAlumno, $idAlumnoMateria, $idEstado, $usumodi,$idComision=0) {
		
		$sql = "update alumnomateria set idestadoalumnomateria=" . $idEstado . ",";
		$sql.= " usumodi='" . $usumodi . "',";
		$sql.= " fechamodi=CURRENT_TIMESTAMP";
		$sql.= " where idalumnomateria=" . $idAlumnoMateria;
		$this->PDO->prepare($sql);
                $this->PDO->execute($sql, "AlumnoMateriaModel/desaprobarCondicionales");
		               
		//Actualizo el cupo si es que esta en una comisión y cambio de estado.
		//En caso de pasar a estado LIBRE o CANCELADA descontar del cupo para que pueda reutilizarse.
		//En realidad en todos los casos verifico y actualizo el cupo por
		// si pase de cursando a cancelada y luego a cursando de nuevo.
		$sql= " SELECT ca.idcomcupo from alumnomateria am inner join comalumno ca on am.idcomision=ca.idcomision";
		$sql.= " and am.idpersona=ca.idpersona ";
		$sql.= " where am.idalumnomateria=" . $idAlumnoMateria. " and not am.idcomision is null and ca.estado=1";
                $this->PDO->prepare($sql);
                $this->PDO->execute($sql, "AlumnoMateriaModel/desaprobarCondicionales");
                $arr = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
		if(count($arr)>0){
			$dataCupo=$arr[0]['idcomcupo'];
			//Actualizo ComCupos, si la pase a cancelada o libre actualizaa el cupo, caso contrario continuara igual.
			$sql=  " update comcupos ";
			$sql.= " set cantdisponible=cantidad - ( ";
			$sql.= " SELECT count(*) from alumnomateria am inner join comalumno ca on am.idcomision=ca.idcomision ";
			$sql.= " and am.idpersona=ca.idpersona ";
			$sql.= " where not am.idcomision is null and ca.estado=1 and ca.idcomcupo=".$dataCupo;
			$sql.= " and am.idestadoalumnomateria in (";
			$sql.= $this->POROTO->Config['estado_alumnomateria_cursando'].",";
			$sql.= $this->POROTO->Config['estado_alumnomateria_aprobada'].",";
			$sql.= $this->POROTO->Config['estado_alumnomateria_aprobadaxequiv'].",";
			$sql.= $this->POROTO->Config['estado_alumnomateria_cursadaaprobada'].",";
			$sql.= $this->POROTO->Config['estado_alumnomateria_nivelacion'];
			$sql.= ")";
			$sql.= " ) ";
			$sql.= " where idcomcupo=" . $dataCupo;
			$this->PDO->prepare($sql);
                        $this->PDO->execute($sql, "AlumnoMateriaModel/desaprobarCondicionales");
		}

	}

}
