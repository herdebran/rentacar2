<?php

class alumnocarrera extends Controller {

    public function __construct($poroto) {
        parent::__construct($poroto);
    }

    public function inscribir() {
        if (!$this->ses->tienePermiso('', 'Matriculación a Carrera o Area Acceso desde el Menu')) {
          $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
          header("Location: /", TRUE, 302);
          exit();
          }
          
          //Traigo las configuraciones por si se actualizaron
          $configuraciones=$this->app->getAllConfiguraciones();
          $this->ses->clearConfiguracion();
          foreach ($configuraciones as $conf) {
                        $this->ses->agregarConfiguracion($conf["parametro"],$conf["valor"]);
          }
          
          //Chequeo variable de configuracion
          if(!$this->ses->getParametroConfiguracion("matriculacion_carrera_area")){
          $this->ses->setMessage("Acceso denegado. No esta habilitada la funcionalidad para cargar nueva Carrera o Area.", SessionMessageType::TransactionError);
          header("Location: /", TRUE, 302);
          exit();    
          }
        // ---------------------- Logica del metodo ----------------------------

        $params = array();

        include($this->POROTO->ModelPath . '/alumnocarrera.php');

        $alucarrera = new AlumnoCarreraModel($this->POROTO);
        
        
        $fobas = $alucarrera->getFobasCompletas($this->ses->getIdPersona());
        $params["fobas"] = $fobas;

        $avanzadas = $alucarrera->getAvanzadas2anio($this->ses->getIdPersona());
        $params["avanzadas"] = $avanzadas;

        $areas = $alucarrera->getAreas();
        $params["areas"] = $areas;
        $carreras = $alucarrera->getCarreras();
        $params["carreras"] = $carreras;

        $this->render("/alumno-carrera.php", $params);
    }

    public function ajaxinscripcioncarrera() {
        include($this->POROTO->ModelPath . '/alumnocarrera.php');
        $alucarrera = new AlumnoCarreraModel($this->POROTO);
        $params = $_POST["params"];
        $params["idnivel"] = $alucarrera->getNivelPorCarrera($params["carrera"], $params["anio"])["id"];
        $params["idpersona"] = $this->ses->getIdPersona();
        $params["usuario"] = $this->ses->getUsuario();
        $response = array();
        try {
            $alucarrera->matricularcarrera($params);
            $this->ses->setMessage("La matriculación se realizo con exito.", SessionMessageType::Success);
        } catch (PDOException $e) {
            $this->ses->setMessage("Ocurrio un error en la matriculación. <br/>Error: " . $e->getMessage(), SessionMessageType::TransactionError);
        }
        echo json_encode($response);
    }

    public function ajaxinscripcionarea() {
        include($this->POROTO->ModelPath . '/alumnocarrera.php');
        $alucarrera = new AlumnoCarreraModel($this->POROTO);
        $params = $_POST["params"];
        $params["idnivel"] = $params["anio"];
        $params["idpersona"] = $this->ses->getIdPersona();
        $params["usuario"] = $this->ses->getUsuario();
        $response = array();
        try {
            $alucarrera->inscripcionarea($params);
            $this->ses->setMessage("La inscripcion al area se realizo con exito.", SessionMessageType::Success);
        } catch (PDOException $e) {
            $this->ses->setMessage("Ocurrio un error al inscribir el area. <br/>Error: " . $e->getMessage(), SessionMessageType::TransactionError);
        }
        echo json_encode($response);
    }

}
