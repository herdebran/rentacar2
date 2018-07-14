<?php

class Aplicacion {

    private $PDO;
    private $POROTO;
    private $SES;

    public function __construct($poroto) {
        $this->PROTO = $poroto;
        $this->PDO = $poroto->PDO;
        $this->SES = $poroto->Session;
    }

    public function getMenu() {
        $this->PDO->execute($this->PROTO->getMenuSqlQuery(), 'Aplicacion/getMenu');
        return $this->PDO->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllPersonas() {
        $sql = "SELECT idpersona, nombre, legajo, documentonro, fechanac,  direccion FROM personas";
        $this->PDO->execute($sql, 'Aplicacion/getAllCarreras');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function getAllProvincias() {
        $sql = "SELECT idprovincia, descripcion FROM provincia";
        $this->PDO->execute($sql, 'Aplicacion/getAllProvincias');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function ajaxAllMunicipios($idprovincia) {
        $sql = " select idmunicipio, descripcion";
        $sql.= " from municipio";
        $sql.= " where idprovincia=:idprovincia";
        $sql.= " order by descripcion";
        $params = array(":idprovincia"=>$idprovincia);
        $this->PDO->execute($sql, 'Aplicacion/ajaxAllMunicipios',$params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;        
    }
    
    
    public function ajaxAllLocalidades($idmunicipio) {
            $sql = " select idlocalidad, descripcion, codigopostal";
            $sql.= " from localidad";
            $sql.= " where idmunicipio=:idmunicipio";
            $sql.= " order by descripcion";
            $params = array(":idmunicipio"=>$idmunicipio);
            $this->PDO->execute($sql, 'Aplicacion/ajaxAllLocalidades',$params);
            $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
            return $result;        
    }
    
//    Hernan: creado 02/06/2018 para obtener un fideicomiso por id    
    public function getFideicomisoPorId($idfideicomiso) {
        $sql = "select * from fideicomiso where idfideicomiso=:idfideicomiso";
        $params = array(":idfideicomiso"=>$idfideicomiso);
        $this->PDO->execute($sql, 'Aplicacion/getFideicomisoPorId', $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);

        return $result;



        return $result;
    }



    public function getAllTipoDocumento() {
        $sql = "SELECT id,descripcion FROM tipodoc order by id";
        $this->PDO->execute($sql, 'Aplicacion/getAllCarreras');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getAllCarreras() {
        $sql = "select idcarrera, nombre, descripcion from carreras where estado=1 order by 2";
        $this->PDO->execute($sql, 'Aplicacion/getAllCarreras');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getAllInstrumentos() {
        $sql = "select * from instrumentos";
        $this->PDO->execute($sql, 'Aplicacion/getAllInstrumentos');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * 
     * @return type
     */
    public function getAllRoles() {
        $sql = "select * from roles order by nombre";
        $this->PDO->execute($sql, 'Aplicacion/getAllRoles');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    /**
     * 
     * @return type
     */
    public function getAllPermisos() {
        $sql = "select * from permisos order by nombre";
        $this->PDO->execute($sql, 'Aplicacion/getAllPermisos');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getAllConfiguraciones() {
        $sql = "select parametro,valor from configuracion order by orden";
        $this->PDO->execute($sql, 'Aplicacion/getAllConfiguraciones');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getConfiguracionByParametro($parametro) {
        $sql = "select parametro,valor from configuracion where parametro = :parametro";
        $this->PDO->execute($sql, 'Aplicacion/getConfiguracionByParametro', array(":parametro" => $parametro));
        $result = $this->PDO->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function changeConfigurationValue($array) {
        $sql = "UPDATE configuracion"
                . " SET valor = :valor, usumodi= :usumodi, fechamodi= CURRENT_TIMESTAMP"
                . " where parametro = :parametro";
        $params = array(":valor" => $array["valor"], ":parametro" => $array["parametro"], ":usumodi" => $this->SES->getUsuario() );
        $this->PDO->execute($sql, 'Aplicacion/changeConfigurationValue', $params);
    }

    public function getAllTipoFideicomiso() {
        $sql = "select idtipofideicomiso, descripcion from tipofideicomiso order by descripcion";
        $this->PDO->execute($sql, 'Aplicacion/getAllTipoFideicomiso');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    public function getAllEstadoFideicomiso() {
        $sql = "select idestadofideicomiso, descripcion from estadofideicomiso order by descripcion";
        $this->PDO->execute($sql, 'Aplicacion/getAllEstadoFideicomiso');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    //Hernan 20180612
    public function getAllTipoUnidadFuncional() {
        $sql = "select idtipounidadfuncional, descripcion from tipounidadfuncional order by descripcion";
        $this->PDO->execute($sql, 'Aplicacion/getAllTipoUnidadFuncional');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
}
