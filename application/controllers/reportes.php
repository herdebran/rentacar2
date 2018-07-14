<?php
class Reportes {
	private $POROTO;
	function __construct($poroto) {
		$this->POROTO=$poroto;
		$this->POROTO->pageHeader[] = array("label"=>"Dashboard","url"=>"");		
	}

	function defentry() {
		if ($this->POROTO->Session->isLogged()) { 		
			header("Location: /", TRUE, 302);
		} else {
			include($this->POROTO->ViewPath . "/-login.php");
		}
	}

	/*
        public function reportesexcel(){
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Visualizar Informes')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		header("Location: http://www.empa.edu.ar/Informes/Informesv1.xlsm", TRUE, 302);
	
	}
        */
        
	public function socios($filtro="NOSOCIO", $pagina=1) {
		$db =& $this->POROTO->DB;
		$ses =& $this->POROTO->Session;
		$lib =& $this->POROTO->Libraries['siteLibrary'];

		//Cambio 38 Leo 20170706
		if(!$ses->tienePermiso('','Reporte de Socios')){
				$ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
				header("Location: /", TRUE, 302);
				exit();
		}
		//Fin Cambio 38 Leo 20170706
		
		$db->dbConnect("reportes/socios/" . $filtro);
		$dataFiltro = $db->dbEscape($filtro);
		
		$arrMenu = $this->POROTO->DB->getSQLArray($this->POROTO->getMenuSqlQuery());	

		$sql = "select SQL_CALC_FOUND_ROWS p.apellido, p.nombre, td.descripcion, p.documentonro, u.email, c.nombre carrera, date_format(p.ultimopagocooperadora, '%d/%m/%Y') ultimopagocooperadora";
		$sql.= " from personas p inner join tipodoc td on p.tipodoc=td.id"; 
		$sql.= " inner join usuarios u on u.idpersona=p.idpersona and u.estado=1"; 
		$sql.= " inner join alumnocarrera ac on ac.idpersona=p.idpersona"; 
		$sql.= " inner join carreras c on ac.idcarrera=c.idcarrera"; 

		switch (strtoupper($dataFiltro)) {
			case 'ADHERENTE':
				$sql.= " where p.estado=1 and p.socio='ADHERENTE'";
				break;
			case 'SOCIO':
				$sql.= " where p.estado=1 and p.socio='ACTIVO'";
				break;
			default:
				$sql.= " where p.estado=1 and (p.socio is null or p.socio='NO-SOCIO')";
				break;
		}

		$sql.= " ORDER BY p.apellido, p.nombre"; 
		$sql.= " LIMIT " . (($pagina - 1) * $this->POROTO->Config['records_per_page']) . "," . $this->POROTO->Config['records_per_page']; 
		$viewData = $db->getSQLArray($sql);

		$sql = "SELECT FOUND_ROWS() q";
		$arrQ = $db->getSQLArray($sql);
		$qRec = $arrQ[0]['q'];
		$totPg = ceil($qRec / $this->POROTO->Config['records_per_page']);

		$db->dbDisconnect();

		$pageTitle="Reporte de Socios - " . $dataFiltro;
		include($this->POROTO->ViewPath . "/-header.php");
		include($this->POROTO->ViewPath . "/reporte-socios.php");
		include($this->POROTO->ViewPath . "/-footer.php");
	}

}