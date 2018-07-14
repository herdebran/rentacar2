<style>
input { text-transform: uppercase; }
.small, small { font-size: 82% !important;  }
</style>
<form class="form-horizontal" action="" method="POST" accept-charset="utf-8">  
  <fieldset>
    <h3>Búsqueda de Alumnos</h3>
    <p>Complete al menos un campo para realizar la búsqueda</p>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4">
      <label for="apellido" class="col-xs-4 control-label">Apellido</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="apellido" name="apellido" placeholder="" maxlength="45" value="" autofocus="on" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4">
      <label for="nombre" class="col-xs-4 control-label">Nombre</label>
      <div class="col-xs-8">
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="" maxlength="45" value="" autocomplete="off" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4">
      <label for="tipdoc" class="col-xs-4 control-label">Tipo Doc.</label>
      <div class="col-xs-8">
        <select class="form-control" id="tipdoc" name="tipdoc">
          <option value="0"></option>
<?php foreach ($viewDataTipoDocumento as $tipdoc) { ?>
          <option value="<?php echo $tipdoc['id']; ?>"><?php echo $tipdoc['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4">
      <label for="nrodoc" class="col-xs-4 control-label">Nro.Doc.</label>
      <div class="col-xs-8">
        <input type="number" min="1" max="99999999" class="form-control" id="nrodoc" name="nrodoc" placeholder="" value="" />
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4">
      <label for="estalu" class="col-xs-4 control-label">Estado Alumno</label>
      <div class="col-xs-8">
        <select class="form-control" id="estalu" name="estalu">
          <option value="0"></option>
<?php foreach ($viewDataEstadoAlumnos as $estado) { ?>
          <option value="<?php echo $estado['id']; ?>"><?php echo $estado['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group col-xs-12 col-sm-6 col-lg-4">
      <label for="carrera" class="col-xs-4 control-label">Carrera / Ciclo</label>
      <div class="col-xs-8">
        <select class="form-control" id="carrera" name="carrera">
          <option value="0"></option>
<?php foreach ($viewDataCarreras as $carrera) { ?>
          <option value="<?php echo $carrera['idcarrera']; ?>"><?php echo $carrera['descripcion']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <div class="col-xs-12">
        <button id="btnBuscar" type="button" class="btn btn-primary">Buscar</button>
        <button id="btnReset" type="button" class="btn btn-default">Reset Búsqueda</button>
        <a href="/crearalumno" class="btn btn-success pull-right">Crear Alumno</a>
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
                <th>Apellido</th>
                <th>Nombre</th>
                <th>Documento</th>
                <th>Estado</th>
                <th title="Último Acceso">Ult.Acceso</th>
                <th>Carrera / Ciclo</th>
                <th title="Habilitado">Hab.</th>
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
    $().ready(function(){
       <?php if ($message['param']!="") {  
      $arrData = explode("&" , $message['param']); 
      foreach ($arrData as $dat) {
        if (substr($dat, 0, 2)=="ap") $ap=substr($dat, 3);
        if (substr($dat, 0, 2)=="no") $no=substr($dat, 3);
        if (substr($dat, 0, 2)=="td") $td=substr($dat, 3);
        if (substr($dat, 0, 2)=="nd") $nd=substr($dat, 3);
        if (substr($dat, 0, 2)=="ea") $ea=substr($dat, 3);
        if (substr($dat, 0, 2)=="ca") $ca=substr($dat, 3);
        if (substr($dat, 0, 2)=="pg") $pg=substr($dat, 3);
      }
      ?>
    $('#apellido').val('<?php echo $ap; ?>');
    $('#nombre').val('<?php echo $no; ?>');
    $('#tipdoc').val(<?php echo $td; ?>);
    $('#nrodoc').val(<?php echo $nd; ?>);
    $('#estalu').val(<?php echo $ea; ?>);
    $('#carrera').val(<?php echo $ca; ?>);
    JumpToPage(<?php echo $pg; ?>);
  <?php } ?>
  
  $("#btnReset").click(function() {
    $('#nombre').val('');
    $('#tipdoc').val(0);
    $('#nrodoc').val('');
    $('#estalu').val(0);
    $('#carrera').val(0);
    $('#apellido').val('').focus();
  });
  function JumpToPage(page) {
    var sData = 'ap=' + $('#apellido').val();
    sData += '&no=' + $('#nombre').val();
    sData += '&td=' + $('#tipdoc').val();
    sData += '&nd=' + $('#nrodoc').val();
    sData += '&ea=' + $('#estalu').val();
    sData += '&ca=' + $('#carrera').val();
    sData1=sData;
    sData += '&pg=' + page;
    window.history.pushState(window.history.state, "<?php echo $pageTitle; ?>", "/gestion-alumnos/" + sData);

    $('#table-resultados > tbody').empty();
    if (sData1=='ap=&no=&td=0&nd=&ea=0&ca=0') {
      $("#table-resultados > tbody:last-child").append('<tr><td colspan=8>Ingrese al menos un filtro</td></tr>');
    } else {
      $("#table-resultados > tbody:last-child").append('<tr><td colspan=8>Cargando registros...</td></tr>');
      $.ajax({ url: "/ajaxalumnos/" + page, method: 'POST', data: sData, dataType: 'json', success: function(response){
        $('#table-resultados > tbody').empty();
        if (response.rows.length==0) {
          $("#table-resultados > tbody:last-child").append('<tr><td colspan=8>No hay resultados que coincidan con los criterios de busqueda</td></tr>');
        } else {
          var lastdoc="";
          $.each(response.rows, function(i, value) {
            bCambio = false;
            if (lastdoc!=(value.tdoc + ' ' + value.documentonro) ) {
              lastdoc = value.tdoc + ' ' + value.documentonro;
              bCambio = true;
            }
            newRow = '<tr>';
            if (!bCambio) {
              newRow += '<td colspan=5><small>&nbsp;</small></td>';
            } else {
              newRow += '<td><small>' + value.apellido + '</small></td>';
              newRow += '<td><small>' + value.nombre + '</small></td>';
              newRow += '<td><small>' + value.tdoc + ' ' + value.documentonro + '</small></td>';
              newRow += '<td><small>' + value.estalu + '</small></td>';
              newRow += '<td><small>' + value.lastlogin + '</small></td>';
            }
            if (value.carreradescripcion == '') newRow += '<td><small>No posee ninguna carrera</small></td>'; else newRow += '<td><small>' + value.carreradescripcion + ' - ' + value.instrumento + '</small></td>';
            if (value.estado == '1') newRow += '<td><small>Si</small></td>'; else newRow += '<td><small>No</small></td>';
            newRow += '<td>';
            newRow += '<a href="/modificar/' + value.idpersona +'/' + sData  +'" class="btn btn-default btn-xs" aria-label="Modificar Registro" title="Modificar Registro"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></a>';
            if (value.estalu == 'NIVELACIÓN') {
              newRow += '<a href="/nivelar/' + value.idpersona + '/' + value.idalumnocarrera +'/' + sData + '" class="btn btn-default btn-xs" aria-label="Nivelar" title="Nivelar"><span class="glyphicon glyphicon-align-justify" aria-hidden="true"></span></a>';
            }
            if (value.estado == '1') {
              newRow += '<a href="/deshabilitar/' + value.idpersona +'/' + sData + '" class="btn btn-default btn-xs" aria-label="Deshabilitar" title="Deshabilitar"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></a>';
            } else {
              newRow += '<a href="/habilitar/' + value.idpersona +'/' + sData + '" class="btn btn-default btn-xs" aria-label="Habilitar" title="Habilitar"><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></a>';
            }
            newRow += '<a href="/resetpassword/' + value.idpersona +'/' + sData + '" class="btn btn-default btn-xs" aria-label="Resetear Clave" title="Resetear Clave"><span class="glyphicon glyphicon-erase" aria-hidden="true"></span></append>';
            if (value.carreradescripcion != '') newRow += '<a href="/matricular/'+  value.idcarrera + '/' + value.idalumnocarrera +'/' + value.idpersona +'/' + sData + '" class="btn btn-default btn-xs" aria-label="Matricular Alumno" title="Matricular Alumno"><span class="glyphicon glyphicon-book" aria-hidden="true"></span></a>';
            <?php 
            if($ses->tienePermiso('','Analitico Acceso desde Gestion Alumnos')){
            ?>
            if (value.carreradescripcion != '') newRow += '<a href="/analitico/'+  value.idalumnocarrera + '/' + value.idpersona + '" class="btn btn-default btn-xs" aria-label="Ver Analítico" title="Ver Analítico"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></a>';
            <?php } 
            if($ses->tienePermiso('','Gestion de Equivalencias')){
            ?>
            if (value.carreradescripcion != '') newRow += '<a href="/gestion-equivalencias/'+  value.idpersona + '" class="btn btn-default btn-xs" aria-label="Gestión de Equivalencias" title="Gestión de Equivalencias"><span class="glyphicon glyphicon-random" aria-hidden="true"></span></a>';
            <?php } ?>
            newRow += '</td>';
            
            newRow += '</tr>';

            $("#table-resultados > tbody:last-child").append(newRow);
          });
          var from = parseInt(response.currentpage) - 2;
          if (from < 1) from = 1;
          var to = parseInt(response.currentpage) + 2;
          if (to > response.pages) to=response.pages;


          var navigationRow = '<tr><td colspan=5><small>Pagina ' + response.currentpage + '/' + response.pages + ' (total registros ' + response.qrows.q + ')</small></td>';
          navigationRow+= '<td colspan=2>';
          navigationRow+= '<nav aria-label="Navegación de Resultados">';
          navigationRow+= '  <ul class="pull-right pagination pagination-sm">';

          if (from > 1) {
            if (response.currentpage != 1) {
              navigationRow+= '    <li><a href="#" class="navigation-anchor" data-page="1">1</a></li>';           
            } else {
              navigationRow+= '    <li class="active"><span>1</span></li>';           
            }
          }

          for (i=from; i<=to; i++) {
            if (response.currentpage != i) {
              navigationRow+= '    <li><a href="#" class="navigation-anchor" data-page="' + i + '">' + i + '</a></li>';
            } else {
              navigationRow+= '    <li class="active"><span>' + i + '</span></li>';
            }
          }

          if (to<response.pages) {
            if (response.currentpage != response.pages) {
              navigationRow+= '    <li><a href="#" class="navigation-anchor" data-page="' + response.pages + '">' + response.pages + '</a></li>';           
            } else {
              navigationRow+= '    <li class="active"><span>' + response.pages + '</span></li>';           
            }
          } 

          navigationRow+= '  </ul>';
          navigationRow+= '</nav>';

          navigationRow+= '</td>';
          navigationRow+= '</tr>';
          $("#table-resultados > tbody:last-child").append(navigationRow);
          $(".navigation-anchor").click(function() {
            JumpToPage($(this).attr('data-page'));
          });  
        }    
      }});
    }    

  }
  $("#btnBuscar").click(function() {
    JumpToPage(1);
  });
  $("#apellido").keypress(function(e){ if(e.which == 13) JumpToPage(1); });
  $("#nombre").keypress(function(e){ if(e.which == 13) JumpToPage(1); });
  $("#nrodoc").keypress(function(e){ if(e.which == 13) JumpToPage(1); });
 
    });
  
</script>