<?php
class analiticoalumno {
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

        public function analitico($idalumnocarrera=0, $idalumno = 0, $imprimible = 'NO') {
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		
		//Cambio 38 Leo 20170706
		if ($idalumnocarrera==0 && $idalumno==0){ //Acceso desde el menu
			if(!$ses->tienePermiso('','Analitico Acceso desde Menu')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
			}
		}
		//Fin Cambio 38 Leo 20170706

		$db->dbConnect("analiticoalumno/analitico/" . $idalumnocarrera . "/" . $idalumno);
		
		$dataIdAlumno = $db->dbEscape($idalumno);
		$dataIdAlumnoCarrera = $db->dbEscape($idalumnocarrera);
		
		if ($idalumno==0){
			 $dataIdAlumno = $ses->getIdPersona();
		}
		
		$rolActual=$ses->getIdRole();
		
		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		$sql = "select p.apellido, p.nombre,td.descripcion as tipodoc, p.documentonro from personas p ";
		$sql.= "inner join tipodoc td on p.tipodoc=td.id where idpersona=" . $dataIdAlumno;
		$arr = $db->getSQLArray($sql);
		$nombreAlumno = $arr[0]['nombre'] . " " . $arr[0]['apellido']; 
		$documentoAlumno =$arr[0]['tipodoc'] . " " . $arr[0]['documentonro'];
		
		//Traigo las carreras. Si es alumno o profesor unicamente la carrera en curso, caso contrario todas.
		$sql = "select distinct c.idcarrera,ac.idalumnocarrera,c.descripcion, i.nombre as instrumento,";
		$sql.= " ac.idarea, ar.nombre as area "; 
		$sql.= " from alumnocarrera ac inner join carreras c on ac.idcarrera=c.idcarrera";
		$sql.= " inner join instrumentos i on ac.idinstrumento=i.idinstrumento";
		$sql.= " left join areas ar on ac.idarea=ar.idarea ";
		$sql.= " where c.estado=1 and ac.idpersona=" . $dataIdAlumno;
		if ($rolActual == $this->POROTO->Config['rol_profesor_id']){
			$sql.= " and ac.estado in (1,3)";  //Solo carrera en curso o pre finalizada
		}else{
			$sql.= " and ac.estado in (1,2,3) "; //Tanto carreras en curso como finalizadas 
		}
		$viewDataCarreras = $db->getSQLArray($sql);
		
                if(count($viewDataCarreras)>0){ //Si el alumno tiene alguna carrera
                    if ($dataIdAlumnoCarrera == 0) {
                        $dataIdAlumnoCarrera=$viewDataCarreras[0]['idalumnocarrera']; 
                    }
                    //traigo las materias de la carrera
                    $sql = "select m.materiacompleta as nombre,m.anio, m.nombre as nombrecorto, am.aniocursada, amn.notaexamen,";
                    $sql.= "amn.libro, amn.tomo, amn.folio, co.nombre as comision, ";
                    $sql.= " (case when not amn.notaexamen is null then (case when amn.notaexamen>=4 then eam.descripcion else '' end) ";
                    $sql.= " else eam.descripcion end) as estado, ";
                    $sql.= " te.nombre tipoexamen, eam.idestadoalumnomateria idestado,";
                    $sql.= " (case when amn.notaexamen>=4 then date_format(am.fechaaprobacion,'%d/%m/%Y') else ";
                    $sql.= " (case when not amn.fechaexamen is null then date_format(amn.fechaexamen,'%d/%m/%Y') else '' end) ";
                    $sql.= " end) as fechaaprobacion, ";
                    $sql.= " am.idalumnomateria, amc.condicional,amc.condicionalregla6";
                    if ($rolActual == $this->POROTO->Config['rol_profesor_id'])     $sql.= " , f.esprofesor";	
                    $sql.= " from alumnomateria am inner join viewmaterias m on m.idmateria=am.idmateria";
                    $sql.= " left join comisiones co on am.idcomision=co.idcomision";
                    $sql.= " left join alumnomaterianota amn on amn.idalumnomateria=am.idalumnomateria and amn.idtipoexamen not in (3,4)";
                    $sql.= " left join tipoexamen te on te.idtipoexamen=amn.idtipoexamen";
                    $sql.= " inner join estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria ";
                    //Agregado condicionalidades 20180131
                    $sql.= " left join alumnomateria_condicionalidades amc on am.idalumnomateria=amc.idalumnomateria ";
                    //Fin cambio
                    if ($rolActual == $this->POROTO->Config['rol_profesor_id']) 
                    $sql.= " left join (SELECT c.idmateria esprofesor FROM comprofesor cp INNER JOIN comisiones c on c.idcomision=cp.idcomision INNER JOIN comalumno ca on ca.idcomision=c.idcomision and ca.idpersona=" . $dataIdAlumno . " WHERE cp.idpersona=" . $ses->getIdPersona() . ") f on f.esprofesor=m.idmateria";
                    $sql.= " where am.idalumnocarrera=" . $dataIdAlumnoCarrera. " and m.idcarrera=am.idcarrera";
                    $sql.= " and idpersona=" . $dataIdAlumno;
                    if($imprimible=='si')	$sql.= " and not m.anio='PRE' ";
                    $sql.= " order by m.orden,amn.idalumnomaterianota";
                    $viewData = $db->getSQLArray($sql);
                    
                    //levantar estado alumnocarrera
                    $sql = "select ac.estado, eac.descripcion from alumnocarrera ac inner join estadoalumnocarrera eac on eac.id=ac.estado where ac.idalumnocarrera=" . $dataIdAlumnoCarrera ." and ac.estado in (1,2,3) "; //Tanto carreras en curso como finalizadas
                    $viewDataEstado = $db->getSQLArray($sql);

                    $sql = "select id,descripcion from estadoalumnocarrera where id!=" . $viewDataEstado[0]['estado'];
                    $viewDataEstadosAlumnoCarrera = $db->getSQLArray($sql);
                } //if(count($viewDataCarreras)>0){ //Si el alumno tiene alguna carrera

		$db->dbDisconnect();
	
		if($imprimible=='si'){
			
					include($this->POROTO->ViewPath . "/informe-analitico.php");
						
			}
			else
			{
		$pageTitle="Analítico";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/analitico.php");
		include($this->POROTO->ViewPath . "/-footer.php");		
			}
		
	}
	
	public function setEstadoAlumnoMateria ($idAlumnoCarrera, $idAlumno, $idAlumnoMateria, $idEstado, $idComision = 0) {
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$db->dbConnect("analiticoalumno/setEstadoAlumnoMateria/" . $idAlumnoCarrera . "/" . $idAlumno . "/" . $idAlumnoMateria . "/" . $idEstado);

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Analitico modificar estado materias')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706

		$sql = "update alumnomateria set idestadoalumnomateria=" . $idEstado . ",";
		$sql.= " usumodi='" . $ses->getUsuario() . "',";
		$sql.= " fechamodi=CURRENT_TIMESTAMP";
		$sql.= " where idalumnomateria=" . $idAlumnoMateria;
		$db->update($sql);
		
		//Actualizo el cupo si es que esta en una comisión y cambio de estado.
		//En caso de pasar a estado LIBRE o CANCELADA descontar del cupo para que pueda reutilizarse.
		//En realidad en todos los casos verifico y actualizo el cupo por
		// si pase de cursando a cancelada y luego a cursando de nuevo.
		$sql= " SELECT ca.idcomcupo from alumnomateria am inner join comalumno ca on am.idcomision=ca.idcomision";
		$sql.= " and am.idpersona=ca.idpersona ";
		$sql.= " where am.idalumnomateria=" . $idAlumnoMateria. " and not am.idcomision is null and ca.estado=1";
		$arr = $db->getSQLArray($sql);
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
			$db->update($sql);
		}

		
		$db->dbDisconnect();
		
		$ses->setMessage("Estado modificado", SessionMessageType::Success);
		if ($idComision == 0) { //Si la llamada vino desde analitico
			header("Location: /analitico/" . $idAlumnoCarrera . "/" . $idAlumno , TRUE, 302);
		} else { //Si la llamada vino desde ver-materias, vuelvo
			header("Location: /ver-materias/" , TRUE, 302);
		}

	}

	public function cambiarestadocarrera($idAlumno, $idAlumnoCarrera, $idEstado, $sinvalidar) {
                include($this->POROTO->ModelPath . '/alumnocarrera.php');
		
                $db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		
                if(!$ses->tienePermiso('','Analitico modificar estado Carrera')){
                        $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
                        header("Location: /", TRUE, 302);
                        exit();
                }
                               
                //Variable para validar o no antes de hacer el cambio.
                $bOk = true;
                $sMsg="";
                $claseAlumnoCarrera = new AlumnoCarreraModel($this->POROTO);
                
                if($sinvalidar=="false"){    
                    $sMsg=$claseAlumnoCarrera->verificarCambioEstadoCarrera($idAlumnoCarrera, $idEstado);
                }
               
                if($sMsg==""){ //OK
                    $params["idalumnocarrera"] = $idAlumnoCarrera;
                    $params["idestado"] = $idEstado;
                    $params["usuario"] = $ses->getUsuario();

                    $claseAlumnoCarrera->guardarCambioEstadoCarrera($params);

                    $ses->setMessage("Estado Carrera modificado", SessionMessageType::Success);
                    if($idEstado == 0){ //En caso de pasar la carrera a Borrada redirijo al Home para que no de error.
                        header("Location: /", TRUE, 302);
                    }
                    else{ //En cualquier otro estado redirijo a la misma pagina.
                        header("Location: /analitico/" . $idAlumnoCarrera . "/" . $idAlumno , TRUE, 302);
                    }
		} else {
                    $ses->setMessage($sMsg."\nEn caso de ser necesario tilde Sin validar aprobaciones.", SessionMessageType::TransactionError);
                    header("Location: /analitico/" . $idAlumnoCarrera . "/" . $idAlumno , TRUE, 302);
		}
	}


}
?>