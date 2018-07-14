<?php

class AlumnoCarreraModel {

    private $PDO;
    private $POROTO;
    
    public function __construct($poroto) {
        $this->POROTO = $poroto;
        $this->PDO = $poroto->PDO;
    }

    public function getFobasCompletas($idpersona) {
        //Traer las ultimas 100 operaciones pagas de la persona
        $sql = "Select ac1.idpersona, ac1.idcarrera, ac1.idarea, ac1.idinstrumento, i.nombre as instrumento, c.nombre as carrera
            from alumnocarrera ac1 
            INNER JOIN carreras c on (ac1.idcarrera = c.idcarrera)
            inner join instrumentos i on (ac1.idinstrumento = i.idinstrumento)
            where ac1.idcarrera in (1,5) 
            and ac1.estado in (2,3)
            and ac1.idpersona=:idpersona
            and ac1.idpersona not in (
                select ac2.idpersona 
                from alumnocarrera ac2 
                where ac2.idcarrera in (2, 3, 4) 
                and ac2.idinstrumento=ac1.idinstrumento 
                and ac2.estado in (1, 3)
                )";
        $params = array(":idpersona" => $idpersona);
        $this->PDO->execute($sql, "AlumnoCarreraModel/getFobasCompletas", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getAlumnoCarrera() {
        $sql= " select ac.idpersona,ac.idalumnocarrera,ac.idcarrera,concat(p.nombre,' ',p.apellido) as alumno,c.nombre as carrera,ac.idinstrumento,i.nombre as instrumento,ac.idarea,ac.estado from alumnocarrera ac ";
        $sql.=" inner join personas p on ac.idpersona=p.idpersona ";
        $sql.=" inner join carreras c on ac.idcarrera=c.idcarrera ";
        $sql.=" inner join instrumentos i on ac.idinstrumento=i.idinstrumento ";
        $sql.=" where ac.estado in (1,3)";
        
        $this->PDO->execute($sql, "AlumnoCarreraModel/getAlumnoCarrera");
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function getAvanzadas2anio($idpersona) {
        //Traer las ultimas 100 operaciones pagas de la persona
        $sql = "SELECT ac.idpersona, ac.idnivel, cn.descripcion as nnombre, c.nombre as cnombre,
                c.descripcion as cdescripcion, i.idinstrumento, i.nombre as inombre,
                c.idcarrera
                FROM alumnocarrera ac
                INNER JOIN carreras c ON (ac.idcarrera = c.idcarrera)
                INNER JOIN instrumentos i ON (ac.idinstrumento = i.idinstrumento)
                INNER JOIN carreraniveles cn ON (cn.id = ac.idnivel)
                WHERE ac.idpersona = :idpersona
                AND ac.idcarrera IN (2,3)
                AND ac.estado in (1,3)
                AND ac.idarea=0
                AND Exists(
                    select am.idalumnomateria 
                    from alumnomateria am 
                    where am.idpersona = ac.idpersona
                    and am.idmateria in (7,9,8,4,3,2,1,5,6,36,32,33,34,35) 
                    and am.idestadoalumnomateria in (3,4,5) 
                    and am.idcarrera = ac.idcarrera
                )";
        $params = array(":idpersona" => $idpersona);
        $this->PDO->execute($sql, "AlumnoCarreraModel/getAvanzadas2anio", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getAreas() {
        $sql = "SELECT * FROM areas";
        $this->PDO->execute($sql, "AlumnoCarreraModel/getAreas");
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getCarreras() {
        $sql = "SELECT * FROM carreras where idcarrera in (2, 3, 4)";
        $this->PDO->execute($sql, "AlumnoCarreraModel/getAreas");
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function matricularcarrera($params) {
        $sql = "INSERT INTO alumnocarrera(idpersona, idcarrera, idinstrumento, "
                . "idarea, idnivel, fechainscripcion, estado, "
                . "usucrea, fechacrea) "
                . "VALUES (:idpersona, :idcarrera, :idinstrumento, :idarea, "
                . ":idnivel, :fechainscripcion, :estado, "
                . ":usucrea, :fechacrea)";

        $params = array(":idpersona" => $params["idpersona"],
            ":idcarrera" => $params["carrera"],
            ":idinstrumento" => $params["instrumento"],
            ":idarea" => 0,
            ":idnivel" => $params["idnivel"],
            ":fechainscripcion" => date('Y-m-d H:m:s'),
            ":estado" => 1,
            ":usucrea" => $params["usuario"],
            ":fechacrea" => date('Y-m-d H:m:s')
            );
        
        $result = $this->PDO->execute($sql, "AlumnoCarreraModel/matricularcarrera", $params);
        return $result;
    }

    public function inscripcionarea($params) {
        $sql = "UPDATE alumnocarrera"
                . " SET idarea = :idarea,"
                . " usumodi = :usumodi,"
                . " fechamodi = :fechamodi"
                . " where idpersona = :idpersona"
                . " and idcarrera = :idcarrera";

        $params = array(
            ":idpersona" => $params["idpersona"],
            ":idcarrera" => $params["carrera"],
            ":idarea" => $params["area"],
            ":usumodi" => $params["usuario"],
            ":fechamodi" => date('Y-m-d H:m:s')
            );
        
        $result = $this->PDO->execute($sql, "AlumnoCarreraModel/inscripcionarea", $params);
        return $result;
    }

    public function getNivelPorCarrera($idcarrera) {
        $sql = "SELECT id"
                . " FROM carreraniveles"
                . " where idcarrera = :idcarrera and vistaInscripcion = 1";
        $params = array(":idcarrera" => $idcarrera);
        $this->PDO->execute($sql, "AlumnoCarreraModel/getNivelPorCarrera", $params);
        $result = $this->PDO->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function verificarCambioEstadoCarrera($idAlumnoCarrera,$idEstado){
            //Verifica dependiendo de la carrera y al estado al que quiero pasar, si es posible hacerlo.
            //Devuelve un string vacio si esta OK, un string con el mensaje si no se puede.
            //$db =& $this->POROTO->DB; 
            //$ses =& $this->POROTO->Session;
            //$db->dbConnect("analiticoalumno/verificarCambioEstadoCarrera/" . $idAlumnoCarrera);
            
            $bOk = true;
            $sMsg="";
            
            $dataIdAlumnoCarrera = $idAlumnoCarrera;
            $dataIdEstado = $idEstado;

            //Definiciones a tener en cuenta para evaluar si es posible finalizar o pre finalizar.
            $instrumentosNoPuedenArmonico=array("1","4","5","8","12","14","18");
            $carrerasIMP_PIMP=array("2","3");
            $carrerasFOBA=array("1","5");
            $fobaCantoCursadas = array("104","102","103","106","105","99","100","107");
            $fobaCantoAprobadas = array("101");
            $fobaInstrumentoCursadas = array("104","102","103","105","99","100");
            $fobaInstrumentoAprobadas = array("101"); 

            //Obtengo datos adicionales de AlumnoCarrera
            $sql= "select idpersona,idcarrera,idinstrumento,idarea,estado from alumnocarrera where idalumnocarrera=".$dataIdAlumnoCarrera;
            //$alumnocarrera = $db->getSQLArray($sql);
            $this->PDO->execute($sql, "AlumnoCarreraModel/DatosCarrera");
            $alumnocarrera = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
            
            if(count($alumnocarrera)==0){
                $bOk=false;
                $sMsg="Error al intentar obtener los datos de la carrera.";
            }else{
                $idInstrumento=$alumnocarrera[0]["idinstrumento"];
                $idCarrera=$alumnocarrera[0]["idcarrera"];
                $idArea=$alumnocarrera[0]["idarea"];
                $dataIdAlumno=$alumnocarrera[0]["idpersona"];
                $dataIdEstadoActual=$alumnocarrera[0]["estado"];
            }
            
            // 1) Chequeo si el estado nuevo no es igual al actual.
            if($dataIdEstado==$dataIdEstadoActual && $bOk){
                $bOk=false;
                $sMsg="El estado actual de la carrera es igual al estado al cual se desea cambiar. No es posible el cambio.";
            }
            
            if ($dataIdEstado == 2 && $bOk) { //Pasar a estado Finalizada                       
                    $sql= "select cm.idmateria from carreramateria cm ";
                    $sql.="inner join materias m on cm.idmateria=m.idmateria ";
                    $sql.="left join alumnomateria am on cm.idmateria=am.idmateria and ";
                    $sql.="(am.idestadoalumnomateria in (".$this->POROTO->Config['estado_alumnomateria_aprobada'].",";
                    $sql.=$this->POROTO->Config['estado_alumnomateria_aprobadaxequiv'].",";
                    $sql.=$this->POROTO->Config['estado_alumnomateria_nivelacion'].")) ";
                    $sql.="and am.idpersona=". $dataIdAlumno ." ";
                    $sql.="and am.idalumnocarrera=". $dataIdAlumnoCarrera ." ";
                    $sql.="where cm.idcarrera=".$idCarrera." and m.estado=1 ";
                    //Si esta en IMP o PIMP y no le corresponde armonico.
                    if(in_array($idInstrumento,$instrumentosNoPuedenArmonico) && in_array($idCarrera,$carrerasIMP_PIMP)){
                        $sql.="and m.idmateria not in (4,11) ";
                    }
                    //Si esta en FOBA y no le corresponde armonico.
                    if(in_array($idInstrumento,$instrumentosNoPuedenArmonico) && in_array($idCarrera,$carrerasFOBA)){
                        $sql.="and m.idmateria not in (105) ";
                    }
                    //Si esta en IMP o PIMP y area distinta de folklore no le corresponde cursar espontanea.
                    if(($idArea!=3) && in_array($idCarrera,$carrerasIMP_PIMP)){
                        $sql.="and m.idmateria not in (122,123,124) ";
                    }
                    $sql.="group by cm.idmateria ";
                    $sql.="having max(am.idalumnomateria) is null";
                    //$arr = $db->getSQLArray($sql);
                    $this->PDO->execute($sql, "AlumnoCarreraModel/DatosCarrera");
                    $arr = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
                    if (count($arr)>0) {
                        $bOk = false; //Si trae registros no permito cambiar estado.                        
                        $sMsg="No es posible finalizar la carrera. El alumno posee materias sin aprobar.";
                    }
		}
            
                if ($dataIdEstado == 3 && !in_array($idCarrera,$carrerasFOBA)  && $bOk){ //Chequeo si es posible pasar a estado pre finalizado
                    $bOk=false;
                    $sMsg="No es posible pre finalizar la carrera. Solamente las FOBA aceptan el estado PRE FINALIZAR.";
                }
                
                if ($dataIdEstado == 3 && $bOk) { //PRE Finalizada                       
                        $sql= " select idmateria from alumnomateria ";
                        $sql.=" where idpersona=".$dataIdAlumno." and idalumnocarrera =".$dataIdAlumnoCarrera;
                        $sql.=" and idestadoalumnomateria in (";
                        $sql.=$this->POROTO->Config['estado_alumnomateria_aprobada'].",";
                        $sql.=$this->POROTO->Config['estado_alumnomateria_aprobadaxequiv'].",";
                        $sql.=$this->POROTO->Config['estado_alumnomateria_nivelacion'].")";
                        //$materiasAprobadas = $db->getSQLArray($sql);
                        $this->PDO->execute($sql, "AlumnoCarreraModel/DatosCarrera");
                        $materiasAprobadas = $this->PDO->fetchAll(PDO::FETCH_ASSOC);

                        $sql= " select idmateria from alumnomateria where idpersona=".$dataIdAlumno;
                        $sql.=" and idalumnocarrera =".$dataIdAlumnoCarrera;
                        $sql.=" and idestadoalumnomateria =";
                        $sql.=$this->POROTO->Config['estado_alumnomateria_cursadaaprobada'];
                        //$materiasCursadaAprobada = $db->getSQLArray($sql);
                        $this->PDO->execute($sql, "AlumnoCarreraModel/DatosCarrera");
                        $materiasCursadaAprobada = $this->PDO->fetchAll(PDO::FETCH_ASSOC);

                        $cumple=true;
                        if($idCarrera==1){ //Foba Instrumento
                        foreach($fobaInstrumentoCursadas as $fo){
                            if($cumple){
                                $encontre=false;
                                foreach ($materiasCursadaAprobada as $value) {
                                    if($value["idmateria"]==$fo){
                                            $encontre=true;
                                    }
                                }
                                foreach ($materiasAprobadas as $value) {
                                    if($value["idmateria"]==$fo){
                                            $encontre=true;
                                    }
                                }
                                //Si el instrumento del alumno es uno de los indicados, no es necesario Instrumento Armonico III
                                if($fo=="105"){
                                    if(in_array($idInstrumento,$instrumentosNoPuedenArmonico)){
                                        $encontre=true;
                                    }
                                }
                            }
                            if(!$encontre){
                                $cumple=false;
                            }
                        }
                        foreach($fobaInstrumentoAprobadas as $fo){
                            if($cumple){
                                $encontre=false;
                                foreach ($materiasAprobadas as $value) {
                                    if($value["idmateria"]==$fo){
                                            $encontre=true;
                                    }
                                }
                                //Si el instrumento del alumno es uno de los indicados, no es necesario Instrumento Armonico III
                                if($fo=="105"){
                                    if(in_array($idInstrumento,$instrumentosNoPuedenArmonico)){
                                        $encontre=true;
                                    }
                                }
                            }
                            if(!$encontre){
                                $cumple=false;
                            }
                        }
                        }
                        if($idCarrera==5){ //Foba Canto
                        foreach($fobaCantoCursadas as $fo){
                            if($cumple){
                                $encontre=false;
                                foreach ($materiasCursadaAprobada as $value) {
                                    if($value["idmateria"]==$fo){
                                            $encontre=true;
                                    }
                                }
                                foreach ($materiasAprobadas as $value) {
                                    if($value["idmateria"]==$fo){
                                            $encontre=true;
                                    }
                                }
                                //Si el instrumento del alumno es uno de los indicados, no es necesario Instrumento Armonico III
                                if($fo=="105"){
                                    if(in_array($idInstrumento,$instrumentosNoPuedenArmonico)){
                                        $encontre=true;
                                    }
                                }
                            }
                            if(!$encontre){
                                $cumple=false;
                            }
                        }
                        foreach($fobaCantoAprobadas as $fo){
                            if($cumple){
                                $encontre=false;
                                foreach ($materiasAprobadas as $value) {
                                    if($value["idmateria"]==$fo){
                                            $encontre=true;
                                    }
                                }
                                //Si el instrumento del alumno es uno de los indicados, no es necesario Instrumento Armonico III
                                if($fo=="105"){
                                    if(in_array($idInstrumento,$instrumentosNoPuedenArmonico)){
                                        $encontre=true;
                                    }
                                }
                            }
                            if(!$encontre){
                                $cumple=false;
                            }
                        }
                        }
                        if(!$cumple){
                            $sMsg="No es posible pre finalizar la carrera. El alumno no cumple con las condiciones mínimas para pre finalizar una carrera.";
                        }
                        
                        $bOk=$cumple;
                }
                
                //$db->dbDisconnect();
                return $sMsg; //Si el MSG tengo algo es porque no es posible el cambio, sino, lo hago.
        }
        
    public function guardarCambioEstadoCarrera($params){
        $valorFechaFinalizada = "null";
        if ($params["idestado"] == 2){  //Finalizada
                $valorFechaFinalizada = "CURRENT_TIMESTAMP";
        }
        
        $sql = " update alumnocarrera set estado=:idestado,fechafinalizada=".$valorFechaFinalizada."," ;
        $sql.= " usumodi=:usumodi,";
        $sql.= " fechamodi=CURRENT_TIMESTAMP";
        $sql.= " where idalumnocarrera=:idalumnocarrera";
        
        $params = array(":idalumnocarrera" => $params["idalumnocarrera"],
            ":idestado" => $params["idestado"],
            ":usumodi" => $params["usuario"]
            );
        
        $result = $this->PDO->execute($sql, "AlumnoCarreraModel/guardarCambioEstadoCarrera", $params);
        return $result;
    }

}
