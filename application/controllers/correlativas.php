<?php
class correlativas {
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

        public function ajaxreglas($mat, $car) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("correlativas/ajaxreglas/" . $mat . "/" . $car);
		$sql = "SELECT c.idregla, c.id, r.descripcion, c.valor1, c.valor2, m1.nombre mat, a1.nombre area, concat(c1.nombre, ' - ',c1.descripcion) carr, i1.nombre inst";
		$sql.= " FROM correlatividades c";
		$sql.= " left join materias m1 on c.valor1=m1.idmateria";
		$sql.= " left join areas a1 on c.valor2=a1.idarea";
		$sql.= " left join carreras c1 on c.valor1=c1.idcarrera";
		$sql.= " left join instrumentos i1 on c.valor1=i1.idinstrumento";
		$sql.= " INNER JOIN reglas r ON c.idregla=r.id ";
		$sql.= " WHERE c.idcarrera=" . $car;		
		$sql.= " AND c.idmateria=" . $mat;		
		$sql.= " ORDER BY r.id, c.id";
		$arrData = $db->getSQLArray($sql);

		foreach ($arrData as &$row) {
			if ($row['idregla']==6) {
				$sql = "select nombre from materias where idmateria in (" . $row['valor1'] . ")";
				$a = $db->getSQLArray($sql);
				$m = "";
				foreach ($a as $r){
                                    $m.=$r['nombre'] . ",";
                                }
				$row['materias'] = substr($m,0,-1);
			}

		}
		$db->dbDisconnect();
		echo json_encode($arrData);
	}

	public function ajaxdeleteregla($id) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("correlativas/ajaxdeleteregla/" . $id);
		$sql = "DELETE FROM correlatividades WHERE id=" . $id;
		$db->delete($sql);
		$db->dbDisconnect();
		echo json_encode('OK');
	}

        //Regla Carrera Completa.
	public function ajaxreglacc($mat, $car, $carreraid) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("home/ajaxreglacc/" . $mat . "/" . $car . "/" . $carreraid);

		//validacion. que la carrera seleccionada no sea igual a la carrera $car
		if ($carreraid == $car) {
			$result="La carrera seleccionada no puede ser la misma de la materia actual";
		} else {
			$sql = "INSERT INTO correlatividades (idregla,idcarrera,idmateria,valor1,valor2) SELECT ";
			$sql.= "1";
			$sql.= "," . $car;
			$sql.= "," . $mat;
			$sql.= "," . $carreraid;
			$sql.= ",null";
			$db->insert($sql);
			$result="OK";
		}

		$db->dbDisconnect();
		echo json_encode($result);
	}

        //Regla Materia Cursada Aprobada
	public function ajaxreglamca($mat, $car, $materiaid) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("correlativas/ajaxreglamca/" . $mat . "/" . $car . "/" . $materiaid);

		//validacion. que la materia seleccionada no sea igual a la materia $mat
		if ($materiaid == $mat) {
			$result="La materia seleccionada no puede ser la misma de la materia actual";
		} else {
			$sql = "INSERT INTO correlatividades (idregla,idcarrera,idmateria,valor1,valor2) SELECT ";
			$sql.= "2";
			$sql.= "," . $car;
			$sql.= "," . $mat;
			$sql.= "," . $materiaid;
			$sql.= ",null";
			$db->insert($sql);
			$result="OK";
		}

		$db->dbDisconnect();
		echo json_encode($result);
	}
        
        //Regla Materia Cursada Aprobada y Area.
	public function ajaxreglamcaa($mat, $car, $materiaid, $areaid) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("correlativas/ajaxreglamcaa/" . $mat . "/" . $car . "/" . $materiaid . "/" . $areaid);

		//validacion. que la materia seleccionada no sea igual a la materia $mat
		if ($materiaid == $mat) {
			$result="La materia seleccionada no puede ser la misma de la materia actual";
		} else {
			$sql = "INSERT INTO correlatividades (idregla,idcarrera,idmateria,valor1,valor2) SELECT ";
			$sql.= "3";
			$sql.= "," . $car;
			$sql.= "," . $mat;
			$sql.= "," . $materiaid;
			$sql.= "," . $areaid;
			$db->insert($sql);
			$result="OK";
		}

		$db->dbDisconnect();
		echo json_encode($result);
	}

        //Regla Instrumento distinto de
	public function ajaxreglaid($mat, $car, $instrumentoid) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("home/ajaxreglaid/" . $mat . "/" . $car . "/" . $instrumentoid);

		//sin validaciones

		$sql = "INSERT INTO correlatividades (idregla,idcarrera,idmateria,valor1,valor2) SELECT ";
		$sql.= "4";
		$sql.= "," . $car;
		$sql.= "," . $mat;
		$sql.= "," . $instrumentoid;
		$sql.= ",null";
		$db->insert($sql);
		$result="OK";

		$db->dbDisconnect();
		echo json_encode($result);
	}

        //Regla instrumento igual a 
	public function ajaxreglaii($mat, $car, $instrumentoid) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("correlativas/ajaxreglaii/" . $mat . "/" . $car . "/" . $instrumentoid);

		//sin validaciones

		$sql = "INSERT INTO correlatividades (idregla,idcarrera,idmateria,valor1,valor2) SELECT ";
		$sql.= "5";
		$sql.= "," . $car;
		$sql.= "," . $mat;
		$sql.= "," . $instrumentoid;
		$sql.= ",null";
		$db->insert($sql);
		$result="OK";

		$db->dbDisconnect();
		echo json_encode($result);
	}

        //Regla materias cursando en simultaneo
	public function ajaxreglamcs($mat, $car, $materia1, $materia2, $materia3, $materia4, $materia5) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("correlativas/ajaxreglamcs/" . $mat . "/" . $car . "/" . $materia1 . "/" . $materia2 . "/" . $materia3 . "/" . $materia4 . "/" . $materia5);

		//validacion. al menos una materia tiene que ser distinto de cero
		//validacion. todas las que sean distinto de cero deben ser distinto a la materia actual
		$qMat = 0;
		$m = "";
		if ($materia1=="0") $qMat++; else $m.=$materia1 . ",";
		if ($materia2=="0") $qMat++; else $m.=$materia2 . ",";
		if ($materia3=="0") $qMat++; else $m.=$materia3 . ",";
		if ($materia4=="0") $qMat++; else $m.=$materia4 . ",";
		if ($materia5=="0") $qMat++; else $m.=$materia5 . ",";
		if ($qMat>3) {
			$result="Debe seleccionar al menos dos materias simultaneas";
		} else if ($materia1 == $mat || $materia2 == $mat || $materia3 == $mat || $materia4 == $mat || $materia5 == $mat) {
			$result="Ninguna materia seleccionada puede ser la misma que la materia actual";
		} else {
			$sql = "INSERT INTO correlatividades (idregla,idcarrera,idmateria,valor1,valor2) SELECT ";
			$sql.= "6";
			$sql.= "," . $car;
			$sql.= "," . $mat;
			$sql.= ",'" . substr($m, 0, -1) . "'";
			$sql.= ",null";
			$db->insert($sql);
			$result="OK";
		}

		$db->dbDisconnect();
		echo json_encode($result);
	}

        //Regla Materia Final aprobado
	public function ajaxreglamfa($mat, $car, $materiaid) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("correlativas/ajaxreglamfa/" . $mat . "/" . $car . "/" . $materiaid);

		//validacion. que la materia seleccionada no sea igual a la materia $mat
		if ($materiaid == $mat) {
			$result="La materia seleccionada no puede ser la misma de la materia actual";
		} else {
			$sql = "INSERT INTO correlatividades (idregla,idcarrera,idmateria,valor1,valor2) SELECT ";
			$sql.= "7";
			$sql.= "," . $car;
			$sql.= "," . $mat;
			$sql.= "," . $materiaid;
			$sql.= ",null";
			$db->insert($sql);
			$result="OK";
		}

		$db->dbDisconnect();
		echo json_encode($result);
	}
        
        //NO SE USA  Obtengo las correlativas de una materia para una carrera en particular
        public function getCorrelativas($mat, $car) {
		$db =& $this->POROTO->DB;
		$db->dbConnect("correlativas/getCorrelativas/" . $mat . "/" . $car);
		
                $sql = "SELECT c.idregla, c.id, r.descripcion, c.valor1, c.valor2 ";
		$sql.= " FROM correlatividades c";
		$sql.= " INNER JOIN reglas r ON c.idregla=r.id ";
                $sql.= " where c.idmateria=".$mat." and c.idcarrera=".$car;
		$arrData = $db->getSQLArray($sql);
                $correlativas=array();
                
		foreach ($arrData as &$row) {
                        $regla = array();
                        $regla['idregla']=$row['idregla']; //Regla ID
                        $regla['regla']=$row['descripcion']; //Regla Nombre
			switch ($row['idregla']) {
                            case "1": //Carrera Completa
                                        $sql = "SELECT nombre from carreras where idcarrera=".$row['valor1'];
                                        $arr = $db->getSQLArray($sql);
                                        $regla['carrera']=$arr[0]['nombre'];
                                        break;
                            case "2": //Materia Cursada Aprobada
                                        $sql = "SELECT nombre from viewmaterias where idmateria=".$row['valor1'];
                                        $arr = $db->getSQLArray($sql);
                                        $regla['materia']=$arr[0]['nombre'];
                                        break;
                            case "3": //Materia Cursada Aprobada y Área
                                        $sql = "SELECT nombre from viewmaterias where idmateria=".$row['valor1'];
                                        $arr = $db->getSQLArray($sql);
                                        $sql = "SELECT nombre from areas where idarea=".$row['valor2'];
                                        $arr2 = $db->getSQLArray($sql);
                                        $regla['materia']=$arr[0]['nombre'];
                                        $regla['area']=$arr2[0]['nombre'];
                                        break;
                            case "4": //Instrumento <> de
                                        $sql = "SELECT nombre from instrumentos where idinstrumento=".$row['valor1'];
                                        $arr = $db->getSQLArray($sql);
                                        $regla['idinstrumento']=$arr[0]['nombre'];
                                        break;
                            case "5": //Instrumento = a
                                        $sql = "SELECT nombre from instrumentos where idinstrumento=".$row['valor1'];
                                        $arr = $db->getSQLArray($sql);
                                        $regla['idinstrumento']=$arr[0]['nombre'];
                                        break;
                            case "6": //Materias cursando simultaneo. En este caso Valor1 puede tener uno o mas valores 2,3,4,6 por ej.
                                        $materiasSimultaneas=explode(",",$row['valor1']);
                                        foreach($materiasSimultaneas as $mat1){
                                            $sql = "SELECT distinct nombre from viewmaterias where idmateria=".$mat1;
                                            $arr = $db->getSQLArray($sql);
                                            $regla['materia']=$arr[0]['nombre'];  
                                            $correlativas[]=$regla;
                                            
                                            $regla=array();
                                            $regla['idregla']=$row['idregla']; //Regla ID
                                            $regla['regla']=$row['descripcion']; //Regla Nombre
                                        }
                                        break;
                            case "7": //Materia Final Aprobado    
                                        $sql = "SELECT nombre from viewmaterias where idmateria=".$row['valor1'];
                                        $arr = $db->getSQLArray($sql);
                                        $regla['materia']=$arr[0]['nombre'];
                                        break;                           
			}
                        if($regla['idregla']!=6)   $correlativas[]=$regla;
		}
		$db->dbDisconnect();
		echo json_encode($correlativas);
	}
        
        //Obtengo las correlativas de la materia para la carrera y el alumno en particular.
        //Resultado Correlativas[]
        //              Regla['idregla']
        //              Regla['nombre']
        //              Regla['materia']
        //              Regla['Carrera']
        //              Regla['Instrumento']
        //              Regla['estado']
        //              Regla['mensaje']
        public function getCorrelativasAlumno($idCarrera=0, $idMateria=0, $idComision=0, $idPersona=0,$idalumnocarrera=0) {
        $db =& $this->POROTO->DB; 
        $db->dbConnect("correlativas/getCorrelativasAlumno/".$idCarrera."/".$idMateria."/".$idComision."/".$idPersona."/".$idalumnocarrera);

        $instrumentosNoPuedenArmonico=array("1","4","5","8","12","14","18");
        $carrerasIMP_PIMP=array("2","3");
        
        //levanto datos de la carrera necesarios para correlatividades
        $sql = "SELECT idarea,idinstrumento";
        $sql.= " FROM alumnocarrera";
        $sql.= "  WHERE idpersona=" . $db->dbEscape($idPersona);
        $sql.= " AND idcarrera=" . $db->dbEscape($idCarrera);
        $sql.= " AND idalumnocarrera=" . $db->dbEscape($idalumnocarrera);
        $sql.= " AND estado in (1,3)";
        $dataAlumno = $db->getSQLArray($sql);
        
        
        //carreras completas del alumno
        $sql = "SELECT idcarrera";
        $sql.= " FROM alumnocarrera";
        $sql.= " WHERE idpersona=" . $db->dbEscape($idPersona);
        $sql.= " AND estado in (2,3)"; //Finalizadas y Pre Finalizadas
        $arr = $db->getSQLArray($sql);
        $carrerasCompletas = array();
        foreach ($arr as $row) $carrerasCompletas[]=$row['idcarrera'];

        //traer cursadas aprobadas (estados 3,4,5,7 [APROBADA,APROBADA POR EQUIVALENCIA,CURSADA APROBADA,APROBADA POR NIVELACION])
        $sql = "SELECT distinct idmateria";
        $sql.= " FROM alumnomateria";
        $sql.= " WHERE idpersona=" . $db->dbEscape($idPersona);
        $sql.= " AND idcarrera=" . $db->dbEscape($idCarrera);
        $sql.= " AND idalumnocarrera=" . $db->dbEscape($idalumnocarrera);
        $sql.= " AND idestadoalumnomateria in (";
        $sql.= $this->POROTO->Config['estado_alumnomateria_aprobada'].",";
        $sql.= $this->POROTO->Config['estado_alumnomateria_aprobadaxequiv'].",";
        $sql.= $this->POROTO->Config['estado_alumnomateria_cursadaaprobada'].",";
        $sql.= $this->POROTO->Config['estado_alumnomateria_nivelacion'].")";
        $arr = $db->getSQLArray($sql);
        $materiasCursadaAprobada = array();
        foreach ($arr as $row) $materiasCursadaAprobada[]=$row['idmateria'];
        
        //Cambio 20180310 para materias con reglas condicionales
        //Agrego al arreglo de materias aprobadas las materias que el alumno no necesita ya que no le corresponde cursar.
        //Instrumento Armonico I
        //Instrumento Armonico II
        //Practica Grupal Espontanea I
        //Practica Grupal Espontanea II
        //Practica Grupal Espontanea III
        //Solo en IMP o PIMP
        
        if(in_array($dataAlumno[0]["idinstrumento"],$instrumentosNoPuedenArmonico) && in_array($idCarrera,$carrerasIMP_PIMP)){
            //Si tiene alguno de los instrumentos y la carrera IMP o PIMP
            $materiasCursadaAprobada[]=4; //Instrumento Armonico 1
            $materiasCursadaAprobada[]=11;//Instrumento Armonico 2
        }
        if(in_array($idCarrera,$carrerasIMP_PIMP)){ //IMP o PIMP
            if($dataAlumno[0]["idarea"]!=3){ //Area <> de Folklore no necesitan practica grupal espontanea
            $materiasCursadaAprobada[]=123; //Practica Grupal Espontanea 1
            $materiasCursadaAprobada[]=122; //Practica Grupal Espontanea 2
            $materiasCursadaAprobada[]=124; //Practica Grupal Espontanea 3
            }
        }
        //Fin Cambio 20180310 para materias con reglas condicionales
        
        //traer finales aprobados (estados 3,4,7 [APROBADA,APROBADA POR EQUIVALENCIA,APROBADA POR NIVELACION])
        $sql = "SELECT distinct idmateria";
        $sql.= " FROM alumnomateria";
        $sql.= " WHERE idpersona=" . $db->dbEscape($idPersona);
        $sql.= " AND idcarrera=" . $db->dbEscape($idCarrera);
        $sql.= " AND idalumnocarrera=" . $db->dbEscape($idalumnocarrera);
        $sql.= " AND idestadoalumnomateria in (";
        $sql.= $this->POROTO->Config['estado_alumnomateria_aprobadaxequiv'].",";
        $sql.= $this->POROTO->Config['estado_alumnomateria_aprobada'].",";
        $sql.= $this->POROTO->Config['estado_alumnomateria_nivelacion'].")";
        $arr = $db->getSQLArray($sql);
        $materiasFinalAprobado = array();
        foreach ($arr as $row) $materiasFinalAprobado[]=$row['idmateria'];
        
        //Cambio 20180310 para materias con reglas condicionales
        //Agrego al arreglo de materias aprobadas las materias que el alumno no necesita ya que no le corresponde cursar.
        //Instrumento Armonico I
        //Instrumento Armonico II
        //Practica Grupal Espontanea I
        //Practica Grupal Espontanea II
        //Practica Grupal Espontanea III
        //Solo en IMP o PIMP
        if(in_array($dataAlumno[0]["idinstrumento"],$instrumentosNoPuedenArmonico) && in_array($idCarrera,$carrerasIMP_PIMP)){
            //Si tiene alguno de los instrumentos y la carrera IMP o PIMP
            $materiasFinalAprobado[]=4; //Instrumento Armonico 1
            $materiasFinalAprobado[]=11;//Instrumento Armonico 2
        }
        if(in_array($idCarrera,$carrerasIMP_PIMP)){ //IMP o PIMP
            if($dataAlumno[0]["idarea"]!=3){ //Area <> de Folklore no necesitan practica grupal espontanea
            $materiasFinalAprobado[]=123; //Practica Grupal Espontanea 1
            $materiasFinalAprobado[]=122; //Practica Grupal Espontanea 2
            $materiasFinalAprobado[]=124; //Practica Grupal Espontanea 3
            }
        }
        //Fin Cambio 20180310 para materias con reglas condicionales
        
        //traer materias cursando actualmente (estados 2 [CURSANDO])
        $sql = "SELECT distinct idmateria";
        $sql.= " FROM alumnomateria";
        $sql.= " WHERE idpersona=" . $db->dbEscape($idPersona);
        $sql.= " AND idcarrera=" . $db->dbEscape($idCarrera);
        $sql.= " AND idalumnocarrera=" . $db->dbEscape($idalumnocarrera);
        $sql.= " AND idestadoalumnomateria in (".$this->POROTO->Config['estado_alumnomateria_cursando'].")";
        $arr = $db->getSQLArray($sql);
        $materiasCursando = array();
        foreach ($arr as $row) $materiasCursando[]=$row['idmateria'];

        //traer materias cursando y con tilde de condicionalidad simultanea.
        $sql = "SELECT distinct idmateria";
        $sql.= " FROM alumnomateria am inner join alumnomateria_condicionalidades am2 on am.idalumnomateria=am2.idalumnomateria ";
        $sql.= " WHERE idpersona=" . $db->dbEscape($idPersona);
        $sql.= " AND idcarrera=" . $db->dbEscape($idCarrera);
        $sql.= " AND idalumnocarrera=" . $db->dbEscape($idalumnocarrera);
        $sql.= " and am2.condicionalregla6=1";
        $sql.= " AND idestadoalumnomateria in (".$this->POROTO->Config['estado_alumnomateria_cursando'].")";
        
        $sql.= " UNION ALL ";
        //Uno con todas las aprobadas.
        $sql.= " select distinct idmateria from alumnomateria ";
        $sql.= " WHERE idpersona=" . $db->dbEscape($idPersona);
        $sql.= " AND idcarrera=" . $db->dbEscape($idCarrera);
        $sql.= " AND idalumnocarrera=" . $db->dbEscape($idalumnocarrera);
        $sql.= " AND idestadoalumnomateria in (";
        $sql.= $this->POROTO->Config['estado_alumnomateria_cursadaaprobada'].",";
        $sql.= $this->POROTO->Config['estado_alumnomateria_aprobadaxequiv'].",";
        $sql.= $this->POROTO->Config['estado_alumnomateria_aprobada'].",";
        $sql.= $this->POROTO->Config['estado_alumnomateria_nivelacion'].")";
        
        $arr = $db->getSQLArray($sql);
        
        $materiasCondicional = array();
        foreach ($arr as $row) $materiasCondicional[]=$row['idmateria'];
        
        //Cambio 20180310 para materias con reglas condicionales
        //Agrego al arreglo de materias aprobadas las materias que el alumno no necesita ya que no le corresponde cursar.
        //Instrumento Armonico I
        //Instrumento Armonico II
        //Practica Grupal Espontanea I
        //Practica Grupal Espontanea II
        //Practica Grupal Espontanea III
        //Solo en IMP o PIMP
        if(in_array($dataAlumno[0]["idinstrumento"],$instrumentosNoPuedenArmonico) && in_array($idCarrera,$carrerasIMP_PIMP)){
            //Si tiene alguno de los instrumentos y la carrera IMP o PIMP
            $materiasCondicional[]=4; //Instrumento Armonico 1
            $materiasCondicional[]=11;//Instrumento Armonico 2
        }
        if(in_array($idCarrera,$carrerasIMP_PIMP)){ //IMP o PIMP
            if($dataAlumno[0]["idarea"]!=3){ //Area <> de Folklore no necesitan practica grupal espontanea
            $materiasCondicional[]=123; //Practica Grupal Espontanea 1
            $materiasCondicional[]=122; //Practica Grupal Espontanea 2
            $materiasCondicional[]=124; //Practica Grupal Espontanea 3
            }
        }
        //Fin Cambio 20180310 para materias con reglas condicionales
        

        //levanto las correlativas por cada materia
        $sql = " SELECT c.idregla,valor1,valor2,r.descripcion as nombre FROM correlatividades c inner join reglas r ";
        $sql.= " on c.idregla=r.id WHERE c.idmateria=" . $idMateria." and c.idcarrera=".$idCarrera;
        $correlativas = $db->getSQLArray($sql);

        //evaluo cada correlativa
        $correlativasAlumno=array();
        foreach ($correlativas as $corr) {
                $regla=array();
                $regla['idregla']=$corr['idregla'];
                $regla['nombre']=$corr['nombre'];
                switch ($corr['idregla']) {
                        case "1": //Carrera Completa
                                //Siempre voy a preguntar por cualquiera de las FOBA
                                //FOBA Canto 5
                                //FOBA Instrumento 1
                                //$sql = "SELECT nombre from carreras where idcarrera=".$corr['valor1'];
                                //$arr = $db->getSQLArray($sql);
                                $regla['carrera']="FOBA";
                                if (!in_array(1, $carrerasCompletas) && !in_array(5, $carrerasCompletas)) {
                                    $regla['estado']=false;
                                    $regla['mensaje']="Regla Carrera Aprobada ".$regla['carrera']."<br>";
                                }else{
                                    $regla['estado']=true;
                                    $regla['mensaje']="";
                                }
                                break;
                        case "2": //Materia Cursada Aprobada
                                $sql = "SELECT nombre from viewmaterias where idmateria=".$corr['valor1'];
                                $arr = $db->getSQLArray($sql);
                                $regla['materia']=$arr[0]['nombre'];
                                if (!in_array($corr['valor1'], $materiasCursadaAprobada)) {
                                    $regla['estado']=false;
                                    $regla['mensaje']="Regla Materia con cursada Aprobada ".$regla['materia']."<br>";
                                }else{
                                    $regla['estado']=true;
                                    $regla['mensaje']="";
                                }
                                break;
                        case "3": //Materia Cursada Aprobada y Área
                                    $sql = "SELECT nombre from viewmaterias where idmateria=".$corr['valor1'];
                                    $arr = $db->getSQLArray($sql);
                                    $regla['materia']=$arr[0]['nombre'];
                                    $sql = "SELECT nombre from areas where idarea=".$corr['valor2'];
                                    $arr2 = $db->getSQLArray($sql);
                                    $regla['area']=$arr2[0]['nombre'];
                                    //Ojo si el Area es 0 es porque el alumno no tiene area asignada.
                                    
                                if ($dataAlumno[0]['idarea']==$corr['valor2']){ //Solo evaluar la regla si es del area que tiene el alumno.
                                    if (!in_array($corr['valor1'], $materiasCursadaAprobada)){
                                        $regla['estado']=false;
                                        $regla['mensaje']="Regla Materia con cursada Aprobada ".$arr[0]['nombre']. " y del area ".$arr2[0]['nombre']."<br>";
                                    }
                                    else
                                    {
                                        $regla['estado']=true;
                                        $regla['mensaje']="";
                                    }
                                }
                                else{ //Si la regla no aplica porque es otra area, o porque el alumno no tiene area, devuelve true.
                                    $regla['estado']=true;
                                    $regla['mensaje']="";
                                }
                                break;
                        case "4": //Instrumento <> de
                                $sql = "SELECT nombre from instrumentos where idinstrumento=".$corr['valor1'];
                                $arr = $db->getSQLArray($sql);
                                $regla['instrumento']=$arr[0]['nombre'];
                                if ($dataAlumno[0]['idinstrumento']==$corr['valor1']){
                                    $regla['estado']=false;
                                    $regla['mensaje']="Regla Instrumento distinto de ".$arr[0]['nombre']."<br>";
                                }else{
                                    $regla['estado']=true;
                                    $regla['mensaje']="";
                                }
                                break;
                        case "5": //Instrumento = a
                                $sql = "SELECT nombre from instrumentos where idinstrumento=".$corr['valor1'];
                                $arr = $db->getSQLArray($sql);
                                $regla['instrumento']=$arr[0]['nombre'];
                                if ($dataAlumno[0]['idinstrumento']!=$corr['valor1']){
                                    $regla['estado']=false;
                                    $regla['mensaje']="Regla Instrumento igual a ".$arr[0]['nombre']."<br>";
                                }
                                else{  
                                    $regla['estado']=true;
                                    $regla['mensaje']="";
                                }
                                break;
                        case "6": //Materias cursando simultaneo. En este caso Valor1 puede tener uno o mas valores 2,3,4,6 por ej.
                                $materiasSimultaneas=explode(",",$corr['valor1']);
                                foreach($materiasSimultaneas as $mat1){
                                    $sql = "SELECT distinct nombre from viewmaterias where idmateria=".$mat1;
                                    $arr = $db->getSQLArray($sql);
                                    $regla['materia']=$arr[0]['nombre'];
                                    if (!in_array($mat1, $materiasCondicional)){
                                        $regla['estado']=false;
                                        $regla['mensaje']="Regla Materia Cursando en Simultaneo ".$arr[0]['nombre']."<br>";
                                    }
                                    else { 
                                        $regla['estado']=true;
                                        $regla['mensaje']="";
                                    }
                                    $correlativasAlumno[]=$regla;
                                    $regla=array();
                                    $regla['idregla']=$corr['idregla'];
                                    $regla['nombre']=$corr['nombre'];
                                }
                                break;
                        case "7": //Materia Final Aprobado
                                $sql = "SELECT nombre from viewmaterias where idmateria=".$corr['valor1'];
                                $arr = $db->getSQLArray($sql);
                                $regla['materia']=$arr[0]['nombre'];
                                if (!in_array($corr['valor1'], $materiasFinalAprobado)){
                                    $regla['estado']=false;
                                    $regla['mensaje']="Regla Materia con final Aprobado ".$regla['materia']."<br>";
                                }else{
                                     $regla['estado']=true;
                                     $regla['mensaje']="";
                                }
                                break;
                        //Cambio 20180310 nueva regla
                        case "8": //Area igual a:
                                $sql = "SELECT idarea,nombre from areas where idarea=".$corr['valor1'];
                                $arr = $db->getSQLArray($sql);
                                $regla['area']=$arr[0]['nombre']; 
                                if($dataAlumno[0]['idarea']!=$corr['valor1']){ //Vale la regla
                                     $regla['estado']=false;
                                     $regla['mensaje']="Regla Area igual a ".$regla['area'];
                                }else{
                                     $regla['estado']=true;
                                     $regla['mensaje']="";
                                }
                                break;
                        //Fin Cambio 20180310
                } //Switch
            if($regla['idregla']!=6)    $correlativasAlumno[]=$regla;
        }

        return $correlativasAlumno;
}

public function ajaxCorrelativasAlumno($idCarrera=0, $idMateria=0, $idPersona=0,$idalumnocarrera=0){
       echo json_encode($this->getCorrelativasAlumno($idCarrera, $idMateria, 0, $idPersona,$idalumnocarrera));
   }
}