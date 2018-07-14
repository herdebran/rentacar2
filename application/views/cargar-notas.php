<style>
    input { text-transform: uppercase; }
    .small, small { font-size: 82% !important;  }
</style>
<?php if (count($validationErrors) > 0) { ?>
    <div class="alert alert-danger" role="alert">
        <?php foreach ($validationErrors as $error) { ?>
            <p><?php echo $error; ?>
            <?php } ?>
    </div>
<?php } ?>

<fieldset>
    <h3>Carga Notas de Exámen</h3>

    <div class="form-group col-xs-12">
        <label class="col-xs-1 control-label">Tipo</label>
        <div class="col-xs-3">
            <?php echo $viewDataExamen[0]['tipoexamen'] ?>
        </div>
         <label class="col-xs-1 control-label">Fecha</label>
        <div class="col-xs-4">
            <?php echo $viewDataExamen[0]['fecha'] ?>
        </div>
        <label class="col-xs-2 control-label">Finalizado?</label>
        <div class="col-xs-1">
            <?php
            if ($viewDataExamen[0]['examenfinalizado'] == 1)
                echo "Si";
            else
                echo "No";
            ?>
        </div>
    </div>



    <div class="form-group col-xs-12">
    <label class="col-xs-2 control-label">Aula</label>
    <div class="col-xs-4">
            <?php echo $viewDataExamen[0]['aula'] ?>
        </div>
        <label class="col-xs-2 control-label">Cupo</label>
        <div class="col-xs-4">
            <?php echo $viewDataExamen[0]['cupo'] ?>
        </div>
    </div>

    <div class="form-group col-xs-12">
        <label class="col-xs-1 control-label">Materia</label>
        <div class="col-xs-5">
            <?php echo $viewDataExamen[0]['materia'] ?>
        </div>
        <label class="col-xs-1 control-label">Comisión</label>
        <div class="col-xs-5">
            <?php echo $viewDataExamen[0]['comision'] ?>
        </div>
    </div>


    <div class="form-group col-xs-12">
        <label for="profesor" class="col-xs-2 control-label">Profesor/es </label>
        <div class="col-xs-10">
            <div  id="comision-detalle-profesores">
                <?php
                echo $viewDataExamen[0]['profesores'];
                ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-xs-12">
            <?php if ($viewDataExamen[0]['examenfinalizado'] == 0) { ?>
                <a id="finalizar_examen" href="/examenes/finalizar/<?php echo $dataIdExamen; ?>" id="btnFinalizar" class="btn btn-warning pull-right">Finalizar Exámen</a>
<?php } ?>
        </div>
    </div>
    <div class="form-group">
        <div class="col-xs-12">
            <a href="/gestion-examenes" class="btn btn-primary">Volver</a>
            <?php if ($viewDataExamen[0]['examenfinalizado'] == 0) { ?>
                <button  type="button" class="btn btn-primary btnSubmit">Actualizar</button>
<?php } ?>
        </div>
    </div>


</fieldset>


<fieldset>
    <h3>Alumnos Inscriptos</h3>
    <div class="panel panel-default">
        <div class="panel-body">
            <?php if (isset($reportes)): ?>
                <label for="profesor" class="col-xs-2 control-label">Presentes: </label>
                <div class="col-xs-1">
                    <div  id="comision-detalle-profesores">
                        <?php
                        echo $reportes['presentes'];
                        ?>
                    </div>
                </div>
                <label for="profesor" class="col-xs-2 control-label">Ausentes: </label>
                <div class="col-xs-1">
                    <div  id="comision-detalle-profesores">
                        <?php
                        echo $reportes['ausentes'];
                        ?>
                    </div>
                </div>
                <label for="profesor" class="col-xs-2 control-label">Aprobados: </label>
                <div class="col-xs-1">
                    <div  id="comision-detalle-profesores">
                        <?php
                        echo $reportes['aprobados'];
                        ?>
                    </div>
                </div>
                <label for="profesor" class="col-xs-2 control-label">Desaprobados: </label>
                <div class="col-xs-1">
                    <div  id="comision-detalle-profesores">
                        <?php
                        echo $reportes['desaprobados'];
                        ?>
                    </div>
                </div>
                <br> 
                <hr>
            <?php endif; ?>
<?php if (in_array($viewDataExamen[0]['idtipoexamen'], $soloFinales)): ?>
                <div class="col-md-12">
                    <div class="form-group col-xs-4 col-sm-4">
                        <label class="col-xs-2 control-label">Libro</label>
                        <div class="col-xs-10">
                            <input maxlength="45" class="form-control" type="text" name="examen-libro" id="examen-libro" value="<?php echo $libro; ?>" <?php if ($viewDataExamen[0]['examenfinalizado'] != 0) echo " readonly"; ?> />
                        </div>
                    </div>   
                    <div class="form-group col-xs-4 col-sm-4">
                        <label class="col-xs-2 control-label">Tomo</label>
                        <div class="col-xs-10">
                            <input maxlength="45" class="form-control" type="text" name="examen-tomo" id="examen-tomo" value="<?php echo $tomo; ?>" <?php if ($viewDataExamen[0]['examenfinalizado'] != 0) echo " readonly"; ?> />
                        </div>
                    </div>  
                    <div class="form-group col-xs-4 col-sm-4">
                        <label class="col-xs-2 control-label">Folio</label>
                        <div class="col-xs-10">
                            <input maxlength="45" class="form-control" type="text" name="examen-folio" id="examen-folio" value="<?php echo $folio; ?>" <?php if ($viewDataExamen[0]['examenfinalizado'] != 0) echo " readonly"; ?> />
                        </div>
                    </div>
                </div>
                <br>
                <br>
                <hr>
<?php endif; ?>

            <table class="table table-striped table-hover table-condensed tabla-alumnos" style="text-align:left" id="table-resultados">
                <thead>
                    <tr>
                        <th>#</th>
                        <th style="text-align: left">Alumno</th>
                        <th>Documento</th>
                        <th style="text-align: left">Nota</th>
                    </tr>
                </thead>
                <tbody>
                <form id="frmNotas" class="form-horizontal" action="" method="POST" accept-charset="utf-8">  
                    <?php
                    $i = 0;
                    foreach ($viewDataAlumnos as $alumno) {
                        $i++;
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td style="text-align:left">
                                <?php
                                if ($alumno['estadoalumnomateria'] == "LIBRE") {
                                    echo $alumno['apellido'] . " " . $alumno['nombre'] . " (" . $alumno['estadoalumnomateria'] . ")";
                                } else {
                                    echo $alumno['apellido'] . " " . $alumno['nombre'];
                                }
                                ?></td>
                            <td><?php echo $alumno['documentonro']; ?></td>
                            <?php
                            if (isset($alumnos[$alumno['idpersona']])) {
                                $valNota = $alumnos[$alumno['idpersona']]['nota'];
                                $valLibro = $alumnos[$alumno['idpersona']]['libro'];
                                $valTomo = $alumnos[$alumno['idpersona']]['tomo'];
                                $valFolio = $alumnos[$alumno['idpersona']]['folio'];
                            } else {
                                $valNota = $alumno['nota'];
                                $valLibro = $libro;
                                $valTomo = $tomo;
                                $valFolio = $folio;
                            }
                            ?>                
                            <td><div class=" <?php if (array_key_exists('alu' . $alumno['idpersona'] . '-nota', $validationErrors)) echo "has-error"; ?>"><input class="form-control" type="number" id="alu<?php echo $alumno['idpersona'] ?>-nota"  min="0.00" step="0.01" max="10.00" value="<?php echo $valNota; ?>" 
<?php if ($viewDataExamen[0]['examenfinalizado'] != 0 || $viewDataExamen[0]['idtipoexamen'] == 5) echo " readonly"; ?> /></div></td>
    <!--                            <td><div class=" <?php if (array_key_exists('alu' . $alumno['idpersona'] . '-libro', $validationErrors)) echo "has-error"; ?>"><input class="form-control" type="text" id="alu<?php echo $alumno['idpersona'] ?>-libro" value="<?php echo $valLibro; ?>" <?php if ($viewDataExamen[0]['examenfinalizado'] != 0) echo " readonly"; ?> /></div></td>
                            <td><div class=" <?php if (array_key_exists('alu' . $alumno['idpersona'] . '-tomo', $validationErrors)) echo "has-error"; ?>"><input class="form-control" type="text" id="alu<?php echo $alumno['idpersona'] ?>-tomo" value="<?php echo $valTomo; ?>" <?php if ($viewDataExamen[0]['examenfinalizado'] != 0) echo " readonly"; ?> /></div></td>
                            <td><div class=" <?php if (array_key_exists('alu' . $alumno['idpersona'] . '-folio', $validationErrors)) echo "has-error"; ?>"><input class="form-control" type="text" id="alu<?php echo $alumno['idpersona'] ?>-folio" value="<?php echo $valFolio; ?>" <?php if ($viewDataExamen[0]['examenfinalizado'] != 0) echo " readonly"; ?> /></div></td>-->
                        <input type="hidden" id="alu<?php echo $alumno['idpersona'] ?>-apenom" value="<?php echo $alumno['apellido'] . " " . $alumno['nombre']; ?>" />
                        <input type="hidden" name="alumnos[]" class="alumno-row" id="alu<?php echo $alumno['idpersona'] ?>"  />
                        </tr>
<?php } ?>

                </form>
                </tbody>
            </table>
        </div>
    </div>
</fieldset>
<fieldset>
        <div class="form-group">
        <div class="col-xs-12">
            <a href="/gestion-examenes" class="btn btn-primary">Volver</a>
            <?php if ($viewDataExamen[0]['examenfinalizado'] == 0) { ?>
                <button type="button" class="btn btn-primary btnSubmit">Actualizar</button>
<?php } ?>
        </div>
    </div>
</fieldset>
<script type="text/javascript">
    $().ready(function () {

        $("#finalizar_examen")
                .click(function (event) {
                    var texto = "Esta seguro de querer finalizar el examen?, al finalizarlo no podrá modificar o agregar notas a los alumnos. Las notas de los mismos seran publicadas en sus analiticos. Desea continuar?";
                    if (!confirm(texto)) {
                        event.preventDefault();
                    }
                });
    });

    $(".btnSubmit").click(function () {
        $(".alumno-row").each(function (i) {
            //console.log(i);
            var aluId = $(this).attr("id");
            var valor = aluId.substring(3) + '|';
            valor += $("#" + aluId + '-nota').val() + "|";
            valor += $.trim($("#examen-libro").val()) + "|";
            valor += $.trim($("#examen-tomo").val()) + "|";
            valor += $.trim($("#examen-folio").val()) + "|";
            valor += $("#" + aluId + '-apenom').val();
            $(this).val(valor);
        });
        $("#frmNotas").submit();

    });

</script>