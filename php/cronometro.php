<?php
class Cronometro {
    private $tiempo;
    private $inicio;

    public function __construct() {
        $this->tiempo = 0;
    }

    public function arrancar() {
        if (!isset($this->inicio)) {
            $this->inicio = microtime(true);
        }
    }

    public function parar() {
        if (isset($this->inicio)) {
            $this->tiempo += microtime(true) - $this->inicio;
            unset($this->inicio);
        }
    }

    public function mostrar() {
        $tiempoTotal = $this->tiempo;
        
        $minutos = floor($tiempoTotal / 60);
        $segundos = floor($tiempoTotal % 60);
        $decimas = floor(($tiempoTotal - floor($tiempoTotal)) * 10);
        
        return sprintf("%02d:%02d.%d", $minutos, $segundos, $decimas);
    }

    public function estaEnMarcha() {
        return isset($this->inicio);
    }
}

?>