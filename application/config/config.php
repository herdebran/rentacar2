<?php

if ( ! defined('POROTO')) exit('No direct script access allowed');

$config['empresa_nombre'] = 'Rentacar';
$config['empresa_descripcion'] = 'Alquiler de vehiculos';
$config['empresa_color_titulos'] ='#1B1B1B';
$config['empresa_minilogo'] ='isotipo.png';
$config['empresa_color_header_desarrollo'] ='#1B1B1B';
$config['empresa_cooperadora'] = 'Definir nombre';
$config['empresa_cooperadora_datos']="Definir direccion y telefonos";
$config['path_to_mail_template'] = 'template_aguirre.html';
$config['path_to_mail_inscripcion'] = 'inscripcion_aguirre.txt';

//Variables para validaciones entre carrera e instrumento canto
$config['carrera_foba_canto'] = 2;
$config['instrumento_canto'] = 7;
$config['carrera_foba_instrumento'] = 1;
$config['carrera_imp'] = 0;
$config['carrera_pimp'] = 1;

$config['default_timezone'] = 'America/Argentina/Buenos_Aires';
$config['default_controller'] = 'novedades';
$config['default_controller_function'] = 'defentry';

$config['password_constraints_explained'] = 'La contraseña debe contener letras y/o números, y tener una longitud mínima de 6 caracteres';
$config['records_per_page'] = 30;

$config['rol_usuario_id'] = 1;
$config['rol_administrativo_id'] = 2; //AJUSTE-8NOV
$config['rol_directivo_id'] = 3;      //AJUSTE-8NOV
$config['rol_profesor_id'] = 4;

$config['session_minutes_alive'] = 20;


$config['mail_title_replace_string'] = '%%MAIL-TITLE%%';
$config['mail_body_replace_string'] = '%%MAIL-BODY%%';
$config['mail_nombre_replace_string'] = '%%NOMBRE%%';
$config['mail_documento_replace_string'] = '%%DOCUMENTO%%';
$config['mail_domicilio_replace_string'] = '%%DOMICILIO%%';
$config['mail_instrumento_replace_string'] = '%%INSTRUMENTO%%';
$config['mail_nivel_replace_string'] = '%%NIVEL%%';
$config['mail_carrera_replace_string'] = '%%CARRERA%%';

$config['dominios']['nacionalidad'] = array('ARGENTINA','BOLIVIANA', 'BRASILERA', 'CHILENA', 'COLOMBIANA', 'ECUATORIANA', 'PARAGUAYA','PERUANA','URUGUAYA','VENEZOLANA','RESTO DE AMERICA','EUROPA','ASIA','AFRICANA','OCEANIA');
$config['dominios']['estadocivil'] = array('SOLTERO/A', 'CASADO/A', 'DIVORICADO/A', 'VIUDO/A');
$config['dominios']['sexo'] = array('MASCULINO', 'FEMENINO');
$config['dominios']['turnos'] = array('MAÑANA', 'TARDE', 'VESPERTINO');
$config['dominios']['dias'] = array('LUNES', 'MARTES', 'MIÉRCOLES', 'JUEVES', 'VIERNES', 'SÁBADO', 'DOMINGO');

$config['dia_default_horario'] = "1975-03-26";

$config['show_user_role_at_menu'] = FALSE;
$config['show_poroto_look_and_feel'] = FALSE;
$config['css_poroto_look_and_feel'] = 'poroto';
$config['show_stats_at_foot'] = FALSE;
$config['controler_case_sensitive'] = FALSE;

$config['autoload_libraries'] = array('siteLibrary');
$config['log_sql_filename'] = 'sql-log-'.date('Ymd').'.txt';
$config['base_url'] = 'http://localhost/rentacar';
  