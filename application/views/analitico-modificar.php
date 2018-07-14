<style>
    /*.mce-widget{*/
    /*display: none;*/
    /*}*/
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
    /*
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
    */
    .table td{
        text-align: center;
    }
    .table th{
        text-align: center;
    }
</style>
<?php
// arreglo de anios (del actual hasta 10 antes)
$anios = array();
for ($i = 0; $i < 20; $i++) {
    $anios[] = date('Y') - $i;
}
?>

<form class="form-horizontal" action="" method="POST" accept-charset="utf-8">  
    <fieldset>
        <h3>Materia del Alumno</h3>

        <div class="form-group col-xs-12">
            <label for="alumno" class="col-xs-2 control-label">Alumno</label>
            <div class="col-xs-7">
                <input type="text" class="form-control" name="alumno" value="<?php echo $alumnomateria_detalle[0]["alumno"]; ?>" readonly />
            </div>
            <label for="documento" class="col-xs-1 control-label">Documento</label>
            <div class="col-xs-2">
                <input type="text" class="form-control" name="documento" value="<?php echo $alumnomateria_detalle[0]["documentonro"]; ?>" readonly />
            </div>
        </div>

        <div class="form-group col-xs-12">
            <label for="carrera" class="col-xs-2 control-label">Carrera / Ciclo</label>
            <div class="col-xs-10">
                <input type="text" class="form-control" name="nota['carrera']" value="<?php echo $alumnomateria_detalle[0]["carrera"]; ?>" readonly />
            </div>
        </div>

        <div class="form-group col-xs-12">
            <label for="materia" class="col-xs-2 control-label">Materia</label>
            <div class="col-xs-10">
                <input type="text" class="form-control" name="materia" value="<?php echo $alumnomateria_detalle[0]["materia"]; ?>" readonly />
            </div>
        </div>

        <div class="form-group col-xs-12">
            <label for="comision" class="col-xs-2 control-label">Comisión</label>
            <div class="col-xs-10">
                <input type="text" class="form-control" name="comision" value="<?php echo $alumnomateria_detalle[0]["comision"]; ?>" readonly />
            </div>
        </div>
        <div class="form-group col-xs-12">
            <label for="idalumnomateria" class="col-xs-2 control-label">ID Alumno Materia</label>
            <div class="col-xs-2">
                <input type="text" class="form-control" name="idalumnomateria" value="<?php echo $alumnomateria_detalle[0]["idalumnomateria"]; ?>" readonly />
            </div>
            <label for="idalumnomateria" class="col-xs-2 control-label">Estado Alumno Materia</label>
            <div class="col-xs-2">
                <input type="text" class="form-control" name="estadoalumnomateria" value="<?php echo $alumnomateria_detalle[0]["estadomateria"]; ?>" readonly />
            </div>
            <div class="col-xs-4">
                <a id="eliminarmateria" class="btn btn-danger" href="eliminarmateria/<?php echo $alumnomateria_detalle[0]["idalumnomateria"]; ?>">Eliminar Registro Materia</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/analitico/<?php echo $alumnomateria_detalle[0]["idalumnocarrera"]; ?>/<?php echo $alumnomateria_detalle[0]["idpersona"]; ?>" class="btn btn-primary" style="text-align:right">Volver</a>
            </div>
        </div>
    </fieldset>

</form>



<!-- tocado por leo -->

<h3>Datos a Modificar</h3>
<hr>


<form class="form-horizontal" id="form-editar-materia" action='modificarMateria/<?php echo $alumnomateria_detalle[0]["idalumnomateria"]; ?>' method='POST'>
    <fieldset>

        <div class="form-group col-xs-12">
            <label for="anio-cursada" class="col-xs-2 control-label" for="anio-cursada">Año cursada:</label>
            <div class="col-xs-6">
                <select class="form-control changeMateria" id="anio-cursada" name="datos[anio-cursada]">
                    <?php foreach ($anios as $anio): ?>
                        <option value="<?php echo $anio; ?>"  <?php if ($anio == $alumnomateria_detalle[0]["aniocursada"]) echo "selected"; ?>><?php echo $anio; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class=" col-xs-4">       
                <button class="btn btn-warning" type="submit" id="editar-materia">Actualizar Materia</button>

            </div>


        </div>
        <div class="form-group col-xs-12">

            <label for="fecha-aprobacion" class="col-xs-2 control-label"  for="fecha-aprobacion">Fecha de Aprobación:</label>
            <div class=" col-xs-6">
                <input placeholder="dd/mm/aaaa" maxlength="10" class="form-control changeMateria" id="fecha-aprobacion" name="datos[fecha-aprobacion]" value="<?php echo $alumnomateria_detalle[0]["fechaaprobacion"] ?>">
            </div>


        </div>
    </fieldset>

</form>

<h3>Notas de la Materia</h3>
<hr>

<div class="row">

    <div class="col-md-12">

        <fieldset>
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="col-md-12 text-right">       
                        <button class="btn btn-info" id="agregar-nota">Agregar Nota</button>
                    </div>
                    <table class="table table-striped table-hover table-condensed" id="table-resultados1">
                        <thead>
                            <tr>
                                <th>IDAlumnoMateriaNota</th>
                                <th>Tipo Examen</th>
                                <th>Fecha</th>
                                <th>Nota</th>
                                <th>Libro</th>
                                <th>Tomo</th>
                                <th>Folio</th>
                                <th>Usucrea</th>
                                <th>FechaCrea</th>
                                <th>Usumodi</th>
                                <th>Fechamodi</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($examenes as $key => $examen): ?>

                                <tr>
                                    <td><?php echo $examen["idalumnomaterianota"]; ?></td>
                                    <td><?php echo $examen["tipoexamen"]; ?></td>
                                    <td><?php echo $examen["fechaexamen"]; ?></td>
                                    <td><?php echo $examen["notaexamen"]; ?></td>
                                    <td><?php echo $examen["libro"]; ?></td>
                                    <td><?php echo $examen["tomo"]; ?></td>
                                    <td><?php echo $examen["folio"]; ?></td>
                                    <td><?php echo $examen["usucrea"]; ?></td>
                                    <td><?php echo $examen["fechacrea"]; ?></td>
                                    <td><?php echo $examen["usumodi"]; ?></td>
                                    <td><?php echo $examen["fechamodi"]; ?></td>
                                    <td>
                                        <a href="eliminarnota/<?php echo $alumnomateria_detalle[0]["idalumnomateria"]."/".$examen["idalumnomaterianota"]; ?>" class="btn btn-default btn-xs quitar-nota" aria-label="Quitar Nota" title="Quitar Nota">
                                            <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
                                        </a>
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

<h3>Historial de cambios</h3>
<hr>

<div class="row">

    <div class="col-lg-12">

        <fieldset>
            <div class="panel panel-default">
                <div class="panel-body">
                    <table style="font-size: 10px" class="table table-sm table-hover table-condensed text-small" id="table-resultados1">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>Carrera</th>
                                <th>Materia</th>
                                <th>Comisión</th>
                                <th>Año</th>
                                <th>EstadoMateria</th>
                                <th>Fecha Apro.</th>
                                <th>Usu Crea</th>
                                <th>Fecha Crea</th>
                                <th>Usu Modif</th>
                                <th>Fecha Modif</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($alumnomateria_historial as $key => $value): ?>

                                <tr>
                                    <td><?php echo $value["id"]; ?></td>
                                    <td><?php echo $value["carrera"]; ?></td>
                                    <td><?php echo $value["materia"]; ?></td>
                                    <td><?php echo $value["comision"]; ?></td>
                                    <td><?php echo $value["aniocursada"]; ?></td>
                                    <td><?php echo $value["estadomateria"]; ?></td>
                                    <td><?php echo $value["fechaaprobacion"]; ?></td>
                                    <td><?php echo $value["usucrea"]; ?></td>
                                    <td><?php echo $value["fechacrea"]; ?></td>
                                    <td><?php echo $value["usumodi"]; ?></td>
                                    <td><?php echo $value["fechamodi"]; ?></td>
                                    <td><?php echo $value["accion"]; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                </div>
            </div>
        </fieldset>  

    </div>
    <div class="col-lg-12">

        <fieldset>
            <div class="panel panel-default">
                <div class="panel-body">
                    <table style="font-size: 10px" class="table table-striped table-hover table-condensed" id="table-resultados1">
                        <thead>
                            <tr>
                                <th>#ID</th>
                                <th>IDAlumnoMateriaNota</th>
                                <th>Tipo Examen</th>
                                <th>ID Examen</th>
                                <th>Fecha Examen</th>
                                <th>Nota</th>
                                <th>Libro</th>
                                <th>tomo</th>
                                <th>Folio</th>
                                <th>Usu Crea</th>
                                <th>Fecha Crea</th>
                                <th>Usu Modif</th>
                                <th>Fecha Modif</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <?php foreach ($alumnomaterianota_historial as $key => $value): ?>

                                <tr>
                                    <td><?php echo $value["id"]; ?></td>
                                    <td><?php echo $value["idalumnomaterianota"]; ?></td>
                                    <td><?php echo $value["tipoexamen"]; ?></td>
                                    <td><?php echo $value["idexamen"]; ?></td>
                                    <td><?php echo $value["fechaexamen"]; ?></td>
                                    <td><?php echo $value["notaexamen"]; ?></td>
                                    <td><?php echo $value["libro"]; ?></td>
                                    <td><?php echo $value["tomo"]; ?></td>
                                    <td><?php echo $value["folio"]; ?></td>
                                    <td><?php echo $value["usucrea"]; ?></td>
                                    <td><?php echo $value["fechacrea"]; ?></td>
                                    <td><?php echo $value["usumodi"]; ?></td>
                                    <td><?php echo $value["fechamodi"]; ?></td>
                                    <td><?php echo $value["accion"]; ?></td>
                                </tr>
                            <?php endforeach; ?>

                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>
        </fieldset>  

    </div>
</div>
<div class="modal" id="agregarNotaModal">

    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h3 class="modal-title">Agregar Nota</h3>
            </div>                
            <form id="agregar-nota-form" action="crearnota/<?php echo $alumnomateria_detalle[0]["idalumnomateria"]; ?>" method="post">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="titulo" class="control-label">Tipo Examen:</label>
                        <select required id="tipo-examen" name="nota[tipoexamen]" class="form-control">
                            <option value="">Seleccionar Examen</option>
                            <?php foreach ($tipo_examen as $tipo): ?>
                                <option value="<?php echo $tipo['idtipoexamen']; ?>"><?php echo $tipo['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="contenido" class="control-label">Fecha:</label>
                        <input required  placeholder="dd/mm/aaaa" type="text" maxlength="10" id="fecha" name="nota[fecha]" class="form-control"/>
                    </div>
                    <div class="form-group">
                        <label for="contenido" class="control-label">Nota:</label>
                        <input required type="number" id="nota" name="nota[nota]" min="0.00" step="0.01" max="10.00" class="form-control"/>
                    </div>
                    <div class="form-group ltf">
                        <label for="contenido" class="control-label">Libro:</label>
                        <input  type="text" id="libro" name="nota[libro]" maxlength="45" class="form-control text-uppercase"/>
                    </div>
                    <div class="form-group ltf">
                        <label for="contenido" class="control-label">Tomo:</label>
                        <input  type="text" id="tomo" name="nota[tomo]" maxlength="45" class="form-control text-uppercase" />
                    </div>
                    <div class="form-group ltf">
                        <label for="contenido" class="control-label">Folio:</label>
                        <input  type="text" id="folio" name="nota[folio]"  maxlength="45" class="text-uppercase form-control"/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" id="cancelarNota">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="agregarNota">Enviar</button>
                </div>
            </form>

        </div>
    </div>
</div>
<script>
function isValidDate(dateString){
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
   
   $().ready(() => {
        let finales = ['1', '2', '5', '6'];
        function clearForm() {
            $('#tipo-examen').val("");
            $('#fecha').val("");
            $('#nota').val("");
            $('#libro').val("");
            $('#tomo').val("");
            $('#folio').val("");
        }
        function closeNovedadModal() {
            var ok = confirm("¿Esta seguro que desea cancelar?");
            if (ok) {
                clearForm();
                $('#agregarNotaModal').hide();
            }
        }
        // When the user clicks the button, open the modal
        $("#agregar-nota").click(() => {
            $('#agregarNotaModal').show();
        });

        // When the user clicks on <span> (x), close the modal
        $(".close").click(() => {
            closeNovedadModal();
        });
        // When the user clicks on <button> (Cancelar), close the modal
        $("#cancelarNota").click(() => {
            var ok = confirm("¿Esta seguro que desea cancelar?");
            if (ok) {
                clearForm();
                $('#agregarNotaModal').hide();
            }
        });
        function validarForm() {
            let ok = true;
            const nota = Number($('#nota').val());
            const cumple = (0 <= nota && nota <= 10)
            const nan = isNaN(nota);
            if (nan || !cumple)
            {
                alert("La nota debe ser un número entre 0 y 10");
                ok = false;
            } else {
                let intiger = Number.isInteger(nota);
                if (!intiger) {
                    let examen = $("#tipo-examen").val();
                    if (examen != 5) {
                        alert("Solo el final por promoción permite números decimales");
                        ok = false;
                    }
                } 
            }
			if (!isValidDate($("#fecha").val())){
				 alert("Fecha Inválida");
				ok=false;
			}
			
            return ok;
        }
		
        $("#agregar-nota-form").submit((event) => {
            if (!validarForm()) {
                event.preventDefault();
                return false;
            } else {
                var ok = confirm("¿Esta seguro que desea agregar la nota?");
                if (!ok) {
                    event.preventDefault();
                    return false;
                }
            }
        });
        $("#form-editar-materia").submit((event) => {
            var ok = confirm("¿Esta seguro que desea modificar esta materia?");
            if (!ok) {
                event.preventDefault();
                return false;
            }
        });
        // When the user clicks anywhere outside of the modal, close it
        $(window).click((event) => {
            if (event.target == $('#agregarNotaModal')[0]) {
                var ok = confirm("¿Esta seguro que desea cancelar?");
                if (ok) {
                    clearForm();
                    $('#agregarNotaModal').hide();
                }
            }
        });
        $(".quitar-nota").click((event) => {
            const ok = confirm("¿Esta seguro de quitar esta nota de la materia del alumno?");
            if (!ok) {
                return false;
            }
        });

        $(".changeMateria").change(() => {
            $("#editar-materia").prop('disabled', false)
        });

        $(".ltf").hide();
        $("#tipo-examen").change((event) => {
            if ($.inArray($(event.target).val(), finales) >= 0) {
                $(".ltf").show();
                $("#libro").prop('disabled', false);
                $("#tomo").prop('disabled', false);
                $("#folio").prop('disabled', false);
                $("#libro").prop('required', true);
                $("#tomo").prop('required', true);
                $("#folio").prop('required', true);
            } else {
                $(".ltf").hide();
                $("#libro").prop('disabled', true).val("");
                $("#tomo").prop('disabled', true).val("");
                $("#folio").prop('disabled', true).val("");
                $("#libro").prop('required', false);
                $("#tomo").prop('required', false);
                $("#folio").prop('required', false);
            }
        });
		
        $("#eliminarmateria").click(() => {
            const ok = confirm("¿Esta seguro de eliminar esta materia del analítico del alumno?");
            if (!ok) {
                return false;
            }
        });
    });

</script>