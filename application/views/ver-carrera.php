<?php
$materiaValue = isset($_POST['materia']) ? $_POST['materia'] : $dataIdMateria;
$tipmatValue = isset($_POST['tipmat']) ? $_POST['tipmat'] : $viewData[0]['idtipomateria'];
$nombreValue = isset($_POST['nombre']) ? $_POST['nombre'] : $viewData[0]['nombre'];
$descripcionValue = isset($_POST['descripcion']) ? $_POST['descripcion'] : $viewData[0]['descripcion'];
$anioValue = isset($_POST['anio']) ? $_POST['anio'] : $viewData[0]['anio'];
$promocionableValue = isset($_POST['promocionable']) ? $_POST['promocionable'] : $viewData[0]['promocionable'];
$cantidadmodulosValue = isset($_POST['cantidadmodulos']) ? $_POST['cantidadmodulos'] : $viewData[0]['cantidadmodulos'];
$ordenValue = isset($_POST['orden']) ? $_POST['orden'] : $viewData[0]['orden'];
$libreValue = isset($_POST['libre']) ? $_POST['libre'] : $viewData[0]['libre'];
// Cambios Lautaro 17/05/2018
$aceptafinalLibre = isset($_POST['final_libre']) ? $_POST['final_libre'] : $viewData[0]['final_libre'];
$aceptafinalRegular = isset($_POST['final_regular']) ? $_POST['final_regular'] : $viewData[0]['final_regular'];
$aceptafinalPrevio = isset($_POST['final_previo']) ? $_POST['final_previo'] : $viewData[0]['final_previo'];
$materiaConInstrumento = isset($_POST['materia_instrumento']) ? $_POST['materia_instrumento'] : $viewData[0]['materia_instrumento'];
$materiaConGenero = isset($_POST['materia_genero']) ? $_POST['materia_genero'] : $viewData[0]['materia_genero'];
// Fin cambios Lautaro 17/05/2018
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

    <div class="form-group col-xs-12">
      <label for="carrera" class="col-xs-2 control-label">Carrera / Ciclo</label>
      <div class="col-xs-10">
        <input type="text" class="form-control" id="carrera" name="carrera" placeholder="" maxlength="45" value="<?php echo $dataDescripcionCarrera; ?>" disabled />
      </div>
    </div>

    <div class="form-group col-xs-12 <?php if (array_key_exists('materia', $validationErrors)) echo "has-error"; ?>">
      <label for="materia" class="col-xs-2 control-label">Materia</label>
      <div class="col-xs-10">
        <select class="form-control" id="materia" name="materia" <?php if ($dataIdMateria==0) echo "autofocus"; else echo "disabled"; ?> >
          <option value="0">NUEVA MATERIA</option>
<?php foreach ($viewDataMaterias as $materia) { ?>
          <option value="<?php echo $materia['idmateria']; ?>" <?php if ($materiaValue == $materia['idmateria']) echo "selected"; ?>><?php echo $materia['nombre']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 required <?php if (array_key_exists('nombre', $validationErrors)) echo "has-error"; ?>">
      <label for="nombre" class="col-xs-2 control-label">Nombre</label>
      <div class="col-xs-10">
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="" maxlength="100" value="<?php echo $nombreValue; ?>" <?php /* solo-validacoines-post required */ ?> autocomplete="off" <?php if ($dataIdMateria>0) echo "autofocus"; ?> />
      </div>
    </div>

    <div class="form-group col-xs-12 <?php if (array_key_exists('descripcion', $validationErrors)) echo "has-error"; ?>">
      <label for="descripcion" class="col-xs-2 control-label">Descripción</label>
      <div class="col-xs-10">
        <textarea class="form-control" id="descripcion" name="descripcion" rows="3" maxlength="500" placeholder=""><?php echo $descripcionValue; ?></textarea>
      </div>
    </div>

    <div class="form-group col-xs-12 required <?php if (array_key_exists('anio', $validationErrors)) echo "has-error"; ?>">
      <label for="anio" class="col-xs-2 control-label">Año/Nivel</label>
      <div class="col-xs-10">
        <input type="text" maxlength="20" class="form-control" id="anio" name="anio" placeholder="" value="<?php echo $anioValue; ?>" autocomplete="off" >
      </div>
    </div>

    <div class="form-group col-xs-12 required <?php if (array_key_exists('tipmat', $validationErrors)) echo "has-error"; ?>">
      <label for="tipmat" class="col-xs-2 control-label">Tipo</label>
      <div class="col-xs-10">
        <select class="form-control" id="tipmat" name="tipmat">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataTipoMateria as $tipmat) { ?>
          <option value="<?php echo $tipmat['idtipomateria']; ?>" <?php if ($tipmatValue == $tipmat['idtipomateria']) echo "selected"; ?>><?php echo $tipmat['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 required <?php if (array_key_exists('promocionable', $validationErrors)) echo "has-error"; ?>">
      <div class="col-sm-offset-2 col-sm-10">
        <div class="checkbox">
          <label for="promocionable">
            <input type="checkbox" id="promocionable" name="promocionable" value="promocionable" <?php if ($promocionableValue == '1') echo "checked"; ?>> Promocionable
          </label>
          </div>
                  <div class="checkbox">
          <label for="libre">
            <input type="checkbox" id="libre" name="libre" value="libre" <?php if ($libreValue == '1') echo "checked"; ?>> Permitir Matricularse LIBRE (Sin comisión)
          </label>
        </div>
        <div class="checkbox">
            <label for="materia_instrumento">
                <input type="checkbox" id="materia_instrumento" name="materia_instrumento" value="materia_instrumento" <?php if ($materiaConInstrumento == '1') echo "checked"; ?>> Tiene Instrumentos
            </label>
        </div>
        <div class="checkbox">
            <label for="materia_genero">
                <input type="checkbox" id="materia_genero" name="materia_genero" value="materia_genero" <?php if ($materiaConGenero == '1') echo "checked"; ?>> Aplica Generos
            </label>
        </div>
        <div class="checkbox">
            <label for="final_regular">
                <input type="checkbox" id="final_regular" name="final_regular" value="final_regular" <?php if ($aceptafinalRegular == '1') echo "checked"; ?>> Acepta Final Regular
            </label>
        </div>
        <div class="checkbox">
            <label for="final_previo">
                <input type="checkbox" id="final_previo" name="final_previo" value="final_previo" <?php if ($aceptafinalPrevio == '1') echo "checked"; ?>> Acepta Final Previo
            </label>
        </div>
        <div class="checkbox">
            <label for="final_libre">
                <input type="checkbox" id="final_libre" name="final_libre" value="final_libre" <?php if ($aceptafinalLibre == '1') echo "checked"; ?>> Acepta Final Libre
            </label>
        </div>
      </div>
    </div>

    <div class="form-group col-xs-12 <?php if (array_key_exists('cantidadmodulos', $validationErrors)) echo "has-error"; ?>">
      <label for="cantidadmodulos" class="col-xs-2 control-label">Cantidad Módulos</label>
      <div class="col-xs-10">
        <input type="number" min="0" max="500" class="form-control" id="cantidadmodulos" name="cantidadmodulos" placeholder="" value="<?php echo $cantidadmodulosValue; ?>" >
      </div>
    </div>

    <div class="form-group col-xs-12 <?php if (array_key_exists('orden', $validationErrors)) echo "has-error"; ?>">
      <label for="orden" class="col-xs-2 control-label">Orden (Para ordenar las mat.)</label>
      <div class="col-xs-10">
        <input type="number" min="0" max="1000" class="form-control" id="orden" name="orden" placeholder="" value="<?php echo $ordenValue; ?>" >
      </div>
    </div>
    
  </fieldset>


  <fieldset>
    <h3>Correlatividades</h3>
<?php if ($dataIdMateria==0) { ?>
    <p>Una vez creada la materia, podra editarla y administrar sus correlativas</p>
<?php } else { ?>
    <p>Aqui se visualizan las correlativas definidas para esta materia</p>
      <div class="form-group col-xs-11">
        <label for="profesor" class="col-xs-2 control-label">Tipo Regla</label>
        <div class="col-xs-10">
	<select class="form-control" id="profesor" name="profesor">
              <option value="0">SELECCIONAR</option>
                <?php foreach ($viewDataReglas as $tiporegla) { ?>
                          <option value="<?php echo $tiporegla['id']; ?>"><?php echo $tiporegla['descripcion']; ?></option>
                <?php } ?>
	</select>
        </div>
      </div>
      <div class="form-group col-xs-1">
        <button type="button" id="button-regla" class="btn btn-info pull-right" data-toggle="modal" data-target=".nueva-regla-cc-modal">Agregar</button>
      </div>
      <div class="clearfix"></div>
      <div class="panel panel-default">
        <div class="panel-body  <?php if (array_key_exists('profesores', $validationErrors)) echo "bg-danger"; ?>">
    <div class="row spaced-row">
      <div class="col-xs-12">
      </div>
    </div>
    <table class="table" id="table-correlativas">
      <thead>
        <tr>
          <th>Regla</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <tr>
        <td colspan=2>Sin registros</td>
        </tr>
      </tbody>
    </table>
  </fieldset>
<?php } //OJO  ?>
  <div class="form-group">
    <div class="col-xs-12">
      <button type="submit" class="btn btn-lg btn-primary">Grabar</button>
      <button type="button" id="button-cancelar" class="btn btn-lg btn-default">Cancelar</button>
    </div>
  </div>

</form>



<div class="modal fade nueva-regla-cc-modal" tabindex="-1" role="dialog" aria-labelledby="nueva-regla-cc-title">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="nueva-regla-cc-title">Nueva regla - Carrera Completa</h4>
      </div>
      
      <div class="modal-body">
        <div class="alert alert-danger collapse" id="nueva-regla-cc-validations" role="alert"></div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-12 required">
            <label for="cc-carrera" class="col-xs-3 control-label">Carrera</label>
            <div class="col-xs-9">
              <select class="form-control" id="cc-carrera" name="cc-carrera">
<?php foreach ($viewDataCarreras as $carrera) { ?>
                <option value="<?php echo $carrera['idcarrera']; ?>"><?php echo $carrera['nombre']; ?> - <?php echo $carrera['descripcion']; ?></option>
<?php } ?>
              </select>
            </div>
          </div>
        </div>
      </div> <!-- /.modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn-regla-cc-agregar">Agregar</button>
      </div>

    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.nueva-regla-cc-modal -->

<div class="modal fade nueva-regla-mca-modal" tabindex="-1" role="dialog" aria-labelledby="nueva-regla-mca-title">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="nueva-regla-mca-title">Nueva regla - Materia Cursada Aprobada</h4>
      </div>
      
      <div class="modal-body">
        <div class="alert alert-danger collapse" id="nueva-regla-mca-validations" role="alert"></div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-12 required">
            <label for="mca-materia" class="col-xs-3 control-label">Materia</label>
            <div class="col-xs-9">
              <select class="form-control" id="mca-materia" name="mca-materia">
<?php foreach ($viewDataMateriasRegla as $materia) { ?>
                <option value="<?php echo $materia['idmateria']; ?>"><?php echo $materia['nombre']; ?></option>
<?php } ?>
              </select>
            </div>
          </div>
        </div>
      </div> <!-- /.modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn-regla-mca-agregar">Agregar</button>
      </div>

    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.nueva-regla-mca-modal -->

<div class="modal fade nueva-regla-mcaa-modal" tabindex="-1" role="dialog" aria-labelledby="nueva-regla-mcaa-title">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="nueva-regla-mcaa-title">Nueva regla - Materia Cursada Aprobada y Area</h4>
      </div>
      
      <div class="modal-body">
        <div class="alert alert-danger collapse" id="nueva-regla-mcaa-validations" role="alert"></div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-12 required">
            <label for="mcaa-materia" class="col-xs-3 control-label">Materia</label>
            <div class="col-xs-9">
              <select class="form-control" id="mcaa-materia" name="mcaa-materia">
<?php foreach ($viewDataMateriasRegla as $materia) { ?>
                <option value="<?php echo $materia['idmateria']; ?>"><?php echo $materia['nombre']; ?></option>
<?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group col-xs-12 col-sm-12 required">
            <label for="mcaa-area" class="col-xs-3 control-label">Area</label>
            <div class="col-xs-9">
              <select class="form-control" id="mcaa-area" name="mcaa-area">
<?php foreach ($viewDataAreas as $area) { ?>
                <option value="<?php echo $area['idarea']; ?>"><?php echo $area['nombre']; ?></option>
<?php } ?>
              </select>
            </div>
          </div>
        </div>
      </div> <!-- /.modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn-regla-mcaa-agregar">Agregar</button>
      </div>

    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.nueva-regla-mcaa-modal -->

<div class="modal fade nueva-regla-id-modal" tabindex="-1" role="dialog" aria-labelledby="nueva-regla-id-title">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="nueva-regla-id-title">Nueva regla - Instrumento Distinto De</h4>
      </div>
      
      <div class="modal-body">
        <div class="alert alert-danger collapse" id="nueva-regla-id-validations" role="alert"></div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-12 required">
            <label for="id-instrumento" class="col-xs-3 control-label">Instrumento</label>
            <div class="col-xs-9">
              <select class="form-control" id="id-instrumento" name="id-instrumento">
<?php foreach ($viewDataInstrumentos as $instrumento) { ?>
                <option value="<?php echo $instrumento['idinstrumento']; ?>"><?php echo $instrumento['nombre']; ?></option>
<?php } ?>
              </select>
            </div>
          </div>
        </div>
      </div> <!-- /.modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn-regla-id-agregar">Agregar</button>
      </div>

    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.nueva-regla-id-modal -->

<div class="modal fade nueva-regla-ii-modal" tabindex="-1" role="dialog" aria-labelledby="nueva-regla-ii-title">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="nueva-regla-ii-title">Nueva regla - Instrumento Igual A</h4>
      </div>
      
      <div class="modal-body">
        <div class="alert alert-danger collapse" id="nueva-regla-ii-validations" role="alert"></div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-12 required">
            <label for="ii-instrumento" class="col-xs-3 control-label">Instrumento</label>
            <div class="col-xs-9">
              <select class="form-control" id="ii-instrumento" name="ii-instrumento">
<?php foreach ($viewDataInstrumentos as $instrumento) { ?>
                <option value="<?php echo $instrumento['idinstrumento']; ?>"><?php echo $instrumento['nombre']; ?></option>
<?php } ?>
              </select>
            </div>
          </div>
        </div>
      </div> <!-- /.modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn-regla-ii-agregar">Agregar</button>
      </div>

    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.nueva-regla-ii-modal -->

<div class="modal fade nueva-regla-mcs-modal" tabindex="-1" role="dialog" aria-labelledby="nueva-regla-mcs-title">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="nueva-regla-mcs-title">Nueva regla - Materias Cursada en Simultaneo</h4>
      </div>
      
      <div class="modal-body">
        <div class="alert alert-danger collapse" id="nueva-regla-mcs-validations" role="alert"></div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-12 required">
            <label for="mcs-materia1" class="col-xs-3 control-label">Materia #1</label>
            <div class="col-xs-9">
              <select class="form-control" id="mcs-materia1" name="mcs-materia1">
                <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataMateriasRegla as $materia) { ?>
                <option value="<?php echo $materia['idmateria']; ?>"><?php echo $materia['nombre']; ?></option>
<?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group col-xs-12 col-sm-12 required">
            <label for="mcs-materia2" class="col-xs-3 control-label">Materia #2</label>
            <div class="col-xs-9">
              <select class="form-control" id="mcs-materia2" name="mcs-materia2">
                <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataMateriasRegla as $materia) { ?>
                <option value="<?php echo $materia['idmateria']; ?>"><?php echo $materia['nombre']; ?></option>
<?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group col-xs-12 col-sm-12 required">
            <label for="mcs-materia3" class="col-xs-3 control-label">Materia #3</label>
            <div class="col-xs-9">
              <select class="form-control" id="mcs-materia3" name="mcs-materia3">
                <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataMateriasRegla as $materia) { ?>
                <option value="<?php echo $materia['idmateria']; ?>"><?php echo $materia['nombre']; ?></option>
<?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group col-xs-12 col-sm-12 required">
            <label for="mcs-materia$" class="col-xs-3 control-label">Materia #4</label>
            <div class="col-xs-9">
              <select class="form-control" id="mcs-materia4" name="mcs-materia4">
                <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataMateriasRegla as $materia) { ?>
                <option value="<?php echo $materia['idmateria']; ?>"><?php echo $materia['nombre']; ?></option>
<?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group col-xs-12 col-sm-12 required">
            <label for="mcs-materia5" class="col-xs-3 control-label">Materia #5</label>
            <div class="col-xs-9">
              <select class="form-control" id="mcs-materia5" name="mcs-materia5">
                <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataMateriasRegla as $materia) { ?>
                <option value="<?php echo $materia['idmateria']; ?>"><?php echo $materia['nombre']; ?></option>
<?php } ?>
              </select>
            </div>
          </div>
        </div>
      </div> <!-- /.modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn-regla-mcs-agregar">Agregar</button>
      </div>

    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.nueva-regla-mcs-modal -->

<div class="modal fade nueva-regla-mfa-modal" tabindex="-1" role="dialog" aria-labelledby="nueva-regla-mfa-title">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="nueva-regla-mfa-title">Nueva regla - Materia Final Aprobado</h4>
      </div>
      
      <div class="modal-body">
        <div class="alert alert-danger collapse" id="nueva-regla-mfa-validations" role="alert"></div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-12 required">
            <label for="mfa-materia" class="col-xs-3 control-label">Materia</label>
            <div class="col-xs-9">
              <select class="form-control" id="mfa-materia" name="mfa-materia">
<?php foreach ($viewDataMateriasRegla as $materia) { ?>
                <option value="<?php echo $materia['idmateria']; ?>"><?php echo $materia['nombre']; ?></option>
<?php } ?>
              </select>
            </div>
          </div>
        </div>
      </div> <!-- /.modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="btn-regla-mfa-agregar">Agregar</button>
      </div>

    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.nueva-regla-mfa-modal -->

<script type="text/javascript">
    
  function getReglas() {
    $('#table-correlativas > tbody').empty();
    $('#table-correlativas > tbody:last-child').append('<tr><td colspan=2>CARGANDO...</td></tr>');
    $.ajax({ url: '/correlativas/ajaxreglas/<?php echo $dataIdMateria; ?>/<?php echo $dataIdCarrera; ?>', method: 'POST', dataType: 'json', success: function(response){
      $('#table-correlativas > tbody').empty();
      if (response.length>0) {
        $.each(response, function(i, value) {
          newRow = "<tr>";
          switch (value.idregla) {
            case "1": label = value.descripcion + ' [' + value.carr + ']'; break; 
            case "2": 
            case "7": label = value.descripcion + ' [' + value.mat + ']'; break; 
            case "3": label = value.descripcion + ' [' + value.mat + ']' + ' [' + value.area + ']'; break; 
            case "4": 
            case "5": label = value.descripcion + ' [' + value.inst + ']'; break; 
            case "6": label = value.descripcion + ' [' + value.materias + ']'; break; 
            case "8": label = value.descripcion + ' [' + value.valor1 + ']'; break; 
          }
          newRow+= "<td>" + label + '</td>'; 
          newRow+= '<td><button type="button" class="btn btn-xs btn-warning btn-regla-eliminar" data-id="' + value.id + '">Eliminar</button></td>';
          newRow+= "</tr>";
          $('#table-correlativas > tbody:last-child').append(newRow);
        });    
      } else {
        $('#table-correlativas > tbody:last-child').append('<tr><td colspan=2>Sin registros</td></tr>');
      }
    }});
  }

  $(document).on('click', '.btn-regla-eliminar', function(){
    $.ajax({ url: '/correlativas/ajaxdeleteregla/' + $(this).attr('data-id'), method: 'POST', dataType: 'json', success: function(response){
      getReglas();
    }});
});

 $('#profesor').change(function() {
    switch ($(this).val()) {
      case "1":
        $('#button-regla').removeAttr('disabled');      
        $('#button-regla').attr('data-target','.nueva-regla-cc-modal');
        break;      
      case "2":
        $('#button-regla').removeAttr('disabled');      
        $('#button-regla').attr('data-target','.nueva-regla-mca-modal');
        break;      
      case "3":
        $('#button-regla').removeAttr('disabled');      
        $('#button-regla').attr('data-target','.nueva-regla-mcaa-modal');
        break;      
      case "4":
        $('#button-regla').removeAttr('disabled');      
        $('#button-regla').attr('data-target','.nueva-regla-id-modal');
        break;      
      case "5":
        $('#button-regla').removeAttr('disabled');      
        $('#button-regla').attr('data-target','.nueva-regla-ii-modal');
        break;      
      case "6":
        $('#button-regla').removeAttr('disabled');      
        $('#button-regla').attr('data-target','.nueva-regla-mcs-modal');
        break;      
      case "7":
        $('#button-regla').removeAttr('disabled');      
        $('#button-regla').attr('data-target','.nueva-regla-mfa-modal');
        break;      
      default:
        $('#button-regla').attr('disabled','disabled');
    }
  });

  $('#profesor').trigger('change');

  $('#button-cancelar').click(function() {
    window.location.href = "/gestion-carreras/";
  });
  
    function evaluateMateria() {
    $('#nombre').prop('disabled',($('#materia').val()!="0"));
    $('#descripcion').prop('disabled',($('#materia').val()!="0"));
    $('#anio').prop('disabled',($('#materia').val()!="0"));
    $('#tipmat').prop('disabled',($('#materia').val()!="0"));
    $('#promocionable').prop('disabled',($('#materia').val()!="0"));
    $('#cantidadmodulos').prop('disabled',($('#materia').val()!="0"));
    //$('#orden').prop('disabled',($('#materia').val()!="0"));
    $('#libre').prop('disabled',($('#materia').val()!="0"));
  }
 <?php if ($dataIdMateria==0) { ?>
  evaluateMateria();
  <?php } ?>
  $('#materia').change(function() {
    evaluateMateria();
    if ($('#materia').val()!=0) { //Cargo los datos de la materia que ya existe
      $.ajax({ url: "/ajaxmateria/" + $('#materia').val(), method: 'POST', dataType: 'json', success: function(response){
        if (response.rows.length>0) {
          $('#nombre').val(response.rows[0]['nombre']);
          $('#descripcion').val(response.rows[0]['descripcion']);
          $('#anio').val(response.rows[0]['anio']);
          $('#tipmat').val(response.rows[0]['idtipomateria']);
          $('#promocionable').prop('checked', (response.rows[0]['promocionable']=="1"));
          $('#cantidadmodulos').val(response.rows[0]['cantidadmodulos']);
          //$('#orden').val(response.rows[0]['orden']);
          $('#libre').prop('checked', (response.rows[0]['libre']=="1"));
        }
        $('#table-correlativas > tbody').empty();

    }});
   } else { //Inicializo los datos para una nueva materia.
      $('#nombre').val('');
      $('#descripcion').val('');
      $('#anio').val('');
      $('#tipmat').val(0);
      $('#promocionable').prop('checked', false);
      $('#cantidadmodulos').val('');
      $('#orden').val('');
      $('#libre').prop('checked', false);
      $('#table-correlativas > tbody').empty();
    }
  });
  
  function resetForms() {
    $('#cc-carrera').prop("selectedIndex", 0);
    $('#mca-materia').prop("selectedIndex", 0);
    $('#mcaa-materia').prop("selectedIndex", 0);
    $('#mcaa-area').prop("selectedIndex", 0);
    $('#id-instrumento').prop("selectedIndex", 0);
    $('#ii-instrumento').prop("selectedIndex", 0);
    $('#mcs-materia1').prop("selectedIndex", 0);
    $('#mcs-materia2').prop("selectedIndex", 0);
    $('#mcs-materia3').prop("selectedIndex", 0);
    $('#mcs-materia4').prop("selectedIndex", 0);
    $('#mcs-materia5').prop("selectedIndex", 0);
    $('#mfa-materia').prop("selectedIndex", 0);
    $('#nueva-regla-cc-validations').text('');
    $('#nueva-regla-cc-validations').hide();
    $('#nueva-regla-mca-validations').text('');
    $('#nueva-regla-mca-validations').hide();
    $('#nueva-regla-mcaa-validations').text('');
    $('#nueva-regla-mcaa-validations').hide();
    $('#nueva-regla-id-validations').text('');
    $('#nueva-regla-id-validations').hide();
    $('#nueva-regla-ii-validations').text('');
    $('#nueva-regla-ii-validations').hide();
    $('#nueva-regla-mcs-validations').text('');
    $('#nueva-regla-mcs-validations').hide();
    $('#nueva-regla-mfa-validations').text('');
    $('#nueva-regla-mfa-validations').hide();
  }

  $('.nueva-regla-cc-modal').on('show.bs.modal', function (e)    { resetForms(); });
  $('.nueva-regla-mca-modal').on('show.bs.modal', function (e)   { resetForms(); });
  $('.nueva-regla-mcaa-modal').on('show.bs.modal', function (e)  { resetForms(); });
  $('.nueva-regla-id-modal').on('show.bs.modal', function (e)    { resetForms(); });
  $('.nueva-regla-ii-modal').on('show.bs.modal', function (e)    { resetForms(); });
  $('.nueva-regla-mcs-modal').on('show.bs.modal', function (e)   { resetForms(); });
  $('.nueva-regla-mfa-modal').on('show.bs.modal', function (e)   { resetForms(); });
  $('.nueva-regla-cc-modal').on('shown.bs.modal', function (e)   { $("#cc-carrera").focus(); });
  $('.nueva-regla-mca-modal').on('shown.bs.modal', function (e)  { $("#mca-materia").focus(); });
  $('.nueva-regla-mcaa-modal').on('shown.bs.modal', function (e) { $("#mcaa-materia").focus(); });
  $('.nueva-regla-id-modal').on('shown.bs.modal', function (e)   { $("#id-instrumento").focus(); });
  $('.nueva-regla-ii-modal').on('shown.bs.modal', function (e)   { $("#ii-instrumento").focus(); });
  $('.nueva-regla-mcs-modal').on('shown.bs.modal', function (e)  { $("#mcs-materia1").focus(); });
  $('.nueva-regla-mfa-modal').on('shown.bs.modal', function (e)  { $("#mfa-materia").focus(); });

  $('#btn-regla-cc-agregar').click(function() {
    $('#nueva-regla-cc-validations').hide();
    $.ajax({ url: "/correlativas/ajaxreglacc/<?php echo $dataIdMateria; ?>/<?php echo $dataIdCarrera; ?>/" + $('#cc-carrera').val(), method: 'POST', dataType: 'json', success: function(response){
      if (response == 'OK') {
        $('.nueva-regla-cc-modal').modal('hide');
        getReglas();
      } else {
        $('#nueva-regla-cc-validations').text(response);
        $('#nueva-regla-cc-validations').show();
        $("#cc-carrera").focus();
      }
    }});
  });

  $('#btn-regla-mca-agregar').click(function() {
    $('#nueva-regla-mca-validations').hide();
    $.ajax({ url: "/correlativas/ajaxreglamca/<?php echo $dataIdMateria; ?>/<?php echo $dataIdCarrera; ?>/" + $('#mca-materia').val(), method: 'POST', dataType: 'json', success: function(response){
      if (response == 'OK') {
        $('.nueva-regla-mca-modal').modal('hide');
        getReglas();
      } else {
        $('#nueva-regla-mca-validations').text(response);
        $('#nueva-regla-mca-validations').show();
        $("#mca-materia").focus();
      }
    }});
  });

  $('#btn-regla-mcaa-agregar').click(function() {
    $('#nueva-regla-mcaa-validations').hide();
    $.ajax({ url: "/correlativas/ajaxreglamcaa/<?php echo $dataIdMateria; ?>/<?php echo $dataIdCarrera; ?>/" + $('#mcaa-materia').val() + "/" + $('#mcaa-area').val() + "/", method: 'POST', dataType: 'json', success: function(response){
      if (response == 'OK') {
        $('.nueva-regla-mcaa-modal').modal('hide');
        getReglas();
      } else {
        $('#nueva-regla-mcaa-validations').text(response);
        $('#nueva-regla-mcaa-validations').show();
        $("#mcaa-materia").focus();
      }
    }});
  });

  $('#btn-regla-id-agregar').click(function() {
    $('#nueva-regla-id-validations').hide();
    $.ajax({ url: "/correlativas/ajaxreglaid/<?php echo $dataIdMateria; ?>/<?php echo $dataIdCarrera; ?>/" + $('#id-instrumento').val(), method: 'POST', dataType: 'json', success: function(response){
      if (response == 'OK') {
        $('.nueva-regla-id-modal').modal('hide');
        getReglas();
      } else {
        $('#nueva-regla-id-validations').text(response);
        $('#nueva-regla-id-validations').show();
        $("#id-instrumento").focus();
      }
    }});
  });

  $('#btn-regla-ii-agregar').click(function() {
    $('#nueva-regla-ii-validations').hide();
    $.ajax({ url: "/correlativas/ajaxreglaii/<?php echo $dataIdMateria; ?>/<?php echo $dataIdCarrera; ?>/" + $('#ii-instrumento').val(), method: 'POST', dataType: 'json', success: function(response){
      if (response == 'OK') {
        $('.nueva-regla-ii-modal').modal('hide');
        getReglas();
      } else {
        $('#nueva-regla-ii-validations').text(response);
        $('#nueva-regla-ii-validations').show();
        $("#ii-instrumento").focus();
      }
    }});
  });

  $('#btn-regla-mcs-agregar').click(function() {
    $('#nueva-regla-mcs-validations').hide();
    $.ajax({ url: "/correlativas/ajaxreglamcs/<?php echo $dataIdMateria; ?>/<?php echo $dataIdCarrera; ?>/" + $('#mcs-materia1').val() + '/' + $('#mcs-materia2').val() + '/' + $('#mcs-materia3').val() + '/' + $('#mcs-materia4').val() + '/' + $('#mcs-materia5').val(), method: 'POST', dataType: 'json', success: function(response){
      if (response == 'OK') {
        $('.nueva-regla-mcs-modal').modal('hide');
        getReglas();
      } else {
        $('#nueva-regla-mcs-validations').text(response);
        $('#nueva-regla-mcs-validations').show();
        $("#mcs-materia1").focus();
      }
    }});
  });

  $('#btn-regla-mfa-agregar').click(function() {
    $('#nueva-regla-mfa-validations').hide();
    $.ajax({ url: "/correlativas/ajaxreglamfa/<?php echo $dataIdMateria; ?>/<?php echo $dataIdCarrera; ?>/" + $('#mfa-materia').val(), method: 'POST', dataType: 'json', success: function(response){
      if (response == 'OK') {
        $('.nueva-regla-mfa-modal').modal('hide');
        getReglas();
      } else {
        $('#nueva-regla-mfa-validations').text(response);
        $('#nueva-regla-mfa-validations').show();
        $("#mfa-materia").focus();
      }
    }});
  });

  getReglas();
</script>