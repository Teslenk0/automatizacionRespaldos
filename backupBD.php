#!/usr/bin/php

<?php
require("mail/mailer.php");
require("SFTP/enviarArchivo.php");

//DATOS SERVIDOR MYSQL
$usuario = "root";
$pass = "123456";

//DATOS PARA REALIZAR CONEXION SFTP
/**
 * CAMBIAR ANTES DE UTILIZAR
 */
$usuarioSFTP = "tesla";
$passSFTP = "123456";
$host = "10.10.7.20";
$puerto = 22; 

$fecha = exec("date"); //saco la fecha del sistema

$ARCHIVO_LOG = "logs/backupBD.log"; //genero un archivo .log con el que trabajar

$file = fopen($ARCHIVO_LOG, "a"); //lo abro

$auxfile = "$fecha-backup.sql"; //genero el nombre del archivo del backup

/**
 * ALMACENO EN VARIABLES LA RUTA DONDE ESTARAN LOS ARCHIVOS
 * CAMBIAR ANTES DE UTILIZSAR
 */
$rutaOrigen = "/home/tesla/automatizacionRespaldos/backupsBD/$auxfile"; //en esta ruta esta el archivo localmente
$rutaDestino = "/home/tesla/backupsBD/$auxfile"; //ruta en la que estara remotamente


fwrite($file, "[$fecha]Importando base de datos en '$auxfile'" . PHP_EOL); //escribo en el .log

$respaldo = exec("mysqldump --user=$usuario --password=$pass --all-databases > 'backupsBD/$auxfile'",$output,$return); //saco el respaldo

If($return == 0){

	$fecha = exec("date"); //vuelvo a sacar la fecha
	fwrite($file, "[$fecha]Completado correctamente" . PHP_EOL); //escribo en el .log
	$body = "Se realizo el respaldo de las bases de datos correctamente."; //creo el mensaje para enviar por mail posteriormente
	
	try
	{
		$sftp = new SFTPconexion($host, $puerto); //instancio la conexion por sftp  (host,puerto)
		$sftp->login($usuarioSFTP, $passSFTP); //me logueo
		$sftp->subirArchivo($rutaOrigen, $rutaDestino); //mando el archivo
		fwrite($file, "[$fecha]Se ha enviado el archivo correctamente por SFTP" . PHP_EOL);
		$body = $body."\nSe ha enviado el archivo correctamente por SFTP.";
	}
	catch (Exception $e)
	{
		$excepcion = $e->getMessage(); 
		$fecha = exec("date");
		fwrite($file, "[$fecha]Problema al enviar el archivo por SFTP" . PHP_EOL);
		fwrite($file, "[$fecha].$excepcion" . PHP_EOL);
		$body = $body."\nHubo un problema al enviar el archivo por SFTP -> $excepcion.";
	}
}
else{
	$fecha = exec("date");
	fwrite($file, "[$fecha]Finalizo con errores" . PHP_EOL);
	$body = "No se pudo completar el respaldo de las bases de datos.";
}

$fecha = exec("date");
fwrite($file, "[$fecha]Enviando mail con resultado" . PHP_EOL); //escribo en el log
	
$fecha = exec("date");
if(enviarMail($body)){//mando el mail
	fwrite($file, "[$fecha]Envio completado" . PHP_EOL);
}else{
	fwrite($file, "[$fecha]No se pudo enviar el mail" . PHP_EOL);
}

?>
