<?php
//    
//    Copyright (C) José Mario López Leiva. marioleiva2011@gmail.com_addre
//    September 2017. San Salvador (El Salvador)
//
//    This program is free software; you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation; either version 2 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program; if not, write to the Free Software
//    Foundation, Inc., 675 Mass Ave, Cambridge, MA 02139, USA.

include("../inc/inc.Settings.php");
include("../inc/inc.Language.php");
include("../inc/inc.Init.php");
include("../inc/inc.Extension.php");
include("../inc/inc.DBInit.php");
include("../inc/inc.ClassUI.php");
include("../inc/inc.Authentication.php");

/////////////////////////////////
//1. crearPersona. crea una persona. devuelve el id de la BD de la persona.
 function crearPersona($nombre,$entidad,$cargo,$sexo,$dms)
	 {
	 	$res=true;
		$db = $dms->getDB();
		//formateo sexo
		$sexito="M"; //mujer por defecto
		if(strcmp($sexo, 'H')==0)
		{
			$sexito="H";
		}
		$insertar = "INSERT INTO app_invitado VALUES(NULL,'$nombre','$entidad','$cargo', '$sexito')";
		//echo "INSERTAR persona: ".$insertar;
		$res1 = $db->getResult($insertar);
		if (!$res1)
		{
			$res=false;
		}
		$idCreado=$db->getInsertID();
		return $idCreado;
	 }

	 ///////////////////

function crearGrupos($idPersona,$idGrupo,$dms)
	 {
	 	$res=true;
		$db = $dms->getDB();
		$insertar = "INSERT INTO app_agrupamiento VALUES(NULL,$idPersona,$idGrupo)";
		//echo "INSERTAR crear grupos: ".$insertar;
		$res1 = $db->getResult($insertar);
		if (!$res1)
		{
			$res=false;
		}
		return $res;
	 }

///////////////////////////
	 function crearCorreos($idPersona,$correo,$dms)
	 {
	 	$res=true;
		$db = $dms->getDB();
		$insertar = "INSERT INTO app_correos VALUES(NULL,$idPersona,'$correo')";
		//echo "INSERTAR correo: ".$insertar;
		$res1 = $db->getResult($insertar);
		if (!$res1)
		{
			$res=false;
		}
		return $res;
	 }
////////
	 /*
	 	PARA GUARDAR EL HISTORIAL DE ACCIONES SOBRE PERSONA
	 	-idUsuario: el id del usuario que hace la acción.
	 	-idPersona: perfil de la persona de la bd sobre q se hace accion
	 	-cambio: texto con acción que se hace


	 */
	 function insertarHistorial($idUsuario,$idPersona,$cambio,$dms)
	 {

	 	$res=true;
		$db = $dms->getDB();
		$insertar = "INSERT INTO app_historial VALUES(NULL,$idUsuario,NOW(),'$cambio',$idPersona)";
		//echo "INSERTAR historial: ".$insertar;
		$res1 = $db->getResult($insertar);
		if (!$res1)
		{
			$res=false;
		}
		return $res;

	 }
//tabla seeddms.tblattributedefinitions;
 //generan
if ($user->isGuest())
 {
	UI::exitError(getMLText("no_permitido"),getMLText("access_denied"));
}

// Check to see if the user wants to see only those documents that are still
// in the review / approve stages.
$showInProcess = false;
if (isset($_GET["inProcess"]) && strlen($_GET["inProcess"])>0 && $_GET["inProcess"]!=0) {
	$showInProcess = true;
}

$orderby='n';
if (isset($_GET["orderby"]) && strlen($_GET["orderby"])==1 ) {
	$orderby=$_GET["orderby"];
}

$tmp = explode('.', basename($_SERVER['SCRIPT_FILENAME']));
$view = UI::factory($theme, $tmp[1], array('dms'=>$dms, 'user'=>$user));

//---------PESTAÑA 1: DATOS GENERALES:
$nombrePersona="";
$entidad="";
$cargo="";
$idGrupo="";
$sexo="";
$correo1="";
$correo2="";
$correo3="";
if (isset($_POST["nombrePersona"])) 
{
    $nombrePersona=$_POST["nombrePersona"]; 
}
if (isset($_POST["entidad"])) 
{
    $entidad=$_POST["entidad"]; 
}
if (isset($_POST["cargo"])) 
{
    $cargo=$_POST["cargo"]; 
}
if (isset($_POST["idGrupo"])) 
{
    $idGrupo=$_POST["idGrupo"]; 
}
if (isset($_POST["sexo"])) 
{
    $sexo=$_POST["sexo"]; 
}

////////hago metida en BD
//PRIMERA PARTE: CREAR LA PROPIA PERSONA
$idPersona=crearPersona($nombrePersona,$entidad,$cargo,$sexo,$dms);
//echo "id persona: ".$idPersona;
// SEGUNDA PARTE: CREAR SU AGRUPAMIENTO
crearGrupos($idPersona,$idGrupo,$dms);
//TERCERA PARTE: CORREOS
if (isset($_POST["correo1"])) 
{
    $correo1=$_POST["correo1"]; 
    crearCorreos($idPersona,$correo1,$dms);
}
if (isset($_POST["correo2"])) 
{
    $correo2=$_POST["correo2"];
    crearCorreos($idPersona,$correo2,$dms); 
}
if (isset($_POST["correo3"])) 
{
    $correo3=$_POST["correo3"];
    crearCorreos($idPersona,$correo3,$dms);  
}
//CUARTA PARTE: HISTORIAL 
insertarHistorial($user->getID(),$idPersona,"Se ingresó por primera vez los datos de la persona a la base de datos",$dms);

if($view) 
{
	$view->setParam('orderby', $orderby);
	$view->setParam('showinprocess', $showInProcess);
	$view->setParam('workflowmode', $settings->_workflowMode);
	$view->setParam('cachedir', $settings->_cacheDir);
	$view->setParam('previewWidthList', $settings->_previewWidthList);
	$view->setParam('timeout', $settings->_cmdTimeout);
	//PARAMS A MOSTRAR DE LA CREACIÓN
	$view->setParam('nombrePersona', $nombrePersona);
	$view->setParam('entidad', $entidad);
	$view->setParam('cargo', $cargo);
	$view->setParam('sexo', $sexo);
	$view->setParam('correo1', $correo1);
	$view->setParam('correo2', $correo2);
	$view->setParam('correo3', $correo3);
	$view->setParam('idGrupo', $idGrupo);
	$view($_GET);
	exit;
}
?>
