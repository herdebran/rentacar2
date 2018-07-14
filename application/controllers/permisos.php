<?php

class Permisos extends Controller {

    private $permisos;

    public function __construct($poroto) {
        parent::__construct($poroto);
        include($this->POROTO->ModelPath . '/permisos.php');
        $this->permisos = new ModeloPermisos($this->POROTO);
    }

    function defentry() {
        if ($this->POROTO->Session->isLogged()) {
            $this->index();
        } else {
            include($this->POROTO->ViewPath . "/-login.php");
        }
    }

    public function index() {
        if (!$this->ses->tienePermiso('', 'Gestion de Permisos - Acceso desde Menu')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }

        $params = array();
        $params['viewDataTipoDocumento'] = $this->app->getAllTipoDocumento();
        $params["roles"] = $this->app->getAllRoles();
        $params["permisos"] = $this->app->getAllPermisos();
        $this->render("/gestion-permisos.php", $params);
    }

    public function personasConFiltro($filter = null) {
        include($this->POROTO->ModelPath . '/persona.php');
        $persona = new Persona($this->POROTO);
        if (!$filter) {
            $filter = $_POST['filtros'];
        }
        $personas = $this->permisos->getPersonasPermisos($filter);
        $json = array("data" => $personas);

        echo json_encode($json);
    }

    /**
     * 
     * @param type $idpersona
     */
    public function detalle($idpersona) {
        if (!$this->ses->tienePermiso('', 'Gestion de Permisos - Acceso desde Menu')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /gestion-permisos.php", TRUE, 302);
            exit();
        }

        include($this->POROTO->ModelPath . '/persona.php');
        $personaModel = new Persona($this->POROTO);
        $params = array();
        $params["usuario"] = $this->permisos->getUsuarioByIdPersona($idpersona);
        $params["persona"] = $personaModel->getPersonaById($idpersona);
        $params["personaRoles"] = $this->permisos->getPersonaRolesByPersona($idpersona);
        $params["roles"] = $this->app->getAllRoles();
        $params["permisos"] = $this->app->getAllPermisos();
        $params["permisosAsignados"] = $this->permisos->getPermisosAsignados($idpersona);

        $this->render("/gestion-permisos-detalle.php", $params);
    }

    public function gestionroles($idrol = 0) {
        if (!$this->ses->tienePermiso('', 'Gestion de Permisos - Acceso desde Menu')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /gestion-permisos.php", TRUE, 302);
            exit();
        }

        include($this->POROTO->ModelPath . '/persona.php');
        $personaModel = new Persona($this->POROTO);
        $params = array();
        $params["roles"] = $this->app->getAllRoles();
        $params["rolselected"] = $idrol;
        $params["permisos"] = $tgetAllRoleshis->app->getAllPermisos();
        $params["permisosroles"] = $this->permisos->getRolesPermisos();
        if ($idrol > 0)
            $params["permisosactuales"] = $this->permisos->getPermisosDeRol($idrol);
        else {
            $params["permisosactuales"] = [];
        }
        $this->render("/gestion-roles.php", $params);
    }

    public function setpersonarol($idpersona, $idrol, $estado) {
        if (!$this->ses->tienePermiso('', 'Gestion de Permisos - Modificar')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }

        if ($this->permisos->setpersonarol($idpersona, $idrol, $estado)) {
            $this->ses->setMessage("El rol se cambio exitosamente.", SessionMessageType::Success);
        } else {
            $this->ses->setMessage("Error al cambiar el rol.", SessionMessageType::TransactionError);
        }
        header("Location: /permisos/detalle/$idpersona", TRUE, 302);
    }

    public function setpersonapermiso($idpersona, $idpermiso, $estado) {
        if (!$this->ses->tienePermiso('', 'Gestion de Permisos - Modificar')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }
        if ($this->permisos->setpersonapermiso($idpersona, $idpermiso, $estado)) {
            $this->ses->setMessage("El permiso se cambio exitosamente.", SessionMessageType::Success);
        } else {
            $this->ses->setMessage("Error al cambiar el permiso.", SessionMessageType::TransactionError);
        }
        header("Location: /permisos/detalle/$idpersona", TRUE, 302);
    }

    public function setpermisorol($idrol, $idpermiso, $estado) {
        if (!$this->ses->tienePermiso('', 'Gestion de Permisos - Modificar')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }
        if ($this->permisos->setpermisorol($idrol, $idpermiso, $estado)) {
            $this->ses->setMessage("El permiso se cambio exitosamente.", SessionMessageType::Success);
        } else {
            $this->ses->setMessage("Error al cambiar el permiso.", SessionMessageType::TransactionError);
        }
        header("Location: /permisos/gestionroles/$idrol", TRUE, 302);
    }

    public function setpersonaestado($idpersona, $estado) {
        if (!$this->ses->tienePermiso('', 'Gestion de Permisos - Modificar')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }
        if ($this->permisos->setpersonaestado($idpersona, $estado)) {
            $this->ses->setMessage("El estado de la persona se cambio exitosamente.", SessionMessageType::Success);
        } else {
            $this->ses->setMessage("Error al cambiar el estado de la persona.", SessionMessageType::TransactionError);
        }
        header("Location: /permisos/detalle/$idpersona", TRUE, 302);
    }

    public function resetpass($idpersona) {
        if (!$this->ses->tienePermiso('', 'Gestion de Permisos - Modificar')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }
        include($this->POROTO->ModelPath . '/persona.php');
        $personaModel = new Persona($this->POROTO);
        $persona = $this->permisos->getUsuarioByIdPersona($idpersona);
        if ($this->permisos->resetpass($idpersona, $persona["usuario"])) {
            $this->ses->setMessage("La contraseña se reseteo exitosamente.", SessionMessageType::Success);
        } else {
            $this->ses->setMessage("Error al resetear la contraseña.", SessionMessageType::TransactionError);
        }
        header("Location: /gestion-permisos", TRUE, 302);
    }

    public function crearpersona() { //OK
        $validationErrors = array();
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $db->dbConnect("permisos/crearpersona");

        //Cambio 38 Leo 20170706
        if (!$ses->tienePermiso('', 'Gestion de Permisos - Modificar')) {
            $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /gestion-alumnos", TRUE, 302);
            exit();
        }
        //Fin Cambio 38 Leo 20170706

        if (isset($_POST['apellido'])) {
            $dataApellido = mb_strtoupper($db->dbEscape(trim($_POST['apellido'])), 'UTF-8');
            $dataNombre = mb_strtoupper($db->dbEscape(trim($_POST['nombre'])), 'UTF-8');
            $dataNroDoc = $db->dbEscape(trim(intval($_POST['nrodoc'])));
            $dataTipDoc = $db->dbEscape($_POST['tipdoc']);

            // STAP! VALIDATION TIME
            // apellido - obligatorio maxlength 45
            if ($dataApellido == "")
                $validationErrors['apellido'] = "El campo Apellido es obligatorio";
            if (strlen($dataApellido) > 45)
                $validationErrors['apellido'] = "El campo Apellido puede contener como máximo 45 caracteres";
            // nombre - obligatorio maxlength 45
            if ($dataNombre == "")
                $validationErrors['nombre'] = "El campo Nombre es obligatorio";
            if (strlen($dataNombre) > 45)
                $validationErrors['nombre'] = "El campo Nombre puede contener como máximo 45 caracteres";
            // tipo documento - obligatorio
            if ($dataTipDoc == "" || $dataTipDoc == 0)
                $validationErrors['tipdoc'] = "El campo Tipo de Documento es obligatorio";
            // numero documento - obligatorio
            if ($dataNroDoc == "" || $dataNroDoc == 0)
                $validationErrors['nrodoc'] = "El campo Número de Documento es obligatorio";
            if (!is_numeric($_POST['nrodoc']))
                $validationErrors['nrodoc'] = "El campo Número de Documento no es numérico";
            //buscar tipo y nro de doc si ya existen

            if (count($validationErrors) == 0) {
                $sql = "select idpersona from personas where tipodoc=" . $dataTipDoc . " and documentonro=" . $dataNroDoc;
                $arr2 = $db->getSQLArray($sql);
                if (count($arr2) > 0) {
                    $existePersona = true;
                    $idPersona = $arr2[0]["idpersona"];
                } else {
                    $existePersona = false;
                }

                if (count($validationErrors) == 0 && !$existePersona) {
                    //buscar un nombre de usuario valido
                    $userName = $dataNroDoc;
                    $sql = "select idpersona from usuarios where usuario='" . $userName . "'";
                    $arr = $db->getSQLArray($sql);
                    if (count($arr) > 0) {
                        for ($i = 0; $i < 26; $i++) {
                            $extraChar = chr($i + 65);
                            $userName = $dataNroDoc . $extraChar;
                            $sql = "select idpersona from usuarios where usuario='" . $userName . "'";
                            $arr = $db->getSQLArray($sql);
                            if (count($arr) == 0)
                                break;
                        }
                    }

                    $sqlP = "INSERT INTO personas (legajo,apellido,nombre,tipodoc,documentonro,";
                    $sqlP .= "cuil,telefono1,telefono2,estadocivil,direccion,numero,";
                    $sqlP .= "piso,depto,idLocalidad,codpostal,fechanac,nacionalidad,";
                    $sqlP .= "sexo,entrecalles,hijos,familiaresacargo,socio,ultimopagocooperadora,estado,";
                    $sqlP .= "usucrea,fechacrea,usumodi,fechamodi) SELECT ";
                    $sqlP .= $dataNroDoc;
                    $sqlP .= ",'" . $dataApellido . "'";
                    $sqlP .= ",'" . $dataNombre . "'";
                    $sqlP .= "," . $dataTipDoc;
                    $sqlP .= "," . $dataNroDoc;
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",null";
                    $sqlP .= ",1";
                    $sqlP .= ",'" . $ses->getUsuario() . "'";
                    $sqlP .= ",CURRENT_TIMESTAMP";
                    $sqlP .= ",null";
                    $sqlP .= ",null";

                    $db->begintrans();
                    $newIdPersona = $db->insert($sqlP, '', true);
                    $bOk = $newIdPersona;

                    $sqlU = "INSERT INTO usuarios (idpersona,usuario,email,password,estado,";
                    $sqlU .= "primeracceso,usucrea,fechacrea,usumodi,fechamodi) SELECT ";
                    $sqlU .= $newIdPersona;
                    $sqlU .= ",'" . $userName . "'";
                    $sqlU .= ",''";
                    $sqlU .= ",'" . $userName . "'";
                    $sqlU .= ",1";
                    $sqlU .= ",1";
                    $sqlU .= ",'" . $ses->getUsuario() . "'";
                    $sqlU .= ",CURRENT_TIMESTAMP";
                    $sqlU .= ",null";
                    $sqlU .= ",null";
                    if ($bOk !== false)
                        $bOk = $db->insert($sqlU, '', true);

                    if ($bOk === false) {
                        $db->rollback();
                        $ses->setMessage("Se produjo un error realizando el alta", SessionMessageType::TransactionError);
                        header("Location: /gestion-permisos", TRUE, 302);
                        exit();
                    } else {
                        $db->commit();
                    }
                    $db->dbDisconnect();

                    $ses->setMessage("Usuario y persona creados con éxito", SessionMessageType::Success);
                    header("Location: /gestion-permisos", TRUE, 302);
                    exit();
                } else {
                    $ses->setMessage("Ya existe una persona con ese Tipo y Nùmero de Documento", SessionMessageType::TransactionError);
                }
            } else {
                $ses->setMessage("Complete todos los campos.", SessionMessageType::TransactionError);
            }
        }
        $arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());

        $sql = "SELECT id,descripcion FROM tipodoc order by id";
        $viewDataTipoDocumento = $db->getSQLArray($sql);

        $db->dbDisconnect();

        include($this->POROTO->ViewPath . "/-header.php");
        include($this->POROTO->ViewPath . "/crear-persona.php");
        include($this->POROTO->ViewPath . "/-footer.php");
    }

}
