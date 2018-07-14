<?php

use \PHPMailer\PHPMailer\PHPMailer;
use \PHPMailer\PHPMailer\Exception;

class Fideicomiso extends Controller {

    public function __construct($poroto) {
        parent::__construct($poroto);
        include($this->POROTO->LibrariesPath . 'fpdf181/fpdf.php');
        include($this->POROTO->LibrariesPath . 'vendor/autoload.php');
        include($this->POROTO->ModelPath . '/cooperadora.php');
    }

    function getMontoCuotaActual() {
        return $this->ses->getParametroConfiguracion("montoCuotaCooperadora");
    }

    function buscarPersonas() {
        if (!$this->ses->tienePermiso('', 'Gestión de Cooperadora - Acceso desde Menu')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }
        // ---------------------- Logica del metodo ----------------------------

        $cooperadora = new ModeloCooperadora($this->POROTO);
        $params = array();
        $params['pageTitle'] = "Busqueda de personas";

        $params['viewDataTipoDocumento'] = $this->app->getAllTipoDocumento();
        $params['viewDataCarreras'] = $this->app->getAllCarreras();
        $params["instrumentos"] = $this->app->getAllInstrumentos();
        $params["roles"] = $this->app->getAllRoles();
        $params["estadoscooperadora"] = $cooperadora->getAllEstadosCooperadora();
        $params["cuotas"] = $cooperadora->getAllCuotas();
        // ---------------------- Fin logica del metodo ------------------------
        $this->render("/cooperadora-busqueda.php", $params);
    }

    function historialIngresos() {
        if (!$this->ses->tienePermiso('', 'Gestión de Cooperadora - Ingresos')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }
        // ---------------------- Logica del metodo ----------------------------
        $cooperadora = new ModeloCooperadora($this->POROTO);
        $params = array();
        $params['pageTitle'] = "Busqueda de personas";

        $params['viewDataTipoDocumento'] = $this->app->getAllTipoDocumento();
        $params['viewDataCarreras'] = $this->app->getAllCarreras();
        $params["instrumentos"] = $this->app->getAllInstrumentos();
        $params["roles"] = $this->app->getAllRoles();
        $params["estadoscooperadora"] = $cooperadora->getAllEstadosCooperadora();
        $params["cuotas"] = $cooperadora->getAllCuotas();
        // ---------------------- Fin logica del metodo ------------------------
        $this->render("/cooperadora-ingresos.php", $params);
    }

    public function ajaxBuscarOperaciones() {
        $lib = & $this->POROTO->Libraries['siteLibrary'];
        $res = array();
        $res["error"] = 0;
        if (!$this->ses->tienePermiso('', 'Gestión de Cooperadora - Ingresos')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            $res["error"] = 1;
            $res['data'] = "El usuario no tiene permiso para entrar a esta seccion del sistema.";
        } else {
            // ---------------------- Logica del metodo ----------------------------
            $filtros = $_POST['filtros'];
            // TODO: El "if" debe llamar una fucion que valide que fechadesde y fechahasta sean validas y no se superpongan.
            if (false) {
                $res["error"] = 1;
                $res['data'] = "fecha " . $filtros['fechadesde'] . " resultado " . $lib->validateDate($filtros['fechadesde']) . "Fecha Invalida.";
            } else {
                $cooperadora = new ModeloCooperadora($this->POROTO);
                $filtros['fechadesde'] = $lib->dateDMY2YMD($filtros['fechadesde']);
                $filtros['fechahasta'] = $lib->dateDMY2YMD($filtros['fechahasta']);
                $res['data'] = $cooperadora->getOperaciones($filtros);
            }
        }
        echo json_encode($res);
        // ---------------------- Fin logica del metodo ------------------------
    }

    public function personasConFiltro($filter = null) {
        include($this->POROTO->ModelPath . '/persona.php');
        $persona = new Persona($this->POROTO);
        if (!$filter) {
            $filter = $_POST['filtros'];
        }
        $personas = $persona->getPersonasCooperadora($filter);
        $json = array("data" => $personas);

        echo json_encode($json);
    }

    public function detalle($idpersona) {
        if (!$this->ses->tienePermiso('', 'Gestión de Cooperadora - Acceso desde Menu')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }
        // ---------------------- Logica del metodo ----------------------------
        $params = array();
        $cooperadora = new ModeloCooperadora($this->POROTO);
        $filtros = array("idpersona" => $idpersona, "anio" => date("Y"));
        //$params["cuotas"] = $cooperadora->getCooperadoraCuotasAño($filtros);
        $params["idpersona"] = $idpersona;
        $params["persona"] = $cooperadora->getCooperadoraPersonaDatos($filtros);
        $params["cuotas"] = $cooperadora->getCooperadoraCuotasAño($filtros);
        $params["operaciones"] = $cooperadora->getOperacionesPersona($filtros);
        $params["aniosCuotas"] = $cooperadora->getAñosDeCuotas();
        //$params["valorCuota"] = $cooperadora->getMontoCuotaActual()["valor"];
        $params["valorCuota"] = $this->getMontoCuotaActual();
        // ---------------------- Fin logica del metodo ------------------------
        $this->render("/cooperadora-detalle.php", $params);
    }

    public function cuotasPersonaDeAnio() {

        $cooperadora = new ModeloCooperadora($this->POROTO);
        $anio = $_POST['anioCuota'];
        $idperosna = $_POST["idpersona"];
        $filtros = array("idpersona" => $idperosna, "anio" => $anio);
        $cuotas = $cooperadora->getCooperadoraCuotasAño($filtros);
        foreach ($cuotas as $i => $cuota) {
            if ($cuota["fechapago"] != null) {
                $cuotas[$i]["fechapago"] = date('d/m/Y', strtotime($cuota["fechapago"]));
            }
        }

        echo json_encode($cuotas);
    }

    public function ajaxBuscarPersona() {
        $filtros = $_POST['filtros'];
        include($this->POROTO->ModelPath . '/persona.php');
        $persona = new Persona($this->POROTO);
        $res = array();
        $res["recordsTotal"] = $res["recordsFiltered"] = $res['data'] = $persona->getAllWithFilter($filtros);
        echo json_encode($res);
    }

    public function nuevaOperacion() {
        if (!$this->ses->tienePermiso('', 'Gestión de Cooperadora - Cobrador')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }
        $cooperadora = new ModeloCooperadora($this->POROTO);
        $valores = array();
        //$monto = $cooperadora->getMontoCuotaActual()["valor"];
        $monto = $this->getMontoCuotaActual();

        $montoTotal = $monto * (sizeof($_POST["cuotas"]));
        $valores["fecha"] = date("Y-m-d H:i:s");
        $valores["idpersona"] = $_POST["idpersona"];
        $valores["montototal"] = $montoTotal;
        $valores["monto"] = $monto;
        $valores["cuotas"] = $_POST["cuotas"];
        $valores["usuario"] = $this->ses->getUsuario();

        $response = $cooperadora->nuevaOperacion($valores);

        // mando el email con el comprobante ni bien se ejecuta el pago
        $response["message"] .= $this->generarpdf($response["idope"], true);

        if ($response["ok"] == true) {
            $this->ses->setMessage($response["message"], SessionMessageType::Success);
        } else {
            $this->ses->setMessage($response["message"], SessionMessageType::TransactionError);
        }
        echo json_encode($response);
    }

    public function anularOperacion() {
        if (!$this->ses->tienePermiso('', 'Gestión de Cooperadora - Cobrador')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }
        // ---------------------- Logica del metodo ----------------------------
        $idOpe = $_POST["idoperacion"];
        $idpersona = $_POST["idpersona"];
        $params = array();
        $cooperadora = new ModeloCooperadora($this->POROTO);
        $filtros = array("idoperacion" => $idOpe);
        $anulado = $cooperadora->eliminarOperacion($filtros);
        if ($anulado) {
            $this->ses->setMessage("La operacion se anulo exitosamente.", SessionMessageType::Success);
        } else {
            $this->ses->setMessage("Ocurrio un error al enular la operacion.", SessionMessageType::TransactionError);
        }
        $response = array("ok" => $anulado);
// ---------------------- Fin logica del metodo ------------------------
        echo json_encode($response);
    }

    public function ajaxsendmail() {
        $lib = & $this->POROTO->Libraries['siteLibrary'];
        $idPersona = $_POST["personaId"];
        $idRol = $_POST["rol"];

        $mailto = $_POST["sendto"];

        if (strlen($_POST["sentoadd"]) > 0) {
            $mailto .= "," . $_POST["sentoadd"];
        }

        if ($this->POROTO->Config["override_mail_address"] != "") {
            $mailto = $this->POROTO->Config["override_mail_address"];
        }

        $mailSubject = $this->POROTO->Config["empresa_descripcion"].' - Novedades de Cooperadora';
        $mailBody = $_POST["body"];
        $mailto = $mailto . "," . $_POST["sentoadd"];
        try {
            $lib->sendMail($mailto, $mailSubject, $mailBody);
            $response = array("msj" => "Correo enviado");
            echo json_encode($response);
        } catch (Exception $e) {
            $response = array("msj" => "Error al enviar el correo: " . $e);
            echo json_encode($response);
        }
    }

    public function sendEmail($idOpe, $email) {
        $mail = new PHPMailer(true);                              // Passing `true` enables exceptions
        try {
            //Server settings
            // $mail->SMTPDebug = 2;                                 // Enable verbose debug output
            $mail->isSMTP();
            $mail->CharSet = "utf-8";// Set mailer to use SMTP
            $mail->Host = $this->POROTO->Config["smtp_server_phpmailer"];  // mail.empa.edu.ar
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = $this->POROTO->Config["smtp_mail_from"];                // sistema@empa.edu.ar
            $mail->Password = $this->POROTO->Config["smtp_mail_password"];             // SMTP password
            $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
            $mail->Port = 587;                                    // TCP port to connect to
            //Recipients
            $mail->FromName = $this->POROTO->Config["smtp_mail_from_name"];
            $mail->From     = $this->POROTO->Config["smtp_mail_from"]; 

            if ($this->POROTO->Config["override_mail_address"] != "") {
                $email = $this->POROTO->Config["override_mail_address"];
            }
            $mail->addAddress($email);     // Add a recipient
            //Attachments
            $mail->addAttachment("./../temp/recibo-" . $idOpe . ".pdf");         // Add attachments
            //Content
            //  $mail->isHTML(true);                                  // Set email format to HTML
            $mail->Subject = $this->POROTO->Config["empresa_descripcion"].' - Recibo de Cooperadora';
            $mail->Body = 'Se adjunta el recibo por el pago de la/s cuotas de cooperadora. música';
            $mail->AltBody = '';

            $mail->send();
            return "El recibo se envio al email: " . $email . ".";
        } catch (Exception $e) {
            return 'No se ha podido enviar el email. Mailer Error: ' . $mail->ErrorInfo;
        }
    }

    public function generarpdf($idOpe = 0, $email = false) {

        if (!$this->ses->tienePermiso('', 'Gestión de Cooperadora - Cobrador')) {
            $this->ses->setMessage("Acceso denegado. Contactese con el administrador.", SessionMessageType::TransactionError);
            header("Location: /", TRUE, 302);
            exit();
        }
        if(isset($_POST["params"])){
            $idOpe = $_POST["params"]["idoperacion"];
            $email = $_POST["params"]["email"];
        }
        //obtengo datos de la operacion.
        $cooperadora = new ModeloCooperadora($this->POROTO);
        $operacion = $cooperadora->getOpeById($idOpe);
        if ($operacion) {
            //obtengo datos de la persona.
            include($this->POROTO->ModelPath . '/persona.php');
            $persona = new Persona($this->POROTO);
            $pdata = $persona->getPersonaById($operacion["idpersona"]);
            if ($email) {
                $this->pdf($pdata, $operacion, "F");
                $result = $this->sendEmail($idOpe, $pdata["email"]);
                unlink("./../temp/recibo-" . $idOpe . ".pdf");
                return $result;
            } else {
                $this->pdf($pdata, $operacion, "I");
                exit();
            }
        } else {
            $this->ses->setMessage("La operación solicitada no existe.", SessionMessageType::TransactionError);
            header("Location: /gestion-cooperadora", TRUE, 302);
        }
    }

    private function pdf($pdata, $operacion, $dest) {
        $nombreyape = iconv('UTF-8', 'windows-1252', $pdata["apeynom"]);
        $monto = $operacion["montototal"];
        $fecha = date("d/m/Y");
        $tipoynumdoc = $pdata["tipodocynro"];
        $cuotas = $operacion["cuotas"];

        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $str = iconv('UTF-8', 'windows-1252', 'Recibo Nº: ' . sprintf("%'.08d\n", $operacion["idoperacion"]));
        $pdf->Cell(150, 10, $str);
        $pdf->Cell(40, 10, 'Fecha: ' . $fecha, 0, 1);
        $pdf->Cell(40, 10, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, $this->POROTO->Config["empresa_cooperadora"], 0, 1, 'C');
        $pdf->SetFont('Arial', '', 14);
        $pdf->Cell(0, 10, $this->POROTO->Config["empresa_cooperadora_datos"], 0, 1, 'C');
        $pdf->Cell(40, 10, '', 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(30, 10, "Recibimos de ", 0, 0);

        $str = $nombreyape . ", " . $tipoynumdoc;
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, $str, 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $str = "En concepto del pago de la/s cuotas: ";
        $pdf->Cell(80, 10, $str, 0, 0);

        $pdf->SetFont('Arial', '', 12);
        $pdf->MultiCell(120, 10, $cuotas, 0, 1);

        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(150, 10, "La Cantidad de $", 0, 0, "R");

        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(15, 10, $monto, 1, 1, "C");


        $pdf->Cell(40, 10, '', 0, 1);
        $pdf->Cell(30, 10, '');
        $pdf->Cell(85, 0, '................');
        $pdf->Cell(40, 0, '.......................', 0, 1);

        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(35, 10, '');
        $pdf->Cell(85, 10, 'Firma');
        $pdf->Cell(40, 10, 'Aclaracion');
        $pdf->Output($dest, "./../temp/recibo-" . $operacion["idoperacion"] . ".pdf");
    }

}
