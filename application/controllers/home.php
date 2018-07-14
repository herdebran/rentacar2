<?php
class Home {
	private $POROTO;
        
	function __construct($poroto) {
		$this->POROTO=$poroto;
		$this->POROTO->pageHeader[] = array("label"=>"Dashboard","url"=>"");
       
	}

	function defentry() {
		if ($this->POROTO->Session->isLogged()) {
            $this->menu();
		} else {
			include($this->POROTO->ViewPath . "/-login.php");
		}
	}

	function forgot() {
		//TODO: todo this
		die ("to be implemented soon");
	}

	function menu() { //OK
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		$db->dbConnect("home/menu");

		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		include($this->POROTO->ViewPath . "/-header.php");

		$sql = "select apellido,nombre,sexo from personas where idpersona=" . $ses->getIdPersona();
		$arrSaludo = $db->getSQLArray($sql);
		$saludo = "";

		if (count($arrSaludo)==1) {
			if ($arrSaludo[0]['sexo'] == 'MASCULINO') { 
				$saludo = "Bienvenido";
			} else if ($arrSaludo[0]['sexo'] == 'FEMENINO') { 
				$saludo = "Bienvenida";
			} else {
				$saludo = "Bienvenido/a";
			}
			$saludo .= " " . $arrSaludo[0]['nombre'] . " " . $arrSaludo[0]['apellido'] . " al sistema de Alumnos.";
		}

		echo $saludo;
		echo "<hr />";
		echo "<h3>Novedades</h3><br>";

		include($this->POROTO->ViewPath . "/-footer.php");
	}

	
        
		
	
	function login() { //OK ojo si sacamos del home ver sitelibrary.
            $navegador = substr($_SERVER['HTTP_USER_AGENT'],0,40);
            $remoteIP=$_SERVER['REMOTE_ADDR'];
            $lib =& $this->POROTO->Libraries['siteLibrary'];
            //Cambio 20180224 Para olvide mi contraseña
            
            if(isset($_POST["olvide"],$_POST["username"]) and $_POST["olvide"] == "si"){
                $db =& $this->POROTO->DB; 
                $db->dbConnect("seguridad/login");
                $sql = " select p.idpersona, p.apellido,p.nombre,p.documentonro,u.usuario, u.password, u.primeracceso,u.email from usuarios ";
                $sql.= " u inner join personas p on p.idpersona=u.idpersona where u.usuario='" . $db->dbEscape($_POST["username"]) . "' AND u.estado=1 AND p.estado=1";
		$arr = $db->getSQLArray($sql);
                if (count($arr)==1) {
                    //Envio el mail
                    $dataEMail=$arr[0]["email"];
                    if ($this->POROTO->Config['override_mail_address'] != "") $mailto = $this->POROTO->Config['override_mail_address']; else $mailto = $dataEMail;
                    $mailSubject = $this->POROTO->Config["empresa_descripcion"]." - Recuperar Contraseña";
                    $mailBody = "Estimado/a " . trim($arr[0]["nombre"]) . " ". trim($arr[0]["apellido"]) . ", le enviamos los datos ";
                    $mailBody.= "para acceder al sitio de alumnos.<br>";
                    $mailBody.= "Usuario: ". trim($arr[0]["usuario"])."<br>";
                    $mailBody.= "Contraseña: ".trim($arr[0]["password"]);                    
                    $lib->sendMail($mailto, $mailSubject, $mailBody);
                    //Logueo
                    $loginErrorMessage = "Olvide mi contraseña";
                    $sql = "insert into usuarioaccesos (idpersona,fecha,usuario,contraseña,ip,navegador,estado) ";
                    $sql.= "select ".$arr[0]["idpersona"].",CURRENT_TIMESTAMP,'".$_POST["username"]."',null,'".$remoteIP."','".$navegador."','".$loginErrorMessage."'";
                    $db->insert($sql);
                    $loginErrorMessage = "Se ha enviado la contraseña al correo ".$dataEMail;
                }else
                {
                    $loginErrorMessage = "Contactese con administración. Su usuario no existe o esta deshabilitado.";
                }
                $db->dbDisconnect();
                include($this->POROTO->ViewPath . "/-login.php");
                exit();
            }
            //Fin Cambio 20180224 Para olvide mi contraseña
            
            
            
		if (isset($_POST["username"], $_POST["password"])) {
                    $db =& $this->POROTO->DB; 
                    $db->dbConnect("seguridad/login");

                    //check pwd
		    $sql = "select p.idpersona, p.documentonro, u.password, u.primeracceso from usuarios u inner join personas p on p.idpersona=u.idpersona where u.usuario='" . $db->dbEscape($_POST["username"]) . "' AND u.estado=1 AND p.estado=1";
		    $arr = $db->getSQLArray($sql);
		    if (count($arr)==1) {
			    if (($arr[0]['password'] != $_POST["password"])) {
				$loginErrorMessage = "Contraseña errónea";
                                //update last login stamp
                                $sql = "insert into usuarioaccesos (idpersona,fecha,usuario,contraseña,ip,navegador,estado) select  null,CURRENT_TIMESTAMP,'".$_POST["username"]."','". $_POST["password"]."','".$remoteIP."','".$navegador."','".$loginErrorMessage."'";
                                $db->insert($sql);
                                $db->dbDisconnect();
				include($this->POROTO->ViewPath . "/-login.php");
				exit();
				}
			} else {
                                $loginErrorMessage = "Usuario inválido o deshabilitado";
                                $sql = "insert into usuarioaccesos (idpersona,fecha,usuario,contraseña,ip,navegador,estado) select  null,CURRENT_TIMESTAMP,'".$_POST["username"]."','". $_POST["password"]."','".$remoteIP."','".$navegador."','".$loginErrorMessage."'";
                                $db->insert($sql);
				$db->dbDisconnect();
				include($this->POROTO->ViewPath . "/-login.php");
				exit();
			}

		    $sql = "SELECT p.idpersona, p.legajo, p.apellido, p.nombre, p.estado, u.usuario, ";
                    $sql.= " u.email, u.estado, u.primeracceso";
		    $sql.= " FROM personas p inner join usuarios u on p.idpersona=u.idpersona";
		    $sql.= " where p.idpersona=" . $arr[0]['idpersona'];
		    $arrUserData = $db->getSQLArray($sql);

		    //si tiene mas de un rol, levanto el primero
			$sql = "select r.idrol, r.nombre from personarol pr inner join roles r on pr.idrol=r.idrol where pr.idpersona=" . $arr[0]['idpersona'] . " order by 1";
		    $arrUserRoles = $db->getSQLArray($sql);

		    if (count($arrUserRoles) == 0 || count($arrUserData) == 0) {
				$db->dbDisconnect();
				$loginErrorMessage = "user misconfigured. contact administrator";
				include($this->POROTO->ViewPath . "/-login.php");
				exit();
		    }

			//start session
		    $this->POROTO->Session->startSession($arrUserData[0]['idpersona'],$arrUserData[0]['legajo'],$arrUserData[0]['apellido'],$arrUserData[0]['nombre'],$arrUserData[0]['usuario'],$arrUserData[0]['email'], $arrUserRoles[0]['idrol'], $arrUserRoles[0]['nombre']);

		    //update last login stamp
                    $sql = "insert into usuarioaccesos (idpersona,fecha,usuario,contraseña,ip,navegador,estado) select  ".$arr[0]['idpersona'].",CURRENT_TIMESTAMP,'".$_POST["username"]."','". $_POST["password"]."','".$remoteIP."','".$navegador."','Acceso concedido'";
                    $db->insert($sql);
		    //$sql = "insert into usuarioaccesos (idpersona,fecha) select " . $arr[0]['idpersona'] . ",CURRENT_TIMESTAMP";
		    //$db->insert($sql);
		   
			
		    if ($arr[0]['primeracceso'] == 1) {
				$db->dbDisconnect();
	     		header("Location: /primeracceso", TRUE, 302);
		    } else { 
				$sql = "select r.idrol, r.nombre from personarol pr inner join roles r on pr.idrol=r.idrol where pr.idpersona=" . $arrUserData[0]['idpersona'];
			    $arr = $db->getSQLArray($sql);
				$db->dbDisconnect();
			    if (count($arr)>1) {
			     	header("Location: /pickrole", TRUE, 302);
					//OJO aca revisar porque al entrar por aca no esta entrando a verificar 
					//los permisos y guardarlos en la sesion.
			    } else {
					//Cambio Leo 20170706 Leo
					//Asignar permisos
					$db->dbConnect("seguridad/login");
					$ses =& $this->POROTO->Session;
					$idRol=$arr[0]['idrol'];
					
					$sql= "select p.idpermiso,p.nombre as nombre ";
					$sql.="from permisosroles pr inner join permisos p on pr.idpermiso=p.idpermiso ";
					$sql.="where pr.idRol=".$idRol;	
					$sql.=" union all ";
					$sql.="select p.idpermiso,pe.nombre as nombre from personapermisos p ";
					$sql.="inner join permisos pe on p.idpermiso=pe.idpermiso ";
					$sql.="where p.idpersona= ".$arrUserData[0]['idpersona'];
					$sql.=" order by nombre ";
					
					$result = $db->getSQLArray($sql);
					$ses->clearPermisos(); //Cambio 65 Leo 20171025
					foreach ($result as $permiso) {
							$ses->agregarPermiso($permiso["idpermiso"],$permiso["nombre"]);
					}
                                        
                                        //Cambio 20180222
                                        $sql="select parametro,valor from configuracion order by orden";
                                        $result = $db->getSQLArray($sql);
                                        $ses->clearConfiguracion();
                                        foreach ($result as $conf) {
                                                    $ses->agregarConfiguracion($conf["parametro"],$conf["valor"]);
                                        }
                                        //Fin Cambio 20180222
                                                
					$db->dbDisconnect();
					//Fin Cambio Leo 20170706 Leo
					
		     		header("Location: /", TRUE, 302);
		     	}
	     	}

		} else {
			include($this->POROTO->ViewPath . "/-login.php");
		}
	}

	function logout() {  //OK ojo si sacamos del home ver sitelibrary.
		$this->POROTO->Session->endSession();
      	header("Location: /", TRUE, 302);
	}
	


	function primeracceso() { //OK pero modificarlo para que tome el ROL y de acuerdo a eso asigne los permisos, como login.
		$passwordExplanied = $this->POROTO->Config['password_constraints_explained'];
		if (isset($_POST["password"])) {
			if ($_POST["noModify"]=="0") {
				$db =& $this->POROTO->DB; 
				$db->dbConnect("home/primeracceso");
				$ses =& $this->POROTO->Session;
				$lib =& $this->POROTO->Libraries['siteLibrary'];
				if ($lib->isPasswordValid(trim($_POST['password']))) {
					// cambio ok
					$sql = "update usuarios set primeracceso=0, password='" . $db->dbEscape($_POST['password']) . "' where idpersona=" . $ses->getIdPersona();
					$db->update($sql);

					$sql = "select r.idrol, r.nombre from personarol pr inner join roles r on pr.idrol=r.idrol where pr.idpersona=" . $ses->getIdPersona();
				    $arr = $db->getSQLArray($sql);
					
				    if (count($arr)>1) {
						$db->dbDisconnect();
    			     	header("Location: /pickrole", TRUE, 302);
		    		} else {
						//Agregar permisos
						$idRol=$arr[0]['idrol'];
						
						$sql= "select p.idpermiso,p.nombre as nombre ";
						$sql.="from permisosroles pr inner join permisos p on pr.idpermiso=p.idpermiso ";
						$sql.="where pr.idRol=".$idRol;	
						$sql.=" union all ";
						$sql.="select p.idpermiso,pe.nombre as nombre from personapermisos p ";
						$sql.="inner join permisos pe on p.idpermiso=pe.idpermiso ";
						$sql.="where p.idpersona= ".$ses->getIdPersona();
						$sql.=" order by nombre ";
						
						$result = $db->getSQLArray($sql);
						$ses->clearPermisos(); //Cambio 65 Leo 20171025
						foreach ($result as $permiso) {
								$ses->agregarPermiso($permiso["idpermiso"],$permiso["nombre"]);
						}
                                                
                                                //Cambio 20180222
                                                $sql="select parametro,valor from configuracion order by orden";
                                                $result = $db->getSQLArray($sql);
                                                $ses->clearConfiguracion();
                                                foreach ($result as $conf) {
                                                            $ses->agregarConfiguracion($conf["parametro"],$conf["valor"]);
                                                }
                                                //Fin Cambio 20180222
						$db->dbDisconnect();
						//Fin Cambio Leo 20170706 Leo
    			     	header("Location: /", TRUE, 302);
		    		}
				} else {
					$validationErrors = "La contraseña no cumple con las reglas";
					include($this->POROTO->ViewPath . "/-primer-acceso.php");
				}
			} else {
				$db =& $this->POROTO->DB; 
				$db->dbConnect("home/primeracceso");
				$ses =& $this->POROTO->Session;
				$sql = "update usuarios set primeracceso=0 where idpersona=" . $ses->getIdPersona();
				$db->update($sql);

				$sql = "select r.idrol, r.nombre from personarol pr inner join roles r on pr.idrol=r.idrol where pr.idpersona=" . $ses->getIdPersona();
			    $arr = $db->getSQLArray($sql);

			    if (count($arr)>1) {
					$db->dbDisconnect();
			     	header("Location: /pickrole", TRUE, 302);
	    		} else {
					//Agregar permisos
						$idRol=$arr[0]['idrol'];
						
						$sql= "select p.idpermiso,p.nombre as nombre ";
						$sql.="from permisosroles pr inner join permisos p on pr.idpermiso=p.idpermiso ";
						$sql.="where pr.idRol=".$idRol;	
						$sql.=" union all ";
						$sql.="select p.idpermiso,pe.nombre as nombre from personapermisos p ";
						$sql.="inner join permisos pe on p.idpermiso=pe.idpermiso ";
						$sql.="where p.idpersona= ".$ses->getIdPersona();
						$sql.=" order by nombre ";
						
						$result = $db->getSQLArray($sql);
						$ses->clearPermisos(); //Cambio 65 Leo 20171025
						foreach ($result as $permiso) {
								$ses->agregarPermiso($permiso["idpermiso"],$permiso["nombre"]);
						}
                                                
                                                //Cambio 20180222
                                                $sql="select parametro,valor from configuracion order by orden";
                                                $result = $db->getSQLArray($sql);
                                                $ses->clearConfiguracion();
                                                foreach ($result as $conf) {
                                                            $ses->agregarConfiguracion($conf["parametro"],$conf["valor"]);
                                                }
                                                //Fin Cambio 20180222
						$db->dbDisconnect();
						//Fin Cambio Leo 20170706 Leo					
			     	header("Location: /", TRUE, 302);
	    		}
			}
		} else {
			include($this->POROTO->ViewPath . "/-primer-acceso.php");
		}
	}


 	public function misdatos() {
		$validationErrors = array();
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];


		//detectar si el usuario es profesor y redirigirlo a profesores/misdatos
		if ($ses->getIdRole()==$this->POROTO->Config['rol_profesor_id']) {
			header("Location: /profesores/misdatos", TRUE, 302);
			exit();
		}

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Ver Datos')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706

		$db->dbConnect("home/misdatos");
		
                //Modificacion
		if (isset($_POST['email'], $_POST['nacionalidad'])) {
			$db->dbConnect("home/misdatos");
			$dataNroDoc = $db->dbEscape(intval($_POST['nrodoc']));
			$dataTipDoc = $db->dbEscape($_POST['tipdoc']);
			// $dataApellido = $db->dbEscape(trim($_POST['apellido']));
			// $dataNombre = $db->dbEscape(trim($_POST['nombre']));
			$dataNacionalidad = $db->dbEscape($_POST['nacionalidad']);
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
			if (isset($_POST['primeracceso'])) $dataPrimerAcceso = $db->dbEscape(trim($_POST['primeracceso'])); else $dataPrimerAcceso = "";
			$db->dbDisconnect();

			// STAP! VALIDATION TIME
			// nacionalidad - seteado y mayor a 0
			if ($dataNacionalidad=="0" || $dataNacionalidad=="") $validationErrors['nacionalidad'] = "La Nacionalidad es obligatoria";
			// f.nacim		obligatoria. no mayor a 100 anos ni menor a 5 anios
			if (!$lib->validateDate($dataFNac)) {
				$validationErrors['fnac'] = "La Fecha de Nacimiento es inválida";
			} else {
		    	$d = $lib->datediff($dataFNac);
		    	if ($d > (365*100)) $validationErrors['fnac'] = "La Fecha de Nacimiento es inválida (>100)";
		    	if ($d < (365 * 5)) $validationErrors['fnac'] = "La Fecha de Nacimiento es inválida (BenjaminButton)" . $d;
		    	if ($d<0) $validationErrors['fnac'] = "La Fecha de Nacimiento es inválida (futura)";
			}

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
			// ano egreso opcional. numerico entre 1930 y el año actual
			if (trim($dataAnoEgr)!="") {
				if (is_int($dataAnoEgr)) {
					if (intval($dataAnoEgr) < 1930) $validationErrors['anoegreso'] = "El campo Año Egreso no puede ser anterior a 1930";
					if (intval($dataAnoEgr) > date("Y")) $validationErrors['anoegreso'] = "El campo Año Egreso no puede ser posterior a " . date("Y");
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
			if (!filter_var($dataEMail, FILTER_VALIDATE_EMAIL)) $validationErrors['email'] = "El campo Email es inválido";
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

			if (count($validationErrors) == 0) {  
				$db->dbConnect("home/misdatos");

				$sqlP = "UPDATE personas SET ";
				$sqlP.= "nacionalidad='" . $dataNacionalidad . "'";
				if ($dataFNac == "") $sqlP.= ",fechanac=null"; else $sqlP.= ",fechanac='" . $lib->dateDMY2YMD($dataFNac) . "'";
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
				$sqlP.= " WHERE idpersona=" . $ses->getIdPersona();

				$db->begintrans();
				$bOk = $db->update($sqlP, '', true);

				$sqlA = "INSERT INTO alumnos (idpersona,titulosecundario,otorgadopor,aniooegreso,";
				$sqlA.= "estadoalumno_id,certificadotrabajo,observaciones,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
				$sqlA.= $ses->getIdPersona();
				$sqlA.= ",'" . $dataTitulo . "'";
				$sqlA.= ",'" . $dataOtorga . "'";
				$sqlA.= "," . ($dataAnoEgr!='' ? $dataAnoEgr : 'null');
				$sqlA.= "," . $this->POROTO->Config['estado_alumno_ingresante_id'];
                                $sqlA.= "," . ($dataCLab == "certlab" ? "1" : "0");
				$sqlA.= ",'" . $dataObs . "'";
				$sqlA.= ",'" . $ses->getUsuario() . "'";
				$sqlA.= ",CURRENT_TIMESTAMP ";
				$sqlA.= ",NULL ";
				$sqlA.= ",NULL ";
				$sqlA.= " ON DUPLICATE KEY UPDATE ";
				$sqlA.= "titulosecundario='" . $dataTitulo . "'";
                                $sqlA.= ",certificadotrabajo=".($dataCLab == "certlab" ? "1" : "0");
				$sqlA.= ",otorgadopor='" . $dataOtorga . "'";
				$sqlA.= ",aniooegreso=" . ($dataAnoEgr!='' ? $dataAnoEgr : 'null');
				$sqlA.= ",observaciones='" . $dataObs . "'";
				$sqlA.= ",usumodi='" . $ses->getUsuario() . "'";
				$sqlA.= ",fechamodi=CURRENT_TIMESTAMP";
				if ($bOk!==false) $bOk = $db->insert($sqlA, '', true);

				$sqlF = "INSERT INTO fichamedica (idpersona,obrasocial,contactoemergencia,telefono,enfermedades) SELECT ";
				$sqlF.= $ses->getIdPersona();
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
				if ($dataPass1!="") $sqlU.= ",password='" . $dataPass1 . "'";
				$sqlU.= ",primeracceso=" . ($dataPrimerAcceso == "primeracceso" ? "1" : "0");
				$sqlU.= ",usumodi='" . $ses->getUsuario() . "'";
				$sqlU.= ",fechamodi=CURRENT_TIMESTAMP";
				$sqlU.= " WHERE idpersona=" . $ses->getIdPersona();
				if ($bOk!==false) $bOk = $db->update($sqlU, '', true);

				if ($bOk === false) {
					$db->rollback();
					$ses->setMessage("Se produjo un error realizando la modificación", SessionMessageType::TransactionError);
					header("Location: /", TRUE, 302);
					exit();

				} else {
					$db->commit();
				}
				$db->dbDisconnect();

				$ses->setMessage("La modificación se completó con éxito", SessionMessageType::Success);
				header("Location: /", TRUE, 302);
				exit();

			}

		}

                //Carga de datos en el formulario
		$db->dbConnect("home/misdatos");
                $sql = "SELECT id,descripcion FROM tipodoc order by id";
		$viewDataTipoDocumento = $db->getSQLArray($sql);

		$sql = "select p.apellido, p.nombre, p.nacionalidad, date_format(fechanac, '%d/%m/%Y') fnac_dmy, p.tipodoc, td.descripcion, p.documentonro, p.telefono1, p.telefono2, p.sexo, p.estadocivil  ";
		$sql.= " ,p.direccion, p.entrecalles, p.numero, p.piso, p.depto, p.idlocalidad, l.descripcion localidad_descripcion, l.idprovincia, p.codpostal, a.certificadotrabajo certlab";
		$sql.= " ,a.titulosecundario, a.otorgadopor, a.aniooegreso anoegreso";
		$sql.= " ,fm.obrasocial, fm.contactoemergencia, fm.telefono, fm.enfermedades";
		$sql.= " ,u.usuario, u.password, u.email, u.primeracceso";
		$sql.= " ,a.observaciones";
		$sql.= " ,a.estadoalumno_id, ea.descripcion";
		$sql.= " from personas p inner join tipodoc td on td.id=p.tipodoc inner join usuarios u on u.idpersona=p.idpersona";
		$sql.= " left join localidades l on l.id=p.idLocalidad";
		$sql.= " left join alumnos a on a.idpersona=p.idpersona left join estadoalumno ea on a.estadoalumno_id=ea.id";
		$sql.= " left join fichamedica fm on fm.idpersona=p.idpersona";
		$sql.= " where p.idpersona=" . $ses->getIdPersona();
		$viewData = $db->getSQLArray($sql);

		$sql = "SELECT idcarrera,nombre,descripcion FROM carreras WHERE estado=1 order by nombre";
		$viewDataCarreras = $db->getSQLArray($sql);

		$sql = "select idarea,nombre from areas";
		$viewDataAreas = $db->getSQLArray($sql);

		$sql = "select idinstrumento,nombre,descripcion from instrumentos order by nombre";
		$viewDataInstrumentos = $db->getSQLArray($sql);

		$sql = "select id,descripcion from provincias";
		$viewDataProvincias = $db->getSQLArray($sql);

		$idprov = (isset($_POST['provincia']) ? $_POST['provincia'] : $viewData[0]['idprovincia']);
		if ($idprov=="") $idprov=0;
		$sql = "select id,descripcion,cp from localidades where idprovincia=" . $idprov . " order by descripcion";
		$viewDataLocalidades = $db->getSQLArray($sql);

		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		$sql = "SELECT date_format(ac.fechainscripcion, '%d/%m/%Y') fechainscripcion_dmy, c.nombre carrera_nombre, c.descripcion carrera_descripcion, i.nombre instrumento_nombre, i.descripcion instrumento_descripcion, a.nombre area_nombre, eac.descripcion estado, cn.descripcion nivelDescripcion";
		$sql.= " FROM alumnocarrera ac inner join estadoalumnocarrera eac on ac.estado=eac.id";
		$sql.= " inner join carreras c on ac.idcarrera=c.idcarrera";
		$sql.= " inner join instrumentos i on ac.idinstrumento=i.idinstrumento";
		$sql.= " inner join carreraniveles cn on ac.idnivel=cn.id";
		$sql.= " left join areas a on ac.idarea=a.idarea";
		$sql.= " WHERE ac.idpersona=" . $db->dbEscape($ses->getIdPersona());
		$viewDataAlumnoCarreras = $db->getSQLArray($sql);

		$db->dbDisconnect();

		$viewDataSexo = $this->POROTO->Config['dominios']['sexo'];
		$viewDataNacionalidad = $this->POROTO->Config['dominios']['nacionalidad'];
		$viewDataEstadoCivil = $this->POROTO->Config['dominios']['estadocivil'];
		$newCarreras = array();

		$status = "misdatos";
		$pageTitle="Mis datos";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/ver-alumno.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}






	public function ajaxlocalidades($provinciaId) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("home/ajaxLocalidades");
		$sql = "SELECT id,descripcion,cp FROM localidades where idprovincia=" . $db->dbEscape($provinciaId) . " order by descripcion";
		$arrData = $db->getSQLArray($sql);
		$db->dbDisconnect();
		echo json_encode($arrData);
	}

	public function ajaxnivelescarrera($carreraid) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("home/ajaxNivelesCarrera");
		$sql = "SELECT id,descripcion  FROM carreraniveles where idcarrera=" . $db->dbEscape($carreraid) . " order by orden";
		$arrData = $db->getSQLArray($sql);
		$db->dbDisconnect();
		echo json_encode($arrData);
	}

	

	public function ajaxnivelescarrerainscripcion($carreraid) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("home/ajaxNivelesCarreraInscripcion");
		$sql = "SELECT id,descripcion  FROM carreraniveles where vistainscripcion=1 and idcarrera=" . $db->dbEscape($carreraid) . " order by orden";
		$arrData = $db->getSQLArray($sql);
		$db->dbDisconnect();
		echo json_encode($arrData);
	}

	public function ajaxmateria($materiaid) { //traigo datos basicos de la materia
		$db =& $this->POROTO->DB;
		$db->dbConnect("home/ajaxMateria");
		$sql = "SELECT idtipomateria, nombre, descripcion, anio, promocionable, cantidadmodulos, estado FROM materias where idmateria=" . $db->dbEscape($materiaid);
		$arrData = $db->getSQLArray($sql);
		// $sql = "SELECT m.idmateria,concat(m.anio,' - ',m.nombre) nombre,m.anio,apc,cpc,apr FROM correlativas c INNER JOIN materias m ON m.idmateria=c.idcorrelativa WHERE c.idmateria=" . $materiaid . " ORDER BY m.anio, m.nombre";
		// $arrDataCorr = $db->getSQLArray($sql);
		$db->dbDisconnect();
		$result=array("materia"=>$materiaid, "rows"=>$arrData/*, "correlativas"=>$arrDataCorr*/);
		echo json_encode($result);
	}


//logica de acceso a reempadronamiento
//1- Esta habilitado en la tabla de configuracion el reempadronamiento? NO->Salir con mensaje
//2- Existe solo uno y solo un tipo+numero de documento en la tabla de personas, tiene un registro en la tabla de usuarios, la persona esta habilitada, y su usuario no esta habilitado. NO-> "Debe concurrir a la administratoristracion en persona para su alta inicial en el sistema"
//3- El estado del alumno es reempadronar. NO->"El documento ingresado ya fue reempadronado"
//4- Presento el formulario
	public function reempadronamiento() {
		$validationErrors = array();
		$db =& $this->POROTO->DB;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		$db->dbConnect("home/reempadronamiento");

		$sql = "select valor from configuracion where parametro='reempadronamiento_permitido'";
		$arrConfigDb = $db->getSQLArray($sql);
		$db->dbDisconnect();

		if (count($arrConfigDb) != 1 || $arrConfigDb[0]['valor'] != 'Y') {
			$message = "No se encuentra habilitado el reempadronamiento en este momento";
			include($this->POROTO->ViewPath . "/reempadronamiento-notificacion.php");
			exit();
		}


		if (isset($_POST['tipdoc'], $_POST['nrodoc']) ) {
                        //Entro a traer los datos de la persona y los muestro en el formulario.
			//validar si existe el tipo + nro documento en personas. si existe, permitir modificar el registro; sino, permitir dar el alta
			$db->dbConnect("home/reempadronamiento");
			$sql = "select p.idpersona, a.estadoalumno_id";
			$sql.= " from personas p inner join usuarios u on p.idpersona=u.idpersona";
			$sql.= " left join alumnos a on a.idpersona=p.idpersona";
			$sql.= " where p.tipodoc=" . $db->dbEscape($_POST['tipdoc']);
			$sql.= " and p.documentonro=" . $db->dbEscape(intval($_POST['nrodoc'])) . " and p.estado=1"; // and u.estado=0";
			$arr = $db->getSQLArray($sql);

			if (count($arr)==1) {
				$sql = "SELECT date_format(ac.fechainscripcion, '%d/%m/%Y') fechainscripcion_dmy, c.nombre carrera_nombre, c.descripcion carrera_descripcion, i.nombre instrumento_nombre, i.descripcion instrumento_descripcion, a.nombre area_nombre, eac.descripcion estado, cn.descripcion nivelDescripcion";
				$sql.= " FROM alumnocarrera ac inner join estadoalumnocarrera eac on ac.estado=eac.id";
				$sql.= " inner join carreras c on ac.idcarrera=c.idcarrera";
				$sql.= " inner join instrumentos i on ac.idinstrumento=i.idinstrumento";
				$sql.= " inner join carreraniveles cn on ac.idnivel=cn.id";
				$sql.= " left join areas a on ac.idarea=a.idarea";
				$sql.= " WHERE ac.idpersona=" . $arr[0]['idpersona'];		
				$viewDataAlumnoCarreras = $db->getSQLArray($sql);
			}

			$db->dbDisconnect();

			if (count($arr)==1) {
				if ($arr[0]['estadoalumno_id'] != $this->POROTO->Config['estado_alumno_reempadronar_id']) {
					header("Location: /reempadronamiento?e=1", TRUE, 302);
					exit();
				}
				

				//evaluo si el usuario esta entrando por primera vez a la pantalla de reempadronamiento (tipo y nro doc ya validado), o es un post de su info
				if (isset($_POST['email'], $_POST['nacionalidad'])) {
					$db->dbConnect("home/reempadronamiento");
					$dataNacionalidad = $db->dbEscape($_POST['nacionalidad']);
					$dataFNac = $db->dbEscape($_POST['fnac']);
					$dataTel1 = $db->dbEscape(trim($_POST['telefono1']));
					$dataTel2 = $db->dbEscape(trim($_POST['telefono2']));
					$dataSexo = $db->dbEscape($_POST['sexo']);
					$dataECiv = $db->dbEscape($_POST['estciv']);
					// $dataCLab = $db->dbEscape($_POST['certlab']);
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
					$db->dbDisconnect();
					
					// STAP! VALIDATION TIME
					// nacionalidad - seteado y mayor a 0
					if ($dataNacionalidad=="0" || $dataNacionalidad=="") $validationErrors['nacionalidad'] = "La Nacionalidad es obligatoria";
					// f.nacim		obligatoria. no mayor a 100 anos ni menor a 5 anios
					if (!$lib->validateDate($dataFNac)) {
						$validationErrors['fnac'] = "La Fecha de Nacimiento es inválida";
					} else {
				    	$d = $lib->datediff($dataFNac);
				    	if ($d > (365 * 100)) $validationErrors['fnac'] = "La Fecha de Nacimiento es inválida (>100)";
				    	if ($d < (365 * 5)) $validationErrors['fnac'] = "La Fecha de Nacimiento es inválida";
				    	if ($d < 0) $validationErrors['fnac'] = "La Fecha de Nacimiento es inválida (futura)";
					}
					// telefono fijo obligatorio. entre 6 y 20 caracteres. solo numeros, espacios, parentesis y guion
					if (strlen($dataTel1) < 6 || strlen($dataTel1) > 20) $validationErrors['telefono1'] = "El Teléfono Fijo debe contener entre 6 y 20 caracteres";
					if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTel1)) $validationErrors['telefono1'] = "El Teléfono Fijo contiene caracteres inválidos";
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
					// ano egreso opcional. numerico entre 1930 y el año actual
					if (trim($dataAnoEgr)!="") {
						if (is_int($dataAnoEgr)) {
							if (intval($dataAnoEgr) < 1930) $validationErrors['anoegreso'] = "El campo Año Egreso no puede ser anterior a 1930";
							if (intval($dataAnoEgr) > date("Y")) $validationErrors['anoegreso'] = "El campo Año Egreso no puede ser posterior a " . date("Y");
						}
					}

					// obra social 			opcional maxlength 45
					if (strlen($dataOSoc)>45) $validationErrors['obrasocial'] = "El campo Obra Social puede contener como máximo 45 caracteres";
					// contacto emergencia 	obligatorio maxlength 45
					if ($dataCont=="") $validationErrors['contactoemergencia'] = "El campo Contacto Emergencia es obligatorio";
					if (strlen($dataCont)>45) $validationErrors['contactoemergencia'] = "El campo Contacto Emergencia puede contener como máximo 45 caracteres";
					// telef emergencia 	obligatorio debe contener entre 6 y 20 caracteres. solo numeros, espacios, parentesis y guion
					if (strlen($dataTele) < 6 || strlen($dataTele) > 20) $validationErrors['telefono'] = "El Teléfono Contacto debe contener entre 6 y 20 caracteres";
					if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTele)) $validationErrors['telefono'] = "El Teléfono Contacto contiene caracteres inválidos";
					if ($dataTele=="") $validationErrors['telefono'] = "El campo Teléfono Contacto es obligatorio";
					if ($dataTele!="") {
						if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTele)) $validationErrors['telefono'] = "El Teléfono Contacto contiene caracteres inválidos";
					}
					// enfermedades 		opcional maxlength 500
					if (strlen($dataEnfe)>500) $validationErrors['enfermedades'] = "El campo Enfermedades puede contener como máximo 500 caracteres";

					// email 	obligatorio maxlength 45 a@b.c
					if ($dataEMail=="") $validationErrors['email'] = "El campo Email es obligatorio";
					if (strlen($dataEMail)>45) $validationErrors['email'] = "El campo Email puede contener como máximo 45 caracteres";
					if (!filter_var($dataEMail, FILTER_VALIDATE_EMAIL)) $validationErrors['email'] = "El campo Email es inválido";
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

					// observaciones 	opcional. maxlength 5000
					if (strlen($dataObs) > 5000) $validationErrors['observaciones'] = "El campo Observaciones puede contener como máximo 5000 caracteres. Contiene " . strlen($dataObs);

					// CARRERAS. Tiene que tener al menos una
					// valido el array new-carreras
					$nc = (isset($_POST['new-carreras']) ? $_POST['new-carreras'] : array());
					$totCarreras = count($viewDataAlumnoCarreras) + count($nc);
					//if ($totCarreras < 1) $validationErrors['carreras'] = "Debe tener al menos una carrera registrada";

					if (count($validationErrors) == 0) {  
						$sqlP = "UPDATE personas SET ";
						$sqlP.= " nacionalidad='" . $dataNacionalidad . "'";
						$sqlP.= ",fechanac='" . $lib->dateDMY2YMD($dataFNac) . "'";
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
						$sqlP.= ",usumodi='" . $this->POROTO->Config['usuario_auditoria_sitio_publico'] . "'";
						$sqlP.= ",fechamodi=CURRENT_TIMESTAMP";
						$sqlP.= " WHERE idpersona=" . $arr[0]['idpersona'];

						$sqlA = "INSERT INTO alumnos (idpersona,titulosecundario,otorgadopor,aniooegreso,";
						$sqlA.= "estadoalumno_id,certificadotrabajo,observaciones,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
						$sqlA.= $arr[0]['idpersona'];
						$sqlA.= ",'" . $dataTitulo . "'";
						$sqlA.= ",'" . $dataOtorga . "'";
						$sqlA.= "," . ($dataAnoEgr!='' ? $dataAnoEgr : 'null');
						$sqlA.= "," . $this->POROTO->Config['estado_alumno_cargado_id'];
						$sqlA.= ",0"; 
						$sqlA.= ",'" . $dataObs . "'";
						$sqlA.= ",'" . $this->POROTO->Config['usuario_auditoria_sitio_publico'] . "'";
						$sqlA.= ",CURRENT_TIMESTAMP ";
						$sqlA.= ",NULL ";
						$sqlA.= ",NULL ";
						$sqlA.= " ON DUPLICATE KEY UPDATE ";
						$sqlA.= "titulosecundario='" . $dataTitulo . "'";
						$sqlA.= ",otorgadopor='" . $dataOtorga . "'";
						$sqlA.= ",aniooegreso=" . ($dataAnoEgr!='' ? $dataAnoEgr : 'null');
						$sqlA.= ",estadoalumno_id=" . $this->POROTO->Config['estado_alumno_cargado_id'];
						$sqlA.= ",observaciones='" . $dataObs . "'";
						$sqlA.= ",usumodi='" . $this->POROTO->Config['usuario_auditoria_sitio_publico'] . "'";
						$sqlA.= ",fechamodi=CURRENT_TIMESTAMP";

						$sqlF = "INSERT INTO fichamedica (idpersona,obrasocial,contactoemergencia,telefono,enfermedades) SELECT ";
						$sqlF.= $arr[0]['idpersona'];
						$sqlF.= ",'" . $dataOSoc . "'";
						$sqlF.= ",'" . $dataCont . "'";
						$sqlF.= ",'" . $dataTele . "'";
						$sqlF.= ",'" . $dataEnfe . "'";
						$sqlF.= " ON DUPLICATE KEY UPDATE ";
						$sqlF.= "obrasocial='" . $dataOSoc . "'";;
						$sqlF.= ",contactoemergencia='" . $dataCont . "'";;
						$sqlF.= ",telefono='" . $dataTele . "'";;
						$sqlF.= ",enfermedades='" . $dataEnfe . "'";;

						$sqlU = "update usuarios set ";
						$sqlU.= "password='" . $dataPass1 . "'";
						$sqlU.= ",estado=1";
						$sqlU.= ",email='" . $dataEMail . "'";
						$sqlU.= ",usumodi='" . $this->POROTO->Config['usuario_auditoria_sitio_publico'] . "'";
						$sqlU.= ",fechamodi=CURRENT_TIMESTAMP";
						$sqlU.= " where idpersona=" . $arr[0]['idpersona'];

						$sqlC = array();
						foreach ($nc as $carrera) {
							$a = explode("~**~", $carrera);
							if (count($a)==5) { //vienen: carrera, area, nivel, instrumento, fecha (d/m/y)
								$carreraNombre = "";
								$carreraDescripcion = "";
								$areaNombre = "";
								$nivelNombre = "";
								$instrumentoDescripcion = "";

								$sql = "INSERT INTO alumnocarrera (idpersona,idcarrera,idinstrumento,idarea,fechainscripcion,";
								$sql.= "fechafinalizada,idnivel,estado,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
								$sql.= $arr[0]['idpersona'];
								$sql.= "," . $a[0];
								$sql.= "," . $a[3];
								$sql.= "," . $a[1];
								$sql.= ",'" . $lib->dateDMY2YMD($a[4]) . "'";
								$sql.= ",null";
								$sql.= "," . $a[2];
								$sql.= "," . $this->POROTO->Config['estado_carrera_en_curso_id'];
								$sql.= ",'" . $this->POROTO->Config['usuario_auditoria_sitio_publico'] . "'";
								$sql.= ",CURRENT_TIMESTAMP ";
								$sql.= ",null";
								$sql.= ",null";
								$sqlC[] = $sql;
							}
						}

						$db->dbConnect("home/reempadronamiento");
						$db->begintrans();
						$bOk = $db->update($sqlP, '', true);
						if ($bOk!==false) $bOk = $db->insert($sqlA, '', true);
						if ($bOk!==false) $bOk = $db->insert($sqlF, '', true);
						if ($bOk!==false) $bOk = $db->update($sqlU, '', true);
						foreach ($sqlC as $sqlCarrera) {
							if ($bOk!==false) $bOk = $db->insert($sqlCarrera, '', true);
						}
						if ($bOk === false) {
							$db->rollback();
							header("Location: /reempadronamiento?e=2", TRUE, 302);
							exit();

						} else {
							$db->commit();
						}
						$db->dbDisconnect();

						if ($this->POROTO->Config['override_mail_address'] != "") $mailto = $this->POROTO->Config['override_mail_address']; else $mailto = $dataEMail;
                                                $mailSubject = $this->POROTO->Config["empresa_descripcion"]." - Reempadronamiento completado";
						$mailBody = "Usted ha completado satisfactoriamente el reempadroanmiento. El acceso al sitio debera realizarlo con el usuario '" . trim($_POST['usuario']) . "' y la contraseña '" . trim($_POST['password1']) . "'";
						$lib->sendMail($mailto, $mailSubject, $mailBody);

						$message = "El reempadronamiento se completó con éxito. Se ha enviado un mail a su cuenta " . $mailto . " , informando como proseguir";
						include($this->POROTO->ViewPath . "/reempadronamiento-notificacion.php");
						exit();

					}

				}

				$db->dbConnect("home/reempadronamiento");
                                $sql = "SELECT id,descripcion FROM tipodoc order by id";
				$viewDataTipoDocumento = $db->getSQLArray($sql);

				$sql = "select p.apellido, p.nombre, p.nacionalidad, date_format(fechanac, '%d/%m/%Y') fnac_dmy, p.tipodoc, td.descripcion, p.documentonro, p.telefono1, p.telefono2, p.sexo, p.estadocivil  ";
				$sql.= " ,p.direccion, p.entrecalles, p.numero, p.piso, p.depto, p.idlocalidad, l.descripcion localidad_descripcion, l.idprovincia, p.codpostal, a.certificadotrabajo certlab";
				$sql.= " ,a.titulosecundario, a.otorgadopor, a.aniooegreso anoegreso";
				$sql.= " ,fm.obrasocial, fm.contactoemergencia, fm.telefono, fm.enfermedades";
				$sql.= " ,u.usuario, u.password, u.email, u.primeracceso";
				$sql.= " ,a.observaciones";
				$sql.= " ,a.estadoalumno_id, ea.descripcion";
				$sql.= " from personas p inner join tipodoc td on td.id=p.tipodoc inner join usuarios u on u.idpersona=p.idpersona";
				$sql.= " left join localidades l on l.id=p.idLocalidad";
				$sql.= " left join alumnos a on a.idpersona=p.idpersona left join estadoalumno ea on a.estadoalumno_id=ea.id";
				$sql.= " left join fichamedica fm on fm.idpersona=p.idpersona";
				$sql.= " where p.idpersona=" . $arr[0]['idpersona'];
				$viewData = $db->getSQLArray($sql);

				$sql = "SELECT idcarrera,nombre,descripcion FROM carreras WHERE estado=1 order by nombre";
				$viewDataCarreras = $db->getSQLArray($sql);

				$sql = "select idarea,nombre from areas";
				$viewDataAreas = $db->getSQLArray($sql);

				$sql = "select idinstrumento,nombre,descripcion from instrumentos";
				$viewDataInstrumentos = $db->getSQLArray($sql);

				$sql = "select id,descripcion from provincias";
				$viewDataProvincias = $db->getSQLArray($sql);

				$idprov = (isset($_POST['provincia']) ? $_POST['provincia'] : $viewData[0]['idprovincia']);
				if ($idprov=="") $idprov=0;
				$sql = "select id,descripcion,cp from localidades where idprovincia=" . $idprov . " order by descripcion";
				$viewDataLocalidades = $db->getSQLArray($sql);

				$db->dbDisconnect();
	
				$viewDataSexo = $this->POROTO->Config['dominios']['sexo'];
				$viewDataNacionalidad = $this->POROTO->Config['dominios']['nacionalidad'];
				$viewDataEstadoCivil = $this->POROTO->Config['dominios']['estadocivil'];

				$tempCarreras = isset($_POST['new-carreras']) ? $_POST['new-carreras'] : array();
				$newCarreras = array();

				foreach ($tempCarreras as $tempCarrera) {
					$arr = explode("~**~", $tempCarrera);
					if (count($arr)==5) { //vienen: carrera, area, nivel, instrumento, fecha (d/m/y)
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
	
						$db->dbConnect("home/reempadronamiento");
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

				$status = "reempadronamiento";
				$pageTitle="Reempadronamiento";
				include($this->POROTO->ViewPath . "/-header-no-session.php");
				include($this->POROTO->ViewPath . "/ver-alumno.php");
				include($this->POROTO->ViewPath . "/-footer.php");
			} else {
				header("Location: /reempadronamiento?e=3", TRUE, 302);
				include($this->POROTO->ViewPath . "/reempadronamiento-notificacion.php");
				exit();
			}
		} else {
                    //Entro al login / reempadronamiento para que ingresen Tipo y Nro de Documento.
                    $db->dbConnect("home/reempadronamiento");
		    $sql = "SELECT id,descripcion FROM tipodoc order by id";
		    $viewDataTipoDocumento = $db->getSQLArray($sql);
                    $db->dbDisconnect();
                    include($this->POROTO->ViewPath . "/reempadronamiento.php");
		}


	}


//logica de acceso a inscripcion
//1- Esta habilitada en la tabla de configuracion la inscripcion? NO->Salir con mensaje
//2- Existe una persona+usuario+alumno con el tipo+numero de documento, donde el estado de la persona es habilitado 
//		NO-> Si el estado del usuario es deshabilitado y el estado del alumno es ingresante -> "La inscripcion ya fue registrada"
//				NO->Para inscribirse debe loguearse al sistema
//3- Presento el formulario
	public function inscripcion() {
		$validationErrors = array();
		$db =& $this->POROTO->DB;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		$db->dbConnect("home/inscripcion");
		$sql = "select valor from configuracion where parametro='inscripcion_permitida'";
		$arrConfigDb = $db->getSQLArray($sql);
		$db->dbDisconnect();

		if (count($arrConfigDb) != 1 || $arrConfigDb[0]['valor'] != 'Y') {
			$message = "No se encuentra habilitada la inscripción en este momento";
			include($this->POROTO->ViewPath . "/reempadronamiento-notificacion.php");
			exit();
		}

		if (isset($_POST['tipdoc'], $_POST['nrodoc']) ) { 
			//validar si existe el tipo + nro documento en personas. si existe, no permitir el ingreso
			$db->dbConnect("home/inscripcion");
			$sql = "select p.idpersona, a.estadoalumno_id, u.estado";
			$sql.= " from personas p";
			$sql.= " left join usuarios u on p.idpersona=u.idpersona";
			$sql.= " left join alumnos a on a.idpersona=p.idpersona";
			$sql.= " where p.tipodoc=" . $db->dbEscape($_POST['tipdoc']);
			$sql.= " and p.documentonro=" . $db->dbEscape(intval($_POST['nrodoc'])) . " and p.estado=1";
			$arr = $db->getSQLArray($sql);

			if (count($arr)>0) {
				if ($arr[0]['estadoalumno_id']==$this->POROTO->Config['estado_alumno_ingresante_id']) {
					header("Location: /inscripcion?e=1", TRUE, 302);
				} else {
					header("Location: /inscripcion?e=2", TRUE, 302);
				}
				exit();
			} //if (count($arr)>0) {

		    $sql = "SELECT id,descripcion FROM tipodoc order by id";
			$viewDataTipoDocumento = $db->getSQLArray($sql);

			$db->dbDisconnect();

			if (isset($_POST['email'], $_POST['nacionalidad'])) { //estoy volviendo de completar toda la info.
				$db->dbConnect("home/inscripcion");
				$dataNroDoc = $db->dbEscape(intval($_POST['nrodoc']));
				$dataTipDoc = $db->dbEscape($_POST['tipdoc']);
				$dataApellido = mb_strtoupper($db->dbEscape(trim($_POST['apellido'])), 'UTF-8');
				$dataNombre = mb_strtoupper($db->dbEscape(trim($_POST['nombre'])), 'UTF-8');
				$dataNacionalidad = $db->dbEscape($_POST['nacionalidad']);
				$dataFNac = $db->dbEscape($_POST['fnac']);
				$dataTel1 = $db->dbEscape(trim($_POST['telefono1']));
				$dataTel2 = $db->dbEscape(trim($_POST['telefono2']));
				$dataSexo = $db->dbEscape($_POST['sexo']);
				$dataECiv = $db->dbEscape($_POST['estciv']);
				// $dataCLab = $db->dbEscape($_POST['certlab']);
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
				$db->dbDisconnect();
				// STAP! VALIDATION TIME
				// apellido - obligatorio maxlength 45
				if ($dataApellido=="") $validationErrors['apellido'] = "El Apellido es obligatorio";
				if (strlen($dataApellido)>45) 
					$validationErrors['apellido'] = "El campo Apellido puede contener como máximo 45 caracteres";
				// nombre - obligatorio maxlength 45
				if ($dataNombre=="") 
					$validationErrors['nombre'] = "El Nombre es obligatorio";
				if (strlen($dataNombre)>45) 
					$validationErrors['nombre'] = "El campo Nombre puede contener como máximo 45 caracteres";
				// nacionalidad - seteado y mayor a 0
				if ($dataNacionalidad=="0" || $dataNacionalidad=="") 
					$validationErrors['nacionalidad'] = "La Nacionalidad es obligatoria";
				// f.nacim		obligatoria. no mayor a 100 anos ni menor a 5 anios
				if (!$lib->validateDate($dataFNac)) {
					$validationErrors['fnac'] = "La Fecha de Nacimiento es inválida";
				} else {
			    	$d = $lib->datediff($dataFNac);
			    	if ($d > (365* 100)) $validationErrors['fnac'] = "La Fecha de Nacimiento es inválida (>100)";
			    	if ($d < (365 * 5)) $validationErrors['fnac'] = "La Fecha de Nacimiento es inválida (BenjaminButton)";
			    	if ($d < 0) $validationErrors['fnac'] = "La Fecha de Nacimiento es inválida (futura)";
				}
				// telefono fijo obligatorio. entre 6 y 20 caracteres. solo numeros, espacios, parentesis y guion
				if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTel1)) 
					$validationErrors['telefono1'] = "El Teléfono Fijo contiene caracteres inválidos";
				if (strlen($dataTel1) < 6 || strlen($dataTel1) > 20) 
					$validationErrors['telefono1'] = "El Teléfono Fijo es obligatorio";
				// telefono celular. si no es blanco, debe contener entre 6 y 20 caracteres. 
				//solo numeros, espacios, parentesis y guion
				if ($dataTel2!="") {
					if (strlen($dataTel2) < 6 || strlen($dataTel2) > 20) 
						$validationErrors['telefono2'] = "El Teléfono Celular debe contener entre 6 y 20 caracteres";
					if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTel2)) 
						$validationErrors['telefono2'] = "El Teléfono Celular contiene caracteres inválidos";
				}
				// sexo 		seteado y mayor a 0
				if ($dataSexo=="0" || $dataSexo=="") 
					$validationErrors['sexo'] = "El campo Sexo es obligatorio";
				// estado civil	seteado y mayor a 0
				if ($dataECiv=="0" || $dataECiv=="") 
					$validationErrors['estciv'] = "El Estado Civil es obligatorio";
				// calle 	obligatorio. maxlength 45
				if ($dataCalle=="") 
					$validationErrors['direccion'] = "El campo Calle es obligatorio";
				if (strlen($dataCalle)>45) 
					$validationErrors['direccion'] = "El campo Calle puede contener como máximo 45 caracteres";
				// numero 	obligatorio. maxlength 45
				if ($dataNro=="") 
					$validationErrors['numero'] = "El campo Número es obligatorio";
				if (strlen($dataNro)>45) 
					$validationErrors['numero'] = "El campo Número puede contener como máximo 45 caracteres";
				// entre calles 	opcional. maxlength 45
				if (strlen($dataEntre)>45) 
					$validationErrors['entrecalles'] = "El campo Entre puede contener como máximo 45 caracteres";
				// provincia 	obligatorio	seteado y mayor a 0
				if ($dataProv =="0" || $dataProv=="") $validationErrors['provincia'] = "El campo Provincia es obligatorio";
				// localidad	obligatorio	seteado y mayor a 0
				if ($dataLocal =="0" || $dataLocal=="") $validationErrors['localidad'] = "El campo Localidad es obligatorio";
				// piso 	opcional. maxlength 45
				if (strlen($dataPiso)>45) 
					$validationErrors['piso'] = "El campo Piso puede contener como máximo 45 caracteres";
				// depto 	opcional. maxlength 45
				if (strlen($dataDepto)>45) 
					$validationErrors['depto'] = "El campo Depto puede contener como máximo 45 caracteres";
				// codigo postal 	obligatorio. maxlength 45
				if ($dataCP=="") 
					$validationErrors['codpostal'] = "El campo Cod.Postal es obligatorio";
				if (strlen($dataCP)>45) 
					$validationErrors['codpostal'] = "El campo Cod.Postal puede contener como máximo 45 caracteres";

				// titulo 	opcional. maxlength 45
				if (strlen($dataTitulo)>45) 
					$validationErrors['titulo'] = "El campo Título puede contener como máximo 45 caracteres";
				// otorgado por 	opcional. maxlength 45
				if (strlen($dataOtorga)>45) 
					$validationErrors['otorgadopor'] = "El campo Otorgado Por puede contener como máximo 45 caracteres";
				// ano egreso opcional. numerico entre 1930 y el año actual
				if (trim($dataAnoEgr)!="") {
					if (is_int($dataAnoEgr)) {
						if (intval($dataAnoEgr) < 1930) 
							$validationErrors['anoegreso'] = "El campo Año Egreso no puede ser anterior a 1930";
						if (intval($dataAnoEgr) > date("Y")) 
							$validationErrors['anoegreso'] = "El campo Año Egreso no puede ser posterior a " . date("Y");
					}
				}

				// obra social 			opcional maxlength 45
				if (strlen($dataOSoc)>45) 
					$validationErrors['obrasocial'] = "El campo Obra Social puede contener como máximo 45 caracteres";
				// contacto emergencia 	obligatorio maxlength 45
				if ($dataCont=="") 
					$validationErrors['contactoemergencia'] = "El campo Contacto Emergencia es obligatorio";
				if (strlen($dataCont)>45) 
				$validationErrors['contactoemergencia'] = "El campo Contacto Emergencia puede contener como máximo 45 caracteres";
				// telef emergencia 	obligatorio maxlength 45
				if ($dataTele=="") 
					$validationErrors['telefono'] = "El campo Teléfono Contacto es obligatorio";
				if (strlen($dataTele)>45) 
					$validationErrors['telefono'] = "El campo Teléfono Contacto puede contener como máximo 45 caracteres";
				if ($dataTele!="") {
					if (! preg_match("/^[0-9\s\(\)\-]{6,20}$/", $dataTele)) 
					$validationErrors['telefono'] = "El Teléfono Contacto contiene caracteres inválidos";
				}
				// enfermedades 		opcional maxlength 500
				if (strlen($dataEnfe)>500) 
					$validationErrors['enfermedades'] = "El campo Enfermedades puede contener como máximo 500 caracteres";

				// email 	obligatorio maxlength 45 a@b.c
				if ($dataEMail=="") 
					$validationErrors['email'] = "El campo Email es obligatorio";
				if (strlen($dataEMail)>45) 
					$validationErrors['email'] = "El campo Email puede contener como máximo 45 caracteres";
				if (!filter_var($dataEMail, FILTER_VALIDATE_EMAIL)) 
					$validationErrors['email'] = "El campo Email es inválido";
				// password 	obligatorio maxlength 45 - deben coincidir password1 y password2 minlength 6
				if ($dataPass1=="") { 
					$validationErrors['password1'] = "El campo Contraseña es obligatorio";
				} else 
				{
					if (! $lib->isPasswordValid($dataPass1)) 
					$validationErrors['password1'] = "Contraseña inválida. " . $this->POROTO->Config['password_constraints_explained'];

					if (strlen($dataPass1) > 45) {
						$validationErrors['password1'] = "El campo Contraseña puede contener como máximo 45 caracteres";
					} else 
					{
						if ($dataPass1 != $dataPass2) {
							$validationErrors['password2'] = "El campo Contraseña no coincide con su validación";
						}
					}

				} //if ($dataPass1=="") { 

				// observaciones 	opcional. maxlength 5000
				if (strlen($dataObs) > 5000) 
				$validationErrors['observaciones'] = "El campo Observaciones puede contener como máximo 5000 caracteres. Contiene " . strlen($dataObs);

				// CARRERAS. Tiene que tener al menos una
				$nc = (isset($_POST['new-carreras']) ? $_POST['new-carreras'] : array());
				if (count($nc) < 1) 
					$validationErrors['carreras'] = "Debe tener al menos una carrera registrada";

				//Entro a guardar insertando persona alumno etc.
				if (count($validationErrors) == 0) {  
					$db->dbConnect("home/Inscripcion");

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
					$sqlP.= ",'" . $dataApellido . "'";
					$sqlP.= ",'" . $dataNombre . "'";
					$sqlP.= "," . $dataTipDoc;
					$sqlP.= "," . $dataNroDoc;
					$sqlP.= ",null";
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
					$sqlP.= ",null";
					$sqlP.= ",null";
					$sqlP.= ",1";
					$sqlP.= ",'" . $this->POROTO->Config['usuario_auditoria_sitio_publico'] . "'";
					$sqlP.= ",CURRENT_TIMESTAMP";
					$sqlP.= ",null";
					$sqlP.= ",null";

					$db->begintrans();
					$newIdPersona = $db->insert($sqlP, '', true);
					$bOk = $newIdPersona;

					$sqlA = "INSERT INTO alumnos (idpersona,titulosecundario,otorgadopor,aniooegreso,";
					$sqlA.= "estadoalumno_id,certificadotrabajo,observaciones,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
					$sqlA.= $newIdPersona;
					$sqlA.= ",'" . $dataTitulo . "'";
					$sqlA.= ",'" . $dataOtorga . "'";
					$sqlA.= "," . ($dataAnoEgr!='' ? $dataAnoEgr : 'null');
					$sqlA.= "," . $this->POROTO->Config['estado_alumno_ingresante_id'];
					$sqlA.= ",0"; 
					$sqlA.= ",'" . $dataObs . "'";
					$sqlA.= ",'" . $this->POROTO->Config['usuario_auditoria_sitio_publico'] . "'";
					$sqlA.= ",CURRENT_TIMESTAMP ";
					$sqlA.= ",NULL ";
					$sqlA.= ",NULL ";
					$sqlA.= " ON DUPLICATE KEY UPDATE ";
					$sqlA.= "titulosecundario='" . $dataTitulo . "'";
					$sqlA.= ",otorgadopor='" . $dataOtorga . "'";
					$sqlA.= ",aniooegreso=" . ($dataAnoEgr!='' ? $dataAnoEgr : 'null');
					$sqlA.= ",estadoalumno_id=" . $this->POROTO->Config['estado_alumno_cargado_id'];
					$sqlA.= ",observaciones='" . $dataObs . "'";
					$sqlA.= ",usumodi='" . $this->POROTO->Config['usuario_auditoria_sitio_publico'] . "'";
					$sqlA.= ",fechamodi=CURRENT_TIMESTAMP";
					if ($bOk!==false) $bOk = $db->insert($sqlA, '', true);

					$sqlF = "INSERT INTO fichamedica (idpersona,obrasocial,contactoemergencia,telefono,enfermedades) SELECT ";
					$sqlF.= $newIdPersona;
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

					$sqlU = "INSERT INTO usuarios (idpersona,usuario,email,password,estado,";
					$sqlU.= "primeracceso,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
					$sqlU.= $newIdPersona;
					$sqlU.= ",'" . $userName . "'";
					$sqlU.= ",'" . $dataEMail . "'";
					$sqlU.= ",'" . $dataPass1 . "'";
					$sqlU.= ",0";
					$sqlU.= ",1";
					$sqlU.= ",'" . $this->POROTO->Config['usuario_auditoria_sitio_publico'] . "'";
					$sqlU.= ",CURRENT_TIMESTAMP";
					$sqlU.= ",null";
					$sqlU.= ",null";
					if ($bOk!==false) $bOk = $db->insert($sqlU, '', true);


					$sqlPR = "INSERT INTO personarol (idpersona,idrol,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
					$sqlPR.= $newIdPersona;
					$sqlPR.= "," . $this->POROTO->Config['rol_usuario_id'];
					$sqlPR.= ",'" . $this->POROTO->Config['usuario_auditoria_sitio_publico'] . "'";
					$sqlPR.= ",CURRENT_TIMESTAMP";
					$sqlPR.= ",null";
					$sqlPR.= ",null";
					if ($bOk!==false) $bOk = $db->insert($sqlPR, '', true);



					$sqlC = array();
					foreach ($nc as $carrera) {
						$a = explode("~**~", $carrera);
						if (count($a)==5) { //vienen: carrera, area, nivel, instrumento, fecha (d/m/y)
							$carreraNombre = "";
							$carreraDescripcion = "";
							$areaNombre = "";
							$nivelNombre = "";
							$instrumentoDescripcion = "";

							$sql = "INSERT INTO alumnocarrera (idpersona,idcarrera,idinstrumento,idarea,fechainscripcion,";
							$sql.= "fechafinalizada,idnivel,estado,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
							$sql.= $newIdPersona;
							$sql.= "," . $a[0];
							$sql.= "," . $a[3];
							$sql.= "," . $a[1];
							$sql.= ",'" . $lib->dateDMY2YMD($a[4]) . "'";
							$sql.= ",null";
							$sql.= "," . $a[2];
							$sql.= "," . $this->POROTO->Config['estado_carrera_en_curso_id'];
							$sql.= ",'" . $this->POROTO->Config['usuario_auditoria_sitio_publico'] . "'";
							$sql.= ",CURRENT_TIMESTAMP ";
							$sql.= ",null";
							$sql.= ",null";
							$sqlC[] = $sql;
						}
					} //For

					foreach ($sqlC as $sqlCarrera) {
						if ($bOk!==false) $bOk = $db->insert($sqlCarrera, '', true);
					}
					if ($bOk === false) {
						$db->rollback();
						header("Location: /inscripcion?e=3", TRUE, 302);
						exit();

					} else {
						$db->commit();
					}
					
					$sql = "select descripcion from localidades where id=" . $dataLocal;
					$loc = $db->getSQLArray($sql);
					if (count($loc)==1) $localidad = $loc[0]['descripcion']; else $localidad="";
					$sql = "select descripcion from carreras where idcarrera=" . $a[0];
					$carre = $db->getSQLArray($sql);
					$sql = "select nombre from instrumentos where idinstrumento =" . $a[3];
					$ins = $db->getSQLArray($sql);
					if (count($ins)==1) $instrumento = $ins[0]['nombre']; else $instrumento="";
					$sql = "select descripcion from carreraniveles where id=" . $a[2];
					$arrCN = $db->getSQLArray($sql);
					if (count($arrCN)==1) $nivelNombre = $arrCN[0]['descripcion']; else $nivelNombre = "";

					$db->dbDisconnect();

					if ($this->POROTO->Config['override_mail_address'] != "") $mailto = $this->POROTO->Config['override_mail_address']; else $mailto = $dataEMail;
                                        $mailSubject = $this->POROTO->Config["empresa_descripcion"]." - Inscripcion completada";

					foreach ($viewDataTipoDocumento as $td) if ($td['id']==$dataTipDoc) $tdDesc = $td['descripcion'];
					$mailBody = file_get_contents($this->POROTO->MailPath . $this->POROTO->Config['path_to_mail_inscripcion']);
					$mailBody = str_replace($this->POROTO->Config['mail_nombre_replace_string'], mb_strtoupper(trim($_POST['apellido']), 'UTF-8') . "," . mb_strtoupper(trim($_POST['nombre']), 'UTF-8'), $mailBody);
					$mailBody = str_replace($this->POROTO->Config['mail_documento_replace_string'], $tdDesc . " " . $dataNroDoc, $mailBody);
					$mailBody = str_replace($this->POROTO->Config['mail_domicilio_replace_string'], mb_strtoupper(trim($_POST['direccion']), 'UTF-8') . " " . mb_strtoupper(trim($_POST['numero']), 'UTF-8') . " " . mb_strtoupper(trim($_POST['piso']), 'UTF-8') . " " . mb_strtoupper(trim($_POST['depto']), 'UTF-8') . " - " . $localidad , $mailBody);
					$mailBody = str_replace($this->POROTO->Config['mail_instrumento_replace_string'], $instrumento, $mailBody);
					$mailBody = str_replace($this->POROTO->Config['mail_nivel_replace_string'], $nivelNombre, $mailBody);
					$mailBody = str_replace($this->POROTO->Config['mail_carrera_replace_string'], $carre[0]['descripcion'], $mailBody);


					$lib->sendMail($mailto, $mailSubject, $mailBody);
					$message = "La inscripción se completó con éxito. Se ha enviado un mail a su cuenta " . $mailto . " , informando como proseguir";
					include($this->POROTO->ViewPath . "/reempadronamiento-notificacion.php");
					exit();

				} //if (count($validationErrors) == 0) {  

			} // if (isset($_POST['email'], $_POST['nacionalidad'])) { //estoy volviendo de completar toda la info.

			$db->dbConnect("home/inscripcion");
			
			//buscar un nombre de usuario valido
			$userName = intval($_POST['nrodoc']);
			$sql = "select idpersona from usuarios where usuario='" . $userName . "'";
			$arr = $db->getSQLArray($sql);
			if (count($arr)>0) {
				for ($i=0; $i<26; $i++) {
					$extraChar = chr($i+65);
					$userName = intval($_POST['nrodoc']) . $extraChar;
					$sql = "select idpersona from usuarios where usuario='" . $userName . "'";
					$arr = $db->getSQLArray($sql);
					if (count($arr)==0) break;
				}
			}//if (count($arr)>0) {

			//Inicializo resto de los datos antes de mostrar el formulario de carga.
			$viewData = array(array('apellido'=>'', 'nombre'=>'',
			'nacionalidad'=>'', 'fnac_dmy'=>'',
			'tipodoc'=>$db->dbEscape($_POST['tipdoc']), 
			'descripcion'=>$db->dbEscape(intval($_POST['nrodoc'])),
			'documentonro'=>'', 'telefono1'=>'',
			'telefono2'=>'', 'sexo'=>'',
			'estadocivil'=>'', 'direccion'=>'', 
			'entrecalles'=>'', 'numero'=>'', 
			'piso'=>'', 'depto'=>'', 
			'idlocalidad'=>'', 'localidad_descripcion'=>'', 
			'idprovincia'=> '', 'codpostal'=> '',
			'certlab'=> '', 'titulosecundario'=> '',
			'otorgadopor'=> '', 'anoegreso'=> '',
			'obrasocial'=> '', 'contactoemergencia'=> '',
			'telefono'=> '', 'enfermedades'=> '',
			'usuario'=> $db->dbEscape($userName), 'password'=> '',
			'email'=> '', 'primeracceso'=> '',
			'observaciones'=> '', 'estadoalumno_id'=> '' ));

			$sql = "SELECT idcarrera,nombre,descripcion FROM carreras WHERE estado=1 order by nombre";
			$viewDataCarreras = $db->getSQLArray($sql);

			$sql = "select idarea,nombre from areas";
			$viewDataAreas = $db->getSQLArray($sql);

			$sql = "select idinstrumento,nombre,descripcion from instrumentos";
			$viewDataInstrumentos = $db->getSQLArray($sql);

			$sql = "select id,descripcion from provincias";
			$viewDataProvincias = $db->getSQLArray($sql);

			$idprov = (isset($_POST['provincia']) ? $_POST['provincia'] : $viewData[0]['idprovincia']);
			if ($idprov=="") $idprov=0;
			$sql = "select id,descripcion,cp from localidades where idprovincia=" . $idprov . " order by descripcion";
			$viewDataLocalidades = $db->getSQLArray($sql);

			$db->dbDisconnect();

			$viewDataSexo = $this->POROTO->Config['dominios']['sexo'];
			$viewDataNacionalidad = $this->POROTO->Config['dominios']['nacionalidad'];
			$viewDataEstadoCivil = $this->POROTO->Config['dominios']['estadocivil'];
			$viewDataAlumnoCarreras = array();

			$tempCarreras = isset($_POST['new-carreras']) ? $_POST['new-carreras'] : array();
			$newCarreras = array();

			foreach ($tempCarreras as $tempCarrera) {
				$arr = explode("~**~", $tempCarrera);
				if (count($arr)==5) { //vienen: carrera, area, nivel, instrumento, fecha (d/m/y)
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

					$db->dbConnect("home/inscripcion");
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
				}//if (count($arr)==5) {
			}//foreach ($tempCarreras as $tempCarrera) {

			$status = "inscripcion";
			$pageTitle="Inscripcion";
			include($this->POROTO->ViewPath . "/-header-no-session.php");
			include($this->POROTO->ViewPath . "/ver-alumno.php");
			include($this->POROTO->ViewPath . "/-footer.php");
		} else {
			//Muestro para ingresar el DNI del nuevo ingresante y luego validar si existe o no.
			$db->dbConnect("home/inscripcion");
		    $sql = "SELECT id,descripcion FROM tipodoc order by id";
			$viewDataTipoDocumento = $db->getSQLArray($sql);
			$db->dbDisconnect();

			include($this->POROTO->ViewPath . "/inscripcion.php");
		} //if (isset($_POST['tipdoc'], $_POST['nrodoc']) ) { 
	}




    
    public function ajaxsendmail() {
        $lib = & $this->POROTO->Libraries['siteLibrary'];
                
        $db = & $this->POROTO->DB;
        $idComision = $_POST["co"];
        $idCarrera = $_POST["ca"];
        $idPersona = $_POST["pe"];
        $idRol = $_POST["ro"];
            
        $db->dbConnect("home/ajaxsendmail/" . $idComision . "/" . $idPersona . "/" . $idRol);

        $sql = "select p.email  from alumnomateria am ";
        $sql .= "inner join estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria ";
        $sql .= "inner join comisiones c on c.idmateria=am.idmateria and c.idcomision=am.idcomision ";
        $sql .= "inner join usuarios p on am.idpersona = p.idpersona ";
        $sql .= "left join alumnomaterianota amn on amn.idalumnomateria = am.idalumnomateria and amn.idtipoexamen in (3,4) ";
        $sql .= "left join alumnocarrera ac on ac.idpersona=p.idpersona and ac.idcarrera= 5 ";
        $sql .= "left join instrumentos i on ac.idinstrumento=i.idinstrumento ";
        $sql .= "where c.idcomision=" . $idComision;
        $result = $db->getSQLArray($sql);
        $db->dbDisconnect();
        
        $mailto = "";
        
        foreach ($result as $anEmail => $email)
        {
            $mailto = $mailto . $email["email"].",";
        }
             
        if ($this->POROTO->Config["override_mail_address"] != "") 
             { $mailto = $this->POROTO->Config["override_mail_address"]; }
           
        $mailBody = $_POST["body"];
        $mailSubject = $this->POROTO->Config["empresa_descripcion"]." - Notificacion Comisiones";
        $mailto = $mailto.",".$_POST["sentoadd"];
        try
        {
            $lib->sendMail($mailto, $mailSubject, $mailBody);    
            $response = array ("msj" => "Correo enviado");
            echo json_encode($response);
        } catch (Exception $e ){
            $response = array ("msj" => "Error al enviar el correo: " . $e);
            echo json_encode($response);
        }   
    }

}


