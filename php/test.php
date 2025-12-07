<?php
// Cargar la clase Cronometro sin mostrar su salida HTML
ob_start();
require_once '../cronometro.php';
ob_end_clean();

// Estados: 0 = inicio, 1 = datos personales, 2 = preguntas iniciadas, 3 = completada
if (!isset($_SESSION['estado'])) {
    $_SESSION['estado'] = 0;
}

// Procesar datos personales y empezar prueba
if (isset($_POST['empezar'])) {
    $_SESSION['estado'] = 1;
    $_SESSION['profesion'] = $_POST['profesion'];
    $_SESSION['edad'] = $_POST['edad'];
    $_SESSION['genero'] = $_POST['genero'];
    $_SESSION['pericia'] = $_POST['pericia'];
    $_SESSION['dispositivo'] = $_POST['dispositivo'];
    
    // Crear cronómetro y arrancarlo
    if (!isset($_SESSION['test_cronometro'])) {
        $_SESSION['test_cronometro'] = new Cronometro();
    }
    $_SESSION['test_cronometro']->arrancar();
}

// Procesar finalización de prueba
if (isset($_POST['terminar'])) {
    // Parar cronómetro
    $_SESSION['test_cronometro']->parar();
    $tiempo_formateado = $_SESSION['test_cronometro']->mostrar();
    
    // Calcular respuestas correctas
    $respuestas_correctas = 0;
    $correctas = ['Sepang', 'Malasia', '5540', '20', 'Petronas', 'Alex Marquez', '16', '3', '101.7383756', 'Yamaha'];
    
    for ($i = 1; $i <= 10; $i++) {
        if (isset($_POST['p' . $i]) && trim($_POST['p' . $i]) === $correctas[$i - 1]) {
            $respuestas_correctas++;
        }
    }
    
    // Guardar en base de datos
    $conexion = new mysqli("localhost", "DBUSER2025", "DBPSWD2025", "UO293873_DB");
    
    if (!$conexion->connect_error) {
        $profesion = $_SESSION['profesion'];
        $edad = $_SESSION['edad'];
        $genero = $_SESSION['genero'];
        $pericia = $_SESSION['pericia'];
        $dispositivo = $_SESSION['dispositivo'];
        $comentarios = isset($_POST['comentarios']) ? $_POST['comentarios'] : '';
        $propuestas = isset($_POST['propuestas']) ? $_POST['propuestas'] : '';
        $valoracion = $_POST['valoracion'];
        $tarea_completada = ($respuestas_correctas >= 7) ? 1 : 0;
        
        // Insertar usuario (codigo se autogenera)
        $sql_usuario = "INSERT INTO Usuario (profesion, edad, id_genero, id_pericia) 
                        VALUES ('$profesion', '$edad', '$genero', '$pericia')";
        
        if ($conexion->query($sql_usuario)) {
            // Obtener el codigo_usuario autogenerado
            $codigo_usuario = $conexion->insert_id;
            
            // Insertar resultado
            $sql_resultado = "INSERT INTO Resultado (codigo_usuario, id_dispositivo, tiempo_completado, tarea_completada, comentarios, propuestas_mejora, valoracion) 
                             VALUES ('$codigo_usuario', '$dispositivo', '$tiempo_formateado', '$tarea_completada', '$comentarios', '$propuestas', '$valoracion')";
            $conexion->query($sql_resultado);
            
            // Insertar observaciones si existen
            if (isset($_POST['observaciones']) && $_POST['observaciones'] != '') {
                $observaciones = $_POST['observaciones'];
                $sql_observaciones = "INSERT INTO Observaciones (codigo_usuario, comentarios) 
                                      VALUES ('$codigo_usuario', '$observaciones')";
                $conexion->query($sql_observaciones);
            }
        }
        
        $conexion->close();
    }
    
    $_SESSION['respuestas_correctas'] = $respuestas_correctas;
    $_SESSION['estado'] = 3;
}

$estado = $_SESSION['estado'];
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Alejandro Aldea Viana - UO293873" />
    <meta name="description" content="Test de usabilidad del proyecto MotoGP-Desktop" />
    <meta name="keywords" content="MotoGP, usabilidad, test, prueba" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Test de Usabilidad - MotoGP</title>
    <link rel="icon" type="image/x-icon" href="../multimedia/icono.png" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>

<body>
    <header>
        <h1>Test de Usabilidad - MotoGP Desktop</h1>
    </header>

    <p>Estás en: <a href="../index.html">Inicio</a> &gt; <a href="../juegos.html">Juegos</a> &gt; <strong>Test de Usabilidad</strong></p>

    <main>
        <?php if ($estado == 3): ?>
            <section>
                <h2>Prueba Completada</h2>
                <p>¡Gracias por tu participación!</p>
                <p>Respuestas correctas: <?php echo $_SESSION['respuestas_correctas']; ?> de 10</p>
                <?php
                    // Limpiar sesión
                    session_destroy();
                ?>
            </section>
        <?php elseif ($estado == 0): ?>
            <section>
                <h2>Test de Usabilidad</h2>
                
                <form method="post" action="test.php">
                    <fieldset>
                        <legend>Datos del Participante</legend>
                        
                        <label for="profesion">Profesión:</label>
                        <input type="text" id="profesion" name="profesion" required />
                        
                        <label for="edad">Edad:</label>
                        <input type="number" id="edad" name="edad" min="1" max="120" required />
                        
                        <label for="genero">Género:</label>
                        <select id="genero" name="genero" required>
                            <option value="">Selecciona...</option>
                            <option value="1">Masculino</option>
                            <option value="2">Femenino</option>
                            <option value="3">Otro</option>
                        </select>
                        
                        <label for="pericia">Pericia Informática:</label>
                        <select id="pericia" name="pericia" required>
                            <option value="">Selecciona...</option>
                            <option value="1">Básico</option>
                            <option value="2">Intermedio</option>
                            <option value="3">Avanzado</option>
                            <option value="4">Experto</option>
                        </select>
                        
                        <label for="dispositivo">Dispositivo utilizado:</label>
                        <select id="dispositivo" name="dispositivo" required>
                            <option value="">Selecciona...</option>
                            <option value="1">Ordenador</option>
                            <option value="2">Tableta</option>
                            <option value="3">Teléfono</option>
                        </select>
                    </fieldset>

                    <button type="submit" name="empezar">Empezar Prueba</button>
                </form>
            </section>
        <?php else: ?>
            <section>
                <h2>Preguntas sobre el Proyecto</h2>
                
                <form method="post" action="test.php">
                    <fieldset>
                        <legend>Cuestionario</legend>
                        
                        <label for="p1">1. ¿Cuál es la localidad del circuito?</label>
                        <input type="text" id="p1" name="p1" required />
                        
                        <label for="p2">2. ¿En qué país se encuentra el circuito?</label>
                        <input type="text" id="p2" name="p2" required />
                        
                        <label for="p3">3. ¿Cuál es la longitud del circuito en metros?</label>
                        <input type="text" id="p3" name="p3" required />
                        
                        <label for="p4">4. ¿Cuántas vueltas tiene la carrera?</label>
                        <input type="text" id="p4" name="p4" required />
                        
                        <label for="p5">5. ¿Cuál es el patrocinador principal del circuito?</label>
                        <input type="text" id="p5" name="p5" required />
                        
                        <label for="p6">6. ¿Quién es el vencedor de la carrera?</label>
                        <input type="text" id="p6" name="p6" required />
                        
                        <label for="p7">7. ¿Cuál es la anchura del circuito en metros?</label>
                        <input type="text" id="p7" name="p7" required />
                        
                        <label for="p8">8. ¿Cuántos sectores tiene el circuito?</label>
                        <input type="text" id="p8" name="p8" required />
                        
                        <label for="p9">9. ¿Cuál es la longitud geográfica de las coordenadas de origen?</label>
                        <input type="text" id="p9" name="p9" required />
                        
                        <label for="p10">10. ¿Qué marca de moto utiliza el piloto Maverick Viñales?</label>
                        <input type="text" id="p10" name="p10" required />
                    </fieldset>

                    <fieldset>
                        <legend>Evaluación</legend>
                        
                        <label for="comentarios">Comentarios sobre la experiencia:</label>
                        <textarea id="comentarios" name="comentarios" rows="4"></textarea>
                        
                        <label for="propuestas">Propuestas de mejora:</label>
                        <textarea id="propuestas" name="propuestas" rows="4"></textarea>
                        
                        <label for="valoracion">Valoración de la aplicación (0-10):</label>
                        <input type="number" id="valoracion" name="valoracion" min="0" max="10" step="0.1" required />
                        
                        <label for="observaciones">Observaciones del facilitador:</label>
                        <textarea id="observaciones" name="observaciones" rows="4"></textarea>
                    </fieldset>

                    <button type="submit" name="terminar">Terminar Prueba</button>
                </form>
            </section>
        <?php endif; ?>
    </main>
</body>
</html>
