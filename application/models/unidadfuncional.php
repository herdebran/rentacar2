<?php

//Consultas sobre fideicomisos.
class ModeloUnidadFuncional {

    private $PDO;

    public function __construct($poroto) {
        $this->PDO = $poroto->PDO;
    }

    public function getUnidadFuncional($filtros) {
        //continuar
        $sql = "select a.idunidadfuncional, a.unidadfuncional ,a.idfideicomiso, a.fideicomiso,  a.fideicomiso,
                a.tipofideicomiso, a.tipounidadfuncional, a.domicilio, a.legaldomicilio,
                a.numerounidadfuncional, a.numerolote, a.descripcion, a.idtipounidadfuncional,
                a.estadounidadfuncional, a.porcentajedistribucioncategoriaa, a.porcentajedistribucioncategoriae, 
                a.m2cubiertos, a.observaciones, a.anulada
                from viewunidadfuncional a
                WHERE 1=1 and a.anulada=0";
        $params = array();

        if (isset($filtros['idfideicomiso']) && $filtros['idfideicomiso'] >0) {
            $sql .= " and a.idfideicomiso = :idfideicomiso";
            $params[":idfideicomiso"] =  $filtros['idfideicomiso'];
        }

        if (isset($filtros['idtipounidadfuncional']) && $filtros['idtipounidadfuncional'] >0) {
            $sql .= " and a.idtipounidadfuncional = :tipounidadfuncional";
            $params[":idtipounidadfuncional"] =  $filtros['idtipounidadfuncional'];
        }

        if (isset($filtros['descripcion']) && $filtros['descripcion'] >0) {
            $sql .= " and a.numerounidadfuncional = :numerounidadfuncional";
            $params[":numerounidadfuncional"] =  $filtros['descripcion'] . "%";
        }

        if (isset($filtros['descripcion']) && $filtros['descripcion'] != "") {
            $sql .= " and a.unidadfuncional like :unidadfuncional";
            $params[":unidadfuncional"] = "%" . $filtros['descripcion'] . "%";
        }
        
        if (isset($filtros['idestadounidadfuncional']) && $filtros['idestadounidadfuncional'] >0) {
            $sql .= " and a.idestadounidadfuncional = :idestadounidadfuncional";
            $params[":idestadounidadfuncional"] =  $filtros['idestadounidadfuncional'];
        }
        
        $sql .= " order by a.fideicomiso, a.numerounidadfuncional";

        $this->PDO->execute($sql, "unidadfuncional/getUnidadFuncional", $params);
        $result = $this->PDO->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    public function nuevaUnidadFuncional($valores) {
        //recibe todos los datos de la unidad funcional	
        $this->PDO->beginTransaction('unidadfuncional/nuevaUnidadFuncional');
        try {
            //Agrego el fideicomiso
            $sql = "insert into unidadfuncional (idfideicomiso, numerounidadfuncional, numerolote, descripcion, idtipounidadfuncional, 
                     idestadounidadfuncional, porcentajedistribucioncategoriaa, porcentajedistribucioncategoriae, 
                     m2cubiertos, observaciones, anulada, usucrea, fechacrea)
                     values(:idfideicomiso, :numerounidadfuncional, :numerolote, :descripcion, :idtipounidadfuncional, 
                     :idestadounidadfuncional, :porcentajedistribucioncategoriaa, :porcentajedistribucioncategoriae, 
                     :m2cubiertos, :observaciones, 0, :usucrea, :fechacrea)";
            $params = array(
                    ":idfideicomiso" => $valores["idfideicomiso"],
                    ":numerounidadfuncional" => $valores["numerounidadfuncional"],
                    ":numerolote" => $valores["numerolote"],
                    ":descripcion" => $valores["descripcion"],
                    ":idtipounidadfuncional" => $valores["idtipounidadfuncional"],
                    ":idestadounidadfuncional" => $valores["idestadounidadfuncional"],
                    ":porcentajedistribucioncategoriaa" => $valores["porcentajedistribucioncategoriaa"],
                    ":porcentajedistribucioncategoriae" => $valores["porcentajedistribucioncategoriae"],
                    ":m2cubiertos" => $valores["m2cubiertos"],
                    ":observaciones" => $valores["observaciones"],                                                                                         
                    ":usucrea" => $valores["usuario"],
                    ":fechacrea" => date("Y-m-d H:i:s")
                    );
            
            $this->PDO->execute($sql, 'unidadfuncional/nuevaUnidadFuncional', $params);
            $idUnidadFuncional = $this->PDO->lastInsertId();
            
            $this->PDO->commitTransaction('unidadfuncional/nuevaUnidadFuncional');
            return array("ok" => true, "message" => "La unidad funcional se generó satisfactoriamente.", "idunidadfuncional" => $idUnidadFuncional);
          
        } //del try
        catch (Exception $e) {
            //Rollback the transaction.
            $this->PDO->rollbackTransaction('unidadfuncional/nuevaUnidadFuncional' . $e->getMessage());
            return array("ok" => false, "message" => "Error al generar la unidad funcional. Comuniquese con el administrador.");
        }
    }
    
    public function anularUnidadFuncional($valores) { //Revisar el return si esta bien!!!!
//Anular una operacion
        $this->PDO->beginTransaction('unidadfuncional/anularUnidadFuncional');
        try {
            $sql = " update unidadfuncional set ";
            $sql.= " anulada =:anulada, usumodi=:usumodi, fechamodi=:fechamodi";
            $sql.= " where idunidadfuncional = :idunidadfuncional";

            $params = array(
                    ":idunidadfuncional" => $valores["idunidadfuncional"],
                    ":anulada" => $valores["anulada"],
                    ":usumodi" => $valores["usumodi"],
                    ":fechamodi" => date("Y-m-d H:i:s")
                    );

            
            $this->PDO->execute($sql, 'unidadfuncional/anularUnidadFuncional', $params);
            $this->PDO->commitTransaction('unidadfuncional/anularUnidadFuncional');
            return array("ok" => true, "message" => "La unidad funcional se anuló satisfactoriamente.");
} //del try
        catch (Exception $e) {
            //Rollback the transaction.
            $this->PDO->rollbackTransaction('unidadfuncional/anularUnidadFuncional' . $e->getMessage());
            return array("ok" => false, "message" => "Error al anular la unidad funcional. Comuniquese con el administrador.");
        }            
    }
    
    public function modificarUnidadFuncional($valores){
        $this->PDO->beginTransaction('unidadfuncional/modificarUnidadFuncional');
        try {
            $sql = "update unidadfuncional set
                    numerounidadfuncional=:numerounidadfuncional, numerolote=:numerolote, descripcion=:descripcion, 
                    idtipounidadfuncional=:idtipounidadfuncional, idestadounidadfuncional=:idestadounidadfuncional, porcentajedistribucioncategoriaa=:porcentajedistribucioncategoriaa, 
                    porcentajedistribucioncategoriae=:porcentajedistribucioncategoriae, m2cubiertos=:m2cubiertos, 
                    observaciones=:observaciones, activo=:activo; usumodi=:usumodi, fechamodi=:fechamodi)";
            $sql.= " where idunidadfuncional = :idunidadfuncional";

            $params = array(
                    ":numerounidadfuncional" => $valores["numerounidadfuncional"],
                    ":descripcion" => $valores["descripcion"],
                    ":idtipounidadfuncional" => $valores["idtipounidadfuncional"],
                    ":idestadounidadfuncional" => $valores["idestadounidadfuncional"],
                    ":porcentajedistribucioncategoriaa" => $valores["porcentajedistribucioncategoriaa"],
                    ":porcentajedistribucioncategoriae" => $valores["porcentajedistribucioncategoriae"],
                    ":m2cubiertos" => $valores["m2cubiertos"],
                    ":observaciones" => $valores["observaciones"],                                                                                
                    ":usumodi" => $valores["usuario"],
                    ":fechamodi" => date("Y-m-d H:i:s")
                    );

            $this->PDO->execute($sql, "unidadfuncional/modificarUnidadFuncional", $params);
            $this->PDO->commitTransaction('unidadfuncional/modificarUnidadFuncional');
            return array("ok" => true, "message" => "La unidad funcional se modificó satisfactoriamente.");
            
            
        } //del try
        catch (Exception $e) {
            //Rollback the transaction.
            $this->PDO->rollbackTransaction('unidadfuncional/modificarUnidadFuncional' . $e->getMessage());
            return array("ok" => false, "message" => "Error al modificar la unidad funcional. Comuniquese con el administrador.");
        }        
    }
    
    public function getUnidadFuncionalById($idunidadfuncional) {
        $sql = "select a.idunidadfuncional, a.idfideicomiso, a.idtipounidadfuncional, a.tipounidadfuncional, a.unidadfuncional,
            a.numerounidadfuncional,a.numerolote, a.idestadounidadfuncional, a.estadounidadfuncional,
            a.porcentajedistribucioncategoriaa, a.porcentajedistribucioncategoriae, a.m2cubiertos, 
            a.observaciones, a.anulada
            from viewunidadfuncional a
            where a.idunidadfuncional=:idunidadfuncional";

        $params = array(":idunidadfuncional" => $idunidadfuncional);
        $this->PDO->prepare($sql);
        $this->PDO->execute($sql, "unidadfuncional/getUnidadFuncionalById", $params);
        $result = $this->PDO->fetch(PDO::FETCH_ASSOC);
        return $result;
    }
}
