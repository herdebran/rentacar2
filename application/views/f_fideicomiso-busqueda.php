
<style>
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


<form class="form-horizontal" action="#" id="form-busqueda"
      method="POST" accept-charset="utf-8">
    <fieldset>
        <h3>Búsqueda de Fideicomisos</h3>
        <p>Complete al menos un campo para realizar la búsqueda</p>

        <div class="form-group col-xs-12 col-sm-6 col-lg-4">
            <label for="tipo" class="col-xs-4 control-label">Tipo</label>
            <div class="col-xs-8">
                <select class="form-control" id="tipo" name="tipo">
                    <option value="0">SELECCIONAR</option>
                    <?php foreach ($params['viewDataTipoFideicomiso'] as $tipo) { ?>
                        <option value="<?php echo $tipo['id']; ?>"><?php echo $tipo['descripcion']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-lg-4">
            <label for="descripciom" class="col-xs-4 control-label">Descripción</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="descripcion" name="descripcion" placeholder="" value="">
            </div>
        </div>


        <div class="form-group col-xs-12 col-sm-6 col-lg-4">
            <label for="estado" class="col-xs-4 control-label">Estado Fideicomiso</label>
            <div class="col-xs-8">
                <select class="form-control" id="estado" name="estado">
                    <option value="0">SELECCIONAR</option>
                    <?php foreach ($params["estadosfideicomiso"] as $estado) { ?>
                        <option value="<?php echo $estado['id']; ?>"><?php echo $estado['descripcion']; ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-xs-12">
                <button id="btnBuscar" type="submit" class="btn btn-primary">Buscar</button>
                <button id="btnReset" type="button" class="btn btn-default">Reset Búsqueda</button>
                <?php if ($ses->tienePermiso('', 'Gestión de Cooperadora - Notificaciones')) { ?>
                    <button class="btn btn-info" type="button" aria-label="Notificar" title="Notificacion Masiva" id="notificacionButton">Notificar Personas
                        <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
                    </button>
                <?php } ?>
            </div>
        </div>
    </fieldset>
</form>

<fieldset>
    <h3>Resultados</h3>
    <div class="panel panel-default">
        <div class="panel-body">
            <table class="table table-striped table-hover table-condensed" id="table-resultados" width="100%">
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Calle</th>
                        <th>Número</th>
                        <th>Municipio</th>
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

<div class="modal" id="loadingModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cargando... Por favor espere.</h4>
            </div>
            <div class="modal-body">
                <div class="progress progress-striped active" style="margin-bottom:0;"><div class="progress-bar" style="width: 100%"></div></div>
            </div>
        </div>
    </div>
</div>
</body>
<script>
    $().ready(function () {
// Carga las materias de la carrera elegida
        $("#carrera").change(function () {
            $.ajax({
                url: "/comisiones/ajaxmaterias/" + $(this).val(),
                dataType: 'json',
                success: function (response) {
                    $('#materia').empty();
                    let oldSearch = JSON.parse(sessionStorage.getItem('busqueda'));
                    let idMateria = oldSearch.materia;
                    $('#materia').append($('<option>').text('SELECCIONAR').attr('value', 0));
                    let encontre = false;
                    $.each(response, function (i, value) {
                        $('#materia').append($('<option>').text(value.descripcion).attr('value', value.id));
                        //Si tengo variable del navegador seteada con la materia la selecciono.
                        if (idMateria == value.id) {
                            encontre = true;
                            $('#materia').val(idMateria);
                        }
                    });
                    //Si no se encontreo variable seleccionada o bien no hay ninguna dejo seleccionado el SELECCIONAR
                    if (!encontre)
                        $('#materia').val(0);
                    $("#materia").trigger("change");
                }});
        });
// Verifica si existe una busqueda previa y si la hay la carga en el formulario.
        if (sessionStorage.getItem('busqueda') !== null) {
            let oldSearch = JSON.parse(sessionStorage.getItem('busqueda'));
            $("#nombre").val(oldSearch.nombre);
            $("#apellido").val(oldSearch.apellido);
            $("#tipdoc").val(oldSearch.tipdoc);
            $("#nrodoc").val(oldSearch.nrodoc);
            $("#carrera").val(oldSearch.carrera);
            if (oldSearch.carrera != 0) {
                $("#carrera").trigger("change");
            } else {
                $("#materia").val(0);
            }
            $("#instrumento").val(oldSearch.instrumento);
            $("#rol").val(oldSearch.rol);
            $("#estadocoop").val(oldSearch.estadocoop);
            $("#mespago").val(oldSearch.mespago);
            $("#pago").val(oldSearch.pago);
        }

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
        // DATATABLE------------------------------------------------------------------------//
        // Variable global donde se aloja el objeto datatable.
        var table;
        var firstLoad = true;
        function cargarDataTable() {
            table = $('#table-resultados').removeAttr('width').DataTable({

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
                    error: function(err1){
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

        }
// Form functions ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        $("#form-busqueda").submit(function (e) {
            e.preventDefault();
        });
        $("#btnBuscar").click(function () {

            $('#loadingModal').show();
            $(this).attr("disabled", true);
            if (firstLoad) {
                cargarDataTable();
                firstLoad = false;
            } else {
                table.ajax.reload();
            }
        });
        // Limpia el formulario de busqueda
        function limpiarForm() {
            $("#nombre").val("");
            $("#apellido").val("");
            $("#tipdoc").val(0);
            $("#nrodoc").val("");
            $("#carrera").val(0);
            $("#materia").val(0);
            $("#instrumento").val(0);
            $("#rol").val(0);
            $("#estadocoop").val(0);
            $("#mespago").val(0);
            $("#pago").val("SI");
            sessionStorage.removeItem('busqueda');
        }

        $("#btnReset").click(function () {
            limpiarForm();
        });


    });
</script>
