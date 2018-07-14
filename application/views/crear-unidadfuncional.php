<?php
if ($status=="modificacion") {
    $fideicomisoValue = isset($_POST['fideicomiso']) ? $_POST['fideicomiso'] :$viewDataUnidadFuncional['idfideicomiso'];
    $descripcionValue = isset($_POST['descripcion']) ? $_POST['descripcion'] : $viewDataUnidadFuncional['descripcion'];
    $tipoValue = isset($_POST['tipo']) ? $_POST['tipo'] : $viewDataUnidadFuncional['idtipounidadfuncional'];
    $nrounidadfuncionalValue=isset($_POST['nrounidadfuncional']) ? $_POST['nrounidadfuncional'] :$viewDataUnidadFuncional['numerounidadfuncional'];
    $nroloteValue=isset($_POST['nrolote']) ? $_POST['nrolote'] : $viewDataUnidadFuncional['numerolote'];
    $porcentajedistribucioncategoriaaValue = isset($_POST['porcentajedistribucioncategoriaa']) ? $_POST['porcentajedistribucioncategoriaa'] : $viewDataUnidadFuncional['porcentajedistribucioncategoriaa'];
    $porcentajedistribucioncategoriaeValue = isset($_POST['porcentajedistribucioncategoriae']) ? $_POST['porcentajedistribucioncategoriae'] : $viewDataUnidadFuncional['porcentajedistribucioncategoriae'];
    $m2cubiertosValue = isset($_POST['m2cubiertos']) ? $_POST['m2cubiertos'] : $viewDataUnidadFuncional['m2cubiertos'];
    $observacionesValue = isset($_POST['observaciones']) ? $_POST['observaciones'] : $viewDataUnidadFuncional['observaciones'];
    $usucreaValue = isset($_POST['usucrea']) ? $_POST['usucrea'] : $viewDataUnidadFuncional['usucrea'];
    $fechacreaValue = isset($_POST['fechacrea']) ? $_POST['fechacrea'] : $viewDataUnidadFuncional['fechacrea'];
    $usumodiValue = isset($_POST['usumodi']) ? $_POST['usumodi'] : $viewDataUnidadFuncional['usumodi'];
    $fechamodiValue = isset($_POST['fechamodi']) ? $_POST['fechamodi'] : $viewDataUnidadFuncional['fechamodi'];
} elseif ($status=="alta") {
    $fideicomisoValue = isset($_POST['fideicomiso']) ? $_POST['fideicomiso'] :"";
    $descripcionValue = isset($_POST['descripcion']) ? $_POST['descripcion'] : "";
    $tipoValue = isset($_POST['tipo']) ? $_POST['tipo'] : "0";
    $nrounidadfuncionalValue=isset($_POST['nrounidadfuncional']) ? $_POST['nrounidadfuncional'] : "";
    $nroloteValue=isset($_POST['nrolote']) ? $_POST['nrolote'] : "";
    $porcentajedistribucioncategoriaaValue = isset($_POST['porcentajedistribucioncategoriaa']) ? $_POST['porcentajedistribucioncategoriaa'] : "0";
    $porcentajedistribucioncategoriaeValue = isset($_POST['porcentajedistribucioncategoriae']) ? $_POST['porcentajedistribucioncategoriae'] : "0";
    $m2cubiertosValue = isset($_POST['m2cubiertos']) ? $_POST['m2cubiertos'] : "0";
    $observacionesValue = isset($_POST['observaciones']) ? $_POST['observaciones'] : "";
    $usucreaValue = isset($_POST['usucrea']) ? $_POST['usucrea'] : "";
    $fechacreaValue = isset($_POST['fechacrea']) ? $_POST['fechacrea'] : "";
    $usumodiValue = isset($_POST['usumodi']) ? $_POST['usumodi'] : "";
    $fechamodiValue = isset($_POST['fechamodi']) ? $_POST['fechamodi'] : "";
}
?>
<style>
.form-group.required .control-label:after { 
   content:"*";
   color:red;
}
input, textarea { text-transform: uppercase; }
</style>
<?php if (count($validationErrors)>0) { ?>
<div class="alert alert-danger" role="alert">
 <?php foreach ($validationErrors as $error) { ?>
<p><?php echo $error; ?>
<?php   } ?>
</div>
<?php } ?>
<form class="form-horizontal" action="" method="POST" accept-charset="utf-8">  
  <fieldset>
    <h3><?php echo $pageTitle; ?></h3>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('fideicomiso', $validationErrors)) echo "has-error"; ?>">
      <label for="fideicomiso" class="col-xs-4 control-label">Fideicomiso</label>
      <div class="col-xs-8">
        <select class="form-control" id="tipo" name="fideicomiso">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataFideicomiso as $fideicomiso) { ?>
          <option value="<?php echo $fideicomiso['idfideicomiso']; ?>" <?php if ($fideicomisoValue == $fideicomiso['idfideicomiso']) echo "selected"; ?>><?php echo $fideicomiso['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>
    
    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('descripcion', $validationErrors)) echo "has-error"; ?>">
      <label for="descripcion" class="col-xs-4 control-label">Descripcion</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="" maxlength="100" value="<?php echo $descripcionValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('tipo', $validationErrors)) echo "has-error"; ?>">
      <label for="tipo" class="col-xs-4 control-label">Tipo</label>
      <div class="col-xs-8">
        <select class="form-control" id="estado" name="tipo">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataTipo as $tipo) { ?>
          <option value="<?php echo $tipo['idtipounidadfuncional']; ?>" <?php if ($tipoValue == $tipo['idtipounidadfuncional']) echo "selected"; ?>><?php echo $tipo['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('nrounidadfuncional', $validationErrors)) echo "has-error"; ?>">
      <label for="nrounidadfuncional" class="col-xs-4 control-label">N° Unidad Func.</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="nrounidadfuncional" name="nrounidadfuncional" placeholder="" maxlength="45" value="<?php echo $nrounidadfuncionalValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('nrolote', $validationErrors)) echo "has-error"; ?>">
      <label for="nrolote" class="col-xs-4 control-label">N° Lote</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="nrolote" name="nrolote" placeholder="" maxlength="45" value="<?php echo $nroloteValue; ?>" autocomplete="off" />
      </div>
    </div>
  </fieldset>

  <fieldset>
    <h3>Otros datos</h3>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('porcentajedistribucioncategoriaa', $validationErrors)) echo "has-error"; ?>">
      <label for="porcentajedistribucioncategoriaa" class="col-xs-4 control-label">% Distr. Cat A</label>
      <div class="col-xs-8">
        <input type="number" class="form-control" id="porcentajedistribucioncategoriaa" name="porcentajedistribucioncategoriaa" min="0" max="100" value="<?php echo $porcentajedistribucioncategoriaaValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('porcentajedistribucioncategoriae', $validationErrors)) echo "has-error"; ?>">
      <label for="porcentajedistribucioncategoriae" class="col-xs-4 control-label">% Distr. Cat E</label>
      <div class="col-xs-8">
        <input type="number" class="form-control" id="porcentajedistribucioncategoriae" name="porcentajedistribucioncategoriae" min="0" max="100" value="<?php echo $porcentajedistribucioncategoriaeValue; ?>" autocomplete="off" />
      </div>
    </div>    
    
    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('m2cubiertos', $validationErrors)) echo "has-error"; ?>">
      <label for="m2cubiertos" class="col-xs-4 control-label">M2 cubiertos</label>
      <div class="col-xs-8">
        <input type="number" class="form-control" id="m2cubiertos" name="m2cubiertos" min="0" value="<?php echo $m2cubiertosValue; ?>" autocomplete="off" />
      </div>
    </div>    

    <div class="form-group col-xs-12 <?php if (array_key_exists('observaciones', $validationErrors)) echo "has-error"; ?>">
      <label for="observaciones" class="col-xs-4 control-label">Observaciones</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="observaciones" name="observaciones" placeholder="" maxlength="500" value="<?php echo $observacionesValue; ?>" autocomplete="off" />
      </div>
    </div>
   
  </fieldset>    
    
  <div class="form-group">
    <div class=" col-xs-12">
      <button type="submit" class="btn btn-primary"><?php if ($status=="modificacion") echo "Modificar"; ?><?php if ($status=="alta") echo "Guardar"; ?></button>
      <a href="/gestion-unidadesfuncionales" class="btn btn-default">Cancelar</a>
    </div>
  </div>

</form>

<script type="text/javascript">
  function isValidDate(dateString)
  {
      // First check for the pattern
      if(!/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(dateString))
          return false;

      // Parse the date parts to integers
      var parts = dateString.split("/");
      var day = parseInt(parts[0], 10);
      var month = parseInt(parts[1], 10);
      var year = parseInt(parts[2], 10);

      // Check the ranges of month and year
      if(year < 1000 || year > 3000 || month == 0 || month > 12)
          return false;

      var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

      // Adjust for leap years
      if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
          monthLength[1] = 29;

      // Check the range of the day
      return day > 0 && day <= monthLength[month - 1];
  };

</script>