<?php
class alumnos {
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
	
	public function ajaxalumnos($page = 1) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("alumnos/ajaxalumnos/" . $page);
		if (! is_numeric($page)) {
			$page=1;
		} else {
			if ($page<1) $page=1;
		}

		$sql = "select SQL_CALC_FOUND_ROWS p.apellido, p.nombre, t.descripcion tdoc, p.documentonro, ea.descripcion estalu, ifnull(c.descripcion,'') carreradescripcion,i.nombre as instrumento, u.estado, p.idpersona, ";
                $sql.= "(select ifnull(date_format(max(ua.fecha), '%d/%m/%Y %H:%i:%s'),'n/a') from usuarioaccesos ua where ua.idpersona=p.idpersona) as lastlogin, c.idcarrera,ac.idalumnocarrera";
		$sql.= " from alumnos a ";
		$sql.= " inner join personas p on p.idpersona=a.idpersona";
		$sql.= " inner join usuarios u on u.idpersona=p.idpersona";
		$sql.= " inner join tipodoc t on t.id=p.tipodoc";
		$sql.= " inner join estadoalumno ea on a.estadoalumno_id=ea.id";
		$sql.= " left join alumnocarrera ac on ac.idpersona=p.idpersona and ac.estado in (1,2,3)";
		$sql.= " left join carreras c on ac.idcarrera=c.idcarrera";
                $sql.= " left join instrumentos i on ac.idinstrumento=i.idinstrumento";
		//$sql.= " left join usuarioaccesos ua on ua.idpersona=p.idpersona";
		$sql.= " where p.estado=1";
		if ($_POST['ap']!="") $sql.= " and p.apellido like '%" . $db->dbEscape($_POST['ap']) . "%'";
		if ($_POST['no']!="") $sql.= " and p.nombre like '%" . $db->dbEscape($_POST['no']) . "%'";
		if ($_POST['td']!="" && $_POST['td']!="0" ) $sql.= " and t.id=" . $db->dbEscape($_POST['td']);
		if ($_POST['nd']!="") $sql.= " and p.documentonro like '%" . $db->dbEscape($_POST['nd']) . "%'";
		if ($_POST['ea']!="" && $_POST['ea']!="0" ) $sql.= " and a.estadoalumno_id=" . $db->dbEscape($_POST['ea']);
		if ($_POST['ca']!="" && $_POST['ca']!="0" ) $sql.= " and c.idcarrera=" . $db->dbEscape($_POST['ca']);
		$sql.= " order by apellido,nombre";
		$sql.= " limit " . (($page-1) * $this->POROTO->Config['records_per_page']) . "," . $this->POROTO->Config['records_per_page'];
		$arrData = $db->getSQLArray($sql);

		$sql = "SELECT FOUND_ROWS() q";
		$arrQ = $db->getSQLArray($sql);
		$db->dbDisconnect();
		$pages = ceil($arrQ[0]['q'] / $this->POROTO->Config['records_per_page']);
		$result=array("currentpage"=>$page, "qrows"=>$arrQ[0], "pages"=>$pages, "rows"=>$arrData);
		echo json_encode($result);
	}
	
	public function quitarCarreraAlumno($idpersona, $idcarrera, $idinstrumento, $idarea, $params="") { 
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		
		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Alumno Agregar o Quitar Carrera')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /modificar/" . $idpersona . "/" . $params, TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		if (isset($_POST['confirm'])) {
				$db->dbConnect("alumnos/quitarCarreraAlumno/" . $idpersona . "/" . $idcarrera . "/" . $params);

				$sql = "select count(0) q";
				$sql.= " from alumnomateria am";
				$sql.= " inner join carreramateria cm on am.idmateria=cm.idmateria"; 
				$sql.= " inner join materias m on m.idmateria=cm.idmateria";
				$sql.= " where cm.idcarrera=" . $idcarrera;
				$sql.= " and am.idpersona=" . $idpersona;
				$sql.= " and m.estado=1";
				$arr = $db->getSQLArray($sql);
				if ($arr[0]['q'] > 0) {
					$ses->setMessage("La carrera no pudo quitarse porque tiene materias activas", SessionMessageType::TransactionError, $params);
					header("Location: /modificar/" . $idpersona . "/" . $params, TRUE, 302);
					exit();
				}

				$sql = "UPDATE alumnocarrera set ";
				$sql.= " estado=0";
				$sql.= ",usumodi='" . $ses->getUsuario() . "'";
				$sql.= ",fechamodi=CURRENT_TIMESTAMP";
				$sql.= " WHERE idpersona=" . $idpersona . " AND idcarrera=" . $idcarrera;
				$sql.= " AND idinstrumento=" . $idinstrumento . " AND idarea=" . $idarea;
				$db->update($sql);
				$db->dbDisconnect();
				$ses->setMessage("Carrera quitada con éxito", SessionMessageType::Success, $params);
				header("Location: /gestion-alumnos", TRUE, 302);
				exit();
		} else { //isset post confirm
				$pageTitle="Confirmar Quitar Carrera";
				include($this->POROTO->ViewPath . "/-header.php");
				include($this->POROTO->ViewPath . "/confirmar-quitar-carrera.php");
				include($this->POROTO->ViewPath . "/-footer.php");

		}
	}

	public function modificarCarreraAlumno($idpersona, $idcarrera, $idinstrumento, $idarea, $params="") {
		//solo para administrativos y directivos
		$validationErrors = array();
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		
		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Alumno Agregar o Quitar Carrera')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /modificar/" . $idpersona . "/" . $params, TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		$db->dbConnect("alumnos/modificarCarreraAlumno/" . $idpersona . "/" . $idcarrera . "/" . $params);

			$materiasActivas = false;
			$sql = "select count(0) q";
			$sql.= " from alumnomateria am";
			$sql.= " inner join carreramateria cm on am.idmateria=cm.idmateria"; 
			$sql.= " inner join materias m on m.idmateria=cm.idmateria";
			$sql.= " where cm.idcarrera=" . $idcarrera;
			$sql.= " and am.idpersona=" . $idpersona;
			$sql.= " and m.estado=1";
			$arr = $db->getSQLArray($sql);
			$materiasActivas = ($arr[0]['q'] > 0);

			if (isset($_POST['area'], $_POST['nivel'])) {
				//valido
				$dataFecha = $db->dbEscape($_POST['fecha']);
				$dataArea = $db->dbEscape($_POST['area']);
				$dataNivel = $db->dbEscape($_POST['nivel']);
				if (isset($_POST['instrumento'])) $dataInstrumento = $db->dbEscape($_POST['instrumento']); else $dataInstrumento = "";

				if ($dataNivel == 0) $validationErrors['nivel'] = "Nivel es un campo obligatorio";

				if (!$lib->validateDate($dataFecha)) {
					$validationErrors['fecha'] = "La Fecha es inválida";
				} else {
			    	$d = $lib->datediff($dataFecha);
			    	if ($d<0) $validationErrors['fecha'] = "La Fecha es inválida (futura)";
			    	if ($d > (365*100)) $validationErrors['fecha'] = "La Fecha es inválida (>100)";
				}



				if (count($validationErrors)==0) {
					$sql = "UPDATE alumnocarrera set ";
					$sql.= " fechainscripcion='" . $lib->dateDMY2YMD($dataFecha) . "'";
					$sql.= ",idarea=" . $dataArea;
					$sql.= ",idnivel=" . $dataNivel;
					if ($dataInstrumento != "") $sql.= ",idinstrumento=" . $dataInstrumento;
					$sql.= " where idpersona=" . $idpersona;
					$sql.= " and idcarrera=" . $idcarrera;
					$sql.= " and idinstrumento=" . $idinstrumento;
					$sql.= " and idarea=" . $idarea;
					$db->update($sql);
					$db->dbDisconnect();
					$ses->setMessage("Carrera modificada con éxito", SessionMessageType::Success, $params);
					header("Location: /modificar/" . $idpersona . "/" . $params, TRUE, 302);
					exit();
				}
			}

			$sql = "select c.descripcion carreraDescripcion, date_format(fechainscripcion, '%d/%m/%Y') fecha_dmy, i.nombre instrumentoNombre, a.nombre areaNombre";
			$sql.= " ,ac.idinstrumento,ac.idarea,ac.idnivel";
			$sql.= " from alumnocarrera ac";
			$sql.= " inner join carreras c on c.idcarrera=ac.idcarrera";
			$sql.= " inner join instrumentos i on i.idinstrumento=ac.idinstrumento";
			$sql.= " left join areas a on a.idarea=ac.idarea";
			$sql.= " where ac.idpersona=" . $idpersona;
			$sql.= " and ac.idcarrera=" . $idcarrera;
			$sql.= " and ac.idinstrumento=" . $idinstrumento;
			$sql.= " and ac.idarea=" . $idarea;
			$viewData = $db->getSQLArray($sql);

			$sql = "select idarea,nombre from areas";
			$viewDataAreas = $db->getSQLArray($sql);

			$sql = "SELECT id,descripcion  FROM carreraniveles where idcarrera=" . $idcarrera . " order by orden";
			$viewDataNiveles = $db->getSQLArray($sql);

			$sql = "select idinstrumento,nombre,descripcion from instrumentos";
			$viewDataInstrumentos = $db->getSQLArray($sql);

			$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

			$pageTitle="Modificar Carrera";
			include($this->POROTO->ViewPath . "/-header.php");
			include($this->POROTO->ViewPath . "/modificar-carrera.php");
			include($this->POROTO->ViewPath . "/-footer.php");

	}
	
	public function crearalumno() { //OK
		$validationErrors = array();
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$db->dbConnect("alumnos/crearalumno");
	    
		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Alumno Modificar')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /gestion-alumnos", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706

		if (isset($_POST['apellido'])) {
			$dataApellido = mb_strtoupper($db->dbEscape(trim($_POST['apellido'])), 'UTF-8');
			$dataNombre   = mb_strtoupper($db->dbEscape(trim($_POST['nombre'])), 'UTF-8');
			$dataNroDoc   = $db->dbEscape(trim(intval($_POST['nrodoc'])));
			$dataTipDoc   = $db->dbEscape($_POST['tipdoc']);

			// STAP! VALIDATION TIME
			// apellido - obligatorio maxlength 45
			if ($dataApellido=="") $validationErrors['apellido'] = "El campo Apellido es obligatorio";
			if (strlen($dataApellido)>45) $validationErrors['apellido'] = "El campo Apellido puede contener como máximo 45 caracteres";
			// nombre - obligatorio maxlength 45
			if ($dataNombre=="") $validationErrors['nombre'] = "El campo Nombre es obligatorio";
			if (strlen($dataNombre)>45) $validationErrors['nombre'] = "El campo Nombre puede contener como máximo 45 caracteres";
			// tipo documento - obligatorio
			if ($dataTipDoc=="" || $dataTipDoc==0) $validationErrors['tipdoc'] = "El campo Tipo de Documento es obligatorio";
			// numero documento - obligatorio
			if ($dataNroDoc=="" || $dataNroDoc==0) $validationErrors['nrodoc'] = "El campo Número de Documento es obligatorio";
			if (!is_numeric($_POST['nrodoc'])) $validationErrors['nrodoc'] = "El campo Número de Documento no es numérico";
			//buscar tipo y nro de doc si ya existen

			if (count($validationErrors)==0) {
				$sql = "select idpersona from personas where tipodoc=" . $dataTipDoc . " and documentonro=" . $dataNroDoc;
				$arr2 = $db->getSQLArray($sql);
                                if (count($arr2)> 0) { 
                                    $existePersona=true; 
                                    $idPersona=$arr2[0]["idpersona"];
                                }else{ $existePersona=false; }
                                
                                $sql = "select p.idpersona from alumnos a inner join personas p on a.idpersona=p.idpersona ";
                                $sql.= "where p.tipodoc=" . $dataTipDoc . " and p.documentonro=" . $dataNroDoc;
                                $arr2 = $db->getSQLArray($sql);
                                if (count($arr2)>0) { $existeAlumno=true;}else{ $existeAlumno=false;}
                                
                                $sql=  "select pr.idrol from personarol pr inner join personas p on pr.idpersona=p.idpersona  ";
                                $sql.= "where p.tipodoc=" . $dataTipDoc . " and p.documentonro=" . $dataNroDoc." and pr.idrol=1";
                                $arr2 = $db->getSQLArray($sql);
                                if (count($arr2)>0)
                                    {$existeRolAlumno=true;}else{$existeRolAlumno=false;}
                                
                                if($existePersona){
                                    $sMsg="";
                                    $bOk=true;
                                    $db->begintrans();
                                    if(!$existeAlumno){
                                        //Inserto en tabla alumno
                                        $sMsg.="Se insertó el registro en la tabla alumnos.\n";
                                        $sqlA = "INSERT INTO alumnos (idpersona,titulosecundario,otorgadopor,aniooegreso,";
                                        $sqlA.= "estadoalumno_id,certificadotrabajo,observaciones,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
                                        $sqlA.= $idPersona;
                                        $sqlA.= ",''";
                                        $sqlA.= ",''";
                                        $sqlA.= ",null";
                                        $sqlA.= "," . $this->POROTO->Config['estado_alumno_reempadronar_id'];
                                        $sqlA.= ",0";
                                        $sqlA.= ",''";
                                        $sqlA.= ",'" . $ses->getUsuario() . "'";
                                        $sqlA.= ",CURRENT_TIMESTAMP ";
                                        $sqlA.= ",NULL ";
                                        $sqlA.= ",NULL ";
                                        if ($bOk!==false) $bOk = $db->insert($sqlA, '', true);
                                    }
                                    if(!$existeRolAlumno){
                                        $sMsg.="Se insertó el registro en la tabla personarol.\n";
                                        $sqlPR = "INSERT INTO personarol (idpersona,idrol,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
                                        $sqlPR.= $idPersona;
                                        $sqlPR.= ",1";
                                        $sqlPR.= ",'" . $ses->getUsuario() . "'";
                                        $sqlPR.= ",CURRENT_TIMESTAMP";
                                        $sqlPR.= ",null";
                                        $sqlPR.= ",null";
                                        if ($bOk!==false) $bOk = $db->insert($sqlPR, '', true);
                                    }
                                    if ($bOk === false) {
					$db->rollback();
					$ses->setMessage("Se produjo un error realizando el alta", SessionMessageType::TransactionError);
					header("Location: /gestion-alumnos", TRUE, 302);
					exit();

                                    } else {
                                            $db->commit();
                                    }
                                    $db->dbDisconnect();
                                    if($sMsg=="") $sMsg="El alumno ya existe.";
                                    $ses->setMessage($sMsg, SessionMessageType::Success);
                                    header("Location: /gestion-alumnos", TRUE, 302);
                                    exit();
                                }
			}

			if (count($validationErrors)==0 && !$existePersona) {
				//buscar un nombre de usuario valido
				$userName = $dataNroDoc;
				$sql = "select idpersona from usuarios where usuario='" . $userName . "'";
				$arr = $db->getSQLArray($sql);
				if (count($arr)>0) {
					for ($i=0; $i<26; $i++) {
						$extraChar = chr($i+65);
						$userName = $dataNroDoc . $extraChar;
						$sql = "select idpersona from usuarios where usuario='" . $userName . "'";
						$arr = $db->getSQLArray($sql);
						if (count($arr)==0) break;
					}
				}

				$sqlP = "INSERT INTO personas (legajo,apellido,nombre,tipodoc,documentonro,";
				$sqlP.= "cuil,telefono1,telefono2,estadocivil,direccion,numero,";
				$sqlP.= "piso,depto,idLocalidad,codpostal,fechanac,nacionalidad,";
				$sqlP.= "sexo,entrecalles,hijos,familiaresacargo,estado,";
				$sqlP.= "usucrea,fechacrea,usumodi,fechamodi) SELECT ";
				$sqlP.= $dataNroDoc;
				$sqlP.= ",'" . $dataApellido .			 "'";
				$sqlP.= ",'" . $dataNombre . "'";
				$sqlP.= "," . $dataTipDoc;
				$sqlP.= "," . $dataNroDoc;
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",null";
				$sqlP.= ",1";
				$sqlP.= ",'" . $ses->getUsuario() . "'";
				$sqlP.= ",CURRENT_TIMESTAMP";
				$sqlP.= ",null";
				$sqlP.= ",null";

				$db->begintrans();
				$newIdPersona = $db->insert($sqlP, '', true);
				$bOk = $newIdPersona;

				$sqlA = "INSERT INTO alumnos (idpersona,titulosecundario,otorgadopor,aniooegreso,";
				$sqlA.= "estadoalumno_id,certificadotrabajo,observaciones,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
				$sqlA.= $newIdPersona;
				$sqlA.= ",''";
				$sqlA.= ",''";
				$sqlA.= ",null";
				$sqlA.= "," . $this->POROTO->Config['estado_alumno_reempadronar_id'];
				$sqlA.= ",0";
				$sqlA.= ",''";
				$sqlA.= ",'" . $ses->getUsuario() . "'";
				$sqlA.= ",CURRENT_TIMESTAMP ";
				$sqlA.= ",NULL ";
				$sqlA.= ",NULL ";
				if ($bOk!==false) $bOk = $db->insert($sqlA, '', true);



				$sqlU = "INSERT INTO usuarios (idpersona,usuario,email,password,estado,";
				$sqlU.= "primeracceso,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
				$sqlU.= $newIdPersona;
				$sqlU.= ",'" . $userName . "'";
				$sqlU.= ",''";
				$sqlU.= ",''";
				$sqlU.= ",0";
				$sqlU.= ",1";
				$sqlU.= ",'" . $ses->getUsuario() . "'";
				$sqlU.= ",CURRENT_TIMESTAMP";
				$sqlU.= ",null";
				$sqlU.= ",null";
				if ($bOk!==false) $bOk = $db->insert($sqlU, '', true);


				$sqlPR = "INSERT INTO personarol (idpersona,idrol,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
				$sqlPR.= $newIdPersona;
				$sqlPR.= "," . $this->POROTO->Config['rol_usuario_id'];
				$sqlPR.= ",'" . $ses->getUsuario() . "'";
				$sqlPR.= ",CURRENT_TIMESTAMP";
				$sqlPR.= ",null";
				$sqlPR.= ",null";
				if ($bOk!==false) $bOk = $db->insert($sqlPR, '', true);

				if ($bOk === false) {
					$db->rollback();
					$ses->setMessage("Se produjo un error realizando el alta", SessionMessageType::TransactionError);
					header("Location: /gestion-alumnos", TRUE, 302);
					exit();

				} else {
					$db->commit();
				}
				$db->dbDisconnect();

				$ses->setMessage("Usuario creado con éxito", SessionMessageType::Success);
				header("Location: /gestion-alumnos", TRUE, 302);
				exit();


			}

		}

		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

	    $sql = "SELECT id,descripcion FROM tipodoc order by id";
		$viewDataTipoDocumento = $db->getSQLArray($sql);

		$db->dbDisconnect();

		$pageTitle="Alta de Alumnos";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/crear-alumno.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}
	
	public function modificar($idpersona, $params="") {
		$validationErrors = array();
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		$db->dbConnect("alumnos/modificar/" . $idpersona);

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Alumno Modificar')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706

		if (isset($_POST['email'], $_POST['nacionalidad'])) {
			$db->dbConnect("alumnos/modificar/" . $idpersona);
			$dataNroDoc = $db->dbEscape(intval($_POST['nrodoc']));
			$dataTipDoc = $db->dbEscape($_POST['tipdoc']);
			$dataApellido = mb_strtoupper($db->dbEscape(trim($_POST['apellido'])), 'UTF-8');
			$dataNombre = mb_strtoupper($db->dbEscape(trim($_POST['nombre'])), 'UTF-8');
			$dataNacionalidad = $db->dbEscape($_POST['nacionalidad']);
			//if (isset($_POST['socio'])) $dataSocio = $db->dbEscape($_POST['socio']); else $dataSocio = "";
			//if (isset($_POST['ultimopagocooperadora'])) $dataUltPagoCoop = $db->dbEscape($_POST['ultimopagocooperadora']); else $dataUltPagoCoop = "";
			$dataFNac = $db->dbEscape($_POST['fnac']);
			$dataTel1 = $db->dbEscape(trim($_POST['telefono1']));
			$dataTel2 = $db->dbEscape(trim($_POST['telefono2']));
			$dataSexo = $db->dbEscape($_POST['sexo']);
			$dataECiv = $db->dbEscape($_POST['estciv']);
			if (isset($_POST['certlab'])) $dataCLab = $db->dbEscape(trim($_POST['certlab'])); else $dataCLab = "";
			$dataProv  = $db->dbEscape(trim($_POST['provincia']));
			$dataCalle = mb_strtoupper($db->dbEscape(trim($_POST['direccion'])), 'UTF-8');
			$dataNro   = mb_strtoupper($db->dbEscape(trim($_POST['numero'])), 'UTF-8');
			$dataEntre = mb_strtoupper($db->dbEscape(trim($_POST['entrecalles'])), 'UTF-8');
			$dataLocal = $db->dbEscape($_POST['localidad']);
			$dataPiso  = mb_strtoupper($db->dbEscape(trim($_POST['piso'])), 'UTF-8');
			$dataDepto = mb_strtoupper($db->dbEscape(trim($_POST['depto'])), 'UTF-8');
			$dataCP    = mb_strtoupper($db->dbEscape(trim($_POST['codpostal'])), 'UTF-8');
			$dataTitulo = mb_strtoupper($db->dbEscape(trim($_POST['titulo'])), 'UTF-8');
			$dataOtorga = mb_strtoupper($db->dbEscape(trim($_POST['otorgadopor'])), 'UTF-8');
			$dataAnoEgr = $db->dbEscape($_POST['anoegreso']);
			$dataOSoc = mb_strtoupper($db->dbEscape(trim($_POST['obrasocial'])), 'UTF-8');
			$dataCont = mb_strtoupper($db->dbEscape(trim($_POST['contactoemergencia'])), 'UTF-8');
			$dataTele = $db->dbEscape(trim($_POST['telefono']));
			$dataEnfe = mb_strtoupper($db->dbEscape(trim($_POST['enfermedades'])), 'UTF-8');
			$dataUser  = $db->dbEscape(trim($_POST['usuario']));
			$dataEMail = $db->dbEscape(trim($_POST['email']));
			$dataPass1 = $db->dbEscape(trim($_POST['password1']));
			$dataPass2 = $db->dbEscape(trim($_POST['password2']));
			$dataObs = mb_strtoupper($db->dbEscape(trim($_POST['observaciones'])), 'UTF-8');
			$dataEstAlu = $db->dbEscape(trim($_POST['estalu']));
			if (isset($_POST['primeracceso'])) $dataPrimerAcceso = $db->dbEscape(trim($_POST['primeracceso'])); else $dataPrimerAcceso = "";
			$db->dbDisconnect();
			
			// STAP! VALIDATION TIME
			// apellido - obligatorio maxlength 45
			if ($dataApellido=="") $validationErrors['apellido'] = "El Apellido es obligatorio";
			if (strlen($dataApellido)>45) $validationErrors['apellido'] = "El campo Apellido puede contener como máximo 45 caracteres";
			// nombre - obligatorio maxlength 45
			if ($dataNombre=="") $validationErrors['nombre'] = "El Nombre es obligatorio";
			if (strlen($dataNombre)>45) $validationErrors['nombre'] = "El campo Nombre puede contener como máximo 45 caracteres";
			// nacionalidad - seteado y mayor a 0
			if ($dataNacionalidad=="0" || $dataNacionalidad=="") $validationErrors['nacionalidad'] = "La Nacionalidad es obligatoria";
			// f.nacim		obligatoria. no mayor a 100 anos ni menor a 5 anios
			if (!$lib->validateDate($dataFNac)) {
				$validationErrors['fnac'] = "La Fecha de Nacimiento es inválida";
			} else {
		    	$d = $lib->datediff($dataFNac);
		    	if ($d<0) $validationErrors['fnac'] = "La Fecha de Nacimiento es inválida (futura)";
		    	if ($d > (365*100)) $validationErrors['fnac'] = "La Fecha de Nacimiento es inválida (>100)";
			}
			//fecha ultimo pago cooperadora. opcional. tiene q ser fecha valida si esta definido
			/*if ($dataUltPagoCoop != "") {
				if (!$lib->validateDate($dataUltPagoCoop)) {
					$validationErrors['ultimopagocooperadora'] = "La Fecha Ult.Pago Coop. es inválida";
				} else {
			    	$d = $lib->datediff($dataUltPagoCoop);
			    	if ($d<0) $validationErrors['ultimopagocooperadora'] = "La Fecha Ult.Pago Coop. es inválida (futura)";
			    	if ($d > (365*100)) $validationErrors['ultimopagocooperadora'] = "La Fecha Ult.Pago Coop. es inválida (>100)";
				}
			}*/
			//tipdoc. obligatorio, distinto de 0
			if ($dataTipDoc=="0" || $dataTipDoc=="") $validationErrors['tipdoc'] = "El campo Tipo Documento es obligatorio";
			//nrodoc. obligatorio
			if ($dataNroDoc==0) $validationErrors['nrodoc'] = "El campo Número Documento es obligatorio";
			if (!is_numeric($_POST['nrodoc'])) $validationErrors['nrodoc'] = "El campo Número de Documento no es numérico";

			// telefono fijo obligatorio. entre 6 y 20 caracteres. solo numeros, espacios, parentesis y guion
			if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTel1)) $validationErrors['telefono1'] = "El Teléfono Fijo contiene caracteres inválidos";
			if (strlen($dataTel1) < 6 || strlen($dataTel1) > 20) $validationErrors['telefono1'] = "El Teléfono Fijo es obligatorio";
			// telefono celular. si no es blanco, debe contener entre 6 y 20 caracteres. solo numeros, espacios, parentesis y guion
			if ($dataTel2!="") {
				if (strlen($dataTel2) < 6 || strlen($dataTel2) > 20) $validationErrors['telefono2'] = "El Teléfono Celular debe contener entre 6 y 20 caracteres";
				if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTel2)) $validationErrors['telefono2'] = "El Teléfono Celular contiene caracteres inválidos";
			}
			// sexo 		seteado y mayor a 0
			if ($dataSexo=="0" || $dataSexo=="") $validationErrors['sexo'] = "El campo Sexo es obligatorio";
			// estado civil	seteado y mayor a 0
			if ($dataECiv=="0" || $dataECiv=="") $validationErrors['estciv'] = "El Estado Civil es obligatorio";

			// calle 	obligatorio. maxlength 45
			if ($dataCalle=="") $validationErrors['direccion'] = "El campo Calle es obligatorio";
			if (strlen($dataCalle)>45) $validationErrors['direccion'] = "El campo Calle puede contener como máximo 45 caracteres";
			// numero 	obligatorio. maxlength 45
			if ($dataNro=="") $validationErrors['numero'] = "El campo Número es obligatorio";
			if (strlen($dataNro)>45) $validationErrors['numero'] = "El campo Número puede contener como máximo 45 caracteres";
			// entre calles 	opcional. maxlength 45
			if (strlen($dataEntre)>45) $validationErrors['entrecalles'] = "El campo Entre puede contener como máximo 45 caracteres";
			// provincia 	obligatorio	seteado y mayor a 0
			if ($dataProv =="0" || $dataProv=="") $validationErrors['provincia'] = "El campo Provincia es obligatorio";
			// localidad	obligatorio	seteado y mayor a 0
			if ($dataLocal =="0" || $dataLocal=="") $validationErrors['localidad'] = "El campo Localidad es obligatorio";
			// piso 	opcional. maxlength 45
			if (strlen($dataPiso)>45) $validationErrors['piso'] = "El campo Piso puede contener como máximo 45 caracteres";
			// depto 	opcional. maxlength 45
			if (strlen($dataDepto)>45) $validationErrors['depto'] = "El campo Depto puede contener como máximo 45 caracteres";
			// codigo postal 	obligatorio. maxlength 45
			if ($dataCP=="") $validationErrors['codpostal'] = "El campo Cod.Postal es obligatorio";
			if (strlen($dataCP)>45) $validationErrors['codpostal'] = "El campo Cod.Postal puede contener como máximo 45 caracteres";

			// titulo 	opcional. maxlength 45
			if (strlen($dataTitulo)>45) $validationErrors['titulo'] = "El campo Título puede contener como máximo 45 caracteres";
			// otorgado por 	opcional. maxlength 45
			if (strlen($dataOtorga)>45) $validationErrors['otorgadopor'] = "El campo Otorgado Por puede contener como máximo 45 caracteres";
			// obra social 			opcional maxlength 45
			if (strlen($dataOSoc)>45) $validationErrors['obrasocial'] = "El campo Obra Social puede contener como máximo 45 caracteres";
			// contacto emergencia 	obligatorio maxlength 45
			if ($dataCont=="") $validationErrors['contactoemergencia'] = "El campo Contacto Emergencia es obligatorio";
			if (strlen($dataCont)>45) $validationErrors['contactoemergencia'] = "El campo Contacto Emergencia puede contener como máximo 45 caracteres";
			// telef emergencia 	obligatorio maxlength 45
			if ($dataTele=="") $validationErrors['telefono'] = "El campo Teléfono Contacto es obligatorio";
			if (strlen($dataTele)>45) $validationErrors['telefono'] = "El campo Teléfono Contacto puede contener como máximo 45 caracteres";
			if ($dataTele!="") {
				if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTele)) $validationErrors['telefono'] = "El Teléfono Contacto contiene caracteres inválidos";
			}
			// enfermedades 		opcional maxlength 500
			if (strlen($dataEnfe)>500) $validationErrors['enfermedades'] = "El campo Enfermedades puede contener como máximo 500 caracteres";

			// email 	obligatorio maxlength 45 a@b.c
			if ($dataEMail=="") $validationErrors['email'] = "El campo Email es obligatorio";
			if (strlen($dataEMail)>45) $validationErrors['email'] = "El campo Email puede contener como máximo 45 caracteres";
			if ($dataEMail != "") if (!filter_var($dataEMail, FILTER_VALIDATE_EMAIL)) $validationErrors['email'] = "El campo Email es inválido";
			// password 	obligatorio maxlength 45 - deben coincidir password1 y password2 minlength 6
			if ($dataPass1!="") { 
				if (! $lib->isPasswordValid($dataPass1)) $validationErrors['password1'] = "Contraseña inválida. " . $this->POROTO->Config['password_constraints_explained'];

				if (strlen($dataPass1) > 45) {
					$validationErrors['password1'] = "El campo Contraseña puede contener como máximo 45 caracteres";
				} else {
					if ($dataPass1 != $dataPass2) {
						$validationErrors['password2'] = "El campo Contraseña no coincide con su validación";
					}
				}
			}

			// observaciones 	opcional. maxlength 5000
			if (strlen($dataObs) > 5000) $validationErrors['observaciones'] = "El campo Observaciones puede contener como máximo 5000 caracteres. Contiene " . strlen($dataObs);

			// CARRERAS. Tiene que tener al menos una
			$nc = (isset($_POST['new-carreras']) ? $_POST['new-carreras'] : array());
			// if (count($nc) < 1) $validationErrors['carreras'] = "Debe tener al menos una carrera registrada";

			if (count($validationErrors)==0) {
				$db->dbConnect("alumnos/modificar/" . $idpersona);
				$sql = "select idpersona from personas where idpersona!=" . $idpersona . " and tipodoc=" . $dataTipDoc . " and documentonro=" . $dataNroDoc;
				$valdoc = $db->getSQLArray($sql);
				if (count($valdoc)!= 0) $validationErrors['nrodoc'] = "El tipo y número de documento ingresados ya existen en la base";
				$db->dbDisconnect();
			}

			//if (count($validationErrors) == 0) {  
				$db->dbConnect("alumnos/modificar/" . $idpersona);

				$sqlP = "UPDATE personas SET ";
				$sqlP.= " apellido='" . $dataApellido . "'";
				$sqlP.= ",nombre='" . $dataNombre . "'";
				//if ($dataSocio != "") $sqlP.= ",socio='" . $dataSocio . "'";
				//if ($dataUltPagoCoop != "") $sqlP.= ",ultimopagocooperadora='" . $lib->dateDMY2YMD($dataUltPagoCoop) . "'"; else $sqlP.= ",ultimopagocooperadora=null";
				$sqlP.= ",nacionalidad='" . $dataNacionalidad . "'";
				if ($dataFNac == "") $sqlP.= ",fechanac=null"; else $sqlP.= ",fechanac='" . $lib->dateDMY2YMD($dataFNac) . "'";
				$sqlP.= ",tipodoc=" . $dataTipDoc;
				$sqlP.= ",documentonro=" . $dataNroDoc;
				$sqlP.= ",telefono1='" . $dataTel1 . "'";
				$sqlP.= ",telefono2='" . $dataTel2 . "'";
				$sqlP.= ",sexo='" . $dataSexo . "'";
				$sqlP.= ",estadocivil='" . $dataECiv . "'";
				$sqlP.= ",direccion='" . $dataCalle . "'";
				$sqlP.= ",numero='" . $dataNro . "'";
				$sqlP.= ",entrecalles='" . $dataEntre . "'";
				$sqlP.= ",idlocalidad=" . $dataLocal;
				$sqlP.= ",piso='" . $dataPiso . "'";
				$sqlP.= ",depto='" . $dataDepto . "'";
				$sqlP.= ",codpostal='" . $dataCP . "'";
				$sqlP.= ",usumodi='" . $ses->getUsuario() . "'";
				$sqlP.= ",fechamodi=CURRENT_TIMESTAMP";
				$sqlP.= " WHERE idpersona=" . $idpersona;

				$db->begintrans();
				$bOk = $db->update($sqlP, '', true);

				$sqlA = "INSERT INTO alumnos (idpersona,titulosecundario,otorgadopor,aniooegreso,";
				$sqlA.= "estadoalumno_id,certificadotrabajo,observaciones,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
				$sqlA.= $idpersona;
				$sqlA.= ",'" . $dataTitulo . "'";
				$sqlA.= ",'" . $dataOtorga . "'";
				$sqlA.= "," . ($dataAnoEgr!='' ? $dataAnoEgr : 'null');
				$sqlA.= "," . $dataEstAlu;
				$sqlA.= "," . ($dataCLab == "certlab" ? "1" : "0");
				$sqlA.= ",'" . $dataObs . "'";
				$sqlA.= ",'" . $ses->getUsuario() . "'";
				$sqlA.= ",CURRENT_TIMESTAMP ";
				$sqlA.= ",NULL ";
				$sqlA.= ",NULL ";
				$sqlA.= " ON DUPLICATE KEY UPDATE ";
				$sqlA.= "titulosecundario='" . $dataTitulo . "'";
				$sqlA.= ",otorgadopor='" . $dataOtorga . "'";
				$sqlA.= ",certificadotrabajo=" . ($dataCLab == "certlab" ? "1" : "0");
				$sqlA.= ",aniooegreso=" . ($dataAnoEgr!='' ? $dataAnoEgr : 'null');
				$sqlA.= ",estadoalumno_id=" . $dataEstAlu;
				$sqlA.= ",observaciones='" . $dataObs . "'";
				$sqlA.= ",usumodi='" . $ses->getUsuario() . "'";
				$sqlA.= ",fechamodi=CURRENT_TIMESTAMP";
				if ($bOk!==false) $bOk = $db->insert($sqlA, '', true);

				$sqlF = "INSERT INTO fichamedica (idpersona,obrasocial,contactoemergencia,telefono,enfermedades) SELECT ";
				$sqlF.= $idpersona;
				$sqlF.= ",'" . $dataOSoc . "'";
				$sqlF.= ",'" . $dataCont . "'";
				$sqlF.= ",'" . $dataTele . "'";
				$sqlF.= ",'" . $dataEnfe . "'";
				$sqlF.= " ON DUPLICATE KEY UPDATE ";
				$sqlF.= "obrasocial='" . $dataOSoc . "'";;
				$sqlF.= ",contactoemergencia='" . $dataCont . "'";;
				$sqlF.= ",telefono='" . $dataTele . "'";;
				$sqlF.= ",enfermedades='" . $dataEnfe . "'";;
				if ($bOk!==false) $bOk = $db->insert($sqlF, '', true);

				$sqlU = "UPDATE usuarios SET ";
				$sqlU.= "email='" . $dataEMail . "'";
				if ($dataPass1 != "") $sqlU.= ",password='" . $dataPass1 . "'";
				$sqlU.= ",primeracceso=" . ($dataPrimerAcceso == "primeracceso" ? "1" : "0");
				$sqlU.= ",usumodi='" . $ses->getUsuario() . "'";
				$sqlU.= ",fechamodi=CURRENT_TIMESTAMP";
				$sqlU.= " WHERE idpersona=" . $idpersona;
				if ($bOk!==false) $bOk = $db->update($sqlU, '', true);


				$sqlC = array();
				foreach ($nc as $carrera) {
					$a = explode("~**~", $carrera);
					if (count($a)==5) { //vienen: carrera, area, nivel, instrumento, fecha (d/m/Y)
						$carreraNombre = "";
						$carreraDescripcion = "";
						$areaNombre = "";
						$nivelNombre = "";
						$instrumentoDescripcion = "";

						$sql = "INSERT INTO alumnocarrera (idpersona,idcarrera,idinstrumento,idarea,fechainscripcion,";
						$sql.= "fechafinalizada,idnivel,estado,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
						$sql.= $idpersona;
						$sql.= "," . $a[0];
						$sql.= "," . $a[3];
						$sql.= "," . $a[1];
						$sql.= ",'" . $lib->dateDMY2YMD($a[4]) . "'";
						$sql.= ",null";
						$sql.= "," . $a[2];
						$sql.= "," . $this->POROTO->Config['estado_carrera_en_curso_id'];
						$sql.= ",'" . $ses->getUsuario() . "'";
						$sql.= ",CURRENT_TIMESTAMP ";
						$sql.= ",null";
						$sql.= ",null";
						$sqlC[] = $sql;
					}
				}

				foreach ($sqlC as $sqlCarrera) {
					if ($bOk!==false) $bOk = $db->insert($sqlCarrera, '', true);
				}
				if ($bOk === false) {
					$db->rollback();
					$ses->setMessage("Se produjo un error realizando la modificación", SessionMessageType::TransactionError, $params);
					header("Location: /gestion-alumnos", TRUE, 302);
					exit();

				} else {
					$db->commit();
				}
				$db->dbDisconnect();

				$ses->setMessage("Usuario modificado con éxito", SessionMessageType::Success, $params);
				header("Location: /gestion-alumnos", TRUE, 302);
				exit();

			//}

		}

		$db->dbConnect("alumnos/modificar/" . $idpersona);
	    $sql = "SELECT id,descripcion FROM tipodoc order by id";
		$viewDataTipoDocumento = $db->getSQLArray($sql);

		$sql = "select p.apellido, p.nombre, p.nacionalidad, date_format(fechanac, '%d/%m/%Y') fnac_dmy, p.tipodoc, td.descripcion, p.documentonro, p.telefono1, p.telefono2, p.sexo, p.estadocivil";
		$sql.= " ,p.direccion, p.entrecalles, p.numero, p.piso, p.depto, p.idlocalidad, l.descripcion localidad_descripcion, l.idprovincia, p.codpostal, a.certificadotrabajo certlab";
		$sql.= " ,a.titulosecundario, a.otorgadopor, a.aniooegreso anoegreso";
		$sql.= " ,fm.obrasocial, fm.contactoemergencia, fm.telefono, fm.enfermedades";
		$sql.= " ,u.usuario, u.password, u.email, u.primeracceso";
		$sql.= " ,a.observaciones";
		$sql.= " ,a.estadoalumno_id, ea.descripcion ea_descr";
		$sql.= " from personas p inner join tipodoc td on td.id=p.tipodoc inner join usuarios u on u.idpersona=p.idpersona";
		$sql.= " left join localidades l on l.id=p.idLocalidad";
		$sql.= " left join alumnos a on a.idpersona=p.idpersona left join estadoalumno ea on a.estadoalumno_id=ea.id";
		$sql.= " left join fichamedica fm on fm.idpersona=p.idpersona";
		$sql.= " where p.idpersona=" . $idpersona;
		$viewData = $db->getSQLArray($sql);

		$sql = "SELECT idcarrera,nombre,descripcion FROM carreras WHERE estado=1 order by nombre";
		$viewDataCarreras = $db->getSQLArray($sql);

		$sql = "select idarea,nombre from areas";
		$viewDataAreas = $db->getSQLArray($sql);

		$sql = "select idinstrumento,nombre,descripcion from instrumentos order by nombre";
		$viewDataInstrumentos = $db->getSQLArray($sql);

		$sql = "select id,descripcion from estadoalumno order by 2";
		$viewDataEstadoAlumnos = $db->getSQLArray($sql);

		$sql = "select id,descripcion from provincias";
		$viewDataProvincias = $db->getSQLArray($sql);

		$idprov = (isset($_POST['provincia']) ? $_POST['provincia'] : $viewData[0]['idprovincia']);
		if ($idprov=="") $idprov=0;
		$sql = "select id,descripcion,cp from localidades where idprovincia=" . $idprov . " order by descripcion";
		$viewDataLocalidades = $db->getSQLArray($sql);

		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		$sql = "SELECT date_format(ac.fechainscripcion, '%d/%m/%Y') fechainscripcion_dmy, c.nombre carrera_nombre, c.descripcion carrera_descripcion, i.nombre instrumento_nombre, i.descripcion instrumento_descripcion, a.nombre area_nombre, eac.descripcion estado, cn.descripcion nivelDescripcion,c.idcarrera,ac.idinstrumento,ac.idarea"; // AJUSTE-8NOV AGREGUE C.IDCARRERA
		$sql.= " FROM alumnocarrera ac inner join estadoalumnocarrera eac on ac.estado=eac.id";
		$sql.= " inner join carreras c on ac.idcarrera=c.idcarrera";
		$sql.= " inner join instrumentos i on ac.idinstrumento=i.idinstrumento";
		$sql.= " inner join carreraniveles cn on ac.idnivel=cn.id";
		$sql.= " left join areas a on ac.idarea=a.idarea";
		$sql.= " WHERE ac.idpersona=" . $idpersona;
		$viewDataAlumnoCarreras = $db->getSQLArray($sql);

		$db->dbDisconnect();

		$viewDataSexo = $this->POROTO->Config['dominios']['sexo'];
		$viewDataNacionalidad = $this->POROTO->Config['dominios']['nacionalidad'];
		$viewDataEstadoCivil = $this->POROTO->Config['dominios']['estadocivil'];
		//$viewDataSocio = $this->POROTO->Config['dominios']['socio'];

		$tempCarreras = isset($_POST['new-carreras']) ? $_POST['new-carreras'] : array();
		$newCarreras = array();

		foreach ($tempCarreras as $tempCarrera) {
			$arr = explode("~**~", $tempCarrera);
			if (count($arr)==5) { //vienen: carrera, area, nivel, instrumento, fecha (d/m/Y)
				$carreraNombre = "";
				$carreraDescripcion = "";
				$areaNombre = "";
				$nivelNombre = "";
				$instrumentoDescripcion = "";
				foreach ($viewDataCarreras as $carrera) {
					if ($carrera['idcarrera'] == $arr[0]) {
						$carreraNombre = $carrera['nombre'];
						$carreraDescripcion = $carrera['descripcion'];
						break;
					}
				}
				foreach ($viewDataAreas as $area) {
					if ($area['idarea'] == $arr[1]) {
						$areaNombre = $area['nombre'];
						break;
					}
				}

				$db->dbConnect("alumnos/modificar");
				$sql = "select descripcion from carreraniveles where id=" . $arr[2];
				$arrCN = $db->getSQLArray($sql);
				if (count($arrCN)==1) $nivelNombre = $arrCN[0]['descripcion']; else $nivelNombre = "";
				$db->dbDisconnect();

				foreach ($viewDataInstrumentos as $instr) {
					if ($instr['idinstrumento'] == $arr[3]) {
						$instrumentoNombre = $instr['nombre'];
						break;
					}
				}
				
				$newCarreras[] = array( 'carrera-nombre'=>$carreraNombre, 'carrera-descripcion'=>$carreraDescripcion, 
										'area-nombre'=>$areaNombre, 'nivel-nombre'=>$nivelNombre, 
										'instrumento-nombre'=>$instrumentoNombre, 'fecha'=>$arr[4], 
										'raw'=>$tempCarrera);
			}
		}
		$status = "modificar";
		$pageTitle="Modificar";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/ver-alumno.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}
	
	public function gestion($params="") { //OK
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		$db->dbConnect("alumnos/gestion/" . $params);

		//Cambio 38 Leo 20170706
			if(!$ses->tienePermiso('','Gestión de Alumnos Acceso desde Menu')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
			}
		//Fin Cambio 38 Leo 20170706
		
		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

	    $sql = "SELECT id,descripcion FROM tipodoc order by id";
		$viewDataTipoDocumento = $db->getSQLArray($sql);

		$sql = "select id,descripcion from estadoalumno order by 2";
		$viewDataEstadoAlumnos = $db->getSQLArray($sql);

		$sql = "select idcarrera, nombre, descripcion from carreras where estado=1 order by 2";
		$viewDataCarreras = $db->getSQLArray($sql);

		$db->dbDisconnect();

		$pageTitle="Gestión de Alumnos";
		include($this->POROTO->ViewPath . "/-header.php");
		if ($params!="") $message['param'] = $params;
		include($this->POROTO->ViewPath . "/gestion-alumnos.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}


	public function nivelar($idpersona, $idalumnocarrera, $params="") { //OK
		//Permite nivelar a un alumno a un año superior, por ejemplo si esta en foba inicial y por nivelacion
		//tiene que estar aprobado hasta el nivel 2, ejecuto y deberia ponerle aprobada por nivelacion
		//a las materias del nivel 1.
		//Requisito previo, pasar el alumno a estado NIVELACION, y carreras deben ser FOBA.
		$dataIdCarrera=0;
                
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Alumno Nivelar')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /gestion-alumnos", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		$db->dbConnect("alumnos/nivelar/" . $idpersona . "/" . $idalumnocarrera . "/" . $params);
		$dataIdAlumnoCarrera = $db->dbEscape($idalumnocarrera);
		$dataIdPersona = $db->dbEscape($idpersona);
		$sql = "select p.apellido, p.nombre nombres, c.nombre, c.descripcion, c.idcarrera";
		$sql.= " from personas p";
		$sql.= " inner join alumnocarrera ac on p.idpersona=ac.idpersona";
		$sql.= " inner join alumnos a on a.idpersona=p.idpersona";
		$sql.= " inner join carreras c on c.idcarrera=ac.idcarrera";
		$sql.= " where ac.idpersona=" . $dataIdPersona;
		$sql.= " and ac.idalumnocarrera=" . $dataIdAlumnoCarrera;
		$sql.= " and a.estadoalumno_id=" . $this->POROTO->Config['estado_alumno_nivelar_id'];
		
		$viewData = $db->getSQLArray($sql);
		if (count($viewData)!=1) {
			$ses->setMessage("El alumno seleccionado NO tiene un estado APTO para Nivelar.", SessionMessageType::TransactionError);
			header("Location: /gestion-alumnos", TRUE, 302);
			exit();
		}else{
                        $dataIdCarrera=$viewData[0]["idcarrera"];
                }
		if (isset($_POST['niveles'])) {
			
			$db->begintrans();
			$bOk=true;
			
			$list = "";
			foreach ($_POST['niveles'] as $nivel) $list .= "'" . $db->dbEscape($nivel) . "',";
			$list = substr($list, 0, -1);
			$sql = "select m.idmateria from carreramateria cm inner join materias ";
			$sql.= "m on m.idmateria=cm.idmateria where idcarrera=" . $dataIdCarrera . " and anio ";
			$sql.= "in (" . $list . ") and m.estado=1";
			$materias = $db->getSQLArray($sql);
			foreach ($materias as $materia) {
				$sql= "select * from alumnomateria where idpersona=". $dataIdPersona . " and ";
				$sql.="idalumnocarrera=".$dataIdAlumnoCarrera . " and idmateria=" . $materia['idmateria']. " ";
				$viewAlumnoMateria = $db->getSQLArray($sql);
				if (count($viewAlumnoMateria)==0) {
				//Dar por aprobada cada una de las materias.
				$sql = "INSERT INTO alumnomateria (idpersona, idalumnocarrera,idcarrera, idmateria, aniocursada, ";
				$sql.= "idestadoalumnomateria, fechaaprobacion, usucrea, fechacrea) SELECT";
				$sql.= " " . $dataIdPersona . ",";
                                $sql.= " " . $dataIdAlumnoCarrera . ",";
				$sql.= " " . $dataIdCarrera . ","; //Cambio 43 20170717
				$sql.= " " . $materia['idmateria'] . ",";
				$sql.= " " . date('Y') . ",";
				$sql.= " " . $this->POROTO->Config['estado_alumnomateria_nivelacion']  . ",";
				$sql.= " CURRENT_TIMESTAMP,";
				$sql.= " '" . $ses->getUsuario() . "',";
				$sql.= " CURRENT_TIMESTAMP";
				if ($bOk!==false) $bOk = $db->insert($sql, '', true);
				}
				else
				{
				//update poniendola nivelada siempre y cuando no este aprobada por algun otro motivo.
				if($viewAlumnoMateria[0]["idestadoalumnomateria"]!=$this->POROTO->Config['estado_alumnomateria_aprobada'] && 
				$viewAlumnoMateria[0]["idestadoalumnomateria"]!=$this->POROTO->Config['estado_alumnomateria_aprobadaxequiv'] && 
				$viewAlumnoMateria[0]["idestadoalumnomateria"]!=$this->POROTO->Config['estado_alumnomateria_nivelacion'] ){
					$sql="update alumnomateria set ";
					$sql.="idestadoalumnomateria=". $this->POROTO->Config['estado_alumnomateria_nivelacion']  . ",";
					$sql.="usumodi='" . $ses->getUsuario() . "',";
					$sql.="fechamodi=CURRENT_TIMESTAMP ";
					$sql.="where idpersona=" . $dataIdPersona . " ";
					$sql.="and idalumnocarrera=" . $dataIdAlumnoCarrera . " ";
					$sql.="and idmateria=" .  $materia['idmateria'];
					if ($bOk!==false) $bOk = $db->update($sql, '', true);
					}
				}
				
			}
			$sql = "UPDATE alumnos SET estadoalumno_id=" . $this->POROTO->Config['estado_alumno_regular_id'];
			$sql.= " WHERE idpersona=" . $dataIdPersona;
			if ($bOk!==false) $bOk = $db->update($sql, '', true);
			
			if ($bOk === false) {
					$db->rollback();
					$ses->setMessage("Se produjo un error al guardar los cambios", SessionMessageType::TransactionError);
					header("Location: /gestion-alumnos", TRUE, 302);
					exit();

				} else {
					$db->commit();
					$ses->setMessage("Alumno nivelado con éxito", SessionMessageType::Success, $params);
					header("Location: /gestion-alumnos", TRUE, 302);
				}
			$db->dbDisconnect();
			exit();
		}

		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		$sql = "select count(0),anio";
		$sql.= " from carreramateria cm inner join materias m on cm.idmateria=m.idmateria and m.estado=1";
		$sql.= " where cm.idcarrera=" . $dataIdCarrera;
		$sql.= " group by anio";
		$sql.= " order by 2";
		$viewDataNiveles = $db->getSQLArray($sql);

		$db->dbDisconnect();

		$pageTitle="Nivelacion de Alumno";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/nivelar-alumno.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}
	
}
?>