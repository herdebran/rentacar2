<?php
class carreras {
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
	
	public function gestioncarreras() {
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		$db->dbConnect("carreras/gestioncarreras/");

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Carreras Acceso desde Menu')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706

		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	
	    $sql = "SELECT idcarrera,nombre,descripcion,estado FROM carreras ORDER BY descripcion";
		$viewDataCarreras = $db->getSQLArray($sql);

		$db->dbDisconnect();

		$pageTitle="Gestión de Carreras";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/gestion-carreras.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}
	
	public function ajaxmateriascarrera($carreraid) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("carreras/ajaxMateriasCarrera");
		$sql = " select m.idmateria, m.anio, m.materiacompleta as nombre,m.orden,m.promocionable,m.libre,m.estado";
		$sql.= " from viewmaterias m";
		$sql.= " inner join tipomateria tm on tm.idtipomateria=m.idtipomateria";
		$sql.= " where m.idcarrera=" . $db->dbEscape($carreraid);
		$sql.= " order by m.orden";
		$arrData = $db->getSQLArray($sql);
		$db->dbDisconnect();
		$result=array("carrera"=>$carreraid, "rows"=>$arrData);
		echo json_encode($result);
	}
	
        public function agregarmateria($carreraid, $materiaid=0) {
		$validationErrors = array();
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$db->dbConnect("carreras/agregarmateria/" . $carreraid . "/" . $materiaid);
		$dataIdCarrera = $db->dbEscape($carreraid);
		$dataIdMateria = $db->dbEscape($materiaid);
	    
		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Carreras Agregar Materia') && !$ses->tienePermiso('','Gestion de Carreras Modificar Materia')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /gestion-carreras", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		if (isset($_POST['materia']) || isset($_POST['nombre'])) {
			if ($dataIdMateria > 0) $dataMat = $dataIdMateria; else $dataMat = $db->dbEscape($_POST['materia']);
			if ($dataMat == 0 || $dataIdMateria>0) $dataNombre = mb_strtoupper($db->dbEscape(trim($_POST['nombre'])), 'UTF-8');
			if ($dataMat == 0 || $dataIdMateria>0) $dataDescripcion = mb_strtoupper($db->dbEscape(trim($_POST['descripcion'])), 'UTF-8');
			if ($dataMat == 0 || $dataIdMateria>0) $dataAnio = mb_strtoupper($db->dbEscape(trim($_POST['anio'])), 'UTF-8');
			if ($dataMat == 0 || $dataIdMateria>0) $dataTipMat = $db->dbEscape($_POST['tipmat']);
			if ($dataMat == 0 || $dataIdMateria>0) $dataCantModulos = $db->dbEscape($_POST['cantidadmodulos']);
			if (isset($_POST['orden']))            $dataOrden = $db->dbEscape($_POST['orden']);
			if (isset($_POST['promocionable'])) $dataPromocionable = "1"; else $dataPromocionable = "0";
			if (isset($_POST['libre'])) $dataLibre = "1"; else $dataLibre = "0";
                        // Cambios Lautaro 17/05/2018
			if (isset($_POST['final_libre'])) $aceptafinalLibre = "1"; else $aceptafinalLibre = "0";
			if (isset($_POST['final_regular'])) $aceptafinalRegular = "1"; else $aceptafinalRegular = "0";
			if (isset($_POST['final_previo'])) $aceptafinalPrevio = "1"; else $aceptafinalPrevio = "0";
			if (isset($_POST['materia_instrumento'])) $materiaConInstrumento = "1"; else $materiaConInstrumento = "0";
			if (isset($_POST['materia_genero'])) $materiaConGenero = "1"; else $materiaConGenero = "0";
// Fin cambios Lautaro 17/05/2018
			if ($dataIdMateria==0) { //agrega materia nueva o una existente a otra carrera
				if ($dataMat == 0) { //agrega nueva materia
                                
					// STAP! VALIDATION TIME
					// nombre - obligatorio maxlength 100
					if ($dataNombre=="") $validationErrors['nombre'] = "El campo Nombre es obligatorio";
					if (strlen($dataNombre)>45) $validationErrors['nombre'] = "El campo Nombre puede contener como máximo 100 caracteres";
					// descripcion - obligatorio maxlength 500
					if (strlen($dataDescripcion)>500) $validationErrors['descripcion'] = "El campo Descripción puede contener como máximo 500 caracteres";
					// anio - obligatorio
					if ($dataAnio=="") $validationErrors['anio'] = "El campo Año es obligatorio";
					// tipmat - obligatorio
					if ($dataTipMat=="" || $dataTipMat==0) 	$validationErrors['tipmat'] = "El campo Tipo es obligatorio";
					// cant modulos - no obligatorio, entre 0 y 500
					if ($dataCantModulos!="" && $dataCantModulos!=0) {
						if ($dataCantModulos < 0 || $dataCantModulos > 500) $validationErrors['anio'] = "El campo Cantidad Módulos, si posee un valor debe estar entre 0 y 500";
					}
				}
				if (count($validationErrors)==0) {
					if ($dataMat == 0) { //agrega nueva materia
						$sql = "INSERT INTO materias "
                                                        . "(idtipomateria,nombre,descripcion,anio,"
                                                        . "promocionable,libre,cantidadmodulos,estado,"
                                                        // Cambios Lautaro 17/05/2018
                                                        . "final_regular, final_previo, final_libre, materia_instrumento, materia_genero,"
                                                        // Fin cambios Lautaro 17/05/2018
                                                        . "usucrea,fechacrea,usumodi,fechamodi)"
                                                        . "SELECT ";
						$sql.= $dataTipMat;
						$sql.= ",'" . $dataNombre . "'";
						$sql.= ",'" . $dataDescripcion . "'";
						$sql.= ",'" . $dataAnio . "'";
						$sql.= "," . $dataPromocionable;
						$sql.= "," . $dataLibre;
						
						if ($dataCantModulos == "") $sql.= ",0"; else $sql.= "," . $dataCantModulos;
						
						$sql.= ",1";
                                                
                                                $sql.= "," . $aceptafinalRegular;
                                                $sql.= "," . $aceptafinalPrevio;
                                                $sql.= "," . $aceptafinalLibre;
                                                $sql.= "," . $materiaConInstrumento;
                                                $sql.= "," . $materiaConGenero;
                                                
						$sql.= ",'" . $ses->getUsuario() . "'";
						$sql.= ",CURRENT_TIMESTAMP ";
						$sql.= ",null";
						$sql.= ",null";

						$db->begintrans();
						$newIdMateria = $db->insert($sql, '', true);
						$bOk = $newIdMateria;
					} else { //como agrego una materia ya existente, no hago el insert en materias y pongo como nuevo id insertado (newIdMateria) el id de la materia seleccionada
						$newIdMateria = $dataMat;
						$bOk = true;
					}

					$sql = "INSERT INTO carreramateria (idmateria,idcarrera,orden) SELECT ";
					$sql.= $newIdMateria;
					$sql.= "," . $dataIdCarrera;
					if ($dataOrden == "") $sql.= ",0"; else $sql.= "," . $dataOrden;
					if ($bOk!==false) $bOk = $db->insert($sql, '', true);
					
					if ($bOk === false) {
						$db->rollback();
						$ses->setMessage("Se produjo un error realizando el alta", SessionMessageType::TransactionError);
						header("Location: /agregarmateria/" . $dataIdCarrera, TRUE, 302);
						exit();

					} else {
						$db->commit();
					}
					$db->dbDisconnect();

					$ses->setMessage("Materia creada con éxito", SessionMessageType::Success, $dataIdCarrera);
					header("Location: /gestion-carreras", TRUE, 302);
					exit();
				} //fin validationErrors==0
				
			} else { //modifica materia
			
				// STAP! VALIDATION TIME
				// nombre - obligatorio maxlength 100
				if ($dataNombre=="") $validationErrors['nombre'] = "El campo Nombre es obligatorio";
				if (strlen($dataNombre)>45) $validationErrors['nombre'] = "El campo Nombre puede contener como máximo 100 caracteres";
				// descripcion - obligatorio maxlength 500
				if (strlen($dataDescripcion)>500) $validationErrors['descripcion'] = "El campo Descripción puede contener como máximo 500 caracteres";
				// anio - obligatorio
				if ($dataAnio=="") $validationErrors['anio'] = "El campo Año/Nivel es obligatorio";
				// tipmat - obligatorio
				if ($dataTipMat=="" || $dataTipMat==0) 	$validationErrors['tipmat'] = "El campo Tipo es obligatorio";
				// cant modulos - no obligatorio, entre 0 y 500
				if ($dataCantModulos!="" && $dataCantModulos!=0) {
					if ($dataCantModulos < 0 || $dataCantModulos > 500) $validationErrors['anio'] = "El campo Cantidad Módulos, si posee un valor debe estar entre 0 y 500";
				}
				if (count($validationErrors)==0) {
					$sql = "UPDATE materias SET ";
					$sql.= "idtipomateria=" . $dataTipMat;
					$sql.= ",nombre='" . $dataNombre . "'";
					$sql.= ",descripcion='" . $dataDescripcion . "'";
					$sql.= ",anio='" . $dataAnio . "'";
					$sql.= ",promocionable=" . $dataPromocionable;
					$sql.= ",libre=" . $dataLibre;
					if ($dataCantModulos == "") $sql.= ",cantidadmodulos=0"; else $sql.= ",cantidadmodulos=" . $dataCantModulos;
                                        // Cambios Lautaro 17/05/2018
                                        $sql.= ",final_libre=$aceptafinalLibre";
                                        $sql.= ",final_regular=$aceptafinalRegular";
                                        $sql.= ",final_previo=$aceptafinalPrevio";
                                        $sql.= ",materia_instrumento=$materiaConInstrumento";
                                        $sql.= ",materia_genero=$materiaConGenero";
                                        // Fin cambios Lautaro 17/05/2018
                                        $sql.= ",usumodi='" . $ses->getUsuario() . "'";
                                        $sql.= ",fechamodi=CURRENT_TIMESTAMP ";
					$sql.= " WHERE idmateria=" . $dataIdMateria;

					$db->begintrans();
					$bOk = $db->update($sql, '', true);
					
					$sql = "UPDATE carreramateria SET ";
					if ($dataOrden == "") $sql.= "orden=0"; else $sql.= "orden=" . $dataOrden;
					$sql.= " WHERE idmateria=" . $dataIdMateria . " AND idcarrera=" . $dataIdCarrera;
					$bOk = $db->update($sql, '', true);
							
					if ($bOk === false) {
						$db->rollback();
						$ses->setMessage("Se produjo un error realizando la modificacion", SessionMessageType::TransactionError);
						header("Location: /agregarmateria/" . $dataIdCarrera, TRUE, 302);
						exit();

					} else {
						$db->commit();
					}
					$db->dbDisconnect();

					$ses->setMessage("Materia modificada con éxito", SessionMessageType::Success, $dataIdCarrera);
					header("Location: /gestion-carreras", TRUE, 302);
					exit();
				} //fin validationErrors==0
			} //fin modificar materia
		} //fin es POST



		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		$sql = "SELECT descripcion FROM carreras WHERE idcarrera=" . $dataIdCarrera;
		$arr = $db->getSQLArray($sql);
		$dataDescripcionCarrera = $arr[0]['descripcion'];

		if ($dataIdMateria==0) { //agrega nueva materia
/* era asi antes, pero en el prototipo pide distnto
			//materias: traigo todas las materias activas, que no estan en la carrera actual
			$sql = "SELECT idmateria,nombre FROM materias WHERE estado=1 AND idmateria not IN (SELECT idmateria FROM carreramateria WHERE idcarrera=" . $dataIdCarrera . ")";
			$viewDataMaterias = $db->getSQLArray($sql);
*/
//todas las materias de todas las carreras que estén en estado 1. Para poder identificarlas fácilmente, en vez de traer el nombre de materia nada mas, 
// debería traerlo de esta forma: IMP – 1°Año – Instrumento I" seria CARRERA-AÑO-Nombre Materia.
			$sql = "select m.idmateria, concat(c.nombre,' - ',m.anio,' - ',m.nombre) nombre, final_regular, final_previo, final_libre, materia_instrumento, materia_genero";
			$sql.= " from carreramateria cm";
			$sql.= " inner join carreras c on c.idcarrera=cm.idcarrera";
			$sql.= " inner join materias m on m.idmateria=cm.idmateria";
			$sql.= " where m.estado=1";
			$sql.= " and m.idmateria not in (select idmateria from carreramateria where idcarrera=" . $dataIdCarrera . ")";
			$sql.= " order by c.nombre,m.anio,m.nombre";
			$viewDataMaterias = $db->getSQLArray($sql);
			$viewData = array(
                            array(
                                "idtipomateria"=>0, 
                                "nombre"=>'', 
                                "descripcion"=>'', 
                                "anio"=>"", 
                                "promocionable"=>"",
                                "libre"=>"", 
                                "cantidadmodulos"=>"", 
                                "orden"=>"", 
                                "final_regular" => "", 
                                "final_previo" => "", 
                                "final_libre" => "", 
                                "materia_instrumento" => "", 
                                "materia_genero" => ""
                                )
                            );


		} else { //modifica materia
			$sql = "SELECT idtipomateria,nombre,descripcion,anio,promocionable,libre,cantidadmodulos,orden, final_regular, final_previo, final_libre, materia_instrumento, materia_genero";
			$sql.= " FROM materias m inner join carreramateria cm on m.idmateria=cm.idmateria";
			$sql.= " WHERE m.idmateria=" . $dataIdMateria . " and cm.idcarrera=".$dataIdCarrera;
			$viewData = $db->getSQLArray($sql);

			$sql = "select m.idmateria, concat(c.nombre,' - ',m.anio,' - ',m.nombre) nombre";
			$sql.= " from carreramateria cm";
			$sql.= " inner join carreras c on c.idcarrera=cm.idcarrera";
			$sql.= " inner join materias m on m.idmateria=cm.idmateria";
			$sql.= " where m.estado=1";
			$sql.= " and m.idmateria=" . $dataIdMateria;
			$sql.= " order by c.nombre,m.anio,m.nombre";
			$viewDataMaterias = $db->getSQLArray($sql);

			$sql = "SELECT id,descripcion FROM reglas ORDER BY 1";
			$viewDataReglas = $db->getSQLArray($sql);											 
			
			$sql = "SELECT idcarrera,nombre,descripcion FROM carreras ORDER BY 2";			
			
			$viewDataCarreras = $db->getSQLArray($sql);

						
			$sql = "select m.idmateria, concat(m.anio,' - ',m.nombre) nombre";
			$sql.= " from carreramateria cm";
			$sql.= " inner join carreras c on c.idcarrera=cm.idcarrera";
			$sql.= " inner join materias m on m.idmateria=cm.idmateria";
			$sql.= " where m.estado=1";
			$sql.= " and c.idcarrera=" . $dataIdCarrera;
			$sql.= " order by c.nombre,m.anio,m.nombre";
			$viewDataMateriasRegla = $db->getSQLArray($sql);
		
		}

		//correlativas: traigo todas las materias 
		$sql = "select m.idmateria,concat(m.anio,' - ',m.nombre) nombre";
		$sql.= " from materias m";
		$sql.= " inner join carreramateria cm on cm.idmateria=m.idmateria";
		$sql.= " where cm.idcarrera=" . $dataIdCarrera;
		$sql.= " and m.estado=1";
		if ($dataIdMateria != 0) $sql.= " and m.idmateria!=" . $dataIdMateria;

		$sql.= " ORDER BY m.anio, m.nombre";
		$viewDataCorrelativas = $db->getSQLArray($sql);

	    $sql = "SELECT idtipomateria,descripcion FROM tipomateria ORDER BY 2";
		$viewDataTipoMateria = $db->getSQLArray($sql);

		$sql = "SELECT idarea,nombre FROM areas ORDER BY nombre";
		$viewDataAreas = $db->getSQLArray($sql);

		$sql = "SELECT idinstrumento,nombre FROM instrumentos ORDER BY nombre";
		$viewDataInstrumentos = $db->getSQLArray($sql);												   			 


		if ($dataIdMateria==0) $pageTitle="Agregar Materia"; else $pageTitle="Modificar Materia";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/ver-carrera.php");
		include($this->POROTO->ViewPath . "/-footer.php");

	}
	
	public function desactivarmateria($carreraid, $materiaid) {
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		
		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Carreras Activar/desactivar Materia')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
			$sql = "UPDATE materias set ";
			$sql.= " estado=0";
			$sql.= ",usumodi='" . $ses->getUsuario() . "'";
			$sql.= ",fechamodi=CURRENT_TIMESTAMP";
			$sql.= " WHERE idmateria=" . $materiaid;
			$db->dbConnect("carreras/desactivarmateria/" . $carreraid . "/" . $materiaid);
			$db->update($sql);
			$db->dbDisconnect();
			$ses->setMessage("Materia desactivada con éxito", SessionMessageType::Success);
			header("Location: /gestion-carreras/", TRUE, 302);
			exit();
	}

	public function activarmateria($carreraid, $materiaid) {
		//solo para directivos
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		
		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Carreras Activar/desactivar Materia')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
			$sql = "UPDATE materias set ";
			$sql.= " estado=1";
			$sql.= ",usumodi='" . $ses->getUsuario() . "'";
			$sql.= ",fechamodi=CURRENT_TIMESTAMP";
			$sql.= " WHERE idmateria=" . $materiaid;
			$db->dbConnect("carreras/activarmateria/" . $carreraid . "/" . $materiaid);
			$db->update($sql);
			$db->dbDisconnect();
			$ses->setMessage("Materia activada con éxito", SessionMessageType::Success);
			header("Location: /gestion-carreras/", TRUE, 302);
			exit();
		
	}
        
}
?>