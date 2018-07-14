<?php 

?>
 <style>
input { text-transform: uppercase; }
.small, small { font-size: 82% !important;  }
</style>

<form class="form-horizontal" action="" method="POST" accept-charset="utf-8">  
  <fieldset>
    <h3>Analítico</h3>
    <?php if(count($viewDataCarreras)>0){ //Si el alumno tiene alguna carrera ?>
    <div class="form-group col-xs-12">
      <label for="carrera" class="col-xs-2 control-label">Carrera / Ciclo</label>
      <div class="col-xs-10">
        <select class="form-control" id="carrera" name="carrera" 
		<?php 
		//Si rol Profesor muestro readonly.
		if ($rolActual == $this->POROTO->Config['rol_profesor_id']) echo "readonly disabled"; 
		?>>
		<?php foreach ($viewDataCarreras as $carrera) { ?>
          <option value="<?php echo $carrera['idalumnocarrera']; ?>" <?php if ($dataIdAlumnoCarrera==$carrera['idalumnocarrera']) echo "selected"; ?>><?php 
		  if($carrera['instrumento']!=""){
		  	echo $carrera['descripcion']. " - " . $carrera['instrumento']; 
		  }
		  else
		  {
			  echo $carrera['descripcion']; 
			}
		?></option>
        <?php } ?>
        </select>
      </div>
    </div>
   
    
    <div class="form-group col-xs-12">
      <label for="alumno" class="col-xs-2 control-label">Alumno</label>
      <div class="col-xs-10">
        <input type="text" class="form-control" name="alumno" value="<?php echo $nombreAlumno; ?>" readonly />
      </div>
    </div>
 <?php } //If viewdatacarreras 
    else{
        echo("No tiene ninguna carrera asignada.<br>");
    }
    
if($ses->tienePermiso('','Analitico modificar estado Carrera')){
	?>
    <div class="form-group col-xs-12">
    <label for="estadocarrera" class="col-xs-2 control-label">Estado Actual</label>
    <div class="col-xs-10">
    <input type="text" class="form-control" name="estadocarrera" value="<?php echo $viewDataEstado[0]['descripcion']; ?>" readonly />
    <?php if ($viewDataEstado[0]['descripcion']=="FINALIZADA") echo("La carrera actual esta finalizada, no será posible editar los estados de las materias.");?>
    </div>
    </div>
    <div class="form-group col-xs-12">
      <label for="estadocarrera" class="col-xs-2 control-label">Cambiar Estado A</label>
      <div class="col-xs-4">
        <select class="form-control" id="nuevoestado" name="nuevoestado">
		<?php foreach ($viewDataEstadosAlumnoCarrera as $eac) { ?>
          <option value="<?php echo $eac['id']; ?>" ><?php echo $eac['descripcion']; ?></option>
		<?php } ?>
        </select>
      </div>
      <div class="col-xs-3">
          <label for="sin">
            <input type="checkbox" id="sinvalidaraprobaciones" name="sinvalidaraprobaciones" value="sinvalidaraprobaciones"> Sin validar aprobaciones
          </label>
      </div>
      <div class="col-xs-3">
          <input type="button" id="btnCambiar" class="btn btn-warning" value="Cambiar" />&nbsp;<button type="button" id="btnVolver" class="btn btn-primary">Volver</button>
        
      </div>
    </div>
    <?php } //si tiene permiso de modificar estado Carrera 
	?>
    <?php 
	if($ses->tienePermiso('','Analitico Imprimir Constancia')){
	?>
     <div class="form-group col-xs-12">
     <input type="button" id="btnImprimir" class="btn btn-info" value="Imprimir Constancia" />
     </div>
     <?php } 
	?>
  </fieldset>
  





</form>
<?php 
if(count($viewDataCarreras)>0){ //Si el alumno tiene alguna carrera
?>
  <fieldset>
      <div class="panel panel-default">
        <div class="panel-body">
          <table class="table table-striped table-hover table-condensed" id="table-alumnos">
            <thead>
              <tr>
                <th>Materia</th>
                <th>Año Cursada</th>
                <th>Tipo Exámen</th>
                <th>Fecha Aprob.</th>
                <th>Nota</th>
                <th>Libro</th>
                <th>Tomo</th>
                <th>Folio</th>
                <th>Estado</th>
<?php  if($ses->tienePermiso('','Analitico modificar estado materias') && ($viewDataEstado[0]['descripcion']=="EN CURSO" || $viewDataEstado[0]['descripcion']=="PRE FINALIZADA")){ ?>
                <th>Acciones</th>
<?php   } ?>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($viewData as $rec) { ?>
                <tr   
                <?php if ($rec['condicional']=="1" || $rec['condicionalregla6']=="1"){?>class="warning"<?php } ?>
                >
                <td><small><b><?php echo $rec['nombre'];?></b></small>
				<?php  
				if ($rec['comision']!="") echo("<br/><p  style='font-size:9px'>(". $rec['comision']. ")</p>");
                                ?>
                </td>
                <td><small><?php echo $rec['aniocursada']; ?></small></td>
                <td><small><?php echo $rec['tipoexamen']; ?></small></td>
                <td><small><?php echo $rec['fechaaprobacion']; ?></small></td>
                <td><small><?php echo $rec['notaexamen']; ?></small></td>
                <td><small><?php echo $rec['libro']; ?></small></td>
                <td><small><?php echo $rec['tomo']; ?></small></td>
                <td><small><?php echo $rec['folio']; ?></small></td>
                <td><small><?php echo $rec['estado']; ?></small>
                <?php if ($rec['condicional']=="1") echo("<br><small>(CONDICIONAL)</small>"); ?>
                <?php if ($rec['condicionalregla6']=="1") echo("<br><small>(CONDICIONAL SIMULT.)</small>"); ?>
                </td>
<?php  if($ses->tienePermiso('','Analitico modificar estado materias') && ($viewDataEstado[0]['descripcion']=="EN CURSO" || $viewDataEstado[0]['descripcion']=="PRE FINALIZADA")){ ?>
                <td><small>
<!-- CURSANDO -->
<?php if ($rec['idestado'] != $this->POROTO->Config['estado_alumnomateria_cursando']) { ?>
<a onclick="return confirm('¿Esta seguro de cambiar el estado de la materia a CURSANDO?')" href="/analiticoalumno/setestadoalumnomateria/<?php echo $dataIdAlumnoCarrera; ?>/<?php echo $dataIdAlumno; ?>/<?php echo $rec['idalumnomateria']; ?>/2" class="btn btn-default btn-xs" aria-label="Cambiar estado a Cursando" title="Cambiar estado a Cursando"><span class="glyphicon glyphicon-play" aria-hidden="true"></span></a>
<?php } ?>
<!-- CURSADA APROBADA -->
<?php if ($rec['idestado'] != $this->POROTO->Config['estado_alumnomateria_cursadaaprobada']) { ?><a onclick="return confirm('¿Esta seguro de cambiar el estado de la materia a CURSADA APROBADA?')" href="/analiticoalumno/setestadoalumnomateria/<?php echo $dataIdAlumnoCarrera; ?>/<?php echo $dataIdAlumno; ?>/<?php echo $rec['idalumnomateria']; ?>/5" class="btn btn-default btn-xs" aria-label="Cambiar estado a Cursada Aprobada" title="Cambiar estado a Cursada Aprobada"><span class="glyphicon glyphicon-star-empty" aria-hidden="true"></span></a><?php } ?>
<!-- APROBADA -->
<?php if ($rec['idestado'] != $this->POROTO->Config['estado_alumnomateria_aprobada']) { ?><a onclick="return confirm('¿Esta seguro de cambiar el estado de la materia a APROBADA?')" href="/analiticoalumno/setestadoalumnomateria/<?php echo $dataIdAlumnoCarrera; ?>/<?php echo $dataIdAlumno; ?>/<?php echo $rec['idalumnomateria']; ?>/3" class="btn btn-default btn-xs" aria-label="Cambiar estado a Aprobada" title="Cambiar estado a Aprobada"><span class="glyphicon glyphicon-star" aria-hidden="true"></span></a><?php } ?>
<!-- APROBADA POR EQUIVALENCIAS -->
<?php if ($rec['idestado'] != $this->POROTO->Config['estado_alumnomateria_aprobadaxequiv']) { ?><a onclick="return confirm('¿Esta seguro de cambiar el estado de la materia a APROBADA POR EQUIVALENCIAS?')" href="/analiticoalumno/setestadoalumnomateria/<?php echo $dataIdAlumnoCarrera; ?>/<?php echo $dataIdAlumno; ?>/<?php echo $rec['idalumnomateria']; ?>/4" class="btn btn-default btn-xs" aria-label="Cambiar estado a Aprobada por Equivalencia" title="Cambiar estado a Aprobada por Equivalencia"><span class="glyphicon glyphicon-random" aria-hidden="true"></span></a><?php } ?>
<!-- APROBADA POR NIVELACION -->
<?php if ($rec['idestado'] != $this->POROTO->Config['estado_alumnomateria_nivelacion']) { ?><a onclick="return confirm('¿Esta seguro de cambiar el estado de la materia a APROBADA POR NIVELACIÓN?')" href="/analiticoalumno/setestadoalumnomateria/<?php echo $dataIdAlumnoCarrera; ?>/<?php echo $dataIdAlumno; ?>/<?php echo $rec['idalumnomateria']; ?>/7" class="btn btn-default btn-xs" aria-label="Cambiar estado a Aprobada por nivelación" title="Cambiar estado a Aprobada por nivelación"><span class="glyphicon glyphicon-signal" aria-hidden="true"></span></a><?php } ?>
<!-- LIBRE -->
<?php if ($rec['idestado'] != $this->POROTO->Config['estado_alumnomateria_libre']) { ?><a onclick="return confirm('¿Esta seguro de cambiar el estado de la materia a LIBRE?')" href="/analiticoalumno/setestadoalumnomateria/<?php echo $dataIdAlumnoCarrera; ?>/<?php echo $dataIdAlumno; ?>/<?php echo $rec['idalumnomateria']; ?>/10" class="btn btn-default btn-xs" aria-label="Cambiar estado a Libre" title="Cambiar estado a Libre"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></a><?php } ?>
<!-- DESAPROBADA -->
<?php if ($rec['idestado'] != $this->POROTO->Config['estado_alumnomateria_desaprobada']) { ?><a onclick="return confirm('¿Esta seguro de cambiar el estado de la materia a DESAPROBADA?')" href="/analiticoalumno/setestadoalumnomateria/<?php echo $dataIdAlumnoCarrera; ?>/<?php echo $dataIdAlumno; ?>/<?php echo $rec['idalumnomateria']; ?>/9" class="btn btn-default btn-xs" aria-label="Cambiar estado a Desaprobada" title="Cambiar estado a Desaprobada"><span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span></a><?php } ?>
<!-- CANCELADA -->
<?php if ($rec['idestado'] != $this->POROTO->Config['estado_alumnomateria_cancelada']) { ?><a onclick="return confirm('¿Esta seguro de cambiar el estado de la materia a CANCELADA?')" href="/analiticoalumno/setestadoalumnomateria/<?php echo $dataIdAlumnoCarrera; ?>/<?php echo $dataIdAlumno; ?>/<?php echo $rec['idalumnomateria']; ?>/6" class="btn btn-default btn-xs" aria-label="Cambiar estado a Cancelada" title="Cambiar estado a Cancelada"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a><?php } ?>
<!-- EDITAR ANALITICO -->
<?php if($ses->tienePermiso('','Analitico Edición')){ ?><a href="/analitico-modificar/<?php echo $rec['idalumnomateria']; ?>" class="btn btn-default btn-xs" aria-label="Editar Datos del Analítico" title="Editar Datos del Analítico"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a><?php } ?>
                </small></td>
  <?php } else { ?>
                <td><small>
                </small></td>
  <?php }  //del estado permite modificar materias. ?>
               
              </tr>
              <?php } ?>            
            </tbody>
          </table>
        </div>
      </div>
  </fieldset>
<?php } //If tiene alguna carrera?>

<script type="text/javascript">
  $("#carrera").change(function() {
    window.location.href = '/analitico/' + $(this).val() +'/<?php echo $dataIdAlumno; ?>';
  });
  
<?php  if($ses->tienePermiso('','Analitico modificar estado Carrera')){ ?>
  $('#btnCambiar').click(function() {
   var texto = "¿Esta seguro de querer modificar el estado de la carrera para el alumno?. En el caso de pasarla a finalizada, debe tener todas las materias aprobadas. Una vez finalizada una Carrera no se podrán modificar las materias.";
   if (confirm(texto)) window.location.href = '/cambiarestadocarrera/<?php echo $dataIdAlumno; ?>/<?php echo $dataIdAlumnoCarrera; ?>/' + $('#nuevoestado').val() + '/' + document.getElementById('sinvalidaraprobaciones').checked;
  });
<?php } ?>


 $('#btnImprimir').click(function() {
	  window.location.href = '/analitico/' + $("#carrera").val() +'/<?php echo $dataIdAlumno; ?>/SI';
	 });
 
$("#btnVolver").click(function () {
    window.history.back();
    });
         
</script>