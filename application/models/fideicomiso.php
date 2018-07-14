<?php

//Consultas sobre fideicomisos.
class ModeloFideicomiso {

    private $PDO;

    public function __construct($poroto) {
        $this->PDO = $poroto->PDO;
    }

    public function getFideicomiso($filtros) {
        //continuar
        $sql = "SELECT f.idfideicomiso,f.descripcion,fechaconstitucion,f.idestadofideicomiso,
                f.estadofideicomiso as estado,f.domicilio,f.legaldomicilio,f.idtipofideicomiso,
                f.tipofideicomiso as tipo
                FROM consagui_fideicomisos.viewfideicomiso f 
                WHERE 1=1 ";
        $params = array();
        if (isset($filtros['tipo']) && $filtros['tipo'] >0) {
            $sql .= " and f.idtipofideicomiso = :tipo";
            $params[":tipo"] =  $filtros['tipo'];
        }
        if (isset($filtros['descripcion']) && $filtros['descripcion'] != "") {
            $sql .= " and f.descripcion like :descripcion";
            $params[":descripcion"] = "%" . $filtros['descripcion'] . "%";
        }
        
        if (isset($filtros['estado']) && $filtros['estado'] >0) {
            $sql .= " and f.idestadofideicomiso = :estado";
            $params[":estado"] =  $filtros['estado'];
        }
        
        $sql .= " order by f.tipofideicomiso, f.descripcion";

        $this->PDO->execute($sql, "fideicomiso/getFideicomiso", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function nuevoFideicomiso($valores) {
        //recibe todos los datos del fideicomiso	
        $this->PDO->beginTransaction('fideicomiso/nuevoFideicomiso');
        try {
            //Agrego el fideicomiso
            $sql = "insert into fideicomiso (descripcion, fechaconstitucion, idtipofideicomiso, 
                     idestadofideicomiso, calle, numero, idlocalidad, legalcalle, legalnumero, 
                     legalidlocalidad, diaprimervencimiento, segundovencimientoliquida, 
                     segundovencimientodia, segundovencimientorecargo, tercervencimientoliquida, 
                     tercervencimientodia, tercervencimientorecargo, tasapunitoriodiario, usucrea, fechacrea)
                    values(:descripcion, :fechaconstitucion, :idtipofideicomiso,
                     1, :calle, :numero, :idlocalidad, :legalcalle, :legalnumero,
                     :legalidlocalidad, :diaprimervencimiento, :segundovencimientoliquida,
                     :segundovencimientodia, :segundovencimientorecargo, :tercervencimientoliquida,
                     :tercervencimientodia, :tercervencimientorecargo, :tasapunitoriodiario, :usucrea, :fechacrea)";
            $params = array(
                    ":descripcion" => $valores["descripcion"],
                    ":fechaconstitucion" => $valores["fechaconstitucion"],
                    ":idtipofideicomiso" => $valores["idtipofideicomiso"],
                    ":calle" => $valores["calle"],
                    ":numero" => $valores["numero"],
                    ":idlocalidad" => $valores["idlocalidad"],
                    ":legalcalle" => $valores["legalcalle"],
                    ":legalnumero" => $valores["legalnumero"],
                    ":legalidlocalidad" => $valores["legalidlocalidad"],
                    ":diaprimervencimiento" => $valores["diaprimervencimiento"],
                    ":segundovencimientoliquida" => $valores["segundovencimientoliquida"],
                    ":segundovencimientodia" => $valores["segundovencimientodia"],
                    ":segundovencimientorecargo" => $valores["segundovencimientorecargo"],
                    ":tercervencimientoliquida" => $valores["tercervencimientoliquida"],
                    ":tercervencimientodia" => $valores["tercervencimientodia"],
                    ":tercervencimientorecargo" => $valores["tercervencimientorecargo"],
                    ":tasapunitoriodiario" => $valores["tasapunitoriodiario"],                                                                                        
                    ":usucrea" => $valores["usuario"],
                    ":fechacrea" => date("Y-m-d H:i:s")
                    );
            
            $this->PDO->execute($sql, 'fideicomiso/nuevoFideicomiso', $params);
            $idFideicomiso = $this->PDO->lastInsertId();
            
            $this->PDO->commitTransaction('fideicomiso/nuevoFideicomiso');
            return array("ok" => true, "message" => "El fideicomiso se generó satisfactoriamente.", "idfideicomiso" => $idFideicomiso);
          
        } //del try
        catch (Exception $e) {
            //Rollback the transaction.
            $this->PDO->rollbackTransaction('fideicomiso/nuevoFideicomiso' . $e->getMessage());
            return array("ok" => false, "message" => "Error al generar el fideicomiso. Comuniquese con el administrador.");
        }
    }
    
    public function inactivarFideicomiso($valores) { //Revisar el return si esta bien!!!!
//Anular una operacion
        $this->PDO->beginTransaction('fideicomiso/inactivarFideicomiso');
        try {
            $sql = " update fideicomiso set ";
            $sql.= " idestadofideicomiso =:idestadofideicomiso, usumodi=:usumodi, fechamodi=:fechamodi";
            $sql.= " where idfideicomiso = :idfideicomiso";

            $params = array(
                    ":idfideicomiso" => $valores["idfideicomiso"],
                    ":idestadofideicomiso" => $valores["idestadofideicomiso"],
                    ":usumodi" => $valores["usumodi"],
                    ":fechamodi" => date("Y-m-d H:i:s")
                    );

            
            $this->PDO->execute($sql, 'fideicomiso/inactivarFideicomiso', $params);
            $this->PDO->commitTransaction('fideicomiso/nuevoFideicomiso');
            return array("ok" => true, "message" => "El fideicomiso cambió su estado satisfactoriamente.");
} //del try
        catch (Exception $e) {
            //Rollback the transaction.
            $this->PDO->rollbackTransaction('fideicomiso/inactivarFideicomiso' . $e->getMessage());
            return array("ok" => false, "message" => "Error al cambiar el estado al fideicomiso. Comuniquese con el administrador.");
        }            
    }
    
    public function modificarFideicomiso($valores){
        $this->PDO->beginTransaction('fideicomiso/modificarFideicomiso');
        try {
            $sql = "update fideicomiso set 
                    descripcion=:descripcion, fechaconstitucion=:fechaconstitucion, idtipofideicomiso=:idtipofideicomiso,idestadofideicomiso=:idestadofideicomiso,
                    calle=:calle, numero=:numero, idlocalidad=:idlocalidad, legalcalle=:legalcalle, legalnumero=:legalnumero,
                    legalidlocalidad=:legalidlocalidad, diaprimervencimiento=:diaprimervencimiento, 
                    segundovencimientoliquida=:segundovencimientoliquida, segundovencimientodia=:segundovencimientodia, 
                    segundovencimientorecargo=:segundovencimientorecargo, tercervencimientoliquida=:tercervencimientoliquida,
                    tercervencimientodia=:tercervencimientodia, tercervencimientorecargo=:tercervencimientorecargo, 
                    tasapunitoriodiario=:tasapunitoriodiario, usumodi=:usumodi, fechamodi=:fechamodi";
            $sql.= " where idfideicomiso = :idfideicomiso";

            $params = array(
                    ":idfideicomiso" => $valores["idfideicomiso"],
                    ":descripcion" => $valores["descripcion"],
                    ":fechaconstitucion" => $valores["fechaconstitucion"],
                    ":idtipofideicomiso" => $valores["idtipofideicomiso"],
                    ":idestadofideicomiso" => $valores["idestadofideicomiso"],
                    ":calle" => $valores["calle"],
                    ":numero" => $valores["numero"],
                    ":idlocalidad" => $valores["idlocalidad"],
                    ":legalcalle" => $valores["legalcalle"],
                    ":legalnumero" => $valores["legalnumero"],
                    ":legalidlocalidad" => $valores["legalidlocalidad"],
                    ":diaprimervencimiento" => $valores["diaprimervencimiento"],
                    ":segundovencimientoliquida" => $valores["segundovencimientoliquida"],
                    ":segundovencimientodia" => $valores["segundovencimientodia"],
                    ":segundovencimientorecargo" => $valores["segundovencimientorecargo"],
                    ":tercervencimientoliquida" => $valores["tercervencimientoliquida"],
                    ":tercervencimientodia" => $valores["tercervencimientodia"],
                    ":tercervencimientorecargo" => $valores["tercervencimientorecargo"],
                    ":tasapunitoriodiario" => $valores["tasapunitoriodiario"],                                                                                        
                    ":usumodi" => $valores["usuario"],
                    ":fechamodi" => date("Y-m-d H:i:s")
                    );

            $this->PDO->execute($sql, "fideicomiso/modificarFideicomiso", $params);
            $this->PDO->commitTransaction('fideicomiso/modificarFideicomiso');
            return array("ok" => true, "message" => "El fideicomiso se modificó satisfactoriamente.");
            
            
        } //del try
        catch (Exception $e) {
            //Rollback the transaction.
            $this->PDO->rollbackTransaction('fideicomiso/modificarFideicomiso' . $e->getMessage());
            return array("ok" => false, "message" => "Error al modificar el fideicomiso." . $e->getMessage());
        }        
    }
    
    public function getFideicomisoById($idfideicomiso) {
        $sql = "select 
                     a.descripcion, date_format(a.fechaconstitucion, '%d/%m/%Y') as fechaconstitucion, 
                     a.idtipofideicomiso, b.descripcion as tipofideicomiso,
                     a.idestadofideicomiso, c.descripcion as estadofideicomiso, 
                     a.calle, a.numero, a.idlocalidad, l.descripcion as localidad, 
                     m.descripcion as municipio, p.descripcion as provincia,
                     m.idmunicipio as idmunicipio,p.idprovincia as idprovincia,
                     a.legalcalle, a.legalnumero, a.legalidlocalidad, ll.descripcion as localidad, 
                     lm.idmunicipio as legalidmunicipio,lp.idprovincia as legalidprovincia,
                     lm.descripcion as municipio, lp.descripcion as provincia, a.diaprimervencimiento, 
                     a.segundovencimientoliquida, a.segundovencimientodia, a.segundovencimientorecargo, 
                     a.tercervencimientoliquida, a.tercervencimientodia, a.tercervencimientorecargo, 
                     a.tasapunitoriodiario, a.usucrea, a.fechacrea, a.usumodi, a.fechamodi
                from fideicomiso a
                inner join tipofideicomiso b on a.idtipofideicomiso=b.idtipofideicomiso 
                inner join estadofideicomiso c on a.idestadofideicomiso=c.idestadofideicomiso
                inner join localidad l on a.idlocalidad=l.idlocalidad
                inner join municipio m on m.idmunicipio=l.idmunicipio
                inner join provincia p on p.idprovincia=m.idprovincia
                inner join localidad ll on a.legalidlocalidad=ll.idlocalidad
                inner join municipio lm on lm.idmunicipio=ll.idmunicipio
                inner join provincia lp on lp.idprovincia=lm.idprovincia
                where a.idfideicomiso=:idfideicomiso";

        $params = array(":idfideicomiso" => $idfideicomiso);
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "fideicomiso/getFideicomisoById", $params);
        $result = $this->PDO->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
    
    //Hernan 20180613
    public function BORRAR_VIEJO_getUnidadFuncional($filtros) {
        //continuar
        $sql = "SELECT uf.idunidadfuncional,uf.idfideicomiso, uf.numerounidadfuncional,uf.numerolote,uf.descripcion,
		uf.idtipounidadfuncional, t.descripcion as tipo, uf.porcentajedistribucioncategoriaa, uf.porcentajedistribucioncategoriae,
		uf.m2cubiertos, uf.observaciones, uf.activo, uf.usucrea, uf.fechacrea, uf.usumodi, uf.fechamodi
                FROM consagui_fideicomisos.unidadfuncional uf 
                inner join fideicomiso f on uf.idfideicomiso=f.idfideicomiso
		inner join tipounidadfuncional t on uf.idtipounidadfuncional=t.idtipounidadfuncional
                WHERE 1=1 ";
        $params = array();
        if (isset($filtros['tipo']) && $filtros['tipo'] >0) {
            $sql .= " and uf.idtipounidadfuncional = :tipo";
            $params[":tipo"] =  $filtros['tipo'];
        }
        if (isset($filtros['descripcion']) && $filtros['descripcion'] != "") {
            $sql .= " and uf.descripcion like :descripcion";
            $params[":descripcion"] = "%" . $filtros['descripcion'] . "%";
        }
        
        if (isset($filtros['estado']) && $filtros['estado'] >= 0) {
            $sql .= " and uf.activo = :estado";
            $params[":estado"] =  $filtros['estado'];
        }

        if (isset($filtros['idfideicomiso']) && $filtros['idfideicomiso'] >= 0) {
            $sql .= " and uf.idfideicomiso = :idfideicomiso";
            $params[":idfideicomiso"] =  $filtros['idfideicomiso'];
        }
        
        $sql .= " order by uf.descripcion";

        $this->PDO->execute($sql, "fideicomiso/getUnidadFuncional", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    //Hernan 20180613
    //Lista todos los fideicomisos activos llamando al metodo getFideicomisos
    public function getFideicomisosActivos() {
        $f = array();
        $f['estado'] = 1;

        $result = $this->getFideicomiso($f);
        return $result;
    }
}
