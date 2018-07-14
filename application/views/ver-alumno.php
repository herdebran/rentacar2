<?php
$apellidoValue = isset($_POST['apellido']) ? $_POST['apellido'] : $viewData[0]['apellido'];
$nombreValue = isset($_POST['nombre']) ? $_POST['nombre'] : $viewData[0]['nombre'];
$nacionalidadValue = isset($_POST['nacionalidad']) ? $_POST['nacionalidad'] : $viewData[0]['nacionalidad'];
$fnacValue = isset($_POST['fnac']) ? $_POST['fnac'] : $viewData[0]['fnac_dmy'];
$tel1Value = isset($_POST['telefono1']) ? $_POST['telefono1'] : $viewData[0]['telefono1'];
$tel2Value = isset($_POST['telefono2']) ? $_POST['telefono2'] : $viewData[0]['telefono2'];
$sexoValue = isset($_POST['sexo']) ? $_POST['sexo'] : $viewData[0]['sexo'];
$estcivValue = isset($_POST['estciv']) ? $_POST['estciv'] : $viewData[0]['estadocivil'];
$certlabValue = isset($_POST['certlab']) ? $_POST['certlab'] : $viewData[0]['certlab'];
$provinciaValue = isset($_POST['provincia']) ? $_POST['provincia'] : $viewData[0]['idprovincia'];
$direccionValue = isset($_POST['direccion']) ? $_POST['direccion'] : $viewData[0]['direccion'];
$entrecallesValue = isset($_POST['entrecalles']) ? $_POST['entrecalles'] : $viewData[0]['entrecalles'];
$numeroValue = isset($_POST['numero']) ? $_POST['numero'] : $viewData[0]['numero'];
$pisoValue = isset($_POST['piso']) ? $_POST['piso'] : $viewData[0]['piso'];
$deptoValue = isset($_POST['depto']) ? $_POST['depto'] : $viewData[0]['depto'];
$localidadValue = isset($_POST['localidad']) ? $_POST['localidad'] : $viewData[0]['idlocalidad'];
$codpostalValue = isset($_POST['codpostal']) ? $_POST['codpostal'] : $viewData[0]['codpostal'];
$tituloValue = isset($_POST['titulo']) ? $_POST['titulo'] : $viewData[0]['titulosecundario'];
$otorgadoporValue = isset($_POST['otorgadopor']) ? $_POST['otorgadopor'] : $viewData[0]['otorgadopor'];
$anoegresoValue = isset($_POST['anoegreso']) ? $_POST['anoegreso'] : $viewData[0]['anoegreso'];
$obrasocialValue = isset($_POST['obrasocial']) ? $_POST['obrasocial'] : $viewData[0]['obrasocial'];
$contactoemergenciaValue = isset($_POST['contactoemergencia']) ? $_POST['contactoemergencia'] : $viewData[0]['contactoemergencia'];
$telefonoValue = isset($_POST['telefono']) ? $_POST['telefono'] : $viewData[0]['telefono'];
$enfermedadesValue = isset($_POST['enfermedades']) ? $_POST['enfermedades'] : $viewData[0]['enfermedades'];
$usuarioValue = isset($_POST['usaurio']) ? $_POST['usuario'] : $viewData[0]['usuario'];
$emailValue = isset($_POST['email']) ? $_POST['email'] : $viewData[0]['email'];
if ($status=="modificar") $estalu = isset($_POST['estalu']) ? $_POST['estalu'] : $viewData[0]['estadoalumno_id'];

$primeraccesoValue = isset($_POST['primeracceso']) ? $_POST['primeracceso'] : $viewData[0]['primeracceso'];
$observacionesValue = isset($_POST['observaciones']) ? $_POST['observaciones'] : $viewData[0]['observaciones'];
$tipdocValue = isset($_POST['tipdoc']) ? $_POST['tipdoc'] : $viewData[0]['tipodoc'];
$nrodocValue = isset($_POST['nrodoc']) ? $_POST['nrodoc'] : $viewData[0]['documentonro'];

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
    <h3>Datos Generales</h3>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('apellido', $validationErrors)) echo "has-error"; ?>">
      <label for="apellido" class="col-xs-4 control-label">Apellido</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="apellido" name="apellido" placeholder="" maxlength="45" value="<?php echo $apellidoValue; ?>" <?php /* solo-validacoines-post required */ ?> <?php if ($status=="reempadronamiento" || $status=="misdatos") echo "disabled"; else echo "autofocus=\"on\""; ?> autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('nombre', $validationErrors)) echo "has-error"; ?>">
      <label for="nombre" class="col-xs-4 control-label">Nombre</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="" maxlength="45" value="<?php echo $nombreValue; ?>" <?php if ($status=="reempadronamiento" || $status=="misdatos") echo "disabled"; ?> autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('nacionalidad', $validationErrors)) echo "has-error"; ?>">
      <label for="nacionalidad" class="col-xs-4 control-label">Nacionalidad</label>
      <div class="col-xs-8">
        <select class="form-control" id="nacionalidad" name="nacionalidad">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataNacionalidad as $nacionalidad) { ?>
          <option value="<?php echo mb_strtoupper($nacionalidad, 'UTF-8'); ?>" <?php if ($nacionalidadValue == mb_strtoupper($nacionalidad, 'UTF-8')) echo "selected"; ?>><?php echo $nacionalidad; ?></option>
<?php } //foreach ($viewDataNacionalidad as $nacionalidad)  ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('fnac', $validationErrors)) echo "has-error"; ?>">
      <label for="fnac" class="col-xs-4 control-label">F.Nacimiento</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" maxlength="10" id="fnac" name="fnac" placeholder="dd/mm/aaaa" value="<?php echo $fnacValue; ?>" autocomplete="off" >
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('tipdoc', $validationErrors)) echo "has-error"; ?>">
      <label for="tipdoc" class="col-xs-4 control-label">Tipo Doc.</label>
      <div class="col-xs-8">
        <?php if ($status=="reempadronamiento" || $status=="inscripcion" || $status=="misdatos") 
		echo "<input type=\"hidden\" name=\"tipdoc\" value=\"" . $tipdocValue . "\" />"; ?>
        <select class="form-control" id="tipdoc" name="tipdoc" <?php if ($status=="reempadronamiento" || $status=="inscripcion" || $status=="misdatos") echo "disabled"; ?>>
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataTipoDocumento as $tipdoc) { ?>
          <option value="<?php echo $tipdoc['id']; ?>" <?php if ($tipdocValue == $tipdoc['id']) echo "selected"; ?>><?php echo $tipdoc['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('nrodoc', $validationErrors)) echo "has-error"; ?>">
      <label for="nrodoc" class="col-xs-4 control-label">Nro.Doc.</label>
      <div class="col-xs-8">
        <?php if ($status=="reempadronamiento" || $status=="inscripcion" || $status=="misdatos") 
		echo "<input type=\"hidden\" name=\"nrodoc\" value=\"" . $nrodocValue . "\" />"; ?>
        <input type="number" min="1" max="99999999" class="form-control" id="nrodoc" name="nrodoc" placeholder="" value="<?php echo $nrodocValue; ?>" <?php if ($status=="reempadronamiento" || $status=="inscripcion" || $status=="misdatos") echo "disabled"; ?> autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('telefono1', $validationErrors)) echo "has-error"; ?>">
      <label for="telefono1" class="col-xs-4 control-label">Tel.Fijo</label>
      <div class="col-xs-8">
        <input type="tel" class="form-control" id="telefono1" name="telefono1" placeholder="" value="<?php echo $tel1Value; ?>" autocomplete="off" >
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('telefono2', $validationErrors)) echo "has-error"; ?>">
      <label for="telefono2" class="col-xs-4 control-label">Tel.Celular</label>
      <div class="col-xs-8">
        <input type="tel" class="form-control" id="telefono2" name="telefono2" placeholder="" value="<?php echo $tel2Value; ?>" autocomplete="off" >
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('sexo', $validationErrors)) echo "has-error"; ?>">
      <label for="sexo" class="col-xs-4 control-label">Sexo</label>
      <div class="col-xs-8">
        <select class="form-control" id="sexo" name="sexo">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataSexo as $sexo) { ?>
          <option value="<?php echo mb_strtoupper($sexo, 'UTF-8'); ?>" <?php if ($sexoValue == mb_strtoupper($sexo, 'UTF-8')) echo "selected"; ?>><?php echo $sexo; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('estciv', $validationErrors)) echo "has-error"; ?>">
      <label for="estciv" class="col-xs-4 control-label">Est.Civil</label>
      <div class="col-xs-8">
        <select class="form-control" id="estciv" name="estciv">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataEstadoCivil as $estciv) { ?>
          <option value="<?php echo mb_strtoupper($estciv, 'UTF-8'); ?>" <?php if ($estcivValue == mb_strtoupper($estciv, 'UTF-8')) echo "selected"; ?>><?php echo $estciv; ?></option>
<?php } ?>
        </select>
      </div>
    </div>
<?php 
//if ($status!="reempadronamiento" && $status!="inscripcion" && $status!="misdatos") {
if ($status!="reempadronamiento" && $status!="inscripcion") { ?>
    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('certlab', $validationErrors)) echo "has-error"; ?>">
      <div class="col-sm-offset-2 col-sm-10">
        <div class="checkbox">
          <label for="certlab">
            <input type="checkbox" id="certlab" name="certlab" value="certlab" <?php if ($certlabValue == 1) echo "checked"; ?>> Certificado Laboral Presentado
          </label>
        </div>
      </div>
    </div>
<?php } ?>
  </fieldset>


  <fieldset>
    <h3>Domicilio</h3>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('direccion', $validationErrors)) echo "has-error"; ?>">
      <label for="direccion" class="col-xs-4 control-label">Calle</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="direccion" name="direccion" placeholder="" maxlength="45" value="<?php echo $direccionValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('numero', $validationErrors)) echo "has-error"; ?>">
      <label for="numero" class="col-xs-4 control-label">Número</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="numero" name="numero" placeholder="" maxlength="45" value="<?php echo $numeroValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('entrecalles', $validationErrors)) echo "has-error"; ?>">
      <label for="entrecalles" class="col-xs-4 control-label">Entre</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="entrecalles" name="entrecalles" placeholder="" maxlength="45" value="<?php echo $entrecallesValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('provincia', $validationErrors)) echo "has-error"; ?>">
      <label for="provincia" class="col-xs-4 control-label">Provincia</label>
      <div class="col-xs-8">
        <select class="form-control" id="provincia" name="provincia" >
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataProvincias as $prov) { ?>
          <option value="<?php echo $prov['id']; ?>" <?php if ($provinciaValue == $prov['id']) echo "selected"; ?>><?php echo $prov['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('localidad', $validationErrors)) echo "has-error"; ?>">
      <label for="localidad" class="col-xs-4 control-label">Localidad</label>
      <div class="col-xs-8"><select class="form-control" id="localidad" name="localidad" >
          <option value="0">SELECCIONAR</option><?php foreach ($viewDataLocalidades as $localidad) { ?>
          <option value="<?php echo $localidad['id']; ?>" data-cp="<?php echo $localidad['cp']; ?>" <?php if ($localidadValue == $localidad['id']) echo "selected"; ?>><?php echo $localidad['descripcion']; ?></option>
<?php } ?></select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('piso', $validationErrors)) echo "has-error"; ?>">
      <label for="piso" class="col-xs-4 control-label">Piso</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="piso" name="piso" placeholder="" maxlength="45" value="<?php echo $pisoValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('depto', $validationErrors)) echo "has-error"; ?>">
      <label for="depto" class="col-xs-4 control-label">Depto</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="depto" name="depto" placeholder="" maxlength="45" value="<?php echo $deptoValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('codpostal', $validationErrors)) echo "has-error"; ?>">
      <label for="codpostal" class="col-xs-4 control-label">Cod.Postal</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="codpostal" name="codpostal" placeholder="" maxlength="45" value="<?php echo $codpostalValue; ?>" <?php /* solo-validacoines-post required */ ?> autocomplete="off" />
      </div>
    </div>
  </fieldset>



  <fieldset>
    <h3>Estudios</h3>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('titulo', $validationErrors)) echo "has-error"; ?>">
      <label for="titulo" class="col-xs-4 control-label">Título</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="titulo" name="titulo" maxlength="45" placeholder="" value="<?php echo $tituloValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('otorgadopor', $validationErrors)) echo "has-error"; ?>">
      <label for="otorgadopor" class="col-xs-4 control-label">Otorgado por</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="otorgadopor" name="otorgadopor" maxlength="45" placeholder="" value="<?php echo $otorgadoporValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('anoegreso', $validationErrors)) echo "has-error"; ?>">
      <label for="anoegreso" class="col-xs-4 control-label">Año Egreso</label>
      <div class="col-xs-8">
        <input type="number" min="1930" max="<?php echo date("Y") ?>" class="form-control" id="anoegreso" name="anoegreso" placeholder="" value="<?php echo $anoegresoValue; ?>" >
      </div>
    </div>
  </fieldset>

  <fieldset>
    <h3>Ficha Médica</h3>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('obrasocial', $validationErrors)) echo "has-error"; ?>">
      <label for="obrasocial" class="col-xs-4 control-label">Obra Social</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="obrasocial" name="obrasocial" maxlength="45" placeholder="" value="<?php echo $obrasocialValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('contactoemergencia', $validationErrors)) echo "has-error"; ?>">
      <label for="contactoemergencia" class="col-xs-4 control-label">Nombre de Contacto ante emergencia</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="contactoemergencia" name="contactoemergencia" maxlength="45" placeholder="" value="<?php echo $contactoemergenciaValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('telefono', $validationErrors)) echo "has-error"; ?>">
      <label for="telefono" class="col-xs-4 control-label">Teléfono del Contacto de emergencia</label>
      <div class="col-xs-8">
        <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="" value="<?php echo $telefonoValue; ?>" autocomplete="off" >
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('enfermedades', $validationErrors)) echo "has-error"; ?>">
      <label for="enfermedades" class="col-xs-4 control-label">Enfermedades Crónicas u Otras</label>
      <div class="col-xs-8">
        <textarea class="form-control" id="enfermedades" name="enfermedades" rows="3" maxlength="500" placeholder=""><?php echo $enfermedadesValue; ?></textarea>
      </div>
    </div>
  </fieldset>

  <fieldset>
    <h3>Datos para sitio WEB</h3>
    <div class="row">
    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('usuario', $validationErrors)) echo "has-error"; ?>">
      <label for="usuario" class="col-xs-4 control-label">Usuario</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="usuario" name="usuario" maxlength="45" placeholder="" value="<?php echo $usuarioValue; ?>" <?php if ($status=="reempadronamiento" || $status=="inscripcion" || $status=="modificar" || $status=="misdatos") echo "disabled"; ?>>
<?php if ($status=="reempadronamiento" || $status=="inscripcion" || $status=="modificar" || $status=="misdatos") { ?>
<input type="hidden" name="usuario" value="<?php echo $usuarioValue; ?>" />
<?php } ?>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('email', $validationErrors)) echo "has-error"; ?>">
      <label for="email" class="col-xs-4 control-label">Email</label>
      <div class="col-xs-8">
        <input type="email" class="form-control" id="email" name="email" maxlength="45" placeholder="" value="<?php echo $emailValue; ?>" autocomplete="off" />
      </div>
    </div>

<?php if ($status == "modificar") { ?>
    <div class="form-group col-xs-12 col-sm-6 col-lg-4">
      <label for="estalu" class="col-xs-4 control-label">Estado</label>
      <div class="col-xs-8">
        <select class="form-control" id="estalu" name="estalu" >
<?php foreach ($viewDataEstadoAlumnos as $ea) { ?>
          <option value="<?php echo $ea['id']; ?>" <?php if ($estalu == $ea['id']) echo "selected"; ?>><?php echo $ea['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>
<?php } ?>


    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('password1', $validationErrors)) echo "has-error"; ?>">
      <label for="password1" class="col-xs-4 control-label">Contraseña</label>
      <div class="col-xs-8">
        <input type="password" class="form-control" id="password1" name="password1" maxlength="45" autocomplete="off" placeholder="" value="" >
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('password2', $validationErrors)) echo "has-error"; ?>">
      <label for="password2" class="col-xs-4 control-label">Validar Contraseña</label>
      <div class="col-xs-8">
        <input type="password" class="form-control" id="password2" name="password2" maxlength="45" autocomplete="off" placeholder="" value="" >
      </div>
    </div>

<?php if ($status!="reempadronamiento" && $status!="inscripcion") { ?>
    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('primeracceso', $validationErrors)) echo "has-error"; ?>">
      <div class="col-sm-offset-4 col-sm-8">
        <div class="checkbox">
          <label for="primeracceso">
            <input type="checkbox" id="primeracceso" name="primeracceso" value="primeracceso" <?php if ($primeraccesoValue == 1) echo "checked"; ?> <?php if ($status!='modificar') echo "disabled"; ?>> Primer Acceso
          </label>
        </div>
      </div>
    </div>
<?php } ?>    

    </div>

<?php if ($status == "modificar") { ?>
        <div class="form-group col-xs-12 col-sm-6 col-lg-4">
            <button type="button" id="autogenerar-clave" class="btn btn-sm btn-info">Autogenerar Clave</button>
        </div>  
<?php } ?>          




  </fieldset>



  <fieldset>
    <h3>Observaciones</h3>

    <div class="form-group col-xs-12 <?php if (array_key_exists('observaciones', $validationErrors)) echo "has-error"; ?>">
      <label for="observaciones" class="control-label hidden">Observaciones</label>
      <div class="col-xs-12">
        <textarea class="form-control" id="observaciones" name="observaciones" rows="3" maxlength="5000" placeholder=""><?php echo $observacionesValue; ?></textarea>
      </div>
    </div>
  </fieldset>
  
  <fieldset>
    <h3>Carreras / Ciclo</h3>
      <div class="panel panel-default">
        <div class="panel-body  <?php if (array_key_exists('carreras', $validationErrors)) echo "bg-danger"; ?>">
          <table class="table" id="table-carreras">
            <thead>
              <tr>
                <th>Codigo</th>
                <th>Carrera / Ciclo</th>
                <th>Nivel</th>
                <th>Género</th>
                <th>instrumento</th>
                <th>Fecha Inscrip.</th>
                <th title="Última Cursada">Últ.Curs.</th>
                <th>Estado</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($viewDataAlumnoCarreras as $carrera) { ?>
              <tr>
                <td><small><?php echo $carrera['carrera_nombre']; ?></small></td>
                <td><small><?php echo $carrera['carrera_descripcion']; ?></small></td>
                <td><small><?php echo $carrera['nivelDescripcion']; ?></small></td>
                <td><small><?php echo $carrera['area_nombre']; ?></small></td>
                <td><small><?php echo $carrera['instrumento_nombre']; ?></small></td>
                <td><small><?php echo $carrera['fechainscripcion_dmy']; ?></small></td>
                <td></td>
                <td><small><?php echo $carrera['estado']; ?></small>
                <?php 
		if ($status=="modificar")  { 
                if ($ses->tienePermiso('','Alumno Agregar o Quitar Carrera'))  { 
                ?>
                    <a href="/quitar-carrera/<?php echo $idpersona; ?>/<?php echo $carrera['idcarrera']; ?>/<?php echo $carrera['idinstrumento']; ?>/<?php echo $carrera['idarea']; ?>/<?php echo $params; ?>" class="btn btn-sm btn-warning">Quitar</a> <?php /* AJUSTE-8NOV TODA ESTA LINEA */ ?>
                    <a href="/modificar-carrera/<?php echo $idpersona; ?>/<?php echo $carrera['idcarrera']; ?>/<?php echo $carrera['idinstrumento']; ?>/<?php echo $carrera['idarea']; ?>/<?php echo $params; ?>" class="btn btn-sm btn-warning">Modificar</a> <?php /* AJUSTE-8NOV TODA ESTA LINEA */ ?>
                <?php 
                }}
		?>
                </td>
              </tr>
              <?php } ?>
              <?php foreach ($newCarreras as $newCarrera) { $i=1; ?>
              <tr id="newCarreraN<?php echo $i; ?>">
                <td><small><?php echo $newCarrera['carrera-nombre']; ?></small></td>
                <td><small><?php echo $newCarrera['carrera-descripcion']; ?></small></td>
                <td><small><?php echo $newCarrera['nivel-nombre']; ?></small></td>
                <td><small><?php echo $newCarrera['area-nombre']; ?></small></td>
                <td><small><?php echo $newCarrera['instrumento-nombre']; ?></small></td>
                <td><small><?php echo $newCarrera['fecha']; ?></small></td>
                <td></td>
                <td><small>NUEVA CARRERA </small><button type="button" class="btn btn-sm btn-warning" id="quitarCarrera<?php echo $i; ?>">Quitar</button></td>
                <input type="hidden" name="new-carreras[]" value="<?php echo $newCarrera['raw']; ?>" />
              </tr>
<script type="text/javascript">
  $('#quitarCarrera<?php echo $i; ?>').click(function() { $('#newCarreraN<?php echo $i; ?>').remove(); });
</script>
              <?php $i++;
              } ?>
            </tbody>
          </table>

<?php if ($status == "modificar" || $status == "inscripcion") { 
//  //Modificar SI
    //Reempadronamiento NO
    //Inscripcion SI
    //Mis Datos NO
    ?>
          <div class="form-group">
            <div class="col-xs-12">
              <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target=".nueva-carrera-modal">Agregar Carrera</button>
            </div>
          </div>
<?php } ?>          
        </div>
      </div>
  </fieldset>




  <div class="form-group">
    <div class=" col-xs-12">
      <button type="submit" class="btn btn-lg btn-primary"><?php if ($status=="reempadronamiento") echo "Reempadronar" ?><?php if ($status=="inscripcion") echo "Inscribirme"; ?><?php if ($status=="modificar") echo "Modificar"; ?><?php if ($status=="misdatos") echo "Grabar"; ?></button>
    </div>
  </div>

</form>

 <!-- MODAL DE AGREGAR CARRERA -->
<div class="modal fade nueva-carrera-modal" tabindex="-1" role="dialog" aria-labelledby="nueva-carrera-title">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="nueva-carrera-title">Agregar una nueva Carrera / Ciclo</h4>
      </div>
      
      <div class="modal-body">
        <div class="alert alert-danger collapse" id="nueva-carrera-validations" role="alert"></div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-6 required">
            <label for="nuevaCarrera" class="col-xs-4 control-label">Carrera / Ciclo</label>
            <div class="col-xs-8">
              <select class="form-control" id="nuevaCarrera">
                <option value="0" data-nombre="" data-descripcion="">SELECCIONAR</option>
      <?php foreach ($viewDataCarreras as $carrera) { ?>
                <option value="<?php echo $carrera['idcarrera']; ?>" data-nombre="<?php echo $carrera['nombre']; ?>" data-descripcion="<?php echo $carrera['descripcion']; ?>"><?php echo $carrera['descripcion']; ?></option>
      <?php } ?>
              </select>
            </div>
          </div>

<?php if ($status!="inscripcion") { //El Genero o Area NO lo muestro si estoy en inscripcion?>
          <div class="form-group col-xs-12 col-sm-6">
            <label for="nuevaArea" class="col-xs-4 control-label">Género</label>
            <div class="col-xs-8">
              <select class="form-control" id="nuevaArea">
                <option value="0" data-nombre="" data-descripcion="">SELECCIONAR</option>
      <?php foreach ($viewDataAreas as $area) { ?>
                <option value="<?php echo $area['idarea']; ?>" data-nombre="<?php echo $area['nombre']; ?>" ><?php echo $area['nombre']; ?></option>
      <?php } ?>
              </select>
            </div>
          </div>
<?php } ?>

          <div class="form-group col-xs-12 col-sm-6 required">
            <label for="nuevoNivel" class="col-xs-4 control-label">Nivel</label>
            <div class="col-xs-8">
              <select class="form-control" id="nuevoNivel">
                <option value="0" data-descripcion="">SELECCIONAR</option>
              </select>
            </div>
          </div>

          <div class="form-group col-xs-12 col-sm-6 required">
            <label for="nuevoInstrumento" class="col-xs-4 control-label">Instrumento</label>
            <div class="col-xs-8">
              <select class="form-control" id="nuevoInstrumento">
                <option value="0" data-nombre="" data-descripcion="">SELECCIONAR</option>
      <?php foreach ($viewDataInstrumentos as $instrumento) { ?>
                <option value="<?php echo $instrumento['idinstrumento']; ?>" data-nombre="<?php echo $instrumento['nombre']; ?>" data-descripcion="<?php echo $instrumento['descripcion']; ?>"><?php echo $instrumento['nombre']; ?></option>
      <?php } ?>
              </select>
            </div>
          </div>

          <div class="form-group col-xs-12 col-sm-6">
            <label for="nuevaFecha" class="col-xs-4 control-label">Fecha Inscrip.</label>
            <div class="col-xs-8">
              <input type="text" class="form-control" maxlength="10" id="nuevaFecha" name="nuevaFecha" placeholder="dd/mm/aaaa" 		value="<?php echo date("d/m/Y"); ?>" 
              <?php 
			  if ($status=="inscripcion" || $status=="reempadronamiento"){ ?>disabled="disabled"<?php
			  }
			  else
			  {
			  if (!$ses->tienePermiso('','Alumno Agregar o Quitar Carrera')){ ?>
              disabled="disabled"
              <?php }} ?>
              >
            </div>
          </div>

        </div>
      </div> <!-- /.modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="button-agregar-carrera">Agregar Carrera</button>
      </div>

    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.nueva-carrera-modal -->



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

  $('#button-agregar-carrera').click(function() { //boton al agregar una carrera
    bError = false;

		if ($("#nuevaCarrera option:selected").val() == '0') {
		  $("#nueva-carrera-validations").text("El campo Carrera es Obligatorio").show();
		  bError = true;
		}
	
		if (!bError && $("#nuevoNivel option:selected").val() == '0') {
		  $("#nueva-carrera-validations").text("El campo Nivel es Obligatorio").show();
		  bError = true;
		}
		
                //Validacion: para la carrera FOBA Canto solamente se puede elegir el instrumento CANTO.
		if (!bError && $("#nuevaCarrera option:selected").val() == '<?php echo $this->POROTO->Config['carrera_foba_canto']; ?>' && $("#nuevoInstrumento option:selected").val() != '<?php echo $this->POROTO->Config['instrumento_canto']; ?>') {
		  $("#nueva-carrera-validations").text("Para la carrera elegida FOBA CANTO, solamente se puede seleccionar como instrumento CANTO").show();
		  bError = true;
		}
                //Validacion: para la carrera de FOBA INstrumentos no permitir el instrumento CANTO.
		if (!bError && $("#nuevaCarrera option:selected").val() == '<?php echo $this->POROTO->Config['carrera_foba_instrumento']; ?>' && $("#nuevoInstrumento option:selected").val() == '<?php echo $this->POROTO->Config['instrumento_canto']; ?>') {
		  $("#nueva-carrera-validations").text("Para la carrera elegida FOBA INSTRUMENTO, no se puede seleccionar como instrumento CANTO").show();
		  bError = true;
		}
	
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
		
<?php if ($status=="inscripcion") { //En la inscripcion NO permitir anotarse en mas de una carrera?>		
		if (!bError) {
			//$('#table-carreras tr').length PERMITE SACAR LA CANTIDAD DE REGISTROS DE UNA TABLA MEDIANTE JQUERY
		  if($('#table-carreras tr').length>1){
	  		  $("#nueva-carrera-validations").text("Ya tiene una carrera elegida. En la inscripción NO es posible elegir mas de una.").show();
				 bError = true;	  
			  }
		}
<?php } ?>
		
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
      newRow += '<td><small>' + $("#nuevaCarrera option:selected").attr('data-nombre') + '</small></td>';
      newRow += '<td><small>' + $("#nuevaCarrera option:selected").attr('data-descripcion') + '</small></td>';
      newRow += '<td><small>' + $("#nuevoNivel option:selected").attr('data-descripcion') + '</small></td>';
<?php if ($status=="inscripcion") { ?>
      newRow += '<td></td>';
<?php } else { ?>
      newRow += '<td><small>' + $("#nuevaArea option:selected").attr('data-nombre') + '</small></td>';
<?php } ?>
      newRow += '<td><small>' + $("#nuevoInstrumento option:selected").attr('data-nombre') + '</small></td>';
      newRow += '<td><small>' + $("#nuevaFecha").val() + '</small></td>';
      newRow += '<td></td>';
      newRow += '<td><small>NUEVA CARRERA </small><button type="button" class="btn btn-sm btn-warning" id="quitarCarrera' + qCarreras + '">Quitar</button></td>';
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

  $("#nuevaCarrera").change(function() { //Combo de carrera
    $.ajax({ url: "/<?php if ($status=="inscripcion") echo "ajaxnivelescarrerainscripcion"; else echo "ajaxnivelescarrera"; ?>/" + $(this).val(), dataType: 'json', success: function(response){
      $('#nuevoNivel').empty();
      $('#nuevoNivel').append($('<option>').text('SELECCIONAR').attr('value', 0));
      $.each(response, function(i, value) {
        $('#nuevoNivel').append($('<option>').text(value.descripcion).attr('value', value.id).attr('data-descripcion', value.descripcion));
      });    
    }});

    <?php /* el area solo se muestra para carreras que son IMP/PIMP */ ?>
    if ($("#nuevaCarrera").val() != <?php echo $this->POROTO->Config['carrera_imp']; ?> && $("#nuevaCarrera").val() != <?php echo $this->POROTO->Config['carrera_pimp']; ?>) {
      $("#nuevaArea").val(0);
      $("#nuevaArea").prop('disabled', 'disabled');
    } else {
      $("#nuevaArea").prop('disabled', false);
    }
  });
  $("#provincia").change(function() {
	  
    $.ajax({ url: "/ajaxlocalidades/" + $(this).val(), dataType: 'json', success: function(response){
      $('#localidad').empty();
      $('#localidad').append($('<option>').text('SELECCIONAR').attr('value', 0));
      $.each(response, function(i, value) {
        $('#localidad').append($('<option>').text(value.descripcion).attr('value', value.id).attr('data-cp', value.cp));
      });    
    }});
  });
  $("#localidad").change(function() {
    var valorCP = $("#localidad option:selected").attr("data-cp");
    if (valorCP != "0") $("#codpostal").val(valorCP);
  });    
<?php if ($status == "modificar") { ?>
  $("#autogenerar-clave").click(function() {
    var newP = "";
    var valores = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for (var i=0; i < 8; i++) newP += valores.charAt(Math.floor(Math.random()*valores.length));
    $("#password1").val(newP);
    $("#password2").val(newP);
  });
<?php } ?>          

</script>