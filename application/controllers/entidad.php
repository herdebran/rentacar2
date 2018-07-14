<?php

class Entidad extends Controller {

    private $entidad;
    
    public function __construct($poroto) {
        parent::__construct($poroto);
        include($this->POROTO->ModelPath . '/entidad.php');
        $this->entidad = new ModeloEntidad($this->POROTO);
    }


    
    //Hernan 2018017
    public function crearentidad($identidad = null) { 
        $validationErrors = array();
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $lib =& $this->POROTO->Libraries['siteLibrary'];
        $db->dbConnect("entidad/crearentidad");

        if(!$ses->tienePermiso('','Guardar entidad')){
                        $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
                        header("Location: /gestion-entidades", TRUE, 302);
                        exit();
        }

        if (isset($_POST['razonsocial'])) {
            $dataRazonSocial = mb_strtoupper($db->dbEscape(trim($_POST['razonsocial'])), 'UTF-8');
            $dataTipo = $db->dbEscape(trim(intval($_POST['tipo'])));

            // STAP! VALIDATION TIME
            if ($dataRazonSocial == "")
                $validationErrors['razonsocial'] = "El campo Razon Social es obligatorio";        
            if ($dataTipo == "" || $dataTipo == 0)
                $validationErrors['tipo'] = "El campo Tipo es obligatorio";
            if ($dataApellido == "")
                $validationErrors['apellido'] = "El campo Apellido es obligatorio";
            if ($dataNombre == "")
                $validationErrors['nombre'] = "El campo Nombre es obligatorio";
            if ($dataTipoDoc == "" || $dataTipoDoc == 0)
                $validationErrors['tipodoc'] = "El campo Tipo Documento es obligatorio";
            if (!is_numeric($_POST['nrodoc']))
                $validationErrors['nrodoc'] = "El campo Número Documento debe ser numérico";
            
            //buscar tipo y nro de doc si ya existen

            if (count($validationErrors) == 0) {
                $params = array();
                $params["razonsocial"]=$dataRazonSocial;
                $params["tipo"]=$dataTipo;
                //Mapear el resto para hacer la edicion
                $params["usuario"]=$ses->getUsuario();

                $entidad = $this->entidad;
                
                if ($identidad == null){
                    // Alta nueva entidad
                    $bOk=$entidad->nuevaEntidad($params);
                } else {
                    //Update entidad existente
                    $params["identidad"]=$identidad;
                    $bOk=$entidad->modificarEntidad($params);
                }
                if ($bOk["ok"] === false) {
                    $ses->setMessage("Se produjo un error al persistir." . $bOk["message"], SessionMessageType::TransactionError);
                    header("Location: /gestion-entidades", TRUE, 302);
                    exit();
                } 
                
                $ses->setMessage("Entidad guardada con éxito", SessionMessageType::Success);
                header("Location: /gestion-entidades", TRUE, 302);
                exit();

            } else {
                $ses->setMessage("Complete todos los campos.", SessionMessageType::TransactionError);
            }
        }
        $arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());
        $viewDataTipo=  $this->app->getAllTipoEntidad();
        $viewDataTipoDocumento=  $this->app->getAllTipoDocumento();
        $db->dbDisconnect();

        if ($identidad == null){
            $status="alta";
            $pageTitle="Alta de entidades";
        } else {
            $status="modificacion";
            $pageTitle="Edición de entidades";
            $viewDataEntidad=$this->entidad->getEntidadById($identidad);
            }
        include($this->POROTO->ViewPath . "/-header.php");
        include($this->POROTO->ViewPath . "/crear-entidad.php");
        include($this->POROTO->ViewPath . "/-footer.php");
    }

    //Hernan 20180617
    function gestionentidad() {
        if (!$this->ses->tienePermiso('', 'Gestión de Entidades - Acceso desde Menu')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }
        
        // ---------------------- Logica del metodo ----------------------------
        $entidad = $this->entidad;
        $params = array();
        $params['pageTitle'] = "Busqueda de Entidades";

        $params['viewDataTipoEntidad'] = $this->app->getAllTipoEntidad();
        $params["roles"] = $this->app->getAllRoles();
        // ---------------------- Fin logica del metodo ------------------------
        $this->render("/gestion-entidades.php", $params);
    }

    //Hernan 20180617
    public function entidadesConFiltro($filter = null) {
        $entidad = $this->entidad;
        if (!$filter) {
            $filter = $_POST['filtros'];
        }
        $entidades = $entidad->getEntidad($filter);
        $json = array("data" => $entidades);

        echo json_encode($json);
    }    
 }
