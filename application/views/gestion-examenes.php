<style>
input { text-transform: uppercase; }
.small, small { font-size: 82% !important;  }
</style>

<form class="form-horizontal" action="" method="POST" accept-charset="utf-8">  
  <fieldset>
    <h3>Búsqueda de Exámenes</h3>
    <div class="form-group col-xs-12 col-sm-6">
      <label for="ciclo" class="col-xs-4 control-label">Periodo</label>
      <div class="col-xs-8">
        <select class="form-control" id="ciclo" name="ciclo" autofocus="on">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataCiclos as $ciclo) { ?>
          <option value="<?php echo $ciclo['ciclo']; ?>" ><?php echo $ciclo['ciclo']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>
    
    <div class="form-group col-xs-12 col-sm-6">
      <label for="tipexa" class="col-xs-4 control-label">Tipo Exámen</label>
      <div class="col-xs-8">
        <select class="form-control" id="tipexa" name="tipexa" autofocus="on">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataTipoExamen as $tipexa) { ?>
          <option value="<?php echo $tipexa['id']; ?>" ><?php echo $tipexa['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>


    <div class="form-group col-xs-12 col-sm-6">
      <label for="carrera" class="col-xs-4 control-label">Carrera / Ciclo</label>
      <div class="col-xs-8">
        <select class="form-control" id="carrera" name="carrera">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataCarreras as $carrera) { ?>
          <option value="<?php echo $carrera['id']; ?>"  <?php if ($carrera['id'] == $dataIdCarrera) echo "selected"; ?>><?php echo $carrera['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6">
      <label for="materia" class="col-xs-4 control-label">Materia</label>
      <div class="col-xs-8">
        <select class="form-control" id="materia" name="materia">
          <option value="0">SELECCIONAR</option>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6">
      <label id="lblcomision" for="comision" class="col-xs-4 control-label">Comisión</label>
      <div class="col-xs-8">
        <select class="form-control" id="comision" name="comision">
          <option value="0">SELECCIONAR</option>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6">
      <label id="lblFinalizado" for="finalizado" class="col-xs-4 control-label">Finalizado</label>
      <div class="col-xs-8">
        <select class="form-control" id="finalizado" name="finalizado">
          <option value="0">SELECCIONAR</option>
          <option value="-1">Exámen Finalizado</option>
          <option value="1">Exámen No Finalizado</option>
        </select>
      </div>
    </div>


    <div class="form-group">
      <div class="col-xs-12">
        <button id="btnBuscar" type="button" class="btn btn-primary">Buscar</button>
        <a href="/examenes/crear" id="btnCrear" class="btn btn-success pull-right">Crear Exámen</a>
      </div>
    </div>
  </fieldset>
</form>

<fieldset>
  <h3>Resultados</h3>
    <div class="panel panel-default">
      <div class="panel-body">
        <table class="table table-striped table-hover table-condensed" id="table-resultados">
          <thead>
            <tr>
              <th>Id</th>
              <th>Materia</th>
              <th>Comisión</th>
              <th>Instrumento</th>
              <th>Tipo Exámen</th>
              <th>Fecha</th>
              <th>Finalizado</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>

          </tbody>
        </table>
      </div>
    </div>
</fieldset>

<script type="text/javascript">
  $("#carrera").change(function() {
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
			if(sessionStorage.getItem("gestionexamen-materia")==value.id){
			$encontre=true;
			$('#materia').val(sessionStorage.getItem("gestionexamen-materia"));	
			}
      });
	  //Si no se encontreo variable seleccionada o bien no hay ninguna dejo seleccionado el SELECCIONAR
		if (!$encontre) $('#materia').val(0);    
		$("#materia").trigger("change");
    }});
	//guardo temporalmente la variabla elegida
	  sessionStorage.setItem("gestionexamen-carrera",$(this).val());
  });

  $("#materia").change(function() {
     
    $.ajax({ url: "/examenes/ajaxcomisiones/" + $(this).val() + "/" + $('#ciclo').val()<?php if($ses->tienePermiso('','Gestion de Examenes Profesor')){ echo " +\"/" . $ses->getIdPersona() . "\""; } ?>, dataType: 'json', success: function(response){
      $('#comision').empty();
      $('#comision').append($('<option>').text('SELECCIONAR').attr('value', 0));
	  
	  $encontre=false;
      $.each(response, function(i, value) {
        $('#comision').append($('<option>').text(value.nombre).attr('value', value.id));
		if(sessionStorage.getItem("gestionexamen-comision")==value.id){
				$encontre=true;
				$('#comision').val(sessionStorage.getItem("gestionexamen-comision"));	
		}
      });
	  //Si no se encontreo variable seleccionada o bien no hay ninguna dejo seleccionado el SELECCIONAR
	if (!$encontre) $('#comision').val(0);    
	$("#comision").trigger("change");  
    }});
	  //guardo temporalmente la variabla elegida
	  sessionStorage.setItem("gestionexamen-materia",$(this).val());
  });
  
  $("#comision").change(function() {
	  	  //guardo temporalmente la variabla elegida
	  sessionStorage.setItem("gestionexamen-comision",$(this).val());
	  $("#btnBuscar").trigger("click");
	 });

  $("#btnBuscar").click(function() {

    $('#table-resultados > tbody').empty();
	
    $.ajax({ url: "/examenes/ajaxexamenes/" + $('#ciclo').val() + "/" + $('#tipexa').val() + "/" + $('#carrera').val() + "/" + $('#materia').val() + "/" + $('#comision').val() + "/" + $('#finalizado').val() <?php if($ses->tienePermiso('','Gestion de Examenes Profesor')){ echo " +\"/" . $ses->getIdPersona() . "\""; } ?>, dataType: 'json', success: function(response){
      if (response.length==0) {
        $("#table-resultados > tbody:last-child").append('<tr><td colspan=7>No hay resultados que coincidan con los criterios de busqueda</td></tr>');
      } else {
        $.each(response, function(i, value) {
          newRow = '<tr>';
          newRow += '<td><small>' + value.idexamen + '</small></td>';
          newRow += '<td><small>' + value.materia + '</small></td>';
          newRow += '<td><small>' + (value.comision == null ? "" : value.comision) + '</small></td>';
          newRow += '<td><small>' + (value.instrumento == null ? "" : value.instrumento) + '</small></td>';
          newRow += '<td><small>' + value.tipexa + '</small></td>';
          newRow += '<td><small>' + value.fecha + '</small></td>';
          if (value.examenfinalizado == '1') newRow += '<td><small>Si</small></td>'; else newRow += '<td><small>No</small></td>';
          newRow += '<td>';
		  <?php if($ses->tienePermiso('','Gestion de Examenes Agregar o Modificar')){ ?>
          newRow += '<a id="eliminar_examen_' + value.idexamen +'" href="/examenes/eliminar/' + value.idexamen +'" class="btn btn-default btn-xs" aria-label="Eliminar Exámen" title="Eliminar Exámen"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a>';
		  <?php } ?>
		   <?php if($ses->tienePermiso('','Gestion de Examenes Notas')){ ?>
          if (value.examenfinalizado == '1') {
            newRow += '<a href="/examenes/cargarnotas/' + value.idexamen +'" class="btn btn-default btn-xs" aria-label="Ver Notas Exámen" title="Ver Notas Exámen"><span class="glyphicon glyphicon-saved" aria-hidden="true"></span></a>';
          } else {
            newRow += '<a href="/examenes/cargarnotas/' + value.idexamen +'" class="btn btn-default btn-xs" aria-label="Cargar Notas Exámen" title="Cargar Notas Exámen"><span class="glyphicon glyphicon-saved" aria-hidden="true"></span></a>';
          }
          /**
           * Armo un arreglo con los id de los examenes finalez.
           * Verifico si el idtipoexamen existe en el arreglo.
           * si existe muestro el boton para generar el acta de final,
           * caso contrario no.
           */
          var examenesFinales = ["1", "2", "6"];
          var posTipoExamen = examenesFinales.indexOf(value.idtipoexamen);
          if(value.examenfinalizado == '1' && posTipoExamen > -1){
              newRow += '<a href="/examenes/imprimirActaFinal/' + value.idexamen +'" class="btn btn-default btn-xs" aria-label="Imprimir Acta de Final" title="Imprimir Acta de Final"><span class="glyphicon glyphicon-file" aria-hidden="true"></span></a>';
          }
		  <?php } ?>
          <?php if($ses->tienePermiso('','Gestion de Examenes Agregar o Modificar')){ ?> 
              if (value.examenfinalizado == '0'){ newRow += '<a id="finalizar_examen_' + value.idexamen +'" href="/examenes/finalizar/' + value.idexamen +'" class="btn btn-default btn-xs" aria-label="Finalizar exámen" title="Finalizar exámen"><span class="glyphicon glyphicon-off" aria-hidden="true"></span></a>';}
              if (value.examenfinalizado == '1'){ newRow += '<a id="desfinalizar_examen_' + value.idexamen +'" href="/examenes/deshacerfinalizar/' + value.idexamen +'" class="btn btn-default btn-xs" aria-label="Deshacer Finalizar exámen" title="Deshacer Finalizar exámen"><span class="glyphicon glyphicon-off" aria-hidden="true"></span></a>';}
                  <?php  } ?>
		  <?php  if($ses->tienePermiso('','Gestion de Examenes Inscriptos')){ ?>
          if (value.examenfinalizado == '0') {
           newRow += '<a href="/examenes/inscriptos/' + value.idexamen +'" class="btn btn-default btn-xs" aria-label="Inscriptos al exámen" title="Inscriptos al exámen"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></a>';}
		  <?php  } ?>
          newRow += '</td>';
          
          newRow += '</tr>';

          $("#table-resultados > tbody:last-child").append(newRow);
          $("#finalizar_examen_" + value.idexamen)
              .click(function(event){
                  var texto = "Esta seguro de querer finalizar el examen?, al finalizarlo no podrá modificar o agregar notas a los alumnos. Las notas de los mismos seran publicadas en sus analiticos. Desea continuar?";
                  if (!confirm(texto)) {
                       event.preventDefault();
                  }
            });
          $("#desfinalizar_examen_" + value.idexamen)
              .click(function(event){
                  var texto = "Esta seguro de querer deshacer finalizar el examen?. Desea continuar?";
                  if (!confirm(texto)) {
                       event.preventDefault();
                  }
            });
    $("#eliminar_examen_" + value.idexamen)
             .click(function(event){
                  var texto = "Esta seguro de querer eliminar el examen?, solo es posible eliminar un examen que no este finalizado o bien no tenga aun información en sus inscriptos. Desea continuar?";
                  if (!confirm(texto)) {
                       event.preventDefault();
                  }
            });
        });
      }
    }});

  });

$("#ciclo").change(function() {
//guardo temporalmente la variabla elegida
sessionStorage.setItem("gestionexamen-ciclo",$(this).val());
});
	
$("#tipexa").change(function() {
//guardo temporalmente la variabla elegida
sessionStorage.setItem("gestionexamen-tipexa",$(this).val());
//Solo Final previo no permiten seleccionar comisión
var bValor = (($(this).val() == '1')||($(this).val() == '6'));

if(bValor==true){
$('#comision').empty();
$('#comision').append($('<option>').text('SELECCIONAR').attr('value', 0));
$('#comision').attr('disabled', bValor);
$('#lblcomision').attr('disabled', bValor);
}
else
{
$('#comision').empty();
$('#comision').append($('<option>').text('SELECCIONAR').attr('value', 0));
$('#comision').attr('disabled', bValor);
$('#lblcomision').attr('disabled', bValor);
}
});

$("#finalizado").change(function() {
//guardo temporalmente la variabla elegida
sessionStorage.setItem("gestionexamen-finalizado",$(this).val());
}); 
	  
	  
	  
//Si tengo las variables de sesion con datos de haber entrado previamente a esta pagina, cargo los datos.
if(sessionStorage.getItem("gestionexamen-ciclo")){
	$('#ciclo').val(sessionStorage.getItem("gestionexamen-ciclo"));
}
else
{
	var d = new Date();
	var year = d.getFullYear();
	$('#ciclo').val(year);
}

if(sessionStorage.getItem("gestionexamen-tipexa")){
			$('#tipexa').val(sessionStorage.getItem("gestionexamen-tipexa"));
			$("#tipexa").trigger("change");
}
if(sessionStorage.getItem("gestionexamen-finalizado")){
			$('#finalizado').val(sessionStorage.getItem("gestionexamen-finalizado"));
}else{$('#finalizado').val(1);}

if(sessionStorage.getItem("gestionexamen-carrera")){
			$('#carrera').val(sessionStorage.getItem("gestionexamen-carrera"));
			$("#carrera").trigger("change");
}


</script>