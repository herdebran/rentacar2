<?php
class Profesores {
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

	public function gestion() {
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		
		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Profesores Acceso desde Menu')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		$db->dbConnect("profesores/gestion");
		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	
	    $sql = "SELECT id,descripcion FROM tipodoc order by id";
		$viewDataTipoDocumento = $db->getSQLArray($sql);
		$sql = "SELECT idmateria,nombre FROM materias WHERE estado=1 ORDER BY 2";
		$viewDataMaterias = $db->getSQLArray($sql);
		$db->dbDisconnect();
		$pageTitle="Gestión de Profesores";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/gestion-profesores.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}

	public function ajaxlist($page = 1) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("profesores/ajaxlist/" . $page);
		if (! is_numeric($page)) {
			$page=1;
		} else {
			if ($page<1) $page=1;
		}

		$sql = "select SQL_CALC_FOUND_ROWS pr.idpersona, p.apellido, p.nombre, u.estado, t.descripcion tdoc, p.documentonro";
		$sql.= " from profesores pr";
		$sql.= " inner join personas p on pr.idpersona=p.idpersona";
		$sql.= " inner join tipodoc t on t.id=p.tipodoc";
		$sql.= " inner join usuarios u on u.idpersona=p.idpersona";
		$sql.= " left join comprofesor cp on cp.idpersona=p.idpersona";
		$sql.= " left join comisiones c on cp.idcomision=c.idcomision ";
		$sql.= " where 1=1";
		if ($_POST['ap']!="") $sql.= " and p.apellido like '%" . $db->dbEscape($_POST['ap']) . "%'";
		if ($_POST['no']!="") $sql.= " and p.nombre like '%" . $db->dbEscape($_POST['no']) . "%'";
		if ($_POST['td']!="" && $_POST['td']!="0" ) $sql.= " and t.id=" . $db->dbEscape($_POST['td']);
		if ($_POST['nd']!="") $sql.= " and p.documentonro like '%" . $db->dbEscape($_POST['nd']) . "%'";
		if ($_POST['ma']!="" && $_POST['ma']!="0" ) $sql.= " and c.idmateria=" . $db->dbEscape($_POST['ma']);
		$sql.= " GROUP BY pr.idpersona";
		$sql.= " ORDER BY 2, 3";
		$sql.= " LIMIT " . (($page-1) * $this->POROTO->Config['records_per_page']) . "," . $this->POROTO->Config['records_per_page'];
		$arrData = $db->getSQLArray($sql);

		$sql = "SELECT FOUND_ROWS() q";
		$arrQ = $db->getSQLArray($sql);
		$db->dbDisconnect();
		$pages = ceil($arrQ[0]['q'] / $this->POROTO->Config['records_per_page']);
		$result=array("currentpage"=>$page, "qrows"=>$arrQ[0], "pages"=>$pages, "rows"=>$arrData);
		echo json_encode($result);
	}

	public function habilitar($idPersona, $params="") {
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$db->dbConnect("profesores/habilitar/" . $idPersona);

		//Cambio 38 Leo 20170706
			if(!$ses->tienePermiso('','Gestion de Usuarios Habilitacion y Cambio de contraseña')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /gestion-profesores", TRUE, 302);
				exit();
			}
		//Fin Cambio 38 Leo 20170706

		$sql = "update usuarios set estado=1 where idpersona=" . $db->dbEscape($idPersona);
		$db->update($sql);
		$db->dbDisconnect();
		
		$ses->setMessage("Profesor Habilitado", SessionMessageType::Success, $params);
		header("Location: /gestion-profesores", TRUE, 302);
	}

	public function deshabilitar($idPersona, $params="") {
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$db->dbConnect("profesores/deshabilitar/" . $idPersona);

		//Cambio 38 Leo 20170706
			if(!$ses->tienePermiso('','Gestion de Usuarios Habilitacion y Cambio de contraseña')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /gestion-profesores", TRUE, 302);
				exit();
			}
		//Fin Cambio 38 Leo 20170706

		$sql = "update usuarios set estado=0 where idpersona=" . $db->dbEscape($idPersona);
		$db->update($sql);
		$db->dbDisconnect();
		
		$ses->setMessage("Profesor Deshabilitado", SessionMessageType::Success, $params);
		header("Location: /gestion-profesores", TRUE, 302);
	}

	public function resetpassword($idpersona, $params="") {
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];

		//Cambio 38 Leo 20170706
			if(!$ses->tienePermiso('','Gestion de Usuarios Habilitacion y Cambio de contraseña')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /gestion-profesores", TRUE, 302);
				exit();
			}
		//Fin Cambio 38 Leo 20170706
		$db->dbConnect("profesores/resetpassword/" . $idpersona . "/" . $params);
		
	    $characters = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	    $newPwd = '';
	    for ($i = 0; $i < 8; $i++) {
	        $newPwd .= $characters[rand(0, strlen($characters) - 1)];
	    }

		$sql = "update usuarios set password='" . $newPwd . "', primeracceso=1 where idpersona=" . $db->dbEscape($idpersona);
		$db->update($sql);

		$sql = "select usuario, password, email from usuarios where idpersona=" . $db->dbEscape($idpersona);
		$arr = $db->getSQLArray($sql);

		$db->dbDisconnect();

		if ($this->POROTO->Config['override_mail_address'] != "") $mailto = $this->POROTO->Config['override_mail_address']; else $mailto = $arr[0]['email'];
                $mailSubject = $this->POROTO->Config["empresa_descripcion"]." - Acceso Profesor";
		$mailBody = "Le contamos que puede ingresar al sitio web de alumnos con el usuario <b>" . $arr[0]['usuario'] . "</b> y la contraseña <b>" . $arr[0]['password'] . "</b>";
		$lib->sendMail($mailto, $mailSubject, $mailBody);

		$ses->setMessage("Clave reseteada y notificación enviada", SessionMessageType::Success, $params);
		header("Location: /gestion-profesores", TRUE, 302);
	}

	public function modificar($idPersona, $params="") {
		$validationErrors = array();
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Profesores Agregar o Modificar Profesor')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /gestion-profesores", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706

		$db->dbConnect("profesores/modificar/" . $idPersona);
		$dataIdPersona = $db->dbEscape($idPersona);
		$newCertificados = array();
		
		if (isset($_POST['apellido'])) {
			$dataApellido = mb_strtoupper($db->dbEscape(trim($_POST['apellido'])), 'UTF-8');
			$dataNombre = mb_strtoupper($db->dbEscape(trim($_POST['nombre'])), 'UTF-8');
			$dataNacionalidad = $db->dbEscape($_POST['nacionalidad']);
			$dataFNac = $db->dbEscape($_POST['fnac']);
			$dataTipDoc = $db->dbEscape($_POST['tipdoc']);
			$dataNroDoc = $db->dbEscape(intval($_POST['nrodoc']));
			$dataTel1 = $db->dbEscape(trim($_POST['telefono1']));
			$dataTel2 = $db->dbEscape(trim($_POST['telefono2']));
			$dataSexo = $db->dbEscape($_POST['sexo']);
			$dataECiv = $db->dbEscape($_POST['estciv']);
			$dataHijos = mb_strtoupper($db->dbEscape(trim($_POST['hijos'])), 'UTF-8');
			$dataFamiliares = mb_strtoupper($db->dbEscape(trim($_POST['familiares'])), 'UTF-8');
			$dataCuil = $db->dbEscape($_POST['cuil']);

			$dataCalle = mb_strtoupper($db->dbEscape(trim($_POST['direccion'])), 'UTF-8');
			$dataNro   = mb_strtoupper($db->dbEscape(trim($_POST['numero'])), 'UTF-8');
			$dataEntre = mb_strtoupper($db->dbEscape(trim($_POST['entrecalles'])), 'UTF-8');
			$dataProv  = $db->dbEscape(trim($_POST['provincia']));
			$dataLocal = $db->dbEscape($_POST['localidad']);
			$dataPiso  = mb_strtoupper($db->dbEscape(trim($_POST['piso'])), 'UTF-8');
			$dataDepto = mb_strtoupper($db->dbEscape(trim($_POST['depto'])), 'UTF-8');
			$dataCP    = mb_strtoupper($db->dbEscape(trim($_POST['codpostal'])), 'UTF-8');

			$dataTitulo = mb_strtoupper($db->dbEscape(trim($_POST['titulo'])), 'UTF-8');
			$dataNroRegistro = mb_strtoupper($db->dbEscape(trim($_POST['nroregistro'])), 'UTF-8');
			$dataOtorga = mb_strtoupper($db->dbEscape(trim($_POST['otorgadopor'])), 'UTF-8');
			$dataAnoEgr = $db->dbEscape($_POST['anoegreso']);

			$dataOSoc = mb_strtoupper($db->dbEscape(trim($_POST['obrasocial'])), 'UTF-8');
			$dataCont = mb_strtoupper($db->dbEscape(trim($_POST['contactoemergencia'])), 'UTF-8');
			$dataTele = $db->dbEscape(trim($_POST['telefono']));
			$dataEnfe = mb_strtoupper($db->dbEscape(trim($_POST['enfermedades'])), 'UTF-8');
			
			$dataEMail = $db->dbEscape(trim($_POST['email']));
			$dataPass1 = $db->dbEscape(trim($_POST['password1']));
			$dataPass2 = $db->dbEscape(trim($_POST['password2']));
			if (isset($_POST['primeracceso'])) $dataPrimerAcceso = $db->dbEscape($_POST['primeracceso']); else $dataPrimerAcceso = "";

			$dataAntiguedad = $db->dbEscape(trim($_POST['antiguedad']));
			$dataFIngCol = $db->dbEscape($_POST['ingcol']);
			$dataFIngDoc = $db->dbEscape($_POST['ingdoc']);
			$dataFIngDea = $db->dbEscape($_POST['ingdea']);
			if (isset($_POST['modulostitulares'])) $dataModulosTitulares = $db->dbEscape($_POST['modulostitulares']); else $dataModulosTitulares = "";
			
			
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
			//tipdoc. obligatorio, distinto de 0
			if ($dataTipDoc=="0" || $dataTipDoc=="") $validationErrors['tipdoc'] = "El campo Tipo Documento es obligatorio";
			//nrodoc. obligatorio
			if ($dataNroDoc==0) $validationErrors['nrodoc'] = "El campo Número Documento es obligatorio";
			if (!is_numeric($_POST['nrodoc'])) $validationErrors['nrodoc'] = "El campo Número de Documento no es numérico";
			// telefono fijo obligatorio. entre 6 y 20 caracteres. solo numeros, espacios, parentesis y guion
			if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTel1)) $validationErrors['telefono1'] = "El Teléfono Fijo contiene caracteres inválidos";
			if (strlen($dataTel1) < 6 || strlen($dataTel1) > 20) $validationErrors['telefono1'] = "El Teléfono Fijo es obligatorio y requiere entre 6 y 20 caracteres";
			// telefono celular. si no es blanco, debe contener entre 6 y 20 caracteres. solo numeros, espacios, parentesis y guion
			if ($dataTel2!="") {
				if (strlen($dataTel2) < 6 || strlen($dataTel2) > 20) $validationErrors['telefono2'] = "El Teléfono Celular debe contener entre 6 y 20 caracteres";
				if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTel2)) $validationErrors['telefono2'] = "El Teléfono Celular contiene caracteres inválidos";
			}
			// sexo 		seteado y mayor a 0
			if ($dataSexo=="0" || $dataSexo=="") $validationErrors['sexo'] = "El campo Sexo es obligatorio";
			// estado civil	seteado y mayor a 0
			if ($dataECiv=="0" || $dataECiv=="") $validationErrors['estciv'] = "El Estado Civil es obligatorio";
			// hijos 		opcional. maxlength 45
			if (strlen($dataHijos)>45) $validationErrors['hijos'] = "El campo Hijos puede contener como máximo 45 caracteres";
			// familiares 		opcional. maxlength 45
			if (strlen($dataFamiliares)>45) $validationErrors['familiares'] = "El campo Familiares a Cargo puede contener como máximo 45 caracteres";	
			// cuil 			opcional. solo numeros
			if ($dataCuil !="") if (! is_numeric($dataCuil)) $validationErrors['cuil'] = "El campo Cuil contiene caracteres inválidos";

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

			// titulo 			obligatorio. maxlength 150
			// if ($dataTitulo=="") $validationErrors['titulo'] = "El Título es obligatorio";
			if (strlen($dataTitulo)>150) $validationErrors['titulo'] = "El campo Título puede contener como máximo 150 caracteres";
			// dataNroRegistro 	obligatorio. maxlength 45
			// if ($dataNroRegistro=="") $validationErrors['nroregistro'] = "El Nº Registro es obligatorio";
			if (strlen($dataNroRegistro)>45) $validationErrors['nroregistro'] = "El campo Nº Registro puede contener como máximo 45 caracteres";
			// otorgado por 	obligatorio. maxlength 150
			// if ($dataOtorga=="") $validationErrors['otorgadopor'] = "El campo Otorgado Por es obligatorio";
			if (strlen($dataOtorga)>150) $validationErrors['otorgadopor'] = "El campo Otorgado Por puede contener como máximo 150 caracteres";
			// ano egreso 		obligatorio. numerico. entre 1930 y el anio actual
			if ($dataAnoEgr=="") {
				// $validationErrors['anoegreso'] = "El campo Año Egreso es obligatorio";
			} else {
				if (strval(intval($dataAnoEgr)) != $dataAnoEgr) {
					$validationErrors['anoegreso'] = "El campo Año Egreso contiene caracteres inválidos";
				} else {
					if (intval($dataAnoEgr)<1930) $validationErrors['anoegreso'] = "El campo Año Egreso no puede ser anterior a 1930";
					if (intval($dataAnoEgr)>date("Y")) $validationErrors['anoegreso'] = "El campo Año Egreso no puede ser posterior al año actual";
				}
			}

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
			if ($dataPass1=="") { 
				// $validationErrors['password1'] = "El campo Contraseña es obligatorio";
			} else {
				if (! $lib->isPasswordValid($dataPass1)) $validationErrors['password1'] = "Contraseña inválida. " . $this->POROTO->Config['password_constraints_explained'];

				if (strlen($dataPass1) > 45) {
					$validationErrors['password1'] = "El campo Contraseña puede contener como máximo 45 caracteres";
				} else {
					if ($dataPass1 != $dataPass2) {
						$validationErrors['password2'] = "El campo Contraseña no coincide con su validación";
					}
				}
			}
			//primeracceso checkbox sin validaciones

			//antiguedad a 1/3/16	obligatorio. numerico entre 0 y 99
			if ($dataAntiguedad=="") {
				$validationErrors['antiguedad'] = "El campo Antiguedad Docente al 01/03/2016 es obligatorio";
			} else {
				if (intval($dataAntiguedad) != $dataAntiguedad) {
					$validationErrors['antiguedad'] = "El campo Antiguedad Docente al 01/03/2016 contiene caracteres inválidos";
				} else {
					if (intval($dataAntiguedad)<0) $validationErrors['antiguedad'] = "El campo Antiguedad Docente al 01/03/2016 no puede ser menor a 0";
					if (intval($dataAntiguedad)>99) $validationErrors['antiguedad'] = "El campo Antiguedad Docente al 01/03/2016 no puede ser mayor a 99";
				}
			}
			//fecha ingreso colegio		obligatoria. no mayor a 100 anos ni futura
			if (!$lib->validateDate($dataFIngCol)) {
				$validationErrors['ingcol'] = "La Fecha Ingreso a la Escuela es inválida";
			} else {
		    	$d = $lib->datediff($dataFIngCol);
		    	if ($d<0) $validationErrors['ingcol'] = "La Fecha Ingreso a la Escuela es inválida (futura)";
		    	if ($d > (365*100)) $validationErrors['ingcol'] = "La Fecha Ingreso a la Escuela es inválida (>100)";
			}
			//fecha ingreso docencia	obligatoria. no mayor a 100 anos ni futura
			if (!$lib->validateDate($dataFIngDoc)) {
				$validationErrors['ingdoc'] = "La Fecha Ingreso a la Docencia Provincial es inválida";
			} else {
		    	$d = $lib->datediff($dataFIngDoc);
		    	if ($d<0) $validationErrors['ingdoc'] = "La Fecha Ingreso a la Docencia Provincial es inválida (futura)";
		    	if ($d > (365*100)) $validationErrors['ingdoc'] = "La Fecha Ingreso a la Docencia Provincial es inválida (>100)";
			}
			//fecha ingreso dea		obligatoria. no mayor a 100 anos ni futura
			if (!$lib->validateDate($dataFIngDea)) {
				$validationErrors['ingdea'] = "La Fecha Ingreso a la DEA es inválida";
			} else {
		    	$d = $lib->datediff($dataFIngDea);
		    	if ($d<0) $validationErrors['ingdea'] = "La Fecha Ingreso a la DEA es inválida (futura)";
		    	if ($d > (365*100)) $validationErrors['ingdea'] = "La Fecha Ingreso a la DEA es inválida (>100)";
			}
			//modulostitulares checkbox sin validaciones

			// CERTIFICADOS. sin validaciones
			$nc = (isset($_POST['new-certificados']) ? $_POST['new-certificados'] : array());
			foreach ($nc as $certificado) {
				$a = explode("~**~", $certificado);
				if (count($a)==3) { //vienen: titulo, otorgado, anio
					$newCertificados[] = array( 'titulo'=>$a[0],
												'otorgadopor'=>$a[1],
												'anio'=>$a[2],
												'raw'=>$a[0] . "~**~" . $a[1] . "~**~" . $a[2],
												);
				}
			}


			//me fijo que el tipo y numero de documento no esten registrados ya por otra persona
			if (count($validationErrors)==0) {
				$sql = "select idpersona from personas where idpersona!=" . $dataIdPersona . " AND tipodoc=" . $dataTipDoc . " and documentonro=" . $dataNroDoc;
				$valdoc = $db->getSQLArray($sql);
				if (count($valdoc)!= 0) $validationErrors['nrodoc'] = "El tipo y número de documento ingresados ya existen en la base";
			}

			if (count($validationErrors)==0) {
				$sqlP = "UPDATE personas SET";
				$sqlP.= " apellido='" . $dataApellido . "'";
				$sqlP.= ",nombre='" . $dataNombre . "'";
				$sqlP.= ",tipodoc=" . $dataTipDoc;
				$sqlP.= ",documentonro=" . $dataNroDoc;
				if ($dataCuil=="") $sqlP.= ",cuil=null " ; else $sqlP.= ",cuil=" . $dataCuil;
				$sqlP.= ",telefono1='" . $dataTel1 . "'";
				$sqlP.= ",telefono2='" . $dataTel2 . "'";
				$sqlP.= ",estadocivil='" . $dataECiv . "'";
				$sqlP.= ",direccion='" . $dataCalle . "'";
				$sqlP.= ",numero='" . $dataNro . "'";
				$sqlP.= ",piso='" . $dataPiso . "'";
				$sqlP.= ",depto='" . $dataDepto . "'";
				$sqlP.= ",idLocalidad=" . $dataLocal;
				$sqlP.= ",codpostal='" . $dataCP . "'";
				$sqlP.= ",fechanac='" . $lib->dateDMY2YMD($dataFNac) . "'";
				$sqlP.= ",nacionalidad='" . $dataNacionalidad . "'";
				$sqlP.= ",sexo='" . $dataSexo . "'";
				$sqlP.= ",entrecalles='" . $dataEntre . "'";
				$sqlP.= ",hijos='" . $dataHijos . "'";
				$sqlP.= ",familiaresacargo='" . $dataFamiliares . "'";
				$sqlP.= ",usumodi='" . $ses->getUsuario() . "'";
				$sqlP.= ",fechamodi=CURRENT_TIMESTAMP";
				$sqlP.= " WHERE idpersona=" . $dataIdPersona;

				$db->begintrans();
				$bOk = $db->update($sqlP, '', true);

				$sqlA = "UPDATE profesores SET ";
				$sqlA.= " titulohabilitante='" . $dataTitulo . "'";
				$sqlA.= ",otorgadopor='" . $dataOtorga . "'";
				if ($dataAnoEgr != "") $sqlA.= ",anio=" . $dataAnoEgr; else $sqlA.= ",anio=null";
				$sqlA.= ",nroregistro='" . $dataNroRegistro . "'";
				$sqlA.= ",fechaingcolegio='" . $lib->dateDMY2YMD($dataFIngCol) . "'";
				$sqlA.= ",fechaingdocencia='" . $lib->dateDMY2YMD($dataFIngDoc) . "'";
				$sqlA.= ",fechaingresodea='" . $lib->dateDMY2YMD($dataFIngDea) . "'";
				$sqlA.= ",antiguedadafecha=" . $dataAntiguedad;
				$sqlA.= ",modulostitulares=" . ($dataModulosTitulares == "modulostitulares" ? "1" : "0");
				$sqlA.= ",usumodi='" . $ses->getUsuario() . "'";
				$sqlA.= ",fechamodi=CURRENT_TIMESTAMP";
				$sqlA.= " WHERE idpersona=" . $dataIdPersona;
				if ($bOk!==false) $bOk = $db->update($sqlA, '', true);


				$sqlF = "INSERT INTO fichamedica (idpersona,obrasocial,contactoemergencia,telefono,enfermedades) SELECT ";
				$sqlF.= " $dataIdPersona";
				$sqlF.= ",'" . $dataOSoc . "'";
				$sqlF.= ",'" . $dataCont . "'";
				$sqlF.= ",'" . $dataTele . "'";
				$sqlF.= ",'" . $dataEnfe . "'";
				$sqlF.= " ON DUPLICATE KEY UPDATE obrasocial='" . $dataOSoc . "'";
				$sqlF.= ",contactoemergencia='" . $dataCont . "'";
				$sqlF.= ",telefono='" . $dataTele . "'";
				$sqlF.= ",enfermedades='" . $dataEnfe . "'";
				if ($bOk!==false) $bOk = $db->update($sqlF, '', true);

				$sqlU = "UPDATE usuarios SET ";
				$sqlU.= " email='" . $dataEMail . "'";
				if ($dataPass1 != "") $sqlU.= ",password='" . $dataPass1 . "'";
				$sqlU.= ",primeracceso=" . ($dataPrimerAcceso == "primeracceso" ? "1" : "0");
				$sqlU.= ",usumodi='" . $ses->getUsuario() . "'";
				$sqlU.= ",fechamodi=CURRENT_TIMESTAMP";
				$sqlU.= " WHERE idpersona=" . $dataIdPersona;
				if ($bOk!==false) $bOk = $db->update($sqlU, '', true);

				$sql = "DELETE FROM profesorestudios WHERE idpersona=" . $dataIdPersona;
				if ($bOk!==false) $bOk = $db->update($sql, '', true);

				$sqlC = array();
				foreach ($nc as $certificado) {
					$a = explode("~**~", $certificado);
					if (count($a)==3) { //vienen: titulo, otorgado, anio
						$sql = "INSERT INTO profesorestudios (idpersona, titulo, otorgadopor, anio) SELECT ";
						$sql.= $dataIdPersona;
						$sql.= ",'" . $a[0] . "'";
						$sql.= ",'" . $a[1] . "'";
						$sql.= "," . $a[2];
						$sqlC[] = $sql;

					}
				}

				foreach ($sqlC as $sqlCertificado) {
					if ($bOk!==false) $bOk = $db->insert($sqlCertificado, '', true);
				}
				if ($bOk === false) {
					$db->rollback();
					$ses->setMessage("Se produjo un error realizando la modificación", SessionMessageType::TransactionError, $params);
					header("Location: /gestion-profesores", TRUE, 302);
					exit();

				} else {
					$db->commit();
				}
				$db->dbDisconnect();

				$ses->setMessage("Profesor modificado con éxito", SessionMessageType::Success, $params);
				header("Location: /gestion-profesores", TRUE, 302);
				exit();

			} //validationErrors=0
		} else {
			$sql = "select titulo, otorgadopor,anio, concat(titulo, '~**~', otorgadopor, '~**~',anio) raw from profesorestudios p";
			$sql.= " WHERE p.idpersona=" . $dataIdPersona;
			$newCertificados = $db->getSQLArray($sql);

		}
		$viewDataCertificados = array();

		//cargo el menu del  usuario (por rol)
		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		//cargo arrays para la vista
		$sql = "SELECT p.apellido, p.nombre, p.tipodoc, p.documentonro, p.cuil, p.telefono1, p.telefono2, p.estadocivil, p.direccion, p.numero, p.piso, p.depto, p.idlocalidad, p.codpostal, date_format(p.fechanac, '%d/%m/%Y') fnac_dmy, p.nacionalidad, p.sexo, p.entrecalles, p.hijos, p.familiaresacargo";
		$sql.= ", pr.titulohabilitante, pr.otorgadopor, pr.anio, pr.nroregistro, date_format(pr.fechaingcolegio, '%d/%m/%Y') fechaingcolegio, date_format(pr.fechaingdocencia, '%d/%m/%Y') fechaingdocencia, date_format(pr.fechaingresodea, '%d/%m/%Y') fechaingdea, pr.antiguedadafecha, modulostitulares";
		$sql.= ", fm.contactoemergencia, fm.enfermedades, fm.obrasocial, fm.telefono";
		$sql.= ", u.email, u.primeracceso, u.usuario";
		$sql.= ", l.idprovincia";
		$sql.= " FROM personas p";
		$sql.= " INNER JOIN profesores pr on pr.idpersona=p.idpersona";
		$sql.= " LEFT JOIN fichamedica fm on fm.idpersona=p.idpersona";
		$sql.= " INNER JOIN usuarios u on u.idpersona=p.idpersona";
		$sql.= " LEFT JOIN localidades l on l.id=p.idlocalidad";
		$sql.= " WHERE p.idpersona=" . $dataIdPersona;
		$viewData = $db->getSQLArray($sql);

	    $sql = "SELECT id,descripcion FROM tipodoc order by id";
		$viewDataTipoDocumento = $db->getSQLArray($sql);

		$sql = "select id,descripcion from provincias";
		$viewDataProvincias = $db->getSQLArray($sql);

		$idprov = (isset($_POST['provincia']) ? $_POST['provincia'] : $viewData[0]['idprovincia']);
		if ($idprov=="") $idprov=0;
		$sql = "select id,descripcion,cp from localidades where idprovincia=" . $idprov . " order by descripcion";
		$viewDataLocalidades = $db->getSQLArray($sql);

		$sql = "SELECT cp.situacionrevista_id sr, count(0) q";
		$sql.= " FROM comisiones c";
		$sql.= " INNER JOIN comprofesor cp on cp.idcomision=c.idcomision";
		$sql.= " WHERE c.estado=1";
		$sql.= " AND cp.idpersona=" . $dataIdPersona;
		$sql.= " GROUP BY cp.situacionrevista_id";
		$arr = $db->getSQLArray($sql);
		$modTit = 0;
		$modPro = 0;
		$modSup = 0;
		foreach ($arr as $rec) {
			if ($rec['sr'] == $this->POROTO->Config['profesor_situacion_revista_titular']) $modTit = $rec['q'];
			if ($rec['sr'] == $this->POROTO->Config['profesor_situacion_revista_suplente']) $modSup = $rec['q'];
			if ($rec['sr'] == $this->POROTO->Config['profesor_situacion_revista_provisional']) $modPro = $rec['q'];
		}

		$db->dbDisconnect();
		$viewDataSexo = $this->POROTO->Config['dominios']['sexo'];
		$viewDataNacionalidad = $this->POROTO->Config['dominios']['nacionalidad'];
		$viewDataEstadoCivil = $this->POROTO->Config['dominios']['estadocivil'];

		$status = "modificar";
		$pageTitle="Modificar Profesor";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/ver-profesor.php");
		include($this->POROTO->ViewPath . "/-footer.php");

	}

	public function crear() {
		$validationErrors = array();
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Profesores Agregar o Modificar Profesor')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /gestion-profesores", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		$db->dbConnect("profesores/crear");
		if (isset($_POST['apellido'])) {
			$dataApellido = mb_strtoupper($db->dbEscape(trim($_POST['apellido'])), 'UTF-8');
			$dataNombre = mb_strtoupper($db->dbEscape(trim($_POST['nombre'])), 'UTF-8');
			$dataNacionalidad = $db->dbEscape($_POST['nacionalidad']);
			$dataFNac = $db->dbEscape($_POST['fnac']);
			$dataTipDoc = $db->dbEscape($_POST['tipdoc']);
			$dataNroDoc = $db->dbEscape(intval($_POST['nrodoc']));
			$dataTel1 = $db->dbEscape(trim($_POST['telefono1']));
			$dataTel2 = $db->dbEscape(trim($_POST['telefono2']));
			$dataSexo = $db->dbEscape($_POST['sexo']);
			$dataECiv = $db->dbEscape($_POST['estciv']);
			$dataHijos = mb_strtoupper($db->dbEscape(trim($_POST['hijos'])), 'UTF-8');
			$dataFamiliares = mb_strtoupper($db->dbEscape(trim($_POST['familiares'])), 'UTF-8');
			$dataCuil = $db->dbEscape($_POST['cuil']);

			$dataCalle = mb_strtoupper($db->dbEscape(trim($_POST['direccion'])), 'UTF-8');
			$dataNro   = mb_strtoupper($db->dbEscape(trim($_POST['numero'])), 'UTF-8');
			$dataEntre = mb_strtoupper($db->dbEscape(trim($_POST['entrecalles'])), 'UTF-8');
			$dataProv  = $db->dbEscape(trim($_POST['provincia']));
			$dataLocal = $db->dbEscape($_POST['localidad']);
			$dataPiso  = mb_strtoupper($db->dbEscape(trim($_POST['piso'])), 'UTF-8');
			$dataDepto = mb_strtoupper($db->dbEscape(trim($_POST['depto'])), 'UTF-8');
			$dataCP    = mb_strtoupper($db->dbEscape(trim($_POST['codpostal'])), 'UTF-8');

			$dataTitulo = mb_strtoupper($db->dbEscape(trim($_POST['titulo'])), 'UTF-8');
			$dataNroRegistro = mb_strtoupper($db->dbEscape(trim($_POST['nroregistro'])), 'UTF-8');
			$dataOtorga = mb_strtoupper($db->dbEscape(trim($_POST['otorgadopor'])), 'UTF-8');
			$dataAnoEgr = $db->dbEscape($_POST['anoegreso']);

			$dataOSoc = mb_strtoupper($db->dbEscape(trim($_POST['obrasocial'])), 'UTF-8');
			$dataCont = mb_strtoupper($db->dbEscape(trim($_POST['contactoemergencia'])), 'UTF-8');
			$dataTele = $db->dbEscape(trim($_POST['telefono']));
			$dataEnfe = mb_strtoupper($db->dbEscape(trim($_POST['enfermedades'])), 'UTF-8');
			
			$dataEMail = $db->dbEscape(trim($_POST['email']));
			$dataPass1 = $db->dbEscape(trim($_POST['password1']));
			$dataPass2 = $db->dbEscape(trim($_POST['password2']));

			$dataAntiguedad = $db->dbEscape(trim($_POST['antiguedad']));
			$dataFIngCol = $db->dbEscape($_POST['ingcol']);
			$dataFIngDoc = $db->dbEscape($_POST['ingdoc']);
			$dataFIngDea = $db->dbEscape($_POST['ingdea']);
			$dataFIngDea = $db->dbEscape($_POST['ingdea']);
			if (isset($_POST['modulostitulares'])) $dataModulosTitulares = $db->dbEscape($_POST['modulostitulares']); else $dataModulosTitulares = "";
			
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
			//tipdoc. obligatorio, distinto de 0
			if ($dataTipDoc=="0" || $dataTipDoc=="") $validationErrors['tipdoc'] = "El campo Tipo Documento es obligatorio";
			//nrodoc. obligatorio
			if ($dataNroDoc==0) $validationErrors['nrodoc'] = "El campo Número Documento es obligatorio";
			if (!is_numeric($_POST['nrodoc'])) $validationErrors['nrodoc'] = "El campo Número de Documento no es numérico";
			// telefono fijo obligatorio. entre 6 y 20 caracteres. solo numeros, espacios, parentesis y guion
			if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTel1)) $validationErrors['telefono1'] = "El Teléfono Fijo contiene caracteres inválidos";
			if (strlen($dataTel1) < 6 || strlen($dataTel1) > 20) $validationErrors['telefono1'] = "El Teléfono Fijo es obligatorio y requiere entre 6 y 20 caracteres";
			// telefono celular. si no es blanco, debe contener entre 6 y 20 caracteres. solo numeros, espacios, parentesis y guion
			if ($dataTel2!="") {
				if (strlen($dataTel2) < 6 || strlen($dataTel2) > 20) $validationErrors['telefono2'] = "El Teléfono Celular debe contener entre 6 y 20 caracteres";
				if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTel2)) $validationErrors['telefono2'] = "El Teléfono Celular contiene caracteres inválidos";
			}
			// sexo 		seteado y mayor a 0
			if ($dataSexo=="0" || $dataSexo=="") $validationErrors['sexo'] = "El campo Sexo es obligatorio";
			// estado civil	seteado y mayor a 0
			if ($dataECiv=="0" || $dataECiv=="") $validationErrors['estciv'] = "El Estado Civil es obligatorio";
			// hijos 		opcional. maxlength 45
			if (strlen($dataHijos)>45) $validationErrors['hijos'] = "El campo Hijos puede contener como máximo 45 caracteres";
			// familiares 		opcional. maxlength 45
			if (strlen($dataFamiliares)>45) $validationErrors['familiares'] = "El campo Familiares a Cargo puede contener como máximo 45 caracteres";	
			// cuil 			opcional. solo numeros
			if ($dataCuil !="") if (! is_numeric($dataCuil)) $validationErrors['cuil'] = "El campo Cuil contiene caracteres inválidos";

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

			// titulo 			obligatorio. maxlength 150
			if ($dataTitulo=="") $validationErrors['titulo'] = "El Título es obligatorio";
			if (strlen($dataTitulo)>150) $validationErrors['titulo'] = "El campo Título puede contener como máximo 150 caracteres";
			// dataNroRegistro 	obligatorio. maxlength 45
			if ($dataNroRegistro=="") $validationErrors['nroregistro'] = "El Nº Registro es obligatorio";
			if (strlen($dataNroRegistro)>45) $validationErrors['nroregistro'] = "El campo Nº Registro puede contener como máximo 45 caracteres";
			// otorgado por 	obligatorio. maxlength 150
			if ($dataOtorga=="") $validationErrors['otorgadopor'] = "El campo Otorgado Por es obligatorio";
			if (strlen($dataOtorga)>150) $validationErrors['otorgadopor'] = "El campo Otorgado Por puede contener como máximo 150 caracteres";
			// ano egreso 		obligatorio. numerico. entre 1930 y el anio actual
			if ($dataAnoEgr=="") {
				$validationErrors['anoegreso'] = "El campo Año Egreso es obligatorio";
			} else {
				if (strval(intval($dataAnoEgr)) != $dataAnoEgr) {
					$validationErrors['anoegreso'] = "El campo Año Egreso contiene caracteres inválidos";
				} else {
					if (intval($dataAnoEgr)<1930) $validationErrors['anoegreso'] = "El campo Año Egreso no puede ser anterior a 1930";
					if (intval($dataAnoEgr)>date("Y")) $validationErrors['anoegreso'] = "El campo Año Egreso no puede ser posterior al año actual";
				}
			}

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
			if ($dataPass1=="") { 
				$validationErrors['password1'] = "El campo Contraseña es obligatorio";
			} else {
				if (! $lib->isPasswordValid($dataPass1)) $validationErrors['password1'] = "Contraseña inválida. " . $this->POROTO->Config['password_constraints_explained'];

				if (strlen($dataPass1) > 45) {
					$validationErrors['password1'] = "El campo Contraseña puede contener como máximo 45 caracteres";
				} else {
					if ($dataPass1 != $dataPass2) {
						$validationErrors['password2'] = "El campo Contraseña no coincide con su validación";
					}
				}
			}

			//antiguedad a 1/3/16	obligatorio. numerico entre 0 y 99
			if ($dataAntiguedad=="") {
				$validationErrors['antiguedad'] = "El campo Antiguedad Docente al 01/03/2016 es obligatorio";
			} else {
				if (intval($dataAntiguedad) != $dataAntiguedad) {
					$validationErrors['antiguedad'] = "El campo Antiguedad Docente al 01/03/2016 contiene caracteres inválidos";
				} else {
					if (intval($dataAntiguedad)<0) $validationErrors['antiguedad'] = "El campo Antiguedad Docente al 01/03/2016 no puede ser menor a 0";
					if (intval($dataAntiguedad)>99) $validationErrors['antiguedad'] = "El campo Antiguedad Docente al 01/03/2016 no puede ser mayor a 99";
				}
			}
			//fecha ingreso colegio		obligatoria. no mayor a 100 anos ni futura
			if (!$lib->validateDate($dataFIngCol)) {
				$validationErrors['ingcol'] = "La Fecha Ingreso a la Escuela es inválida";
			} else {
		    	$d = $lib->datediff($dataFIngCol);
		    	if ($d<0) $validationErrors['ingcol'] = "La Fecha Ingreso a la Escuela es inválida (futura)";
		    	if ($d > (365*100)) $validationErrors['ingcol'] = "La Fecha Ingreso a la Escuela es inválida (>100)";
			}
			//fecha ingreso docencia	obligatoria. no mayor a 100 anos ni futura
			if (!$lib->validateDate($dataFIngDoc)) {
				$validationErrors['ingdoc'] = "La Fecha Ingreso a la Docencia Provincial es inválida";
			} else {
		    	$d = $lib->datediff($dataFIngDoc);
		    	if ($d<0) $validationErrors['ingdoc'] = "La Fecha Ingreso a la Docencia Provincial es inválida (futura)";
		    	if ($d > (365*100)) $validationErrors['ingdoc'] = "La Fecha Ingreso a la Docencia Provincial es inválida (>100)";
			}
			//fecha ingreso dea		obligatoria. no mayor a 100 anos ni futura
			if (!$lib->validateDate($dataFIngDea)) {
				$validationErrors['ingdea'] = "La Fecha Ingreso a la DEA es inválida";
			} else {
		    	$d = $lib->datediff($dataFIngDea);
		    	if ($d<0) $validationErrors['ingdea'] = "La Fecha Ingreso a la DEA es inválida (futura)";
		    	if ($d > (365*100)) $validationErrors['ingdea'] = "La Fecha Ingreso a la DEA es inválida (>100)";
			}
			//modulostitulares checkbox sin validaciones


			// CERTIFICADOS. sin validaciones
			$nc = (isset($_POST['new-certificados']) ? $_POST['new-certificados'] : array());

			//me fijo que el tipo y numero de documento no esten registrados ya por otra persona
			if (count($validationErrors)==0) {
				$sql = "select idpersona from personas where tipodoc=" . $dataTipDoc . " and documentonro=" . $dataNroDoc;
				$valdoc = $db->getSQLArray($sql);
				if (count($valdoc)!= 0) $validationErrors['nrodoc'] = "El tipo y número de documento ingresados ya existen en la base";
			}

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


			if (count($validationErrors)==0) {
				$sqlP = "INSERT INTO personas (legajo,apellido,nombre,tipodoc,documentonro,";
				$sqlP.= "cuil,telefono1,telefono2,estadocivil,direccion,numero,";
				$sqlP.= "piso,depto,idLocalidad,codpostal,fechanac,nacionalidad,";
				$sqlP.= "sexo,entrecalles,hijos,familiaresacargo,socio,estado,";
				$sqlP.= "usucrea,fechacrea,usumodi,fechamodi) SELECT ";
				$sqlP.= $dataNroDoc;
				$sqlP.= ",'" . $dataApellido . "'";
				$sqlP.= ",'" . $dataNombre . "'";
				$sqlP.= "," . $dataTipDoc;
				$sqlP.= "," . $dataNroDoc;
				if ($dataCuil=="") $sqlP.= ",null" ; else $sqlP.= "," . $dataCuil;
				$sqlP.= ",'" . $dataTel1 . "'";
				$sqlP.= ",'" . $dataTel2 . "'";
				$sqlP.= ",'" . $dataECiv . "'";
				$sqlP.= ",'" . $dataCalle . "'";
				$sqlP.= ",'" . $dataNro . "'";
				$sqlP.= ",'" . $dataPiso . "'";
				$sqlP.= ",'" . $dataDepto . "'";
				$sqlP.= "," . $dataLocal;
				$sqlP.= ",'" . $dataCP . "'";
				$sqlP.= ",'" . $lib->dateDMY2YMD($dataFNac) . "'";
				$sqlP.= ",'" . $dataNacionalidad . "'";
				$sqlP.= ",'" . $dataSexo . "'";
				$sqlP.= ",'" . $dataEntre . "'";
				$sqlP.= ",'" . $dataHijos . "'";
				$sqlP.= ",'" . $dataFamiliares . "'";
				$sqlP.= ",null";
				$sqlP.= ",1";
				$sqlP.= ",'" . $ses->getUsuario() . "'";
				$sqlP.= ",CURRENT_TIMESTAMP";
				$sqlP.= ",null";
				$sqlP.= ",null";

				$db->begintrans();
				$newIdPersona = $db->insert($sqlP, '', true);
				$bOk = $newIdPersona;

				$sqlA = "INSERT INTO profesores (idpersona, titulohabilitante, otorgadopor, anio, nroregistro, fechaingcolegio";
				$sqlA.= ", fechaingdocencia, fechaingresodea, antiguedadafecha, modulostitulares, usucrea, fechacrea, usumodi, fechamodi) SELECT ";
				$sqlA.= $newIdPersona;
				$sqlA.= ",'" . $dataTitulo . "'";
				$sqlA.= ",'" . $dataOtorga . "'";
				$sqlA.= "," . $dataAnoEgr;
				$sqlA.= ",'" . $dataNroRegistro . "'";
				$sqlA.= ",'" . $lib->dateDMY2YMD($dataFIngCol) . "'";
				$sqlA.= ",'" . $lib->dateDMY2YMD($dataFIngDoc) . "'";
				$sqlA.= ",'" . $lib->dateDMY2YMD($dataFIngDea) . "'";
				$sqlA.= "," . $dataAntiguedad;
				$sqlA.= "," . ($dataModulosTitulares == "modulostitulares" ? "1" : "0");
				$sqlA.= ",'" . $ses->getUsuario() . "'";
				$sqlA.= ",CURRENT_TIMESTAMP ";
				$sqlA.= ",NULL ";
				$sqlA.= ",NULL ";
				if ($bOk!==false) $bOk = $db->insert($sqlA, '', true);

				$sqlF = "INSERT INTO fichamedica (idpersona,obrasocial,contactoemergencia,telefono,enfermedades) SELECT ";
				$sqlF.= $newIdPersona;
				$sqlF.= ",'" . $dataOSoc . "'";
				$sqlF.= ",'" . $dataCont . "'";
				$sqlF.= ",'" . $dataTele . "'";
				$sqlF.= ",'" . $dataEnfe . "'";
				if ($bOk!==false) $bOk = $db->insert($sqlF, '', true);

				$sqlU = "INSERT INTO usuarios (idpersona,usuario,email,password,estado,";
				$sqlU.= "primeracceso,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
				$sqlU.= $newIdPersona;
				$sqlU.= ",'" . $userName . "'";
				$sqlU.= ",'" . $dataEMail . "'";
				$sqlU.= ",'" . $dataPass1 . "'";
				$sqlU.= ",0";
				$sqlU.= ",1";
				$sqlU.= ",'" . $ses->getUsuario() . "'";
				$sqlU.= ",CURRENT_TIMESTAMP";
				$sqlU.= ",null";
				$sqlU.= ",null";
				if ($bOk!==false) $bOk = $db->update($sqlU, '', true);

				$sqlPR = "INSERT INTO personarol (idpersona,idrol,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
				$sqlPR.= $newIdPersona;
				$sqlPR.= "," . $this->POROTO->Config['rol_profesor_id'];
				$sqlPR.= ",'" . $ses->getUsuario() . "'";
				$sqlPR.= ",CURRENT_TIMESTAMP";
				$sqlPR.= ",null";
				$sqlPR.= ",null";
				if ($bOk!==false) $bOk = $db->insert($sqlPR, '', true);


				$sqlC = array();
				foreach ($nc as $certificado) {
					$a = explode("~**~", $certificado);
					if (count($a)==3) { //vienen: titulo, otorgado, anio
						$sql = "INSERT INTO profesorestudios (idpersona, titulo, otorgadopor, anio) SELECT ";
						$sql.= $newIdPersona;
						$sql.= ",'" . $a[0] . "'";
						$sql.= ",'" . $a[1] . "'";
						$sql.= "," . $a[2];
						$sqlC[] = $sql;
					}
				}

				foreach ($sqlC as $sqlCertificado) {
					if ($bOk!==false) $bOk = $db->insert($sqlCertificado, '', true);
				}
				if ($bOk === false) {
					$db->rollback();
					$ses->setMessage("Se produjo un error realizando la creación", SessionMessageType::TransactionError);
					header("Location: /gestion-profesores", TRUE, 302);
					exit();

				} else {
					$db->commit();
				}
				$db->dbDisconnect();

				$ses->setMessage("Profesor creado con éxito", SessionMessageType::Success);
				header("Location: /gestion-profesores", TRUE, 302);
				exit();

			} //validationErrors=0
		}

		//cargo el menu del  usuario (por rol)
		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		//cargo arrays para la vista

		// $sql = "select nombre from materias where idmateria=" . $dataIdMateria;
		// $arr = $db->getSQLArray($sql);
		// $dataDescripcionMateria = $arr[0]['nombre'];

	    $sql = "SELECT id,descripcion FROM tipodoc order by id";
		$viewDataTipoDocumento = $db->getSQLArray($sql);

		$sql = "select id,descripcion from provincias";
		$viewDataProvincias = $db->getSQLArray($sql);

		$idprov = (isset($_POST['provincia']) ? $_POST['provincia'] : "");
		if ($idprov=="") $idprov=0;
		$sql = "select id,descripcion,cp from localidades where idprovincia=" . $idprov . " order by descripcion";
		$viewDataLocalidades = $db->getSQLArray($sql);

		$db->dbDisconnect();
		$viewDataSexo = $this->POROTO->Config['dominios']['sexo'];
		$viewDataNacionalidad = $this->POROTO->Config['dominios']['nacionalidad'];
		$viewDataEstadoCivil = $this->POROTO->Config['dominios']['estadocivil'];
		$viewDataCertificados = array();
		$newCertificados = array();

		//como es un crear, pongo los valoreas default en 0 (porque se comparte la pagina con el modificar)
		$viewData = array(array('apellido'=>"",
								'nombre'=>"",
								'nacionalidad'=>"",
								'tipodoc'=>"",
								'documentonro'=>0,
								'fnac_dmy'=>"",
								'telefono1'=>"",
								'telefono2'=>"",
								'sexo'=>"",
								'estadocivil'=>"",
								'hijos'=>"NO",
								'familiaresacargo'=>"NO",
								'cuil'=>"",
								'direccion'=>"",
								'entrecalles'=>"", 
								'numero'=>"", 
								'piso'=>"", 
								'depto'=>"", 
								'idlocalidad'=>"", 
								'localidad_descripcion'=>"", 
								'idprovincia'=> "", 
								'codpostal'=> "",
								'titulohabilitante'=> "",
								'nroregistro'=> "",
								'titulohabilitante'=> "",
								'nroregistro'=> "",
								'otorgadopor'=> "",
								'anio'=> "",
								'obrasocial'=> "", 'contactoemergencia'=> "",
								'telefono'=> "", 'enfermedades'=> "",
								'antiguedadafecha'=> "",
								'fechaingcolegio'=> "",
								'fechaingdocencia'=> "",
								'fechaingdea'=> "",
								'modulostitulares'=> "",
								'usuario'=> "",
								'email'=> "",
								'primeracceso'=> "1",


			        ));
		$modTit = 0;
		$modPro = 0;
		$modSup = 0;

		$status = "crear";
		$pageTitle="Crear Profesor";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/ver-profesor.php");
		include($this->POROTO->ViewPath . "/-footer.php");

	}

 	public function misdatos() {
		$validationErrors = array();
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Ver Datos')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		$db->dbConnect("profesores/misdatos");
		$newCertificados = array();
		$dataIdPersona = $ses->getIdPersona();

		if (isset($_POST['apellido'])) {
			$dataApellido = mb_strtoupper($db->dbEscape(trim($_POST['apellido'])), 'UTF-8');
			$dataNombre = mb_strtoupper($db->dbEscape(trim($_POST['nombre'])), 'UTF-8');
			$dataNacionalidad = $db->dbEscape($_POST['nacionalidad']);
			$dataFNac = $db->dbEscape($_POST['fnac']);
			$dataTel1 = $db->dbEscape(trim($_POST['telefono1']));
			$dataTel2 = $db->dbEscape(trim($_POST['telefono2']));
			$dataSexo = $db->dbEscape($_POST['sexo']);
			$dataECiv = $db->dbEscape($_POST['estciv']);
			$dataHijos = mb_strtoupper($db->dbEscape(trim($_POST['hijos'])), 'UTF-8');
			$dataFamiliares = mb_strtoupper($db->dbEscape(trim($_POST['familiares'])), 'UTF-8');
			$dataCuil = $db->dbEscape($_POST['cuil']);

			$dataCalle = mb_strtoupper($db->dbEscape(trim($_POST['direccion'])), 'UTF-8');
			$dataNro   = mb_strtoupper($db->dbEscape(trim($_POST['numero'])), 'UTF-8');
			$dataEntre = mb_strtoupper($db->dbEscape(trim($_POST['entrecalles'])), 'UTF-8');
			$dataProv  = $db->dbEscape(trim($_POST['provincia']));
			$dataLocal = $db->dbEscape($_POST['localidad']);
			$dataPiso  = mb_strtoupper($db->dbEscape(trim($_POST['piso'])), 'UTF-8');
			$dataDepto = mb_strtoupper($db->dbEscape(trim($_POST['depto'])), 'UTF-8');
			$dataCP    = mb_strtoupper($db->dbEscape(trim($_POST['codpostal'])), 'UTF-8');

			$dataTitulo = mb_strtoupper($db->dbEscape(trim($_POST['titulo'])), 'UTF-8');
			$dataNroRegistro = mb_strtoupper($db->dbEscape(trim($_POST['nroregistro'])), 'UTF-8');
			$dataOtorga = mb_strtoupper($db->dbEscape(trim($_POST['otorgadopor'])), 'UTF-8');
			$dataAnoEgr = $db->dbEscape($_POST['anoegreso']);

			$dataOSoc = mb_strtoupper($db->dbEscape(trim($_POST['obrasocial'])), 'UTF-8');
			$dataCont = mb_strtoupper($db->dbEscape(trim($_POST['contactoemergencia'])), 'UTF-8');
			$dataTele = $db->dbEscape(trim($_POST['telefono']));
			$dataEnfe = mb_strtoupper($db->dbEscape(trim($_POST['enfermedades'])), 'UTF-8');
			
			$dataEMail = $db->dbEscape(trim($_POST['email']));
			$dataPass1 = $db->dbEscape(trim($_POST['password1']));
			$dataPass2 = $db->dbEscape(trim($_POST['password2']));

			$dataAntiguedad = $db->dbEscape(trim($_POST['antiguedad']));
			$dataFIngCol = $db->dbEscape($_POST['ingcol']);
			$dataFIngDoc = $db->dbEscape($_POST['ingdoc']);
			$dataFIngDea = $db->dbEscape($_POST['ingdea']);
			if (isset($_POST['modulostitulares'])) $dataModulosTitulares = $db->dbEscape($_POST['modulostitulares']); else $dataModulosTitulares = "";
			
			
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
			// telefono fijo obligatorio. entre 6 y 20 caracteres. solo numeros, espacios, parentesis y guion
			if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTel1)) $validationErrors['telefono1'] = "El Teléfono Fijo contiene caracteres inválidos";
			if (strlen($dataTel1) < 6 || strlen($dataTel1) > 20) $validationErrors['telefono1'] = "El Teléfono Fijo es obligatorio y requiere entre 6 y 20 caracteres";
			// telefono celular. si no es blanco, debe contener entre 6 y 20 caracteres. solo numeros, espacios, parentesis y guion
			if ($dataTel2!="") {
				if (strlen($dataTel2) < 6 || strlen($dataTel2) > 20) $validationErrors['telefono2'] = "El Teléfono Celular debe contener entre 6 y 20 caracteres";
				if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTel2)) $validationErrors['telefono2'] = "El Teléfono Celular contiene caracteres inválidos";
			}
			// sexo 		seteado y mayor a 0
			if ($dataSexo=="0" || $dataSexo=="") $validationErrors['sexo'] = "El campo Sexo es obligatorio";
			// estado civil	seteado y mayor a 0
			if ($dataECiv=="0" || $dataECiv=="") $validationErrors['estciv'] = "El Estado Civil es obligatorio";
			// hijos 		opcional. maxlength 45
			if (strlen($dataHijos)>45) $validationErrors['hijos'] = "El campo Hijos puede contener como máximo 45 caracteres";
			// familiares 		opcional. maxlength 45
			if (strlen($dataFamiliares)>45) $validationErrors['familiares'] = "El campo Familiares a Cargo puede contener como máximo 45 caracteres";	
			// cuil 			opcional. solo numeros
			if ($dataCuil !="") if (! is_numeric($dataCuil)) $validationErrors['cuil'] = "El campo Cuil contiene caracteres inválidos";

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

			// titulo 			opcional. maxlength 150
			// if ($dataTitulo=="") $validationErrors['titulo'] = "El Título es obligatorio";
			if (strlen($dataTitulo)>150) $validationErrors['titulo'] = "El campo Título puede contener como máximo 150 caracteres";
			// dataNroRegistro 	opcional. maxlength 45
			// if ($dataNroRegistro=="") $validationErrors['nroregistro'] = "El Nº Registro es obligatorio";
			if (strlen($dataNroRegistro)>45) $validationErrors['nroregistro'] = "El campo Nº Registro puede contener como máximo 45 caracteres";
			// otorgado por 	opcional. maxlength 150
			// if ($dataOtorga=="") $validationErrors['otorgadopor'] = "El campo Otorgado Por es obligatorio";
			if (strlen($dataOtorga)>150) $validationErrors['otorgadopor'] = "El campo Otorgado Por puede contener como máximo 150 caracteres";
			// ano egreso 		opcional. numerico. entre 1930 y el anio actual
			if ($dataAnoEgr=="") {
				// $validationErrors['anoegreso'] = "El campo Año Egreso es obligatorio";
			} else {
				if (strval(intval($dataAnoEgr)) != $dataAnoEgr) {
					$validationErrors['anoegreso'] = "El campo Año Egreso contiene caracteres inválidos";
				} else {
					if (intval($dataAnoEgr)<1930) $validationErrors['anoegreso'] = "El campo Año Egreso no puede ser anterior a 1930";
					if (intval($dataAnoEgr)>date("Y")) $validationErrors['anoegreso'] = "El campo Año Egreso no puede ser posterior al año actual";
				}
			}

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
			if ($dataPass1=="") { 
				// $validationErrors['password1'] = "El campo Contraseña es obligatorio";
			} else {
				if (! $lib->isPasswordValid($dataPass1)) $validationErrors['password1'] = "Contraseña inválida. " . $this->POROTO->Config['password_constraints_explained'];

				if (strlen($dataPass1) > 45) {
					$validationErrors['password1'] = "El campo Contraseña puede contener como máximo 45 caracteres";
				} else {
					if ($dataPass1 != $dataPass2) {
						$validationErrors['password2'] = "El campo Contraseña no coincide con su validación";
					}
				}
			}

			//antiguedad a 1/3/16	obligatorio. numerico entre 0 y 99
			if ($dataAntiguedad=="") {
				$validationErrors['antiguedad'] = "El campo Antiguedad Docente al 01/03/2016 es obligatorio";
			} else {
				if (intval($dataAntiguedad) != $dataAntiguedad) {
					$validationErrors['antiguedad'] = "El campo Antiguedad Docente al 01/03/2016 contiene caracteres inválidos";
				} else {
					if (intval($dataAntiguedad)<0) $validationErrors['antiguedad'] = "El campo Antiguedad Docente al 01/03/2016 no puede ser menor a 0";
					if (intval($dataAntiguedad)>99) $validationErrors['antiguedad'] = "El campo Antiguedad Docente al 01/03/2016 no puede ser mayor a 99";
				}
			}
			//fecha ingreso colegio		obligatoria. no mayor a 100 anos ni futura
			if (!$lib->validateDate($dataFIngCol)) {
				$validationErrors['ingcol'] = "La Fecha Ingreso a la Escuela es inválida";
			} else {
		    	$d = $lib->datediff($dataFIngCol);
		    	if ($d<0) $validationErrors['ingcol'] = "La Fecha Ingreso a la Escuela es inválida (futura)";
		    	if ($d > (365*100)) $validationErrors['ingcol'] = "La Fecha Ingreso a la Escuela es inválida (>100)";
			}
			//fecha ingreso docencia	obligatoria. no mayor a 100 anos ni futura
			if (!$lib->validateDate($dataFIngDoc)) {
				$validationErrors['ingdoc'] = "La Fecha Ingreso a la Docencia Provincial es inválida";
			} else {
		    	$d = $lib->datediff($dataFIngDoc);
		    	if ($d<0) $validationErrors['ingdoc'] = "La Fecha Ingreso a la Docencia Provincial es inválida (futura)";
		    	if ($d > (365*100)) $validationErrors['ingdoc'] = "La Fecha Ingreso a la Docencia Provincial es inválida (>100)";
			}
			//fecha ingreso dea		obligatoria. no mayor a 100 anos ni futura
			if (!$lib->validateDate($dataFIngDea)) {
				$validationErrors['ingdea'] = "La Fecha Ingreso a la DEA es inválida";
			} else {
		    	$d = $lib->datediff($dataFIngDea);
		    	if ($d<0) $validationErrors['ingdea'] = "La Fecha Ingreso a la DEA es inválida (futura)";
		    	if ($d > (365*100)) $validationErrors['ingdea'] = "La Fecha Ingreso a la DEA es inválida (>100)";
			}
			//modulostitulares checkbox sin validaciones


			// CERTIFICADOS. sin validaciones
			$nc = (isset($_POST['new-certificados']) ? $_POST['new-certificados'] : array());
			foreach ($nc as $certificado) {
				$a = explode("~**~", $certificado);
				if (count($a)==3) { //vienen: titulo, otorgado, anio
					$newCertificados[] = array( 'titulo'=>$a[0],
												'otorgadopor'=>$a[1],
												'anio'=>$a[2],
												'raw'=>$a[0] . "~**~" . $a[1] . "~**~" . $a[2],
												);
				}
			}



			if (count($validationErrors)==0) {
				$sqlP = "UPDATE personas SET";
				$sqlP.= " apellido='" . $dataApellido . "'";
				$sqlP.= ",nombre='" . $dataNombre . "'";
				if ($dataCuil=="") $sqlP.= ",cuil=null" ; else $sqlP.= ",cuil=" . $dataCuil;
				$sqlP.= ",telefono1='" . $dataTel1 . "'";
				$sqlP.= ",telefono2='" . $dataTel2 . "'";
				$sqlP.= ",estadocivil='" . $dataECiv . "'";
				$sqlP.= ",direccion='" . $dataCalle . "'";
				$sqlP.= ",numero='" . $dataNro . "'";
				$sqlP.= ",piso='" . $dataPiso . "'";
				$sqlP.= ",depto='" . $dataDepto . "'";
				$sqlP.= ",idLocalidad=" . $dataLocal;
				$sqlP.= ",codpostal='" . $dataCP . "'";
				$sqlP.= ",fechanac='" . $lib->dateDMY2YMD($dataFNac) . "'";
				$sqlP.= ",nacionalidad='" . $dataNacionalidad . "'";
				$sqlP.= ",sexo='" . $dataSexo . "'";
				$sqlP.= ",entrecalles='" . $dataEntre . "'";
				$sqlP.= ",hijos='" . $dataHijos . "'";
				$sqlP.= ",familiaresacargo='" . $dataFamiliares . "'";
				$sqlP.= ",usumodi='" . $ses->getUsuario() . "'";
				$sqlP.= ",fechamodi=CURRENT_TIMESTAMP";
				$sqlP.= " WHERE idpersona=" . $dataIdPersona;

				$db->begintrans();
				$bOk = $db->update($sqlP, '', true);

				$sqlA = "UPDATE profesores SET ";
				$sqlA.= " titulohabilitante='" . $dataTitulo . "'";
				$sqlA.= ",otorgadopor='" . $dataOtorga . "'";
				if ($dataAnoEgr != "") $sqlA.= ",anio=" . $dataAnoEgr; else $sqlA.= ",anio=null";
				$sqlA.= ",nroregistro='" . $dataNroRegistro . "'";
				$sqlA.= ",fechaingcolegio='" . $lib->dateDMY2YMD($dataFIngCol) . "'";
				$sqlA.= ",fechaingdocencia='" . $lib->dateDMY2YMD($dataFIngDoc) . "'";
				$sqlA.= ",fechaingresodea='" . $lib->dateDMY2YMD($dataFIngDea) . "'";
				$sqlA.= ",antiguedadafecha=" . $dataAntiguedad;
				$sqlA.= ",modulostitulares=" . ($dataModulosTitulares == "modulostitulares" ? "1" : "0");
				$sqlA.= ",usumodi='" . $ses->getUsuario() . "'";
				$sqlA.= ",fechamodi=CURRENT_TIMESTAMP";
				$sqlA.= " WHERE idpersona=" . $dataIdPersona;
				if ($bOk!==false) $bOk = $db->update($sqlA, '', true);

				$sqlF = "INSERT INTO fichamedica (idpersona,obrasocial,contactoemergencia,telefono,enfermedades) SELECT ";
				$sqlF.= " $dataIdPersona";
				$sqlF.= ",'" . $dataOSoc . "'";
				$sqlF.= ",'" . $dataCont . "'";
				$sqlF.= ",'" . $dataTele . "'";
				$sqlF.= ",'" . $dataEnfe . "'";
				$sqlF.= " ON DUPLICATE KEY UPDATE obrasocial='" . $dataOSoc . "'";
				$sqlF.= ",contactoemergencia='" . $dataCont . "'";
				$sqlF.= ",telefono='" . $dataTele . "'";
				$sqlF.= ",enfermedades='" . $dataEnfe . "'";
				if ($bOk!==false) $bOk = $db->update($sqlF, '', true);


				$sqlU = "UPDATE usuarios SET ";
				$sqlU.= " email='" . $dataEMail . "'";
				if ($dataPass1 != "") $sqlU.= ",password='" . $dataPass1 . "'";
				$sqlU.= ",usumodi='" . $ses->getUsuario() . "'";
				$sqlU.= ",fechamodi=CURRENT_TIMESTAMP";
				$sqlU.= " WHERE idpersona=" . $dataIdPersona;
				if ($bOk!==false) $bOk = $db->update($sqlU, '', true);

				$sql = "DELETE FROM profesorestudios WHERE idpersona=" . $dataIdPersona;
				if ($bOk!==false) $bOk = $db->update($sql, '', true);

				$sqlC = array();
				foreach ($nc as $certificado) {
					$a = explode("~**~", $certificado);
					if (count($a)==3) { //vienen: titulo, otorgado, anio
						$sql = "INSERT INTO profesorestudios (idpersona, titulo, otorgadopor, anio) SELECT ";
						$sql.= $dataIdPersona;
						$sql.= ",'" . $a[0] . "'";
						$sql.= ",'" . $a[1] . "'";
						$sql.= "," . $a[2];
						$sqlC[] = $sql;

					}
				}

				foreach ($sqlC as $sqlCertificado) {
					if ($bOk!==false) $bOk = $db->insert($sqlCertificado, '', true);
				}
				if ($bOk === false) {
					$db->rollback();
					$ses->setMessage("Se produjo un error realizando la modificación", SessionMessageType::TransactionError);
					header("Location: /", TRUE, 302);
					exit();

				} else {
					$db->commit();
				}
				$db->dbDisconnect();

				$ses->setMessage("Datos modificado con éxito", SessionMessageType::Success);
				header("Location: /", TRUE, 302);
				exit();

			} //validationErrors=0
		} else {
			$sql = "select titulo, otorgadopor,anio, concat(titulo, '~**~', otorgadopor, '~**~',anio) raw from profesorestudios p";
			$sql.= " WHERE p.idpersona=" . $dataIdPersona;
			$newCertificados = $db->getSQLArray($sql);

		}
		$viewDataCertificados = array();

		//cargo el menu del  usuario (por rol)
		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		//cargo arrays para la vista
		$sql = "SELECT p.apellido, p.nombre, p.tipodoc, p.documentonro, p.cuil, p.telefono1, p.telefono2, p.estadocivil, p.direccion, p.numero, p.piso, p.depto, p.idlocalidad, p.codpostal, date_format(p.fechanac, '%d/%m/%Y') fnac_dmy, p.nacionalidad, p.sexo, p.entrecalles, p.hijos, p.familiaresacargo";
		$sql.= ", pr.titulohabilitante, pr.otorgadopor, pr.anio, pr.nroregistro, date_format(pr.fechaingcolegio, '%d/%m/%Y') fechaingcolegio, date_format(pr.fechaingdocencia, '%d/%m/%Y') fechaingdocencia, date_format(pr.fechaingresodea, '%d/%m/%Y') fechaingdea, pr.antiguedadafecha, modulostitulares";
		$sql.= ", fm.contactoemergencia, fm.enfermedades, fm.obrasocial, fm.telefono";
		$sql.= ", u.email, u.primeracceso, u.usuario";
		$sql.= ", l.idprovincia";
		$sql.= " FROM personas p";
		$sql.= " INNER JOIN profesores pr on pr.idpersona=p.idpersona";
		$sql.= " LEFT JOIN fichamedica fm on fm.idpersona=p.idpersona";
		$sql.= " INNER JOIN usuarios u on u.idpersona=p.idpersona";
		$sql.= " LEFT JOIN localidades l on l.id=p.idlocalidad";
		$sql.= " WHERE p.idpersona=" . $dataIdPersona;
		$viewData = $db->getSQLArray($sql);

	    $sql = "SELECT id,descripcion FROM tipodoc order by id";
		$viewDataTipoDocumento = $db->getSQLArray($sql);

		$sql = "select id,descripcion from provincias";
		$viewDataProvincias = $db->getSQLArray($sql);

		$idprov = (isset($_POST['provincia']) ? $_POST['provincia'] : $viewData[0]['idprovincia']);
		if ($idprov=="") $idprov=0;
		$sql = "select id,descripcion,cp from localidades where idprovincia=" . $idprov . " order by descripcion";
		$viewDataLocalidades = $db->getSQLArray($sql);

		$sql = "SELECT cp.situacionrevista_id sr, count(0) q";
		$sql.= " FROM comisiones c";
		$sql.= " INNER JOIN comprofesor cp on cp.idcomision=c.idcomision";
		$sql.= " WHERE c.estado=1";
		$sql.= " AND cp.idpersona=" . $dataIdPersona;
		$sql.= " GROUP BY cp.situacionrevista_id";
		$arr = $db->getSQLArray($sql);
		$modTit = 0;
		$modPro = 0;
		$modSup = 0;
		foreach ($arr as $rec) {
			if ($rec['sr'] == $this->POROTO->Config['profesor_situacion_revista_titular']) $modTit = $rec['q'];
			if ($rec['sr'] == $this->POROTO->Config['profesor_situacion_revista_suplente']) $modSup = $rec['q'];
			if ($rec['sr'] == $this->POROTO->Config['profesor_situacion_revista_provisional']) $modPro = $rec['q'];
		}

		$db->dbDisconnect();
		$viewDataSexo = $this->POROTO->Config['dominios']['sexo'];
		$viewDataNacionalidad = $this->POROTO->Config['dominios']['nacionalidad'];
		$viewDataEstadoCivil = $this->POROTO->Config['dominios']['estadocivil'];

		$status = "misdatos";
		$pageTitle="Mis Datos";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/ver-profesor.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}

}