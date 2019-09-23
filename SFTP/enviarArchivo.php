<?php

class SFTPconexion
{
    private $conexion;
    private $sftp;

    public function __construct($host, $port=22) //constructor, seteo el puerto por defecto -> 22
    {
        $this->conexion = @ssh2_connect($host, $port);
        if (! $this->conexion)
            throw new Exception("No se puede conectar a $host:$port");
    }

    public function login($username, $password)
    {
        if (! @ssh2_auth_password($this->conexion, $username, $password))
            throw new Exception("No se pudo autenticar con el usuario: $username " .
                                "y la contrasenia $password.");

        $this->sftp = @ssh2_sftp($this->conexion);
        if (! $this->sftp)
            throw new Exception("No se pudo inicializar la conexion SFTP");
    }

    public function subirArchivo($archivo_local, $archivo_remoto)
    {
        $sftp = $this->sftp;
        $cadConexion = @fopen("ssh2.sftp://$sftp$archivo_remoto", 'w');

        if (! $cadConexion)
            throw new Exception("No se pudo abrir el archivo: $archivo_remoto");

        $datos_a_mandar = @file_get_contents($archivo_local);
        if ($datos_a_mandar === false)
            throw new Exception("No se pudo abrir el archivo local: $archivo_local.");

        if (@fwrite($cadConexion, $datos_a_mandar) === false)
            throw new Exception("No se pudo mandar el siguiente archivo: $archivo_local.");

        @fclose($cadConexion);
    }
}

?>