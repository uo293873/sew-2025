<?php
class Clasificacion {
    private $documento;

    public function __construct() {
        $this->documento = "xml/circuitoEsquema.xml";
    }

    public function consultar() {
        // Obtiene el archivo XML
        $datos = file_get_contents($this->documento);
        if($datos == null) {
            echo "<h3>Error en el archivo XML recibido</h3>";
            return;
        }

        // Se convierte el string en un objeto XML
        $xml = new SimpleXMLElement($datos);

        // Informacion general del circuito
        echo "<section>";
        echo "<h3>Información del Circuito</h3>";
        echo "<p>Nombre: {$xml->nombre}</p>";
        echo "<p>Localidad: {$xml->localidad}, {$xml->pais}</p>";
        echo "<p>Longitud: {$xml->longitud} {$xml->longitud['unidades']}</p>";
        echo "<p>Anchura: {$xml->anchura} {$xml->anchura['unidades']}</p>";
        echo "<p>Número de vueltas: {$xml->numeroVueltas}</p>";
        echo "<p>Fecha: {$xml->fecha}</p>";
        echo "<p>Hora de inicio: {$xml->horaInicio}</p>";
        echo "</section>";

        // Vencedor de la carrera
        echo "<section>";
        echo "<h3>Vencedor de la Carrera</h3>";
        echo "<p>Piloto: {$xml->vencedor->nombrePiloto}</p>";
        echo "<p>Tiempo: " . $this->formatearTiempo($xml->vencedor->tiempoCarrera) . "</p>";
        echo "</section>";

        // Clasificacion Mundial
        echo "<section>";
        echo "<h3>Clasificación Mundial</h3>";
        echo "<table>";
        echo "<caption>Clasificación del Mundial de MotoGP tras la carrera de Sepang</caption>";
        echo "<thead><tr><th scope='col' id='posicion'>Posición</th><th scope='col' id='piloto'>Piloto</th><th scope='col' id='puntos'>Puntos</th></tr></thead>";
        echo "<tbody>";
        
        foreach ($xml->clasificacionMundial->pilotoClasificado as $piloto) {
            echo "<tr>";
            echo "<td headers='posicion'>{$piloto['posicion']}</td>";
            echo "<td headers='piloto'>{$piloto->nombrePilotoClasificado}</td>";
            echo "<td headers='puntos'>{$piloto->puntosPiloto}</td>";
            echo "</tr>";
        }
        
        echo "</tbody>";
        echo "</table>";
        echo "</section>";
    }

    private function formatearTiempo($tiempoISO) {
        $tiempo = str_replace('PT', '', $tiempoISO);
        $tiempo = str_replace('M', 'm ', $tiempo);
        $tiempo = str_replace('S', 's', $tiempo);
        return $tiempo;
    }
}

$clasificacion = new Clasificacion();
?>
<!DOCTYPE HTML>

<html lang="es">
<head>
    <!-- Datos que describen el documento -->
    <meta charset="UTF-8" />
    <meta name ="author" content ="Alejandro Aldea Viana - UO293873" />
    <meta name ="description" content ="Información de las clasificaciones del proyecto MotoGP-Desktop" />
    <meta name ="keywords" content ="MotoGP, motociclismo, deportes, velocidad" />
    <meta name ="viewport" content ="width=device-width, initial-scale=1.0" />
    <title>MotoGP-Clasificaciones</title>
    <link rel="icon" type="image/x-icon" href="multimedia/icono.png" />
    <link rel="stylesheet" type="text/css" href="estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="estilo/layout.css" />
</head>

<body>
    <header>
        <h1><a href="index.html" title="Ir a la página principal">MotoGP Desktop</a></h1>
        <nav>
            <a href="index.html" title="Página de inicio">Inicio</a>
            <a href="piloto.html" title="Información del piloto">Piloto</a>
            <a href="circuito.html" title="Información del circuito">Circuito</a>
            <a href="meteorologia.html" title="Información sobre meteorología">Meteorología</a>
            <a href="clasificaciones.php" title="Clasificaciones de MotoGP" class="active">Clasificaciones</a>
            <a href="juegos.html" title="Juegos de MotoGP">Juegos</a>
            <a href="ayuda.html" title="Ayuda de MotoGP">Ayuda</a>
        </nav>
    </header>

    <p>Estás en: <a href="index.html">Inicio</a> &gt; <strong>Clasificaciones</strong></p>

    <main>
        <?php $clasificacion->consultar(); ?>
    </main>
</body>
</html>