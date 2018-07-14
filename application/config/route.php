<?php 
/**
  * Site Routing Rules
  *
  * The $route array contains the rules. Just like codeigniter, the
  * (:any) wildcard could be used
  *
  * @package  poroto
  * @version  1.2
  * @access   public
  * @copyright 2015-2017 7dedos
  * @author Augusto Wloch <agosto@7dedos.com>
  */
if ( ! defined('POROTO')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Base Site URL
|--------------------------------------------------------------------------
|
| URL to your CodeIgniter root. Typically this will be your base URL,
| WITH a trailing slash:
|
|	http://example.com/
|
| If this is not set then CodeIgniter will guess the protocol, domain and
| path to your installation.
|
*/

$route['login'] = 'home/login';
$route['logout'] = 'home/logout';
$route['forgot'] = 'home/forgot';
$route['primeracceso'] = 'home/primeracceso';
$route['reempadronamiento'] = 'home/reempadronamiento';
$route['reportesexcel'] = 'reportes/reportesexcel';

$route['habilitar/(:any)/(:any)'] = 'seguridad/habilitar/$1/si/$2';
$route['deshabilitar/(:any)/(:any)'] = 'seguridad/habilitar/$1/no/$2';
$route['resetpassword/(:any)/(:any)'] = 'seguridad/resetpassword/$1/$2';
$route['inscripcion'] = 'home/inscripcion';
$route['pickrole'] = 'seguridad/pickrole';
$route['ver-materias'] = 'vermateria/vermaterias';
$route['imprimircomision/(:any)/(:any)'] = 'vermateria/imprimircomision/$1/$2';
$route['misdatos'] = 'home/misdatos';

$route['gestion-carreras'] = 'carreras/gestioncarreras';
$route['ajaxmateriascarrera/(:any)'] = 'carreras/ajaxmateriascarrera/$1';
$route['ajaxlocalidades/(:any)'] = 'home/ajaxlocalidades/$1';
$route['proceso1/(:any)'] = 'procesos/proceso1/$1';
$route['proceso2'] = 'procesos/proceso2';
$route['proceso3/(:any)'] = 'procesos/proceso3/$1';
$route['proceso4/(:any)/(:any)'] = 'procesos/proceso4/$1/$2';
$route['proceso5/(:any)/(:any)'] = 'procesos/proceso5/$1/$2';
$route['verprocesos'] = 'procesos/verprocesos';

$route['gestion-cooperadora'] = 'cooperadora/buscarpersonas';
$route['gestion-cooperadora/(:any)'] = 'cooperadora/detalle/$1';

$route['reportes-dinamicos'] = 'reportedinamico/reportes';
$route['gestion-permisos'] = 'permisos';
$route['crear-persona'] = 'permisos/crearpersona';
$route['gestion-fideicomisos'] = 'fideicomiso/gestion';
$route['crearfideicomiso'] = 'fideicomiso/crearfideicomiso';
$route['ajaxmunicipios/(:any)'] = 'fideicomiso/ajaxmunicipios/$1';
$route['ajaxlocalidades/(:any)'] = 'fideicomiso/ajaxlocalidades/$1';
$route['crearfideicomiso/(:any)'] = 'fideicomiso/crearfideicomiso/$1';
