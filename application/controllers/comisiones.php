<?php

class Comisiones {

    private $POROTO;

    function __construct($poroto) {
        $this->POROTO = $poroto;
        $this->POROTO->pageHeader[] = array("label" => "Dashboard", "url" => "");
        include($this->POROTO->ModelPath . '/materia.php');
    }

    function defentry() {
        if ($this->POROTO->Session->isLogged()) {
            header("Location: /", TRUE, 302);
        } else {
            include($this->POROTO->ViewPath . "/-login.php");
        }
    }

    public function gestion($idCarrera = 0, $idMateria = 0) {
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $lib = & $this->POROTO->Libraries['siteLibrary'];

        //Cambio 38 Leo 20170706
        if ($idCarrera == 0 && $idMateria == 0) {
            if (!$ses->tienePermiso('', 'Gestion de Comisiones Acceso desde Menu')) {
                $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
                header("Location: /", TRUE, 302);
                exit();
            }
        }
        //Fin Cambio 38 Leo 20170706

        $db->dbConnect("comisiones/gestion");
        $arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());

        $sql = "SELECT idcarrera,descripcion";
        $sql .= " FROM carreras";
        $sql .= "  WHERE estado=1";
        $sql .= "  ORDER BY 2";
        $viewDataCarreras = $db->getSQLArray($sql);

        //Se usa para cuando vuelvo a la pag (back)
        if ($idMateria != 0) {
            $sql = "select m.idmateria id, m.materiacompleta as descripcion";
            $sql .= " from viewmaterias m";
            $sql .= " where m.idcarrera=" . $db->dbEscape($idCarrera);
            $sql .= " and m.estado=1";
            $sql .= " order by m.orden";
            $viewDataMaterias = $db->getSQLArray($sql);
        }

        $db->dbDisconnect();

        $pageTitle = "Gestión de Comisiones";
        include($this->POROTO->ViewPath . "/-header.php");
        include($this->POROTO->ViewPath . "/gestion-comisiones.php");
        include($this->POROTO->ViewPath . "/-footer.php");
    }

    public function ajaxmaterias($idCarrera) { //OK
        $db = & $this->POROTO->DB;
        $db->dbConnect("comisiones/ajaxmaterias/" . $idCarrera);

        $sql = "select m.idmateria id, m.materiacompleta as descripcion";
        $sql .= " from viewmaterias m";
        $sql .= " where m.idcarrera=" . $db->dbEscape($idCarrera);
        $sql .= " and m.estado=1";
        $sql .= " order by orden";
        $result = $db->getSQLArray($sql);
        $db->dbDisconnect();

        echo json_encode($result);
    }

    public function ajaxlist($idMateria, $anio) { //OK
        //Trae los resultados de la busqueda de comisiones
        $db = & $this->POROTO->DB;
        $db->dbConnect("comisiones/ajaxlist/" . $idMateria . "/" . $anio);

        $sql = "SELECT c.idcomision, c.codigo, c.nombre, c.turno, c.estado,c.matriculacionhabilitada, ";
        $sql .= " ch.dia,date_format(ch.inicio,'%H:%i') inicio, c.aula, date_format(ch.fin,'%H:%i') fin";
        $sql .= " FROM comisiones c";
        $sql .= " left JOIN comhorario ch on ch.idcomision=c.idcomision";
        $sql .= " WHERE c.idmateria=" . $db->dbEscape($idMateria);
        $sql .= " and c.anio=" . $db->dbEscape($anio);
        $sql .= " ORDER BY c.codigo,c.nombre,c.idcomision,ch.dia,ch.inicio";

        $arrData = $db->getSQLArray($sql);

        if (count($arrData) == 0) {
            $result = $arrData;
        } else {
            $result = array();
            $comision = $arrData[0]['idcomision'];
            $codigo = $arrData[0]['codigo'];
            $nombre = $arrData[0]['nombre'];
            $turno = $arrData[0]['turno'];
            $estado = $arrData[0]['estado'];
            $aula = $arrData[0]['aula'];
            $matriculacion = $arrData[0]['matriculacionhabilitada'];

            if ($arrData[0]['dia'] != "") {
                $horarios = $arrData[0]['dia'] . " " . $arrData[0]['inicio'] . " a " . $arrData[0]['fin'];
            } else {
                $horarios = "";
            }
            $firstRow = true;
            foreach ($arrData as $row) { //agrupo para tener los horarios en un solo string
                if ($comision != $row['idcomision']) {
                    $result[] = array("idcomision" => $comision,
                        "codigo" => $codigo,
                        "nombre" => $nombre,
                        "turno" => $turno,
                        "estado" => $estado,
                        "matriculacion" => $matriculacion,
                        "horarios" => $horarios,
                        "aula" => $aula);
                    $comision = $row['idcomision'];
                    $codigo = $row['codigo'];
                    $nombre = $row['nombre'];
                    $turno = $row['turno'];
                    $estado = $row['estado'];
                    $aula = $row['aula'];
                    $matriculacion = $row['matriculacionhabilitada'];
                    if ($row['dia'] != "")
                        $horarios = $row['dia'] . " " . $row['inicio'] . " a " . $row['fin'];
                    else
                        $horarios = "";
                } else {
                    if ($firstRow) {
                        $firstRow = false;
                    } else {
                        if ($row['dia'] != "") {
                            $horarios .= " - " . $row['dia'] . " " . $row['inicio'] . " a " . $row['fin'];
                        }
                    }
                }
            }
            $result[] = array("idcomision" => $comision,
                "codigo" => $codigo,
                "nombre" => $nombre,
                "turno" => $turno,
                "estado" => $estado,
                "matriculacion" => $matriculacion,
                "horarios" => $horarios,
                "aula" => $aula);
        }
        echo json_encode($result);
    }

    public function habilitar($idComision, $idCarrera, $idMateria) { //OK
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;

        //Cambio 38 Leo 20170706
        if (!$ses->tienePermiso('', 'Gestion de Comisiones Edicion (ABM)')) {
            $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /gestion-comisiones/", TRUE, 302);
            exit();
        }
        //Fin Cambio 38 Leo 20170706

        $db->dbConnect("comisiones/habilitar/" . $idComision);
        $sql = "update comisiones set estado=1 where idcomision=" . $db->dbEscape($idComision);
        $db->update($sql);
        $db->dbDisconnect();

        $ses->setMessage("Comisión Habilitada", SessionMessageType::Success);
        header("Location: /gestion-comisiones/" . $idCarrera . "/" . $idMateria, TRUE, 302);
    }

    public function deshabilitar($idComision, $idCarrera, $idMateria) { //OK
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;

        //Cambio 38 Leo 20170706
        if (!$ses->tienePermiso('', 'Gestion de Comisiones Edicion (ABM)')) {
            $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /gestion-comisiones/", TRUE, 302);
            exit();
        }
        //Fin Cambio 38 Leo 20170706

        $db->dbConnect("comisiones/habilitar/" . $idComision);
        $sql = "update comisiones set estado=0 where idcomision=" . $db->dbEscape($idComision);
        $db->update($sql);
        $db->dbDisconnect();

        $ses->setMessage("Comisión Deshabilitada", SessionMessageType::Success);
        header("Location: /gestion-comisiones/" . $idCarrera . "/" . $idMateria, TRUE, 302);
    }

    public function crear($idCarrera, $idMateria) { //OK
        $materiaModel = new MateriaModel($this->POROTO);
        $validationErrors = array();
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;

        //Cambio 38 Leo 20170706
        if (!$ses->tienePermiso('', 'Gestion de Comisiones Edicion (ABM)')) {
            $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /gestion-comisiones/", TRUE, 302);
            exit();
        }
        //Fin Cambio 38 Leo 20170706

        $db->dbConnect("comisiones/crear/" . $idCarrera . "/" . $idMateria);
        $dataIdCarrera = $db->dbEscape($idCarrera);
        $dataIdMateria = $db->dbEscape($idMateria);
        $newProfesores = array();
        $newHorarios = array();
        $newCupos = array();

        if (isset($_POST['codigo'])) { //entro por el post con los datos para guardar.
            $dataCodigo = mb_strtoupper($db->dbEscape(trim($_POST['codigo'])), 'UTF-8');
            $dataAnio = $db->dbEscape(intval(trim($_POST['anio'])));
            $dataAula = mb_strtoupper($db->dbEscape(trim($_POST['aula'])));
            // Area es el genero.
            $dataArea = $db->dbEscape(trim($_POST['area']));
            $dataInstrumento = $db->dbEscape(trim($_POST['instrumento']));
            $dataNombre = mb_strtoupper($db->dbEscape(trim($_POST['nombre'])), 'UTF-8');
            $dataCupof = mb_strtoupper($db->dbEscape(trim($_POST['cupof'])), 'UTF-8');
            $dataTurno = $db->dbEscape(trim($_POST['turno']));
            if (isset($_POST['matriculacion']))
                $dataMatriculacion = "1";
            else
                $dataMatriculacion = "0";

            //VALIDAR
            // codigo - obligatorio | maxlength 10
            if ($dataCodigo == "")
                $validationErrors['codigo'] = "El campo Nombre Comisión es obligatorio";
            if (strlen($dataCodigo) > 10)
                $validationErrors['codigo'] = "El campo Nombre Comisión puede contener como máximo 10 caracteres";
            // anio - obligatorio | valor entre 1900 y 2100
            if ($dataAnio == "" || $dataAnio == 0)
                $validationErrors['anio'] = "El campo Año es obligatorio";
            if ($dataAnio < 1900 || $dataAnio > 2100)
                $validationErrors['codigo'] = "El campo Año contiene un valor inválido";
            // nombre - obligatorio | maxlength 250
            if ($dataNombre == "")
                $validationErrors['nombre'] = "El campo Nombre es obligatorio";
            if (strlen($dataNombre) > 250)
                $validationErrors['nombre'] = "El campo Nombre puede contener como máximo 250 caracteres";
            if (strlen($dataCupof) > 45)
                $validationErrors['cupof'] = "El campo Cupof puede contener como máximo 45 caracteres";
            // turno - obligatorio
            if ($dataTurno == "0")
                $validationErrors['turno'] = "El campo Turno es obligatorio";
            
            $materia = $materiaModel->getMateriaById($dataIdMateria);
            if ( !$materia["materia_instrumento"] && $dataInstrumento){
                $validationErrors['instrumento'] = "La materia no acepta instrumentos.";
            }             
            if ( $materia["materia_instrumento"] && !$dataInstrumento){
                $validationErrors['instrumento'] = "La materia requiere elegir un instrumento.";
            } 
            if ( !$materia["materia_genero"] && $dataArea){
                $validationErrors['area'] = "La materia no acepta generos.";
            }             
            if ( $materia["materia_genero"] && !$dataArea){
                $validationErrors['area'] = "La materia requiere elegir un genero.";
            } 
            
            if (isset($_POST['profesores'])) {
                //if (count($_POST['profesores'])==0) $validationErrors['profesores'] = "Debe tener cargado al menos un profesor";
                //por cada profesor, valido y recreo los valores en $newProfesores para el repost
                foreach ($_POST['profesores'] as $prof) { //4028~**~FLAMINMAN,DÉBORAH AILIN~**~1~**~TITULAR
                    $arr = explode("~**~", $prof);
                    if (count($arr) == 4) {
                        $newProfesores[] = array("idpersona" => $arr[0],
                            "apellidonombre" => $arr[1],
                            "situacionrevista_id" => $arr[2],
                            "situacionrevista_descripcion" => $arr[3]);
                    } else {
                        $validationErrors['profesores'] = "Se detectaron valores inválidos en el listado de profesores";
                    }
                }
            }
            /* else {
              $validationErrors['profesores'] = "Debe tener cargado al menos un profesor";
              } */

            if (isset($_POST['horarios'])) {
                //if (count($_POST['horarios'])==0) $validationErrors['horarios'] = "Debe tener cargado al menos un horario";
                //por cada horario, valido y recreo los valores en $newHorarios para el repost
                foreach ($_POST['horarios'] as $hor) { //LUNES09001800
                    $horaH = substr($hor, -4);
                    $horaD = substr($hor, -8, 4);
                    $dia = substr($hor, 0, -8);

                    if (strlen($dia) > 0) {
                        $newHorarios[] = array("dia" => $dia,
                            "desde4" => $horaD,
                            "hasta4" => $horaH,
                            "desde" => substr($horaD, 0, 2) . ":" . substr($horaD, 2, 2),
                            "hasta" => substr($horaH, 0, 2) . ":" . substr($horaH, 2, 2));
                    } else {
                        $validationErrors['horarios'] = "Se detectaron valores inválidos en el listado de horarios";
                    }
                }
            }
            /* else {
              $validationErrors['horarios'] = "Debe tener cargado al menos un horario";
              } */

            if (isset($_POST['cupos'])) {
                if (count($_POST['cupos']) == 0)
                    $validationErrors['cupos'] = "Debe tener cargado al menos un cupo";

                //por cada cupo, valido y recreo los valores en $newCupos para el repost
                foreach ($_POST['cupos'] as $cupo) { //NOMBREDELCUP~**~25~**~25
                    $arr = explode("~**~", $cupo);
                    if (count($arr) == 3) {
                        $newCupos[] = array("cuponombre" => $arr[0],
                            "disponibilidad" => $arr[1],
                            "cantdisponible" => $arr[2]);
                    } else {
                        $validationErrors['cupos'] = "Se detectaron valores inválidos en el listado de cupos";
                    }
                }
            } else {
                $validationErrors['cupos'] = "Debe tener cargado al menos un cupo";
            }


            if (count($validationErrors) == 0) {
                $sql = "INSERT INTO comisiones (idmateria, idarea, idinstrumento, nombre, codigo, anio, aula, turno, estado, ";
                $sql .= " matriculacionhabilitada, cupof, usucrea, fechacrea, usumodi, fechamodi) SELECT ";
                $sql .= $dataIdMateria;
                $sql .= "," . $dataArea;
                $sql .= "," . $dataInstrumento;
                $sql .= ",'" . $dataNombre . "'";
                $sql .= ",'" . $dataCodigo . "'";
                $sql .= "," . $dataAnio;
                if($dataAula==""){
                    $sql.=",null";
                }
                else
                {
                    $sql .= ",'" . $dataAula . "'";
                }
                $sql .= ",'" . $dataTurno . "'";
                $sql .= ",1";
                $sql .= "," . $dataMatriculacion;
                $sql .= ",'" . $dataCupof . "'";
                $sql .= ",'" . $ses->getUsuario() . "'";
                $sql .= ",CURRENT_TIMESTAMP";
                $sql .= ",null";
                $sql .= ",null";

                $db->begintrans();
                $newIdComision = $db->insert($sql, '', true);
                $bOk = $newIdComision;

                if (isset($newProfesores)) {
                    foreach ($newProfesores as $profesor) {
                        $sql = "INSERT INTO comprofesor (idpersona, idcomision, situacionrevista_id, usucrea, fechacrea, usumodi, fechamodi) SELECT ";
                        $sql .= $profesor['idpersona'];
                        $sql .= "," . $newIdComision;
                        $sql .= "," . $profesor['situacionrevista_id'];
                        $sql .= ",'" . $ses->getUsuario() . "'";
                        $sql .= ",CURRENT_TIMESTAMP";
                        $sql .= ",null";
                        $sql .= ",null";
                        if ($bOk !== false)
                            $bOk = $db->insert($sql, '', true);
                    }
                }

                if (isset($newHorarios)) {
                    foreach ($newHorarios as $horario) {
                        $sql = "INSERT INTO comhorario (idcomision, dia, inicio, fin) SELECT ";
                        $sql .= $newIdComision;
                        $sql .= ",'" . $horario['dia'] . "'";
                        $sql .= ",'" . $this->POROTO->Config['dia_default_horario'] . " " . $horario['desde'] . "'";
                        $sql .= ",'" . $this->POROTO->Config['dia_default_horario'] . " " . $horario['hasta'] . "'";
                        if ($bOk !== false)
                            $bOk = $db->insert($sql, '', true);
                    }
                }

                if (isset($newCupos)) {
                    foreach ($newCupos as $cupo) {
                        $sql = "INSERT INTO comcupos (idcomision, descripcion, cantidad, cantdisponible) SELECT ";
                        $sql .= $newIdComision;
                        $sql .= ",'" . $cupo['cuponombre'] . "'";
                        $sql .= "," . $cupo['disponibilidad'];
                        $sql .= "," . $cupo['cantdisponible'];
                        if ($bOk !== false)
                            $bOk = $db->insert($sql, '', true);
                    }
                }

                if ($bOk === false) {
                    $db->rollback();
                    $ses->setMessage("Se produjo un error creando la comisión", SessionMessageType::TransactionError);
                    header("Location: /gestion-comisiones/" . $dataIdCarrera . "/" . $dataIdMateria, TRUE, 302);
                    exit();
                } else {
                    $db->commit();
                }
                $db->dbDisconnect();

                $ses->setMessage("Comisión creada con éxito", SessionMessageType::Success, $dataIdCarrera);
                header("Location: /gestion-comisiones/" . $dataIdCarrera . "/" . $dataIdMateria, TRUE, 302);
                exit();
            } //validationErrors=0
        }

        //cargo el menu del  usuario (por rol)
        $arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());

        //cargo arrays para la vista

        $sql = "select nombre from materias where idmateria=" . $dataIdMateria;
        $arr = $db->getSQLArray($sql);
        $dataDescripcionMateria = $arr[0]['nombre'];

        $sql = "select idarea,nombre from areas order by 2";
        $viewDataAreas = $db->getSQLArray($sql);

        $sql = "select idinstrumento,nombre from instrumentos order by 2";
        $viewDataInstrumentos = $db->getSQLArray($sql);

        $sql = "select p.idpersona, p.apellido, p.nombre from profesores pr inner join personas p on p.idpersona=pr.idpersona where p.estado=1 order by 2,3";
        $viewDataProfesores = $db->getSQLArray($sql);

        $sql = "select id,descripcion from situacionrevista order by 2";
        $viewDataSitRevista = $db->getSQLArray($sql);

        $db->dbDisconnect();
        $viewDataTurnos = $this->POROTO->Config['dominios']['turnos'];
        $viewDataDias = $this->POROTO->Config['dominios']['dias'];

        //como es un crear, pongo los valoreas default en 0 (porque se comparte la pagina con el modificar)
        $viewData = array(array("codigo" => "",
                "anio" => date("Y"),
                "aula" => "",
                "idarea" => 0,
                "idinstrumento" => 0,
                "nombre" => "",
                "cupof" => "",
                "turno" => "",
                "matriculacion" => 1,
        ));

        $pageTitle = "Crear Comisión";
        $isCloned = "";
        include($this->POROTO->ViewPath . "/-header.php");
        include($this->POROTO->ViewPath . "/ver-comision.php");
        include($this->POROTO->ViewPath . "/-footer.php");
    }

    public function cancelarclonar($idComision, $idCarrera, $idMateria) { //OK
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;

        //Cambio 38 Leo 20170706
        if (!$ses->tienePermiso('', 'Gestion de Comisiones Edicion (ABM)')) {
            $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /gestion-comisiones/", TRUE, 302);
            exit();
        }
        //Fin Cambio 38 Leo 20170706

        $db->dbConnect("comisiones/cancelarclonar/" . $idComision . "/" . $idCarrera . "/" . $idMateria);
        $dataIdCarrera = $db->dbEscape($idCarrera);
        $dataIdMateria = $db->dbEscape($idMateria);
        $dataIdComision = $db->dbEscape($idComision);

        $sql = "DELETE FROM comprofesor WHERE idcomision=" . $dataIdComision;
        $db->delete($sql);

        $sql = "DELETE FROM comhorario WHERE idcomision=" . $dataIdComision;
        $db->delete($sql);

        $sql = "DELETE FROM comcupos WHERE idcomision=" . $dataIdComision;
        $db->delete($sql);

        $sql = "DELETE FROM comisiones WHERE idcomision=" . $dataIdComision;
        $db->delete($sql);

        $db->dbDisconnect();

        header("Location: /gestion-comisiones/" . $dataIdCarrera . "/" . $dataIdMateria, TRUE, 302);
    }

    public function clonar($idComision, $idCarrera, $idMateria) {
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;

        //Cambio 38 Leo 20170706
        if (!$ses->tienePermiso('', 'Gestion de Comisiones Edicion (ABM)')) {
            $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /gestion-comisiones/", TRUE, 302);
            exit();
        }
        //Fin Cambio 38 Leo 20170706

        $db->dbConnect("comisiones/clonar/" . $idComision . "/" . $idCarrera . "/" . $idMateria);
        $dataIdCarrera = $db->dbEscape($idCarrera);
        $dataIdMateria = $db->dbEscape($idMateria);
        $dataIdComision = $db->dbEscape($idComision);

        $sql = "SELECT m.nombre mat, ifnull(a.nombre,'') area, aula, i.nombre inst";
        $sql .= " FROM comisiones c";
        $sql .= " INNER JOIN materias m on m.idmateria=c.idmateria";
        $sql .= " LEFT JOIN instrumentos i on i.idinstrumento=c.idinstrumento";
        $sql .= " LEFT JOIN areas a on a.idarea=c.idarea";
        $sql .= " WHERE idcomision=" . $dataIdComision;
        $viewData = $db->getSQLArray($sql);

        //duplico los registros por sql
        $sql = "INSERT INTO comisiones (idmateria, idarea, idinstrumento, nombre, codigo, anio, aula, turno, estado, matriculacionhabilitada, cupof, usucrea, fechacrea, usumodi, fechamodi) SELECT ";
        $sql .= " idmateria, idarea, idinstrumento";
        $sql .= " , concat(codigo,\"-\",anio+1,\"-\",\"" . $viewData[0]['mat'] . "\"";
        if ($viewData[0]['area'] != "")
            $sql .= ",\"-" . $viewData[0]['area'] . "\"";
        if ($viewData[0]['inst'] != "")
            $sql .= ",\"-" . $viewData[0]['inst'] . "\"";
        $sql .= ")";
        $sql .= " , codigo, anio+1 anio,aula,turno, estado, matriculacionhabilitada, cupof";
        $sql .= ",'" . $ses->getUsuario() . "'";
        $sql .= ",CURRENT_TIMESTAMP";
        $sql .= ",null";
        $sql .= ",null";
        $sql .= " FROM comisiones";
        $sql .= " WHERE idcomision=" . $dataIdComision;

        $db->begintrans();
        $newIdComision = $db->insert($sql, '', true);
        $bOk = $newIdComision;

        $sql = "INSERT INTO comprofesor (idpersona, idcomision, situacionrevista_id, usucrea, fechacrea, usumodi, fechamodi) SELECT ";
        $sql .= " idpersona";
        $sql .= " ," . $newIdComision;
        $sql .= " ,situacionrevista_id";
        $sql .= ",'" . $ses->getUsuario() . "'";
        $sql .= ",CURRENT_TIMESTAMP";
        $sql .= ",null";
        $sql .= ",null";
        $sql .= " FROM comprofesor";
        $sql .= " WHERE idcomision=" . $dataIdComision;
        if ($bOk !== false)
            $bOk = $db->insert($sql, '', true);

        $sql = "INSERT INTO comhorario (idcomision, dia, inicio, fin) SELECT ";
        $sql .= $newIdComision;
        $sql .= ", dia, inicio, fin";
        $sql .= " FROM comhorario";
        $sql .= " WHERE idcomision=" . $dataIdComision;
        if ($bOk !== false)
            $bOk = $db->insert($sql, '', true);

        $sql = "INSERT INTO comcupos (idcomision, descripcion, cantidad, cantdisponible) SELECT ";
        $sql .= $newIdComision;
        $sql .= ", descripcion, cantidad, cantidad";
        $sql .= " FROM comcupos";
        $sql .= " WHERE idcomision=" . $dataIdComision;
        if ($bOk !== false)
            $bOk = $db->insert($sql, '', true);

        if ($bOk === false) {
            $db->rollback();
            $ses->setMessage("Se produjo un error clonando la comisión", SessionMessageType::TransactionError);
            header("Location: /gestion-comisiones/" . $dataIdCarrera . "/" . $dataIdMateria, TRUE, 302);
            exit();
        } else {
            $db->commit();
        }
        $db->dbDisconnect();


        //redirect a modificar
        header("Location: /comisiones/modificar/" . $newIdComision . "/" . $dataIdCarrera . "/clon", TRUE, 302);
    }

    public function modificar($idComision, $idCarrera, $isCloned = "") { //OK
        $materiaModel = new MateriaModel($this->POROTO);
        $validationErrors = array();
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;

        //Cambio 38 Leo 20170706
        if (!$ses->tienePermiso('', 'Gestion de Comisiones Edicion (ABM)') && !$ses->tienePermiso('', 'Gestion de Comisiones  Ver')) {
            $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /gestion-comisiones/", TRUE, 302);
            exit();
        }
        //Fin Cambio 38 Leo 20170706

        $db->dbConnect("comisiones/modificar/" . $idComision . "/" . $idCarrera);
        $dataIdCarrera = $db->dbEscape($idCarrera);
        $dataIdComision = $db->dbEscape($idComision);

        //cargo los datos de la comision, profesores, horarios y cupos
        $sql = "SELECT idmateria, idarea, idinstrumento, nombre, codigo, anio, aula, turno, cupof, matriculacionhabilitada as matriculacion";
        $sql .= " FROM comisiones";
        $sql .= " WHERE idcomision=" . $dataIdComision;
        $viewData = $db->getSQLArray($sql);
        $dataIdMateria = $viewData[0]['idmateria'];

        $sql = "SELECT p.idpersona, concat(p.apellido, \",\", p.nombre) apellidonombre, cp.situacionrevista_id, s.descripcion situacionrevista_descripcion";
        $sql .= " FROM comprofesor cp";
        $sql .= " INNER JOIN personas p on p.idpersona=cp.idpersona";
        $sql .= " INNER JOIN situacionrevista s on s.id=cp.situacionrevista_id";
        $sql .= " WHERE idcomision=" . $dataIdComision;
        $newProfesores = $db->getSQLArray($sql);

        $sql = "SELECT dia, date_format(inicio, \"%H%i\") desde4, date_format(fin, \"%H%i\") hasta4, date_format(inicio, \"%H:%i\") desde, date_format(fin, \"%H:%i\") hasta";
        $sql .= " FROM comhorario";
        $sql .= " WHERE idcomision=" . $dataIdComision;
        $newHorarios = $db->getSQLArray($sql);

        $sql = " SELECT descripcion cuponombre, cantidad disponibilidad, cantdisponible";
        $sql .= " FROM comcupos";
        $sql .= " WHERE idcomision=" . $dataIdComision;
        $newCupos = $db->getSQLArray($sql);

        if (isset($_POST['codigo'])) {  //Entro modificando.
            $newProfesores = array();
            $newHorarios = array();
            $newCupos = array();
            $dataCodigo = mb_strtoupper($db->dbEscape(trim($_POST['codigo'])), 'UTF-8');
            $dataAnio = $db->dbEscape(intval(trim($_POST['anio'])));
            $dataAula = mb_strtoupper($db->dbEscape(trim($_POST['aula'])), 'UTF-8');
            $dataArea = $db->dbEscape(trim($_POST['area']));
            $dataInstrumento = $db->dbEscape(trim($_POST['instrumento']));
            $dataNombre = mb_strtoupper($db->dbEscape(trim($_POST['nombre'])), 'UTF-8');
            $dataCupof = mb_strtoupper($db->dbEscape(trim($_POST['cupof'])), 'UTF-8');
            if (isset($_POST['matriculacion']))
                $dataMatriculacion = "1";
            else
                $dataMatriculacion = "0";
            $dataTurno = $db->dbEscape(trim($_POST['turno']));



            // VALIDAR
            // codigo - obligatorio | maxlength 10
            if ($dataCodigo == "")
                $validationErrors['codigo'] = "El campo Nombre Comisión es obligatorio";
            if (strlen($dataCodigo) > 10)
                $validationErrors['codigo'] = "El campo Nombre Comisión puede contener como máximo 10 caracteres";
            // anio - obligatorio | valor entre 1900 y 2100
            if ($dataAnio == "" || $dataAnio == 0)
                $validationErrors['anio'] = "El campo Año es obligatorio";
            if ($dataAnio < 1900 || $dataAnio > 2100)
                $validationErrors['codigo'] = "El campo Año contiene un valor inválido";
            // area 
            // if ($dataArea=="0") $validationErrors['area'] = "El campo Área es obligatorio";
            // instrumento - 
            // if ($dataInstrumento=="0") $validationErrors['instrumento'] = "El campo Instrumento es obligatorio";
            // nombre - obligatorio | maxlength 250
            if ($dataNombre == "")
                $validationErrors['nombre'] = "El campo Nombre es obligatorio";
            if (strlen($dataNombre) > 250)
                $validationErrors['nombre'] = "El campo Nombre puede contener como máximo 250 caracteres";
            // cupof - maxlength 45
            // if ($dataCupof=="") $validationErrors['cupof'] = "El campo Cupof es obligatorio";
            if (strlen($dataCupof) > 45)
                $validationErrors['cupof'] = "El campo Cupof puede contener como máximo 45 caracteres";
            // turno - obligatorio
            if ($dataTurno == "0")
                $validationErrors['turno'] = "El campo Turno es obligatorio";
            
            $materia = $materiaModel->getMateriaById($dataIdMateria);
            if ( !$materia["materia_instrumento"] && $dataInstrumento){
                $validationErrors['instrumento'] = "La materia no acepta instrumentos.";
            }             
            if ( $materia["materia_instrumento"] && !$dataInstrumento){
                $validationErrors['instrumento'] = "La materia requiere elegir un instrumento.";
            } 
            if ( !$materia["materia_genero"] && $dataArea){
                $validationErrors['area'] = "La materia no acepta generos.";
            }             
            if ( $materia["materia_genero"] && !$dataArea){
                $validationErrors['area'] = "La materia requiere elegir un genero.";
            } 
            if (isset($_POST['profesores'])) {
                //if (count($_POST['profesores'])==0) $validationErrors['profesores'] = "Debe tener cargado al menos un profesor";
                //por cada profesor, valido y recreo los valores en $newProfesores para el repost
                foreach ($_POST['profesores'] as $prof) { //4028~**~FLAMINMAN,DÉBORAH AILIN~**~1~**~TITULAR
                    $arr = explode("~**~", $prof);
                    if (count($arr) == 4) {
                        $newProfesores[] = array("idpersona" => $arr[0],
                            "apellidonombre" => $arr[1],
                            "situacionrevista_id" => $arr[2],
                            "situacionrevista_descripcion" => $arr[3]);
                    } else {
                        $validationErrors['profesores'] = "Se detectaron valores inválidos en el listado de profesores";
                    }
                }
            } /* else {
              $validationErrors['profesores'] = "Debe tener cargado al menos un profesor";
              } */

            if (isset($_POST['horarios'])) {
                //if (count($_POST['horarios'])==0) $validationErrors['horarios'] = "Debe tener cargado al menos un horario";
                //por cada horario, valido y recreo los valores en $newHorarios para el repost
                foreach ($_POST['horarios'] as $hor) { //LUNES09001800
                    $horaH = substr($hor, -4);
                    $horaD = substr($hor, -8, 4);
                    $dia = substr($hor, 0, -8);

                    if (strlen($dia) > 0) {
                        $newHorarios[] = array("dia" => $dia,
                            "desde4" => $horaD,
                            "hasta4" => $horaH,
                            "desde" => substr($horaD, 0, 2) . ":" . substr($horaD, 2, 2),
                            "hasta" => substr($horaH, 0, 2) . ":" . substr($horaH, 2, 2));
                    } else {
                        $validationErrors['horarios'] = "Se detectaron valores inválidos en el listado de horarios";
                    }
                }
            } /* else {
              $validationErrors['horarios'] = "Debe tener cargado al menos un horario";
              } */

            if (isset($_POST['cupos'])) {
                if (count($_POST['cupos']) == 0)
                    $validationErrors['cupos'] = "Debe tener cargado al menos un cupo";

                //por cada cupo, valido y recreo los valores en $newCupos para el repost
                foreach ($_POST['cupos'] as $cupo) { //NOMBREDELCUP~**~25~**~25
                    $arr = explode("~**~", $cupo);
                    if (count($arr) == 3) {
                        $newCupos[] = array("cuponombre" => $arr[0],
                            "disponibilidad" => $arr[1],
                            "cantdisponible" => $arr[2]);
                    } else {
                        $validationErrors['cupos'] = "Se detectaron valores inválidos en el listado de cupos";
                    }
                }
            } else {
                $validationErrors['cupos'] = "Debe tener cargado al menos un cupo";
            }


            if (count($validationErrors) == 0) {
                $sql = "UPDATE comisiones SET ";
                $sql .= "idarea=" . $dataArea;
                $sql .= ",idinstrumento=" . $dataInstrumento;
                $sql .= ",nombre='" . $dataNombre . "'";
                $sql .= ",codigo='" . $dataCodigo . "'";
                $sql .= ",anio=" . $dataAnio;
                if($dataAula=="") {
                    $sql.=",aula=null";
                }
                else
                {
                    $sql .= ",aula='" . $dataAula . "'";
                }
                $sql .= ",turno='" . $dataTurno . "'";
                $sql .= ",cupof='" . $dataCupof . "'";
                $sql .= ",matriculacionhabilitada=" . $dataMatriculacion;
                $sql .= ",usumodi='" . $ses->getUsuario() . "'";
                $sql .= ",fechamodi=CURRENT_TIMESTAMP";
                $sql .= " WHERE idcomision=" . $dataIdComision;

                $db->begintrans();
                $bOk = $db->update($sql, '', true);

                $sql = "DELETE FROM comprofesor WHERE idcomision=" . $dataIdComision;
                if ($bOk !== false)
                    $bOk = $db->delete($sql, '', true);
                $sql = "DELETE FROM comhorario WHERE idcomision=" . $dataIdComision;
                if ($bOk !== false)
                    $bOk = $db->delete($sql, '', true);
                $sql = "DELETE FROM comcupos WHERE idcomision=" . $dataIdComision . " AND cantidad=cantdisponible";
                if ($bOk !== false)
                    $bOk = $db->delete($sql, '', true);

                if (isset($newProfesores)) {
                    foreach ($newProfesores as $profesor) {
                        $sql = "INSERT INTO comprofesor (idpersona, idcomision, situacionrevista_id, usucrea, fechacrea, usumodi, fechamodi) SELECT ";
                        $sql .= $profesor['idpersona'];
                        $sql .= "," . $dataIdComision;
                        $sql .= "," . $profesor['situacionrevista_id'];
                        $sql .= ",'" . $ses->getUsuario() . "'";
                        $sql .= ",CURRENT_TIMESTAMP";
                        $sql .= ",null";
                        $sql .= ",null";
                        if ($bOk !== false)
                            $bOk = $db->insert($sql, '', true);
                    }
                }

                if (isset($newHorarios)) {
                    foreach ($newHorarios as $horario) {
                        $sql = "INSERT INTO comhorario (idcomision, dia, inicio, fin) SELECT ";
                        $sql .= $dataIdComision;
                        $sql .= ",'" . $horario['dia'] . "'";
                        $sql .= ",'" . $this->POROTO->Config['dia_default_horario'] . " " . $horario['desde'] . "'";
                        $sql .= ",'" . $this->POROTO->Config['dia_default_horario'] . " " . $horario['hasta'] . "'";
                        if ($bOk !== false)
                            $bOk = $db->insert($sql, '', true);
                    }
                }

                if (isset($newCupos)) {
                    foreach ($newCupos as $cupo) {
                        if ($cupo['disponibilidad'] == $cupo['cantdisponible']) {
                            $sql = "INSERT INTO comcupos (idcomision, descripcion, cantidad, cantdisponible) SELECT ";
                            $sql .= $dataIdComision;
                            $sql .= ",'" . $cupo['cuponombre'] . "'";
                            $sql .= "," . $cupo['disponibilidad'];
                            $sql .= "," . $cupo['cantdisponible'];
                            if ($bOk !== false)
                                $bOk = $db->insert($sql, '', true);
                        }
                    }
                }

                if ($bOk === false) {
                    $db->rollback();
                    $ses->setMessage("Se produjo un error modificando la comisión", SessionMessageType::TransactionError);
                    header("Location: /gestion-comisiones/" . $dataIdCarrera . "/" . $dataIdMateria, TRUE, 302);
                    exit();
                } else {
                    $db->commit();
                }
                $db->dbDisconnect();

                $ses->setMessage("Comisión modificada con éxito", SessionMessageType::Success, $dataIdCarrera);
                header("Location: /gestion-comisiones/" . $dataIdCarrera . "/" . $dataIdMateria, TRUE, 302);
                exit();
            } //validationErrors=0
        }

        //cargo el menu del  usuario (por rol)
        $arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());

        //cargo arrays para la vista
        $sql = "select nombre from materias where idmateria=" . $dataIdMateria;
        $arr = $db->getSQLArray($sql);
        $dataDescripcionMateria = $arr[0]['nombre'];

        $sql = "select idarea,nombre from areas order by 2";
        $viewDataAreas = $db->getSQLArray($sql);

        $sql = "select idinstrumento,nombre from instrumentos order by 2";
        $viewDataInstrumentos = $db->getSQLArray($sql);

        $sql = "select p.idpersona, p.apellido, p.nombre from profesores pr inner join personas p on p.idpersona=pr.idpersona where p.estado=1 order by 2,3";
        $viewDataProfesores = $db->getSQLArray($sql);

        $sql = "select id,descripcion from situacionrevista order by 2";
        $viewDataSitRevista = $db->getSQLArray($sql);

        $db->dbDisconnect();
        $viewDataTurnos = $this->POROTO->Config['dominios']['turnos'];
        $viewDataDias = $this->POROTO->Config['dominios']['dias'];


        $pageTitle = "Modificar Comisión";
        include($this->POROTO->ViewPath . "/-header.php");
        include($this->POROTO->ViewPath . "/ver-comision.php");
        include($this->POROTO->ViewPath . "/-footer.php");
    }

// cambio de comision
    public function cambiocomision() {

        // TODO: Verificar permisos de usuario para este modulo

        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $lib = & $this->POROTO->Libraries['siteLibrary'];

        $db->dbConnect("comisiones/cambiocomision");
        $arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());

        $sql = "SELECT idcarrera,descripcion";
        $sql .= " FROM carreras";
        $sql .= "  WHERE estado=1";
        $sql .= "  ORDER BY 2";
        $viewDataCarreras = $db->getSQLArray($sql);

        $db->dbDisconnect();

        $pageTitle = "Gestión de Comisiones";
        include($this->POROTO->ViewPath . "/-header.php");
        include($this->POROTO->ViewPath . "/cambio-comision.php");
        include($this->POROTO->ViewPath . "/-footer.php");
    }

    public function ajaxacomisioncompleta($idComision) { //Trae los alumnos de la comision seleccionada
        $db = & $this->POROTO->DB;
        $db->dbConnect("comisiones/ajaxcomisioncompleta/" . $idComision);
        $sql = "SELECT p.apellido, p.nombre, td.descripcion as tipodocumento, p.documentonro, 
        p.legajo, eam.descripcion as estdomateria, am.idAlumnoMateria, am.idalumnocarrera, p.idpersona, 
		cc.descripcion as nombrecupo, cc.idcomcupo, am.idcomision
        FROM personas p
        INNER JOIN alumnomateria am on p.idpersona = am.idpersona
        INNER JOIN estadoalumnomateria eam on am.idestadoalumnomateria=eam.idestadoalumnomateria
        INNER JOIN tipodoc td on p.tipodoc = td.id
        INNER JOIN comalumno ca on am.idcomision = ca.idcomision and am.idpersona = ca.idpersona
        INNER JOIN comcupos cc on cc.idcomcupo = ca.idcomcupo
        WHERE am.idcomision=$idComision and ca.estado=1 and eam.idestadoalumnomateria=" . $this->POROTO->Config['estado_alumnomateria_cursando'] . " ORDER BY  p.apellido, p.nombre";

        $result['alumnos'] = $db->getSQLArray($sql);
        $sql = "SELECT *
        FROM comcupos 
        WHERE idcomision=$idComision";

        $result['cupos'] = $db->getSQLArray($sql);

        $db->dbDisconnect();

        echo json_encode($result);
    }

    public function ajaxcomisiones($idmateria, $anio) { //Trae las comisiones de la materia seleccionada.
        $db = & $this->POROTO->DB;
        $db->dbConnect("comisiones/ajaxcomisiones/" . $idmateria);

        $sql = "select idcomision as id, nombre";
        $sql .= " from comisiones c";
        $sql .= " where c.idmateria=" . $db->dbEscape($idmateria);
        $sql .= " and c.estado=1";
        $sql .= " and c.anio=" . $db->dbEscape($anio);
        $sql .= " order by nombre asc";
        $result = $db->getSQLArray($sql);
        $db->dbDisconnect();

        echo json_encode($result);
    }

    public function cambiarcomision($idpersona, $com_origen, $cupo_origen, $com_destino, $cupo_destino) {
        try {
            $ses = & $this->POROTO->Session;
            $pdo = & $this->POROTO->PDO->getPdo();

            $sql = "SELECT apellido, nombre FROM personas WHERE idpersona = :idpersona";
            $query = $pdo->prepare($sql);
            $params = array(':idpersona' => $idpersona);
            $query->execute($params);

            $persona = $query->fetchAll(PDO::FETCH_ASSOC);

            $pdo->beginTransaction();

            // Busco si el alumno no tiene una relacion existente con la comision / cupo destino
            $sql = "SELECT *  
                FROM `comalumno` 
                WHERE `idcomision` = :com_destino AND `idpersona` = :idpersona AND `idcomcupo` = :cupo_destino";
            $query = $pdo->prepare($sql);
            $params = array(':idpersona' => $idpersona, ':com_destino' => $com_destino, ':cupo_destino' => $cupo_destino);
            $query->execute($params);

            $relacion_existente = $query->fetchAll(PDO::FETCH_ASSOC);

            // Incrementamos en 1a cantidad de plazas disponibles del cupo origen
            $sql = "UPDATE comcupos 
                SET cantdisponible = cantdisponible + 1 
                WHERE idcomcupo = :cupo_origen";
            $query = $pdo->prepare($sql);
            $params = array(':cupo_origen' => $cupo_origen);
            $query->execute($params);

            // Deshabilito la relacion entre alumno y comision
            $sql = "UPDATE `comalumno` 
                SET  `estado`=:estado, `usumodi`= :usumodi, `fechamodi`=CURRENT_TIMESTAMP  
                WHERE `idcomision` = :com_origen AND `idpersona` = :idpersona AND `idcomcupo` = :cupo_origen";
            $query = $pdo->prepare($sql);
            $params = array(':estado' => 0,
                ':idpersona' => $idpersona,
                ':com_origen' => $com_origen,
                ':cupo_origen' => $cupo_origen,
                ':usumodi' => $ses->getUsuario());
            $query->execute($params);

            if (empty($relacion_existente)) {
                //creo nueva relacion
                $sql = "INSERT INTO `comalumno` (idcomision, idpersona, idcomcupo, estado, usucrea, fechacrea) 
                     VALUES (:com_destino, :idpersona, :cupo_destino, :estado, :usuario , CURRENT_TIMESTAMP )";
            } else {
                // pongo "estado" = 1 en la relacion existente
                $sql = "UPDATE `comalumno` 
                SET  `estado`=:estado, `usumodi`= :usuario, `fechamodi`=CURRENT_TIMESTAMP  
                WHERE `idcomision` = :com_destino AND `idpersona` = :idpersona AND `idcomcupo` = :cupo_destino; ";
            }
            $query = $pdo->prepare($sql);
            $params = array(':estado' => 1,
                ':idpersona' => $idpersona,
                ':com_destino' => $com_destino,
                ':cupo_destino' => $cupo_destino,
                ':usuario' => $ses->getUsuario());
            $query->execute($params);

            // Decremento en 1 la cantidad de plazas disponibles del cupo destino
            $sql = "UPDATE comcupos 
                SET cantdisponible = cantdisponible-1 
                WHERE idcomcupo = :cupo_destino; ";
            $query = $pdo->prepare($sql);
            $params = array(':cupo_destino' => $cupo_destino);
            $query->execute($params);
            // Actualizo alumnomateria

            $sql = "UPDATE alumnomateria 
                SET idcomision = :com_destino , `usumodi`= :usuario, `fechamodi`=CURRENT_TIMESTAMP  
                WHERE `idpersona` = :idpersona AND `idcomision` = :com_origen AND `idestadoalumnomateria` = :estadoalumnomateria";
            $query = $pdo->prepare($sql);
            $params = array(':idpersona' => $idpersona, ':com_origen' => $com_origen, ':com_destino' => $com_destino, ':estadoalumnomateria' => $this->POROTO->Config['estado_alumnomateria_cursando'], ':usuario' => $ses->getUsuario());
            $query->execute($params);

            $pdo->commit();


            $ses->setMessage("El alumno " . $persona[0]['apellido'] . ", " . $persona[0]['nombre'] . " se cambio de comisión.", SessionMessageType::Success, "");

            echo json_encode("mock pasando persona: $idpersona, comision origin: $com_origen, cupo origen: $cupo_origen ==> comision destino: $com_destino, cupo destino: $cupo_destino");
        } catch (Exception $e) {
            $pdo->rollback();
            $ses->setMessage("No se ah podido realizar el cambio de comisión. Error: " . $e->getMessage(), SessionMessageType::TransactionError, "");
        }
    }

}
