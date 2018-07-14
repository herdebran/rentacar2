<?php

class analiticomodificar {

    private $POROTO;
    private $PDO;

    function __construct($poroto) {
        $this->POROTO = $poroto;
        $this->PDO = $this->POROTO->PDO->getPdo();
        $this->POROTO->pageHeader[] = array("label" => "Dashboard", "url" => "");
    }

    function defentry() {
        if ($this->POROTO->Session->isLogged()) {
            header("Location: /", TRUE, 302);
        } else {
            include($this->POROTO->ViewPath . "/-login.php");
        }
    }

        public function index($idAlumnoMateria=0) {

        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $lib = & $this->POROTO->Libraries['siteLibrary'];

        //Acceso
          if (!$ses->tienePermiso('', 'Analitico Edición')) {
          $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
          header("Location: /", TRUE, 302);
          exit();
          }
         
        $db->dbConnect("analiticomodificar/index/" . $idAlumnoMateria);
		$dataIdAlumnoMateria = $db->dbEscape($idAlumnoMateria);
       
        $rolActual = $ses->getIdRole();
        $arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());
	
		//Detalle de alumnomateria
		$sql= " select am.idalumnomateria,am.idpersona,concat(p.nombre,' ',p.apellido) as alumno,p.documentonro,am.idalumnocarrera,c.descripcion as ";
		$sql.=" carrera,am.idmateria,m.nombre as materia,am.idcomision,co.nombre as comision, ";
		$sql.=" am.aniocursada,am.idestadoalumnomateria,eam.descripcion as estadomateria, ";
		$sql.=" date_format(am.fechaaprobacion,'%d/%m/%Y') as fechaaprobacion,";
		$sql.=" am.usumodi,date_format(am.fechamodi,'%d/%m/%Y') as fechamodi ";
		$sql.=" from alumnomateria am inner join personas p on am.idpersona=p.idpersona ";
                $sql.=" left join alumnocarrera ac on am.idalumnocarrera=ac.idalumnocarrera ";
		$sql.=" left join carreras c on ac.idcarrera=c.idcarrera ";
		$sql.=" left join materias m on am.idmateria=m.idmateria ";
		$sql.=" left join comisiones co on am.idcomision=co.idcomision ";
		$sql.=" left join estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria ";
		$sql.=" where idalumnomateria= :idalumnomateria ";
        $query = $this->PDO->prepare($sql);
        $params = array(':idalumnomateria' => $dataIdAlumnoMateria);
        $query->execute($params);
		$alumnomateria_detalle = $query->fetchAll(PDO::FETCH_ASSOC);
        
        $sql = "SELECT amn.idalumnomaterianota,amn.idtipoexamen,t.nombre as tipoexamen,";
		$sql.=" amn.idexamen,date_format(amn.fechaexamen,'%d/%m/%Y') as fechaexamen,amn.notaexamen,amn.libro,";
		$sql.=" amn.tomo,amn.folio,amn.usucrea,date_format(amn.fechacrea,'%d/%m/%Y') as fechacrea,";
		$sql.=" amn.usumodi,date_format(amn.fechamodi,'%d/%m/%Y') as fechamodi ";
		$sql.= " FROM alumnomaterianota amn inner join tipoexamen t on amn.idtipoexamen=t.idtipoexamen ";
		$sql.= " where idalumnomateria= :idalumnomateria";
        $query = $this->PDO->prepare($sql);
		$params = array(':idalumnomateria' => $dataIdAlumnoMateria );
        $query->execute($params);
        $examenes = $query->fetchAll(PDO::FETCH_ASSOC);
        
        $sql = "SELECT * FROM `tipoexamen` order by nombre";
        $query = $this->PDO->prepare($sql);
        $query->execute();
        $tipo_examen = $query->fetchAll(PDO::FETCH_ASSOC);
        

        
		//Detalle de alumnomateria_historial
                $sql= " select 9999999999 as id,am.idalumnomateria,am.idpersona,concat(p.nombre,' ',p.apellido) as ";
		$sql.=" alumno,p.documentonro,am.idalumnocarrera,ac.idcarrera,c.descripcion as ";
		$sql.=" carrera,am.idmateria,m.nombre as materia,am.idcomision,co.nombre as comision, ";
		$sql.=" am.aniocursada,am.idestadoalumnomateria,eam.descripcion as estadomateria, ";
		$sql.=" date_format(am.fechaaprobacion,'%d/%m/%Y') as fechaaprobacion,am.usucrea,";
		$sql.=" date_format(am.fechacrea,'%d/%m/%Y') as fechacrea,am.usumodi,";
		$sql.=" date_format(am.fechamodi,'%d/%m/%Y') as fechamodi,'' as accion ";
		$sql.=" from alumnomateria am inner join personas p on am.idpersona=p.idpersona ";
                $sql.=" left join alumnocarrera ac on am.idalumnocarrera=ac.idalumnocarrera ";
		$sql.=" left join carreras c on ac.idcarrera=c.idcarrera ";
		$sql.=" left join materias m on am.idmateria=m.idmateria ";
		$sql.=" left join comisiones co on am.idcomision=co.idcomision ";
		$sql.=" left join estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria ";
		$sql.=" where idalumnomateria= :idalumnomateria";
                $sql.=" UNION ALL ";
		$sql.= " select am.id as id,am.idalumnomateria,am.idpersona,concat(p.nombre,' ',p.apellido) as ";
		$sql.=" alumno,p.documentonro,am.idalumnocarrera,ac.idcarrera,c.descripcion as ";
		$sql.=" carrera,am.idmateria,m.nombre as materia,am.idcomision,co.nombre as comision, ";
		$sql.=" am.aniocursada,am.idestadoalumnomateria,eam.descripcion as estadomateria, ";
		$sql.=" date_format(am.fechaaprobacion,'%d/%m/%Y') as fechaaprobacion,am.usucrea,";
		$sql.=" date_format(am.fechacrea,'%d/%m/%Y') as fechacrea,am.usumodi,";
		$sql.=" date_format(am.fechamodi,'%d/%m/%Y') as fechamodi,am.accion ";
		$sql.=" from alumnomateria_historial am inner join personas p on am.idpersona=p.idpersona ";
                $sql.=" left join alumnocarrera ac on am.idalumnocarrera=ac.idalumnocarrera ";
		$sql.=" left join carreras c on ac.idcarrera=c.idcarrera ";
		$sql.=" left join materias m on am.idmateria=m.idmateria ";
		$sql.=" left join comisiones co on am.idcomision=co.idcomision ";
		$sql.=" left join estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria ";
		$sql.=" where idalumnomateria= :idalumnomateria order by id desc ";
        $query = $this->PDO->prepare($sql);
        $params = array(':idalumnomateria' => $dataIdAlumnoMateria);
        $query->execute($params);
		$alumnomateria_historial = $query->fetchAll(PDO::FETCH_ASSOC);
		
		//Detalle de alumnomaterianota_historial
		$sql= " select a.id,a.idalumnomaterianota,a.idalumnomateria,a.idtipoexamen,te.nombre as ";
		$sql.=" tipoexamen,a.idexamen,date_format(a.fechaexamen,'%d/%m/%Y') as fechaexamen, ";
		$sql.=" a.notaexamen,a.libro,a.tomo,a.folio,a.usucrea,";
		$sql.=" date_format(a.fechacrea,'%d/%m/%Y') as fechacrea,a.usumodi,";
		$sql.=" date_format(a.fechamodi,'%d/%m/%Y') as fechamodi,a.accion ";
		$sql.=" from alumnomaterianota_historial a inner join tipoexamen te on a.idtipoexamen=te.idtipoexamen ";
		$sql.=" where a.idalumnomateria= :idalumnomateria order by a.id desc ";
        $query = $this->PDO->prepare($sql);
        $params = array(':idalumnomateria' => $dataIdAlumnoMateria);
        $query->execute($params);
		$alumnomaterianota_historial = $query->fetchAll(PDO::FETCH_ASSOC);

        $pageTitle = "Modificacion de Analítico";
        include($this->POROTO->ViewPath . "/-header.php");
        include($this->POROTO->ViewPath . "/analitico-modificar.php");
        include($this->POROTO->ViewPath . "/-footer.php");
    }
	
	public function modificarmateria($idAlumnoMateria=0) {
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		
		$db->dbConnect("analiticomodificar/modificarmateria");
		$ok=true;
		
		$datos=$_POST['datos']; //Obtengo el array datos
		
		$dataAnioCursada = $db->dbEscape($datos['anio-cursada']);
		$dataFechaAprobacion = $db->dbEscape($datos['fecha-aprobacion']);
		
		if($dataFechaAprobacion!=""){
			if($lib->validateDate($dataFechaAprobacion)){
				$dataFechaAprobacion=$lib->dateDMY2YMD($dataFechaAprobacion);
			}else{
				$ses->setMessage("Fecha de Aprobación Inválida.", SessionMessageType::TransactionError);
				$ok=false;
			}
		}else{	
			$dataFechaAprobacion=null;
		}

		if ($ok){
			$sql = "update alumnomateria set aniocursada= :aniocursada, fechaaprobacion= :fechaaprobacion, ";
			$sql.= "usumodi= :usumodi, fechamodi=now() ";
			$sql.= "where idalumnomateria= :idalumnomateria"; 
		
			$query = $this->PDO->prepare($sql);
        	$params = array(':idalumnomateria' => $idAlumnoMateria,
						':aniocursada' => $dataAnioCursada,
						':fechaaprobacion' => $dataFechaAprobacion,
						':usumodi' => $ses->getUsuario());
		
        	if ($query->execute($params)){
			$ses->setMessage("Datos AlumnoMateria modificados con éxito", SessionMessageType::Success);	
			}
			else
			{
				$ses->setMessage("Error al modificar los datos. ", SessionMessageType::TransactionError);
			}
		}
		$db->dbDisconnect();

		header("Location: /analitico-modificar/".$idAlumnoMateria, TRUE, 302);
	}
	
	public function crearNota($idAlumnoMateria=0) {
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		$db->dbConnect("analiticomodificar/crear");
		
		//Cambio 38 Leo 20170706
		//if(!$ses->tienePermiso('','Gestion de Examenes Agregar o Modificar')){
  		//	    $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
		//		header("Location: /gestion-examenes", TRUE, 302);
		//		exit();
		//}
		//Fin Cambio 38 Leo 20170706
		$nota=$_POST['nota']; //Obtengo el array nota.
		
		$dataTipoExamen = $db->dbEscape($nota['tipoexamen']);
		$dataFecha = $db->dbEscape($nota['fecha']);
		$dataNota = $db->dbEscape($nota['nota']);
		if ($dataTipoExamen==1 || $dataTipoExamen==2 || $dataTipoExamen==5 || $dataTipoExamen==6){
			$dataLibro = strtoupper($db->dbEscape($nota['libro']));
			$dataTomo = strtoupper($db->dbEscape($nota['tomo']));
			$dataFolio = strtoupper($db->dbEscape($nota['folio']));
		}

		if($dataTipoExamen==1 || $dataTipoExamen==2 || $dataTipoExamen==5 || $dataTipoExamen==6){
		$sql = "insert into alumnomaterianota (idalumnomateria,idtipoexamen,idexamen,fechaexamen,";
		$sql.= "notaexamen,libro,tomo,folio,usucrea,fechacrea) ";
		$sql.= "values(:idalumnomateria,:idtipoexamen,null,:fechaexamen,:notaexamen,";
		$sql.= ":libro,:tomo,:folio,";
		$sql.= ":usucrea,now())";
		}
		else
		{
		$sql = "insert into alumnomaterianota (idalumnomateria,idtipoexamen,idexamen,fechaexamen,";
		$sql.= "notaexamen,usucrea,fechacrea) ";
		$sql.= "values(:idalumnomateria,:idtipoexamen,null,:fechaexamen,:notaexamen,";
		$sql.= ":usucrea,now())";
		}

		$query = $this->PDO->prepare($sql);

		
		if($dataTipoExamen==1 || $dataTipoExamen==2 || $dataTipoExamen==5 || $dataTipoExamen==6){
		$params = array(':idalumnomateria' => $idAlumnoMateria, ':idtipoexamen' => $dataTipoExamen, 
		':fechaexamen' => $lib->dateDMY2YMD($dataFecha), ':notaexamen' => $dataNota, 
		':libro' => $dataLibro, ':tomo' => $dataTomo, ':folio' => $dataFolio, ':usucrea' =>  $ses->getUsuario());
		}else{
		$params = array(':idalumnomateria' => $idAlumnoMateria, ':idtipoexamen' => $dataTipoExamen, 
		':fechaexamen' => $lib->dateDMY2YMD($dataFecha), ':notaexamen' => $dataNota, ':usucrea' =>  $ses->getUsuario());
		}
		
		if ($query->execute($params)){
				$ses->setMessage("Nota creada con éxito", SessionMessageType::Success);	
			}
			else
			{
				$ses->setMessage("Error al guardar la nota. ", SessionMessageType::TransactionError);
			}
		$db->dbDisconnect();
		
		header("Location: /analitico-modificar/".$idAlumnoMateria, TRUE, 302);
	}
	
	
	public function eliminarNota($idAlumnoMateria,$idAlumnoMateriaNota) {
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$db->dbConnect("analiticomodificar/index/" . $idAlumnoMateriaNota);
		$dataIdAlumnoMateriaNota = $db->dbEscape($idAlumnoMateriaNota);

		$sql = "DELETE FROM alumnomaterianota WHERE idalumnomaterianota= :idalumnomaterianota";
		$query = $this->PDO->prepare($sql);
        $params = array(':idalumnomaterianota' => $dataIdAlumnoMateriaNota);
        if ($query->execute($params)){
			$ses->setMessage("Nota eliminada con éxito", SessionMessageType::Success);	
			}
			else
			{
				$ses->setMessage("Error al eliminar la nota. ", SessionMessageType::TransactionError);
			}
		
		$db->dbDisconnect();

		header("Location: /analitico-modificar/".$idAlumnoMateria, TRUE, 302);
	}
	
	public function eliminarmateria($idAlumnoMateria) {
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$db->dbConnect("analiticomodificar/eliminarmateria/" . $idAlumnoMateria);

        //Acceso
          if (!$ses->tienePermiso('', 'Analitico Edición')) {
          $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
          header("Location: /", TRUE, 302);
          exit();
          }
		
		//Validamos que no tenga notas, ni se este cursando la materia, en cuyo caso tampoco podemos eliminarla
		$sql = "select am.idalumnomateria,amn.idalumnomaterianota,am.idpersona,am.idcarrera,am.idestadoalumnomateria ";
		$sql.= "FROM alumnomateria am left join alumnomaterianota amn on ";
		$sql.= "am.idalumnomateria=amn.idalumnomateria ";
		$sql.= "WHERE am.idalumnomateria= :idalumnomateria";
		$query = $this->PDO->prepare($sql);
        $params = array(':idalumnomateria' => $idAlumnoMateria);
		$query->execute($params);
		$alumnomaterianotas = $query->fetchAll(PDO::FETCH_ASSOC);
		$notasExistentes=false;
		$cursando=false;
		$IDCarrera=0;
		$IDPersona=0;
		
		foreach ($alumnomaterianotas as $key => $examen){
			$IDCarrera=$examen["idcarrera"];
			$IDPersona=$examen["idpersona"];
			if($examen["idalumnomaterianota"])	$notasExistentes=true;
			if($examen["idestadoalumnomateria"]==$this->POROTO->Config['estado_alumnomateria_cursando'])	$cursando=true;
		}

		if ($notasExistentes || $cursando){
			if($notasExistentes){
			$ses->setMessage("No es posible eliminar la materia si tiene notas asignadas. Elimine todas las notas primero. ", SessionMessageType::TransactionError);
			}
			if($cursando){
			$ses->setMessage("No es posible eliminar la materia si esta en estado CURSANDO. Cambie el estado a CANCELADA, LIBRE o DESAPROBADA desde el analítico y luego elimine la materia. ", SessionMessageType::TransactionError);
			}
			$db->dbDisconnect();
			header("Location: /analitico-modificar/".$idAlumnoMateria, TRUE, 302);
			exit();
		}
		else
		{			
			$sql = "DELETE FROM alumnomateria WHERE idalumnomateria= :idalumnomateria";
			$query = $this->PDO->prepare($sql);
			$params = array(':idalumnomateria' => $idAlumnoMateria);
			if ($query->execute($params)){
				$ses->setMessage("Materia eliminada con éxito", SessionMessageType::Success);	
				$db->dbDisconnect();
				header("Location: /analitico/".$IDCarrera."/".$IDPersona, TRUE, 302);
				exit();		
			}
			else
			{
				$ses->setMessage("Error al eliminar la materia. ", SessionMessageType::TransactionError);
				$db->dbDisconnect();
				header("Location: /analitico-modificar/".$idAlumnoMateria, TRUE, 302);
				exit();
			}
		}
		
	}
}

?>