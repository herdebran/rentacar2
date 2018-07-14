<?php
class vermateria {
	
	private $POROTO;
	function __construct($poroto) {
		$this->POROTO=$poroto;
		$this->POROTO->pageHeader[] = array("label"=>"Dashboard","url"=>"");		
	}

	function defentry() {
		if ($this->POROTO->Session->isLogged()) { 		
			header("Location: /", TRUE, 302);
		} else {
			include($this->POROTO->ViewPath . "/-login.php");
		}
	}

	public function vermaterias() {
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Ver Materias Acceso desde Menu')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706

		$db->dbConnect("vermateria/vermaterias/");
		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	
	
		//Obtengo las carreras dependiendo de si soy administrativo directivo profesor o alumno.
		if($ses->tienePermiso('','Ver Materias como Administrativo o Directivo')){
			$sql = "select idcarrera,descripcion from carreras where estado=1 order by 2";
		}
		if($ses->tienePermiso('','Ver Materias como Profesor')){
			$sql = "select distinct ca.idcarrera, ca.descripcion, c.aula";
			$sql.= " from comprofesor cp inner join comisiones c on cp.idcomision=c.idcomision ";
			$sql.= "inner join materias m on m.idmateria=c.idmateria inner join carreramateria cm ";
			$sql.= "on cm.idmateria=m.idmateria inner join carreras ca on cm.idcarrera=ca.idcarrera";
			$sql.= " where cp.idpersona=" . $ses->getIdPersona();
			$sql.= " and ca.estado in (1,2)";
			$sql.= " and c.estado=1";
			$sql.= " and m.estado=1";
			$sql.= " order by 2";			
		}
		if($ses->tienePermiso('','Ver Materias como alumno')){
			$sql = "select c.idcarrera, concat(c.descripcion,' - ',i.nombre) as descripcion";
			$sql.= " from alumnocarrera ac inner join carreras c on ac.idcarrera=c.idcarrera";
			$sql.= " left join instrumentos i on ac.idinstrumento=i.idinstrumento";
			$sql.= " where ac.estado in (1,2,3) and c.estado=1 and ac.idpersona=" . $ses->getIdPersona();
		}
		$viewDataCarreras = $db->getSQLArray($sql);
		$db->dbDisconnect();
		$pageTitle="Ver Materias";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/ver-materias.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}

public function ajaxmaterias() { //Trae las materias de la carrera seleccionada
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$idCarrera = $_POST['ca'];
		$idPersona  = $_POST['pe'];
		
		$db->dbConnect("vermateria/ajaxmaterias/" . $idCarrera . "/" . $idPersona);

		if($ses->tienePermiso('','Ver Materias como Administrativo o Directivo')){ //Administrativo o directivo
			$sql = "select m.idmateria id, m.materiacompleta as nombre";
			$sql.= " from viewmaterias m ";
			$sql.= " where m.idcarrera=" . $db->dbEscape($idCarrera);
			$sql.= " and m.estado=1";
			$sql.= " order by m.orden";
		}
		if($ses->tienePermiso('','Ver Materias como Profesor')){ //Profesor	
			$sql = "select distinct m.idmateria id, m.materiacompleta as nombre";
			$sql.= " from viewmaterias m ";
			$sql.= " inner join comisiones c on c.idmateria=m.idmateria";
			$sql.= " inner join comprofesor cp on cp.idcomision=c.idcomision";
			$sql.= " where m.idcarrera=" . $idCarrera;
			$sql.= " and m.estado=1";
			$sql.= " and c.estado=1";
			$sql.= " and cp.idpersona=" . $idPersona;
			$sql.= " order by m.orden";
		}
		if($ses->tienePermiso('','Ver Materias como alumno')){ //Alumno
			$sql = "select distinct m.idmateria id, m.materiacompleta as nombre";
			$sql.= " from comalumno ca";
			$sql.= " inner join comisiones c on c.idcomision=ca.idcomision";
			$sql.= " inner join viewmaterias m on c.idmateria=m.idmateria";
			$sql.= " where m.idcarrera=" . $idCarrera;
			$sql.= " and m.estado=1";
			$sql.= " and c.estado=1";
			$sql.= " and ca.estado=1";
			$sql.= " and ca.idpersona=" . $idPersona;
			$sql.= " order by m.orden";
		}		
		$result = $db->getSQLArray($sql);
		$db->dbDisconnect();

		echo json_encode($result);
}
	
public function ajaxcomisiones() { //Trae las comisiones de la materia seleccionada.
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$idMateria = $_POST['ma'];
		$idPersona  = $_POST['pe'];

		$db->dbConnect("vermateria/ajaxcomisiones/" . $idMateria . "/" . $idPersona);

		if($ses->tienePermiso('','Ver Materias como Administrativo o Directivo')){ //Administrativo o directivo
			$sql = "select distinct c.idcomision id, c.nombre from carreramateria cm inner join ";
			$sql.= "comisiones c on cm.idmateria=c.idmateria";
			$sql.= " where cm.idmateria=" . $idMateria;
			$sql.= " and c.estado=1";
			$sql.= " order by c.anio desc,c.nombre asc";
		}
		if($ses->tienePermiso('','Ver Materias como Profesor')){ //Profesor	
			$sql = "select distinct c.idcomision id, c.nombre";
			$sql.= " from viewmaterias m ";
			$sql.= " inner join comisiones c on c.idmateria=m.idmateria";
			$sql.= " inner join comprofesor cp on cp.idcomision=c.idcomision";
			$sql.= " where m.idmateria=" . $idMateria;
			$sql.= " and m.estado=1";
			$sql.= " and c.estado=1";
			$sql.= " and cp.idpersona=" . $idPersona;
			$sql.= " order by c.anio desc,c.nombre asc";
		}
		if($ses->tienePermiso('','Ver Materias como alumno')){ //Alumno
			$sql = "select distinct c.idcomision id, c.nombre";
			$sql.= " from comalumno ca";
			$sql.= " inner join comisiones c on c.idcomision=ca.idcomision";
			$sql.= " inner join viewmaterias m on c.idmateria=m.idmateria";
			$sql.= " where m.idmateria=" . $idMateria;
			$sql.= " and m.estado=1";
			$sql.= " and c.estado=1";
			$sql.= " and ca.estado=1";
			$sql.= " and ca.idpersona=" . $idPersona;
			$sql.= " order by c.anio desc,c.nombre asc";
		}		
		$result = $db->getSQLArray($sql);
		$db->dbDisconnect();

		echo json_encode($result);		
}	

public function ajaxcomision() { //Trae los alumnos de la comision seleccionada

		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		
		$idComision = $_POST['co'];
		$idCarrera = $_POST['ca'];
		$idPersona  = $_POST['pe'];

		$db->dbConnect("vermateria/ajaxcomision/" . $idComision . "/" . $idPersona);

		$sql = "select i.nombre instrumento, am.idcarrera,am.idalumnocarrera, p.idpersona, p.apellido, p.nombre, p.documentonro, ";
		$sql.= "p.legajo, eam.descripcion eam, am.idestadoalumnomateria, am.idAlumnoMateria";
		$sql.= " ,max(case when amn.idtipoexamen=3 then amn.notaexamen else null end) primerparcial";
		$sql.= " ,max(case when amn.idtipoexamen=4 then amn.notaexamen else null end) segundoparcial";
		$sql.= " from alumnomateria am";
		$sql.= " inner join estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria";
		$sql.= " inner join comisiones c on c.idmateria=am.idmateria and c.idcomision=am.idcomision";
		$sql.= " inner join personas p on am.idpersona = p.idpersona";
		$sql.= " left join alumnomaterianota amn on amn.idalumnomateria = am.idalumnomateria and amn.idtipoexamen in (3,4)";
		$sql.= " left join alumnocarrera ac on am.idalumnocarrera=ac.idalumnocarrera";
		$sql.= " left join instrumentos i on ac.idinstrumento=i.idinstrumento";
		$sql.= " where c.idcomision=" . $idComision;
		if($ses->tienePermiso('','Ver Materias como alumno')){ //Alumno
			$sql.= " and p.idpersona=" . $idPersona;
		}
		$sql.= " group by p.idpersona";
		$sql.= " order by p.apellido, p.nombre";
		$result = $db->getSQLArray($sql);
		$db->dbDisconnect();

		echo json_encode($result);		
	}

        public function imprimirActaParcial($idComision = 0, $idPersona = 0){
                //Recibiendo la comision traigo los datos de la misma asi como los alumnos para imprimir el acta.
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		
		
		$db->dbConnect("vermateria/imprimircomision/" . $idComision);
		
		$sql = "SELECT idcomision,nombre, aula from comisiones where idcomision=".$idComision;
		$arrComisiones = $db->getSQLArray($sql);
		
		foreach($arrComisiones as &$comision){
			//Traigo los horarios
			$sql = "SELECT dia, date_format(inicio,'%H:%i') inicio, date_format(fin,'%H:%i') fin";
			$sql.= " FROM comhorario";
			$sql.= " WHERE idcomision=" . $idComision;			
			$arrHorarios = $db->getSQLArray($sql);
			$comision['horarios'] = "";
			foreach ($arrHorarios as $horario) 
			$comision['horarios'] .= " | " . $horario['dia'] . " " . $horario['inicio'] . " a " . $horario['fin'];
			
			$comision['horarios'] = substr($comision['horarios'], 3);
			unset($arrHorarios);
			
			//Traigo los datos del profesor de la comision
			$sql = "SELECT p.apellido, p.nombre, sr.descripcion";
			$sql.= " FROM comprofesor cp";
			$sql.= " INNER JOIN personas p on cp.idpersona=p.idpersona and p.estado=1";
			$sql.= " INNER JOIN situacionrevista sr on cp.situacionrevista_id=sr.id";
			$sql.= " WHERE cp.idcomision=" . $idComision;
			$arrProfesores = $db->getSQLArray($sql);
			$comision['profesores'] = "";
			foreach ($arrProfesores as $profesor) 
			$comision['profesores'] .= " | " . $profesor['apellido'] . "," . $profesor['nombre'] . " (" . $profesor['descripcion'] . ")";
			$comision['profesores'] = substr($comision['profesores'], 3);
			unset($arrProfesores);
		}
		
		$sql = "select i.nombre instrumento, am.idcarrera,am.idalumnocarrera, p.idpersona, p.apellido, p.nombre, p.documentonro, ";
		$sql.= "p.legajo, eam.descripcion eam, am.idestadoalumnomateria, am.idAlumnoMateria";
		$sql.= " ,max(case when amn.idtipoexamen=3 then amn.notaexamen else null end) primerparcial";
		$sql.= " ,max(case when amn.idtipoexamen=4 then amn.notaexamen else null end) segundoparcial";
		$sql.= " from alumnomateria am";
		$sql.= " inner join estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria";
		$sql.= " inner join comisiones c on c.idmateria=am.idmateria and c.idcomision=am.idcomision";
		$sql.= " inner join personas p on am.idpersona = p.idpersona";
		$sql.= " left join alumnomaterianota amn on amn.idalumnomateria = am.idalumnomateria and amn.idtipoexamen in (3,4)";
		$sql.= " left join alumnocarrera ac on am.idalumnocarrera=ac.idalumnocarrera";
		$sql.= " left join instrumentos i on ac.idinstrumento=i.idinstrumento";
		$sql.= " where c.idcomision=" . $idComision;
		if($ses->tienePermiso('','Ver Materias como alumno')){ //Alumno
			$sql.= " and p.idpersona=" . $idPersona;
		}
                // REVISAR: Comento el estadoalumnomateria porque es innecesario 
                // dado que quiero todoslos alimnos con sus notas para sacar el
                // promedio sin importar si estan curasando, aprobados o 
                // cancelados o cualquier otro estado...
                //$sql.= " and am.idestadoalumnomateria=".$this->POROTO->Config['estado_alumnomateria_cursando'];
		$sql.= " group by p.idpersona";
		$sql.= " order by p.apellido, p.nombre";
		$alumnos = $db->getSQLArray($sql);
		
		$db->dbDisconnect();
	
		include($this->POROTO->ViewPath . "/acta-parcial.php");
	}
        
        public function imprimirComision($idComision = 0, $idPersona = 0){
	//Recibiendo la comision traigo los datos de la misma asi como los alumnos para imprimir asistencia.
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		
		
		$db->dbConnect("vermateria/imprimircomision/" . $idComision);
		
		$sql = "SELECT idcomision,nombre, aula from comisiones where idcomision=".$idComision;
		$arrComisiones = $db->getSQLArray($sql);
		
		foreach($arrComisiones as &$comision){
			//Traigo los horarios
			$sql = "SELECT dia, date_format(inicio,'%H:%i') inicio, date_format(fin,'%H:%i') fin";
			$sql.= " FROM comhorario";
			$sql.= " WHERE idcomision=" . $idComision;			
			$arrHorarios = $db->getSQLArray($sql);
			$comision['horarios'] = "";
			foreach ($arrHorarios as $horario) 
			$comision['horarios'] .= " | " . $horario['dia'] . " " . $horario['inicio'] . " a " . $horario['fin'];
			
			$comision['horarios'] = substr($comision['horarios'], 3);
			unset($arrHorarios);
			
			//Traigo los datos del profesor de la comision
			$sql = "SELECT p.apellido, p.nombre, sr.descripcion";
			$sql.= " FROM comprofesor cp";
			$sql.= " INNER JOIN personas p on cp.idpersona=p.idpersona and p.estado=1";
			$sql.= " INNER JOIN situacionrevista sr on cp.situacionrevista_id=sr.id";
			$sql.= " WHERE cp.idcomision=" . $idComision;
			$arrProfesores = $db->getSQLArray($sql);
			$comision['profesores'] = "";
			foreach ($arrProfesores as $profesor) 
			$comision['profesores'] .= " | " . $profesor['apellido'] . "," . $profesor['nombre'] . " (" . $profesor['descripcion'] . ")";
			$comision['profesores'] = substr($comision['profesores'], 3);
			unset($arrProfesores);
		}
		
		$sql = "select i.nombre instrumento, am.idcarrera,am.idalumnocarrera, p.idpersona, p.apellido, p.nombre, p.documentonro, ";
		$sql.= "p.legajo, eam.descripcion eam, am.idestadoalumnomateria, am.idAlumnoMateria";
		$sql.= " ,max(case when amn.idtipoexamen=3 then amn.notaexamen else null end) primerparcial";
		$sql.= " ,max(case when amn.idtipoexamen=4 then amn.notaexamen else null end) segundoparcial";
		$sql.= " from alumnomateria am";
		$sql.= " inner join estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria";
		$sql.= " inner join comisiones c on c.idmateria=am.idmateria and c.idcomision=am.idcomision";
		$sql.= " inner join personas p on am.idpersona = p.idpersona";
		$sql.= " left join alumnomaterianota amn on amn.idalumnomateria = am.idalumnomateria and amn.idtipoexamen in (3,4)";
		$sql.= " left join alumnocarrera ac on am.idalumnocarrera=ac.idalumnocarrera";
		$sql.= " left join instrumentos i on ac.idinstrumento=i.idinstrumento";
		$sql.= " where c.idcomision=" . $idComision;
		if($ses->tienePermiso('','Ver Materias como alumno')){ //Alumno
			$sql.= " and p.idpersona=" . $idPersona;
		}
                $sql.= " and am.idestadoalumnomateria=".$this->POROTO->Config['estado_alumnomateria_cursando'];
		$sql.= " group by p.idpersona";
		$sql.= " order by p.apellido, p.nombre";
		$alumnos = $db->getSQLArray($sql);
		
		$db->dbDisconnect();
	
		include($this->POROTO->ViewPath . "/informe-asistencia.php");
	}

public function ajaxdatoscomision() { //Seguir
//Funcion a utilizarse desde Ver-Materias para obtener los datos basicos de la comision seleccionada en la lista.
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		
		$idComision = $_POST['comision'];
		
		$db->dbConnect("vermateria/ajaxdatoscomision/" . $idComision);
		
		$sql = "SELECT idcomision, aula from comisiones where idcomision=".$idComision;
		$arrComisiones = $db->getSQLArray($sql);
		
		foreach($arrComisiones as &$comision){
			//Traigo los horarios
			$sql = "SELECT dia, date_format(inicio,'%H:%i') inicio, date_format(fin,'%H:%i') fin";
			$sql.= " FROM comhorario";
			$sql.= " WHERE idcomision=" . $idComision;			
			$arrHorarios = $db->getSQLArray($sql);
			$comision['horarios'] = "";
			foreach ($arrHorarios as $horario) 
			$comision['horarios'] .= " | " . $horario['dia'] . " " . $horario['inicio'] . " a " . $horario['fin'];
			
			$comision['horarios'] = substr($comision['horarios'], 3);
			unset($arrHorarios);
			
			//Traigo los datos del profesor de la comision
			$sql = "SELECT p.apellido, p.nombre, sr.descripcion";
			$sql.= " FROM comprofesor cp";
			$sql.= " INNER JOIN personas p on cp.idpersona=p.idpersona and p.estado=1";
			$sql.= " INNER JOIN situacionrevista sr on cp.situacionrevista_id=sr.id";
			$sql.= " WHERE cp.idcomision=" . $idComision;
			$arrProfesores = $db->getSQLArray($sql);
			$comision['profesores'] = "";
			foreach ($arrProfesores as $profesor) 
			$comision['profesores'] .= " | " . $profesor['apellido'] . "," . $profesor['nombre'] . " (" . $profesor['descripcion'] . ")";
			$comision['profesores'] = substr($comision['profesores'], 3);
			unset($arrProfesores);
		}
		$db->dbDisconnect();
		echo json_encode($arrComisiones);
}
	
public function ajaxsetmail() { //Setear el listado de destinatarios.
        $lib = & $this->POROTO->Libraries['siteLibrary'];
                
        $db = & $this->POROTO->DB;
        $idComision = $_POST['co'];
            
        $db->dbConnect("home/ajaxsendmail/" . $idComision);
		
		$sql= 'select distinct u.email from comalumno ca inner join alumnomateria am 
			   on ca.idpersona=am.idpersona and ca.idcomision=am.idcomision
  			   inner join usuarios u on ca.idpersona=u.idpersona
			   where ca.idcomision='.$idComision;
	/*		   
        $sql = 'select p.email  from alumnomateria am
                inner join estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria
                inner join comisiones c on c.idmateria=am.idmateria and c.idcomision=am.idcomision
                inner join usuarios p on am.idpersona = p.idpersona
                left join alumnomaterianota amn on amn.idalumnomateria = am.idalumnomateria and amn.idtipoexamen in (3,4)
                left join alumnocarrera ac on ac.idpersona=p.idpersona and ac.idcarrera= 5
                left join instrumentos i on ac.idinstrumento=i.idinstrumento
                where c.idcomision='.$idComision;
				*/
        $result = $db->getSQLArray($sql);
        $db->dbDisconnect();
        $mailto = '';
        foreach ($result as $anEmail => $email)
        {
            $mailto = $mailto . $email['email'].',';
        }      
        echo json_encode($mailto);
    }
	
}
?>