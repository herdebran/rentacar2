<?php

class Novedades {

    private $POROTO;
    private $PDO;
    private $db;
    private $ses;
    private $lib;

    function __construct($poroto) {
        $this->POROTO = $poroto;
        $this->POROTO->pageHeader[] = array("label" => "Dashboard", "url" => "");
        $this->PDO = $this->POROTO->PDO->getPdo();
        $this->db = & $this->POROTO->DB;
        $this->ses = & $this->POROTO->Session;
        $this->lib = & $this->POROTO->Libraries['siteLibrary'];
    }

    function defentry() {
        if ($this->POROTO->Session->isLogged()) {
            $this->index();
        } else {
            include($this->POROTO->ViewPath . "/-login.php");
        }
    }

    function index() {
        $this->db->dbConnect("home/menu");
        $arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());
        $saludo = $this->load_saludo($this->ses->getIdPersona());
        $roles = $this->load_roles();
        $editor = $this->ses->tienePermiso('', 'Gestión de Novedades');
        $novedades = $this->load_novedades($this->ses->getIdRole());
        // Alerta para alumno-carrera
        include($this->POROTO->ModelPath . '/alumnocarrera.php');
        $alucarrera = new AlumnoCarreraModel($this->POROTO);
        $fobas = $alucarrera->getFobasCompletas($this->ses->getIdPersona());
        $avanzadas = $alucarrera->getAvanzadas2anio($this->ses->getIdPersona());
        // Fin Alerta para alumno-carrera

        include($this->POROTO->ViewPath . "/-header.php");
        include($this->POROTO->ViewPath . "/novedades.php");
        include($this->POROTO->ViewPath . "/-footer.php");
    }

    private function load_saludo($idpersona) {

        $sql = "SELECT apellido,nombre FROM personas WHERE idpersona=:idpersona";
        $query = $this->PDO->prepare($sql);
        $params = array(':idpersona' => $idpersona);
        $query->execute($params);
        $arrSaludo = $query->fetch(PDO::FETCH_ASSOC);

        $saludo = "Bienvenido/a";
        if (count($arrSaludo) == 1) {
            $saludo .= " " . $arrSaludo[0]['nombre'] . " " . $arrSaludo[0]['apellido'] . ".";
        }
        return $saludo;
    }

    // Carga todas las novedades disponibles segun el rol del usuario (si es ADMINISTRATIVO carga todas)
    private function load_novedades($idrol) {
        $sql = "SELECT idnovedad, idrol, titulo, contenido, usucrea, fechacrea 
                FROM novedadesdestinatario nd 
                inner join  novedades n on (n.id = nd.idnovedad)";
        if ($this->ses->tienePermiso('', 'Gestión de Novedades')) {
            $sql .= "GROUP BY idnovedad ORDER BY fechacrea DESC";
            $query = $this->PDO->prepare($sql);
            $query->execute();
        } else {
            $sql .= " where nd.idrol = :rol GROUP BY idnovedad ORDER BY fechacrea DESC";
            $query = $this->PDO->prepare($sql);
            $params = array(':rol' => $idrol);
            $query->execute($params);
        }
        $result = $query->fetchAll(PDO::FETCH_ASSOC);
        $novedades = array();
        foreach ($result as $nov) {
            $destinatarios = $this->load_destinatarios_by_novedad($nov['idnovedad']);
            $nov['destinatarios'] = $destinatarios;
            $novedades[] = $nov;
        }
        return $novedades;
    }

    private function load_destinatarios_by_novedad($idnovedad) {
        $query = $this->PDO->prepare("SELECT r.idrol, r.nombre 
                                FROM roles r 
                                inner join novedadesdestinatario nd on (r.idrol = nd.idrol)
                                where nd.idnovedad = :idnovedad");
        $params = array(':idnovedad' => $idnovedad);
        $query->execute($params);
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // Retorna todos los roles menos el ADMINISTRATIVO (este lee siempre, no hace falta seleccionarlo y no puede evitarse que lea todas)
    private function load_roles() {
        $query = $this->PDO->prepare("SELECT * FROM roles");
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save() {
        if (filter_input(INPUT_POST, 'idnovedad') != 0) {
            $this->edit();
        } else {
            $this->PDO->beginTransaction();

            $sql = "INSERT INTO novedades ( titulo, contenido, usucrea) 
                VALUES (:titulo, :contenido, :usucrea)";
            $query = $this->PDO->prepare($sql);
            $params = array(':titulo' => filter_input(INPUT_POST, 'titulo'),
                ':contenido' => filter_input(INPUT_POST, 'contenido'),
                ':usucrea' => $this->ses->getUsuario());
            $consistencia = $query->execute($params);
            $idnovedad = $this->PDO->lastInsertId();
            $destinatarios = $_POST['destinatarios'];

            $i = 0;
            $size = sizeof($destinatarios);
            while (($i < $size) && ($consistencia)) {
                $sql = "INSERT INTO novedadesdestinatario ( idnovedad, idrol) 
                VALUES (:idnovedad, :idrol)";
                $query = $this->PDO->prepare($sql);
                $params = array(':idnovedad' => $idnovedad,
                    ':idrol' => $destinatarios[$i]);
                $consistencia = $query->execute($params);
                $i++;
            }
            if ($consistencia) {
                $this->PDO->commit();
            } else {
                $this->PDO->rollback();
            }
            header("Location: /", TRUE, 302);
        }
    }

    public function edit() {
        $idnovedad = filter_input(INPUT_POST, 'idnovedad');
        $consistencia = true;
        $this->PDO->beginTransaction();

        $sql = 'update novedades
                set titulo = :titulo, contenido = :contenido,
                usumodif = :usumodif, fechamodif = :fechamodif
                where id = :idnovedad';
        $query = $this->PDO->prepare($sql);
        $params = array(':titulo' => filter_input(INPUT_POST, 'titulo'),
            ':contenido' => filter_input(INPUT_POST, 'contenido'),
            ':usumodif' => $this->ses->getUsuario(),
            ':fechamodif' => date('Y-m-d H:m:s'),
            ':idnovedad' => $idnovedad);
        $consistencia = $query->execute($params);
        if ($consistencia) {
            $sql = "delete from novedadesdestinatario where idnovedad = :idnovedad";
            $query = $this->PDO->prepare($sql);
            $params = array(":idnovedad" => $idnovedad);
            $consistencia = $query->execute($params);
            if ($consistencia) {
                $destinatarios = $_POST['destinatarios'];
                $i = 0;
                $size = sizeof($destinatarios);
                while (($i < $size) && ($consistencia)) {
                    $sql = "INSERT INTO novedadesdestinatario ( idnovedad, idrol) 
                            VALUES (:idnovedad, :idrol)";
                    $query = $this->PDO->prepare($sql);
                    $params = array(':idnovedad' => $idnovedad,
                        ':idrol' => $destinatarios[$i]);
                    $consistencia = $query->execute($params);
                    $i++;
                }
            }
        }

        if ($consistencia) {
            $this->PDO->commit();
        } else {
            $this->PDO->rollback();
        }
        header("Location: /", TRUE, 302);
    }

    public function delete() {
        $sql = "Delete from novedades where id = :idnovedad";
        $query = $this->PDO->prepare($sql);
        $params = array(':idnovedad' => filter_input(INPUT_POST, 'idnovedad'));
        if ($query->execute($params)) {
            echo json_encode(["status" => "ok", "message" => "la novedad fue eliminada"]);
        } else {
            echo json_encode(["status" => "ok", "message" => "error al eliminar"]);
        }
    }

}

?>