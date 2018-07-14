<?php

class Matriculacion {

    private $POROTO;

    function __construct($poroto) {
        $this->POROTO = $poroto;
        $this->POROTO->pageHeader[] = array("label" => "Dashboard", "url" => "");
    }

    function defentry() {
        if ($this->POROTO->Session->isLogged()) {
            header("Location: /", TRUE, 302);
        } else {
            $this->matricular();
        }
    }

//recibe por post:
//	carrera -> id de la carrera seleccionada
//	alumno  -> id de la persona
//	modo    -> 1-correlativas (Alumno)
//             2-todas las materias (Administrativo)
//             3-inscriptas (traigo las materias inscriptas)
//utiliza el elemento de configuracion 'matriculacion_incluir_cursada'

    public function ajaxmaterias() { //Se le agrego idalumnocarrera
        $idCarrera = $_POST['carrera'];
        $idAlumno = $_POST['alumno'];
        $idAlumnoCarrera = $_POST['idalumnocarrera'];
        $dataIdModo = htmlentities($_POST['modo'], ENT_COMPAT | ENT_QUOTES, "UTF-8");
        $db = & $this->POROTO->DB;
        $db->dbConnect("matriculacion/ajaxmaterias/" . $idCarrera . "/" . $idAlumno . "/" . $dataIdModo);

        include($this->POROTO->ControllerPath . '/correlativas.php');
        $claseCorrelativas = new correlativas($this->POROTO);

        if ($dataIdModo == 3) { //solo inscriptas (estadomateria=2)
            $sql = "select m.idmateria, m.anio, m.materiacompleta as nombre,c.nombre as comision ";
            $sql .= " from alumnomateria am inner join viewmaterias m on (am.idmateria=m.idmateria and m.idcarrera=" . $idCarrera . ")";
            $sql .= " left join comisiones c on am.idcomision=c.idcomision ";
            $sql .= " where am.idestadoalumnomateria in (" . $this->POROTO->Config['estado_alumnomateria_cursando'] . ",";
            $sql .= $this->POROTO->Config['estado_alumnomateria_libre'] . ") ";
            $sql .= " and am.idpersona=" . $idAlumno;
            $sql .= " and am.idalumnocarrera=" . $idAlumnoCarrera;
            $sql .= " order by m.orden";
            $arrMateriasOk = $db->getSQLArray($sql);
        }
        if ($dataIdModo == 1 || $dataIdModo == 2) {  //1 Alumno 2 Administrativo.										
            //levanto el listado de materias de la carrera. por un left join me 
            //fijo si ya fue cursada (puede haber mas de un registro)
            //Si materia cursada aprobada o libre => solo_cursada>=1 y 
            //la considero como ya cursada
            //si tuvo un estado 3,4 (aprobada,aprobada por equiv) => aprobada>1 y la considero como materia aprobada
            //elimino las materias que tienen estado CURSANDO (inscriptas) y las libres al menos una vez.
            $sql = "select m.idmateria,m.anio,m.materiacompleta as nombre,";
            $sql .= " sum(case when am.idestadoalumnomateria in (" . $this->POROTO->Config['estado_alumnomateria_cursadaaprobada'] . ",";
            $sql .= $this->POROTO->Config['estado_alumnomateria_libre'] . ") then 1 else 0 end) solo_cursada,";
            $sql .= " sum(case when am.idestadoalumnomateria in (" . $this->POROTO->Config['estado_alumnomateria_aprobada'] . ",";
            $sql .= $this->POROTO->Config['estado_alumnomateria_aprobadaxequiv'] . "," . $this->POROTO->Config['estado_alumnomateria_cursadaaprobada'] . ",";
            $sql .= $this->POROTO->Config['estado_alumnomateria_nivelacion'] . ") then 1 else 0 end) cursada,";
            $sql .= " sum(case when am.idestadoalumnomateria in (" . $this->POROTO->Config['estado_alumnomateria_aprobada'] . ",";
            $sql .= $this->POROTO->Config['estado_alumnomateria_aprobadaxequiv'] . "," . $this->POROTO->Config['estado_alumnomateria_nivelacion'];
            $sql .= ") then 1 else 0 end) aprobada,";
            $sql .= " sum(case when am.idestadoalumnomateria in (" . $this->POROTO->Config['estado_alumnomateria_cursando'] . ") then 1 else 0 end) inscripta ";
            $sql .= " from viewmaterias m ";
            $sql .= " left join alumnomateria am on am.idmateria=m.idmateria and am.idpersona=" . $db->dbEscape($idAlumno);
            $sql .= " and am.idalumnocarrera=" . $db->dbEscape($idAlumnoCarrera);
            $sql .= " where m.idcarrera=" . $db->dbEscape($idCarrera);
            $sql .= " and m.estado=1";
            $sql .= " group by m.idmateria";
            $sql .= " order by m.orden";
            $arrMaterias = $db->getSQLArray($sql);

            $arrMateriasOk = array();

            foreach ($arrMaterias as $row) {
                if ($row['solo_cursada'] == "0") { //Solo traigo las que no estoy cursando aun o libre.
                    //Busco las correlativas para este alumno.
                    $resultados = $claseCorrelativas->getCorrelativasAlumno($idCarrera, $row['idmateria'], 0, $idAlumno, $idAlumnoCarrera);
                    if ($dataIdModo == 1) { //Si es alumno, solo traigo materias sin correlativas
                        $valida = true;
                        foreach ($resultados as $regla) {
                            if ($regla["idregla"] != 6 && $regla["estado"] == false)
                                $valida = false;
                        }
                        if ($valida)
                            $arrMateriasOk[] = array("idmateria" => $row['idmateria'], "nombre" => $row['nombre'], "anio" => $row['anio'], "correlativas" => $resultados);
                    }else { //Traigo todas, cumplan o no las correlatividades.
                        $arrMateriasOk[] = array("idmateria" => $row['idmateria'], "nombre" => $row['nombre'], "anio" => $row['anio'], "correlativas" => $resultados);
                    }
                }
            }

            //busco en arrMateriasOk, las materia que tienen inscriptas>0
            foreach ($arrMateriasOk as $k => $item)
                foreach ($arrMaterias as $materia)
                    if ($materia['idmateria'] == $item['idmateria'])
                        if ($materia['inscripta'] > 0 || $materia['aprobada'] > 0)
                            unset($arrMateriasOk[$k]);
        } // if ($dataIdModo == 1 || $dataIdModo == 2   {
        $db->dbDisconnect();
        echo json_encode($arrMateriasOk);
    }

    //OBtiene las comisiones disponibles para matricularse.
    //recibe por post:
    //	materia  -> id de la materia
    //	carrera  -> id de la carrera selecionada
    //	alumno   -> idpersona del alumno

    
    public function ajaxcomisiones() { //OK
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $lib = & $this->POROTO->Libraries['siteLibrary'];

        $idMateria = $_POST['materia'];
        $idCarrera = $_POST['carrera'];
        $idAlumnoCarrera = $_POST['idalumnocarrera'];
        $idAlumno = $_POST['alumno'];
        $permiteLibre = 0;

        $db->dbConnect("matriculacion/ajaxcomisiones/" . $idMateria . "/" . $idCarrera . "/" . $idAlumno . "/" . $idAlumnoCarrera);

        //traer de alumnocarrera, si la inscripcion tiene instrumento/area y CURSANDO.
        $sql = "select idarea,idinstrumento from alumnocarrera where idcarrera=";
        $sql .= $idCarrera . " and idpersona=" . $idAlumno . " and fechafinalizada is null and estado in (1,3) ";
        $sql .= "and idalumnocarrera=" . $idAlumnoCarrera;
        //Aca se podria sacar el estado ya que siempre voy a traer la q corresponda...

        $arrAlumno = $db->getSQLArray($sql);

        //Verifico si la materia permite anotarse libre.
        $sql = "select idmateria,libre from viewmaterias m where idmateria=" . $db->dbEscape($idMateria);
        $arrMatLibre = $db->getSQLArray($sql);
        $permiteLibre = $arrMatLibre[0]['libre'];

        //Trae las comisiones disponibles para anotarse, siempre y cuando esten activas y en coincidencia con iinstr.
        $sql = "SELECT c.idcomision, c.codigo, c.nombre, c.turno, c.aula ";
        $sql .= " FROM comisiones c ";
        $sql .= " WHERE c.idmateria=" . $db->dbEscape($idMateria);
        $sql .= " AND c.estado=1 ";
        //Cambio 20180206 para traer solo las comisiones del año actual
        $sql .= " AND c.anio=" . date("Y");
        //Fin cambio 20180206
        //Solo el alumno puede matricularse en las habilitadas.
        //20180315 A pedido de Pablo se asigno este permiso tambien a admin y directivos para que desde el modulo de matriculacion
        //solamente se pueda matricular a comisiones con matriculacionhabilitada=1
        if ($ses->tienePermiso('', 'Matricularme Permitir solo en Comisiones Habilitadas')) {
            $sql .= " and matriculacionhabilitada=1 ";
        }
        $sql .= " and (idarea=0 or idarea=" . $arrAlumno[0]['idarea'] . ")";
        $sql .= " and (idinstrumento=0 or idinstrumento=" . $arrAlumno[0]['idinstrumento'] . ")";

        //Para el caso de ALUMNOS, deben tener el permiso siguiente, pero a su vez la materia debe permitir LIBRE
        //Solo asi puede matricularse libre.
        if ($ses->tienePermiso('', 'Matricularme Permitir en materias LIBRE (Alumno)') && $permiteLibre) {
            //Agrego una linea mas para anotarlo sin comision
            $sql .= " UNION ";
            $sql .= " SELECT 0 as idcomision,'----' AS codigo,'LIBRE (SIN COMISIÓN)' as nombre,'' as turno, '' as aula ";
        }

        //Para el caso de Administrativos o Directivos, tienen el siguiente permiso, 
        //y pueden matricular libre en una materia sin importar si la materia permite libre o no.
        if ($ses->tienePermiso('', 'Matricularme Permitir en materias LIBRE (Admin)')) {
            //Agrego una linea mas para anotarlo sin comision
            $sql .= " UNION ";
            $sql .= " SELECT 0 as idcomision,'----' AS codigo,'LIBRE (SIN COMISIÓN)' as nombre,'' as turno, '' as aula ";
        }

        $arrComisiones = $db->getSQLArray($sql);

        foreach ($arrComisiones as &$comision) {
            if ($comision['idcomision'] > 0) { //Solo traigo datos si es una comision real, sino es LIBRE
                //Traigo los horarios
                $sql = "SELECT dia, date_format(inicio,'%H:%i') inicio, date_format(fin,'%H:%i') fin";
                $sql .= " FROM comhorario";
                $sql .= " WHERE idcomision=" . $comision['idcomision'];
                $arrHorarios = $db->getSQLArray($sql);
                $comision['horarios'] = "";
                foreach ($arrHorarios as $horario)
                    $comision['horarios'] .= "~**~" . $horario['dia'] . " " . $horario['inicio'] . " a " . $horario['fin'];

                $comision['horarios'] = substr($comision['horarios'], 4);
                unset($arrHorarios);

                //Traigo los cupos
                $sql = "SELECT idcomcupo, descripcion, cantidad, cantdisponible";
                $sql .= " FROM comcupos";
                $sql .= " WHERE idcomision=" . $comision['idcomision'];
                $comision['cupos'] = $db->getSQLArray($sql);
                $cupoCupos = "";
                $cupoTotal = 0;
                $dispTotal = 0;
                foreach (($comision['cupos']) as $cupo) {
                    $cupoCupos .= $cupo['descripcion'] . "~***~" . $cupo['cantidad'] . "~***~";
                    $cupoCupos .= $cupo['cantdisponible'] . "~***~" . $cupo['idcomcupo'] . "~**~";
                    $cupoTotal += $cupo['cantidad'];
                    $dispTotal += $cupo['cantdisponible'];
                }
                //Traigo los datos del profesor de la comision
                $sql = "SELECT p.apellido, p.nombre, sr.descripcion";
                $sql .= " FROM comprofesor cp";
                $sql .= " INNER JOIN personas p on cp.idpersona=p.idpersona and p.estado=1";
                $sql .= " INNER JOIN situacionrevista sr on cp.situacionrevista_id=sr.id";
                $sql .= " WHERE cp.idcomision=" . $comision['idcomision'];
                $arrProfesores = $db->getSQLArray($sql);
                $comision['profesores'] = "";
                foreach ($arrProfesores as $profesor)
                    $comision['profesores'] .= "~**~" . $profesor['apellido'] . "," . $profesor['nombre'] . " (" . $profesor['descripcion'] . ")";

                $comision['profesores'] = substr($comision['profesores'], 4);
                unset($arrProfesores);
                $comision['cupoCupos'] = substr($cupoCupos, 0, -4);
                $comision['cupoTotal'] = $cupoTotal;
                $comision['cupoDisponible'] = $dispTotal;
            }
        }
        $db->dbDisconnect();
        echo json_encode($arrComisiones);
    }

    public function matriculacionlibre30porciento($IdMateria,$IdCarrera,$IdPersona) {
        /*
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $lib = & $this->POROTO->Libraries['siteLibrary'];

        $db->dbConnect("matriculacion/");
        
        //Traigo el resto de las materias del año de la materia en cuestion.
        $sql="select m.idmateria from materias m inner join carreramateria cm on
        m.idmateria=cm.idmateria where m.idmateria not in (".$IdMateria.")
        and m.anio = (select anio from materias where idmateria=".$IdMateria.")
        and cm.idcarrera=".$IdCarrera." and estado=1";
        
        //traer de alumnocarrera, si la inscripcion tiene instrumento/area y CURSANDO.
        $sql = "select idarea,idinstrumento from alumnocarrera where idcarrera=";
        $sql .= $idCarrera . " and idpersona=" . $idAlumno . " and fechafinalizada is null and estado in (1,3) ";
        $sql .= "and idalumnocarrera=" . $idAlumnoCarrera;
        //Aca se podria sacar el estado ya que siempre voy a traer la q corresponda...

        $arrAlumno = $db->getSQLArray($sql);
    */
        }
    
    public function matriculacionhabilitada($idPersona = 0, $idCarrera = 0) { //Matriculacion ESCALONADA
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $lib = & $this->POROTO->Libraries['siteLibrary'];

        $grupoCarrera = "";

        //Vuelvo a actualizar las variables de configuración por si cambiaron recien...
        $sql = "select parametro,valor from configuracion order by orden";
        $result = $db->getSQLArray($sql);
        $ses->clearConfiguracion();
        foreach ($result as $conf) {
            $ses->agregarConfiguracion($conf["parametro"], $conf["valor"]);
        }

        if ($idCarrera == 1 || $idCarrera == 5) { //FOBA
            $grupoCarrera = "FOBA";
        } else {
            $grupoCarrera = "SUPERIOR";
        }

        $sql = "";
        /* cuarto año */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_cuarto_anio") && $grupoCarrera == "SUPERIOR") {
            $sql .= "select am.idpersona,am.idcarrera,'CUARTO AÑO' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        inner join alumnos al on
        am.idpersona=al.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_cuarto_anio_materias") . ")
        and am.idestadoalumnomateria in (3,4,5)
        and ac.estado in (1,3) 
        and am.idpersona=" . $idPersona . " ";
        }

        /* tercero año */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_tercer_anio") && $grupoCarrera == "SUPERIOR") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "
        select am.idpersona,am.idcarrera,'TERCER AÑO' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_tercer_anio_materias") . ")
        and am.idestadoalumnomateria in (3,4,5)
        and ac.estado in (1,3) 
        and am.idpersona=" . $idPersona . " ";
        }

        /* segundo año */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_segundo_anio") && $grupoCarrera == "SUPERIOR") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select am.idpersona,am.idcarrera,'SEGUNDO AÑO' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_segundo_anio_materias") . ")
        and am.idestadoalumnomateria in (3,4,5)
        and ac.estado in (1,3)
        and am.idpersona=" . $idPersona . " ";
        }

        /* primer año FOBA finalizado o pre finalizado */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_primer_anio_certif") && $grupoCarrera == "SUPERIOR") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select ac.idpersona," . $idCarrera . " as idcarrera,'PRIMER AÑO c/Certif' as matriculacion from alumnocarrera ac
        inner join alumnos a on ac.idpersona=a.idpersona
        where ac.idcarrera in (1,5) and ac.estado in (2,3)
        and a.certificadotrabajo=1
        and ac.idpersona=" . $idPersona . " ";
        }

        /* primer año FOBA finalizado o pre finalizado MIERCOLES 14 */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_primer_anio_scertif") && $grupoCarrera == "SUPERIOR") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select ac.idpersona, " . $idCarrera . " as idcarrera,'PRIMER AÑO s/Certif' as matriculacion from alumnocarrera ac
        inner join alumnos a on ac.idpersona=a.idpersona
        where ac.idcarrera in (1,5) and ac.estado in (2,3)
        and ac.idpersona=" . $idPersona . " ";
        }

        /* FORMACIÓN BÁSICA NIVEL III C/CONSTANCIA LAB.
          TENER CONSTANCIA LABORAL CON HORARIOS<BR>
          LENGUAJE MUSICAL II con cursada aprobada<br><br>
         */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_niveliii_constancia") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select am.idpersona,am.idcarrera,'NIVEL III c/Constancia' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        inner join alumnos al on
        am.idpersona=al.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_niveliii_materias") . ")
        and am.idestadoalumnomateria in (3,4,5)
        and ac.estado in (1,3)
        and al.certificadotrabajo=1
        and am.idpersona=" . $idPersona . " ";
        }

        /* FORMACIÓN BÁSICA NIVEL III
          LENGUAJE MUSICAL II con cursada aprobada (FOBA)<br>
         */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_niveliii") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select am.idpersona,am.idcarrera,'NIVEL III Regulares' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        inner join alumnos al on
        am.idpersona=al.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_niveliii_materias") . ")
        and am.idestadoalumnomateria in (3,4,5)
        and ac.estado in (1,3)
        and am.idpersona=" . $idPersona . " ";
        }

        /* FORMACIÓN BÁSICA NIVEL III Ingresantes
          LENGUAJE MUSICAL II estado nivelacion (FOBA)<br>
         */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_niveliii_ingresantes") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select am.idpersona,am.idcarrera,'NIVEL III Ingresantes' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        inner join alumnos al on
        am.idpersona=al.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_niveliii_materias") . ")
        and am.idestadoalumnomateria in (7)
        and ac.estado in (1,3)
        and am.idpersona=" . $idPersona . " ";
        }

        /* FORMACIÓN BÁSICA NIVEL II C/CONSTANCIA LAB
          TENER CONSTANCIA LABORAL CON HORARIOS<BR>
          LENGUAJE MUSICAL I con cursada aprobada (FOBA)
         */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_nivelii_constancia") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select am.idpersona,am.idcarrera,'NIVEL II c/Constancia' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        inner join alumnos al on
        am.idpersona=al.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_nivelii_materias") . ")
        and am.idestadoalumnomateria in (3,4,5)
        and ac.estado in (1)
        and al.certificadotrabajo=1
        and am.idpersona=" . $idPersona . " ";
        }

        /* FORMACIÓN BÁSICA NIVEL II
          LENGUAJE MUSICAL I con cursada aprobada (FOBA)
         */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_nivelii") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select am.idpersona,am.idcarrera,'NIVEL II Regulares' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        inner join alumnos al on
        am.idpersona=al.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_nivelii_materias") . ")
        and am.idestadoalumnomateria in (3,4,5)
        and ac.estado in (1)
        and am.idpersona=" . $idPersona . " ";
        }

        /* FORMACIÓN BÁSICA NIVEL II
          LENGUAJE MUSICAL I con NIVELACION (FOBA)
         */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_nivelii_ingresantes") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select am.idpersona,am.idcarrera,'NIVEL II Ingresantes' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        inner join alumnos al on
        am.idpersona=al.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_nivelii_materias") . ")
        and am.idestadoalumnomateria in (7)
        and ac.estado in (1)
        and am.idpersona=" . $idPersona . " ";
        }

        /* FORMACIÓN BÁSICA NIVEL I C/CONSTANCIA LAB
          TENER CONSTANCIA LABORAL CON HORARIOS
          LENGUAJE MUSICAL PREP con cursada aprobada (FOBA)
         */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_niveli_constancia") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select am.idpersona,am.idcarrera,'NIVEL I c/Constancia' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        inner join alumnos al on
        am.idpersona=al.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_niveli_materias") . ")
        and am.idestadoalumnomateria in (3,4,5)
        and ac.estado in (1)
        and al.certificadotrabajo=1
        and am.idpersona=" . $idPersona . " ";
        }

        /* FORMACIÓN BÁSICA NIVEL I
          LENGUAJE MUSICAL PREP con cursada aprobada
         */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_niveli") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select am.idpersona,am.idcarrera,'NIVEL I Regulares' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        inner join alumnos al on
        am.idpersona=al.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_niveli_materias") . ")
        and am.idestadoalumnomateria in (3,4,5)
        and ac.estado in (1)
        and am.idpersona=" . $idPersona . " ";
        }


        /* FORMACIÓN BÁSICA NIVEL I INGRESANTES C/CONSTANCIA LAB
          TENER CONSTANCIA LABORAL CON HORARIOS
          LENGUAJE MUSICAL PREP APROBADO POR NIVELACION (FOBA)
         */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_niveli_ingresantes_constancia") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select am.idpersona,am.idcarrera,'NIVEL I Ingresantes c/Constancia' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        inner join alumnos al on
        am.idpersona=al.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_niveli_materias") . ")
        and am.idestadoalumnomateria in (7)
        and ac.estado in (1)
        and al.certificadotrabajo=1
        and am.idpersona=" . $idPersona . " ";
        }


        /* FORMACIÓN BÁSICA NIVEL I INGRESANTES
          LENGUAJE MUSICAL PREP APROBADO POR NIVELACION (FOBA)
         */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_niveli_ingresantes") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select am.idpersona,am.idcarrera,'NIVEL I Ingresantes' as matriculacion from alumnomateria am 
        inner join alumnocarrera ac on 
        am.idcarrera=ac.idcarrera and am.idpersona=ac.idpersona
        inner join alumnos al on
        am.idpersona=al.idpersona
        where am.idmateria in (" . $ses->getParametroConfiguracion("matriculacion_niveli_materias") . ")
        and am.idestadoalumnomateria in (7)
        and ac.estado in (1)
        and am.idpersona=" . $idPersona . " ";
        }


        /* FORMACIÓN BÁSICA PREPARATORIO C/CONSTANCIA LAB.
          TRAIGO ALUMNOS REGULARES QUE TENGAN LA CARRERA FOBA EN ESTADO CURSANDO.
         *      */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_pre_constancia") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select ac.idpersona,ac.idcarrera,'PRE c/Constancia' as matriculacion from 
        alumnocarrera ac
        inner join alumnos al on
        ac.idpersona=al.idpersona
        where
        ac.idcarrera in (1,5)
        and ac.estado in (1)
        and al.estadoalumno_id=3
        and al.certificadotrabajo=1
        and ac.idpersona=" . $idPersona . " ";
        }

        /* FORMACIÓN BÁSICA PREPARATORIO INGRESANTES C/CONSTANCIA LAB.
          TRAIGO LOS ALUMNOS CON ESTADO INGRESANTES Y QUE TENGAN LA CARRERA FOBA EN ESTADO CURSANDO
         *      */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_pre_ingresantes_constancia") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select ac.idpersona,ac.idcarrera,'PRE Ingresantes C/Constancia' as matriculacion from 
        alumnocarrera ac
        inner join alumnos al on
        ac.idpersona=al.idpersona
        where
        ac.idcarrera in (1,5)
        and al.estadoalumno_id=6
        and ac.estado in (1)
        and al.certificadotrabajo=1
        and ac.idpersona=" . $idPersona . " ";
        }

        /* FORMACIÓN BÁSICA PREPARATORIO TODO EL RESTO
          QUE TENGAN LA CARRERA FOBA EN ESTADO CURSANDO
         */
        if ($ses->getParametroConfiguracion("matriculacion_habilitada_pre") && $grupoCarrera == "FOBA") {
            if ($sql != "")
                $sql .= " union all ";
            $sql .= "select ac.idpersona,ac.idcarrera,'PRE' as matriculacion from 
        alumnocarrera ac
        inner join alumnos al on
        ac.idpersona=al.idpersona
        where
        ac.idcarrera in (1,5)
        and al.estadoalumno_id in (3,6)
        and ac.estado in (1)
        and ac.idpersona=" . $idPersona . " ";
        }


        if ($sql != "") {
            $arrResultados = $db->getSQLArray($sql);
        } else {
            $arrResultados = array();
        }
        $encontre = false;

        $arr2 = array();
        foreach ($arrResultados as $re) {
            if ($idPersona == $re["idpersona"] && $idCarrera == $re["idcarrera"]) {
                $encontre = true;
                $regla = array();
                $regla['idpersona'] = $re['idpersona'];
                $regla['idcarrera'] = $re['idcarrera'];
                $regla['matriculacion'] = $re['matriculacion'];
                $arr2[] = $regla;
            }
        }
        return $arr2;
    }

    public function matricular($idCarrera = 0, $idAlumnoCarrera = 0, $idPersona = 0, $params = "") { // OK
        //Procedimiento para entrar a formulario de Matriculacion desde Gestion Alumnos o Alumno
        //Variable $modoAdministrativo seteada a mano dependiendo de donde entre.
        //Si no viene ningun parametro, estoy entrando desde el menu Matricularme.
        //Si viene la carrera y la persona vengo desde Gestion de Alumnos Matricular.
        $validationErrors = array();
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $lib = & $this->POROTO->Libraries['siteLibrary'];

        $db->dbConnect("matriculacion/matricular/" . $idCarrera . "/" . $idAlumnoCarrera . "/" . $idPersona . "/" . $params);

        if ($idCarrera == 0) { //Acceso desde el menu
            if (!$ses->tienePermiso('', 'Matricularme Acceso desde Menu')) {
                $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
                header("Location: /", TRUE, 302);
                exit();
            }
            $dataIdPersona = $ses->getIdPersona();
            $dataIdCarrera = $idCarrera;
            $dataIdAlumnoCarrera = $idAlumnoCarrera;
            $modoAdministrativo = false;
            $tienePermisos = true;
        } else { //Acceso desde Gestion de Alumnos
            if (!$ses->tienePermiso('', 'Matricularme desde Gestion de Alumnos')) {
                $ses->setMessage("Acceso denegado. Contactese con el administrador."
                        , SessionMessageType::TransactionError);
                header("Location: /", TRUE, 302);
                exit();
            }

            $dataIdPersona = $db->dbEscape($idPersona);
            $dataIdCarrera = $db->dbEscape($idCarrera);
            $dataIdAlumnoCarrera = $db->dbEscape($idAlumnoCarrera);
            $tienePermisos = true;
            $modoAdministrativo = true;
        }

        //cargo el menu del  usuario (por rol)
        $arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());

        if (!$tienePermisos) {
            header("Location: /", TRUE, 302);
            exit();
        }

        if (isset($_POST['carrera'])) { //Entro por la insercion
            $dataCarrera = $db->dbEscape(intval(trim($_POST['carrera'])));
            $dataIdAlumnoCarrera = $db->dbEscape(intval(trim($_POST['idalumnocarrerahidden'])));
            $dataMateria = $db->dbEscape(intval(trim($_POST['idmateria'])));
            $dataComision = $db->dbEscape(intval(trim($_POST['idcomision'])));

            if (isset($_POST['check-condicional']))
                $dataCondicional = "1";
            else
                $dataCondicional = "0";

            //Cambio 20180316 enviar notificacion al alumno
            if (isset($_POST['check-email']))
                $dataEnviarEmail = "1";
            else
                $dataEnviarEmail = "0";

            $regla6validar = $_POST['tieneregla6'];
            $regla6 = $_POST['cumpleregla6'];


            // Si no se anota LIBRE, chequear CUPO de la comision elegida
            if ($dataComision > 0) {
                //Chequeo el CUPO    
                $dataCupo = $db->dbEscape(intval(trim($_POST['comision-detalle-cupotipo'])));
                $sql = "SELECT cantdisponible FROM comcupos WHERE idcomcupo=" . $dataCupo;
                $arr = $db->getSQLArray($sql);
                if (count($arr) != 1 || $arr[0]['cantdisponible'] < 1)
                    $validationErrors['cupotipo'] = "No existe cupo disponible para esta comisión / rol";
            }

            //20180323 Chequeo que no se haya matriculado antes o en una sesion paralela en la misma materia.
            $sql = " SELECT * from alumnomateria where idalumnocarrera=" . $dataIdAlumnoCarrera;
            $sql .= " and idmateria=" . $dataMateria . " and idestadoalumnomateria not in ";
            $sql .= " (" . $this->POROTO->Config['estado_alumnomateria_cancelada'] . "," . $this->POROTO->Config['estado_alumnomateria_desaprobada'] . ")";
            $arr = $db->getSQLArray($sql);
            if (count($arr) > 0) {
                $validationErrors['consistencia'] = "No es posible matricularse en la materia seleccionada ya que el alumno ya posee dicha materia entre las materias activas. Consulte con administración.";
            }


            if (count($validationErrors) == 0) {
                $sql = "SELECT email FROM usuarios where idpersona=" . $dataIdPersona;
                $arr = $db->getSQLArray($sql);
                $mailUsuario = $arr[0]['email'];
                unset($arr);

                if ($dataComision > 0) { //ANOTADO EN UNA COMISION EN PARTICULAR
                    $sql = "SELECT anio FROM comisiones WHERE idcomision=" . $dataComision;
                    $arr = $db->getSQLArray($sql);
                    $comisionAnio = $arr[0]['anio'];
                    unset($arr);
                    //Traigo datos que voy a usar para el mail
                    $sql = "SELECT m.nombre materia,c.nombre comision,c.turno, cc.descripcion rol, ch.dia,";
                    $sql .= " date_format(ch.inicio, '%H:%i') inicio, date_format(ch.fin, '%H:%i') fin";
                    $sql .= " FROM comisiones c";
                    $sql .= " INNER JOIN comcupos cc on cc.idcomision=c.idcomision";
                    $sql .= " INNER JOIN materias m on m.idmateria=c.idmateria";
                    $sql .= " left JOIN comhorario ch on ch.idcomision=c.idcomision";
                    $sql .= " WHERE cc.idcomcupo=" . $dataCupo;
                    $arrDataMail = $db->getSQLArray($sql);

                    //Inserto en alumno materia CURSANDO.
                    $sql = "INSERT INTO alumnomateria (idalumnocarrera,idpersona, idcarrera, idmateria, idcomision, aniocursada,";
                    $sql .= " idestadoalumnomateria, fechaaprobacion, usucrea, fechacrea, usumodi, fechamodi)";
                    $sql .= " SELECT " . $dataIdAlumnoCarrera . "," . $dataIdPersona;
                    $sql .= "," . $dataCarrera; //Cambio 43 20170717
                    $sql .= "," . $dataMateria;
                    $sql .= "," . $dataComision;
                    $sql .= "," . $comisionAnio;
                    $sql .= "," . $this->POROTO->Config['estado_alumnomateria_cursando'];
                    $sql .= ",null";
                    $sql .= ",'" . $ses->getUsuario() . "'";
                    $sql .= ",CURRENT_TIMESTAMP";
                    $sql .= ",null";
                    $sql .= ",null";
                    $db->begintrans();
                    $idAlumnoMateria = $db->insert($sql, '', true);
                    $bOk = $idAlumnoMateria;

                    //Registro condicionalidades si es necesario.
                    // materia,0,null
                    // materia,1,0 tiene regla6 y no la cumple.
                    // materia,0,1 tiene regla6 y la cumple.
                    $sql = " insert into alumnomateria_condicionalidades(idalumnomateria,condicional,condicionalregla6) ";
                    $sql .= " Select " . $idAlumnoMateria . "," . $dataCondicional . ",";

                    if ($regla6validar == "true") {
                        if ($regla6 == "true") {
                            $sql .= "0";
                        } else {
                            $sql .= "1";
                        }
                    } else {
                        $sql .= "null";
                    }

                    $bOk = $db->insert($sql, '', true);

                    //"Si cumplio la regla6, se supone que debo cambiar la condicionalidad en las otras materias.
                    //verrrrrrrr
                    if ($regla6validar && $regla6) {
                        $sql = " select idpersona,valor1 ";
                        $sql .= " from alumnomateria am inner join alumnomateria_condicionalidades amc ";
                        $sql .= " on am.idalumnomateria=amc.idalumnomateria ";
                        $sql .= " inner join correlatividades c on am.idmateria=c.idmateria and am.idcarrera=c.idcarrera ";
                        $sql .= " where c.idregla=6 and amc.condicionalregla6=0 and am.idalumnomateria=" . $idAlumnoMateria;
                        $arrValores = $db->getSQLArray($sql);
                        if (count($arrValores) > 0) {
                            $sql = " update alumnomateria_condicionalidades set ";
                            $sql .= " condicionalregla6=0 ";
                            $sql .= " where condicionalregla6=1 and idalumnomateria in (";
                            $sql .= " select am.idalumnomateria from alumnomateria am inner join correlatividades co ";
                            $sql .= " on am.idmateria=co.idmateria and am.idcarrera=co.idcarrera ";
                            $sql .= " where co.idregla=6 and am.idmateria in (" . $arrValores[0]["valor1"] . ") ";
                            $sql .= " and am.idpersona=" . $arrValores[0]["idpersona"] . ")";
                            $bOk = $db->update($sql, '', true);
                        }
                    }

                    //Busco si existe el registro en comalumno lo actualizo, sino lo inserto.
                    $sql = "select * from comalumno where ";
                    $sql .= " idcomision=" . $dataComision;
                    $sql .= " and idpersona=" . $dataIdPersona;
                    $sql .= " and idcomcupo=" . $dataCupo;
                    $arrDataComAlumno = $db->getSQLArray($sql);
                    if (count($arrDataComAlumno) > 0) {
                        //Actualizo
                        $sql = "update comalumno set estado=1,usumodi='" . $ses->getUsuario() . "', ";
                        $sql .= "fechamodi=CURRENT_TIMESTAMP ";
                        $sql .= "where idcomision=" . $dataComision;
                        $sql .= " and idpersona=" . $dataIdPersona;
                        $sql .= " and idcomcupo=" . $dataCupo;
                        if ($bOk !== false)
                            $bOk = $db->update($sql, '', true);
                    }
                    else {
                        //Inserto en Comalumno 			   
                        $sql = "INSERT INTO comalumno (idcomision, idpersona, idcomcupo, estado, usucrea, ";
                        $sql .= " fechacrea, usumodi, fechamodi)";
                        $sql .= " SELECT " . $dataComision;
                        $sql .= "," . $dataIdPersona;
                        $sql .= "," . $dataCupo;
                        $sql .= ",1";
                        $sql .= ",'" . $ses->getUsuario() . "'";
                        $sql .= ",CURRENT_TIMESTAMP";
                        $sql .= ",null";
                        $sql .= ",null";
                        if ($bOk !== false)
                            $bOk = $db->insert($sql, '', true);
                    }

                    //Actualizo en comcupos restando uno.
                    $sql = "UPDATE comcupos";
                    $sql .= " SET cantdisponible = cantdisponible-1";
                    $sql .= " WHERE idcomcupo = " . $dataCupo;
                    if ($bOk !== false)
                        $bOk = $db->update($sql, '', true);
                }else {     //ANOTADO LIBRE SIN COMISION
                    //Traigo datos que voy a usar para el mail
                    $sql = "SELECT m.nombre materia";
                    $sql .= " FROM ";
                    $sql .= " materias m";
                    $sql .= " WHERE m.idmateria=" . $dataMateria;
                    $arrDataMail = $db->getSQLArray($sql);
                    //Inserto en alumno materia CURSANDO.
                    $sql = "INSERT INTO alumnomateria (idalumnocarrera,idpersona, idcarrera, idmateria, idcomision, aniocursada,";
                    $sql .= " idestadoalumnomateria, fechaaprobacion, usucrea, fechacrea, usumodi, fechamodi)";
                    $sql .= " SELECT " . $dataIdAlumnoCarrera . "," . $dataIdPersona;
                    $sql .= "," . $dataCarrera;
                    $sql .= "," . $dataMateria;
                    $sql .= ",null";
                    $sql .= "," . date("Y");
                    $sql .= "," . $this->POROTO->Config['estado_alumnomateria_libre'];
                    $sql .= ",null";
                    $sql .= ",'" . $ses->getUsuario() . "'";
                    $sql .= ",CURRENT_TIMESTAMP";
                    $sql .= ",null";
                    $sql .= ",null";
                    $db->begintrans();
                    $idAlumnoMateria = $db->insert($sql, '', true);
                    $bOk = $idAlumnoMateria;

                    //Registro condicionalidades si es necesario.
                    // materia,0,null
                    // materia,1,0 tiene regla6 y no la cumple.
                    // materia,0,1 tiene regla6 y la cumple.
                    $sql = " insert into alumnomateria_condicionalidades(idalumnomateria,condicional,condicionalregla6) ";
                    $sql .= " Select " . $idAlumnoMateria . "," . $dataCondicional . ",";

                    if ($regla6validar == "true") {
                        if ($regla6 == "true") {
                            $sql .= "0";
                        } else {
                            $sql .= "1";
                        }
                    } else {
                        $sql .= "null";
                    }

                    $bOk = $db->insert($sql, '', true);

                    //"Si cumplio la regla6, se supone que debo cambiar la condicionalidad en las otras materias.
                    //verrrrrrrr
                    if ($regla6validar && $regla6) {
                        $sql = " select idpersona,valor1 ";
                        $sql .= " from alumnomateria am inner join alumnomateria_condicionalidades amc ";
                        $sql .= " on am.idalumnomateria=amc.idalumnomateria ";
                        $sql .= " inner join correlatividades c on am.idmateria=c.idmateria and am.idcarrera=c.idcarrera ";
                        $sql .= " where c.idregla=6 and amc.condicionalregla6=0 and am.idalumnomateria=" . $idAlumnoMateria;
                        $arrValores = $db->getSQLArray($sql);
                        if (count($arrValores) > 0) {
                            $sql = " update alumnomateria_condicionalidades set ";
                            $sql .= " condicionalregla6=0 ";
                            $sql .= " where condicionalregla6=1 and idalumnomateria in ( ";
                            $sql .= " select am.idalumnomateria from alumnomateria am inner join correlatividades co ";
                            $sql .= " on am.idmateria=co.idmateria and am.idcarrera=co.idcarrera ";
                            $sql .= " where co.idregla=6 and am.idmateria in (" . $arrValores[0]["valor1"] . ") ";
                            $sql .= " and am.idpersona=" . $arrValores[0]["idpersona"] . ")";
                            $bOk = $db->update($sql, '', true);
                        }
                    }
                }


                if ($bOk === false) {
                    $db->rollback();
                    $ses->setMessage("Se produjo un error registrando la matriculación", SessionMessageType::TransactionError, $params);
                    if ($modoAdministrativo) {
                        header("Location: /matricular/" . $dataCarrera . "/" . $idAlumnoCarrera . "/" . $dataIdPersona . "/" . $params, TRUE, 302);
                    } else {
                        header("Location: /matricularme", TRUE, 302);
                    }
                    exit();
                } else {
                    $db->commit();
                }
                $db->dbDisconnect();

                if ($this->POROTO->Config['override_mail_address'] != "")
                    $mailto = $this->POROTO->Config['override_mail_address'];
                else
                    $mailto = $mailUsuario;
                $mailSubject = $this->POROTO->Config["empresa_descripcion"]." - Matriculación completada";
                if ($dataComision > 0) { //ANOTADO EN UNA COMISION EN PARTICULAR
                    $mailBody = "Usted ha completado satisfactoriamente la matriculación. Este es el detalle de la comisión:<br />";
                    $mailBody .= "Materia: <b>" . $arrDataMail[0]['materia'] . "</b><br />";
                    $mailBody .= "Comision: <b>" . $arrDataMail[0]['comision'] . "</b><br />";
                    $mailBody .= "Turno: <b>" . $arrDataMail[0]['turno'] . "</b><br />";
                    $mailBody .= "Rol: <b>" . $arrDataMail[0]['rol'] . "</b><br />";
                    $mailBody .= "Horarios:<br />";
                    if ($arrDataMail[0]['dia'] != "") {
                        foreach ($arrDataMail as $horario) {
                            $mailBody .= "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<b>";
                            $mailBody .= $horario['dia'] . " " . $horario['inicio'] . " a " . $horario['fin'] . "</b><br/>";
                        }
                    }
                } else {
                    $mailBody = "Usted ha completado satisfactoriamente la matriculación. Este es el detalle de la misma<br />";
                    $mailBody .= "Materia: <b>" . $arrDataMail[0]['materia'] . "</b><br />";
                    $mailBody .= "LIBRE: <b>SI</b><br />";
                }

                //Envio de email notificación al alumno
                if ($modoAdministrativo) {
                    if ($dataEnviarEmail == 1) {
                        $lib->sendMail($mailto, $mailSubject, $mailBody);
                    }
                } else {
                    $lib->sendMail($mailto, $mailSubject, $mailBody);
                }


                $ses->setMessage("Matriculación realizada con éxito<br>" . $mailBody, SessionMessageType::Success, $params);
                if ($modoAdministrativo) {
                    header("Location: /matricular/" . $dataCarrera . "/" . $idAlumnoCarrera . "/" . $dataIdPersona . "/" . $params, TRUE, 302);
                } else {
                    header("Location: /matricularme", TRUE, 302);
                }
                exit();
            } //validationErrors=0
            else {
                //Si hubo algun error ya sea de correlativas o de cupos
                //mostraer cartel de error. (ver porque en teoria como tiene datos la var directamente la muestra.
            }
        } //del if set
        //Traigo las carreras de la persona que esten disponibles para matricularse.
        $sql = " SELECT c.idcarrera, c.descripcion, ac.idalumnocarrera,ac.idinstrumento,i.nombre as instrumento";
        $sql .= " FROM alumnocarrera ac";
        $sql .= " INNER JOIN carreras c on ac.idcarrera=c.idcarrera";
        $sql .= " left join instrumentos i on ac.idinstrumento=i.idinstrumento";
        $sql .= " WHERE ac.idpersona=" . $dataIdPersona;
        $sql .= " and ac.estado in (1,3)";
        $sql .= " and ac.fechafinalizada is null";
        $sql .= " and c.estado=1";
        $sql .= " ORDER BY 2";
        $viewData = $db->getSQLArray($sql);

        if (!$ses->tienePermiso('', 'Matricularme desde Gestion de Alumnos')) {
            //Valido Matriculacion ESCALONADA SOLO PARA ALUMNOS.
            $viewData2 = array(); //Array temporal donde solo se pondran las carreras que cumplan
            foreach ($viewData as $arrtemp1) {
                $cumple = false;
                $arrtemp3 = array();
                $arrtemp3 = $this->matriculacionhabilitada($dataIdPersona, $arrtemp1["idcarrera"]);
                foreach ($arrtemp3 as $arrtemp4) {
                    //CONTINUAR: si cumple 2 de las reglas estaria agregando dos veces la carrera.
                    $cumple = true;
                    //echo("Cumple".$arrtemp4["idpersona"]." ".$arrtemp4["idcarrera"]." ".$arrtemp4["matriculacion"]." <br>");
                }
                if ($cumple) {
                    $arrtemp2 = array();
                    $arrtemp2["idcarrera"] = $arrtemp1["idcarrera"];
                    $arrtemp2["descripcion"] = $arrtemp1["descripcion"];
                    $arrtemp2["idalumnocarrera"] = $arrtemp1["idalumnocarrera"];
                    $arrtemp2["idinstrumento"] = $arrtemp1["idinstrumento"];
                    $arrtemp2["instrumento"] = $arrtemp1["instrumento"];
                    $viewData2[] = $arrtemp2;
                }
            }
            $viewDataCarreras = $viewData2; //Habilito en el combo SOLO las carreras disponibles para matricularme segun las reglas.
        } else {
            $viewDataCarreras = $viewData;
        }

        $sql = "SELECt p.legajo, p.apellido, p.nombre, td.descripcion, p.documentonro";
        $sql .= " FROM personas p";
        $sql .= " INNER JOIN tipodoc td on p.tipodoc=td.id";
        $sql .= " WHERE p.idpersona=" . $dataIdPersona;
        $viewData = $db->getSQLArray($sql);

        $pageTitle = "Matriculación";
        include($this->POROTO->ViewPath . "/-header.php");
        include($this->POROTO->ViewPath . "/matriculacion.php");
        include($this->POROTO->ViewPath . "/-footer.php");
    }

}
