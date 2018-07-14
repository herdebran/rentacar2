<style>
    input {
        text-transform: uppercase;
    }

    .small, small {
        font-size: 82% !important;
    }
</style>

<form class="form-horizontal" action="" method="POST" accept-charset="utf-8">
    <fieldset>
        <h3>Cambio de Comisiones</h3>
        <p>Seleccione una materia y se cargaran automáticamente sus comisiones</p>

        <div class="form-group col-xs-12">
            <label for="carrera" class="col-xs-2 control-label">Carrera / Ciclo</label>
            <div class="col-xs-10">
                <select class="form-control" id="carrera" name="carrera">
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


        <div class="form-group">
            <div class="col-xs-12">
            </div>
        </div>
    </fieldset>

    <fieldset>
        <div class="form-group col-md-12">
            <div class="col-md-6 col-sm-6">
                <div class="form-group">
                    <label for="comisiones1" class="col-md-3 control-label">Comision Origen</label>
                    <div class="col-md-9">
                        <select class="form-control combo-comision" id="comisiones1" name="comisiones_1">
                            <option value="0">SELECCIONAR</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="horario1" class="col-md-3 control-label">Horario: </label>
                    <div class="col-md-9">
                        <div  id="comision-detalle-horarios1"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="profesor1" class="col-md-3 control-label">Profesor: </label>
                    <div class="col-md-9">
                        <div  id="comision-detalle-profesores1"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="comisiones2" class="col-md-3 control-label">Comision Destino</label>
                    <div class="col-md-9">
                        <select class="form-control combo-comision" id="comisiones2" name="comisiones_2">
                            <option value="0">SELECCIONAR</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="horario2" class="col-md-3 control-label">Horario: </label>
                    <div class="col-md-9">
                        <div  id="comision-detalle-horarios2"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="profesor2" class="col-md-3 control-label">Profesor: </label>
                    <div class="col-md-9">
                        <div id="comision-detalle-profesores2"></div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="cupos" class="col-md-3  control-label">Seleccionar cupo destino</label>
                    <div class="col-md-9">
                        <select class="form-control" id="cupos" name="cupos">
                            <option value="0">SELECCIONAR</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </fieldset>
</form>
<fieldset>
    <div class="col-md-12">
        <div class="col-md-6">

            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-striped table-hover table-condensed" id="table-resultados1">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Alumno</th>
                            <th>Documento</th>
                            <th>Cupo</th>
                            <th>Acciones</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
        <div class="col-md-6">

            <div class="panel panel-default">
                <div class="panel-body">
                    <table class="table table-striped table-hover table-condensed" id="table-resultados2">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Alumno</th>
                            <th>Documento</th>
                            <th>Cupo</th>
                        </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</fieldset>

<script type="text/javascript">

    $("#carrera").change(function () {
        $.ajax({
            url: "/comisiones/ajaxmaterias/" + $(this).val(),
            dataType: 'json',
            success: function (response) {
                $('#btnCrearComision').hide();
                $('#materia').empty();

                $('#materia').append($('<option>').text('SELECCIONAR').attr('value', 0));

                $.each(response, function (i, value) {
                    $('#materia').append($('<option>').text(value.descripcion).attr('value', value.id));
                    //Si tengo variable del navegador seteada con la materia la selecciono.
                    if (sessionStorage.getItem("gestioncomisiones-materia") == value.id) {
                        $('#materia').val(sessionStorage.getItem("gestioncomisiones-materia"));
                    }
                });
                //Si no se encontreo variable seleccionada o bien no hay ninguna dejo seleccionado el SELECCIONAR
                $("#materia").trigger("change");
            }
        });
        //guardo temporalmente la variabla elegida
        sessionStorage.setItem("gestioncomisiones-carrera", $(this).val());
    });

    $("#materia").change(function () {
        $.ajax({
            url: "/comisiones/ajaxcomisiones/" + $(this).val()+"/<?php echo date("Y"); ?>" ,
            dataType: 'json',
            success: function (response) {
                $('.combo-comision').empty();
                $('.combo-comision').append($('<option>').text('SELECCIONAR').attr('value', 0));
                $.each(response, function (i, value) {
                    $('.combo-comision').append($('<option>').text(value.nombre).attr('value', value.id));
                    //Si tengo variable del navegador seteada con la materia la selecciono.
                    if (sessionStorage.getItem("gestioncomisiones-comision1") == value.id) {
                        $('#comisiones1').val(sessionStorage.getItem("gestioncomisiones-comision1"));
                    }
                    if (sessionStorage.getItem("gestioncomisiones-comision2") == value.id) {
                        $('#comisiones2').val(sessionStorage.getItem("gestioncomisiones-comision2"));
                    }
                });
                //Si no se encontreo variable seleccionada o bien no hay ninguna dejo seleccionado el SELECCIONAR
                $(".combo-comision").trigger("change");
            }
        });
        //guardo temporalmente la variabla elegida
        sessionStorage.setItem("gestioncomisiones-materia", $(this).val());
    });

    $(".combo-comision").change(function (event) {
        var numero = event.target.name.split("_")[1];

        var valor = event.target.value;
        var tabla = "#table-resultados" + numero;

        $(tabla + " > tbody").empty();
        $.ajax({
            url: "/comisiones/ajaxacomisioncompleta/" + valor,
            dataType: "json",
            success: function (response) {
                if (response.length == 0) {
                    $(tabla + " > tbody:last-child").append('<tr><td colspan=6>No hay resultados que coincidan con los criterios de busqueda</td></tr>');
                } else {
                    armarTabla(response['alumnos'], tabla);
                }
                if (numero == 2) {
                    $("#cupos").empty();
                    $("#cupos").append($('<option>').text('SELECCIONAR').attr('value', 0));
                    $.each(response['cupos'], function (i, value) {
                        var elem = $('<option>').text(value.descripcion + ' - ' + value.cantdisponible + '/' + value.cantidad).attr('value', value.idcomcupo).attr('data-disponible', value.cantdisponible);
                        if (value.cantdisponible == 0) {
                            elem.attr('disabled','disabled');
                        }
                        $('#cupos').append(elem);
                        if (sessionStorage.getItem("gestioncomisiones-cupos") == value.idcomcupo) {
                            $('#cupos').val(sessionStorage.getItem("gestioncomisiones-cupos"));
                        }
                    });
                }
            }
        });
        var detalleprofesor = "#comision-detalle-profesores" + numero;
        var detallehorario = "#comision-detalle-horarios" + numero;
        //Traido detalle de la comision elegida.
        sData = 'comision=' + $(this).val();
        $.ajax({url: "/vermateria/ajaxdatoscomision", method: 'POST', data: sData, dataType: 'json', success: function (response) {
            if (response.length == 0) {
                $(detalleprofesor).empty();
                $(detallehorario).empty();
            } else {
                $.each(response, function(i, value) {
                    //Traigo los profesores	
                    $(detalleprofesor).empty();
                    if (value.profesores!=false)
                        $(detalleprofesor).append("<p class=\"form-control-static\">" + value.profesores + "</p>");

                    //Traigo los horarios
                    $(detallehorario).empty();
                    if (value.horarios!=false)
                        $(detallehorario).append("<p class=\"form-control-static\">" + value.horarios + "</p>");
                });
            }
        }}); //del ajax
        //guardo temporalmente la comision elegida
        sessionStorage.setItem("gestioncomisiones-comision" + numero, $(this).val());
    });


    $("#cupos").change(function () {
        sessionStorage.setItem("gestioncomisiones-cupos", $(this).val());
    });

    $('table').on('click', 'a.btncambiarcomision', function () {
        var disponibles = $('#cupos :selected').attr('data-disponible');
        if(disponibles > 0){
            var elemhtml = $(this)[0];
            var idpersona = $(elemhtml).data("idpersona");
			var alumno = $(elemhtml).data("alumno");
			var cupo_origen = $(elemhtml).data("cupo_origent");
            var idcomision_origen = $("#comisiones1").val();
            var idcupo_origen = $(elemhtml).data("cupo_origen");
            var idcomsion_destino = $("#comisiones2").val();
            var idcupo_destino = $("#cupos").val();
			
			
			if (idcomision_origen==idcomsion_destino && idcupo_origen==idcupo_destino){
				alert("No se puede realizar el cambio de comisión. Debe elegir distintas comisiones o cupos.");
				}
            else {
				var ok = confirm("Alumno " + alumno + " \n\n-->COMISIÓN ORIGEN:\n" + $("#comisiones1 option:selected").text() + " Cupo: " + cupo_origen + " \n\n-->COMISIÓN DESTINO:\n" + $("#comisiones2 option:selected").text() + " Cupo: " + $("#cupos option:selected").text() +". \n\n¿Esta seguro de realizar el cambio?\n\nIDPersona "+idpersona+" o "+idcomision_origen+" "+idcupo_origen+" --> d "+idcomsion_destino+" "+idcupo_destino);

            if (ok) {
                $.ajax({
                    url: "/ajaxcambiarcomision/" + idpersona + "/" + idcomision_origen + "/" + idcupo_origen + "/" + idcomsion_destino + "/" + idcupo_destino,
                    dataType: "json",
                    success: function (response) {
                        location.reload();
                    },
                    error: function () {
                        location.reload();
                    }
                });
            }
			}
		}else{
			alert("Debe elegir un cupo destino con plazas disponibles.");
		}
			
        return false;
    });

    function armarTabla(data, tabla) {
        var index = 1
        $.each(data, function (i, value) {
            newRow = '<tr>';
            newRow += '<td><small>' + index++ + '</small></td>';
            newRow += '<td><small>' + value.apellido + ', ' + value.nombre + '</small></td>';
            newRow += '<td><small>' + value.tipodocumento + ' ' + value.documentonro + '</small></td>';
            newRow += '<td><small>' + value.nombrecupo + '</small></td>';
            if (tabla == "#table-resultados1") {
                newRow += '<td>' +
                    '<a href="#" class="btn btn-default btn-xs btncambiarcomision" data-idpersona="' + value.idpersona + '" data-cupo_origen="' + value.idcomcupo + '" data-cupo_origent="' + value.nombrecupo + '" data-alumno="' + value.apellido + ', ' + value.nombre + '">' +
                    '<span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>' +
                    '</a></td>';
            }
            newRow += '</tr>';

            $(tabla + " > tbody:last-child").append(newRow);
            //Cambio 38 20170710 Leo
            $("#deshabilitar_" + value.idpersona)
                .click(function (event) {
                    var texto = "¿Esta seguro de querer deshabilitar la comisión?";
                    if (!confirm(texto)) {
                        event.preventDefault();
                    }
                });
            $("#habilitar_" + value.idpersona)
                .click(function (event) {
                    var texto = "¿Esta seguro de querer habilitar la comisión?";
                    if (!confirm(texto)) {
                        event.preventDefault();
                    }
                });
            //Fin Cambio 38 20170710 Leo
        });
    }


    if (sessionStorage.getItem("gestioncomisiones-carrera")) {
        $('#carrera').val(sessionStorage.getItem("gestioncomisiones-carrera"));
        $("#carrera").trigger("change");
    }

</script>