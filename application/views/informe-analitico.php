<?php
$ses =& $this->POROTO->Session;
$message = $ses->getMessage();
$alertType = "alert-info";
switch ($message['type']) {
    case SessionMessageType::Success: $alertType = "alert-success"; break;
    case SessionMessageType::TransactionError: $alertType = "alert-danger"; break;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Informe Analitico</title>
  <!-- Normalize or reset CSS with your favorite library -->
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/3.0.3/normalize.css">
  <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.2.3/paper.css"> -->
   <link href="/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/css/normalize.css">
 
  <!-- Load paper.css for happy printing -->
  <link rel="stylesheet" href="/css/paper.css">
  <!-- Set page size here: A5, A4 or A3 -->
  <!-- Set also "landscape" if you need -->
  <style>@page { size: A4 }</style>
</head>
<body class="A4">
<section class="sheet padding-10mm">
<article>
    <?php if($this->POROTO->Config['empresa_nombre']=='EMPA') {?>
      <img src="/images/bannerprovincia.jpg"  style="margin-right: 20px; margin-top: -3px" />
    <?php }else{ ?>
      <img src="/images/banner_julianaguirre2.jpg"  style="margin-right: 20px; margin-top: -3px" />
    <?php } ?>
      <h4 style="text-align:center">CONSTANCIA DE ASIGNATURAS APROBADAS <br>
      <?php 
	  $carreraActual="";
	  $instrumentoActual="";
	  $areaActual="";
	  
	  foreach ($viewDataCarreras as $carrera) {
                        if ($dataIdAlumnoCarrera==$carrera['idalumnocarrera']) {
                        $carreraActual=$carrera['descripcion'];
			$instrumentoActual=$carrera['instrumento'];
			$areaActual=$carrera['area'];
                        $dataIdCarrera=$carrera['idcarrera'];
                        echo $carreraActual;
                        }
	} ?></h4>
    <br>
 <?php if($dataIdCarrera==1 || $dataIdCarrera==5){ //Si es FOBA muestro un cartel?>
Se deja constancia que <b><?php echo($nombreAlumno." ".$documentoAlumno);?></b> es alumno/a de este establecimiento en la <b><?php echo($carreraActual); ?></b>, Formación Pregrado para la carrera de <b>INSTRUMENTISTA EN MÚSICA POPULAR</b>. Instrumento  <b><?php echo($instrumentoActual); ?></b>. Resolución Nº <b>13.231/99</b> y ha acreditado las siguientes Asignaturas del plan de estudios, cuyo detalle figura al pie.
<?php } ?>

<?php if($dataIdCarrera==2){ //Si es IMP ?>
Se deja constancia que <b><?php echo($nombreAlumno." ".$documentoAlumno);?></b> es alumno/a de este establecimiento en la carrera <b><?php echo($carreraActual); ?></b>. Instrumento  <b><?php echo($instrumentoActual." ".$areaActual); ?></b>. Resolución Nº <b>13.235/99</b> y ha acreditado las siguientes Asignaturas del plan de estudios, cuyo detalle figura al pie.
<?php } ?>

 <?php if($dataIdCarrera==4){ //Si es PEM?>
Se deja constancia que <b><?php echo($nombreAlumno." ".$documentoAlumno);?></b> es alumno/a de este establecimiento en la carrera <b><?php echo($carreraActual); ?></b>. Instrumento  <b><?php echo($instrumentoActual); ?></b>. Resolución Nº <b>855/11</b> y ha acreditado las siguientes Asignaturas del plan de estudios, cuyo detalle figura al pie.
<?php } ?>

<?php if($dataIdCarrera==3){ //Si es PIMP ?>
Se deja constancia que <b><?php echo($nombreAlumno." ".$documentoAlumno);?></b> es alumno/a de este establecimiento en la carrera <b><?php echo($carreraActual); ?></b>. Instrumento  <b><?php echo($instrumentoActual." ".$areaActual); ?></b>. 
<?php 	if ($instrumentoActual!="CANTO") { ?>
<?php 	    if ($areaActual=="AREA TANGO") { ?>Resolución Nº <b>13.234/99 y 6188/03</b><?php } ?>
<?php 	    if ($areaActual=="AREA JAZZ") { ?>Resolución Nº <b>13.234/99 y 6187/03</b><?php } ?>
<?php 	    if ($areaActual=="AREA FOLKLORE") { ?>Resolución Nº <b>13.234/99 y 6186/03</b><?php } ?>
<?php 	    if ($areaActual=="") { ?>Resolución Nº <b>13.235/99</b><?php } ?>
<?php   }else{ //CANTO    ?>
<?php 	    if ($areaActual=="AREA TANGO") { ?>Resolución Nº <b>13.234/99 y 6193/03</b><?php } ?>
<?php 	    if ($areaActual=="AREA JAZZ") { ?>Resolución Nº <b>13.234/99 y 388/04</b><?php } ?>
<?php 	    if ($areaActual=="AREA FOLKLORE") { ?>Resolución Nº <b>13.234/99 y 6189/03</b><?php } ?>
<?php 	    if ($areaActual=="") { ?>Resolución Nº <b>13.235/99</b><?php } ?>
<?php   }     ?>
 y ha acreditado las siguientes Asignaturas del plan de estudios, cuyo detalle figura al pie.
<?php } ?>

<br/><br/>
       
          <table id="table-alumnos" class="table table-striped table-hover table-condensed" >
            <thead>
              <tr>
                <th>Año / Nivel</th>
                <th>Asignatura</th>
                <th>Nota</th>
                <th>Fecha Aprob.</th>
                <th>Libro/Folio</th>
              </tr>
            </thead>
            <tbody>
              <?php 
			  $registrosporpagmin=20;
			  $registrosporpagmax=24;
			  $i=0;
			  foreach ($viewData as $rec) { 
			  $i++;
			  if($i>=$registrosporpagmin and $i<=$registrosporpagmax){ //Salto de Pagina
					//Vuelvo a hacer el header.
					?>
					</tbody>
					</table></article>
					</section>
					<section class="sheet padding-10mm">
					<article>
                                            <?php if($this->POROTO->Config['empresa_nombre']=='EMPA') {?>
      <img src="/images/bannerprovincia.jpg"  style="margin-right: 20px; margin-top: -3px" />
    <?php }else{ ?>
      <img src="/images/banner_julianaguirre2.jpg"  style="margin-right: 20px; margin-top: -3px" />
    <?php } ?>
					<table id="table-alumnos" class="table table-striped table-hover table-condensed" >
								<thead>
								  <tr>
									<th>Año / Nivel</th>
									<th>Asignatura</th>
									<th>Nota</th>
									<th>Fecha Aprob.</th>
									<th>Libro/Folio</th>
								  </tr>
								</thead>
								<tbody>
					<?php
					$i=1;
				}
					?>
              <tr>
                <td><small><?php echo $rec['anio']; ?></small></td>
                <td><small><?php echo $rec['nombrecorto']; ?></small></td>
                <td><small><?php echo $rec['notaexamen']; ?></small></td>
                <td><small>
				<?php if($rec['fechaaprobacion']!="") echo $rec['fechaaprobacion']; else echo($rec['estado']); ?>
                </small></td>
                <td><small><?php echo $rec['libro'].$rec['folio']; ?></small></td>
              </tr>
              <?php } ?>
                       
            </tbody>
          </table>
        <br>
        A pedido del interesado y al solo efecto de ser presentado ante ..........................................................................<br>se extiende la presente constancia, en la ciudad de Avellaneda a los ....... días del mes ............. de dos mil .........
<br><br><br>

<table cellspacing="0" cellpadding="0" border="0"><tr><td>.............................</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;.............................</tr>
<tr><td>Sello del establecimiento</td><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Director</tr></table>

	</article>
   </section>
   
</body>

</html>
