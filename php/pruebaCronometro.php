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

session_start();

if (!isset($_SESSION['cronometro']))
    $_SESSION['cronometro'] = new Cronometro();

$cronometro = $_SESSION['cronometro'];

$mostrarTiempo = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['arrancar'])) {
        $cronometro->arrancar();
    } elseif (isset($_POST['parar'])) {
        $cronometro->parar();
    } elseif (isset($_POST['mostrar'])) {
        $mostrarTiempo = true;
    }
}

$tiempoMostrado = $mostrarTiempo ? $cronometro->mostrar() : '00:00.0';
?>

<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Alejandro Aldea Viana - UO293873" />
    <meta name="description" content="Cronómetro del proyecto MotoGP-Desktop" />
    <meta name="keywords" content="MotoGP, motociclismo, deportes, velocidad, cronómetro" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>MotoGP-Cronómetro</title>
    <link rel="icon" type="image/x-icon" href="multimedia/icono.png" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
    <header>
        <h1><a href="../index.html" title="Ir a la página principal">MotoGP Desktop</a></h1>
        <nav>
            <a href="../index.html" title="Página de inicio">Inicio</a>
            <a href="../php/piloto.html" title="Información del piloto">Piloto</a>
            <a href="../php/circuito.html" title="Información del circuito">Circuito</a>
            <a href="../php/meteorologia.html" title="Información sobre meteorología">Meteorología</a>
            <a href="../php/clasificaciones.html" title="Clasificaciones de MotoGP">Clasificaciones</a>
            <a href="../php/juegos.html" title="Juegos de MotoGP" class="active">Juegos</a>
            <a href="../php/ayuda.html" title="Ayuda de MotoGP">Ayuda</a>
        </nav>
    </header>

    <p>Estás en: <a href="../index.html">Inicio</a> &gt; <a href="../juegos.html">Juegos</a> &gt; <strong>Cronómetro</strong></p>

    <main>
        <section>
            <h2>Cronómetro</h2>
            
            <p>Tiempo transcurrido: <strong><?php echo $tiempoMostrado; ?></strong></p>
            
            <form method="post" action="pruebaCronometro.php">
                <button type="submit" name="arrancar" <?php echo $cronometro->estaEnMarcha() ? 'disabled' : ''; ?>>Arrancar</button>
                <button type="submit" name="parar" <?php echo !$cronometro->estaEnMarcha() ? 'disabled' : ''; ?>>Parar</button>
                <button type="submit" name="mostrar" <?php echo $cronometro->estaEnMarcha() ? 'disabled' : ''; ?>>Mostrar</button>
            </form>
        </section>
    </main>
</body>
</html>