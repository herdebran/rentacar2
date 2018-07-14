<?php ?>
<style>
    input { text-transform: uppercase; }
    .small, small { font-size: 82% !important;  }
</style>

<style>
    /* The Modal (background) */
    .modal {
        display: none; /* Hidden by default */
        position: fixed; /* Stay in place */
        z-index: 1; /* Sit on top */
        padding-top: 100px; /* Location of the box */
        left: 0;
        top: 0;
        width: 100%; /* Full width */
        height: 100%; /* Full height */
        overflow: auto; /* Enable scroll if needed */
        background-color: rgb(0, 0, 0); /* Fallback color */
        background-color: rgba(0, 0, 0, 0.4); /* Black w/ opacity */
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
</style>
<form class="form-horizontal" action="" method="POST" accept-charset="utf-8">  
    <fieldset>
        <h3>Ver Materias</h3>
        <div class="form-group col-xs-12">
            <label for="carrera" class="col-xs-2 control-label">Carrera / Ciclo</label>
            <div class="col-xs-10">
                <select class="form-control" id="carrera" name="carrera" autofocus="on">
                    <option value="0">SELECCIONAR</option>
                    <?php foreach ($viewDataCarreras as $carrera) { ?>
                        <option value="<?php echo $carrera['idcarrera']; ?>"><?php echo $carrera['descripcion']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="form-group col-xs-12">
            <label for="materia" class="col-xs-2 control-label">Materia</label>
            <div class="col-xs-10">
                <select class="form-control" id="materia" name="materia">
                    <option value="0">SELECCIONAR</option>
                </select>
            </div>
        </div>
        <div class="form-group col-xs-12">
            <label for="comision" class="col-xs-2 control-label">Comisión</label>
            <div class="col-xs-10">
                <select class="form-control" id="comision" name="comision">
                    <option value="0">SELECCIONAR</option>
                </select>
            </div>
        </div>
        
        <div class="col-sm-12">
            <div class="form-group">
              <label class="col-sm-2 control-label">Profesores</label>
              <div class="col-sm-10">
                <div id="comision-detalle-profesor">
                </div>
              </div>
            </div>
          </div>
          
          <div class="col-sm-12">
            <div class="form-group">
              <label class="col-sm-2 control-label">Horarios</label>
              <div class="col-sm-10">
                <div id="comision-detalle-horarios">
                </div>
              </div>
            </div>
          </div>
        
          <div class="col-sm-12">
            <div class="form-group">
              <label class="col-sm-2 control-label">Aula</label>
              <div class="col-sm-10">
                <div id="comision-detalle-aula">
                </div>
              </div>
            </div>
          </div>
    </fieldset>
</form>
<?php if($ses->tienePermiso('','Ver Materias Enviar Notificaciones')){?>
<button class="btn btn-info" aria-label="Notificar" title="Notificar Comisión" id="emailButton">Notificar Comisión <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span></button>
<?php }  
if($ses->tienePermiso('','Ver Materia Imprimir Lista de Asistencia')){?>
<button class="btn btn-info" aria-label="Imprimir" title="Imprimir" id="imprimirButton">Imprimir Lista Asistencia</button>
<?php
}
// TODO: Verificar que el permiso de este boton este correcto.
if ($ses->tienePermiso('', 'Ver Materia Imprimir Lista de Asistencia')) {?>
    <button class="btn btn-info" aria-label="Imprimir" title="Imprimir" id="actaParcial" disabled>Imprimir Acta de Cursada</button>
<?php } ?>
<fieldset>
    <h3>Resultados</h3>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped table-hover table-condensed" id="table-alumnos">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Alumno</th>
                        <th>Documento</th>
                        <th>Instrumento</th>
                        <th>Primer Parcial</th>
                        <th>Segundo Parcial</th>
                        <th>Estado Materia</th>
<?php if($ses->tienePermiso('','Ver Materias Acciones') || $ses->tienePermiso('','Analitico Acceso desde Ver materias')){?>
                            <th class="hide-on-print">Acciones</th>
<?php } ?>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="exampleModalLabel">Notificación a alumnos</h4>
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
                        <textarea  class="form-control" rows="5" id="comment"></textarea>
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
<?php if($ses->tienePermiso('','Ver Materias Enviar Notificaciones')){ ?>
//ENVIO DE EMAIL ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
    function closeEmailModal() {
        var ok = confirm("¿Seguro que desea cancelar el envio?");
        if (ok) {
            $('#emaiModal').hide();
        }
    }
    // When the user clicks the button, open the modal
    $("#emailButton").click(function () {
        $('#emaiModal').show();
        //alert("Cargando Datos del email");
        var mailData;
        mailData += '&body=' + $("#comment").val();
        mailData += '&co=' + $('#comision').val();
        $.ajax({
            url: "/vermateria/ajaxsetmail",
            method: 'POST',
            data: mailData,
            dataType: 'json',
            success: function (response) {
                $('#recipient-name').val(response.toString());
            },
            error: function () {
                alert("no se puedo enviar el correo");
                $('#emaiModal').hide();
            }
        });
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
            var mailData;
            mailData = 'sendto=' + $("#recipient-name").val();
            mailData += '&sentoadd=' + $("#recipient-add-name").val();
            mailData += '&body=' + $("#comment").val();
            mailData += '&co=' + $('#comision').val();
            mailData += '&ca=' + $("#carrera").val();
            mailData += '&pe=<?php echo $ses->getIdPersona(); ?>';
            mailData += '&ro=<?php echo $ses->getIdRole(); ?>';
            request = $.ajax({
                url: "/home/ajaxsendmail",
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
            request.fail(function (jqXHR, textStatus, errorThrown) {
                alert("Err" + textStatus + " " + errorThrown);
            });

            request.always(function () {
                // Reenable the inputs
                $('#emaiModal').hide();
            });
        } else {
            alert("El formato de uno de los correos adicionales no es correcto");
        }
    });
<?php } ?>

//AJAX elijo carrera ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	
	//Al cambiar la carrera trae las materias
    $("#carrera").change(function () {
        var sData = 'ca=' + $(this).val();
        sData += '&pe=<?php echo $ses->getIdPersona(); ?>';
        $('#materia').empty();
		$('#materia').append($('<option>').text('SELECCIONAR').attr('value', 0));
		$('#comision').empty();
		$('#comision').append($('<option>').text('SELECCIONAR').attr('value', 0));
        $('#table-alumnos > tbody').empty();
        $.ajax({url: "/vermateria/ajaxmaterias", method: 'POST', data: sData, dataType: 'json', success: function (response) {
				$encontre=false;
				
                $.each(response, function (i, value) {
                    $('#materia').append($('<option>').text(value.nombre).attr('value', value.id));
						//Si tengo variable del navegador seteada con la materia la selecciono.
						if(sessionStorage.getItem("ver-materias-materia")==value.id){
							$encontre=true;
							$('#materia').val(sessionStorage.getItem("ver-materias-materia"));	
							}
                });
				//Si no se encontreo variable seleccionada o bien no hay ninguna dejo seleccionado el SELECCIONAR
				if (!$encontre) $('#materia').val(0);
                $("#materia").trigger("change");
            }});
			//guardo temporalmente la variabla elegida
			sessionStorage.setItem("ver-materias-carrera",$(this).val());		
    });
	
	//Al cambiar la materia trae las comisiones
$("#materia").change(function () {
        var sData = 'ma=' + $(this).val();
        sData += '&pe=<?php echo $ses->getIdPersona(); ?>';
        $('#comision').empty();
		$('#comision').append($('<option>').text('SELECCIONAR').attr('value', 0));
        $('#table-alumnos > tbody').empty();
        $.ajax({url: "/vermateria/ajaxcomisiones", method: 'POST', data: sData, dataType: 'json', success: function (response) {
				$encontre=false;
                $.each(response, function (i, value) {
                    $('#comision').append($('<option>').text(value.nombre).attr('value', value.id));
					if(sessionStorage.getItem("ver-materias-comision")==value.id){
							$encontre=true;
							$('#comision').val(sessionStorage.getItem("ver-materias-comision"));	
					}
                });
				if (!$encontre) $('#comision').val(0);
                $("#comision").trigger("change");
            }});
			//guardo temporalmente la variabla elegida
			sessionStorage.setItem("ver-materias-materia",$(this).val());
    });

	//Al elegir la comision trae los datos
    $("#comision").change(function () {
        var sData = 'co=' + $(this).val();
        sData += '&pe=<?php echo $ses->getIdPersona(); ?>';
        sData += '&ca=' + $("#carrera").val();
        $('#table-alumnos > tbody').empty();
		//guardo temporalmente la variabla elegida
		sessionStorage.setItem("ver-materias-comision",$(this).val());
		//Traido los alumnos de la comision
        $.ajax({url: "/vermateria/ajaxcomision", method: 'POST', data: sData, dataType: 'json', success: function (response) {
                if (response.length == 0) {
                    $("#table-alumnos > tbody:last-child").append('<tr><td colspan=8>No hay datos en la Comisión</td></tr>');
                    $("#actaParcial").attr('disabled', true)
                } else {
                    var num = 0;
                    $.each(response, function (i, value) {
                        newRow = '<tr>';
                        newRow += '<td><small>' + ++num + '</small></td>';
                        newRow += '<td><small>' + value.apellido + ' ' + value.nombre + '</small></td>';
                        newRow += '<td><small>' + value.documentonro + '</small></td>';
                        newRow += '<td><small>' + value.instrumento + '</small></td>';
                        if (value.primerparcial == null)
                            newRow += '<td><small>n/a</small></td>';
                        else
                            newRow += '<td><small>' + value.primerparcial + '</small></td>';
                        if (value.segundoparcial == null)
                            newRow += '<td><small>n/a</small></td>';
                        else
                            newRow += '<td><small>' + value.segundoparcial + '</small></td>';
                        newRow += '<td><small>' + value.eam + '</small></td>';
<?php if($ses->tienePermiso('','Ver Materias Acciones') || $ses->tienePermiso('','Analitico Acceso desde Ver materias')){?>
                            newRow += '<td class="hide-on-print"><a href="/analitico/' + value.idalumnocarrera + '/' + value.idpersona + '" class="btn btn-default btn-xs" aria-label="Ver Analítico" title="Ver Analítico"><span class="glyphicon glyphicon-list-alt" aria-hidden="true"></span></a>';
			    newRow += '<a href="/modificar/' + value.idpersona + '/ap=ca&no=&td=0&nd=&ea=0&ca=2&pg=1" class="btn btn-default btn-xs" aria-label="Ver Alumno" title="Ver Alumno"><span class="glyphicon glyphicon-user" aria-hidden="true"></span></a>';
<?php if($ses->tienePermiso('','Ver Materias Acciones')){ ?>
//CURSANDO
                                if (value.idestadoalumnomateria != <?php echo($this->POROTO->Config['estado_alumnomateria_cursando']); ?>)
                                    newRow += '<a onclick="return confirm(\'¿Esta seguro de cambiar el estado de la materia a CURSANDO?\')" href="/analiticoalumno/setestadoalumnomateria/' + value.idalumnocarrera + '/' + value.idpersona + '/' + value.idAlumnoMateria + '/2/' + $("#comision").val() + '" class="btn btn-default btn-xs" aria-label="Cambiar estado a Cursando" title="Cambiar estado a Cursando"><span class="glyphicon glyphicon-play" aria-hidden="true"></span></a>';

//CURSADA APROBADA
                                if (value.idestadoalumnomateria != <?php echo($this->POROTO->Config['estado_alumnomateria_cursadaaprobada']); ?>)
                                    newRow += '<a onclick="return confirm(\'¿Esta seguro de cambiar el estado de la materia a CURSADA APROBADA?\')" href="/analiticoalumno/setestadoalumnomateria/' + value.idalumnocarrera + '/' + value.idpersona + '/' + value.idAlumnoMateria + '/5/' + $("#comision").val() + '" class="btn btn-default btn-xs" aria-label="Cambiar estado a Cursada Aprobada" title="Cambiar estado a Cursada Aprobada"><span class="glyphicon glyphicon-star-empty" aria-hidden="true"></span></a>';

//APROBADA									
                                if (value.idestadoalumnomateria != <?php echo($this->POROTO->Config['estado_alumnomateria_aprobada']); ?>)
                                    newRow += '<a onclick="return confirm(\'¿Esta seguro de cambiar el estado de la materia a APROBADA?\')"href="/analiticoalumno/setestadoalumnomateria/' + value.idalumnocarrera + '/' + value.idpersona + '/' + value.idAlumnoMateria + '/3/' + $("#comision").val() + '" class="btn btn-default btn-xs" aria-label="Cambiar estado a Aprobada" title="Cambiar estado a Aprobada"><span class="glyphicon glyphicon-star" aria-hidden="true"></span></a>';

//APROBADA POR EQUIVALENCIA
                                if (value.idestadoalumnomateria != <?php echo($this->POROTO->Config['estado_alumnomateria_aprobadaxequiv']); ?>)
                                    newRow += '<a onclick="return confirm(\'¿Esta seguro de cambiar el estado de la materia a APROBADA POR EQUIVALENCIAS?\')" href="/analiticoalumno/setestadoalumnomateria/' + value.idalumnocarrera + '/' + value.idpersona + '/' + value.idAlumnoMateria + '/4/' + $("#comision").val() + '" class="btn btn-default btn-xs" aria-label="Cambiar estado a Aprobada por Equivalencia" title="Cambiar estado a Aprobada por Equivalencia"><span class="glyphicon glyphicon-random" aria-hidden="true"></span></a>';

//APROBADA POR NIVELACION
                                if (value.idestadoalumnomateria != <?php echo($this->POROTO->Config['estado_alumnomateria_nivelacion']); ?>)
                                    newRow += '<a onclick="return confirm(\'¿Esta seguro de cambiar el estado de la materia a APROBADA POR NIVELACIÓN?\')" href="/analiticoalumno/setestadoalumnomateria/' + value.idalumnocarrera + '/' + value.idpersona + '/' + value.idAlumnoMateria + '/7/' + $("#comision").val() + '" class="btn btn-default btn-xs" aria-label="Cambiar estado a Aprobada por nivelación" title="Cambiar estado a Aprobada por nivelación"><span class="glyphicon glyphicon-signal" aria-hidden="true"></span></a>';

//LIBRE
                                if (value.idestadoalumnomateria != <?php echo($this->POROTO->Config['estado_alumnomateria_libre']); ?>)
                                    newRow += '<a onclick="return confirm(\'¿Esta seguro de cambiar el estado de la materia a LIBRE?\')" href="/analiticoalumno/setestadoalumnomateria/' + value.idalumnocarrera + '/' + value.idpersona + '/' + value.idAlumnoMateria + '/10/' + $("#comision").val() + '" class="btn btn-default btn-xs" aria-label="Cambiar estado a LIBRE" title="Cambiar estado a LIBRE"><span class="glyphicon glyphicon-time" aria-hidden="true"></span></a>';

//DESAPROBADA
                                if (value.idestadoalumnomateria != <?php echo($this->POROTO->Config['estado_alumnomateria_desaprobada']); ?>)
                                    newRow += '<a onclick="return confirm(\'¿Esta seguro de cambiar el estado de la materia a DESAPROBADA?\')" href="/analiticoalumno/setestadoalumnomateria/' + value.idalumnocarrera + '/' + value.idpersona + '/' + value.idAlumnoMateria + '/9/' + $("#comision").val() + '" class="btn btn-default btn-xs" aria-label="Cambiar estado a Desaprobada" title="Cambiar estado a Desaprobada"><span class="glyphicon glyphicon-thumbs-down" aria-hidden="true"></span></a>';

//CANCELADA											
                                if (value.idestadoalumnomateria != <?php echo($this->POROTO->Config['estado_alumnomateria_cancelada']); ?>)
                                    newRow += '<a onclick="return confirm(\'¿Esta seguro de cambiar el estado de la materia a CANCELADA?\')" href="/analiticoalumno/setestadoalumnomateria/' + value.idalumnocarrera + '/' + value.idpersona + '/' + value.idAlumnoMateria + '/6/' + $("#comision").val() + '" class="btn btn-default btn-xs" aria-label="Cambiar estado a Cancelada" title="Cambiar estado a Cancelada"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></a>';

<?php } ?>								
                            newRow += '</td>';
<?php } ?>
                        newRow += '</tr>';

                        $("#table-alumnos > tbody:last-child").append(newRow);
                    });
                    $("#actaParcial").attr('disabled', false)
                }
        }});// del ajax
			
		//Traido detalle de la comision elegida.
		sData = 'comision=' + $(this).val();

        $.ajax({url: "/vermateria/ajaxdatoscomision", method: 'POST', data: sData, dataType: 'json', success: function (response) { 
                if (response.length == 0) {
					$('#comision-detalle-profesor').empty();
					$('#comision-detalle-horarios').empty();
					$('#comision-detalle-aula').empty();
                } else {
                    $.each(response, function(i, value) {
                                //Traigo los profesores	
                        $('#comision-detalle-profesor').empty();
                        if (value.profesores!=false)
                            $('#comision-detalle-profesor').append("<p class=\"form-control-static\">" + value.profesores + "</p>");	

                                //Traigo los horarios
                        $('#comision-detalle-horarios').empty();
                        if (value.horarios!=false)
                            $('#comision-detalle-horarios').append("<p class=\"form-control-static\">" + value.horarios + "</p>");

                                //Traigo el aula
                        $('#comision-detalle-aula').empty();
                        if (value.aula != null)
                            $('#comision-detalle-aula').append("<p class=\"form-control-static\">" + value.aula + "</p>");
                        else
                            $('#comision-detalle-aula').append("<p class=\"form-control-static\">----</p>");
                    });
                }
            }}); //del ajax

});


$("#imprimirButton").click(function () {
	window.location.href = '/imprimircomision/' + $("#comision").val() + '/' + <?php echo $ses->getIdPersona();?>;
});

    $("#actaParcial").click(function () {
        window.open(
                '/vermateria/imprimirActaParcial/' + $("#comision").val() + '/' + <?php echo $ses->getIdPersona(); ?>,
                '_blank' // <- This is what makes it open in a new window.
                );
    });
    
    // corrige un problema del sessionClocl.
   $().ready(function(){
       //Si tengo las variables de sesion con datos de haber entrado previamente a esta pagina, cargo los datos.
        if(sessionStorage.getItem("ver-materias-carrera")){
                                $('#carrera').val(sessionStorage.getItem("ver-materias-carrera"));
                                $("#carrera").trigger("change");
        }
   });

</script>