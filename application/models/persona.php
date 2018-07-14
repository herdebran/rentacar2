<?php

class Persona
{

    private $PDO;

    public function __construct($poroto)
    {
        $this->PDO = $poroto->PDO;
    }

    /**
     * 
     * @param type $idpersona
     * @return type
     */
    public function getPersonaById($idpersona)
    {
        $sql = "select distinct p.idpersona, p.email,p.tipodocumento tdoc, p.documentonro,
		concat(p.apellido,' ',p.nombre) as apeynom,
        concat(p.tipodocumento,' ',p.documentonro) as tipodocynro,
		r.nombre as rol,
		p.email
        from 
        viewpersona p 
		left join personarol pr on p.idpersona=pr.idpersona
		left join roles r on pr.idrol=r.idrol
        where p.idpersona=:idpersona";
        $params = array(":idpersona" => $idpersona);
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "persona/getPersonaById", $params);
        $result = $this->PDO->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /*
    public function getPersonasCooperadora($filtros)
    {
        //continuar

        $sql = "select distinct p.idpersona,t.descripcion tdoc, p.documentonro,
		concat(p.apellido,' ',p.nombre) as apeynom,
                concat(t.descripcion,' ',p.documentonro) as tipodocynro,
		ea.descripcion estalu,
		'' as rol,
		ce.descripcion as estadocooperadora,
		cp.fechaultimopago,
		ifnull(c.nombre,'') carreranombre,
		u.email
        from 
        personas p 
	inner join personarol pr on p.idpersona=pr.idpersona
	inner join roles r on pr.idrol=r.idrol
        inner join usuarios u on u.idpersona=p.idpersona
        inner join tipodoc t on t.id=p.tipodoc
	left join alumnos a on p.idpersona=a.idpersona
        left join estadoalumno ea on a.estadoalumno_id=ea.id
        left join alumnocarrera ac on ac.idpersona=p.idpersona and ac.estado in (1,3)
        left join carreras c on ac.idcarrera=c.idcarrera
	left join alumnomateria am on p.idpersona=am.idpersona
	left join cooperadorapersona cp on p.idpersona=cp.idpersona
	left join cooperadoraestados ce on cp.idestado=ce.id
        where p.estado=1";
        $params = array();
        if (isset($filtros['apellido']) && $filtros['apellido'] != "") {
            $sql .= " and p.apellido like :apellido";
            $params[":apellido"] = "%" . $filtros['apellido'] . "%";
        }
        if (isset($filtros['nombre']) && $filtros['nombre'] != "") {
            $sql .= " and p.nombre like :nombre";
            $params[":nombre"] = "%" . $filtros['nombre'] . "%";
        }
        if (isset($filtros['tipdoc']) && $filtros['tipdoc'] != "" && $filtros['tipdoc'] != "0") {
            $sql .= " and t.id=:tipdoc";
            $params[":tipdoc"] = $filtros['tipdoc'];
        }
        if (isset($filtros['nrodoc']) && $filtros['nrodoc'] != "") {
            $sql .= " and p.documentonro = :nrodoc";
            $params[":nrodoc"] = $filtros['nrodoc'];
        }
        if (isset($filtros['carrera']) && $filtros['carrera'] != "" && $filtros['carrera'] != "0") {
            $sql .= " and c.idcarrera=:carrera";
            $params[":carrera"] = $filtros['carrera'];
        }
        if (isset($filtros['materia']) && $filtros['materia'] != "" && $filtros['materia'] != "0") {
            $sql .= " and am.idmateria=:materia";
            $params[":materia"] = $filtros['materia'];
        }
        if (isset($filtros['instrumento']) && $filtros['instrumento'] != "" && $filtros['instrumento'] != "0") {
            $sql .= " and ac.idinstrumento=:instrumento";
            $params[":instrumento"] = $filtros['instrumento'];
        }
        if (isset($filtros['rol']) && $filtros['rol'] != "" && $filtros['rol'] != "0") {
            $sql .= " and pr.idrol=:rol";
            $params[":rol"] = $filtros['rol'];
        }
        if (isset($filtros['estadocoop']) && $filtros['estadocoop'] != "" && $filtros['estadocoop'] != "0") {
            $sql .= " and cp.idestado=:estadocoop";
            $params[":estadocoop"] = $filtros['estadocoop'];
        }
        if (isset($filtros['mespago']) && $filtros['mespago'] != "" && $filtros['mespago'] != "0") { //Si ingreso un mes de pago.
            if (isset($filtros['pago']) && $filtros['pago'] == "SI") { //Traer si pago la cuota seleccionada
                $sql .= " and exists (select o.idpersona from operaciones o inner join operaciondetalle od ";
                $sql .= " on o.idoperacion=od.idoperacion where od.idcuota=:mespago ";
                $sql .= " and o.idpersona=p.idpersona and o.anulada=0)";
            } else { //Traer si no pago la cuota seleccionada
                $sql .= " and not exists (select o.idpersona from operaciones o inner join operaciondetalle od ";
                $sql .= " on o.idoperacion=od.idoperacion where od.idcuota=:mespago ";
                $sql .= " and o.idpersona=p.idpersona and o.anulada=0)";
            }
            $params[":mespago"] = $filtros['mespago'];
        }

        $sql .= " order by apellido, p.nombre";

        $this->PDO->execute($sql, "Persona/getPersonasCooperadora", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
*/
    public function getAllWithFilter($filtros)
    { //NO USAR!!!!
        $sql = "select p.idpersona,
                concat(p.apellido,' ',p.nombre) as apeynom,
                t.descripcion tdoc,p.documentonro,
                ifnull(date_format(max(ua.fecha), '%d/%m/%Y %H:%i:%s'),'n/a') lastlogin
            from alumnos a 
            inner join personas p on p.idpersona=a.idpersona
            inner join usuarios u on u.idpersona=p.idpersona
            inner join tipodocumento t on t.idtipodocumento=p.tipodoc
            left join usuarioaccesos ua on ua.idpersona=p.idpersona
            where p.estado=1";
        $params = array();
        if (isset($filtros['apellido']) && $filtros['apellido'] != "") {
            $sql .= " and p.apellido like :apellido";
            $params[":apellido"] = "%" . $filtros['apellido'] . "%";
        }
        if (isset($filtros['nombre']) && $filtros['nombre'] != "") {
            $sql .= " and p.nombre like :nombre";
            $params[":nombre"] = "%" . $filtros['nombre'] . "%";
        }
        if (isset($filtros['tipdoc']) && $filtros['tipdoc'] != "" && $filtros['tipdoc'] != "0") {
            $sql .= " and t.id=:tipdoc";
            $params[":tipdoc"] = $filtros['tipdoc'];
        }
        if (isset($filtros['nrodoc']) && $filtros['nrodoc'] != "") {
            $sql .= " and p.documentonro like :nrodoc";
            $params[":nrodoc"] = "%" . $filtros['nrodoc'] . "%";
        }

        $sql .= " group by p.idpersona";
        $sql .= " order by 1, 2";
        //$sql .= " limit 3";
        //$sql .= " limit " . (($page - 1) * $this->POROTO->Config['records_per_page']) . "," . $this->POROTO->Config['records_per_page'];
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "persona/getAllWithFilter", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function nuevaPersona($valores) {
        //recibe todos los datos del fideicomiso	
        $this->PDO->beginTransaction('persona/nuevaPersona');
        try {
            //Agrego la persona
            $sql = "insert into personas (legajo, razonsocial, apellido, nombre, tipodoc,
                    documentonro, cargoenempresa, iddomicilio, iddomiciliofacturacion, telefono1, telefono2,
                    telefono3, vendedorporcganancia, email, web, idtipocondicioniva, informacionpago, idtipofactura,
                    facturanombre, observaciones, estado, usucrea, fechacrea)
                    values(:legajo, :razonsocial, :apellido, :nombre, :tipodoc,
                    :documentonro, :cargoenempresa, null, null, :telefono1, :telefono2,
                    :telefono3, :vendedorporcganancia, :email, :web, :idtipocondicioniva, :informacionpago, :idtipofactura,
                    :facturanombre, :observaciones, 1, :usucrea, :fechacrea)";
            $params = array(
                    ":legajo" => $valores["legajo"],
                    ":razonsocial" => $valores["razonsocial"],
                    ":apellido" => $valores["apellido"],
                    ":nombre" => $valores["nombre"],
                    ":numero" => $valores["numero"],
                    ":tipodoc" => $valores["tipodoc"],
                    ":documentonro" => $valores["documentonro"],
                    ":cargoenempresa" => $valores["cargoenempresa"],
                    ":telefono1" => $valores["telefono1"],
                    ":telefono2" => $valores["telefono2"],
                    ":telefono3" => $valores["telefono3"],
                    ":vendedorporcganancia" => $valores["vendedorporcganancia"],
                    ":email" => $valores["email"],
                    ":web" => $valores["web"],
                    ":idtipocondicioniva" => $valores["idtipocondicioniva"],
                    ":informacionpago" => $valores["informacionpago"],
                    ":idtipofactura" => $valores["idtipofactura"],
                    ":facturanombre" => $valores["facturanombre"],                                                                                        
                    ":observaciones" => $valores["observaciones"],   
                    ":usucrea" => $valores["usuario"],
                    ":fechacrea" => date("Y-m-d H:i:s")
                    );
            
            $this->PDO->execute($sql, 'persona/nuevaPersona', $params);
            $idpersona = $this->PDO->lastInsertId();

            $sql = "insert into domicilio (calle, nro, piso, depto, idlocalidad,
            cp, usucrea, fechacrea)
            values(:calle, :nro, :piso, :depto, :idlocalidad,
            :cp, :usucrea, :fechacrea)";
    $params = array(
            ":calle" => $valores["dcalle"],
            ":nro" => $valores["dnro"],
            ":piso" => $valores["dpiso"],
            ":depto" => $valores["ddepto"],
            ":numero" => $valores["dnumero"],
            ":cp" => $valores["dcp"],
            ":usucrea" => $valores["usuario"],
            ":fechacrea" => date("Y-m-d H:i:s")
            );

            $this->PDO->execute($sql, 'persona/nuevaPersona', $params);
            $iddomicilio = $this->PDO->lastInsertId();

            $sql = "insert into domicilio (calle, nro, piso, depto, idlocalidad,
            cp, usucrea, fechacrea)
            values(:calle, :nro, :piso, :depto, :idlocalidad,
            :cp, :usucrea, :fechacrea)";
    $params = array(
            ":calle" => $valores["dfcalle"],
            ":nro" => $valores["dfnro"],
            ":piso" => $valores["dfpiso"],
            ":depto" => $valores["dfdepto"],
            ":numero" => $valores["dfnumero"],
            ":cp" => $valores["dfcp"],
            ":usucrea" => $valores["usuario"],
            ":fechacrea" => date("Y-m-d H:i:s")
            );

            $this->PDO->execute($sql, 'persona/nuevaPersona', $params);
            $iddomiciliofacturacion = $this->PDO->lastInsertId();

            $sql = "update personas set 
                    iddomicilio=:iddomicilio, iddomiciliofacturacion=:iddomiciliofacturacion";
            $sql.= " where idpersona = :idpersona";

            $params = array(
                ":idpersona" => $idpersona,
                ":iddomicilio" => $iddomicilio,
                ":iddomiciliofacturacion" => $iddomiciliofacturacion);
            
            $this->PDO->execute($sql, 'persona/nuevaPersona', $params); 
            
            $this->PDO->commitTransaction('persona/nuevaPersona');
            return array("ok" => true, "message" => "La entidad se generó satisfactoriamente.", "identidad" => $idpersona);
          
        } //del try
        catch (Exception $e) {
            //Rollback the transaction.
            $this->PDO->rollbackTransaction('persona/nuevaPersona' . $e->getMessage());
            return array("ok" => false, "message" => "Error al generar la entidad. Comuniquese con el administrador.");
        }
    }

    public function modificarPersona($valores){
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

    public function modificarestadoPersona($valores) { //Revisar el return si esta bien!!!!
        //Anular una operacion
                $this->PDO->beginTransaction('persona/modificarestadoPersona');
                try {
                    $sql = " update persona set ";
                    $sql.= " motivo =:motivo, estado=:estado, usumodi=:usumodi, fechamodi=:fechamodi";
                    $sql.= " where idpersona = :idpersona";
        
                    $params = array(
                            ":idpersona" => $valores["idpersona"],
                            ":estado" => $valores["estado"],
                            ":usumodi" => $valores["usumodi"],
                            ":fechamodi" => date("Y-m-d H:i:s")
                            );
        
                    
                    $this->PDO->execute($sql, 'persona/modificarestadoPersona', $params);
                    $this->PDO->commitTransaction('persona/modificarestadoPersona');
                    return array("ok" => true, "message" => "La entidad cambió su estado satisfactoriamente.");
        } //del try
                catch (Exception $e) {
                    //Rollback the transaction.
                    $this->PDO->rollbackTransaction('persona/modificarestadoPersona' . $e->getMessage());
                    return array("ok" => false, "message" => "Error al cambiar el estado al fideicomiso. Comuniquese con el administrador.");
                }            
    }
}
