<?php

if ( ! defined('POROTO')) exit('No direct script access allowed');


$db['default_connection'] = 'rentacar'; //Tanto PDO como la conexion comun usan esta conexion por defecto    

//DESARROLLO
//if ($_SERVER['HTTP_HOST'] == 'prox.consaguirre.com.ar') {
$db['rentacar']['database'] = "rentacar";
$db['rentacar']['hostname'] = "localhost";
$db['rentacar']['username'] = "rentacar";
$db['rentacar']['password'] = "123456";
$_SERVER['Entorno']="DESARROLLO";
//}