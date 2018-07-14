<?php

class Examenes {
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

	public function gestion($idcarrera=0, $idmateria=0, $idcomision=0, $tipoexamen=0) { //OK
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		
		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Examenes Acceso desde Menu')){
  			    $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		$db->dbConnect("examenes/gestion/" . $idcarrera . "/" . $idmateria . "/" . $idcomision . "/" . $tipoexamen);
		$dataIdCarrera = $db->dbEscape($idcarrera);
		$dataIdMateria = $db->dbEscape($idmateria);
		$dataIdComision = $db->dbEscape($idcomision);
		$dataTipoExamen = $db->dbEscape($tipoexamen);
		
		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		$sql = "SELECT idcarrera id, descripcion FROM carreras 	WHERE estado=1";
		$viewDataCarreras = $db->getSQLArray($sql);
		
		
	    $sql = "SELECT idtipoexamen id,descripcion FROM tipoexamen order by id";
		$viewDataTipoExamen = $db->getSQLArray($sql);
		
		$sql = "select distinct year(now()) as ciclo union select distinct year(fecha) as ciclo from examenes where estado=1 order by ciclo asc";
		$viewDataCiclos = $db->getSQLArray($sql);

		$db->dbDisconnect();

		$pageTitle="Gestión de Exámenes";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/gestion-examenes.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}

	public function ajaxmateriasparainscripcion() { //OK
		$idAlumnoCarrera = $_POST['carrera'];
		$idAlumno  = $_POST['alumno'];
		
		//Busca la materia del alumno con estado CURSADA APROBADA o LIBRE y siempre y cuando no hayan pasado 5 años de la cursada
		//y existe un final previo de esa materia, lo muestro para inscribir.
		
		$db =& $this->POROTO->DB;
		$db->dbConnect("examenes/ajaxmateriasparainscripcion/" . $idAlumnoCarrera . "/" . $idAlumno);
                
                //Traigo examenes Previos
		$sql = "select m.idmateria, m.nombre, e.idexamen, date_format(e.fecha, '%d/%m/%Y %H:%i') fecha, ";
		$sql.= "e.cupo, count(ea.idpersona) alu, i.nombre as instrumento, ";
		$sql.= "sum(case when ea.idpersona=". $idAlumno . " then 1 else 0 end) yainscripto, "; 
		$sql.= " m.libre,am.idestadoalumnomateria ";
		$sql.= " from examenes e"; 
                $sql.= " left join examenalumno ea on ea.idexamen=e.idexamen";
		$sql.= " left join instrumentos i on e.idinstrumento=i.idinstrumento";
		$sql.= " inner join materias m on e.idmateria=m.idmateria";
		$sql.= " inner join alumnomateria am on am.idmateria=m.idmateria and am.idpersona=". $idAlumno . " and ";
		$sql.= " am.idestadoalumnomateria in (".$this->POROTO->Config['estado_alumnomateria_cursadaaprobada'].") and am.idalumnocarrera=".$idAlumnoCarrera;
		$sql.= " where e.idtipoexamen=1";
		$sql.= " and e.estado=1";
		$sql.= " and e.examenfinalizado=0";
		$sql.= " and am.aniocursada+5>=YEAR(CURDATE())"; //SE TIENE EN CUENTA QUE SI EL AÑO DE CURSADA ES HACE MAS DE 5 AÑOS, YA NO PERMITIR RENDIR FINAL.
		$sql.= " group by e.idexamen ";
		$sql.= " UNION ALL ";
                //Traigo examenes Libres
		$sql.= "select m.idmateria, m.nombre, e.idexamen, date_format(e.fecha, '%d/%m/%Y %H:%i') fecha, ";
		$sql.= "e.cupo, count(ea.idpersona) alu, i.nombre as instrumento, ";
		$sql.= "sum(case when ea.idpersona=". $idAlumno . " then 1 else 0 end) yainscripto, "; 
		$sql.= " m.libre,am.idestadoalumnomateria ";
		$sql.= " from examenes e"; 
                $sql.= " left join examenalumno ea on ea.idexamen=e.idexamen";
		$sql.= " left join instrumentos i on e.idinstrumento=i.idinstrumento";
		$sql.= " inner join materias m on e.idmateria=m.idmateria";
		$sql.= " inner join alumnomateria am on am.idmateria=m.idmateria and am.idpersona=". $idAlumno . " and ";
		$sql.= " am.idestadoalumnomateria in (".$this->POROTO->Config['estado_alumnomateria_libre'].") and am.idalumnocarrera=".$idAlumnoCarrera;
		$sql.= " where e.idtipoexamen=6";
		$sql.= " and e.estado=1";
		$sql.= " and e.examenfinalizado=0";
		$sql.= " and am.aniocursada+5>=YEAR(CURDATE())"; //SE TIENE EN CUENTA QUE SI EL AÑO DE CURSADA ES HACE MAS DE 5 AÑOS, YA NO PERMITIR RENDIR FINAL.
		$sql.= " group by e.idexamen ";
                
                $sql.= " order by 2,4";

		$result = $db->getSQLArray($sql);
		

		$db->dbDisconnect();

		echo json_encode($result);
	}

	public function ajaxmaterias($idCarrera, $idProfesor = 0) { //OK
		$db =& $this->POROTO->DB;
		$db->dbConnect("examenes/ajaxmaterias/" . $idCarrera . "/" . $idProfesor);

		if ($idProfesor == 0) {
			$sql = "select m.idmateria id, m.materiacompleta as nombre";
			$sql.= " from viewmaterias m";
			$sql.= " where m.idcarrera=" . $db->dbEscape($idCarrera);
			$sql.= " and m.estado=1";
			$sql.= " order by m.orden";
		} else {
			$sql = "SELECT DISTINCT m.idmateria id, m.materiacompleta as nombre FROM comprofesor cp";
			$sql.= " INNER JOIN comisiones c ON cp.idcomision=c.idcomision"; 
			$sql.= " INNER JOIN viewmaterias m ON m.idmateria=c.idmateria";
			$sql.= " WHERE c.estado=1 AND m.estado=1 AND cp.idpersona=" . $idProfesor;
			$sql.= " and m.idcarrera=" . $db->dbEscape($idCarrera);
			$sql.= " order by m.orden";
		}
		$result = $db->getSQLArray($sql);
		$db->dbDisconnect();

		echo json_encode($result);
	}
	
	public function ajaxcomisiones($idMateria, $anio,$idProfesor = 0) { //OK
		$db =& $this->POROTO->DB;
		$db->dbConnect("examenes/ajaxcomisiones/" . $idMateria . "/" . $anio ."/" . $idProfesor);
                
                //Agregado inner con tabla comhorario para traer los horarios de las comisiones
		if ($idProfesor == 0) {
			$sql = "SELECT c.idcomision id, c.nombre, ch.inicio hora";
			$sql.= " FROM comisiones c";
                        $sql.= " INNER JOIN comhorario ch ON c.idcomision=ch.idcomision";
			$sql.= " WHERE c.estado=1 and c.idmateria=" . $idMateria;
                        if($anio!=0) { $sql.=" and c.anio=".$anio;}
                        $sql.= " GROUP BY id";
			$sql.= " ORDER BY 2";

		} else {
			$sql = "SELECT c.idcomision id, c.nombre, ch.inicio hora FROM comprofesor cp"; 
			$sql.= " INNER JOIN comisiones c ON cp.idcomision=c.idcomision";
                        $sql.= " INNER JOIN comhorario ch ON c.idcomision=ch.idcomision";
			$sql.= " WHERE c.estado=1 AND cp.idpersona=" . $idProfesor . " AND c.idmateria=" . $idMateria;
                        if($anio!=0) {$sql.=" and c.anio=".$anio; }
                        $sql.= " GROUP BY id";
                        $sql.= " ORDER BY 2";
		}
		$result = $db->getSQLArray($sql);
		$db->dbDisconnect();
                // convierto el string dateTime en HH:MM para el campo de horario.
                foreach ($result as $key => $comision) {
                        $datetime = $result[$key]["hora"];
                        $date = strtotime($datetime);
                        $result[$key]["hora"] = date("H:i", $date);
                }
		echo json_encode($result);
	}

	public function ajaxexamenes($ciclo, $tipoexamen, $carrera, $materia, $comision=0, $finalizado=0, $idProfesor = 0) { //OK
		$db =& $this->POROTO->DB;
		$db->dbConnect("examenes/ajaxexamenes/" . $ciclo . "/" . $tipoexamen . "/" . $carrera . "/" . $materia . "/" . $comision . "/" . $idProfesor );
		
		$sql = "select distinct e.idexamen,  m.materiacompleta materia, c.nombre comision, ";
		$sql.= "te.nombre tipexa, e.fecha, e.examenfinalizado, ";
		$sql.= "e.idtipoexamen, i.nombre as instrumento ";
		$sql.= " from examenes e ";
		$sql.= " inner join viewmaterias m on m.idmateria=e.idmateria";
		$sql.= " left join examenprofesor ep on ep.idexamen=e.idexamen";
		$sql.= " inner join tipoexamen te on te.idtipoexamen=e.idtipoexamen";
		$sql.= " left join instrumentos i on e.idinstrumento=i.idinstrumento ";
		$sql.= " left join comisiones c on e.idcomision=c.idcomision";
		$sql.= " where 1=1 ";
		$sql.= " and e.estado=1 ";
		if ($idProfesor != 0) $sql.= " and ep.idpersona=" . $idProfesor;
		if ($tipoexamen != 0) $sql.= " and e.idtipoexamen=" . $tipoexamen;
		if ($finalizado != 0) $sql.= " and e.examenfinalizado=" . ($finalizado>0 ? "0" : "1");
		if ($carrera != 0) $sql.= " and m.idcarrera=" . $carrera;
		if ($materia != 0) $sql.= " and m.idmateria=" . $materia;
		if ($comision!=0) $sql .= " and c.idcomision=" . $comision;
		if ($ciclo != 0) $sql.= " and year(e.fecha)=" . $ciclo;
		$sql.= " ORDER BY m.idcarrera,m.orden,c.nombre,e.fecha";
		$arrData = $db->getSQLArray($sql);

		$db->dbDisconnect();
		echo json_encode($arrData);
	}

	public function crear() { //OK
		$validationErrors = array();
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Examenes Agregar o Modificar')){
  			    $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /gestion-examenes/", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		$db->dbConnect("examenes/crear");
		$newProfesores = array();

		if (isset($_POST['tipexa'])) { //Vengo a modificar
			$dataTipoExamen = $db->dbEscape($_POST['tipexa']);
			$dataCarrera = $db->dbEscape($_POST['carrera']);
			$dataMateria = $db->dbEscape($_POST['materia']);
			$dataComision = (isset($_POST['comision']) ? $db->dbEscape($_POST['comision']) : 0);
			$dataNombre = mb_strtoupper($db->dbEscape(trim($_POST['nombre'])), 'UTF-8');
			$dataFecha = $db->dbEscape($_POST['fecha']);

			$dataHora = $db->dbEscape($_POST['hora']);
			$dataCupo = $db->dbEscape(intval($_POST['cupo']));
			$dataAula = mb_strtoupper($db->dbEscape(trim($_POST['aula'])), 'UTF-8');
			$dataInstrumento = $db->dbEscape($_POST['instrumento']);
			$dataArea = $db->dbEscape($_POST['area']);
			
			// Validaciones ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			// tipo exmaen - seteado y mayor a 0
			if ($dataTipoExamen=="0" || $dataTipoExamen=="") $validationErrors['tipexa'] = "El tipo de exámen es obligatorio";
			// carrera - seteado y mayor a 0
			if ($dataCarrera=="0" || $dataCarrera=="") $validationErrors['carrera'] = "La Carrera es obligatoria";
			// materia - seteado y mayor a 0
			if ($dataMateria=="0" || $dataMateria=="") $validationErrors['materia'] = "La materia es obligatoria";
			// comision -
			//La comision es obligatoria para Cuatrimestres y Promocion.
			//Para examenes regulares puedo dirigirlo a una comision en particular o hacerlo global a todas.
			if ($dataTipoExamen=="3" || $dataTipoExamen=="4" || $dataTipoExamen=="5"){
			//LOs examenes 1 parcial 2 parcial y final promocion requieren comision
			if ($dataComision=="0" || $dataComision=="") 
			$validationErrors['comision'] = "La comisión es obligatoria para un exámen del tipo Cuatrimestrales o Promoción";				
			}
			if (!$lib->validateDate($dataFecha)) {
                            $validationErrors['fecha'] = "La Fecha es inválida";
			} 
			else {
				$d = $lib->datediff($dataFecha);
				if ($d > (365*10)) $validationErrors['fecha'] = "La Fecha es inválida (>10)";
				if ($d < (365*-10)) $validationErrors['fecha'] = "La Fecha es inválida (<10)";
			}
                        
			//cupo obligatorio en final libre y previo
                        if(($dataTipoExamen==1 || $dataTipoExamen==6) && $dataCupo==0) $validationErrors['cupo'] = "El campo Cupo es obligatorio";
                        
			if (isset($_POST['profesores'])) { 
				if (count($_POST['profesores'])==0) $validationErrors['profesores'] = "Debe tener cargado al menos un profesor";
				//por cada cupo, recreo los valores en $newProfesores para el repost
				foreach ($_POST['profesores'] as $profesor) { 
					$arr = explode("~**~", $profesor);
					if (count($arr)==2) {
						$newProfesores[]=array("idpersona"=>$arr[0], 
						                  "apellidonombre"=>$arr[1]);
					} else {
						$validationErrors['profesores'] = "Se detectaron valores inválidos en el listado de profesores";						
					}
				}
			} else {
				$validationErrors['profesores'] = "Debe tener cargado al menos un profesor";
			}

			if($dataTipoExamen==5){ 			//Valido el tipo de examen promocion.
                                $sql = "select valor from configuracion where parametro='promocion_automatizada'";
                                $arrConfigDb = $db->getSQLArray($sql);
                                if (count($arrConfigDb) == 1 && $arrConfigDb[0]['valor'] == 'Y') {
                                            $validationErrors['tipexa'] = "El tipo de examen PROMOCION esta deshabilitado por estar el parametro PROMOCION_AUTOMATIZADA habilitado";
                                }
				$sql = "select promocionable from materias where idmateria=" . $dataMateria;
				$arr = $db->getSQLArray($sql);
				if($arr[0]['promocionable']!="1") 
				$validationErrors['materia'] = "La materia elegida NO es Promocionable. No es posible crear examenes por promoción";
			}
			
			//Valido que no exista un examen con las mismas caracteristicas sin estar finalizado.
			if (count($validationErrors)==0) {
				if($dataTipoExamen==3 || $dataTipoExamen==4 || $dataTipoExamen==5){ //1 2 parcial o promocion
                                    $sql="select * from examenes where idtipoexamen=" . $dataTipoExamen	;
                                    if($dataComision!=0)	$sql.=" and idcomision= " . $dataComision;
                                    $sql.=" and estado=1";
                                    $sql.=" and year(fecha)=year('".$lib->dateDMY2YMD($dataFecha)."')";
                                }
                                if($dataTipoExamen==1 || $dataTipoExamen==2 || $dataTipoExamen==6){ //Finales
                                    $sql="select * from examenes where idtipoexamen=" . $dataTipoExamen	;
                                    if($dataMateria!=0)	$sql.=" and idmateria= " . $dataMateria;
                                    if($dataComision!=0)	$sql.=" and idcomision= " . $dataComision;
                                    if($dataInstrumento!=0){	
                                                    $sql.=" and idinstrumento= " . $dataInstrumento;
                                            }else{
                                                    $sql.=" and idinstrumento=0 ";
                                    }
                                    if($dataArea!=0)	$sql.=" and idarea= " . $dataArea;
                                    $sql.=" and estado=1";
                                    $sql.=" and examenfinalizado=0 ";
                                }
                                
				$arr = $db->getSQLArray($sql);
				if(count($arr)>0) 
				$validationErrors['materia'] = "Ya existe un examen con las mismas características del que intenta crear. Verifique y borre los examenes que no correspondan.";
			}
			
			//Inserción +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			if (count($validationErrors)==0) {
				
				$db->begintrans();
				$bOk=true; //Variable usada para el manejo de transacciones.
				
				//Cambio Leo 20170626 se agrego campo estado en la insercion
				$sql = "INSERT INTO examenes (idtipoexamen, idcomision, idmateria, ";
				$sql.= " idinstrumento, idarea, nombre, fecha,";
				$sql.= " aula, cupo, examenfinalizado, estado, usucrea, fechacrea, usumodi, fechamodi) ";
				$sql.= " SELECT " . $dataTipoExamen;
				$sql.= "," . $dataComision;
				$sql.= "," . $dataMateria;
				$sql.= "," . $dataInstrumento;
				$sql.= "," . $dataArea;
				$sql.= ",'" . $dataNombre . "'";
				$sql.= ",'" . $lib->dateDMY2YMD($dataFecha) . " " . $dataHora . "'";
				$sql.= ",'" . $dataAula . "'";
				$sql.= "," . $dataCupo;
				$sql.= ",0";
				$sql.= ",1"; //estado activo
				$sql.= ",'" . $ses->getUsuario() . "'";
				$sql.= ",CURRENT_TIMESTAMP";
				$sql.= ",null";
				$sql.= ",null";
				$newIdExamen = $db->insert($sql,'',true);
				$bOk = $newIdExamen;

				foreach ($newProfesores as $profesor) {
					$sql = "INSERT INTO examenprofesor (idexamen, idpersona) SELECT ";
					$sql.= $newIdExamen . "," . $profesor['idpersona'];
					if ($bOk!==false) $bOk = $db->insert($sql,'',true);
				}

				//1° Cuatrimestre y 2° Cuatrimestre. crea el examen para todos los alumnos de la comision insertando 
				if ($dataTipoExamen=="3" || $dataTipoExamen=="4") {
					//traigo los alumnos de la comisión pero solo los que 
					//esten en estado CURSANDO (Cambio Leo 38 20170710)
					$sql = "INSERT INTO examenalumno (idexamen, idpersona, nota, libro, ";
					$sql.= "tomo, folio, usucrea, fechacrea, usumodi, fechamodi) ";
					$sql.= "SELECT ".$newIdExamen.",ca.idpersona,null,null,null,null,";
					$sql.= "'" . $ses->getUsuario() . "',";
					$sql.= "CURRENT_TIMESTAMP,null,null ";
					$sql.= "FROM comalumno ca ";
					$sql.= "left join alumnomateria am on ca.idpersona=am.idpersona and ca.idcomision=am.idcomision ";
					$sql.= "WHERE ca.estado=1 AND ca.idcomision=".$dataComision." and ";
					$sql.= "am.idestadoalumnomateria=".$this->POROTO->Config['estado_alumnomateria_cursando'];
					if ($bOk!==false) $bOk = $db->insert($sql,'',true);
				}
				
				//Final Regular
				if ($dataTipoExamen=="2") { 
				//Cambio 39 leo 20170711 
				//Final regular: este final se da a fin de año unicamente, en diciembre
				//El final puede estar asociado a una comision o bien a la materia en forma global.
				//Dependiendo de ello voy a inscribir automaticamente a todos los alumnos o bien solo a los de la comisión elegida.
				//Esto es una definición de ellos.
                                $partesFecha = explode('/',$db->dbEscape($_POST['fecha']));
                                $dataAnioExamen=$partesFecha[2];
				$sql = "INSERT INTO examenalumno (idexamen, idpersona, nota, ";
				$sql.= "libro, tomo, folio, usucrea, fechacrea, usumodi, fechamodi) ";
				$sql.= "SELECT ".$newIdExamen.",am.idpersona,null,null,null,null,";
				$sql.= "'".$ses->getUsuario()."'";
				$sql.= ",CURRENT_TIMESTAMP";
				$sql.= ",null";
				$sql.= ",null ";
				$sql.= "FROM alumnomateria am left join comisiones c on ";
				$sql.= "(am.idcomision = c.idcomision and am.idmateria=c.idmateria) ";
				$sql.= "where am.idestadoalumnomateria=".$this->POROTO->Config['estado_alumnomateria_cursadaaprobada']." ";
				$sql.= " and c.anio=".$dataAnioExamen;
				$sql.= " and c.estado=1 ";
				$sql.= " and am.idmateria=".$dataMateria;
				if ($dataComision!=0)	$sql.=  " and am.idcomision=".$dataComision;

				if ($bOk!==false) $bOk = $db->insert($sql,'',true);				
				//Fin Cambio 39 leo 20170711 
				}

				//Para la materia promocionable, una vez cargadas las notas del segundo parcial, el profesor debe crear el tipo de examen “PROMOCION” con una fecha. 
				//Al momento que el profesor tiene todo el listado de los que promocionaron, genera este examen y automáticamente se inscribe a los alumnos que cumplan
				// con los requisitos de la promoción en dicho examen. Después el profesor entrará a ese examen y cargara las notas de los alumnos.
				if ($dataTipoExamen == "5") {
					$sql ="select am.idpersona,p.apellido,";
					$sql.="sum(case when amn.idtipoexamen=3 and amn.notaexamen >=" . $this->POROTO->Config['nota_parcial_aprobado_materia_no_promocionable'] ;
					$sql.=" then 1 else 0 end) primerparcialaprobado,";
					$sql.=" sum(case when amn.idtipoexamen=4 and amn.notaexamen >=" . $this->POROTO->Config['nota_segundo_parcial_aprobado_materia_promocionable'];
					$sql.=" then 1 else 0 end) segundoparcialaprobado,";
					$sql.="max(case when amn.idtipoexamen=3 then amn.notaexamen  else 0 end) primerparcialmax,";
					$sql.="max(case when amn.idtipoexamen=4 then amn.notaexamen  else 0 end) segundoparcialmax,";
					$sql.="(max(case when amn.idtipoexamen=3 then amn.notaexamen  else 0 end) + ";
					$sql.="max(case when amn.idtipoexamen=4 then amn.notaexamen  else 0 end)) / 2 promedio ";
					$sql.="from alumnomateria am inner join alumnomaterianota amn on amn.idalumnomateria=am.idalumnomateria ";
					$sql.="left join personas p on am.idpersona=p.idpersona where ";
					$sql.="am.idmateria=".$dataMateria." and am.idcomision=".$dataComision." ";
					$sql.="and am.idestadoalumnomateria=".$this->POROTO->Config['estado_alumnomateria_cursadaaprobada']." "; //Cursada aprobada
					$sql.="group by am.idpersona ";
					$sql.="having primerparcialaprobado>0 and segundoparcialaprobado>0 and promedio>=". $this->POROTO->Config['nota_segundo_parcial_aprobado_materia_promocionable'];

					$arrAlumnosInscriptosAutomaticos = $db->getSQLArray($sql);

					foreach ($arrAlumnosInscriptosAutomaticos as $alu) {
						$sql = "INSERT INTO examenalumno (idexamen, idpersona, nota, libro, tomo, folio, usucrea, fechacrea, usumodi, fechamodi)";
						$sql.= " SELECT " . $newIdExamen;
						$sql.= "," . $alu['idpersona'];
						$sql.= ", ".$alu['promedio'].", null, null, null";
						$sql.= ",'" . $ses->getUsuario() . "'";
						$sql.= ",CURRENT_TIMESTAMP";
						$sql.= ",null";
						$sql.= ",null";
						if ($bOk!==false) $bOk = $db->insert($sql,'',true);
					}
				}

                                
				if ($bOk!==false) {
					$db->commit();
					$ses->setMessage("Exámen creado con éxito.", SessionMessageType::Success);
				}else{
					$db->rollback();
					$ses->setMessage("Se produjo un error al intentar guardar en la base de datos.", SessionMessageType::TransactionError);
				}
				$db->dbDisconnect();
				header("Location: /gestion-examenes", TRUE, 302);              
				exit();

			} //validationErrors=0
		}

		//cargo el menu del  usuario (por rol)
		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		//cargo arrays tipos de examenes (contemplando que profesores solo pueden crear cierto tipo).
	    $sql = "SELECT idtipoexamen id,descripcion FROM tipoexamen";
		if($ses->tienePermiso('','Gestion de Examenes Profesor'))	
				    	$sql.= " WHERE idtipoexamen in (3,4,5)";
	    $sql.= " ORDER BY id";
		$viewDataTipoExamen = $db->getSQLArray($sql);
		
		//Cargo arrays de carreras
		if($ses->tienePermiso('','Gestion de Examenes Profesor')){	
		$sql = "SELECT distinct c.idcarrera id, c.descripcion FROM carreras c inner join carreramateria cm on c.idcarrera=cm.idcarrera ";
		$sql.= "inner join comisiones co on cm.idmateria=co.idmateria and co.estado=1 ";
		$sql.= "inner join comprofesor cp on co.idcomision=cp.idcomision WHERE c.estado=1 and cp.idpersona=".$ses->getIdPersona();
		$sql.= " group by c.idcarrera,c.descripcion";
		}
		else
		{
		$sql = "SELECT distinct c.idcarrera id, c.descripcion FROM carreras c WHERE c.estado=1";
		}
		$viewDataCarreras = $db->getSQLArray($sql);
		
		$sql = "select idarea,nombre from areas order by 2";
		$viewDataAreas = $db->getSQLArray($sql);

		$sql = "select idinstrumento,nombre from instrumentos order by 2";
		$viewDataInstrumentos = $db->getSQLArray($sql);

		$sql = "select p.idpersona, p.apellido, p.nombre from profesores pr inner join personas p on p.idpersona=pr.idpersona where p.estado=1 order by 2,3";
		$viewDataProfesores = $db->getSQLArray($sql);

		$db->dbDisconnect();
		
		//Cambio 38 Leo 20170710
		$idProfesor=0; //Envio el profesor por defecto para el nuevo examen si es que el creador es profesor
		if($ses->tienePermiso('','Gestion de Examenes Profesor')){
			$idProfesor=$ses->getIdPersona();
		}
		//Fin Cambio Leo
		$viewData = array(array('carrera'=>0,
								'materia'=>0,
								'tipexa'=>0,
								'fecha'=>date("d/m/Y"),
								'hora'=>"00:00",
								'cupo'=>"0",
								'comision'=>0,
								'nombre'=>"",
								'aula'=>"",
								'instrumento'=>0,
								'area'=>0,
								'idProfesor'=>$idProfesor,   //Edicion para mandar el primer profe por defecto.
			        ));
		// $modTit = 0;
		// $modPro = 0;
		// $modSup = 0;

		$status = "crear";
		$pageTitle="Crear Exámen";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/ver-examen.php");
		include($this->POROTO->ViewPath . "/-footer.php");

	}

	public function eliminar($idExamen) {
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$db->dbConnect("examenes/eliminar/" . $idExamen);
		$dataIdExamen = $db->dbEscape($idExamen);

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Examenes Agregar o Modificar')){
  			    $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /gestion-examenes", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706

		//Cambio LEO 20170623 logica para que no borre examenes que esten finalizados o bien con algun dato en inscriptos.
		$sql = "SELECT * FROM examenes e left join examenalumno ea on e.idexamen=ea.idexamen ";
		$sql.= " where ((not nota is null) or (not libro is null) or (not tomo is null) or (not folio is null) or examenfinalizado=1) ";
		$sql.= " and e.idExamen=" . $dataIdExamen;
		$consulta = $db->getSQLArray($sql);
		if (count($consulta)!=0) {
			$ses->setMessage("No es posible eliminar el Examen ya que posee información en sus inscriptos o bien esta finalizado.", SessionMessageType::TransactionError);
			header("Location: /gestion-examenes", TRUE, 302);
			exit();
		}
		//Fin Cambio Leo 
		
		$sql = "update examenes set estado=0 WHERE idexamen=" . $dataIdExamen;
		$db->update($sql);

		$db->dbDisconnect();

		$ses->setMessage("Exámen eliminado con éxito", SessionMessageType::Success);
		header("Location: /gestion-examenes", TRUE, 302);
	}

    private function getDatosExamen($idExamen) {
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $lib = & $this->POROTO->Libraries['siteLibrary'];
        $validationErrors = array();
        $db->dbConnect("examenes/getDatosExamen/" . $idExamen);
        $dataIdExamen = $db->dbEscape($idExamen);
        
        // Obtengo los profesores del examen.
		$sql= " select p.nombre,p.apellido from examenprofesor ep inner join ";
		$sql.=" personas p on ep.idpersona=p.idpersona ";
		$sql.=" where ep.idexamen=".$idExamen;
        $profesores = $db->getSQLArray($sql);
        
        // Se obtienen datos generales del examen.
        $sql = "select e.nombre, date_format(e.fecha, '%d/%m/%Y %H:%i') fecha, e.aula, e.cupo, e.examenfinalizado, te.nombre tipoexamen, e.idmateria, m.nombre materia, e.idcomision, c.nombre comision, te.idtipoexamen";
        $sql .= " from examenes e inner join tipoexamen te on e.idtipoexamen=te.idtipoexamen";
        $sql .= " inner join materias m on m.idmateria=e.idmateria";
        $sql .= " left join comisiones c on c.idcomision=e.idcomision";
        $sql .= " where e.estado=1 and e.idexamen=" . $dataIdExamen;
        $viewDataExamen = $db->getSQLArray($sql);
        
        // Armo el string de profesores.
        $viewDataExamen[0]['profesores'] = "";
         foreach ($profesores as $profesor) 
		$viewDataExamen[0]['profesores'] .= " | " . $profesor['apellido'] . "," . $profesor['nombre'];
	$viewDataExamen[0]['profesores'] = substr($viewDataExamen[0]['profesores'], 3);
        
        return $viewDataExamen;
    }

    private function getDatosAlumnos($idExamen) {
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $lib = & $this->POROTO->Libraries['siteLibrary'];
        $validationErrors = array();
        $db->dbConnect("examenes/getDatosExamen/" . $idExamen);
        $dataIdExamen = $db->dbEscape($idExamen);
        //Traigo los alumnos anotados
        $sql = "select p.idpersona, p.apellido, p.nombre, ea.nota, ea.libro, ea.tomo, ea.folio, ";
        $sql .= " p.documentonro, eam.descripcion as estadoalumnomateria";
        $sql .= " from examenalumno ea inner join personas p on ea.idpersona=p.idpersona";
        $sql .= " inner join examenes e on ea.idexamen=e.idexamen";
        $sql .= " inner join alumnomateria am on e.idmateria=am.idmateria and am.idpersona=ea.idpersona";
        $sql .= " inner join estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria";
        $sql .= " where ea.idexamen=" . $dataIdExamen;
        $sql .= " and am.idalumnomateria=";
        $sql .= "(select max(idalumnomateria) from alumnomateria am2 where am2.idpersona=ea.idpersona and am2.idmateria=e.idmateria) ";
        $sql .= " group by p.idpersona,p.apellido,p.nombre,ea.nota,ea.libro,ea.tomo,ea.folio,";
        $sql .= " p.documentonro";
        $sql .= " order by p.apellido,p.nombre asc";
        return $db->getSQLArray($sql);
    }

    private function getReportesAlumnos($viewDataAlumnos,$idTipoExamen){
		$notaAprobada=0;
		if($idTipoExamen==1 || $idTipoExamen==2 || $idTipoExamen==3 || $idTipoExamen==4 || $idTipoExamen==6){
					$notaAprobada=4;
		}else{ //Promocion
					$notaAprobada=7;
		}
        
		$reportes = array("aprobados" => 0, "presentes" => 0, "desaprobados" => 0, "ausentes" => 0);
        foreach ($viewDataAlumnos as $alu) {
            if($alu['nota']>0){
                $reportes['presentes']++;

                if($alu['nota'] >=$notaAprobada){
                    $reportes['aprobados']++;
                }else{
                    $reportes['desaprobados']++;
                }
            }else{
                    $reportes['ausentes']++;
            }
        }
        return $reportes;
    }
	
    public function cargarnotas($idExamen) {
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		$validationErrors = array();
		$db->dbConnect("examenes/cargarnotas/" . $idExamen);
		$dataIdExamen = $db->dbEscape($idExamen);

                // Arreglo con id de examenes finales.
                $soloFinales = array(1, 2, 5, 6);
                
		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Examenes Notas')){
  			    $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /gestion-examenes/", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706

                //Traigo los datos del examen
                $viewDataExamen =  $this->getDatosExamen($idExamen);
                
		if (isset($_POST['alumnos'])) { //Actualizacion de notas
			$alumnos=array();
			foreach ($_POST['alumnos'] as $alu) {
				$alu = explode("|", $alu);
				//Cambio Leo parciales no permtir coma
				if((intval($alu[1])>0 || intval($alu[1])<=10) && ($viewDataExamen[0]["idtipoexamen"]!=5)){
					//SOLO las promociones aceptan COMA, el resto todo numero entero.
					$resto=floatval($alu[1]) - intval($alu[1]);
					if(floatval($resto)>0){
						$validationErrors['alu' . intval($alu[0]) . '-nota'] = $alu[5] . ": Nota inválida. Un examen cuatrimestral o final no puede tener nota con decimales. Solo las promociones aceptan decimales.";
					}
				}
				//Fin Cambio Leo
			
				if (intval($alu[1])<0 || intval($alu[1])>10) $validationErrors['alu' . intval($alu[0]) . '-nota'] = $alu[5] . ": Nota inválida";

				$alu[1] = str_replace(",", ".", $alu[1]);
				$alumnos[$alu[0]] = array("id"=>intval($alu[0]), "nota"=>($alu[1]=="" ? "" : intval($alu[1]*100)/100), "libro"=>($alu[2]=="" ? "" : $alu[2]), "tomo"=>($alu[3]=="" ? "" :$alu[3]), "folio"=>($alu[4]=="" ? "" : $alu[4]));
			}

			if (count($validationErrors)==0) {
				foreach ($alumnos as $alu) {
					$sql = "INSERT INTO examenalumno (idexamen, idpersona, nota, libro, tomo, folio, usucrea, fechacrea, usumodi, fechamodi)";
					$sql.= " SELECT " . $dataIdExamen;
					$sql.= "," . $alu['id'];
					$sql.= "," . ($alu['nota'] == "" ? "null" : $alu['nota']);
					$sql.= "," . ($alu['libro'] == "" ? "null" : "'".strtoupper($alu['libro'])."'");
					$sql.= "," . ($alu['tomo'] == "" ? "null" : "'".strtoupper($alu['tomo'])."'");
					$sql.= "," . ($alu['folio'] == "" ? "null" : "'".strtoupper($alu['folio'])."'");
					$sql.= ",'" . $ses->getUsuario() . "'";
					$sql.= ",CURRENT_TIMESTAMP";
					$sql.= ",null";
					$sql.= ",null";
					$sql.= " ON DUPLICATE KEY UPDATE ";
					$sql.= "nota=" . ($alu['nota'] == "" ? "null" : $alu['nota']);
					$sql.= ",libro=" . ($alu['libro'] == "" ? "null" : "'".strtoupper($alu['libro'])."'");
					$sql.= ",tomo=" . ($alu['tomo'] == "" ? "null" : "'".strtoupper($alu['tomo'])."'");
					$sql.= ",folio=" . ($alu['folio'] == "" ? "null" : "'".strtoupper($alu['folio'])."'");
					$sql.= ",usumodi='" . $ses->getUsuario() . "'";
					$sql.= ",fechamodi=CURRENT_TIMESTAMP";
					$db->update($sql);
				}
				$db->dbDisconnect();

				$ses->setMessage("Notas actualizadas con éxito", SessionMessageType::Success);
				header("Location: /examenes/cargarnotas/" . $dataIdExamen, TRUE, 302);
				exit();
			}
		}

		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	
                
		//Traigo los alumnos anotados
                $viewDataAlumnos = $this->getDatosAlumnos($idExamen);
                
                $reportes = $this->getReportesAlumnos($viewDataAlumnos,$viewDataExamen[0]['idtipoexamen']);
               
                
                $libro = null;
                $tomo = null;
                $folio = null;
                foreach ($viewDataAlumnos as $alu) {
                    if($libro == null){
                        $libro = $alu['libro'];
                    }
                    if($tomo == null){
                        $tomo = $alu['tomo'];
                    }
                    if($folio == null){
                        $folio = $alu['folio'];
                    }
                }
		$db->dbDisconnect();

		$pageTitle="Cargar Notas de Exámenes";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/cargar-notas.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}
/*
Cuando se tilde de finalizado al examen, el sistema automáticamente debe actualizar en la tabla AlumnoMateria las notas (ya sea porque estoy en un examen parcial o final y agregar además datos de folio etc.). Si por ejemplo tengo 1° parcial y 1 recuperatorio de ese parcial, el sistema toma la nota > 4 y esa nota la guarda en el campo Parcial1 de la tabla AlumnoMateria. Lo mismo para el segundo parcial
Además en esta instancia no se pueden editar mas las notas.
Agregar Botón imprimir, antes de que se rinda el examen, para que se imprima la lista de alumnos y se pueda usar para completar notas.

*/	
        public function examenalumnodeshacer($idexamen,$idpersona,$idAlumnoMateria,$idestadoalumnomateriaactual){
            //guarda en la tabla examenalumnodeshacer el estado actual de la persona para idalumnomateria indicado.
            $db =& $this->POROTO->DB; 
            $ses =& $this->POROTO->Session;
            
            $sOK=true;
            
            $sql= " select * from examenalumnodeshacer ";
            $sql.=" where idexamen=".$idexamen;
            $sql.=" and idpersona=".$idpersona ;
            $sql.=" and idalumnomateria=".$idAlumnoMateria;
            if ($sOK!==false){
                $arr = $db->getSQLArray($sql);
            }
            if(count($arr)>0){ //Si existe registro, actualizo el estado anterior de alumnomateria.
                $sql= " update examenalumnodeshacer set ";
                $sql.=" idestadoalumnomateria=".$idestadoalumnomateriaactual;
                $sql.=" where idexamen=".$idexamen;
                $sql.=" and idpersona=".$idpersona ;
                $sql.=" and idalumnomateria=".$idAlumnoMateria;
                if ($sOK!==false) $sOK=$db->update($sql,'',true);
            }
            else
            { //Inserto
                $sql= " insert into examenalumnodeshacer (idexamen,idpersona,idalumnomateria,idestadoalumnomateria)";
                $sql.=" values(".$idexamen.",".$idpersona.",".$idAlumnoMateria.",".$idestadoalumnomateriaactual.")";
                if ($sOK!==false) $sOK=$db->insert($sql,'',true);
            }
            return $sOK;
        }
        
	public function finalizar($idExamen) { //OKK
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Examenes Finalizar')){
		$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
		header("Location: /gestion-examenes/", TRUE, 302);
		exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		$db->dbConnect("examenes/finalizar/" . $idExamen);
		$dataIdExamen = $db->dbEscape($idExamen);
		
		$db->begintrans();
		$sOK=true; //Variable usada para el manejo de transacciones.
		
                $promocion_automatizada=false;
                $sql = "select valor from configuracion where parametro='promocion_automatizada'";
                $arrConfigDb = $db->getSQLArray($sql);
                if (count($arrConfigDb) == 1 && $arrConfigDb[0]['valor'] == 'Y') {
                    $promocion_automatizada=true;
                }
                
		//Traigo las notas de inscriptos al examen
		$sql = " select e.idexamen,e.idtipoexamen,e.idmateria,e.fecha,date_format(e.fecha,'%Y/%m%/%d') as fechacorta,";
		$sql.= " ea.idpersona,date_format(current_timestamp, '%Y') anio, ";
		$sql.= " ea.nota, ea.libro, ea.tomo, ea.folio, e.idcomision";
		$sql.= " from examenalumno ea inner join examenes e on e.idexamen=ea.idexamen";
		$sql.= " where e.estado=1 and ea.idexamen=" . $dataIdExamen; //Cambio Leo 20170626 se le agrego estado = 1
		if ($sOK!==false){
			$data = $db->getSQLArray($sql);
		}
                if(empty($data)) {
                    $ses->setMessage("No se puede finalizar un examen sin alumnos.", SessionMessageType::TransactionError);
                    $db->dbDisconnect();
                    header("Location: /examenes/cargarnotas/$idExamen", TRUE, 302);
                    exit();
                }
		if( in_array($data[0]['idtipoexamen'], array(1, 2, 5, 6)) ){

                    $libro = null;
                    $tomo = null;
                    $folio = null;
                    foreach ($data as $alu) {
                        if($libro == null){
                            $libro = $alu['libro'];
                        }
                        if($tomo == null){
                            $tomo = $alu['tomo'];
                        }
                        if($folio == null){
                            $folio = $alu['folio'];
                        }
                    }
                    if($libro == null || $tomo == null || $folio == null) {
                        $ses->setMessage("Los parametros \"Libro\", \"Tomo\" y \"Folio\" son obligatorios para finalizar un examen Final.", SessionMessageType::TransactionError);
                        $db->dbDisconnect();
                        header("Location: /examenes/cargarnotas/$idExamen", TRUE, 302);
                        exit();
                    }
                }
                
                $materia_promocionable=false;
                $sql="select promocionable from materias where idmateria=".$data[0]['idmateria'];
                $arr = $db->getSQLArray($sql);
                if(count($arr)>0){
                    if($arr[0]["promocionable"]==1){
                        $materia_promocionable=true;
                    }
                }
                                            
		foreach ($data as $rec) {
			$alumnoMateriId =0; //Inicializo
			$idestadoalumnomateria=0; //Inicializo
			
			if ($rec['nota'] == "") $nota="0"; else $nota=$rec['nota'];
			if ($rec['libro'] == "") $libro="null"; else $libro="'".strtoupper($rec['libro'])."'";
			if ($rec['tomo'] == "") $tomo="null"; else $tomo="'".strtoupper($rec['tomo'])."'";
			if ($rec['folio'] == "") $folio="null"; else $folio="'".strtoupper($rec['folio'])."'";
			if ($rec['idpersona'] == "") $idpersona="0"; else $idpersona=$rec['idpersona'];
			if ($rec['idmateria'] == "") $idmateria="0"; else $idmateria=$rec['idmateria'];
			if ($rec['idcomision'] == "") $idcomision="0"; else $idcomision=$rec['idcomision'];
			//if ($rec['asistencia'] == "") $asistencia="0"; else $asistencia=$rec['asistencia'];
			/*
			
			-Examenes Primer Cuatrimestre: OK
			Si asistencia=1 y la nota < 4 actualizo estadoalumnomateria a LIBRE y pongo la nota en alumnomaterianota
			Si asistencia=0 (no debe tomar su nota) actualizo estadoalumnomateria a LIBRE
			Si asistencia=1 y la nota >=4 actualizo la nota en alumnomaterianota

			-Examenes Segundo Cuatrimestre: OK
			Si asistencia=1 y la nota < 4 actualizo estadoalumnomateria a LIBRE y pongo la nota en alumnomaterianota
			Si asistencia=1 la nota >=4 y la nota de primer parcial >=4 actualizo estadoalumnomateria a CURSADA APROBADA
			Si asistencia=0 (no debe tener nota) actualizo estadoalumnomateria a LIBRE
			
			-Examenes Final Previo y Final Regular: OK
			Si asistencia=1 y la nota < 4 pongo la nota en alumnomaterianota, mantengo el estado que diga AlumnoMateria 
			Libre o Cursada Aprobada.
			Si asistencia=1 y la nota >= 4 pongo la nota en alumnomaterianota, y cambio estado alumnomateria a APROBADA
			Si asistencia=0 no asistio al examen entonces no hago nada.
			
			-Examen Final por Promocion: OK
			Todos tendran tildado asistencia=1
			Todos tendran una nota promedio >= 7.
			Guardar nota en alumnomaterianota
			modificar estado en alumnomateria.
			
			PENDIENTE
			Vencimiento de cursada a los 5 años. Si rindio mal los finales durante 5 años desde el año de cursada entonces
			Actualizar estado AlumnoMateria a LIBRE. Ver cuando puedo hacer esto?????. 
			*/
			//Primer cuatrimestre++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			if ($rec['idtipoexamen']==3){
					//Obtengo registro de idalumnomateria para la materia en cuestion y estado CURSANDO
					//Traigo el MAX porque si para una misma materia tengo mas de un registro tomo siempre el ultimo
					//El mas actualizado. Si me anote en materia cancele y me volvi a anotar, tomaria el ultimo
					//Si quede libre quiero recursarla la recurso, obtendria este ultimo registro y esta OKKKK.
					$sql = "select max(idalumnomateria) as idalumnomateria,idestadoalumnomateria from alumnomateria"; 
					$sql.=" WHERE idmateria=".$idmateria." and idcomision=".$idcomision;
					$sql.=" and idpersona=".$idpersona." and idestadoalumnomateria=".$this->POROTO->Config['estado_alumnomateria_cursando']." ";
					$dataAM = $db->getSQLArray($sql);
					if (count($dataAM) == 1) { 
						$alumnoMateriId = $dataAM[0]['idalumnomateria'];
                                                $idestadoalumnomateriaactual=$dataAM[0]['idestadoalumnomateria'];
					}
                                        //echo("idalumnomateria ".$alumnoMateriId."<br>");
					if ($alumnoMateriId!=0){ //Si hay un registro en alumno materia correcto
                                        
                                        $sOK=$this->examenalumnodeshacer($dataIdExamen,$idpersona,$alumnoMateriId,$idestadoalumnomateriaactual);
					
                                           //Inserto la nota
						if ($nota>0){
							$sql = "insert into alumnomaterianota (idalumnomateria, idtipoexamen,idexamen, fechaexamen, ";
							$sql.="notaexamen, ";
							$sql.="libro, tomo, folio, usucrea, fechacrea, usumodi, fechamodi) ";
							$sql.= " select " . $alumnoMateriId;
							$sql.= "," . $rec['idtipoexamen'];
							$sql.= "," . $rec['idexamen'];
							$sql.= ",'" . $rec['fecha'] . "'";
							$sql.= "," . $nota;
							$sql.= "," . $libro;
							$sql.= "," . $tomo;
							$sql.= "," . $folio;
							$sql.= ",'" . $ses->getUsuario() . "'";
							$sql.= ",CURRENT_TIMESTAMP";
							$sql.= ",null";
							$sql.= ",null";
							if ($sOK!==false) $sOK=$db->insert($sql,'',true);
						}
						if ($nota<4){ //Si no asistio al examen o asistio pero se saco menos de 4 --> LIBRE
							//Actualizo el estado de alumnomateria a LIBRE
							$sql= " update alumnomateria set idestadoalumnomateria=".$this->POROTO->Config['estado_alumnomateria_libre'].", "; //Estado LIBRE
							$sql.= "usumodi='" . $ses->getUsuario() . "',";
							$sql.= "fechamodi=CURRENT_TIMESTAMP";
							$sql.=" where idalumnomateria=".$alumnoMateriId;
							if ($sOK!==false) $sOK=$db->update($sql,'',true);
				
		//Actualizar cupo de la comision
		//Actualizo ComCupos, si la pase a cancelada o libre actualizaa el cupo, caso contrario continuara igual.
		$sql= " SELECT ca.idcomcupo from alumnomateria am inner join comalumno ca on am.idcomision=ca.idcomision";
		$sql.= " and am.idpersona=ca.idpersona ";
		$sql.= " where am.idalumnomateria=" . $alumnoMateriId. " and not am.idcomision is null and ca.estado=1";
		$arr = $db->getSQLArray($sql);
		if(count($arr)>0){
			$dataCupo=$arr[0]['idcomcupo'];
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
			if ($sOK!==false) $sOK=$db->update($sql,'',true);
		}
						}
					}
			}
			
			//Segundo cuatrimestre+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			if ($rec['idtipoexamen']==4){
                                        $notaPrimerParcial=0; //por defecto
                                        $notaSegundoParcial=0; //por defecto
                                        $notaPromocion=0; //por defecto
					//Obtengo registro de idalumnomateria para la materia en cuestion y estado CURSANDO
					$sql = "select  max(idalumnomateria) as idalumnomateria,idestadoalumnomateria from alumnomateria";
					$sql.=" WHERE idmateria=".$idmateria." and idcomision=".$idcomision;
					$sql.=" and idpersona=".$idpersona." and idestadoalumnomateria=".$this->POROTO->Config['estado_alumnomateria_cursando'];
					$dataAM = $db->getSQLArray($sql);
					if (count($dataAM) == 1) { 
						$alumnoMateriId = $dataAM[0]['idalumnomateria'];					
                                                $idestadoalumnomateriaactual=$dataAM[0]['idestadoalumnomateria'];
					}
					
					if ($alumnoMateriId!=0){ //Si hay un registro en alumno materia 
                                        $sOK=$this->examenalumnodeshacer($dataIdExamen,$idpersona,$alumnoMateriId,$idestadoalumnomateriaactual);
						//Inserto la nota
						if ($nota==0){
							$idestadoalumnomateria=$this->POROTO->Config['estado_alumnomateria_libre']; //LIBRE
						}
						if ($nota>0){
							$sql = "insert into alumnomaterianota (idalumnomateria, idtipoexamen,idexamen, fechaexamen, ";
							$sql.="notaexamen, ";
							$sql.="libro, tomo, folio, usucrea, fechacrea, usumodi, fechamodi) ";
							$sql.= " select " . $alumnoMateriId;
							$sql.= "," . $rec['idtipoexamen'];
							$sql.= "," . $rec['idexamen'];
							$sql.= ",'" . $rec['fecha'] . "'";
							$sql.= "," . $nota;
							$sql.= "," . $libro;
							$sql.= "," . $tomo;
							$sql.= "," . $folio;
							$sql.= ",'" . $ses->getUsuario() . "'";
							$sql.= ",CURRENT_TIMESTAMP";
							$sql.= ",null";
							$sql.= ",null";
							if ($sOK!==false) $sOK=$db->insert($sql,'',true);
							if ($nota<4){
								$idestadoalumnomateria=$this->POROTO->Config['estado_alumnomateria_libre']; //LIBRE
							}
							if ($nota>=4){
								//Verifico la nota del primer parcial, si ambas son >= 4 aprobado.
								$sql="select notaexamen from alumnomaterianota where idalumnomateria=".$alumnoMateriId;
								$sql.=" and idtipoexamen=3";
								$dataAM = $db->getSQLArray($sql);
								if (count($dataAM) == 1) { 
									$notaPrimerParcial = $dataAM[0]['notaexamen'];					
								}
								if ($notaPrimerParcial<4){
									$idestadoalumnomateria=$this->POROTO->Config['estado_alumnomateria_libre']; 
									//LIBRE
								}
								if ($notaPrimerParcial>=4){
									$idestadoalumnomateria=$this->POROTO->Config['estado_alumnomateria_cursadaaprobada']; 		
									//CURSADA APROBADA
								}
							}
						}
						
						//Actualizo el estado de alumnomateria
						
						$sql= " update alumnomateria set idestadoalumnomateria=".$idestadoalumnomateria.", "; //Estado
						$sql.= "usumodi='" . $ses->getUsuario() . "',";
						$sql.= "fechamodi=CURRENT_TIMESTAMP";
						$sql.=" where idalumnomateria=".$alumnoMateriId;
						if ($sOK!==false) $sOK=$db->update($sql,'',true);
		
                                                //Cambio 2017-08-16				
                                                //Actualizar cupo de la comision
                                                //Actualizo ComCupos, si la pase a cancelada o libre actualizaa el cupo, caso contrario continuara igual.
                                                $sql= " SELECT ca.idcomcupo from alumnomateria am inner join comalumno ca on am.idcomision=ca.idcomision";
                                                $sql.= " and am.idpersona=ca.idpersona ";
                                                $sql.= " where am.idalumnomateria=" . $alumnoMateriId. " and not am.idcomision is null and ca.estado=1";
                                                $arr = $db->getSQLArray($sql);
                                                if(count($arr)>0){
                                                        $dataCupo=$arr[0]['idcomcupo'];
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
                                                        if ($sOK!==false) $sOK=$db->update($sql,'',true);
                                                }
                                                //Fin Cambio 2017-08-16

					
                                                //Cambio 79 2017-12-26
                                                $notaSegundoParcial=$nota;
                                                $notaPromedio=($notaPrimerParcial+$notaSegundoParcial)/2;
                                                if($promocion_automatizada && $materia_promocionable && $notaPrimerParcial>=4 && $notaSegundoParcial>=7 && $notaPromedio>=7){
                                                    //Promociono....
                                                    $sql = "insert into alumnomaterianota (idalumnomateria, idtipoexamen, fechaexamen, ";
                                                    $sql.="notaexamen, ";
                                                    $sql.="usucrea, fechacrea, usumodi, fechamodi) ";
                                                    $sql.= " select " . $alumnoMateriId;
                                                    $sql.= ",5"; //Promocion
                                                    $sql.= ",'" . $rec['fechacorta'] . "'";
                                                    $sql.= "," . $notaPromedio;
                                                    $sql.= ",'" . $ses->getUsuario() . "'";
                                                    $sql.= ",CURRENT_TIMESTAMP";
                                                    $sql.= ",null";
                                                    $sql.= ",null";
                                                    if ($sOK!==false) $sOK=$db->insert($sql,'',true);
                                                    $idestadoalumnomateria=$this->POROTO->Config['estado_alumnomateria_aprobada']; //APROBADA
                                                    //Actualizo el estado de alumnomateria
                                                    $sql= " update alumnomateria set idestadoalumnomateria=".$idestadoalumnomateria.", "; //Estado
                                                    $sql.=" fechaaprobacion='".$rec['fechacorta']."',";
                                                    $sql.= "usumodi='" . $ses->getUsuario() . "',";
                                                    $sql.= "fechamodi=CURRENT_TIMESTAMP";
                                                    $sql.=" where idalumnomateria=".$alumnoMateriId;
                                                    if ($sOK!==false) $sOK=$db->update($sql,'',true);
                                                }
                                            //Fin Cambio 79 2017-12-26    
                                        
					}//if $alumnoMateriId
			}
			//Final Previo  Regular  Libre++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			if ($rec['idtipoexamen']==1 || $rec['idtipoexamen']==2 || $rec['idtipoexamen']==6){
					//Obtengo registro de idalumnomateria para la materia en cuestion y estado CURSADA APROBADA O LIBRE.
					$sql = "select  max(idalumnomateria) as idalumnomateria,idestadoalumnomateria from alumnomateria";
					$sql.=" WHERE idmateria=".$idmateria;
					$sql.=" and idpersona=".$idpersona." and idestadoalumnomateria in (".$this->POROTO->Config['estado_alumnomateria_cursadaaprobada'].",".$this->POROTO->Config['estado_alumnomateria_libre'].")";
					$dataAM = $db->getSQLArray($sql);
					if (count($dataAM) == 1) { 
						$alumnoMateriId = $dataAM[0]['idalumnomateria'];
                                                $idestadoalumnomateriaactual=$dataAM[0]['idestadoalumnomateria'];

					}
					if ($alumnoMateriId!=0){ //Si hay un registro en alumno materia correcto
                                        $sOK=$this->examenalumnodeshacer($dataIdExamen,$idpersona,$alumnoMateriId,$idestadoalumnomateriaactual);
						//Inserto la nota
						if ($nota>0){
							$sql = "insert into alumnomaterianota (idalumnomateria, idtipoexamen,idexamen, fechaexamen, ";
							$sql.="notaexamen, ";
							$sql.="libro, tomo, folio, usucrea, fechacrea, usumodi, fechamodi) ";
							$sql.= " select " . $alumnoMateriId;
							$sql.= "," . $rec['idtipoexamen'];
							$sql.= "," . $rec['idexamen'];
							$sql.= ",'" . $rec['fechacorta'] . "'";
							$sql.= "," . $nota;
							$sql.= "," . $libro;
							$sql.= "," . $tomo;
							$sql.= "," . $folio;
							$sql.= ",'" . $ses->getUsuario() . "'";
							$sql.= ",CURRENT_TIMESTAMP";
							$sql.= ",null";
							$sql.= ",null";
							if ($sOK!==false) $sOK=$db->insert($sql,'',true);
							if ($nota<4){
								//$idestadoalumnomateria=10; //LIBRE Mantengo el estado que habia....
							}
							if ($nota>=4){
								$idestadoalumnomateria=$this->POROTO->Config['estado_alumnomateria_aprobada']; //APROBADA
								//Actualizo el estado de alumnomateria
								$sql= " update alumnomateria set idestadoalumnomateria=".$idestadoalumnomateria.", "; //Estado
								$sql.=" fechaaprobacion='".$rec['fechacorta']."',";
								$sql.= "usumodi='" . $ses->getUsuario() . "',";
								$sql.= "fechamodi=CURRENT_TIMESTAMP";
								$sql.=" where idalumnomateria=".$alumnoMateriId;
								if ($sOK!==false) $sOK=$db->update($sql,'',true);
								
								
							}
						}//del if asistencia
					} //del if $alumnoMateriId
			} //del IF examen previo o regular

			//Final Promocion+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
			if ($rec['idtipoexamen']==5){
					//Obtengo registro de idalumnomateria para la materia en cuestion y estado CURSADA APROBADA.
					$sql = "select  max(idalumnomateria) as idalumnomateria,idestadoalumnomateria from alumnomateria";
					$sql.=" WHERE idmateria=".$idmateria;
					$sql.=" and idpersona=".$idpersona." and idestadoalumnomateria in (".$this->POROTO->Config['estado_alumnomateria_cursadaaprobada'].")";
					$dataAM = $db->getSQLArray($sql);
					if (count($dataAM) == 1) { 
						$alumnoMateriId = $dataAM[0]['idalumnomateria'];
                                                $idestadoalumnomateriaactual=$dataAM[0]['idestadoalumnomateria'];
					}
					if ($alumnoMateriId!=0){ //Si hay un registro en alumno materia correcto
                                        $sOK=$this->examenalumnodeshacer($dataIdExamen,$idpersona,$alumnoMateriId,$idestadoalumnomateriaactual);
						//Inserto la nota
						if ($nota>=7){
							$sql = "insert into alumnomaterianota (idalumnomateria, idtipoexamen,idexamen, fechaexamen, ";
							$sql.="notaexamen, ";
							$sql.="libro, tomo, folio, usucrea, fechacrea, usumodi, fechamodi) ";
							$sql.= " select " . $alumnoMateriId;
							$sql.= "," . $rec['idtipoexamen'];
							$sql.= "," . $rec['idexamen'];
							$sql.= ",'" . $rec['fechacorta'] . "'";
							$sql.= "," . $nota;
							$sql.= "," . $libro;
							$sql.= "," . $tomo;
							$sql.= "," . $folio;
							$sql.= ",'" . $ses->getUsuario() . "'";
							$sql.= ",CURRENT_TIMESTAMP";
							$sql.= ",null";
							$sql.= ",null";
							if ($sOK!==false) $sOK=$db->insert($sql,'',true);
							$idestadoalumnomateria=$this->POROTO->Config['estado_alumnomateria_aprobada']; //APROBADA
							//Actualizo el estado de alumnomateria
							$sql= " update alumnomateria set idestadoalumnomateria=".$idestadoalumnomateria.", "; //Estado
							$sql.=" fechaaprobacion='".$rec['fechacorta']."',";
							$sql.= "usumodi='" . $ses->getUsuario() . "',";
							$sql.= "fechamodi=CURRENT_TIMESTAMP";
							$sql.=" where idalumnomateria=".$alumnoMateriId;
							if ($sOK!==false) $sOK=$db->update($sql,'',true);
						}//del if asistencia
					} //del If $alumnoMateriId
			} //del IF examen promocion
		}

		$sql = " UPDATE examenes SET examenfinalizado=1,";
                $sql.= " usumodi='" . $ses->getUsuario() . "',";
                $sql.= " fechamodi=CURRENT_TIMESTAMP";
                $sql.= " WHERE idexamen=" . $dataIdExamen;
		if ($sOK!==false) $sOK=$db->update($sql,'',true);

		if ($sOK!==false) {
			$db->commit();
			$ses->setMessage("Exámen finalizado con éxito.", SessionMessageType::Success);					
		}else
			{$db->rollback();
			$ses->setMessage("Se produjo un error al intentar guardar en la base de datos.", SessionMessageType::TransactionError);			
			}
		$db->dbDisconnect();
		header("Location: /gestion-examenes", TRUE, 302);
	}

        
        public function alumnomateriaestadoanterior($idAlumnoMateria){
            //Obtiene el estado anterior de la alumnomateria sacado del historial
            //No se usa por ahora.
            $db =& $this->POROTO->DB; 
            $ses =& $this->POROTO->Session;
            $dataEstadoActual=0;
            $dataEstadoNuevo=0;
            $sql= " select idestadoalumnomateria from alumnomateria where idalumnomateria=".$idAlumnoMateria;
            $arr = $db->getSQLArray($sql);
            if(count($arr)>0){
                $dataEstadoActual=$arr[0]['idestadoalumnomateria'];
            }
            //Obtengo Estado Anterior
            $sql= " select id,idestadoalumnomateria from alumnomateria_historial where idalumnomateria=".$idAlumnoMateria;
            $sql.=" order by id desc";
            $historial = $db->getSQLArray($sql);   
            foreach ($historial as $hist) {
                    if($hist["idestadoalumnomateria"]!=$dataEstadoActual){
                         $dataEstadoNuevo=$hist["idestadoalumnomateria"];
                    }
            }    
            if($dataEstadoNuevo==0) $dataEstadoNuevo=$dataEstadoActual;
            return $dataEstadoNuevo;
        }
        
        public function deshacerfinalizar($idExamen) {
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;

		if(!$ses->tienePermiso('','Gestion de Examenes Deshacer Finalizar')){
                    $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
                    header("Location: /gestion-examenes/", TRUE, 302);
                    exit();
		}

		$db->dbConnect("examenes/deshacerfinalizar/" . $idExamen);
		$dataIdExamen = $db->dbEscape($idExamen);
		
		$db->begintrans();
		$sOK=true; //Variable usada para el manejo de transacciones.
		$tablaexamenalumnodeshacer=false; //Variable utilizada para saber si existen registros en tabla examenalumnodeshacer
                //Si no existen registros debo avisar al usuario para que actualice a mano los estados de las materias para los alumnos.
                
                $dataEstadoAlumnoMateria=0;
                $promocion_automatizada=false;
                
                $sql="select * from examenes where idexamen=".$dataIdExamen;
                $arr = $db->getSQLArray($sql);
		if(count($arr)>0){
			$dataTipoExamen=$arr[0]['idtipoexamen'];
                }
                
                //Si es segundo parcial y esta promocion_automatizada habilitada elimino tambien la nota de promocion.
                $sql = "select valor from configuracion where parametro='promocion_automatizada'";
                $arrConfigDb = $db->getSQLArray($sql);
                if (count($arrConfigDb) == 1 && $arrConfigDb[0]['valor'] == 'Y') {
                    if($dataTipoExamen==4){ //Segundo parcial
                        $promocion_automatizada=true;
                    }
                }
                
                $sql= "select idalumnomateria,idestadoalumnomateria from examenalumnodeshacer ";
                $sql.="where idexamen=".$dataIdExamen;
                /* Query antigua antes de existir examenalumnodeshacer.
                $sql= " select distinct amn.idalumnomateria,am.idestadoalumnomateria from alumnomaterianota amn ";
                $sql.=" inner join alumnomateria am on amn.idalumnomateria=am.idalumnomateria ";
                $sql.=" where amn.idexamen=".$dataIdExamen;
                 */
                $arr = $db->getSQLArray($sql);
                
                //recorremos cada registro de alumnomateria
                if(count($arr)>0){
                    $tablaexamenalumnodeshacer=true;
                    foreach ($arr as $alumnomateria) {
                        $dataEstadoAlumnoMateria=$alumnomateria["idestadoalumnomateria"];    
                        if($dataEstadoAlumnoMateria!=0){
                            //Actualizar en alumnomateria el status de dicha materia para que vuevla a su estado CURSANDO.
                            $sql = "update alumnomateria set idestadoalumnomateria=".$dataEstadoAlumnoMateria.", ";
                            $sql.= "usumodi='" . $ses->getUsuario() . "', ";
                            $sql.= "fechamodi=CURRENT_TIMESTAMP ";
                            $sql.= "where idalumnomateria=".$alumnomateria["idalumnomateria"];
                            if ($sOK!==false) $sOK=$db->update($sql,'',true);
                        }

                        if($promocion_automatizada){ 
                            //Si es 2° parcial y promocion automatizada, borro la nota de promocion.
                            $sql = " delete from alumnomaterianota where ";
                            $sql.= " idalumnomateria=".$alumnomateria["idalumnomateria"];
                            $sql.= " and idtipoexamen=5";
                            if ($sOK!==false) $sOK=$db->delete($sql,'',true);
                            $sql = "update alumnomateria set fechaaprobacion=null ";
                            $sql.= "where idalumnomateria=".$alumnomateria["idalumnomateria"];
                            if ($sOK!==false) $sOK=$db->update($sql,'',true);
                        }  
                    }
                }else{
                    $tablaexamenalumnodeshacer=false;
                }
                
                // delete from alumnomaterianota where idexamen
                $sql = "update alumnomaterianota set ";
                $sql.= "usumodi='" . $ses->getUsuario() . "', ";
                $sql.= "fechamodi=CURRENT_TIMESTAMP ";
                $sql.= "where idexamen=" . $dataIdExamen;
                if ($sOK!==false) $sOK=$db->update($sql,'',true);
                $sql = " delete from alumnomaterianota where idexamen=" . $dataIdExamen;
                if ($sOK!==false) $sOK=$db->delete($sql,'',true);
                    
                //actualizo el estado del examen a no finalizado.
                $sql = "update examenes set examenfinalizado=0 where idexamen=" . $dataIdExamen;
                if ($sOK!==false) $sOK=$db->update($sql,'',true); 
                
                if ($sOK!==false) {
			$db->commit();
                        if($tablaexamenalumnodeshacer){
                            $msg="Exámen desfinalizado con éxito.<br>Se han borrado las notas de los alumnos en el analítico.<br>Se han actualizado los estados de las materias para cada alumno en el analítico.";
                        }else{
                            $msg="Exámen desfinalizado con éxito.<br>Se han borrado las notas de los alumnos en el analítico.<br>ATENCIÓN!!! no se han actualizado los estados de las materias para cada alumno en el analítico, debe realizar esta tarea manualmente desde Ver Materias.";
                        }
			$ses->setMessage($msg, SessionMessageType::Success);					
		}else
			{$db->rollback();
			$ses->setMessage("Se produjo un error al intentar guardar en la base de datos.", SessionMessageType::TransactionError);			
			}
		$db->dbDisconnect();
		header("Location: /gestion-examenes", TRUE, 302);
	}
        
	public function inscripcion() { //OK
		//abrir pantalla de inscripcion-examenes.php
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Inscripcion a Examenes Acceso desde Menu')){
			$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
			header("Location: /", TRUE, 302);
			exit();
		}
		//Fin Cambio 38 Leo 20170706	
		$db->dbConnect("examenes/inscripcion");
		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	
			
		$sql = "SELECT ac.idcarrera,ac.idalumnocarrera, concat(c.descripcion,' - ',i.nombre) as nombre";
		$sql.= " FROM alumnocarrera ac inner join carreras c on ac.idcarrera=c.idcarrera";
		$sql.= " left join instrumentos i on ac.idinstrumento=i.idinstrumento";
		$sql.= " where ac.fechafinalizada is null and ac.estado in (1,3) and c.estado=1 and ac.idpersona=" . $ses->getIdPersona();
		$viewDataCarreras = $db->getSQLArray($sql);
		$db->dbDisconnect();

		$pageTitle="Inscripción a Exámenes";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/inscripcion-examenes.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}

	public function inscribir($idExamen) { //OK
	//una vez en la pantalla inscripcion-examenes.php inscribe al alumno en el final.
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;
		$db->dbConnect("examenes/inscribir/" . $idExamen);
		$dataIdExamen = $db->dbEscape($idExamen);

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Inscripcion a Examenes Acceso desde Menu')){
			$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
			header("Location: /inscripcion-examenes/", TRUE, 302);
			exit();
		}
		//Fin Cambio 38 Leo 20170706

		$sql = "INSERT INTO examenalumno (idexamen, idpersona, nota, libro, tomo, folio, usucrea, fechacrea, usumodi, fechamodi)";
		$sql.= " SELECT " . $dataIdExamen;
		$sql.= "," . $ses->getIdPersona();
		$sql.= ", null, null, null, null";
		$sql.= ",'" . $ses->getUsuario() . "'";
		$sql.= ",CURRENT_TIMESTAMP";
		$sql.= ",null";
		$sql.= ",null";
		$db->insert($sql);


		$db->dbDisconnect();

		$ses->setMessage("Inscripcion al Exámen realizada con éxito", SessionMessageType::Success);
		header("Location: /inscripcion-examenes", TRUE, 302);
	}

	public function inscriptos($idexamen) { //OK
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];
		
		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Examenes Inscriptos')){
		$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
		header("Location: /gestion-examenes/", TRUE, 302);
		exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		$db->dbConnect("examenes/inscriptos/" . $idexamen);
		$dataIdExamen = $db->dbEscape($idexamen);

		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		//Datos de cabecera del examen.
		$sql = " select  e.idtipoexamen, te.descripcion tipexa , m.nombre materia, ";
		$sql.= " e.idcomision, e.fecha,c.nombre as comision, ";
		$sql.= " e.aula,e.cupo,i.nombre as instrumento,a.nombre as area, ";
		$sql.= " count(distinct ea.idpersona) q ";
		$sql.= " from examenes e ";
		$sql.= " inner join tipoexamen te on e.idtipoexamen=te.idtipoexamen ";
		$sql.= " inner join materias m on e.idmateria=m.idmateria ";
		$sql.= " left join instrumentos i on e.idinstrumento=i.idinstrumento ";
		$sql.= " left join areas a on e.idarea=a.idarea ";
		$sql.= " left join comisiones c on e.idcomision=c.idcomision ";
		$sql.= " left join examenalumno ea on ea.idexamen=e.idexamen ";
		$sql.= " where e.estado=1 and e.idexamen=" . $dataIdExamen; //Cambio Leo 20170626 agregado estado = 1
		$sql.= " group by e.idexamen ";
		$viewData = $db->getSQLArray($sql);
		
		//Traigo los alumnos anotados
		$sql = "select p.idpersona, p.legajo,p.apellido, p.nombre, ";
		$sql.= " p.documentonro, eam.descripcion as estadoalumnomateria"; 
		$sql.= " from examenalumno ea inner join personas p on ea.idpersona=p.idpersona";
		$sql.= " inner join examenes e on ea.idexamen=e.idexamen";
		$sql.= " inner join alumnomateria am on e.idmateria=am.idmateria and am.idpersona=ea.idpersona";
		$sql.= " inner join estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria";
		$sql.= " where ea.idexamen=" . $dataIdExamen;
		$sql.= " and am.idalumnomateria=";
		$sql.= "(select max(idalumnomateria) from alumnomateria am2 where am2.idpersona=ea.idpersona and am2.idmateria=e.idmateria) ";
		$sql.= " group by p.idpersona,p.apellido,p.nombre,p.legajo,ea.nota,ea.libro,ea.tomo,ea.folio,p.documentonro,eam.descripcion";
		$sql.= " order by p.apellido,p.nombre asc";
		$viewDataAlumnos = $db->getSQLArray($sql);

		$db->dbDisconnect();

		$pageTitle="Inscriptos a Exámen";
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/inscriptos-examenes.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}

	public function reprocesar($idExamen) { //OK
		$db =& $this->POROTO->DB; 
		$ses =& $this->POROTO->Session;

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Gestion de Examenes Inscriptos Reprocesar')){
		$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
		header("Location: /gestion-examenes/", TRUE, 302);
		exit();
		}
		//Fin Cambio 38 Leo 20170706

		$db->dbConnect("examenes/reprocesar/" . $idExamen);
		$db->begintrans();
		$sOK=true; //Variable usada para el manejo de transacciones.
		$dataIdExamen = $db->dbEscape($idExamen);

		//Obtengo datos de Cabecera del examen.
		$sql = "select e.idtipoexamen, e.idcomision, e.idmateria, year(e.fecha) as anioexamen";
		$sql.= " from examenes e";
		$sql.= " where e.estado=1 and e.idexamen=" . $dataIdExamen;
		$rec = $db->getSQLArray($sql);
		$dataTipoExamen = $rec[0]['idtipoexamen'];
		$dataComision = $rec[0]['idcomision'];
		$dataMateria = $rec[0]['idmateria'];
                $dataAnioExamen=$rec[0]['anioexamen'];
		
		if($dataTipoExamen==1 || $dataTipoExamen==6){ //Final Previo o Libre
		$ses->setMessage("El tipo de Examen Final Previo NO permite inscribir automaticamente alumnos.", SessionMessageType::TransactionError);
		}
			
		if($dataTipoExamen==2){ //Final Regular
				$sql= "SELECT am.idpersona ";
				$sql.= "FROM alumnomateria am left join comisiones c on ";
				$sql.= "(am.idcomision = c.idcomision and am.idmateria=c.idmateria) ";
				$sql.= "where am.idestadoalumnomateria=".$this->POROTO->Config['estado_alumnomateria_cursadaaprobada']." ";
				$sql.= "and c.anio=".$dataAnioExamen." "; //Si una persona aprobo la cursada este año pero no aprobo el final regular,
				//en diciembre del año siguiente cuando se cree el examen regular no aparecera ya que cambio el año.
				$sql.= "and c.estado=1 ";
				$sql.= "and am.idmateria=".$dataMateria;
				$sql.= " and am.idpersona not in (select idpersona from examenalumno where idexamen=" . $dataIdExamen . ")";
				if ($dataComision!=0)	$sql.=  " and am.idcomision=".$dataComision;
				$arrAlumnosComision = $db->getSQLArray($sql);

				foreach ($arrAlumnosComision as $alu) {
					$sql = "INSERT INTO examenalumno (idexamen, idpersona, nota, libro, ";
					$sql.= "tomo, folio, usucrea, fechacrea, usumodi, fechamodi)";
					$sql.= " SELECT " . $dataIdExamen;
					$sql.= "," . $alu['idpersona'];
					$sql.= ", null, null, null, null";
					$sql.= ",'" . $ses->getUsuario() . "'";
					$sql.= ",CURRENT_TIMESTAMP";
					$sql.= ",null";
					$sql.= ",null";
					if ($sOK!==false) $sOK=$db->insert($sql,'',true);
				}
				$q = count($arrAlumnosComision);
				if ($q > 0) $q = "+" . $q;
				$ses->setMessage("Inscripción al exámen reprocesada (" . $q . ")", SessionMessageType::Success);
		}

		if($dataTipoExamen==3 || $dataTipoExamen==4){ //Cuatrimestres
					//esten en estado CURSANDO (Cambio Leo 38 20170710)
					$sql = "SELECT ca.idpersona ";
					$sql.= "FROM comalumno ca ";
					$sql.= "left join alumnomateria am on ca.idpersona=am.idpersona and ca.idcomision=am.idcomision ";
					$sql.= "WHERE ca.estado=1 AND ca.idcomision=".$dataComision." and am.idestadoalumnomateria=".$this->POROTO->Config['estado_alumnomateria_cursando']." ";
					$sql.= " and ca.idpersona not in (select idpersona from examenalumno where idexamen=" . $dataIdExamen . ")";
					$arrAlumnosComision = $db->getSQLArray($sql);
					foreach ($arrAlumnosComision as $alu) {
						$sql = "INSERT INTO examenalumno (idexamen, idpersona, nota, libro, ";
						$sql.= "tomo, folio, usucrea, fechacrea, usumodi, fechamodi) ";
						$sql.= " SELECT " . $dataIdExamen;
						$sql.= "," . $alu['idpersona'];
						$sql.= ", null, null, null, null";
						$sql.= ",'" . $ses->getUsuario() . "'";
						$sql.= ",CURRENT_TIMESTAMP";
						$sql.= ",null";
						$sql.= ",null";
						if ($sOK!==false) $sOK=$db->insert($sql,'',true);
					}
					$q = count($arrAlumnosComision);
					if ($q > 0) $q = "+" . $q;
					$ses->setMessage("Inscripción al exámen reprocesada (" . $q . ")", SessionMessageType::Success);
	}
		

		if($dataTipoExamen==5){
					$sql ="select am.idpersona,p.apellido,";
					$sql.="sum(case when amn.idtipoexamen=3 and amn.notaexamen >=" . $this->POROTO->Config['nota_parcial_aprobado_materia_no_promocionable'] ;
					$sql.=" then 1 else 0 end) primerparcialaprobado,";
					$sql.=" sum(case when amn.idtipoexamen=4 and amn.notaexamen >=" . $this->POROTO->Config['nota_segundo_parcial_aprobado_materia_promocionable'];
					$sql.=" then 1 else 0 end) segundoparcialaprobado,";
					$sql.="max(case when amn.idtipoexamen=3 then amn.notaexamen  else 0 end) primerparcialmax,";
					$sql.="max(case when amn.idtipoexamen=4 then amn.notaexamen  else 0 end) segundoparcialmax,";
					$sql.="(max(case when amn.idtipoexamen=3 then amn.notaexamen  else 0 end) + ";
					$sql.="max(case when amn.idtipoexamen=4 then amn.notaexamen  else 0 end)) / 2 promedio ";
					$sql.="from alumnomateria am inner join alumnomaterianota amn on amn.idalumnomateria=am.idalumnomateria ";
					$sql.="left join personas p on am.idpersona=p.idpersona where ";
					$sql.="am.idmateria=".$dataMateria." and am.idcomision=".$dataComision." ";
					$sql.="and am.idestadoalumnomateria=".$this->POROTO->Config['estado_alumnomateria_cursadaaprobada']." "; //Cursada aprobada
					$sql.="and am.idpersona not in (select idpersona from examenalumno where idexamen=" . $dataIdExamen . ") ";
					$sql.="group by am.idpersona ";
					$sql.="having primerparcialaprobado>0 and segundoparcialaprobado>0 and promedio>=". $this->POROTO->Config['nota_segundo_parcial_aprobado_materia_promocionable'];
					
					$arrAlumnosComision = $db->getSQLArray($sql);			
					foreach ($arrAlumnosComision as $alu) {
						$sql = "INSERT INTO examenalumno (idexamen, idpersona, nota, libro, tomo, folio, usucrea, fechacrea, usumodi, fechamodi)";
						$sql.= " SELECT " . $dataIdExamen;
						$sql.= "," . $alu['idpersona'];
						$sql.= ", ".$alu['promedio'].", null, null, null";
						$sql.= ",'" . $ses->getUsuario() . "'";
						$sql.= ",CURRENT_TIMESTAMP";
						$sql.= ",null";
						$sql.= ",null";
						if ($sOK!==false) $sOK=$db->insert($sql,'',true);
					}
					$q = count($arrAlumnosComision);
					if ($q > 0) $q = "+" . $q;
					$ses->setMessage("Inscripción al exámen reprocesada (" . $q . ")", SessionMessageType::Success);			
		}
	
		if ($sOK!==false){
			$db->commit();
		}else{
			$db->rollback();
			$ses->setMessage("Se produjo un error al intentar guardar en la base de datos.", SessionMessageType::TransactionError);			
		}
		$db->dbDisconnect();
		header("Location: /examenes/inscriptos/" . $dataIdExamen, TRUE, 302);
	}
        
    // REFACTORIZAR - Lautaro.
    public function imprimirActaFinal($idExamen) {
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $lib = & $this->POROTO->Libraries['siteLibrary'];
        $validationErrors = array();
        $db->dbConnect("examenes/cargarnotas/" . $idExamen);
        $dataIdExamen = $db->dbEscape($idExamen);

        // Arreglo con id de examenes finales.
        $soloFinales = array(1, 2, 5, 6);

        //Cambio 38 Leo 20170706
        if (!$ses->tienePermiso('', 'Gestion de Examenes Notas')) {
            $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /gestion-examenes/", TRUE, 302);
            exit();
        }
        //Fin Cambio 38 Leo 20170706
        //Traigo los datos del examen
        $viewDataExamen = $this->getDatosExamen($idExamen);

        if (isset($_POST['alumnos'])) { //Actualizacion de notas
            $alumnos = array();
            foreach ($_POST['alumnos'] as $alu) {
                $alu = explode("|", $alu);
                //Cambio Leo parciales no permtir coma
                if ((intval($alu[1]) > 0 || intval($alu[1]) <= 10) && ($viewDataExamen[0]["idtipoexamen"] != 5)) {
                    //SOLO las promociones aceptan COMA, el resto todo numero entero.
                    $resto = floatval($alu[1]) - intval($alu[1]);
                    if (floatval($resto) > 0) {
                        $validationErrors['alu' . intval($alu[0]) . '-nota'] = $alu[5] . ": Nota inválida. Un examen cuatrimestral o final no puede tener nota con decimales. Solo las promociones aceptan decimales.";
                    }
                }
                //Fin Cambio Leo

                if (intval($alu[1]) < 0 || intval($alu[1]) > 10)
                    $validationErrors['alu' . intval($alu[0]) . '-nota'] = $alu[5] . ": Nota inválida";

                $alu[1] = str_replace(",", ".", $alu[1]);
                $alumnos[$alu[0]] = array("id" => intval($alu[0]), "nota" => ($alu[1] == "" ? "" : intval($alu[1] * 100) / 100), "libro" => ($alu[2] == "" ? "" : $alu[2]), "tomo" => ($alu[3] == "" ? "" : $alu[3]), "folio" => ($alu[4] == "" ? "" : $alu[4]));
            }

            if (count($validationErrors) == 0) {
                foreach ($alumnos as $alu) {
                    $sql = "INSERT INTO examenalumno (idexamen, idpersona, nota, libro, tomo, folio, usucrea, fechacrea, usumodi, fechamodi)";
                    $sql .= " SELECT " . $dataIdExamen;
                    $sql .= "," . $alu['id'];
                    $sql .= "," . ($alu['nota'] == "" ? "null" : $alu['nota']);
                    $sql .= "," . ($alu['libro'] == "" ? "null" : "'" . strtoupper($alu['libro']) . "'");
                    $sql .= "," . ($alu['tomo'] == "" ? "null" : "'" . strtoupper($alu['tomo']) . "'");
                    $sql .= "," . ($alu['folio'] == "" ? "null" : "'" . strtoupper($alu['folio']) . "'");
                    $sql .= ",'" . $ses->getUsuario() . "'";
                    $sql .= ",CURRENT_TIMESTAMP";
                    $sql .= ",null";
                    $sql .= ",null";
                    $sql .= " ON DUPLICATE KEY UPDATE ";
                    $sql .= "nota=" . ($alu['nota'] == "" ? "null" : $alu['nota']);
                    $sql .= ",libro=" . ($alu['libro'] == "" ? "null" : "'" . strtoupper($alu['libro']) . "'");
                    $sql .= ",tomo=" . ($alu['tomo'] == "" ? "null" : "'" . strtoupper($alu['tomo']) . "'");
                    $sql .= ",folio=" . ($alu['folio'] == "" ? "null" : "'" . strtoupper($alu['folio']) . "'");
                    $sql .= ",usumodi='" . $ses->getUsuario() . "'";
                    $sql .= ",fechamodi=CURRENT_TIMESTAMP";
                    $db->update($sql);
                }
                $db->dbDisconnect();

                $ses->setMessage("Notas actualizadas con éxito", SessionMessageType::Success);
                header("Location: /examenes/cargarnotas/" . $dataIdExamen, TRUE, 302);
                exit();
            }
        }

        $arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());

        //Traigo los alumnos anotados
        $viewDataAlumnos = $this->getDatosAlumnos($idExamen);

        $reportes = $this->getReportesAlumnos($viewDataAlumnos, $viewDataExamen[0]['idtipoexamen']);


        $libro = null;
        $tomo = null;
        $folio = null;
        foreach ($viewDataAlumnos as $alu) {
            if ($libro == null) {
                $libro = $alu['libro'];
            }
            if ($tomo == null) {
                $tomo = $alu['tomo'];
            }
            if ($folio == null) {
                $folio = $alu['folio'];
            }
        }
        $db->dbDisconnect();

        $pageTitle = "Acta Final";

        include($this->POROTO->ViewPath . "/acta-final.php");
    }

}