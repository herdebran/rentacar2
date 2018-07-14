<?php
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
  <style>@page { size: A4 landscape }</style>
</head>
<body class="A4 landscape">
<section class="sheet padding-10mm">
<article>
<h5 style="text-align:left"> 
    <?php if($this->POROTO->Config['empresa_nombre']=='EMPA') {?>
      <img src="/images/bannerprovincia.jpg"  style="margin-top: -3px; width:30%; height:30%" />
    <?php }else{ ?>
      <img src="/images/banner_julianaguirre2.jpg"  style="margin-top: -3px; width:30%; height:30%" />
    <?php } ?>
    
<b>LISTADO DE ASISTENCIA &nbsp;&nbsp;&nbsp;Curso:&nbsp;</b><?php 
 echo($arrComisiones[0]['nombre']);
 ?></h5>
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
                <th colspan="32"></th>
                <th>Observaciones</th>
              </tr>
            </thead>
            <tbody>
              <?php 
			  $registrosporpagmin=18;
			  $registrosporpagmax=18;
			  $i=0;
			  $j=0;
			  foreach ($alumnos as $rec) { 
			  $j++; //Total
			  $i++;
if($i>=$registrosporpagmin and $i<=$registrosporpagmax){ //Salto de Pagina
//Vuelvo a hacer el header.
?>
</tbody>
</table></article>
</section>
<section class="sheet padding-10mm">
<article>
<h5 style="text-align:left"> 
        <?php if($this->POROTO->Config['empresa_nombre']=='EMPA') {?>
      <img src="/images/bannerprovincia.jpg"  style="margin-top: -3px; width:30%; height:30%" />
    <?php }else{ ?>
      <img src="/images/banner_julianaguirre2.jpg"  style="margin-top: -3px; width:30%; height:30%" />
    <?php } ?>
LISTADO DE ASISTENCIA &nbsp;&nbsp;&nbsp;<b>Curso:&nbsp;</b><?php 
echo($arrComisiones[0]['nombre']);
?></h5>
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
<th colspan="32"></th>
<th>Observaciones</th>
</tr>
</thead>
<tbody>

<?php
$i=1;
}
?>
              <tr>
                <td><small><?php echo $j; ?></small></td>
                <td><small><?php echo $rec['documentonro']; ?></small></td>
                <td><small><?php echo $rec['apellido'] . " " . $rec['nombre'] ; ?></small></td>
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td>
              </tr>
              <?php } ?>
                       
            </tbody>
          </table>
        <br>
      
	</article>
   </section>
   
</body>

</html>
