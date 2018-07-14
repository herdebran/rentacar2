<?php
$message = $ses->getMessage();
$alertType = "alert-info";
switch ($message['type']) {
    case SessionMessageType::Success: $alertType = "alert-success";
        break;
    case SessionMessageType::TransactionError: $alertType = "alert-danger";
        break;
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Acta de Cursada</title>
        <!-- Normalize or reset CSS with your favorite library -->
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
        <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.2.3/paper.css"> -->
        <link href="/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="/css/normalize.css">

        <!-- Load paper.css for happy printing -->
        <link rel="stylesheet" href="/css/paper.css">
        <!-- Set page size here: A5, A4 or A3 -->
        <!-- Set also "landscape" if you need -->
        <style>@page { size: A4 portrait }</style>
        <style>
            .shorty{
                max-width: 45px;
            }

        </style>
    </head>
    <body class="A4">
        <section class="sheet padding-10mm">
            <article>
                <h5 style="text-align:left; font-size: 0.88em"> 
                        <?php if($this->POROTO->Config['empresa_nombre']=='EMPA') {?>
      <img src="/images/bannerprovincia.jpg"  style="margin-top: -3px; width:30%; height:30%" />
    <?php }else{ ?>
      <img src="/images/banner_julianaguirre2.jpg"  style="margin-top: -3px; width:30%; height:30%" />
    <?php } ?>
                    <b>ACTA DE CURSADA &nbsp;&nbsp;&nbsp;Asignatura:&nbsp;</b>
                    <?php
                    echo($arrComisiones[0]['nombre']);
                    ?>
                </h5>
                <div>
                    <table  class="table table-striped table-hover table-condensed">
                        <tr>
                            <td><b>Profesor:</b>
                                <?php
                                echo($arrComisiones[0]['profesores']);
                                ?>
                            </td>
                            <td><b>Aula:</b>
                                <?php
                                echo $arrComisiones[0]['aula'] != null ? $arrComisiones[0]['aula'] : "-----";
                                ?>
                            </td>
                            <td><b>Horario:</b>
                                <?php
                                echo($arrComisiones[0]['horarios']);
                                ?>
                            </td>
                        </tr>
                    </table>
                </div>      
                <table id="table-alumnos" class="table table-striped table-hover table-condensed" >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Documento</th>
                            <th>Apellido y Nombre</th>
                            <th class="shorty">1er Cuatr.</th>
                            <th class="shorty">2do Cuatr.</th>
                            <th>Promedio</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $registrosporpagmin = 28;
                        $registrosporpagmax = 28;
                        $i = 0;
                        $j = 0;
                        $aprobados_primer_parcial = 0;
                        $aplazados_primer_parcial = 0;
                        $ausentes_primer_parcial = 0;
                        $aprobados_segundo_parcial = 0;
                        $aplazados_segundo_parcial = 0;
                        $ausentes_segundo_parcial = 0;
                        foreach ($alumnos as $rec) {
                            $j++; //Total
                            $i++;
                            if ($i >= $registrosporpagmin and $i <= $registrosporpagmax) {
                                //Salto de Pagina
                                //Vuelvo a hacer el header.
                                ?>
                            </tbody>
                        </table>
                    </article>
                </section>
                <section class="sheet padding-10mm">
                    <article>
                        <h5 style="text-align:left; font-size: 0.88em"> 
                                      <?php if($this->POROTO->Config['empresa_nombre']=='EMPA') {?>
      <img src="/images/bannerprovincia.jpg"  style="margin-top: -3px; width:30%; height:30%" />
    <?php }else{ ?>
      <img src="/images/banner_julianaguirre2.jpg"  style="margin-top: -3px; width:30%; height:30%" />
    <?php } ?>
                            <b>ACTA DE CURSADA &nbsp;&nbsp;&nbsp;Asignatura:&nbsp;</b>
                            <?php
                            echo($arrComisiones[0]['nombre']);
                            ?>
                        </h5>
                        <div>
                            <table  class="table table-striped table-hover table-condensed">
                                <tr>
                                    <td><b>Profesor:</b>
                                        <?php
                                        echo($arrComisiones[0]['profesores']);
                                        ?>
                                    </td>
                                    <td><b>Aula:</b>
                                        <?php
                                        echo $arrComisiones[0]['aula'] != null ? $arrComisiones[0]['aula'] : "-----";
                                        ?>
                                    </td>
                                    <td><b>Horario:</b>
                                        <?php
                                        echo($arrComisiones[0]['horarios']);
                                        ?>
                                    </td>
                                </tr>
                            </table>
                        </div>      
                        <table id="table-alumnos" class="table table-striped table-hover table-condensed" >
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Documento</th>
                                    <th>Apellido y Nombre</th>
                                    <th class="shorty">1er Cuatr.</th>
                                    <th class="shorty">2do Cuatr.</th>
                                    <th>Promedio</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>

                                <?php
                                $i = 1;
                            }
                            ?>
                            <tr>
                                <td><small><?php echo $j; ?></small></td>
                                <td><small><?php echo $rec['documentonro']; ?></small></td>
                                <td><small><?php echo $rec['apellido'] . " " . $rec['nombre']; ?></small></td>
                                <td class="notaFinal"><?php echo $rec['primerparcial']; ?></td>
                                <td class="notaFinal"><?php echo $rec['segundoparcial']; ?></td>
                                <td class="notaFinal">
                                    <?php
                                    $promedio = ($rec['primerparcial'] + $rec['segundoparcial']) / 2;
                                    echo $promedio == 0 ? "" : number_format((float)$promedio, 2, '.', '');
                                    ?>
                                </td>
                                <td></td>
                            </tr>

                            <?php
                            if ($rec['primerparcial'] == null) {
                                $ausentes_primer_parcial++;
                            } elseif ($rec['primerparcial'] >= 4) {
                                $aprobados_primer_parcial++;
                            } else {
                                $aplazados_primer_parcial++;
                            }
                            if ($rec['segundoparcial'] == null) {
                                $ausentes_segundo_parcial++;
                            } elseif ($rec['segundoparcial'] >= 4) {
                                $aprobados_segundo_parcial++;
                            } else {
                                $aplazados_segundo_parcial++;
                            }
                        }
                        ?>

                    </tbody>
                </table>


                <?php
                if ($i >= $registrosporpagmax - 6) {
                    ?>
                </article>
            </section>
            <section class="sheet padding-10mm">
                <article>
                    <h5 style="text-align:left; font-size: 0.88em"> 
                                  <?php if($this->POROTO->Config['empresa_nombre']=='EMPA') {?>
      <img src="/images/bannerprovincia.jpg"  style="margin-top: -3px; width:30%; height:30%" />
    <?php }else{ ?>
      <img src="/images/banner_julianaguirre2.jpg"  style="margin-top: -3px; width:30%; height:30%" />
    <?php } ?>
                        <b>ACTA DE CURSADA &nbsp;&nbsp;&nbsp;Asignatura:&nbsp;</b>
                        <?php
                        echo($arrComisiones[0]['nombre']);
                        ?>
                    </h5>
                    <?php
                }
                ?>
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th colspan="3"  class="text-center">1º Cuatrimestre</th>
                                </tr>
                            </thead>
                            <tr>
                                <td class="col-md-4">Aprobados</td>
                                <td class="col-md-4"><?php echo $aprobados_primer_parcial; ?></td>
                                <td class="col-md-4" rowspan="4" class="text-center" style="vertical-align:bottom">Firma y aclaracion del Profesor/a</td>
                            </tr>
                            <tr>
                                <td class="col-md-4">Aplazados</td>
                                <td class="col-md-4"><?php echo $aplazados_primer_parcial; ?></td>
                            </tr>
                            <tr>
                                <td class="col-md-4">Ausentes</td>
                                <td class="col-md-4"><?php echo $ausentes_primer_parcial; ?></td>
                            </tr>
                            <tr>
                                <td class="col-md-4">Fecha</td>
                                <td class="col-md-4"></td>
                            </tr>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered text-center">
                            <thead>
                                <tr>
                                    <th colspan="3"  class="text-center">2º Cuatrimestre</th>
                                </tr>
                            </thead>
                            <tr>
                                <td class="col-md-4">Aprobados</td>
                                <td class="col-md-4"><?php echo $aprobados_segundo_parcial; ?></td>
                                <td class="col-md-4" rowspan="4" class="text-center" style="vertical-align:bottom">Firma y aclaracion del Profesor/a</td>
                            </tr>
                            <tr>
                                <td class="col-md-4">Aplazados</td>
                                <td class="col-md-4"><?php echo $aplazados_segundo_parcial; ?></td>
                            </tr>
                            <tr>
                                <td class="col-md-4">Ausentes</td>
                                <td class="col-md-4"><?php echo $ausentes_segundo_parcial; ?></td>
                            </tr>
                            </tr>
                            <tr>
                                <td class="col-md-4">Fecha</td>
                                <td class="col-md-4"></td>
                            </tr>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </article>
        </section>

    </body>

</html>
