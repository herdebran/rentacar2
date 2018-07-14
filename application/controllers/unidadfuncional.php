<?php

class UnidadFuncional extends Controller {

    private $unidadfuncional;
    private $fideicomiso;
    
    public function __construct($poroto) {
        parent::__construct($poroto);
        include($this->POROTO->ModelPath . '/unidadfuncional.php');
        include($this->POROTO->ModelPath . '/fideicomiso.php');
        $this->unidadfuncional = new ModeloUnidadFuncional($this->POROTO);
        $this->fideicomiso = new ModeloFideicomiso($this->POROTO);
    }


    
    //Hernan 20180607
    public function crearunidadfuncional($idunidadfuncional = null) { 
        $validationErrors = array();
        $db = & $this->POROTO->DB;
        $ses = & $this->POROTO->Session;
        $lib =& $this->POROTO->Libraries['siteLibrary'];
        $db->dbConnect("fideicomiso/crearunidadfuncional");

        if(!$ses->tienePermiso('','Guardar unidad funcional')){
                        $ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
                        header("Location: /gestion-unidadesfuncionales", TRUE, 302);
                        exit();
        }

        if (isset($_POST['descripcion'])) {
            $dataDescripcion = mb_strtoupper($db->dbEscape(trim($_POST['descripcion'])), 'UTF-8');
            $dataTipo = $db->dbEscape(trim(intval($_POST['tipo'])));

            // STAP! VALIDATION TIME
            if ($dataDescripcion == "")
                $validationErrors['descripcion'] = "El campo Descripcion es obligatorio";
            if ($dataFechaConstitucion == "")
            {
               $validationErrors['fechaconst'] = "El campo Fecha de constitucion es obligatorio";
            }
            else {       
                if (!$lib->validateDate($dataFechaConstitucion)) 
		$validationErrors['fechaconst'] = "El campo Fecha de constitucion es inválido";
            }        
            if ($dataTipo == "" || $dataTipo == 0)
                $validationErrors['tipo'] = "El campo Tipo es obligatorio";
            if ($dataEstado == "" || $dataEstado == 0)
                $validationErrors['estado'] = "El campo Estado es obligatorio";
            if ($dataCalle == "")
                $validationErrors['calle'] = "El campo Calle es obligatorio";
            if ($dataNumero == "")
                $validationErrors['numero'] = "El campo Numero es obligatorio";
            if (!is_numeric($_POST['numero']))
                $validationErrors['numero'] = "El campo Número debe ser numérico";
            if ($dataLocalidad == "" || $dataLocalidad == 0)
                $validationErrors['localidad'] = "El campo Localidad es obligatorio";
            if ($dataLegalCalle == "")
                $validationErrors['legalcalle'] = "El campo Calle Legal es obligatorio";
            if ($dataLegalNumero == "")
                $validationErrors['legalnumero'] = "El campo Numero Legal es obligatorio";
            if (!is_numeric($_POST['legalnumero']))
                $validationErrors['legalnumero'] = "El campo Número Legal debe ser numérico";
            if ($dataLegalLocalidad == "" || $dataLegalLocalidad == 0)
                $validationErrors['legallocalidad'] = "El campo Localidad Legal es obligatorio";
            if ($dataDiaPrimerVenc == "")
                $validationErrors['diaprimervenc'] = "El dia de primer vencimiento es obligatorio";
            if (!is_numeric($_POST['diaprimervenc']))
                $validationErrors['diaprimervenc'] = "El dia de primer vencimiento debe ser numérico";
            //buscar tipo y nro de doc si ya existen

            if (count($validationErrors) == 0) {
                $params = array();
                $params["descripcion"]=$dataDescripcion;
                $params["idtipofideicomiso"]=$dataTipo;
                $params["usuario"]=$ses->getUsuario();

                $fideicomiso = $this->fideicomiso;
                
                if ($idfideicomiso == null){
                    // Alta nuevo fideicomiso
                    $bOk=$fideicomiso->nuevoFideicomiso($params);
                } else {
                    //Update fideicomiso existente
                    $params["idfideicomiso"]=$idfideicomiso;
                    $bOk=$fideicomiso->modificarFideicomiso($params);
                }
                if ($bOk["ok"] === false) {
                    $ses->setMessage("Se produjo un error al persistir." . $bOk["message"], SessionMessageType::TransactionError);
                    header("Location: /gestion-fideicomisos", TRUE, 302);
                    exit();
                } 
                
                $ses->setMessage("Fideicomiso guardado con éxito", SessionMessageType::Success);
                header("Location: /gestion-fideicomisos", TRUE, 302);
                exit();

            } else {
                $ses->setMessage("Complete todos los campos.", SessionMessageType::TransactionError);
            }
        }
        $arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());
        $viewDataTipo=  $this->app->getAllTipoUnidadFuncional();
        $viewDataFideicomiso=$this->fideicomiso->getFideicomisosActivos();
        $db->dbDisconnect();

        if ($idunidadfuncional == null){
            $status="alta";
            $pageTitle="Alta de unidad funcional";
        } else {
            $status="modificacion";
            $pageTitle="Edición de unidad funcional";
            $viewDataUnidadFuncional=$this->unidadfuncional->getUnidadFuncionalById($idunidadfuncional);
            }
        include($this->POROTO->ViewPath . "/-header.php");
        include($this->POROTO->ViewPath . "/crear-unidadfuncional.php");
        include($this->POROTO->ViewPath . "/-footer.php");
    }

    //Hernan 20180612
    function gestionuf() {
        if (!$this->ses->tienePermiso('', 'Gestión de Unidades Funcionales - Acceso desde Menu')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }
        
        // ---------------------- Logica del metodo ----------------------------
        $fideicomiso = $this->fideicomiso;
        $params = array();
        $params['pageTitle'] = "Busqueda de Unidades Funcionales";

        $params['viewDataTipoUF'] = $this->app->getAllTipoUnidadFuncional();
        $params['viewDataFideicomisos'] = $fideicomiso->getFideicomisosActivos();
        $params["roles"] = $this->app->getAllRoles();
        // ---------------------- Fin logica del metodo ------------------------
        $this->render("/gestion-unidadesfuncionales.php", $params);
    }

    //Hernan 20180613: Cambiar por la funcion de martin modelo unidadfuncional
    public function unidadesfuncionalesConFiltro($filter = null) {
        $uf = $this->unidadfuncional;
        if (!$filter) {
            $filter = $_POST['filtros'];
        }
        $unidadesfuncionales = $uf->getUnidadFuncional($filter);
        $json = array("data" => $unidadesfuncionales);

        echo json_encode($json);
    }    
 }
