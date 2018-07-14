<?php
if ($status=="modificacion") {
    $descripcionValue = isset($_POST['descripcion']) ? $_POST['descripcion'] : $viewDataFideicomiso['descripcion'];
    $fechaconstValue = isset($_POST['fechaconst']) ? $_POST['fechaconst'] : $viewDataFideicomiso['fechaconstitucion'];
    $tipoValue = isset($_POST['tipo']) ? $_POST['tipo'] : $viewDataFideicomiso['idtipofideicomiso'];
    $estadoValue = isset($_POST['estado']) ? $_POST['estado'] : $viewDataFideicomiso['idestadofideicomiso'];
    $calleValue = isset($_POST['calle']) ? $_POST['calle'] : $viewDataFideicomiso['calle'];
    $numeroValue = isset($_POST['numero']) ? $_POST['numero'] : $viewDataFideicomiso['numero'];
    $provinciaValue = isset($_POST['provincia']) ? $_POST['provincia'] : $viewDataFideicomiso['idprovincia'];
    $municipioValue = isset($_POST['municipio']) ? $_POST['municipio'] : $viewDataFideicomiso['idmunicipio'];
    $localidadValue = isset($_POST['localidad']) ? $_POST['localidad'] : $viewDataFideicomiso['idlocalidad'];
    $legalcalleValue = isset($_POST['legalcalle']) ? $_POST['legalcalle'] : $viewDataFideicomiso['legalcalle'];
    $legalnumeroValue = isset($_POST['legalnumero']) ? $_POST['legalnumero'] : $viewDataFideicomiso['legalnumero'];
    $legalprovinciaValue = isset($_POST['legalprovincia']) ? $_POST['legalprovincia'] : $viewDataFideicomiso['legalidprovincia'];
    $legalmunicipioValue = isset($_POST['legalmunicipio']) ? $_POST['legalmunicipio'] : $viewDataFideicomiso['legalidmunicipio'];
    $legallocalidadValue = isset($_POST['legallocalidad']) ? $_POST['legallocalidad'] : $viewDataFideicomiso['legalidlocalidad'];
    $diaprimervencValue = isset($_POST['diaprimervenc']) ? $_POST['diaprimervenc'] : $viewDataFideicomiso['diaprimervencimiento'];
    $segundovencliquidaValue = isset($_POST['segundovencliquida']) ? $_POST['segundovencliquida'] : $viewDataFideicomiso['segundovencimientoliquida'];
    $segundovencdiaValue = isset($_POST['segundovencdia']) ? $_POST['segundovencdia'] : $viewDataFideicomiso['segundovencimientodia'];
    $segundovencrecargoValue = isset($_POST['segundovencrecargo']) ? $_POST['segundovencrecargo'] : $viewDataFideicomiso['segundovencimientorecargo'];
    $tercervencliquidaValue = isset($_POST['tercervencliquida']) ? $_POST['tercervencliquida'] : $viewDataFideicomiso['tercervencimientoliquida'];
    $tercervencdiaValue = isset($_POST['tercervencdia']) ? $_POST['tercervencdia'] : $viewDataFideicomiso['tercervencimientodia'];
    $tercervencrecargoValue = isset($_POST['tercervencrecargo']) ? $_POST['tercervencrecargo'] : $viewDataFideicomiso['tercervencimientorecargo'];
    $tasapunitoriodiarioValue = isset($_POST['tasapunitoriodiario']) ? $_POST['tasapunitoriodiario'] : $viewDataFideicomiso['tasapunitoriodiario'];
    $usucreaValue = isset($_POST['usucrea']) ? $_POST['usucrea'] : $viewDataFideicomiso['usucrea'];
    $fechacreaValue = isset($_POST['fechacrea']) ? $_POST['fechacrea'] : $viewDataFideicomiso['fechacrea'];
    $usumodiValue = isset($_POST['usumodi']) ? $_POST['usumodi'] : $viewDataFideicomiso['usumodi'];
    $fechamodiValue = isset($_POST['fechamodi']) ? $_POST['fechamodi'] : $viewDataFideicomiso['fechamodi'];
} elseif ($status=="alta") {
    $descripcionValue = isset($_POST['descripcion']) ? $_POST['descripcion'] : "";
    $fechaconstValue = isset($_POST['fechaconst']) ? $_POST['fechaconst'] : "";
    $tipoValue = isset($_POST['tipo']) ? $_POST['tipo'] : "0";
    $estadoValue = isset($_POST['estado']) ? $_POST['estado'] : "0";
    $calleValue = isset($_POST['calle']) ? $_POST['calle'] : "";
    $numeroValue = isset($_POST['numero']) ? $_POST['numero'] : "";
    $provinciaValue = isset($_POST['provincia']) ? $_POST['provincia'] :"";
    $municipioValue = isset($_POST['municipio']) ? $_POST['municipio'] :"";
    $localidadValue = isset($_POST['localidad']) ? $_POST['localidad'] : "";
    $legalcalleValue = isset($_POST['legalcalle']) ? $_POST['legalcalle'] : "";
    $legalnumeroValue = isset($_POST['legalnumero']) ? $_POST['legalnumero'] : "";
    $legalprovinciaValue = isset($_POST['legalprovincia']) ? $_POST['legalprovincia'] : "";
    $legalmunicipioValue = isset($_POST['legalmunicipio']) ? $_POST['legalmunicipio'] : "";
    $legallocalidadValue = isset($_POST['legallocalidad']) ? $_POST['legallocalidad'] : "";
    $diaprimervencValue = isset($_POST['diaprimervenc']) ? $_POST['diaprimervenc'] : "";
    $segundovencliquidaValue = isset($_POST['segundovencliquida']) ? $_POST['segundovencliquida'] : "0";
    $segundovencdiaValue = isset($_POST['segundovencdia']) ? $_POST['segundovencdia'] : "";
    $segundovencrecargoValue = isset($_POST['segundovencrecargo']) ? $_POST['segundovencrecargo'] : "0";
    $tercervencliquidaValue = isset($_POST['tercervencliquida']) ? $_POST['tercervencliquida'] :"0";
    $tercervencdiaValue = isset($_POST['tercervencdia']) ? $_POST['tercervencdia'] : "";
    $tercervencrecargoValue = isset($_POST['tercervencrecargo']) ? $_POST['tercervencrecargo'] : "0";
    $tasapunitoriodiarioValue = isset($_POST['tasapunitoriodiario']) ? $_POST['tasapunitoriodiario'] : "";
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

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('descripcion', $validationErrors)) echo "has-error"; ?>">
      <label for="descripcion" class="col-xs-4 control-label">Descripcion</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="" maxlength="100" value="<?php echo $descripcionValue; ?>" <?php /* solo-valider="" dacoines-post required */ ?> <?php if ($status=="reempadronamiento" || $status=="misdatos") echo "disabled"; else echo "autofocus=\"on\""; ?> autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('fechaconst', $validationErrors)) echo "has-error"; ?>">
      <label for="fechaconst" class="col-xs-4 control-label">F.Constitucion</label>
      <div class="col-xs-8">
        <input type="text" class="form-control datepicker" maxlength="10" id="fechaconst" name="fechaconst" placeholder="dd/mm/aaaa" value="<?php echo $fechaconstValue; ?>" autocomplete="off" >
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('tipo', $validationErrors)) echo "has-error"; ?>">
      <label for="tipo" class="col-xs-4 control-label">Tipo</label>
      <div class="col-xs-8">
        <select class="form-control" id="tipo" name="tipo">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataTipo as $tipo) { ?>
          <option value="<?php echo $tipo['idtipofideicomiso']; ?>" <?php if ($tipoValue == $tipo['idtipofideicomiso']) echo "selected"; ?>><?php echo $tipo['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('estado', $validationErrors)) echo "has-error"; ?>">
      <label for="estado" class="col-xs-4 control-label">Estado</label>
      <div class="col-xs-8">
        <select class="form-control" id="estado" name="estado">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataEstado as $estado) { ?>
          <option value="<?php echo $estado['idestadofideicomiso']; ?>" <?php if ($estadoValue == $estado['idestadofideicomiso']) echo "selected"; ?>><?php echo $estado['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>
  
  </fieldset>
  <fieldset>
    <h3>Domicilio Obra</h3>
    
    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('calle', $validationErrors)) echo "has-error"; ?>">
      <label for="calle" class="col-xs-4 control-label">Calle</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="calle" name="calle" placeholder=""  maxlength="45" value="<?php echo $calleValue; ?>" autocomplete="off" >
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('numero', $validationErrors)) echo "has-error"; ?>">
      <label for="numero" class="col-xs-4 control-label">Número</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="numero" name="numero" placeholder="" maxlength="5" value="<?php echo $numeroValue; ?>" autocomplete="off" >
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('provincia', $validationErrors)) echo "has-error"; ?>">
      <label for="provincia" class="col-xs-4 control-label">Provincia</label>
      <div class="col-xs-8">
        <select class="form-control" id="provincia" name="provincia" >
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataProvincias as $prov) { ?>
          <option value="<?php echo $prov['idprovincia']; ?>" <?php if ($provinciaValue == $prov['idprovincia']) echo "selected"; ?>><?php echo $prov['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('municipio', $validationErrors)) echo "has-error"; ?>">
      <label for="municipio" class="col-xs-4 control-label">Municipio</label>
      <div class="col-xs-8">
          <select class="form-control" id="municipio" name="municipio" >
          <option value="0">SELECCIONAR</option><?php foreach ($viewDataMunicipios as $municipio) { ?>
          <option value="<?php echo $municipio['idmunicipio']; ?>" <?php if ($municipioValue == $municipio['idmunicipio']) echo "selected"; ?>><?php echo $municipio['descripcion']; ?></option>
<?php } ?></select>
      </div>
    </div>
    
    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('localidad', $validationErrors)) echo "has-error"; ?>">
      <label for="localidad" class="col-xs-4 control-label">Localidad</label>
      <div class="col-xs-8">
          <select class="form-control" id="localidad" name="localidad" >
          <option value="0">SELECCIONAR</option><?php foreach ($viewDataLocalidades as $localidad) { ?>
          <option value="<?php echo $localidad['idlocalidad']; ?>" <?php if ($localidadValue == $localidad['idlocalidad']) echo "selected"; ?>><?php echo $localidad['descripcion']; ?></option>
<?php } ?></select>
      </div>
    </div>    
  </fieldset>


  <fieldset>
    <h3>Domicilio Legal</h3>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('legalcalle', $validationErrors)) echo "has-error"; ?>">
      <label for="legalcalle" class="col-xs-4 control-label">Calle</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="legalcalle" name="legalcalle" placeholder="" maxlength="45" value="<?php echo $legalcalleValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('legalnumero', $validationErrors)) echo "has-error"; ?>">
      <label for="legalnumero" class="col-xs-4 control-label">Número</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="legalnumero" name="legalnumero" placeholder="" maxlength="5" value="<?php echo $legalnumeroValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('legalprovincia', $validationErrors)) echo "has-error"; ?>">
      <label for="legalprovincia" class="col-xs-4 control-label">Provincia</label>
      <div class="col-xs-8">
        <select class="form-control" id="legalprovincia" name="legalprovincia" >
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataProvincias as $prov) { ?>
          <option value="<?php echo $prov['idprovincia']; ?>" <?php if ($legalprovinciaValue == $prov['idprovincia']) echo "selected"; ?>><?php echo $prov['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('legalmunicipio', $validationErrors)) echo "has-error"; ?>">
      <label for="legalmunicipio" class="col-xs-4 control-label">Municipio</label>
      <div class="col-xs-8">
          <select class="form-control" id="legalmunicipio" name="legalmunicipio" >
          <option value="0">SELECCIONAR</option><?php foreach ($viewDataLegalMunicipios as $municipio) { ?>
          <option value="<?php echo $municipio['idmunicipio']; ?>" <?php if ($legalmunicipioValue == $municipio['idmunicipio']) echo "selected"; ?>><?php echo $municipio['descripcion']; ?></option>
<?php } ?></select>
      </div>
    </div>
    
    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('legallocalidad', $validationErrors)) echo "has-error"; ?>">
      <label for="legallocalidad" class="col-xs-4 control-label">Localidad</label>
      <div class="col-xs-8">
          <select class="form-control" id="legallocalidad" name="legallocalidad" >
          <option value="0">SELECCIONAR</option><?php foreach ($viewDataLegalLocalidades as $localidad) { ?>
          <option value="<?php echo $localidad['idlocalidad']; ?>" <?php if ($legallocalidadValue == $localidad['idlocalidad']) echo "selected"; ?>><?php echo $localidad['descripcion']; ?></option>
<?php } ?></select>
      </div>
    </div>  
    
  </fieldset>



  <fieldset>
    <h3>Otros datos</h3>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('diaprimervenc', $validationErrors)) echo "has-error"; ?>">
      <label for="diaprimervenc" class="col-xs-4 control-label">Día 1er Venc.</label>
      <div class="col-xs-8">
        <input type="number" class="form-control" id="diaprimervenc" name="diaprimervenc" min="0" max="31" value="<?php echo $diaprimervencValue; ?>" autocomplete="off" />
      </div>
    </div>
    
    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('segundovencliquida', $validationErrors)) echo "has-error"; ?>">
      <div class="col-sm-offset-4 col-sm-8">
        <div class="checkbox">
          <label for="segundovencliquida">
            <input type="checkbox" id="segundovencliquida" name="segundovencliquida" value="segundovencliquida" <?php if ($segundovencliquidaValue == 1) echo "checked"; ?>>Liquida 2do Venc.
          </label>
        </div>
      </div>
    </div>
    
    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('segundovencdia', $validationErrors)) echo "has-error"; ?>">
      <label for="segundovencdia" class="col-xs-4 control-label">Día 2do Venc.</label>
      <div class="col-xs-8">
        <input type="number" class="form-control" id="segundovencdia" name="segundovencdia" min="0" max="31" <?php if ($segundovencliquidaValue == 0) echo "disabled"; ?> value="<?php echo $segundovencdiaValue; ?>" autocomplete="off" />
      </div>
    </div>    

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('segundovencrecargo', $validationErrors)) echo "has-error"; ?>">
      <label for="segundovencrecargo" class="col-xs-4 control-label">Recargo %</label>
      <div class="col-xs-8">
        <input type="number" pattern="-?[0-9]+[\,.]*[0-9]+" step=".1" class="form-control" id="segundovencrecargo" name="segundovencrecargo" min="0" max="100" <?php if ($segundovencliquidaValue == 0) echo "disabled"; ?> value="<?php echo $segundovencrecargoValue; ?>" autocomplete="off" />
      </div>
    </div>    

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('tercervencliquida', $validationErrors)) echo "has-error"; ?>">
      <div class="col-sm-offset-4 col-sm-8">
        <div class="checkbox">
          <label for="tercervencliquida">
            <input type="checkbox" id="tercervencliquida" name="tercervencliquida" value="tercervencliquida" <?php if ($tercervencliquidaValue == 1) echo "checked"; ?>> Liquida 3r Venc.
          </label>
        </div>
      </div>
    </div>
    
    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('tercervencdia', $validationErrors)) echo "has-error"; ?>">
      <label for="tercervencdia" class="col-xs-4 control-label">Día 3r Venc.</label>
      <div class="col-xs-8">
        <input type="number" class="form-control" id="tercervencdia" name="tercervencdia" min="0" max="31" <?php if ($tercervencliquidaValue == 0) echo "disabled"; ?> value="<?php echo $tercervencdiaValue; ?>" autocomplete="off" />
      </div>
    </div>    

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('tercervencrecargo', $validationErrors)) echo "has-error"; ?>">
      <label for="tercervencrecargo" class="col-xs-4 control-label">Recargo %</label>
      <div class="col-xs-8">
        <input type="number" pattern="-?[0-9]+[\,.]*[0-9]+" step=".1" class="form-control" id="tercervencrecargo" name="tercervencrecargo" min="0" max="100" <?php if ($tercervencliquidaValue == 0) echo "disabled"; ?> value="<?php echo $tercervencrecargoValue; ?>" autocomplete="off" />
      </div>
    </div>    

  </fieldset>

  <fieldset>
    <h3>Tasa de punitorio</h3>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('tasapunitoriodiario', $validationErrors)) echo "has-error"; ?>">
      <label for="tasapunitoriodiario" class="col-xs-4 control-label">Punitorio diario % </label>
      <div class="col-xs-8">
        <input type="number" pattern="-?[0-9]+[\,.]*[0-9]+" step=".1" class="form-control" id="tasapunitoriodiario" name="tasapunitoriodiario" min="0" max="100" value="<?php echo $tasapunitoriodiarioValue; ?>" autocomplete="off" />
      </div>
    </div>
  </fieldset>


  <div class="form-group">
    <div class=" col-xs-12">
      <button type="submit" class="btn btn-primary"><?php if ($status=="modificacion") echo "Modificar"; ?><?php if ($status=="alta") echo "Guardar"; ?></button>
      <a href="/gestion-fideicomisos" class="btn btn-default">Cancelar</a>
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

  $("#legalprovincia").change(function() {
      $.ajax({ url: "/ajaxmunicipios/" + $(this).val(), dataType: 'json', success: function(response){
      $('#legalmunicipio').empty();
      $('#legalmunicipio').append($('<option>').text('SELECCIONAR').attr('value', 0));
      $.each(response, function(i, value) {
        $('#legalmunicipio').append($('<option>').text(value.descripcion).attr('value', value.idmunicipio));
      }); 
    }});
  });
    $("#legalmunicipio").change(function() {
      $.ajax({ url: "/ajaxlocalidades/" + $(this).val(), dataType: 'json', success: function(response){
      $('#legallocalidad').empty();
      $('#legallocalidad').append($('<option>').text('SELECCIONAR').attr('value', 0));
      $.each(response, function(i, value) {
           $('#legallocalidad').append($('<option>').text(value.descripcion).attr('value', value.idlocalidad));
      }); 
    }});
  });  
    $("#segundovencliquida").click(function() {
      if (this.checked) {
        $("#segundovencdia").removeAttr("disabled");
        $("#segundovencrecargo").removeAttr("disabled");
    } else {
        $("#segundovencdia").attr("disabled", true);
        $("#segundovencrecargo").attr("disabled", true);
   }
  });
  $("#tercervencliquida").click(function() {
      if (this.checked) {
        $("#tercervencdia").removeAttr("disabled");
        $("#tercervencrecargo").removeAttr("disabled");
    } else {
        $("#tercervencdia").attr("disabled", true);
        $("#tercervencrecargo").attr("disabled", true);
   }


  });
  
  //USO DE LAS FECHAS
     $.datepicker.regional['es'] = {
        closeText: 'Cerrar',
        prevText: '< Ant',
        nextText: 'Sig >',
        currentText: 'Hoy',
        monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        monthNamesShort: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun', 'Jul', 'Ago', 'Sep', 'Oct', 'Nov', 'Dic'],
        dayNames: ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
        dayNamesShort: ['Dom', 'Lun', 'Mar', 'Mié', 'Juv', 'Vie', 'Sáb'],
        dayNamesMin: ['Do', 'Lu', 'Ma', 'Mi', 'Ju', 'Vi', 'Sá'],
        weekHeader: 'Sm',
        dateFormat: 'dd/mm/yy',
        firstDay: 7,
        isRTL: false,
        showMonthAfterYear: false,
        yearSuffix: ''
    };
    $.datepicker.setDefaults($.datepicker.regional['es']);
    $(".datepicker").datepicker();
  //FIN USO DE LAS FECHAS  
 
 
   //reemplazar lo de carrera para provincia
    
$("#provincia").change(function() { //OK
$.ajax({ url: "/ajaxmunicipios/" + $(this).val() , dataType: 'json', success: function(response){
$('#municipio').empty();
$('#localidad').empty();
$('#municipio').append($('<option>').text('SELECCIONAR').attr('value', 0));
$('#localidad').append($('<option>').text('SELECCIONAR').attr('value', 0));
$encontre=false;
$.each(response, function(i, value) {
$('#municipio').append($('<option>').text(value.descripcion).attr('value', value.idmunicipio));
    if(sessionStorage.getItem("crearfideicomiso-municipio")==value.idmunicipio){
    $encontre=true;
    $('#municipio').val(sessionStorage.getItem("crearfideicomiso-municipio"));	
    }
});
//Si no se encontreo variable seleccionada o bien no hay ninguna dejo seleccionado el SELECCIONAR
if (!$encontre) $('#municipio').val(0);    
$("#municipio").trigger("change");
}});
//guardo temporalmente la variabla elegida
sessionStorage.setItem("crearfideicomiso-provincia",$(this).val());
});
  
$("#municipio").change(function () { //OK
        $.ajax({url: "/ajaxlocalidades/" + $(this).val(), dataType: 'json',
			success: function (response) {
				$('#localidad').empty();
				$('#localidad').append($('<option>').text('SELECCIONAR').attr('value', 0));
				$encontre=false;
				$.each(response, function (i, value) {
					$('#localidad').append($('<option>').text(value.descripcion).attr('value', value.idlocalidad));
					if(sessionStorage.getItem("crearfideicomiso-localidad")==value.idlocalidad){
						$encontre=true;
						$('#localidad').val(sessionStorage.getItem("crearfideicomiso-localidad"));	
					}
				}); //each
	  //Si no se encontreo variable seleccionada o bien no hay ninguna dejo seleccionado el SELECCIONAR
	if (!$encontre) {
		$('#localidad').val(0);    
	}
	$("#localidad").trigger("change");
    }});
	  //guardo temporalmente la variabla elegida
	  sessionStorage.setItem("crearfideicomiso-municipio",$(this).val());
 });
  
  //reemplazar lo de carrera para localidad
  
$("#localidad").change(function () { //OK
	  //guardo temporalmente la variabla elegida
	  sessionStorage.setItem("crearfideicomiso-localidad",$(this).val());
});

//esta ultima es la q activa todo.
if(sessionStorage.getItem("crearfideicomiso-provincia")){
			$('#provincia').val(sessionStorage.getItem("crearfideicomiso-provincia"));
			$("#provincia").trigger("change");
}
$("#legalprovincia").change(function() { //OK
$.ajax({ url: "/ajaxmunicipios/" + $(this).val() , dataType: 'json', success: function(response){
$('#legalmunicipio').empty();
$('#legallocalidad').empty();
$('#legalmunicipio').append($('<option>').text('SELECCIONAR').attr('value', 0));
$('#legallocalidad').append($('<option>').text('SELECCIONAR').attr('value', 0));
$encontre=false;
$.each(response, function(i, value) {
$('#legalmunicipio').append($('<option>').text(value.descripcion).attr('value', value.idmunicipio));
    if(sessionStorage.getItem("crearfideicomiso-legalmunicipio")==value.idmunicipio){
    $encontre=true;
    $('#legalmunicipio').val(sessionStorage.getItem("crearfideicomiso-legalmunicipio"));	
    }
});
//Si no se encontreo variable seleccionada o bien no hay ninguna dejo seleccionado el SELECCIONAR
if (!$encontre) $('#legalmunicipio').val(0);    
$("#legalmunicipio").trigger("change");
}});
//guardo temporalmente la variabla elegida
sessionStorage.setItem("crearfideicomiso-legalprovincia",$(this).val());
});
  
$("#legalmunicipio").change(function () { //OK
        $.ajax({url: "/ajaxlocalidades/" + $(this).val(), dataType: 'json',
			success: function (response) {
				$('#legallocalidad').empty();
				$('#legallocalidad').append($('<option>').text('SELECCIONAR').attr('value', 0));
				$encontre=false;
				$.each(response, function (i, value) {
					$('#legallocalidad').append($('<option>').text(value.descripcion).attr('value', value.idlocalidad));
					if(sessionStorage.getItem("crearfideicomiso-legallocalidad")==value.idlocalidad){
						$encontre=true;
						$('#legallocalidad').val(sessionStorage.getItem("crearfideicomiso-legallocalidad"));	
					}
				}); //each
	  //Si no se encontreo variable seleccionada o bien no hay ninguna dejo seleccionado el SELECCIONAR
	if (!$encontre) {
		$('#legallocalidad').val(0);    
	}
	$("#legallocalidad").trigger("change");
    }});
	  //guardo temporalmente la variabla elegida
	  sessionStorage.setItem("crearfideicomiso-legalmunicipio",$(this).val());
 });
  
  //reemplazar lo de carrera para localidad
  
$("#legallocalidad").change(function () { //OK
	  //guardo temporalmente la variabla elegida
	  sessionStorage.setItem("crearfideicomiso-legallocalidad",$(this).val());
});

//esta ultima es la q activa todo.
if(sessionStorage.getItem("crearfideicomiso-legalprovincia")){
			$('#legalprovincia').val(sessionStorage.getItem("crearfideicomiso-legalprovincia"));
			$("#legalprovincia").trigger("change");
}
</script>