<?php

//Consultas sobre personas, aca podemos agregar la consulta que viene de busqueda cooperadora.
class ModeloCooperadora {

    private $PDO;

    public function __construct($poroto) {
        $this->PDO = $poroto->PDO;
    }

    public function getOpeById($opeID) {
        //Traer las ultimas 100 operaciones pagas de la persona
        $sql = "select idoperacion, idpersona, fecha,
            (select group_concat(cuota order by idcuota asc  separator ' , ') from operaciondetalle od where od.idoperacion=o.idoperacion ) as cuotas,  o.montototal 
            from operaciones o 
            where o.idoperacion = :idoperacion and o.anulada = 0 
            order by fecha desc";
        $params = array(":idoperacion"=>$opeID);
        $this->PDO->execute($sql, "Cooperadora/getOperacionesPersona", $params);
        $result = $this->PDO->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getOperacionesPersona($filtros) {
        //Traer las ultimas 100 operaciones pagas de la persona
        $sql = "select idoperacion, fecha,
            (select group_concat(cuota order by idcuota asc  separator ' | ') from operaciondetalle od where od.idoperacion=o.idoperacion ) as cuotas,  o.montototal 
            from operaciones o 
            where o.idpersona = :idpersona and o.anulada = 0 
            order by fecha desc limit 100";
        $params = array();
        if ($filtros['idpersona'] != "") {
            $params[":idpersona"] = $filtros['idpersona'];
        }

        $this->PDO->execute($sql, "Cooperadora/getOperacionesPersona", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getOperaciones($filtros) {
        //Trae todas las operaciones filtrando por fecha
        $sql = "select right(concat('00000000',idoperacion),8) as idoperacion, fecha,p.apellido,p.nombre,concat(td.descripcion,' ',p.documentonro) as documento,
            (select group_concat(cuota order by idcuota asc  separator ' | ') from operaciondetalle od where od.idoperacion=o.idoperacion ) as cuotas,  o.montototal,
            concat(p2.apellido,' ',p2.nombre) as cobrador
            from operaciones o inner join personas p on o.idpersona=p.idpersona
            left join tipodoc td on p.tipodoc=td.id
            inner join usuarios u on o.usucrea=u.usuario
            inner join personas p2 on u.idpersona=p2.idpersona
            where o.anulada = 0 and fecha between :fechadesde and :fechahasta
            order by fecha desc";
        $params = array();
        if ($filtros['fechadesde'] != "") {
            $params[":fechadesde"] = $filtros['fechadesde'];
        }
        if ($filtros['fechahasta'] != "") {
            $params[":fechahasta"] = $filtros['fechahasta'];
        }

        $this->PDO->execute($sql, "Cooperadora/getOperaciones", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }
    
    public function getCooperadoraCuotasAño($filtros) {
//Traer las cuotas del año solicitado para la persona e indicando si esta paga o no. (filtros de año y id persona)
        $sql = " select cp.id as idcuota,cp.año,cp.mes,cp.cuota,od.monto,op.fecha as fechapago,
            (case when od.idoperaciondetalle is null then false else true end) as pago 
            from operaciondetalle od
            INNER JOIN operaciones op on (od.idoperacion = op.idoperacion and op.idpersona = :idpersona and op.anulada = 0)
            RIGHT join cooperadoracuotas cp on (od.idcuota = cp.id)
            where cp.año = :anio 
            order by cp.año, mes";

        $params = array();
        if ($filtros['anio'] != "") {
            $params[":anio"] = $filtros['anio'];
        }

        if ($filtros['idpersona'] != "") {
            $params[":idpersona"] = $filtros['idpersona'];
        }

        $this->PDO->execute($sql, "Cooperadora/getCooperadoraCuotasAño", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

// FIXME Devolver el estado en cooperadora.
    public function getCooperadoraPersonaDatos($filtros) {
//obtiene los datos principales de cooperadora para la persona.
        $sql = " select p.apellido, p.nombre, concat(td.descripcion, ' ', p.documentonro) as documento, cp.idestado, ";
        $sql .= " ce.descripcion as estadocooperadora ";
        $sql .= " from personas p left join cooperadorapersona cp on p.idpersona = cp.idpersona ";
        $sql .= " inner join tipodoc td on p.tipodoc = td.id ";
        $sql .= " left join cooperadoraestados ce on cp.idestado = ce.id";
        $sql .= " where p.idpersona = :idpersona ";
        $params = array();
        if ($filtros['idpersona'] != "") {
            $params[":idpersona"] = $filtros['idpersona'];
        }
        $this->PDO->execute($sql, "Cooperadora/getCooperadoraPersonaDatos", $params);
        $result = $this->PDO->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    public function nuevaOperacion($valores) {
        //VER si retorno para arriba el error para mostrarlo en pantalla.
        //recibe el año, idpersona, tildes con las cuotas a pagar (array), monto total de la operacion, fecha.		
        $this->PDO->beginTransaction('cooperadora/nuevaOperacion');
        try {
            //Valido que ninguna cuota este paga (por si hay concurrencia). 
            foreach ($valores["cuotas"] as $cuota) {
                $sql= " select * from operaciondetalle od inner join operaciones o on od.idoperacion=o.idoperacion ";
                $sql.=" where o.idpersona=:idpersona and o.anulada=0";
                $sql.=" and od.idcuota=:idcuota";
                $params = array(
                ":idpersona" => $valores["idpersona"],
                ":idcuota" => $cuota["idcuota"]
                );
                $this->PDO->execute($sql, "Cooperadora/nuevaOperacion", $params);
                $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
                if(count($result)>0){
                    //Rollback the transaction.
                    $this->PDO->rollbackTransaction('Cooperadora/nuevaOperacion');
                    return array("ok" => false, "message" => "Error al generar la operacion. Una de las cuotas ya esta paga. Refresque la página y vuelva a intentarlo.");
                    exit();
                }
            }
            
            //Agrego la operacion
            $sql = "insert into operaciones (fecha, idpersona, montototal, anulada, usucrea, fechacrea)
                values(:fecha, :idpersona, :montototal, 0, :usucrea, :fechacrea)";
            $params = array(
                ":fecha" => $valores["fecha"],
                ":idpersona" => $valores["idpersona"],
                ":montototal" => $valores["montototal"],
                ":usucrea" => $valores["usuario"],
                ":fechacrea" => date("Y-m-d H:i:s")
            );
           
            $this->PDO->execute($sql, 'Cooperadora/nuevaOperacion', $params);
            $idOperacion = $this->PDO->lastInsertId();
            
            //insert operacion detalle
            foreach ($valores["cuotas"] as $cuota) {
                $sql = "insert into operaciondetalle (idoperacion, idcuota, cuota, monto)
                VALUES(:idoperacion, :idcuota, :cuota, :monto) ";
                $params = array(
                    ":idoperacion" => $idOperacion,
                    ":idcuota" => $cuota["idcuota"],
                    ":cuota" => $cuota["cuota"], //Nombre
                    ":monto" => $valores["monto"]
                );
                $this->PDO->execute($sql, 'Cooperadora/nuevaOperacion', $params);
            }
            
            //Inserto o actualizo en cooperadorapersona
            //TODO: cambiar el estado de cooperadora dependiendo de las cuotas pagas.
            $sql= " select id from cooperadorapersona where idpersona=:idpersona";
            $params = array(
            ":idpersona" => $valores["idpersona"]
            );
            $this->PDO->execute($sql, "Cooperadora/cooperadorapersona", $params);
            $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
            if(count($result)>0){
                    $sql = "update cooperadorapersona set fechaultimopago=:fecha where idpersona=:idpersona";    
            }else{
                    $sql = "insert into cooperadorapersona (idpersona,idestado,fechaultimopago) values(:idpersona,null,:fecha)";
            }
            $params = array(
                ":idpersona" => $valores["idpersona"],
                ":fecha" => date("Y-m-d H:i:s")
            );
            $this->PDO->execute($sql, 'Cooperadora/guardarCooperadoraPersona', $params);
            
            $this->PDO->commitTransaction('Cooperadora/nuevaOperacion');
            return array("ok" => true, "message" => "La operacion se genero satisfactoriamente.", "idope" => $idOperacion);
          
        } //del try
        catch (Exception $e) {
            //Rollback the transaction.
            $this->PDO->rollbackTransaction('Cooperadora/nuevaOperacion' . $e->getMessage());
            return array("ok" => false, "message" => "Error al generar la operacion. Comuniquese con el administrador.");
        }
    }

// FIXIT no vuelve a habilitar las cuotas al anular la operacion.
    public function eliminarOperacion($filtros) { //Revisar el return si esta bien!!!!
//Anular una operacion
        $sql = " update operaciones set anulada = 1 where idoperacion = :idoperacion";

        $params = array();
        if ($filtros['idoperacion'] != "") {
            $params[":idoperacion"] = $filtros['idoperacion'];
        }

        $this->PDO->execute($sql, 'Cooperadora/elimianroepracion', $params);
        $result = $this->PDO->rowCount();
        return $result;
    }

    public function getAñosDeCuotas() {
        $sql = "select distinct año from cooperadoracuotas order by año desc";
        $this->PDO->execute($sql, 'Aplicacion/getAllCarreras');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

/*    public function getMontoCuotaActual() {
        $sql = "select valor from configuracion where parametro = 'montoCuotaCooperadora'";
        $this->PDO->execute($sql, 'Aplicacion/getAllCarreras');
        $result = $this->PDO->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
*/
    public function getAllEstadosCooperadora() {
        $sql = "select * from cooperadoraestados";
        $this->PDO->execute($sql, 'Aplicacion/getAllEstadosCooperadora');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function getAllCuotas() {
        $sql = "select * from cooperadoracuotas order by año desc,mes desc";
        $this->PDO->execute($sql, 'Aplicacion/getAllCuotas');
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

}
