<?php

//Consultas sobre fideicomisos.
class ModeloEntidad {

    private $PDO;

    public function __construct($poroto) {
        $this->PDO = $poroto->PDO;
    }

    public function getEntidad($filtros) {
        //continuar
        $sql = "SELECT a.identidad, a.idtipoentidad, b.descripcion as tipoentidad, a.razonsocial, 
                    a.apellido, a.nombre, a.cargoenempresa, a.idtipodocumento, c.descripcion as tipodocumento, 
                    a.nrodocumento, a.iddomicilio, a.telefono1, a.telefono2, a.telefono3, a.email, a.web, 
                    a.idtipocondicioniva, e.descripcion as tipocondicioniva, a.informacionpago, a.idtipofactura, 
                    f.descripcion as tipofactura, a.facturanombre, facturadomicilio, a.facturalocalidad, 
                    a.observaciones, a.vendedorporcganancia, a.activo, a.motivobaja, d.calle, d.nro, d.piso, d.depto, 
                    d.idlocalidad, l.descripcion as localidad, m.idmunicipio as idmunicipio,
                    m.descripcion as municipio, p.descripcion as provincia, p.idprovincia as idprovincia,
                    a.usucrea, a.fechacrea, a.usumodi, a.fechamodi
                FROM entidad a
                INNER JOIN tipoentidad b on a.idtipoentidad = b.idtipoentidad
                LEFT JOIN tipodocumento c on a.idtipodocumento = c.idtipodocumento
                LEFT JOIN tipocondicioniva e on a.idtipocondicioniva = e.idtipocondicioniva
                LEFT JOIN tipofactura f on a.idtipofactura = f.idtipofactura
                LEFT JOIN domicilio d on a.iddomicilio = d.iddomicilio;
                inner join localidad l on d.idlocalidad=l.idlocalidad
                inner join municipio m on m.idmunicipio=l.idmunicipio
                inner join provincia p on p.idprovincia=m.idprovincia
                WHERE 1=1 ";
        $params = array();
        if (isset($filtros['idtipoentidad']) && $filtros['idtipoentidad'] >0) {
            $sql .= " and a.idtipoentidad = :tipoentidad";
            $params[":tipoentidad"] =  $filtros['tipoentidad'];
        }
        if (isset($filtros['razonsocial']) && $filtros['razonsocial'] != "") {
            $sql .= " and a.razonsocial like :razonsocial";
            $params[":razonsocial"] = "%" . $filtros['razonsocial'] . "%";
        }
        
        if (isset($filtros['idtipodocumento']) && $filtros['idtipodocumento'] >0) {
            $sql .= " and a.idtipodocumento = :tipodocumento";
            $params[":tipodocumento"] =  $filtros['tipodocumento'];
        }
        
        if (isset($filtros['nrodocumento']) && $filtros['nrodocumento'] != "") {
            $sql .= " and a.nrodocumento like :nrodocumento";
            $params[":nrodocumento"] = "%" . $filtros['nrodocumento'] . "%";
        }
        
        if (isset($filtros['activo'])) {
            $sql .= " and f.activo = :activo";
            $params[":activo"] =  $filtros['activo'];
        }
        
        $sql .= " order by b.descripcion, a.razonsocial, a.apellido";

        $this->PDO->execute($sql, "entidad/getEntidad", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function nuevaEntidad($valores) {
        //recibe todos los datos del fideicomiso	
        $this->PDO->beginTransaction('fideicomiso/nuevoFideicomiso');
        try {
            //Agrego el fideicomiso
            $sql = "insert into fideicomiso (identidad, idtipoentidad, razonsocial, apellido, nombre, cargoenempresa, idtipodocumento, nrodocumento,
                    telefono1, telefono2, telefono3, email, web, idtipocondicioniva, informacionpago, idtipofactura,
                    facturanombre, facturadomicilio, facturalocalidad, observaciones, vendedorporcganancia, activo, motivobaja, usucrea,
                    fechacrea)
                    values(:idtipoentidad, :descripcion as tipoentidad, razonsocial, apellido, nombre, cargoenempresa, idtipodocumento, c.descripcion as tipodocumento, nrodocumento, iddomicilio, 
                    :telefono1, telefono2, telefono3, email, web, idtipocondicioniva, informacionpago, idtipofactura,
                    :facturanombre, :facturadomicilio, :facturalocalidad, :observaciones, :vendedorporcganancia, 1, :motivobaja, usucrea,
                    :fechacrea)";
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
    
    public function inactivarEntidad($valores) { //Revisar el return si esta bien!!!!
//Anular una operacion
        $this->PDO->beginTransaction('entidad/inactivarEntidad');
        try {
            $sql = " update entidad set ";
            $sql.= " motivo =:motivo, activo=:activo, usumodi=:usumodi, fechamodi=:fechamodi";
            $sql.= " where identidad = :identidad";

            $params = array(
                    ":identidad" => $valores["identidad"],
                    ":motivo" => $valores["motivo"],
                    ":activo" => $valores["activo"],
                    ":usumodi" => $valores["usumodi"],
                    ":fechamodi" => date("Y-m-d H:i:s")
                    );

            
            $this->PDO->execute($sql, 'entidad/inactivarEntidad', $params);
            $this->PDO->commitTransaction('entidad/inactivarEntidad');
            return array("ok" => true, "message" => "La entidad cambió su estado satisfactoriamente.");
} //del try
        catch (Exception $e) {
            //Rollback the transaction.
            $this->PDO->rollbackTransaction('fideicomiso/inactivarFideicomiso' . $e->getMessage());
            return array("ok" => false, "message" => "Error al cambiar el estado al fideicomiso. Comuniquese con el administrador.");
        }            
    }
    
    public function modificarFideicomiso($valores){
        $this->PDO->beginTransaction('fideicomiso/nuevoFideicomiso');
        try {
            $sql = "update fideicomiso set 
                    descripcion=:descripcion, fechaconstitucion=:fechaconstitucion, idtipofideicomiso=:idtipofideicomiso,
                    calle=:calle, numero=:numero, idlocalidad=:idlocalidad, legalcalle=:legalcalle, legalnumero=:legalnumero,
                    legalidlocalidad=:legalidlocalidad, diaprimervencimiento=:diaprimervencimiento, 
                    segundovencimientoliquida=:segundovencimientoliquida, segundovencimientodia=:segundovencimientodia, 
                    segundovencimientorecargo=:segundovencimientorecargo, tercervencimientoliquida=:tercervencimientoliquida,
                    tercervencimientodia=:tercervencimientodia, tercervencimientorecargo=:tercervencimientorecargo, 
                    tasapunitoriodiario=:tasapunitoriodiario, usumodi=:usumodi, fechamodi=:fechamodi)";
            $sql.= " where idfideicomiso = :idfideicomiso";

            $params = array(
                    ":descripcion" => $valores["descripcion"],
                    ":fechaconstitucion" => $valores["fechaconstitucion"],
                    ":idtipofideicomiso" => $valores["idtipofideicomiso"],
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
            return array("ok" => false, "message" => "Error al modificar el fideicomiso. Comuniquese con el administrador.");
        }        
    }
    
    public function getFideicomisoById($idfideicomiso) {
        $sql = "select 
                     a.descripcion, a.fechaconstitucion, 
                     a.idtipofideicomiso, b.descripcion as tipofideicomiso,
                     a.idestadofideicomiso, e.descripcion as estadofideicomiso, 
                     a.calle, a.numero, a.idlocalidad, l.descripcion as localidad, 
                     m.descripcion as municipio, p.descripcion as provincia,
                     a.legalcalle, a.legalnumero, a.legalidlocalidad, ll.descripcion as localidad, 
                     lm.descripcion as municipio, lp.descripcion as provincia, a.diaprimervencimiento, 
                     a.segundovencimientoliquida, a.segundovencimientodia, a.segundovencimientorecargo, 
                     a.tercervencimientoliquida, a.tercervencimientodia, a.tercervencimientorecargo, 
                     a.tasapunitoriodiario, a.usucrea, a.fechacrea
                from fideicomiso a
                inner join tipofideicomiso b on a.idtipofideicomiso=b.idtipofideicomiso 
                inner join estadofideicomiso c on a.idestadofideicomiso=c.idestadofideicomiso
                inner join localidad l on a.idlocalidad=l.idlocalidad
                inner join municipio m on m.idmunicipio=l.idmunicipio
                inner join provincia p on p.idprovincia=m.idprovincia
                inner join localidad ll on a.idlegallocalidad=ll.idlocalidad
                inner join municipio lm on lm.idmunicipio=ll.idmunicipio
                inner join provincia lp on lp.idprovincia=lm.idprovincia
                where p.idfideicomiso=:idfideicomiso";

        $params = array(":idfideicomiso" => $idfideicomiso);
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "fideicomiso/getFideicomisoById", $params);
        $result = $this->PDO->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
}
