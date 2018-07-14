<?php
$apellidoValue = isset($_POST['apellido']) ? $_POST['apellido'] : "";
$nombreValue = isset($_POST['nombre']) ? $_POST['nombre'] : "";
$tipdocValue = isset($_POST['tipdoc']) ? $_POST['tipdoc'] : 0;
$nrodocValue = isset($_POST['nrodoc']) ? $_POST['nrodoc'] : "";
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
    <h3>Alta de un Persona <small>datos básicos</small></h3>

    <div class="form-group col-xs-12 col-sm-6 required <?php if (array_key_exists('apellido', $validationErrors)) echo "has-error"; ?>">
      <label for="apellido" class="col-xs-4 control-label">Apellido</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="apellido" name="apellido" placeholder="" maxlength="45" value="<?php echo $apellidoValue; ?>" autofocus="on" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 required <?php if (array_key_exists('nombre', $validationErrors)) echo "has-error"; ?>">
      <label for="nombre" class="col-xs-4 control-label">Nombre</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="" maxlength="45" value="<?php echo $nombreValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 required <?php if (array_key_exists('tipdoc', $validationErrors)) echo "has-error"; ?>">
      <label for="tipdoc" class="col-xs-4 control-label">Tipo Doc.</label>
      <div class="col-xs-8">
        <select class="form-control" id="tipdoc" name="tipdoc">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataTipoDocumento as $tipdoc) { ?>
          <option value="<?php echo $tipdoc['id']; ?>" <?php if ($tipdocValue == $tipdoc['id']) echo "selected"; ?>><?php echo $tipdoc['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 required <?php if (array_key_exists('nrodoc', $validationErrors)) echo "has-error"; ?>">
      <label for="nrodoc" class="col-xs-4 control-label">Nro.Doc.</label>
      <div class="col-xs-8">
        <input type="text" maxlength="8" class="form-control" id="nrodoc" name="nrodoc" placeholder="" value="<?php echo $nrodocValue; ?>" />
      </div>
    </div>
  </fieldset>
  <button type="submit" class="btn btn-primary">Crear</button>
  <a href="/gestion-permisos" class="btn btn-default">Cancelar</a>
</form>




