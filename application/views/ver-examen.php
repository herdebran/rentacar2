<?php
$tipexaValue = isset($_POST['tipexa']) ? $_POST['tipexa'] : $viewData[0]['tipexa'];
$carreraValue = isset($_POST['carrera']) ? $_POST['carrera'] : $viewData[0]['carrera'];
$materiaValue = isset($_POST['materia']) ? $_POST['materia'] : $viewData[0]['materia'];
$comisionValue = isset($_POST['comision']) ? $_POST['comision'] : $viewData[0]['comision'];
$fechaValue = isset($_POST['fecha']) ? $_POST['fecha'] : $viewData[0]['fecha'];
$horaValue = isset($_POST['hora']) ? $_POST['hora'] : $viewData[0]['hora'];
$nombreValue = isset($_POST['nombre']) ? $_POST['nombre'] : $viewData[0]['nombre'];
$cupoValue = isset($_POST['cupo']) ? $_POST['cupo'] : $viewData[0]['cupo'];
$aulaValue = isset($_POST['aula']) ? $_POST['aula'] : $viewData[0]['aula'];
$instrumentoValue = isset($_POST['instrumento']) ? $_POST['instrumento'] : $viewData[0]['instrumento'];
$areaValue = isset($_POST['area']) ? $_POST['area'] : $viewData[0]['area'];
//Cambio 38 Leo 20170710
$idProfesor = isset($_POST['idProfesor']) ? $_POST['idProfesor'] : $viewData[0]['idProfesor'];
//Fin Cambio 38
?>
<style>
    .form-group.required .control-label:after { 
        content:"*";
        color:red;
    }
    input, textarea { text-transform: uppercase; }
</style>
<?php if (count($validationErrors) > 0) { ?>
    <div class="alert alert-danger" role="alert">
        <?php foreach ($validationErrors as $error) { ?>
            <p><?php echo $error; ?>
            <?php } ?>
    </div>
<?php } ?>
<form id="daForm" class="form-horizontal" action="" method="POST" accept-charset="utf-8">  
    <fieldset>
        <h3><?php echo $pageTitle; ?></h3>

        <div class="form-group col-xs-12 col-md-6 required <?php if (array_key_exists('tipexa', $validationErrors)) echo "has-error"; ?>">
            <label for="tipexa" class="col-xs-2  col-md-3 control-label">Tipo Exámen</label>
            <div class="col-xs-10 col-md-9">
                <select class="form-control" id="tipexa" name="tipexa" autofocus="on">
                    <option value="0">SELECCIONAR</option>
                    <?php foreach ($viewDataTipoExamen as $tipexa) { ?>
                        <option value="<?php echo $tipexa['id']; ?>" <?php if ($tipexaValue == $tipexa['id']) echo "selected"; ?>><?php echo $tipexa['descripcion']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

<div class="form-group col-xs-12 col-md-6 required <?php if (array_key_exists('carrera', $validationErrors)) echo "has-error"; ?>">
      <label for="carrera" class="col-xs-2 col-md-3 control-label">Carrera / Ciclo</label>
      <div class="col-xs-10 col-md-9">
        <select class="form-control" id="carrera" name="carrera">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataCarreras as $carrera) { ?>
          <option value="<?php echo $carrera['id']; ?>"><?php echo $carrera['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

        <div class="form-group col-xs-12 col-md-6 required <?php if (array_key_exists('materia', $validationErrors)) echo "has-error"; ?>">
            <label for="materia" class="col-xs-2 col-md-3 control-label">Materia</label>
            <div class="col-xs-10 col-md-9">
                <select class="form-control" id="materia" name="materia">
                    <option value="0">SELECCIONAR</option>

                </select>
            </div>
        </div>

        <div class="form-group col-xs-12 col-md-6 <?php if($ses->tienePermiso('','Gestion de Examenes Profesor')){ echo "required";} ?> <?php if (array_key_exists('comision', $validationErrors)){ echo "has-error";} ?>">
            <label for="comision" class="col-xs-2  col-md-3 control-label">Comisión</label>
            <div class="col-xs-10 col-md-9">
                <select class="form-control" id="comision" name="comision">
                    <option value="0">SELECCIONAR</option>

                </select>
            </div>
        </div>

        <div class="form-group col-xs-12 col-md-6 <?php if (array_key_exists('nombre', $validationErrors)) echo "has-error"; ?>">
            <label for="nombre" class="col-xs-2 col-md-3 control-label">Nombre</label>
            <div class="col-xs-10 col-md-9">
                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="" maxlength="45" value="<?php echo $nombreValue; ?>" autocomplete="off"  />
            </div>
        </div>

        <div class="form-group col-xs-12 col-sm-6 required <?php if (array_key_exists('fecha', $validationErrors)) echo "has-error"; ?>">
            <label for="fecha" class="col-xs-2 col-md-3 control-label">Fecha</label>
            <div class="col-xs-10 col-md-9">
                <input type="text" class="form-control" maxlength="10" id="fecha" name="fecha" placeholder="dd/mm/aaaa" value="<?php echo $fechaValue; ?>" autocomplete="off" >
            </div>
        </div>


        <div class="form-group col-xs-12 col-sm-6 <?php if (array_key_exists('hora', $validationErrors)) echo "has-error"; ?>">
            <label for="hora" class="col-xs-2 col-md-3 control-label">Hora</label>
            <div class="col-xs-10 col-md-9">
                <input type="text" class="form-control" maxlength="5" id="hora" name="hora" placeholder="hh:mm" value="<?php echo $horaValue; ?>" autocomplete="off" >
            </div>
        </div>

        <div hidden="true" id="cupo" class="form-group col-xs-12 col-sm-6 required <?php if (array_key_exists('cupo', $validationErrors)) echo "has-error"; ?>">
            <label for="cupo" class="col-xs-2 col-md-3 control-label">Cupo</label>
            <div class="col-xs-10 col-md-9">
                <input type="text" class="form-control" maxlength="3" id="cupo" name="cupo" placeholder="" value="<?php echo $cupoValue; ?>" autocomplete="off" >
            </div>
        </div>

        <div class="form-group col-xs-12 col-sm-6 <?php if (array_key_exists('aula', $validationErrors)) echo "has-error"; ?>">
            <label for="aula" class="col-xs-2 col-md-3 control-label">Aula</label>
            <div class="col-xs-10 col-md-9">
                <input type="text" class="form-control" maxlength="45" id="aula" name="aula" placeholder="" value="<?php echo $aulaValue; ?>" autocomplete="off" >
            </div>
        </div>

        <div class="form-group col-xs-12 col-sm-6 <?php if (array_key_exists('instrumento', $validationErrors)) echo "has-error"; ?>">
            <label for="instrumento" class="col-xs-2 col-md-3 control-label">Instrumento</label>
            <div class="col-xs-10 col-md-9">
                <select class="form-control" id="instrumento" name="instrumento">
                    <option value="0">SELECCIONAR</option>
                    <?php foreach ($viewDataInstrumentos as $instrumento) { ?>
                        <option value="<?php echo $instrumento['idinstrumento']; ?>" <?php if ($instrumentoValue == $instrumento['idinstrumento']) echo "selected"; ?>><?php echo $instrumento['nombre']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group col-xs-12 col-sm-6<?php if (array_key_exists('area', $validationErrors)) echo "has-error"; ?>">
            <label for="area" class="col-xs-2 col-md-3 control-label">Género</label>
            <div class="col-xs-10 col-md-9">
                <select class="form-control" id="area" name="area">
                    <option value="0">SELECCIONAR</option>
                    <?php foreach ($viewDataAreas as $area) { ?>
                        <option value="<?php echo $area['idarea']; ?>" <?php if ($areaValue == $area['idarea']) echo "selected"; ?>><?php echo $area['nombre']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="clearfix"></div>


        <fieldset>
            <h3>Profesores</h3>
            <p>Administre aqui los profesores asignados al exámen</p>
            <p id="profesor-duplicado" class="text-danger collapse">El profesor ya fue registrado para este exámen. No puede duplicarse</p>
            <p id="profesor-required" class="text-danger collapse">Debe especificar al menos un Profesor para el exámen</p>

            <div class="form-group col-xs-12">
                <label for="profesor" class="col-xs-2 control-label">Profesor</label>
                <div class="col-xs-10">
                    <select class="form-control" id="profesor" name="profesor">
                        <option value="0">SELECCIONAR</option>
                        <?php foreach ($viewDataProfesores as $profesor) { ?>
                            <option value="<?php echo $profesor['idpersona']; ?>" <?php if ($idProfesor == $profesor['idpersona']) echo "selected"; ?> ><?php echo $profesor['apellido']; ?>,<?php echo $profesor['nombre']; ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="form-group col-xs-12">
                <button type="button" class="btn btn-info pull-right" id="button-agregar-profesor">Agregar</button>
            </div>

            <div class="clearfix"></div>

            <div class="panel panel-default">
                <div class="panel-body  <?php if (array_key_exists('profesores', $validationErrors)) echo "bg-danger"; ?>">
                    <table class="table" id="table-profesores">
                        <thead>
                            <tr>
                                <th>Profesor</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($newProfesores as $newProfesor) { ?>
                                <tr id="newProfesor<?php echo $newProfesor['idpersona']; ?>">
                            <input type="hidden" name="profesores[]" value="<?php echo $newProfesor['idpersona']; ?>~**~<?php echo $newProfesor['apellidonombre']; ?>" />
                            <td><?php echo $newProfesor['apellidonombre']; ?></td>
                            <td><button type="button" id="quitarProfesor<?php echo $newProfesor['idpersona']; ?>">Quitar</button></td>
                            </tr>
                            <script type="text/javascript">
                                $('#quitarProfesor<?php echo $newProfesor['idpersona']; ?>').click(function () {
                                    $('#newProfesor<?php echo $newProfesor['idpersona']; ?>').remove();
                                });
                            </script>
                        <?php } ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </fieldset>


        <div class="form-group">
            <div class="col-xs-12">
                <button type="submit" id="btnSubmit" class="btn btn-lg btn-primary">Grabar</button>
                <button type="button" id="button-cancelar" class="btn btn-lg btn-default">Cancelar</button>
            </div>
        </div>

</form>

<script type="text/javascript">
$("#tipexa").change(function() { //OK
//guardo temporalmente la variabla elegida
sessionStorage.setItem("verexamen-tipexa",$(this).val());
//Solo Final previo no permiten seleccionar comisión pero si el cupo
var bValor = (($(this).val() == '1')||($(this).val() == '6'));
if(bValor==true){
    $("#cupo").show();
    $('#comision').val(0);  
    sessionStorage.setItem("verexamen-comision",0);
    $('#comision').attr('disabled', bValor);
}
else
{
    $("#cupo").hide();
    $('#comision').attr('disabled', bValor);
}
});


$("#carrera").change(function() { //OK
$.ajax({ url: "/examenes/ajaxmaterias/" + $(this).val() 
<?php if($ses->tienePermiso('','Gestion de Examenes Profesor')){ echo " +\"/" . $ses->getIdPersona() . "\""; } ?>, dataType: 'json', success: function(response){
$('#materia').empty();
$('#comision').empty();
$('#materia').append($('<option>').text('SELECCIONAR').attr('value', 0));
$('#comision').append($('<option>').text('SELECCIONAR').attr('value', 0));
$encontre=false;
$.each(response, function(i, value) {
$('#materia').append($('<option>').text(value.nombre).attr('value', value.id));
//Si tengo variable del navegador seteada con la materia la selecciono.
if(sessionStorage.getItem("verexamen-materia")==value.id){
$encontre=true;
$('#materia').val(sessionStorage.getItem("verexamen-materia"));	
}
});
//Si no se encontreo variable seleccionada o bien no hay ninguna dejo seleccionado el SELECCIONAR
if (!$encontre) $('#materia').val(0);    
$("#materia").trigger("change");
}});
//guardo temporalmente la variabla elegida
sessionStorage.setItem("verexamen-carrera",$(this).val());
});
  
 
$("#materia").change(function () { //OK
        $.ajax({url: "/examenes/ajaxcomisiones/" + $(this).val()<?php echo(" +\"/".date("Y"). "\""); ?><?php if($ses->tienePermiso('','Gestion de Examenes Profesor')){ echo " +\"/" . $ses->getIdPersona() . "\"";} ?>, dataType: 'json',
			success: function (response) {
				$('#comision').empty();
				$("#hora").val('00:00');
				$('#comision').append($('<option>').text('SELECCIONAR').attr('value', 0));
				$encontre=false;
				$.each(response, function (i, value) {
					$('#comision').append($('<option>').text(value.nombre).attr('value', value.id));
					if(sessionStorage.getItem("verexamen-comision")==value.id){
						$encontre=true;
						$('#comision').val(sessionStorage.getItem("verexamen-comision"));	
					}
				}); //each
	  //Si no se encontreo variable seleccionada o bien no hay ninguna dejo seleccionado el SELECCIONAR
	if (!$encontre) {
		$('#comision').val(0);    
	}
	$("#comision").trigger("change");
    }});
	  //guardo temporalmente la variabla elegida
	  sessionStorage.setItem("verexamen-materia",$(this).val());
 });
  
$("#comision").change(function () { //OK
	  //guardo temporalmente la variabla elegida
	  sessionStorage.setItem("verexamen-comision",$(this).val());
});


function removeProfesorRow(event) {
	$('#newProfesor' + event.data.a).remove();
}

$('#button-cancelar').click(function () {
        window.location.href = "/gestion-examenes";
});

$('#button-agregar-profesor').click(function () {
        if ($("#profesor option:selected").val() == 0) {
            $("#profesor-required").show();
        } else {
            $("#profesor-required").hide();
            var bEsta = false;

            $('input[name="profesores[]"]').each(function () {
                var parts = $(this).val().split("~**~");
                if (parts[0] == $("#profesor option:selected").val())
                    bEsta = true;
            });

            if (bEsta) {
                $("#profesor-duplicado").show();
            } else {
                $("#profesor-duplicado").hide();
                var newRow = '<tr id="newProfesor' + $("#profesor option:selected").val() + '">';
                newRow += '<input type="hidden" name="profesores[]" value="' + $("#profesor option:selected").val() + '~**~' + $("#profesor option:selected").text() + '" />';
                newRow += '<td>' + $("#profesor option:selected").text() + '</td>';
                newRow += '<td><button type="button" id="quitarProfesor' + $("#profesor option:selected").val() + '">Quitar</button></td>';
                newRow += '</tr>';
                $("#table-profesores > tbody:last-child").append(newRow);
                $('#quitarProfesor' + $("#profesor option:selected").val()).click({a: $("#profesor option:selected").val()}, removeProfesorRow);
            }
        }
})


function validateHora() {
	if (/^([0-9])$/.test($("#hora").val()))
		$("#hora").val("0" + $("#hora").val());
	if (/^([0-1][0-9]|2[0-3])$/.test($("#hora").val()))
		$("#hora").val($("#hora").val() + ":00");
	var isValidHora = /^([0-1][0-9]|2[0-3]):([0-5][0-9])$/.test($("#hora").val());
	if (!isValidHora)
		$("#hora").val("");
}

$("#hora").on('blur', validateHora);

$("#btnSubmit").on('click', function (e) {
	e.preventDefault();
	validateHora();
	$("#daForm").submit();
});

//Si tengo las variables de sesion con datos de haber entrado previamente a esta pagina, cargo los datos.
if(sessionStorage.getItem("verexamen-tipexa")){
			$('#tipexa').val(sessionStorage.getItem("verexamen-tipexa"));
			$("#tipexa").trigger("change");
}
if(sessionStorage.getItem("verexamen-carrera")){
			$('#carrera').val(sessionStorage.getItem("verexamen-carrera"));
			$("#carrera").trigger("change");
}

</script>