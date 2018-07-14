<style>
input { text-transform: uppercase; }
.small, small { font-size: 82% !important;  }


    /* The Modal (background) */
    .modal {
        display: none;
        /* Hidden by default */
        position: fixed;
        /* Stay in place */
        z-index: 1;
        /* Sit on top */
        padding-top: 100px;
        /* Location of the box */
        left: 0;
        top: 0;
        width: 100%;
        /* Full width */
        height: 100%;
        /* Full height */
        overflow: auto;
        /* Enable scroll if needed */
        background-color: rgb(0, 0, 0);
        /* Fallback color */
        background-color: rgba(0, 0, 0, 0.4);
        /* Black w/ opacity */
    }
    /* Modal Content */
    .modal-content {
        background-color: #fefefe;
        margin: auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
    }
    /* The Close Button */
    .close {
        color: #aaaaaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close:hover,
    .close:focus {
        color: #000;
        text-decoration: none;
        cursor: pointer;
    }
    input {
        text-transform: uppercase;
    }
    .small,
    small { 
        font-size: 82% !important;
    }
</style>

<form class="form-horizontal" action="" method="POST" accept-charset="utf-8">  
  <fieldset>
    <h3>Búsqueda de Profesores</h3>
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
      <label for="materia" class="col-xs-4 control-label">Materia</label>
      <div class="col-xs-8">
        <select class="form-control" id="materia" name="materia">
          <option value="0"></option>
<?php foreach ($viewDataMaterias as $materia) { ?>
          <option value="<?php echo $materia['idmateria']; ?>"><?php echo $materia['nombre']; ?></option>
<?php } ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <div class="col-xs-12">
        <button id="btnBuscar" type="button" class="btn btn-primary">Buscar</button>
        <button id="btnReset" type="button" class="btn btn-default">Reset Búsqueda</button>
        <?php if ($ses->tienePermiso('', 'Gestion de Profesores Agregar o Modificar Profesor')) { ?>
                    <button class="btn btn-info" type="button" aria-label="Notificar" title="Notificacion Masiva" id="notificacionButton">Notificar Profesores
                        <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
                    </button>
                <?php } ?>
        <a href="/profesores/crear" class="btn btn-success pull-right">Crear Profesor</a>
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
                <th>Apellido</th>
                <th>Nombre</th>
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

<div class="modal" id="emaiModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="exampleModalLabel">Notificación personas</h4>
            </div>
            <div class="modal-body">
                <form id="mail-body">
                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Receptores:</label>
                        <input type="text" class="form-control" id="recipient-name" disabled="" />
                        <br>
                        <label for="recipient-add-name" class="control-label">Agregar Destinatarios:</label>
                        <input id="recipient-add-name" type="text" class="form-control" id="recipient-name">
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="5" id="comment"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="cancelEmail">Cancelar</button>
                <button type="button" class="btn btn-primary" id="sendEmail">Enviar</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $().ready(function () {

        var table;
        table = $("#table-resultados").DataTable({
            language: {
                url: "http://cdn.datatables.net/plug-ins/1.10.16/i18n/Spanish.json"
            },
            processing: false,
            serverSide: false,
            scrollX: true,
            ajax: {
                url: "/cooperadora/personasConFiltro",
                method: "post",
                data: function (data) {
                    data = {};
                    data.nombre = $("#nombre").val();
                    data.apellido = $("#apellido").val();
                    data.tipdoc = $("#tipdoc").val();
                    data.nrodoc = $("#nrodoc").val();
                    data.carrera = $("#carrera").val();
                    data.materia = $("#materia").val();
                    data.instrumento = $("#instrumento").val();
                    data.rol = $("#rol").val();
                    data.estadocoop = $("#estadocoop").val();
                    data.mespago = $("#mespago").val();
                    data.pago = $("#pago").val();
                    sessionStorage.setItem("busqueda", JSON.stringify(data));
                    return {'filtros': data};
                },
                error: function (err1) {
                    addGlobalMessage("alert-danger", "\
                        <p>Error de comunicación Ajax: Ah ocurrido un error al obtener los datos desde el servidor.</p>\
                        <p>Por favor vuelva a intentarlo en unos minutos.</p>\
                        <p>Si el problema persiste comuniquese con el administrador del sistema.</p>");
                },
                complete: function () {
                    $('#loadingModal').hide();
                    $("#btnBuscar").attr("disabled", false);
                }
            },
            columns: [
                {data: 'tipodocynro',
                    width: "10%"
                },
                {data: 'apeynom'},
                {data: 'rol'},
                {data: 'carreranombre'},
                {data: 'estadocooperadora'},
                {data: 'fechaultimopago'},
                {data: 'acciones',
                    width: "5%",
                    render: function (data, type, row, meta) {
                        return '<a type="button" class="btn btn-default btn-xs" href="cooperadora/detalle/' + row["idpersona"] + '"><i class="fa fa-money" ></i></a>';
                    }
                }
            ]
        });
        //ENVIO DE EMAIL ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Cierra el modal de email.
        function closeEmailModal() {
            var ok = confirm("¿Seguro que desea cancelar el envio?");
            if (ok) {
                $('#emaiModal').hide();
            }
        }
        // Cuando el usuario clickea el boton de notificacion se abre el modal de email.
        $("#notificacionButton").click(function () {
            let personasFiltradas = table.rows({filter: 'applied'}).data();
            let mails = [];
            $.each(personasFiltradas, function (key, persona) {
                mails.push(persona.email);
            });
            $('#emaiModal').show();
            $('#recipient-name').val(mails.join(","));
        });
        // When the user clicks on <span> (x), close the modal
        $(".close").click(function () {
            closeEmailModal();
        });
        // When the user clicks on <button> (Cancelar), close the modal
        $("#cancelEmail").click(function () {
            var ok = confirm("¿seguro que desea cancelar el envio?");
            if (ok) {
                $('#emaiModal').hide();
            }
        });
        // When the user clicks anywhere outside of the modal, close it
        $(window).click(function (event) {
            if (event.target == $('#emaiModal')[0]) {
                var ok = confirm("¿seguro que desea cancelar el envio?");
                if (ok) {
                    $('#emaiModal').hide();
                }
            }
        });
        function isEmail(email) {
            var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
            return regex.test(email);
        }
        // When the user clicks on <button> (Enviar)
        $("#sendEmail").click(function () {
            // obtengo los correos adisionales
            var emailExtras = $("#recipient-add-name").val();
            // limpio espacios en blanco
            emailExtras = emailExtras.split(" ").join("");
            emailExtras = emailExtras.split(",");
            var sent = true;
            var email = [];
            // armo un arreglo con todos los correos no vacios
            $.each(emailExtras, function (i, mail) {
                if (mail !== "") {
                    email.push(mail);
                }
            });
            // reviso que sean todos emails validos
            $.each(email, function (i, mail) {
                if (!isEmail(mail)) {
                    sent = false;
                }
            });
            // se ejecuta el envio de emails por ajax
            if (sent) {
                var mailData = {};
                mailData.sendto = $('#recipient-name').val();
                mailData.sentoadd = $("#recipient-add-name").val();
                mailData.body = $("#comment").val();
                mailData.personaId = <?php echo $ses->getIdPersona(); ?>;
                mailData.rol = <?php echo $ses->getIdRole(); ?>;
                request = $.ajax({
                    url: "/cooperadora/ajaxsendmail",
                    method: 'POST',
                    data: mailData,
                    dataType: 'json',
                    timeout: 50000,
                    async: true
                });
                // Callback handler that will be called on success
                request.done(function (response, textStatus, jqXHR) {
                    alert("Mensaje Enviado");
                });
                // Callback handler that will be called on failure
                request.fail(function (jqXHR, textStatus) {
                    addGlobalMessage("alert-danger", "\
                        <p>Error de comunicación Ajax: Ah ocurrido un error durante la comunicacion con el servidor.</p>\
                        <p>Por favor vuelva a intentarlo en unos minutos.</p>\
                        <p>Si el problema persiste comuniquese con el administrador del sistema.</p>");
                });
                request.always(function () {
                    // Reenable the inputs
                    $('#emaiModal').hide();
                });
            } else {
                alert("El formato de uno de los correos adicionales no es correcto");
            }
        });

    });
    
  <?php if ($message['param']!="") { 
      $arrData = explode("&" , $message['param']); 
      foreach ($arrData as $dat) {
        if (substr($dat, 0, 2)=="ap") $ap=substr($dat, 3);
        if (substr($dat, 0, 2)=="no") $no=substr($dat, 3);
        if (substr($dat, 0, 2)=="td") $td=substr($dat, 3);
        if (substr($dat, 0, 2)=="nd") $nd=substr($dat, 3);
        if (substr($dat, 0, 2)=="ma") $ma=substr($dat, 3);
        if (substr($dat, 0, 2)=="pg") $pg=substr($dat, 3);
      }
      ?>
    $('#apellido').val('<?php echo $ap; ?>');
    $('#nombre').val('<?php echo $no; ?>');
    $('#tipdoc').val(<?php echo $td; ?>);
    $('#nrodoc').val(<?php echo $nd; ?>);
    $('#materia').val(<?php echo $ma; ?>);
    JumpToPage(<?php echo $pg; ?>);
  <?php } ?>
  
  $("#btnReset").click(function() {
    $('#nombre').val('');
    $('#tipdoc').val(0);
    $('#nrodoc').val('');
    $('#materia').val(0);
    $('#apellido').val('').focus();
  });
  function JumpToPage(page) {
    var sData = 'ap=' + $('#apellido').val();
    sData += '&no=' + $('#nombre').val();
    sData += '&td=' + $('#tipdoc').val();
    sData += '&nd=' + $('#nrodoc').val();
    sData += '&ma=' + $('#materia').val();
    sData1=sData;
    sData += '&pg=' + page;
    $('#table-resultados > tbody').empty();
    if (sData1=='ap=&no=&td=0&nd=&ma=0') {
      $("#table-resultados > tbody:last-child").append('<tr><td colspan=5>Ingrese al menos un filtro</td></tr>');
    } else {
      $.ajax({ url: "/profesores/ajaxlist/" + page, method: 'POST', data: sData, dataType: 'json', success: function(response){
        if (response.rows.length==0) {
          $("#table-resultados > tbody:last-child").append('<tr><td colspan=5>No hay resultados que coincidan con los criterios de busqueda</td></tr>');
        } else {
          $.each(response.rows, function(i, value) {
            newRow = '<tr>';
            newRow += '<td><small>' + value.idpersona + '</small></td>';
            newRow += '<td><small>' + value.apellido + '</small></td>';
            newRow += '<td><small>' + value.nombre + '</small></td>';
            if (value.estado == '1') newRow += '<td><small>Si</small></td>'; else newRow += '<td><small>No</small></td>';
            newRow += '<td>';
			//Acciones
			<?php		
			//Cambio 38 Leo 20170706
			if($ses->tienePermiso('','Gestion de Profesores Agregar o Modificar Profesor')){ 
			?>
            newRow += '<a href="/profesores/modificar/' + value.idpersona +'/' + sData  +'" class="btn btn-default btn-xs" aria-label="Modificar Registro" title="Modificar Registro"><span class="glyphicon glyphicon-pencil" aria-hidden="true"></span></button>';
			<?php 
			} 	
			if($ses->tienePermiso('','Gestion de Usuarios Habilitacion y Cambio de contraseña')){ 
			?>
            if (value.estado == '1') {
              newRow += '<a href="/profesores/deshabilitar/' + value.idpersona +'/' + sData + '" class="btn btn-default btn-xs" aria-label="Deshabilitar" title="Deshabilitar"><span class="glyphicon glyphicon-remove-circle" aria-hidden="true"></span></button>';
            } else {
              newRow += '<a href="/profesores/habilitar/' + value.idpersona +'/' + sData + '" class="btn btn-default btn-xs" aria-label="Habilitar" title="Habilitar"><span class="glyphicon glyphicon-ok-circle" aria-hidden="true"></span></button>';
            }
            newRow += '<a href="/profesores/resetpassword/' + value.idpersona +'/' + sData + '" class="btn btn-default btn-xs" aria-label="Resetear Clave" title="Resetear Clave"><span class="glyphicon glyphicon-erase" aria-hidden="true"></span></button>';
			<?php 
			 } 
			 ?>
            newRow += '</td>';
            
            newRow += '</tr>';

            $("#table-resultados > tbody:last-child").append(newRow);
          });
          var from = parseInt(response.currentpage) - 2;
          if (from < 1) from = 1;
          var to = parseInt(response.currentpage) + 2;
          if (to > response.pages) to=response.pages;


          var navigationRow = '<tr><td colspan=3><small>Página ' + response.currentpage + '/' + response.pages + ' (total registros ' + response.qrows.q + ')</small></td>';
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

</script>