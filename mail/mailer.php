<?php
require("class.phpmailer.php");

function enviarMail($mensaje){

    $mail = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth = true; // True para que verifique autentificación de la cuenta o de lo contrario False
    $mail->Username = "grouptesluy@hotmail.com"; 
    $mail->Password = "Teslenk0"; // El Password de tu casilla de correos
    $mail->Host = "smtp.office365.com";
    $mail->SMTPSecure = "tls"; 
    $mail->Port = 587;
    $mail->From = "grouptesluy@hotmail.com";
    $mail->FromName = "mysqlmonitor";
    $mail->Subject = "ATENCION: informacion sobre respaldos";
    $mail->AddAddress("teslasapbe@gmail.com","Brahian Pena");
    $mail->WordWrap = 50; 
    $mail->Body = $mensaje;
    if($mail->Send()){
        return true;
    }else{
        return false;
    }

}

?>