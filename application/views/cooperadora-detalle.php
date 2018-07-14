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



<form class="form-horizontal" action="#" method="POST" accept-charset="utf-8">
    <fieldset>
        <h3>Búsqueda de Alumnos</h3>

        <div class="form-group col-xs-12 col-sm-6 col-lg-4">
            <label for="apellido" class="col-xs-4 control-label">Nombre y Apellido</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="apellido" name="apellido" placeholder="" maxlength="45" value="<?php echo $params["persona"]["nombre"]; ?> <?php echo $params["persona"]["apellido"]; ?>"
                       autofocus="on" autocomplete="off" readonly>
            </div>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-lg-4">
            <label for="nrodoc" class="col-xs-4 control-label">Nro.Doc.</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="nrodoc" name="nrodoc" value="<?php echo $params["persona"]["documento"]; ?>" readonly>
            </div>
        </div>

        <div class="form-group col-xs-12 col-sm-6 col-lg-4">
            <label for="nrodoc" class="col-xs-4 control-label">Estado Coop.</label>
            <div class="col-xs-8">
                <input type="text" class="form-control" id="estadcoop" name="estadcoop" value="<?php echo $params["persona"]["estadocooperadora"]; ?>" readonly>
            </div>
        </div>
        <div class="form-group">
            <div class="col-xs-12">
                <a href="/gestion-cooperadora" id="btnBuscar" type="button" class="btn btn-primary">Volver</a>
            </div>
        </div>
        <input hidden="" type="text" id="idpersona" disabled="" value="<?php echo $params["idpersona"]; ?>">
        <input hidden="" type="text" id="valorCuota" disabled="" value="<?php echo $params["valorCuota"]; ?>">


    </fieldset>
</form>

<div class="row">
    <div class="col-md-4">
        <fieldset>
            <h3>Cuotas</h3>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="form-group">
                        <label for="carrera" class="col-xs-5 control-label">Busqueda de Pagos</label>
                        <div class="col-xs-7">
                            <select class="form-control" id="anioCuota" name="carrera">
                                <?php foreach ($params["aniosCuotas"] as $i => $anio): ?>
                                    <option value="<?php echo $anio["año"]; ?>" <?php
                                    if (date('Y') == $anio["año"]) {
                                        echo "selected";
                                    }
                                    ?>><?php echo $anio["año"]; ?></option>
                                        <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <hr>
                    <table class="table table-striped table-hover table-condensed" id="table-cuotas">
                        <thead>
                            <tr>
                                <th>Cuota</th>
                                <th>Fecha Pago</th>
                                <th>Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($params["cuotas"] as $cuota): ?>
                                <tr>
                                    <td><?php echo $cuota["mes"] ?></td>
                                    <?php if ($cuota["fechapago"] == null): ?>
                                        <td>---</td>
                                    <?php else: ?>
                                        <td><?php echo date('d/m/Y', strtotime($cuota["fechapago"])); ?></td>
                                    <?php endif; ?>
                                    <?php if ($cuota["monto"] == null): ?>
                                        <td>---</td>
                                    <?php else: ?>
                                        <td>$ <?php echo $cuota["monto"] ?></td>
                                    <?php endif; ?>

                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </fieldset>
    </div>

    <div class="col-md-8">
        <fieldset>
            <h3>Operaciones</h3>
            <div class="panel panel-default">
                <div class="panel-body">
                    <br>
                    <?php if ($ses->tienePermiso('', 'Gestión de Cooperadora - Cobrador')) { ?>
                        <div class="form-group pull-right">
                            <div class="col-xs-12">
                                <button class="btn btn-success" type="button" title="Nuevo Pago" id="nuevoPagoButton">Nuevo Pago
                                    <span class="glyphicon glyphicon-usd" aria-hidden="true"></span>
                                </button>

                            </div>
                        </div>
                    <?php } ?>
                    <br>
                    <br>
                    <br>
                    <table class="table table-striped table-hover table-condensed" id="table-operaciones">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Fecha Pago</th>
                                <th>Cuotas</th>
                                <th>Monto Total</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($params["operaciones"] as $i => $ope): ?>
                                <tr>
                                    <td><?php echo sprintf("%'.08d\n", $ope["idoperacion"]); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($ope["fecha"])); ?></td>
                                    <td><?php echo $ope["cuotas"] ?></td>
                                    <td>$ <?php echo $ope["montototal"] ?></td>
                                    <td>
                                        <?php if ($ses->tienePermiso('', 'Gestión de Cooperadora - Cobrador')) { ?>
                                            <button class="btn btn-default btn-xs anularOperacion" aria-label="Eliminar" title="Eliminar"
                                                    data-operacion="<?php echo $ope["idoperacion"]; ?>">
                                                <span class="glyphicon glyphicon-remove-circle" aria-hidden="true" data-operacion="<?php echo $ope["idoperacion"]; ?>"  ></span>
                                            </button>

                                            <a target="_blank"
                                               class="btn btn-default btn-xs reimprimirOperacion" 
                                               aria-label="Reimprimir" 
                                               title="Reimprimir" 
                                               data-operacion="<?php echo $ope["idoperacion"]; ?>"
                                               href="/cooperadora/generarpdf/<?php echo $ope["idoperacion"]; ?>">
                                                <span class="glyphicon glyphicon-print" aria-hidden="true"></span>
                                            </a>

                                            <button class="btn btn-default btn-xs enviarEmailComprobante" 
                                                    aria-label="Enviar Email" 
                                                    title="Enviar Email" 
                                                    data-operacion="<?php echo $ope["idoperacion"]; ?>">
                                                <span class="glyphicon glyphicon-envelope" aria-hidden="true"></span>
                                            </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </fieldset>
    </div>
</div>
</div>
<div class="modal" id="nuevoPagoModal">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="modalLabel">Nuevo Pago</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <fieldset>
                        <!-- Select Basic -->
                        <div class="form-group">
                            <label class="col-md-2 control-label" for="Año">Año</label>
                            <div class="col-md-2">
                                <select id="anioPagar" name="anioPagar" class="form-control">
                                    <?php foreach ($params["aniosCuotas"] as $i => $anio): ?>
                                        <option value="<?php echo $anio["año"]; ?>" <?php
                                        if (date('Y') == $anio["año"]) {
                                            echo "selected";
                                        }
                                        ?>><?php echo $anio["año"]; ?></option>
                                            <?php endforeach; ?>
                                </select>
                            </div>
                            <div id="checkbox-cuota">
                                <?php foreach ($params["cuotas"] as $i => $cuota): ?>
                                    <label class="checkbox-inline" for="checkboxes-<?php echo $i; ?>">
                                        <input 
                                        <?php if ($cuota["pago"]): ?>
                                                disabled=""
                                                checked="true"
                                            <?php endif; ?>
                                            type="checkbox" 
                                            name="checkboxes" 
                                            id="checkboxes-<?php echo $i; ?>" 
                                            data-cuota="<?php echo $cuota["cuota"]; ?>"
                                            value="<?php echo $cuota["idcuota"]; ?>">
                                            <?php echo $cuota["mes"]; ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <!-- Text input-->
                        <div class="form-group">

                            <label class="col-md-2 control-label" for="fecha">fecha</label>  
                            <div class="col-md-3">
                                <input id="fechaNuevoPago" name="fecha" type="text" value="<?php echo date("d/m/Y") ?>" class="form-control input-md" readonly>

                            </div>
                            <label class="col-md-2 control-label" for="monto">Monto</label>
                            <div class="col-md-2 input-group input-group-sm">
                                <span class="input-group-addon">$</span>
                                <input id="montoTotal" name="monto" type="text" placeholder="0" value="0" class="form-control " readonly>

                            </div>

                        </div>
                    </fieldset>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="cancelNuevoPago">Cancelar</button>
                <button type="button" class="btn btn-primary" id="sendNuevoPago" disabled="">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        //ENVIO DE EMAIL ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        function closeNuevoPagoModal() {
            var ok = confirm("¿Seguro que desea cancelar la operacion?");
            if (ok) {
                $("#checkbox-cuota input:checkbox:enabled")
                        .each(function (i, elem) {
                            $(elem).prop("checked", false);
                        });
                $("#sendNuevoPago").prop("disabled", true);
                $('#nuevoPagoModal').hide();
                $("#montoTotal").val(0);
            }
        }
        // When the user clicks the button, open the modal
        $("#nuevoPagoButton").click(function () {
            $('#nuevoPagoModal').show();
        });

        const cambioCheck = function (event) {
            var valorCuota = parseInt($("#valorCuota").val());

            let valorActual = parseInt($("#montoTotal").val());
            let nuevoValor = valorActual;
            if ($(event.target)[0].checked) {
                nuevoValor = valorActual + valorCuota;
            } else {
                nuevoValor = valorActual - valorCuota;
            }
            $("#sendNuevoPago").prop("disabled", nuevoValor <= 0);
            $("#montoTotal").val(nuevoValor);
        }

        $("#checkbox-cuota input:checkbox:not(:checked)").change(cambioCheck);
        // When the user clicks on <span> (x), close the modal
        $(".close").click(function () {
            closeNuevoPagoModal();
        });
        // When the user clicks on <button> (Cancelar), close the modal
        $("#cancelNuevoPago").click(function () {
            closeNuevoPagoModal();
        });
        // When the user clicks anywhere outside of the modal, close it
        $(window).click(function (event) {
            if (event.target === $('#nuevoPagoModal')[0]) {
                closeNuevoPagoModal();
            }
        });

        // When the user clicks on <button> (Enviar)
        $("#sendNuevoPago").click(function () {

            let anioPagar = $("#anioPago").val();
            let fechaNuevoPago = $("#fechaNuevoPago").val();
            let idpersona = $("#idpersona").val();
            let cuotas = [];
            $("#checkbox-cuota input:checkbox:enabled:checked")
                    .each(function (i, elem) {
                        cuotas.push({idcuota: $(elem).val(), cuota: $(elem).data("cuota")});
                    });
            request = $.ajax({
                url: "/cooperadora/nuevaOperacion",
                method: 'POST',
                data: {
                    anioPagar: anioPagar,
                    fechaNuevoPago: fechaNuevoPago,
                    idpersona: idpersona,
                    cuotas: cuotas
                }
            });

            // Callback handler that will be called on success
            request.done(function (response, textStatus, jqXHR) {
                resp = jQuery.parseJSON(response);
                location.reload();

            });

            // Callback handler that will be called on failure
            //request.fail(function (jqXHR, textStatus, errorThrown) {
            //    alert("Err" + textStatus + " " + errorThrown);
            //});

            // Callback handler that will be called on failure
            request.fail(function (jqXHR, textStatus) {
                alert("fallo")
                addGlobalMessage("alert-danger", "\
                            <p>Error de comunicación Ajax al servidor:</p>\
                            <p>Por favor vuelva a intentarlo en unos minutos. Si el problema persiste comuniquese con el administrador del sistema.</p>");
            });
            request.always(function () {
                // Reenable the inputs
                $('#nuevoPagoModal').hide();
            });

        });

        $(".anularOperacion").click(function (event) {
            ok = confirm("¿Esta seguro que desea eliminar la operación?");
            if (ok) {
                var elem = $(event.target);
                if (elem.is("span")) {
                    elem = $(elem.parent());
                }
                let idOpe = elem.data("operacion");
                let idpersona = $("#idpersona").val();
                let request = $.ajax({
                    url: "/cooperadora/anularOperacion",
                    data: {idoperacion: idOpe, idpersona: idpersona},
                    type: 'POST'
                });
                request.done(function (response, textStatus, jqXHR) {
                    let resp = jQuery.parseJSON(response);
                    if (resp.ok) {
                        location.reload();
                    }
                });
                request.fail(function (jqXHR, textStatus) {
                    addGlobalMessage("alert-danger", "\
                                <p>Error de comunicación Ajax al servidor:</p>\
                                <p>Por favor vuelva a intentarlo en unos minutos. Si el problema persiste comuniquese con el administrador del sistema.</p>");
                });
            }
        });

        $(".enviarEmailComprobante").click(function (event) {
            ok = confirm("¿Quiere enviar el comprobante por correo?");
            if (ok) {
                var elem = $(event.target);
                if (elem.is("span")) {
                    elem = $(elem.parent());
                }
                let idOpe = elem.data("operacion");
                let idpersona = $("#idpersona").val();
                let request = $.ajax({
                    url: "/cooperadora/generarpdf/" + idOpe + "",
                    data: {params: {idoperacion: idOpe, email: true}},
                    type: 'POST'
                });
                request.done(function (response, textStatus, jqXHR) {
                    console.log(response)
                    // addGlobalMessage("alert-success", "Email enviado al correo: ")
                });
                request.fail(function (jqXHR, textStatus, errorThrown) {
                    addGlobalMessage("alert-error", "El email no pudo enviarse")
                    console.error("Error en el llamado: " + textStatus + " " + errorThrown);
                });
            }
        });
    </script>

    <script>
        $().ready(function () {
            $("#anioCuota").change(function (event) {
                let anioCuota = $(event.target).val();
                let idpersona = $("#idpersona").val();
                $.ajax({
                    type: 'POST',
                    url: "/cooperadora/cuotasPersonaDeAnio",
                    data: {"anioCuota": anioCuota,
                        "idpersona": idpersona},
                    success: function (data) {
                        let objectData = jQuery.parseJSON(data);
                        let tbody = $("#table-cuotas tbody");
                        console.log(objectData)
                        tbody.empty();
                        $.each(objectData, function (index, row) {
                            let tr = $("<tr/>");
                            let tdCuota = $("<td/>").text(row.mes);
                            let tdFechaPago;
                            if (row.fechapago === null) {
                                tdFechaPago = $("<td/>").text("---");
                            } else {
                                tdFechaPago = $("<td/>").text(row.fechapago);
                            }
                            let tdMonto;
                            if (row.monto === null) {
                                tdMonto = $("<td/>").text("---");
                            } else {
                                tdMonto = $("<td/>").text("$ " + row.monto);
                            }
                            tr.append(tdCuota, tdFechaPago, tdMonto);
                            tbody.append(tr);
                        });
                    }
                });
            });
            $("#anioPagar").change(function (event) {
                let anioCuota = $(event.target).val();
                let idpersona = $("#idpersona").val();
                request = $.ajax({
                    type: 'POST',
                    url: "/cooperadora/cuotasPersonaDeAnio",
                    data: {"anioCuota": anioCuota,
                        "idpersona": idpersona}
                });
                request.done(function (data) {
                    let objectData = jQuery.parseJSON(data);
                    let divContent = $("#checkbox-cuota");
                    divContent.empty();
                    $.each(objectData, function (index, row) {
                        let label = $('<label />').addClass('checkbox-inline').prop("for", "checkboxes-" + index).html(row.mes);
                        let chk = $('<input/>')
                                .attr({
                                    id: 'checkboxes-' + index,
                                    name: 'checkboxes',
                                    type: 'checkbox',
                                    value: row.idcuota,
                                    "data-cuota": row.cuota,
                                    checked: function () {
                                        row.pago != 0
                                    },
                                    disabled: function () {
                                        row.pago != 0
                                    }
                                });
                        label.prepend(chk);
                        divContent.append(label);
                    });
                    $(".checkbox-inline").on("change", cambioCheck);
                    $("#montoTotal").val(0);
                });

            });
        });
    </script>