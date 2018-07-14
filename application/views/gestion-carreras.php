<style>
input { text-transform: uppercase; }
</style>

  <fieldset>
    <h3>Administración de Carrera / Ciclo</h3>
    <p>Seleccione la carrera que desea administrar</p>

      <div class="row spaced-row">
        <label for="carrera" class="col-xs-2 control-label">Carrera / Ciclo</label>
        <div class="col-xs-10">
          <select class="form-control" id="carrera" name="carrera" autofocus>
            <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataCarreras as $carrera) { ?>
            <option value="<?php echo $carrera['idcarrera']; ?>"><?php echo $carrera['descripcion']; ?></option>
<?php } ?>
          </select>
        </div>
      </div>

      <div class="row spaced-row">
        <div class="col-xs-12">
          <button type="button" id="agregar-materia" class="btn btn-success pull-right">Agregar Materia</button>
        </div>
      </div>
  </fieldset>

  <fieldset>
    <h3>Detalle de la Carrera / Ciclo</h3>
      <div class="panel panel-default">
        <div class="panel-body">
          <table class="table table-striped table-hover table-condensed" id="table-resultados">
            <thead>
              <tr>
                <th>Código</th>
                <th>Materia</th>
                <th>Orden</th>
                <th>Promocionable</th>
                <th>Libre</th>
                <th>Activo</th>
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

function AppendRow(ma,an,no,orden,promocionable,libre,es,ac) {
    var newRow = '<tr>';
    newRow += '<td><small>' + ma + '</small></td>';
    newRow += '<td><small>' + no + '</small></td>';
    newRow += '<td><small>' + orden + '</small></td>';
    newRow += '<td><small>' + (promocionable==1 ? 'Si' : 'No') + '</small></td>';
    newRow += '<td><small>' + (libre==1 ? 'Si' : 'No') + '</small></td>';
    newRow += '<td><small>' + (es==1 ? 'Si' : 'No') + '</small></td>';
    newRow += '<td>';
<?php 
if($ses->tienePermiso('','Gestion de Carreras Modificar Materia')){
?>
    newRow += '<a href="/modificar-materia/' + ma +'/' + $('#carrera').val()  +'" class="btn btn-default btn-xs" aria-label="Modificar Materia" title="Modificar Materia"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
<?php 
}
if($ses->tienePermiso('','Gestion de Carreras Activar/desactivar Materia')){
?>
    if (es == '1') {
      newRow += '<a href="/desactivar-materia/' + ma +'/' + $('#carrera').val() + '" class="btn btn-default btn-xs" aria-label="Desactivar" title="Desactivar"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a>';
    } else {
      newRow += '<a href="/activar-materia/' + ma +'/' + $('#carrera').val() + '" class="btn btn-default btn-xs" aria-label="Activar" aria-label="Activar"><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></a>';
    }
<?php } ?>
    newRow += '<a href="/gestion-comisiones/' + $('#carrera').val() + '/' + ma  +'" class="btn btn-default btn-xs" aria-label="Ver Comisiones" title="Ver Comisiones"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>';
    newRow += '</td>';
    newRow += '</tr>';
    $("#table-resultados > tbody:last-child").append(newRow);
  }
  function LoadCarrera() {
    if ($('#carrera').val()!=0) {
      $.ajax({ url: "/carreras/ajaxmateriascarrera/" + $('#carrera').val(), method: 'POST', dataType: 'json', success: function(response){
        if (response.rows.length==0) {
          $("#table-resultados > tbody:last-child").append('<tr><td colspan=8>No se encontraron materias para la carrera seleccionada</td></tr>');
        } else {
          var lastMateria="";
          var lastAnio="";
          var lastNombre="";
          var lastEstado="";
          var orden="";
          var promocionable="";
          var libre="";
          
          $.each(response.rows, function(i, value) {
            bCambio = false;
            if (lastMateria == "") lastMateria = value.idmateria;<?php /* la primera vez no marco cambio */ ?>
            if (lastMateria!=(value.idmateria) ) bCambio = true;
            if (bCambio) { 
              AppendRow(lastMateria, lastAnio, lastNombre,orden, promocionable,libre,lastEstado);
              lastMateria = value.idmateria;
            }
            
            lastAnio = value.anio;
            lastNombre = value.nombre;
            orden=value.orden;
            promocionable=value.promocionable;
            libre=value.libre;
            lastEstado = value.estado;
          });
          AppendRow(lastMateria, lastAnio, lastNombre,orden,promocionable,libre, lastEstado);
        }    
      }});
    }    

  }
  $("#carrera").change(function() {
    $('#table-resultados > tbody').empty();
    LoadCarrera();
    //guardo temporalmente la variabla elegida
    sessionStorage.setItem("gestioncarrera-carrera",$(this).val());	
  });
  
  $("#agregar-materia").click(function() {
    if ($('#carrera').val()>0) window.location.href = "/agregarmateria/" + $('#carrera').val();
  });
  
 //Si tengo las variables de sesion con datos de haber entrado previamente a esta pagina, cargo los datos.
if(sessionStorage.getItem("gestioncarrera-carrera")){
			$('#carrera').val(sessionStorage.getItem("gestioncarrera-carrera"));
			$("#carrera").trigger("change");
}
  
</script>