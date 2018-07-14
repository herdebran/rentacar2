<style>
input { text-transform: uppercase; }
.small, small { font-size: 82% !important;  }
</style>

<fieldset>
  <h3>Inscriptos al Examen</h3>
  <div class="form-group col-xs-12 col-sm-6 col-lg-4">
    <strong>Tipo de Examen</strong> <?php echo $viewData[0]['tipexa']; ?>
  </div>
  <div class="form-group col-xs-12 col-sm-6 col-lg-4">
    <strong>Materia</strong> <?php echo $viewData[0]['materia']; ?>
  </div>
  <?php if ($viewData[0]['comision']!=''){?>
   <div class="form-group col-xs-12 col-sm-6 col-lg-4">
    <strong>Comisión</strong> <?php echo $viewData[0]['comision']; ?>
  </div>
  <?php } ?>
   <?php if ($viewData[0]['fecha']!=''){?>
   <div class="form-group col-xs-12 col-sm-6 col-lg-4">
    <strong>Fecha</strong> <?php echo $viewData[0]['fecha']; ?>
  </div>
  <?php } ?>
  <?php if ($viewData[0]['aula']!=''){?>
   <div class="form-group col-xs-12 col-sm-6 col-lg-4">
    <strong>Aula</strong> <?php echo $viewData[0]['aula']; ?>
  </div>
  <?php } ?>
  <?php if ($viewData[0]['instrumento']!=''){?>
   <div class="form-group col-xs-12 col-sm-6 col-lg-4">
    <strong>Instrumento</strong> <?php echo $viewData[0]['instrumento']; ?>
  </div>
  <?php } ?>
  <?php if ($viewData[0]['area']!=''){?>
   <div class="form-group col-xs-12 col-sm-6 col-lg-4">
    <strong>Area</strong> <?php echo $viewData[0]['area']; ?>
  </div>
  <?php } ?>
    <?php if ($viewData[0]['cupo']!=0){?>
   <div class="form-group col-xs-12 col-sm-6 col-lg-4">
    <strong>Cupo</strong> <?php echo $viewData[0]['cupo']; ?>
  </div>
  <?php } ?>
  <div class="form-group col-xs-12 col-sm-6 col-lg-4">
    <strong>Inscriptos</strong> <?php echo $viewData[0]['q']; ?>
  </div>
  <div class="form-group">
    <div class="col-xs-12">
      <a href="/gestion-examenes" class="btn btn-primary">Volver</a>
      <?php if($ses->tienePermiso('','Gestion de Examenes Inscriptos Reprocesar')){ ?>
      <?php if ($viewData[0]['idtipoexamen'] != "1") { ?>
      <a href="/examenes/reprocesar/<?php echo $dataIdExamen; ?>"class="btn btn-success pull-right">Reprocesar Inscriptos</a>
      <?php } ?>
      <?php } ?>
    </div>
  </div>
</fieldset>

<fieldset>
  <h3>Alumnos Inscriptos</h3>
    <div class="panel panel-default">
      <div class="panel-body">
        <table class="table table-striped table-hover table-condensed" id="table-resultados">
          <thead>
            <tr>
              <th>#</th>
              <th>Alumno</th>
              <th>Documento</th>
            </tr>
          </thead>
          <tbody>
<?php 
$i=0;
foreach ($viewDataAlumnos as $alu) { 
	$i++;
?>
            <tr>
              <td><?php echo $i; ?></td>
              <td><?php 
			  if ( $alu['estadoalumnomateria'] == "LIBRE" ){  
			  		echo $alu['apellido'] . " " . $alu['nombre'] . " (" . $alu['estadoalumnomateria'].")"; 
			  }
			  else
			  {
				    echo $alu['apellido'] . " " . $alu['nombre']; 
			  }
			  ?></td>
              <td><?php echo $alu['documentonro']; ?></td>
            </tr>
<?php } ?>
          </tbody>
        </table>
      </div>
    </div>
</fieldset>