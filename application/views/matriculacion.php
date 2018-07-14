<?php
$flagMateria = true;
$flagComision = true;
$valueCarrera = isset($_POST['carrera']) ? $_POST['carrera'] : $dataIdCarrera;
$valueIdAlumnoCarrera = isset($_POST['idalumnocarrera']) ? $_POST['idalumnocarrera'] : $dataIdAlumnoCarrera;
$valueMateria = isset($_POST['idmateria']) ? $_POST['idmateria'] : 0;
$valueComision = isset($_POST['idcomision']) ? $_POST['idcomision'] : 0;
?>
<style>
    input { text-transform: uppercase; }
    .small, small { font-size: 82% !important;  }

    #table-materias tr.seleccionable {
        cursor: pointer;
    }
    #table-comisiones tr.seleccionable {
        cursor: pointer;
    }
    .form-group {
        margin-bottom: 0;
    }
</style>

<?php if (count($validationErrors) > 0) { ?>
    <div class="form-group col-xs-12">
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php foreach ($validationErrors as $error) { ?>
                <?php echo ($error); ?><br />
            <?php } ?>
        </div>
    </div>
    <div class="clearfix"></div>
<?php } ?>
<form class="form-horizontal" action="" method="POST" id="formu" accept-charset="utf-8">  
    <fieldset>
        <h3>Matriculación
            <button type="button" id="btnVolver" class="btn btn-warning pull-right">Volver</button><br />
        </h3>
        <?php if (count($viewDataCarreras) == 0) { 
            include("matriculacion-escalonada.php");
         } ?>
        <?php if ($modoAdministrativo) { ?>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Alumno</label>
                    <div class="col-sm-10">
                        <p class="form-control-static"><?php echo $viewData[0]['apellido'] ?>,<?php echo $viewData[0]['nombre'] ?></p>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Documento</label>
                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <?php echo $viewData[0]['descripcion'] ?> <?php echo $viewData[0]['documentonro'] ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php 
        //Cargo la lista de Carreras para matricularse
        if (count($viewDataCarreras) != 0) { ?>
            <div class="form-group col-xs-12">
                <input type="hidden" name="idalumnocarrerahidden" id="idalumnocarrerahidden" value="" />
                <label for="carrera" class="col-xs-2 control-label">Carrera</label>
                <div class="col-xs-10">
                    <select class="form-control" id="carrera" name="carrera" 
                            <?php if (!$flagComision) { ?>autofocus="on" <?php } ?>>
                                <?php foreach ($viewDataCarreras as $carrera) { ?>
                            <option value="<?php echo $carrera['idcarrera']; ?>" data-idalumnocarrera="<?php echo $carrera['idalumnocarrera']; ?>" 
                                    <?php if ($carrera['idcarrera'] == $valueCarrera) echo "selected"; ?>><?php echo($carrera['descripcion']." - ".$carrera['instrumento']); ?></option>
                                <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>
    </fieldset>
<?php if (count($viewDataCarreras) != 0) { ?>
    <div class="col-sm-6">
        <fieldset>
            <h4>Materias</h4>
            <input type="hidden" name="idmateria" id="idmateria" value="" />
            <input type="hidden" name="tieneregla6" id="tieneregla6" value="false" />	
            <input type="hidden" name="cumpleregla6" id="cumpleregla6" value="false" />
            <div class="panel panel-default">
                <div class="panel-heading" data-toggle="collapse" data-target="#mat" id="ya-matriculadas" style="cursor: pointer;">
                    Ya matriculadas<span class="pull-right glyphicon glyphicon-plus"></span>
                </div>
                <div class="panel-body collapse" id="mat">
                    <table class="table table-striped  table-condensed" id="table-materias-inscriptas">
                        <thead>
                            <tr>
                                <th>Materia</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <div class="panel panel-default">

                <div class="panel-heading">
                    PASO 1 - Disponibles para matriculación
                </div>
                <div class="panel-body">
                    <table class="table table-striped table-hover table-condensed" id="table-materias">
                        <thead>
                            <tr>
                                <th>Materia</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </fieldset>
    </div>
    <div class="col-sm-6">
        <fieldset>

            <div id="sin-reglas-matriculacion" hidden="">
                <h4>Correlatividades para matricularse</h4>
                <div class="panel panel-default">
                    <div class="panel-body">
                        <p style="margin-bottom: 0px"><small>La materia elegida no tiene condiciones de inscripcion</small></p>
                    </div>
                </div>
            </div>

            <div id="reglas-matriculacion" hidden="" >
                <h4>Correlatividades para matricularse</h4>

                <div class="panel panel-default">

                    <div class="panel-body"  id="correlativas">

                        <table class="table table-striped table-hover table-condensed" id="table-reglascorrelativas">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Carrera</th>
                                    <th>Materia</th>
                                    <th>Area</th>
                                    <th>Instrumento</th>
                                    <th>Cumple</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <h4>Comisiones</h4>

            <div class="panel panel-default">
                <div class="panel-heading collapse comisiones-title">            
                </div>
                <div class="panel-body"  id="comm">

                    <table class="table table-striped table-hover table-condensed" id="table-comisiones">
                        <thead>
                            <tr>
                                <th>Nombre Comisión</th>
                                <th>Comisión</th>
                                <th>Turno</th>
                                <th>Cupo</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="detalle-comision" class="panel panel-default">
                <div id="lblComision"  class="panel-heading"></div>
                <div id="comisiones-title" class="panel-heading collapse">            
                </div>
                <input type="hidden" name="idcomision" id="idcomision" value="" />
                <div class="panel-body">
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
                    <div class="form-group col-sm-12 required 
                         <?php if (array_key_exists('cupotipo', $validationErrors)) echo "has-error"; ?>">
                        <label for="comision-detalle-cupotipo" class="col-sm-2 control-label">Cupo/Rol</label>
                        <div class="col-sm-10">
                            <select class="form-control" id="comision-detalle-cupotipo" name="comision-detalle-cupotipo" 
                                    <?php if (array_key_exists('cupotipo', $validationErrors)) echo "autofocus"; ?>>
                                <option value="0">SELECCIONAR</option>
                            </select>
                            <?php if (array_key_exists('cupotipo', $validationErrors)) { ?>
                                <span id="" class="help-block"><?php echo ($validationErrors['cupotipo']) ?></span>
                            <?php } ?>
                        </div>
                    </div>

                    <?php if ($ses->tienePermiso('', 'Matricularme desde Gestion de Alumnos')) { ?>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-6 control-label">Cupo</label>
                                <div class="col-sm-6">
                                    <p id="comision-detalle-cupocant" class="form-control-static"></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="col-sm-6 control-label">Condicional</label>
                                <div class="col-sm-6">
                                    <input type="checkbox" name="check-condicional" class="form-control-static" id="check-condicional">
                                </div>
                            </div>
                        </div>
                    <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-6 control-label">Enviar Notificación al alumno</label>
                                <div class="col-sm-6">
                                    <input type="checkbox" name="check-email" class="form-control-static" id="check-email">
                                </div>
                            </div>
                        </div>
                    <?php } else { ?>

                        <div class="col-sm-12">
                            <div class="form-group">
                                <label class="col-sm-2 control-label">Cupo</label>
                                <div class="col-sm-10">
                                    <p id="comision-detalle-cupocant" class="form-control-static"></p>
                                </div>
                            </div>
                        </div>
                    <?php }; ?>
                    <div class="form-group">
                        <div class="col-xs-12">
                            <button type="button" id="button-matriculacion" class="btn btn-lg btn-primary">Matricular</button>
                        </div>
                    </div>

                </div>

            </div>

        </fieldset>
    </div>
<?php } //if (count($viewDataCarreras) != 0) { ?>
</form>

<script type="text/javascript">

    $().ready(function () {
        // Variable global para manejar las reglas de correlatividad de las materias.
        var correlativas;
        $('.collapse').on('shown.bs.collapse', function () {
            $(".glyphicon").removeClass("glyphicon-plus")
                            .addClass("glyphicon-minus");
        });

        $('.collapse').on('hidden.bs.collapse', function () {
            $(".glyphicon").removeClass("glyphicon-minus")
                            .addClass("glyphicon-plus");
        });

        $('.correlativa-simultaneas').on('show.bs.modal', function (e) {
            $('#modal-error').hide();
        });
        $('.correlativa-simultaneas').on('shown.bs.modal', function (e) {
            $('.matsim-first').focus();
        });
        // Obtiene las reglas de la materia seleccionada y despliega una tabla 
        // con las mismas y si se cumplen o no. Caso contrario un msj informando
        // que no hay reglas.
        function evaluarCondiciones(idMateria) {

            var tbody = $('#table-reglascorrelativas > tbody');
            tbody.empty();
            $('#reglas-matriculacion').hide();
            $('#sin-reglas-matriculacion').hide();
            var reglas = [];
            $.each(JSON.parse(correlativas), function (i, elem) {
                if (elem.idmateria === idMateria) {
                    reglas = elem.correlativas;
                    return false;
                }
            });
            if (reglas.length > 0) {
                $.each(reglas, function (i, regla) {
                    var tr = $("<tr/>");
                    var tdNombre = $("<td/>");
                    var smallNombre = $("<small/>").text(regla.nombre);
                    tdNombre.append(smallNombre);
                    var tdCarrera = $("<td/>");
                    if (regla.carrera != undefined) {
                        var smallCarrera = $("<small/>").text(regla.carrera);
                    } else {
                        var smallCarrera = $("<small/>").text("---");
                    }
                    tdCarrera.append(smallCarrera);
                    var tdMateria = $("<td/>");
                    ;
                    if (regla.materia != undefined) {
                        var smallMateria = $("<small/>").text(regla.materia);
                    } else {
                        var smallMateria = $("<small/>").text("---");
                    }
                    tdMateria.append(smallMateria);
                    var tdArea = $("<td/>");
                    ;
                    if (regla.area != undefined) {
                        var smallArea = $("<small/>").text(regla.area);
                    } else {
                        var smallArea = $("<small/>").text("---");
                    }
                    tdArea.append(smallArea);
                    var tdInstrumento = $("<td/>");
                    if (regla.instrumento != undefined) {
                        var smallInstrumento = $("<small/>").text(regla.instrumento);
                    } else {
                        var smallInstrumento = $("<small/>").text("---");
                    }
                    tdInstrumento.append(smallInstrumento);
                    var tdCumple = $("<td/>");
                    var icono = $("<span/>");
                    var smallInstrumento = $("<small/>");
                    if (regla.estado) {
                        icono.addClass("glyphicon glyphicon-ok");
                        tr.addClass("success");
                    } else {
                        icono.addClass("glyphicon glyphicon-remove");
                        tr.addClass("danger");
                    }
                    smallInstrumento.append(icono);
                    tdCumple.append(smallInstrumento);
                    tr.append(tdNombre, tdCarrera, tdMateria, tdArea, tdInstrumento, tdCumple);
                    tbody.append(tr);
                });
                $('#reglas-matriculacion').show();
            } else {
                $('#sin-reglas-matriculacion').show();
            }

        }

        //se dispara desde el OnClick de la Materia elegida.
        //Obtengo las comisiones disponibles de la materia seleccionada.								   
        function procesarMateria(idMateria) {
            $('#idmateria').val(idMateria);
            $('#table-comisiones > tbody').empty();
            $("#detalle-comision").hide();
            $(".comisiones-title").text("PASO 2 - Materia:  " +
            $('#materia' + idMateria).attr('data-materia'));
            $('#correlativa-simultaneas-title').text('Materias con cursada simultanea para: ' + $('#materia' + idMateria).attr('data-materia'));
            $(".comisiones-title").show();
            evaluarCondiciones(idMateria);
            var sData = 'materia=' + idMateria;
            sData += '&alumno=<?php echo $dataIdPersona; ?>';
            sData += '&carrera=' + $("#carrera").val();
            sData += '&idalumnocarrera=' + $("#carrera").find(':selected').data('idalumnocarrera');
            $.ajax({url: "/matriculacion/ajaxcomisiones", method: 'POST', data: sData, dataType: 'json', success: function (response) {
                    if (response.length == 0) {
                        $("#table-comisiones > tbody:last-child").append('<tr><td colspan=5>No existen comisiones</td></tr>');
                    } else {
                        $.each(response, function (i, value) {
                            newRow = '<tr id="comision' + value.idcomision + '" class="seleccionable" data-id="' + value.idcomision + '">';
                            newRow += '<input type="hidden" id="comision-' + value.idcomision + '-codigo" name="comision-' + value.idcomision + '-codigo" value="' + value.codigo + '" />';
                            newRow += '<input type="hidden" id="comision-' + value.idcomision + '-profesor" name="comision-' + value.idcomision + '-profesores" value="' + value.profesores + '" />';
                            newRow += '<input type="hidden" id="comision-' + value.idcomision + '-comision" name="comision-' + value.idcomision + '-comision" value="' + value.nombre + '" />';
                            newRow += '<input type="hidden" id="comision-' + value.idcomision + '-horarios" name="comision-' + value.idcomision + '-horarios" value="' + value.horarios + '" />';
                            newRow += '<input type="hidden" id="comision-' + value.idcomision + '-cupos" name="comision-' + value.idcomision + '-cupos" value="' + value.cupoCupos + '" />';
                            newRow += '<input type="hidden" id="comision-' + value.idcomision + '-aula" name="comision-' + value.idcomision + '-aula" value="' + value.aula + '" />';
                            if (value.idcomision == 0) { //SIN COMISION ANOTADO LIBRE
                                newRow += '<td><small>---</small></td>';
                                newRow += '<td><small><b>' + value.nombre + '</b></small></td>';
                                newRow += '<td><small>---</small></td>';
                                newRow += '<td><small>---</small></td>';
                            } else {
                                newRow += '<td><small>' + value.codigo + '</small></td>';
                                newRow += '<td><small>' + value.nombre + '</small></td>';
                                newRow += '<td><small>' + value.turno + '</small></td>';
                                newRow += '<td><small>' + value.cupoDisponible + '/' + value.cupoTotal + '</small></td>';
                            }
                            newRow += '</tr>';
                            $("#table-comisiones > tbody:last-child").append(newRow);
                            $('#comision' + value.idcomision).click(function () {
                                procesarComision(value.idcomision);
                            });
                        });
                        $('html, body').animate({scrollTop: $('#sin-reglas-matriculacion').offset().top - 60}, 'fast');
                    }
<?php
if ($flagComision && $valueComision > 0) {
    $flagComision = false;
    ?>
                        procesarComision(<?php echo $valueComision; ?>);
<?php } ?>
                }
            });
            $('html, body').animate({scrollTop: $('#sin-reglas-matriculacion').offset().top - 60}, 'fast');
        }

        // TODO: Ver bien para que sirve...
        function reloadCupomatsim(matsim) {
            var valor = $('#matsim' + matsim + ' option:selected').attr('data-cupos');
            var arrValores = valor.split('~**~');
            $('#cupomatsim' + matsim).empty();
            for (var i = 0; i < arrValores.length; i += 3) {
                $('#cupomatsim' + matsim).append($('<option>').text(arrValores[i + 1]).attr('value', arrValores[i]).attr('data-cupo', arrValores[i + 2]));
            }

            $('#matsim' + matsim + '-data').html('<b>Profesores</b>: ' + $('#matsim' + matsim + ' option:selected').attr('data-profesores') + ' <b>Horarios</b>: ' + $('#matsim' + matsim + ' option:selected').attr('data-HORARIOS'));
        }

        // Se dispara al elegir una comision.
        function procesarComision(idComision) {
            $('#idcomision').val(idComision);
            if (idComision > 0) { //Si tiene una comision
                var arrProfesores = $('#comision-' + idComision + '-profesor').val().split("~**~");
                $('#comision-detalle-profesor').empty();
                $.each(arrProfesores, function (i, value) {
                    if (value != "false")
                        $('#comision-detalle-profesor').append("<p class=\"form-control-static\">" + value + "</p>");
                    else
                        $('#comision-detalle-profesor').append("---");
                });
                var arrHorarios = $('#comision-' + idComision + '-horarios').val().split("~**~");
                $('#comision-detalle-horarios').empty();
                $.each(arrHorarios, function (i, value) {
                    if (value != "false")
                        $('#comision-detalle-horarios').append("<p class=\"form-control-static\">" + value + "</p>");
                    else
                        $('#comision-detalle-horarios').append("---");
                });
                var aula = $('#comision-' + idComision + '-aula').val();
                $('#comision-detalle-aula').empty();
                if (aula != "null")
                    $('#comision-detalle-aula').append("<p class=\"form-control-static\">" + aula + "</p>");
                else
                    $('#comision-detalle-aula').append("---");
                $("#lblComision").text('PASO 3 - Comisión:  ' + $('#comision-' + idComision + '-comision').val());
                var arrCupos = $('#comision-' + idComision + '-cupos').val().split("~**~");
                $('#comision-detalle-cupotipo').empty();
                $.each(arrCupos, function (i, value) {
                    var arrValoresCupos = value.split("~***~");
                    $('#comision-detalle-cupotipo').append($('<option>').text(arrValoresCupos[0]).attr('value', arrValoresCupos[3]).attr('data-cupo', arrValoresCupos[1]).attr('data-disponible', arrValoresCupos[2]));
                });
                $("#comision-detalle-cupocant").text($('#comision-detalle-cupotipo option:selected').attr('data-disponible') + ' de ' + $('#comision-detalle-cupotipo option:selected').attr('data-cupo'));
                $('#comision-detalle-cupotipo').change(function () {
                    $("#comision-detalle-cupocant").text($('#comision-detalle-cupotipo option:selected').attr('data-disponible') + ' de ' + $('#comision-detalle-cupotipo option:selected').attr('data-cupo'));
                });
            } else //Anotado LIBRE Sin comision
            {
                $('#comision-detalle-profesor').empty();
                $('#comision-detalle-profesor').append("---");
                $('#comision-detalle-horarios').empty();
                $('#comision-detalle-horarios').append("---");
                $('#comision-detalle-aula').empty();
                $('#comision-detalle-aula').append("---");
                $('#comision-detalle-cupotipo').empty();
                $('#comision-detalle-cupotipo').append("---");
                $("#comision-detalle-cupocant").empty();
                $("#comision-detalle-cupocant").append("---");
                $("#lblComision").text("COMISIÓN: LIBRE (SIN COMISION)");
            }
            $('#detalle-comision').show();
            $('html, body').animate({scrollTop: $('#detalle-comision').offset().top - 60}, 'fast');
        }

        //Funcion change de Carrera
        $("#carrera").change(function () {
            $('#table-materias > tbody').empty();
            $('#table-materias-inscriptas > tbody').empty();
            $(".comisiones-title").hide();
            $('#table-comisiones > tbody').empty();
            $("#detalle-comision").hide();
            
            var sData = 'carrera=' + $(this).val();
            sData += '&idalumnocarrera=' + $("#carrera").find(':selected').data('idalumnocarrera');
            sData += '&alumno=<?php echo $dataIdPersona; ?>';
            sData += '&modo=<?php
            if ($modoAdministrativo)
                echo "2";
            else
                echo "1";
            ?>';
            $.ajax({url: "/matriculacion/ajaxmaterias", method: 'POST', data: sData, dataType: 'json', success: function (response) {
                    if (response.length == 0) {
                        $("#table-materias > tbody:last-child").append('<tr><td colspan=2>Búsqueda sin resultados</td></tr>');
                    } else {
                        correlativas = JSON.stringify(response);
                        $.each(response, function (i, value) {
                            newRow = '<tr id="materia' + value.idmateria + '" class="seleccionable" data-materia="' + value.nombre + '" data-anio="' + value.anio + '"">';
                            newRow += '<td><small>' + value.nombre + '</small></td>';
                            newRow += '</tr>';
                            $("#table-materias > tbody:last-child").append(newRow);
                            $('#materia' + value.idmateria).click(function () {
                                procesarMateria(value.idmateria);
                            });
                        });
               }
<?php
if ($flagMateria && $valueMateria > 0) {
    $flagMateria = false;
    ?> procesarMateria(<?php echo $valueMateria; ?>);<?php } ?>
                }});
            sData = 'carrera=' + $(this).val();
            sData += '&idalumnocarrera=' + $("#carrera").find(':selected').data('idalumnocarrera');
            sData += '&alumno=<?php echo $dataIdPersona; ?>';
            sData += '&modo=3';
            
            $.ajax({url: "/matriculacion/ajaxmaterias", method: 'POST', data: sData, dataType: 'json', success: function (response) {
                    if (response.length == 0) {
                        $("#table-materias-inscriptas > tbody:last-child").append('<tr><td colspan=2>Sin materias</td></tr>');
                    } else {
                        $.each(response, function (i, value) {
                            newRow = '<tr>';
                            newRow += '<td style="color: grey;"><small>' + value.nombre + '</small>';
                            if (value.comision != null)
                                newRow += "<p  style='font-size:9px;'>(" + value.comision + ")";
                            else
                                newRow += "<p  style='font-size:9px;'>(LIBRE)";
                            newRow += '</td></tr>';
                            $("#table-materias-inscriptas > tbody:last-child").append(newRow);
                        });
                    }

                }});
        });
        
        $("#btnVolver").click(function () {
<?php if ($ses->tienePermiso('', 'Matricularme desde Gestion de Alumnos')) { ?>
                window.location.href = '/gestion-alumnos/<?php echo $params; ?>';
<?php } else { ?>
                window.location.href = '/';
<?php } ?>
        });
<?php if (($dataIdCarrera != 0 && count($viewDataCarreras) > 0) || (!$ses->tienePermiso('', 'Matricularme desde Gestion de Alumnos'))) { ?>
            $("#carrera").trigger('change');
<?php } ?>

        // Se dispara el hcer clik en el boton de "matricular" y
        // evalua el cumplimiento de la regla 6
        $('#button-matriculacion').click(function () {
            var reglas = [];
            var idMateria = $('#idmateria').val();
            var tiene = false;
            var cumple = true;
            // Busco las correlatividades de la materia elegida.
            $.each(JSON.parse(correlativas), function (i, materia) {
                if (materia.idmateria === idMateria) {
                    // Busco si la materia tiene como correlativa alguna regla6
                    $.each(materia.correlativas, function (i, regla) {
                        // Si tiene relga 6 
                        if (regla.idregla == 6) {
                            tiene = true;
                            // Si cumple regla6 
                            if (!regla.estado) {
                                cumple = false;
                            }
                        }
                    });
                    // si no tiene regla6 mando cumple en false por requerimiento del backend;
                    if (!tiene) {
                        cumple = false;
                    }
                    return false;
                }
            });
            $("#tieneregla6").val(tiene);
            $("#cumpleregla6").val(cumple);
            //Agrego idalumnocarrera al form antes del submit.
            
            $("#idalumnocarrerahidden").attr('value',$("#carrera").find(':selected').data('idalumnocarrera'));
            
            $('#formu').submit();
        });
    }
    );
</script>