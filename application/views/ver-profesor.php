<?php
$apellidoValue = isset($_POST['apellido']) ? $_POST['apellido'] : $viewData[0]['apellido'];
$nombreValue = isset($_POST['nombre']) ? $_POST['nombre'] : $viewData[0]['nombre'];
$nacionalidadValue = isset($_POST['nacionalidad']) ? $_POST['nacionalidad'] : $viewData[0]['nacionalidad'];
$fnacValue = isset($_POST['fnac']) ? $_POST['fnac'] : $viewData[0]['fnac_dmy'];
$tipdocValue = isset($_POST['tipdoc']) ? $_POST['tipdoc'] : $viewData[0]['tipodoc'];
$nrodocValue = isset($_POST['nrodoc']) ? $_POST['nrodoc'] : $viewData[0]['documentonro'];
$tel1Value = isset($_POST['telefono1']) ? $_POST['telefono1'] : $viewData[0]['telefono1'];
$tel2Value = isset($_POST['telefono2']) ? $_POST['telefono2'] : $viewData[0]['telefono2'];
$sexoValue = isset($_POST['sexo']) ? $_POST['sexo'] : $viewData[0]['sexo'];
$estcivValue = isset($_POST['estciv']) ? $_POST['estciv'] : $viewData[0]['estadocivil'];
$hijosValue = isset($_POST['hijos']) ? $_POST['hijos'] : $viewData[0]['hijos'];
$familiaresValue = isset($_POST['familiares']) ? $_POST['familiares'] : $viewData[0]['familiaresacargo'];
$cuilValue = isset($_POST['cuil']) ? $_POST['cuil'] : $viewData[0]['cuil'];

$direccionValue = isset($_POST['direccion']) ? $_POST['direccion'] : $viewData[0]['direccion'];
$numeroValue = isset($_POST['numero']) ? $_POST['numero'] : $viewData[0]['numero'];
$entrecallesValue = isset($_POST['entrecalles']) ? $_POST['entrecalles'] : $viewData[0]['entrecalles'];
$provinciaValue = isset($_POST['provincia']) ? $_POST['provincia'] : $viewData[0]['idprovincia'];
$localidadValue = isset($_POST['localidad']) ? $_POST['localidad'] : $viewData[0]['idlocalidad'];
$pisoValue = isset($_POST['piso']) ? $_POST['piso'] : $viewData[0]['piso'];
$deptoValue = isset($_POST['depto']) ? $_POST['depto'] : $viewData[0]['depto'];
$codpostalValue = isset($_POST['codpostal']) ? $_POST['codpostal'] : $viewData[0]['codpostal'];

$tituloValue = isset($_POST['titulo']) ? $_POST['titulo'] : $viewData[0]['titulohabilitante'];
$nroregistroValue = isset($_POST['nroregistro']) ? $_POST['nroregistro'] : $viewData[0]['nroregistro'];
$otorgadoporValue = isset($_POST['otorgadopor']) ? $_POST['otorgadopor'] : $viewData[0]['otorgadopor'];
$anioValue = isset($_POST['anoegreso']) ? $_POST['anoegreso'] : $viewData[0]['anio'];

//grilla

$obrasocialValue = isset($_POST['obrasocial']) ? $_POST['obrasocial'] : $viewData[0]['obrasocial'];
$contactoemergenciaValue = isset($_POST['contactoemergencia']) ? $_POST['contactoemergencia'] : $viewData[0]['contactoemergencia'];
$telefonoValue = isset($_POST['telefono']) ? $_POST['telefono'] : $viewData[0]['telefono'];
$enfermedadesValue = isset($_POST['enfermedades']) ? $_POST['enfermedades'] : $viewData[0]['enfermedades'];

$antiguedadValue = isset($_POST['antiguedad']) ? $_POST['antiguedad'] : $viewData[0]['antiguedadafecha'];
$ingColValue = isset($_POST['ingcol']) ? $_POST['ingcol'] : $viewData[0]['fechaingcolegio'];
$ingDocValue = isset($_POST['ingdoc']) ? $_POST['ingdoc'] : $viewData[0]['fechaingdocencia'];
$ingDeaValue = isset($_POST['ingdea']) ? $_POST['ingdea'] : $viewData[0]['fechaingdea'];
$modTitularesValue = isset($_POST['modulostitulares']) ? $_POST['modulostitulares'] : $viewData[0]['modulostitulares'];

$usuarioValue = isset($_POST['usuario']) ? $_POST['usuario'] : $viewData[0]['usuario'];
$emailValue = isset($_POST['email']) ? $_POST['email'] : $viewData[0]['email'];
$primeraccesoValue = isset($_POST['primeracceso']) ? $_POST['primeracceso'] : $viewData[0]['primeracceso'];
// $observacionesValue = isset($_POST['observaciones']) ? $_POST['observaciones'] : $viewData[0]['observaciones'];


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
        <input type="text" class="form-control" id="apellido" name="apellido" placeholder="" maxlength="45" value="<?php echo $apellidoValue; ?>" <?php if ($status=="misdatos2") echo "disabled"; else echo "autofocus=\"on\""; ?> autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('nombre', $validationErrors)) echo "has-error"; ?>">
      <label for="nombre" class="col-xs-4 control-label">Nombre</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="" maxlength="45" value="<?php echo $nombreValue; ?>" <?php if ($status=="misdatos2") echo "disabled"; ?> autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('nacionalidad', $validationErrors)) echo "has-error"; ?>">
      <label for="nacionalidad" class="col-xs-4 control-label">Nacionalidad</label>
      <div class="col-xs-8">
        <select class="form-control" id="nacionalidad" name="nacionalidad">
          <option value="0">SELECCIONAR</option>
<?php foreach ($viewDataNacionalidad as $nacionalidad) { ?>
          <option value="<?php echo mb_strtoupper($nacionalidad, 'UTF-8'); ?>" <?php if ($nacionalidadValue == mb_strtoupper($nacionalidad, 'UTF-8')) echo "selected"; ?>><?php echo $nacionalidad; ?></option>
<?php } ?>
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
        <?php if ($status=="reempadronamiento" || $status=="inscripcion" || $status=="misdatos") echo "<input type=\"hidden\" name=\"tipdoc\" value=\"" . $tipdocValue . "\" />"; ?>
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
        <?php if ($status=="reempadronamiento" || $status=="inscripcion" || $status=="misdatos") echo "<input type=\"hidden\" name=\"nrodoc\" value=\"" . $nrodocValue . "\" />"; ?>
        <input type="number" min="1" max="99999999" class="form-control" id="nrodoc" name="nrodoc" placeholder="" value="<?php echo $nrodocValue; ?>" <?php if ($status=="reempadronamiento" || $status=="inscripcion" || $status=="misdatos") echo "disabled"; ?> autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('telefono1', $validationErrors)) echo "has-error"; ?>">
      <label for="telefono1" class="col-xs-4 control-label">Tel.Fijo</label>
      <div class="col-xs-8">
        <input type="tel" class="form-control" id="telefono1" name="telefono1" placeholder="" value="<?php echo $tel1Value; ?>"  autocomplete="off" >
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

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('hijos', $validationErrors)) echo "has-error"; ?>">
      <label for="hijos" class="col-xs-4 control-label">Hijos</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="hijos" name="hijos" placeholder="" maxlength="45" value="<?php echo $hijosValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('familiares', $validationErrors)) echo "has-error"; ?>">
      <label for="familiares" class="col-xs-4 control-label">Familiares a Cargo</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="familiares" name="familiares" placeholder="" maxlength="45" value="<?php echo $familiaresValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('cuil', $validationErrors)) echo "has-error"; ?>">
      <label for="cuil" class="col-xs-4 control-label">CUIL/CUIT</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="cuil" name="cuil" placeholder="" maxlength="11" value="<?php echo $cuilValue; ?>"  autocomplete="off" />
      </div>
    </div>


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
      <div class="col-xs-8">
        <select class="form-control" id="localidad" name="localidad" >
          <option value="0">SELECCIONAR</option>

<?php foreach ($viewDataLocalidades as $localidad) { ?>
          <option value="<?php echo $localidad['id']; ?>" data-cp="<?php echo $localidad['cp']; ?>" <?php if ($localidadValue == $localidad['id']) echo "selected"; ?>><?php echo $localidad['descripcion']; ?></option>
<?php } ?>
        </select>
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
        <input type="text" class="form-control" id="codpostal" name="codpostal" placeholder="" maxlength="45" value="<?php echo $codpostalValue; ?>" autocomplete="off" />
      </div>
    </div>
  </fieldset>



  <fieldset>
    <h3>Estudios</h3>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('titulo', $validationErrors)) echo "has-error"; ?>">
      <label for="titulo" class="col-xs-4 control-label">Título Hab.</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="titulo" name="titulo" maxlength="45" placeholder="" value="<?php echo $tituloValue; ?>" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('nroregistro', $validationErrors)) echo "has-error"; ?>">
      <label for="nroregistro" class="col-xs-4 control-label">Nº Registro</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="nroregistro" name="nroregistro" maxlength="45" placeholder="" value="<?php echo $tituloValue; ?>" autocomplete="off" />
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
        <input type="number" min="1930" max="<?php echo date("Y") ?>" class="form-control" id="anoegreso" name="anoegreso" placeholder="" value="<?php echo $anioValue; ?>" >
      </div>
    </div>

    <div class="clearfix"></div>

    <h3>Otros Certificados/Cursos</h3>
      <div class="panel panel-default">
        <div class="panel-body  <?php if (array_key_exists('carreras', $validationErrors)) echo "bg-danger"; ?>">
          <table class="table" id="table-certificados">
            <thead>
              <tr>
                <th>Título</th>
                <th>Otorgado por</th>
                <th>Año de Egreso</th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              <?php $i=1; foreach ($viewDataCertificados as $certif) { ?>
              <tr id="newCertificadoDB<?php echo $i; ?>">
                <td><small><?php echo $certif['titulo']; ?></small></td>
                <td><small><?php echo $certif['otorgadopor']; ?></small></td>
                <td><small><?php echo $certif['anio']; ?></small></td>
                <td><button type="button" class="btn btn-sm btn-warning" id="quitarCertificadoDB<?php echo $i; ?>">Quitar</button></td>
                <input type="hidden" name="new-certificados[]" value="<?php echo $certif['titulo']; ?>~**~<?php echo $certif['otorgadopor']; ?>~**~<?php echo $certif['anio']; ?>" />
              </tr>
<script type="text/javascript">
  $('#quitarCertificadoDB<?php echo $i; ?>').click(function() { $('#newCertificadoDB<?php echo $i; ?>').remove(); });
</script>
              <?php $i++;
              } ?>
              <?php $i=1; foreach ($newCertificados as $newCertificado) { ?>
              <tr id="newCertificadoN<?php echo $i; ?>">
                <td><small><?php echo $newCertificado['titulo']; ?></small></td>
                <td><small><?php echo $newCertificado['otorgadopor']; ?></small></td>
                <td><small><?php echo $newCertificado['anio']; ?></small></td>
                <td><button type="button" class="btn btn-sm btn-warning" id="quitarCertificado<?php echo $i; ?>">Quitar</button></td>
                <input type="hidden" name="new-certificados[]" value="<?php echo $newCertificado['raw']; ?>" />
              </tr>
<script type="text/javascript">
  $('#quitarCertificado<?php echo $i; ?>').click(function() { $('#newCertificadoN<?php echo $i; ?>').remove(); });
</script>
              <?php $i++;
              } ?>
            </tbody>
          </table>

          <div class="form-group">
            <div class="col-xs-12">
              <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target=".nuevo-certificado-modal">Agregar Certificado</button>
            </div>
          </div>
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
<?php if ($status != "crear") { ?>
      <div class="form-group col-xs-12 col-sm-6 col-lg-4 <?php if (array_key_exists('usuario', $validationErrors)) echo "has-error"; ?>">
        <label for="usuario" class="col-xs-4 control-label">Usuario</label>
        <div class="col-xs-8">
          <input type="text" class="form-control" id="usuario" name="usuario" maxlength="45" placeholder="" value="<?php echo $usuarioValue; ?>" <?php if ($status=="reempadronamiento" || $status=="inscripcion" || $status=="modificar" || $status=="misdatos") echo "disabled"; ?> >
        <?php if ($status=="reempadronamiento" || $status=="inscripcion" || $status=="modificar" || $status=="misdatos") { ?>
          <input type="hidden" name="usuario" value="<?php echo $usuarioValue; ?>" />
        <?php } ?>
        </div>
      </div>
<?php } ?>          

      <div class="form-group col-xs-12 col-sm-6 col-lg-4 required <?php if (array_key_exists('email', $validationErrors)) echo "has-error"; ?>">
        <label for="email" class="col-xs-4 control-label">Email</label>
        <div class="col-xs-8">
          <input type="email" class="form-control" id="email" name="email" maxlength="45" placeholder="" value="<?php echo $emailValue; ?>" autocomplete="off" />
        </div>
      </div>

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

<?php if ($status == "modificar") { ?>
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

<?php if ($status == "crear") { ?>
        <div class="form-group col-xs-12 col-sm-6 col-lg-4">
            <button type="button" id="autogenerar-clave" class="btn btn-sm btn-info">Autogenerar Clave</button>
        </div>  
<?php } ?>          

  </fieldset>

  <fieldset>
    <h3>Datos</h3>
    <div class="row">

      <div class="form-group col-xs-12 required <?php if (array_key_exists('antiguedad', $validationErrors)) echo "has-error"; ?>">
        <label for="antiguedad" class="col-xs-4 control-label">Antiguedad Docente al 01/03/2016 (años)</label>
        <div class="col-xs-8">
          <input type="number" class="form-control" id="antiguedad" name="antiguedad" maxlength="3" min="0" max="99" placeholder="" value="<?php echo $antiguedadValue; ?>" autocomplete="off" />
        </div>
      </div>

      <div class="form-group col-xs-12 required <?php if (array_key_exists('ingcol', $validationErrors)) echo "has-error"; ?>">
        <label for="ingcol" class="col-xs-4 control-label">Fecha Ingreso a la Escuela</label>
        <div class="col-xs-8">
          <input type="text" class="form-control" maxlength="10" id="ingcol" name="ingcol" placeholder="dd/mm/aaaa" value="<?php echo $ingColValue; ?>" autocomplete="off" >
        </div>
      </div>

      <div class="form-group col-xs-12 required <?php if (array_key_exists('ingdoc', $validationErrors)) echo "has-error"; ?>">
        <label for="ingdoc" class="col-xs-4 control-label">Fecha Ingreso a la Docencia Provincial</label>
        <div class="col-xs-8">
          <input type="text" class="form-control" maxlength="10" id="ingdoc" name="ingdoc" placeholder="dd/mm/aaaa" value="<?php echo $ingDocValue; ?>" autocomplete="off" >
        </div>
      </div>

      <div class="form-group col-xs-12 required <?php if (array_key_exists('ingdea', $validationErrors)) echo "has-error"; ?>">
        <label for="ingdea" class="col-xs-4 control-label">Fecha Ingreso a la DEA</label>
        <div class="col-xs-8">
          <input type="text" class="form-control" maxlength="10" id="ingdea" name="ingdea" placeholder="dd/mm/aaaa" value="<?php echo $ingDeaValue; ?>" autocomplete="off" >
        </div>
      </div>

      <div class="form-group col-xs-12">
        <div class="col-sm-offset-4 col-sm-8">
          <div class="checkbox">
            <label for="modulostitulares">
              <input type="checkbox" name="modulostitulares" id="modulostitulares" value="modulostitulares" <?php if ($modTitularesValue == 1) echo "checked"; ?> > Tiene módulos titulares
            </label>
          </div>
        </div>
      </div>

<?php if ($status != "crear") { ?>
      <div class="form-group col-xs-12">
        <label class="col-xs-4 control-label">Módulos titulares</label>
        <div class="col-xs-8">
          <input type="text" class="form-control" maxlength="10" value="<?php echo $modTit; ?>" disabled />
        </div>
      </div>

      <div class="form-group col-xs-12">
        <label class="col-xs-4 control-label">Módulos provisionales</label>
        <div class="col-xs-8">
          <input type="text" class="form-control" maxlength="10" value="<?php echo $modPro; ?>" disabled />
        </div>
      </div>

      <div class="form-group col-xs-12">
        <label class="col-xs-4 control-label">Módulos suplentes</label>
        <div class="col-xs-8">
          <input type="text" class="form-control" maxlength="10" value="<?php echo $modSup; ?>" disabled />
        </div>
      </div>
<?php } ?>

    </div>
  </fieldset>



  <div class="form-group">
    <div class=" col-xs-12">
      <button type="submit" class="btn btn-lg btn-primary"><?php if ($status=="crear") echo "Crear"; ?><?php if ($status=="modificar") echo "Modificar"; ?><?php if ($status=="misdatos") echo "Actualizar"; ?></button>
    </div>
  </div>

</form>


<div class="modal fade nuevo-certificado-modal" tabindex="-1" role="dialog" aria-labelledby="nuevo-certificado-title">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="nuevo-certificado-title">Agregar un nuevo Certificado/Curso</h4>
      </div>
      
      <div class="modal-body">
        <div class="alert alert-danger collapse" id="nuevo-certificado-validations" role="alert"></div>
        <div class="row">
          <div class="form-group col-xs-12 col-sm-6 required">
            <label for="nuevo-titulo" class="col-xs-4 control-label">Título</label>
            <div class="col-xs-8">
              <input type="text" class="form-control" id="nuevo-titulo" name="nuevo-titulo" placeholder="" maxlength="150" value="" autocomplete="off" />
            </div>
          </div>
          <div class="form-group col-xs-12 col-sm-6 required">
            <label for="nuevo-otorgado" class="col-xs-4 control-label">Otorgado por</label>
            <div class="col-xs-8">
              <input type="text" class="form-control" id="nuevo-otorgado" name="nuevo-otorgado" placeholder="" maxlength="150" value="" autocomplete="off" />
            </div>
          </div>

          <div class="form-group col-xs-12 col-sm-6 required">
            <label for="nuevo-anio" class="col-xs-4 control-label">Año de Egreso</label>
            <div class="col-xs-8">
              <input type="number" min="1930" max="<?php echo date("Y") ?>" class="form-control" id="nuevo-anio" name="nuevo-anio" placeholder="" value="" >
            </div>
          </div>


        </div>
      </div> <!-- /.modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        <button type="button" class="btn btn-primary" id="button-agregar-certificado">Agregar Certificado/Curso</button>
      </div>

    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.nuevo-certificado-modal -->



<script type="text/javascript">
  var qCertificados = <?php echo count($newCertificados); ?>;

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


<?php if ($status == "crear") { ?>
  $("#autogenerar-clave").click(function() {
    var newP = "";
    var valores = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    for (var i=0; i < 8; i++) newP += valores.charAt(Math.floor(Math.random()*valores.length));
    $("#password1").val(newP);
    $("#password2").val(newP);
  });
<?php } ?>          

  $('.nuevo-certificado-modal').on('hide.bs.modal', function (e) {
      $("#nuevo-certificado-validations").text("").hide();
  });

  $('.nuevo-certificado-modal').on('shown.bs.modal', function (e) {
      $("#nuevo-titulo").focus();
  });

  $('#button-agregar-certificado').click(function() {
    $("#nuevo-titulo").val($("#nuevo-titulo").val().toUpperCase());
    $("#nuevo-otorgado").val($("#nuevo-otorgado").val().toUpperCase());
    bError = false;

    if (!bError && $("#nuevo-titulo").val() == '') {
      $("#nuevo-certificado-validations").text("El campo Título es Obligatorio").show();
      bError = true;
    }
    if (!bError && $("#nuevo-otorgado").val() == '') {
      $("#nuevo-certificado-validations").text("El campo Título es Obligatorio").show();
      bError = true;
    }
    if (!bError && $("#nuevo-anio").val() == '') {
      $("#nuevo-certificado-validations").text("El campo Año es Obligatorio").show();
      bError = true;
    }

    if (!bError) {
      qCertificados++;
      newCertificadoValue = $("#nuevo-titulo").val();
      newCertificadoValue += '~**~' + $("#nuevo-otorgado").val();
      newCertificadoValue += '~**~' + $("#nuevo-anio").val();
      newRow = '<tr id="newCertificadoN' + qCertificados + '">';
      newRow += '<td><small>' + $("#nuevo-titulo").val() + '</small></td>';
      newRow += '<td><small>' + $("#nuevo-otorgado").val() + '</small></td>';
      newRow += '<td><small>' + $("#nuevo-anio").val() + '</small></td>';
      newRow += '<td><button type="button" class="btn btn-sm btn-warning" id="quitarCertificado' + qCertificados + '">Quitar</button></td>';
      newRow += '<input type="hidden" name="new-certificados[]" value="' + newCertificadoValue +'" />';
      newRow += '</tr>';
      $("#table-certificados > tbody:last-child").append(newRow);
      $('#quitarCertificado' + qCertificados).click(function() { $('#newCertificadoN' + qCertificados).remove(); });
      $('.nuevo-certificado-modal').modal('hide');

      $("#nuevo-titulo").val('');
      $("#nuevo-otorgado").val('');
      $("#nuevo-anio").val('');
      
    }
  })


</script>