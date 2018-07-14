<?php
$areaValue = isset($_POST['area']) ? $_POST['area'] : $viewData[0]['idarea'];
$nivelValue = isset($_POST['nivel']) ? $_POST['nivel'] : $viewData[0]['idnivel'];
$fechaValue = isset($_POST['fecha']) ? $_POST['fecha'] : $viewData[0]['fecha_dmy'];
$instrumentoValue = isset($_POST['instrumento']) ? $_POST['instrumento'] : $viewData[0]['idinstrumento'];
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
    <h3>Modificar Carrera <small><?php echo $viewData[0]['carreraDescripcion']; ?><?php if ($viewData[0]['instrumentoNombre'] != "") echo " - Instrumento " . $viewData[0]['instrumentoNombre']; ?><?php if ($viewData[0]['areaNombre'] != "") echo " - " . $viewData[0]['areaNombre']; ?></small></h3>

    <div class="form-group col-xs-12 col-sm-6 <?php if (array_key_exists('area', $validationErrors)) echo "has-error"; ?>">
      <label for="area" class="col-xs-4 control-label">Área</label>
      <div class="col-xs-8">
        <select class="form-control" id="area" name="area">
          <option value="0" data-nombre="" data-descripcion="">SELECCIONAR</option>
<?php foreach ($viewDataAreas as $area) { ?>
          <option value="<?php echo $area['idarea']; ?>"" <?php if ($area['idarea'] == $areaValue) echo "selected"; ?> ><?php echo $area['nombre']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 <?php if (array_key_exists('nivel', $validationErrors)) echo "has-error"; ?> required">
      <label for="nivel" class="col-xs-4 control-label">Nivel</label>
      <div class="col-xs-8">
        <select class="form-control" id="nivel" name="nivel">
          <option value="0" data-nombre="" data-descripcion="">SELECCIONAR</option>
<?php foreach ($viewDataNiveles as $nivel) { ?>
          <option value="<?php echo $nivel['id']; ?>" <?php if ($nivel['id'] == $nivelValue) echo "selected"; ?> ><?php echo $nivel['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 <?php if (array_key_exists('fecha', $validationErrors)) echo "has-error"; ?>">
      <label for="fecha" class="col-xs-4 control-label">Fecha Inscrip.</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" maxlength="10" id="fecha" name="fecha" placeholder="dd/mm/aaaa" value="<?php echo $fechaValue; ?>" <?php /* solo-validacoines-post required */ ?> autocomplete="off" >
      </div>
    </div>

<?php if (!$materiasActivas) { ?>
    <div class="form-group col-xs-12 col-sm-6 <?php if (array_key_exists('instrumento', $validationErrors)) echo "has-error"; ?> required">
      <label for="instrumento" class="col-xs-4 control-label">Instrumento</label>
      <div class="col-xs-8">
        <select class="form-control" id="instrumento" name="instrumento">
          <option value="0" data-nombre="" data-descripcion="">SELECCIONAR</option>
<?php foreach ($viewDataInstrumentos as $instrumento) { ?>
          <option value="<?php echo $instrumento['idinstrumento']; ?>" <?php if ($instrumento['idinstrumento'] == $instrumentoValue) echo "selected"; ?> ><?php echo $instrumento['nombre']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>
<?php } ?>

  </fieldset>

  <div class="form-group">
    <div class=" col-xs-12">
      <button type="submit" class="btn btn-lg btn-primary">Modificar</button>
      <a href="/modificar/<?php echo $idpersona; ?>/<?php echo $params; ?>" class="btn btn-lg btn-default">Cancelar</a>
    </div>
  </div>

</form>

<script type="text/javascript">
  var qCarreras = <?php echo count($newCarreras); ?>;

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

  $('.nueva-carrera-modal').on('hide.bs.modal', function (e) {
      $("#nueva-carrera-validations").text("").hide();
  });

  $('#button-agregar-carrera').click(function() {
    bError = false;

    if ($("#nuevaCarrera option:selected").val() == '0') {
      $("#nueva-carrera-validations").text("El campo Carrera es Obligatorio").show();
      bError = true;
    }

    if (!bError && $("#nuevoNivel option:selected").val() == '0') {
      $("#nueva-carrera-validations").text("El campo Nivel es Obligatorio").show();
      bError = true;
    }
<?php /* ?>
    if (!bError && $("#nuevaArea option:selected").val() == '0') {
      $("#nueva-carrera-validations").text("El campo Area es Obligatorio").show();
      bError = true;
    }
<?php */ ?>

    if (!bError && $("#nuevoInstrumento option:selected").val() == '0') {
      $("#nueva-carrera-validations").text("El campo Instrumento es Obligatorio").show();
      bError = true;
    }

    if (!bError && $("#nuevaFecha").val() == '') {
      $("#nueva-carrera-validations").text("El campo Fecha es Obligatorio").show();
      bError = true;
    }

    if (!bError && !isValidDate($("#nuevaFecha").val())) {
      $("#nueva-carrera-validations").text("El campo Fecha es invalido").show();
      bError = true;
    }

    if (!bError) {
      qCarreras++;
      newCarreraValue = $("#nuevaCarrera option:selected").val();
<?php if ($status=="inscripcion") { ?>
      newCarreraValue += '~**~0';
<?php } else { ?>
      newCarreraValue += '~**~' + $("#nuevaArea option:selected").val();
<?php } ?>
      newCarreraValue += '~**~' + $("#nuevoNivel option:selected").val();
      newCarreraValue += '~**~' + $("#nuevoInstrumento option:selected").val();
      newCarreraValue += '~**~' + $("#nuevaFecha").val();
      newRow = '<tr id="newCarreraN' + qCarreras + '">';
      newRow += '<td>' + $("#nuevaCarrera option:selected").attr('data-nombre') + '</td>';
      newRow += '<td>' + $("#nuevaCarrera option:selected").attr('data-descripcion') + '</td>';
      newRow += '<td>' + $("#nuevoNivel option:selected").attr('data-descripcion') + '</td>';
<?php if ($status=="inscripcion") { ?>
      newRow += '<td></td>';
<?php } else { ?>
      newRow += '<td>' + $("#nuevaArea option:selected").attr('data-nombre') + '</td>';
<?php } ?>
      newRow += '<td>' + $("#nuevoInstrumento option:selected").attr('data-nombre') + '</td>';
      newRow += '<td>' + $("#nuevaFecha").val() + '</td>';
      newRow += '<td></td>';
      newRow += '<td>NUEVA CARRERA - <button type="button" class="btn btn-sm btn-warning" id="quitarCarrera' + qCarreras + '">Quitar</button></td>';
      newRow += '<input type="hidden" name="new-carreras[]" value="' + newCarreraValue +'" />';
      newRow += '</tr>';
      $("#table-carreras > tbody:last-child").append(newRow);
      $('#quitarCarrera' + qCarreras).click(function() { $('#newCarreraN' + qCarreras).remove(); });
      $('.nueva-carrera-modal').modal('hide');

      $("#nuevaCarrera").val('0');
      $("#nuevoNivel").val('0');
      $("#nuevaArea").val('0');
      $("#nuevoInstrumento").val('0');

    }
  })

  $("#nuevaCarrera").change(function() {
    $.ajax({ url: "/<?php if ($status=="inscripcion") echo "ajaxnivelescarrerainscripcion"; else echo "ajaxnivelescarrera"; ?>/" + $(this).val(), dataType: 'json', success: function(response){
      $('#nuevoNivel').empty();
      $('#nuevoNivel').append($('<option>').text('SELECCIONAR').attr('value', 0));
      $.each(response, function(i, value) {
        $('#nuevoNivel').append($('<option>').text(value.descripcion).attr('value', value.id).attr('data-descripcion', value.descripcion));
      });    
    }});

    <?php /* el area solo se muestra para carreras que no son IMP/PIMP */ ?>
    if ($("#nuevaCarrera").val() != <?php echo $this->POROTO->Config['carrera_imp']; ?> && $("#nuevaCarrera").val() != <?php echo $this->POROTO->Config['carrera_pimp']; ?>) {
      $("#nuevaArea").val(0);
      $("#nuevaArea").prop('disabled', 'disabled');
    } else {
      $("#nuevaArea").prop('disabled', false);
    }
  });
      
</script>