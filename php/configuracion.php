<?php
class Configuracion {
    private $servidor;
    private $usuario;
    private $contra;
    private $baseDatos;
    private $conexion;

    public function __construct() {
        $this->servidor = "localhost";
        $this->usuario = "DBUSER2025";
        $this->contra = "DBPSWD2025";
        $this->baseDatos = "UO293873_DB";
        $this->conexion = null;
    }

    private function conectar($conBaseDatos = true) {
        if ($this->conexion && !$this->conexion->connect_error) {
            return;
        }

        if ($conBaseDatos) {
            $this->conexion = new mysqli($this->servidor, $this->usuario, $this->contra, $this->baseDatos);
        } else {
            $this->conexion = new mysqli($this->servidor, $this->usuario, $this->contra);
        }

        if ($this->conexion->connect_error) {
            $this->conexion = null;
        }
    }

    public function reiniciarBaseDatos() {
        $this->conectar(false);
        
        if (!$this->conexion) {
            return "<p>Error al conectar con el servidor.</p>";
        }
        
        if (!$this->verificarExistenciaBaseDatos()) {
            return $this->crearBaseDatos();
        }
        
        $error = $this->reconectarConBaseDatos();
        if ($error) {
            return $error;
        }
        
        $mensajes = "";

        // Eliminar todas las tablas en el orden correcto
        $tablas = ['Observaciones', 'Resultado', 'Usuario', 'Dispositivo', 'PericiaInformatica', 'Genero']; 
        foreach ($tablas as $tabla) {
            if ($this->conexion->query("DROP TABLE IF EXISTS $tabla")) {
                $mensajes .= "<p>Tabla $tabla eliminada correctamente.</p>";
            } else {
                $mensajes .= "<p>Error al eliminar la tabla $tabla: " . $this->conexion->error . "</p>";
            }
        }

        $mensajes .= $this->procesarArchivoSQL();
        $this->insertarValoresIniciales();

        $mensajes .= "<p>Base de datos reiniciada correctamente.</p>";
        return $mensajes;
    }

    private function verificarExistenciaBaseDatos() {
        $resultado = $this->conexion->query("SHOW DATABASES LIKE '" . $this->baseDatos . "'");
        return $resultado && $resultado->num_rows > 0;
    }

    private function reconectarConBaseDatos() {
        $this->conexion->close();
        $this->conexion = null;
        $this->conectar(true);
        
        if (!$this->conexion) {
            return "<p>Error al conectar con la base de datos.</p>";
        }
        return null;
    }

    private function procesarArchivoSQL() {
        $mensajes = "";
        $archivo = fopen('UO293873_DB.sql', 'r');
        if (!$archivo) {
            return "<p>Error al abrir el archivo SQL.</p>";
        }

        $query = '';
        $dentroInsert = false;
        
        while (!feof($archivo)) {
            $linea = fgets($archivo);
            
            if (stripos($linea, '--') === 0) {
                continue;
            }
            
            if (stripos($linea, 'INSERT INTO') !== false) {
                $dentroInsert = true;
            }
            
            if ($dentroInsert) {
                if (strpos($linea, ';') !== false) {
                    $dentroInsert = false;
                }
                continue;
            }
            
            if (trim($linea) == '' ||
                stripos($linea, 'CREATE DATABASE') !== false || 
                stripos($linea, 'USE ') !== false) {
                continue;
            }
            
            $query .= $linea;
            
            if (strpos($linea, ';') !== false) {
                $query = trim($query);
                if (!empty($query)) {
                    if (!$this->conexion->query($query)) {
                        $mensajes .= "<p>Error en consulta: " . $this->conexion->error . "</p>";
                    }
                }
                $query = '';
            }
        }

        fclose($archivo);
        return $mensajes;
    }

    private function exportarTabla($archivo, $nombreTabla, $titulo, $columnas) {
        $resultado = $this->conexion->query("SELECT * FROM $nombreTabla");
        if ($resultado && $resultado->num_rows > 0) {
            fputcsv($archivo, [$titulo], ';');
            fputcsv($archivo, $columnas, ';');
            while ($fila = $resultado->fetch_row()) {
                fputcsv($archivo, $fila, ';');
            }
            fputcsv($archivo, [], ';');
        }
    }

    private function insertarValoresIniciales() {
        $this->conexion->select_db($this->baseDatos);
        $this->conexion->query("INSERT INTO Genero (nombre) VALUES ('Masculino'), ('Femenino'), ('Otro')");
        $this->conexion->query("INSERT INTO PericiaInformatica (nivel) VALUES ('Básico'), ('Intermedio'), ('Avanzado'), ('Experto')");
        $this->conexion->query("INSERT INTO Dispositivo (tipo) VALUES ('Ordenador'), ('Tableta'), ('Teléfono')");
    }

    public function eliminarBaseDatos() {
        $this->conectar(false);
        
        if (!$this->conexion) {
            return "<p>Error al conectar con el servidor.</p>";
        }

        if (!$this->verificarExistenciaBaseDatos()) {
            return "<p>La base de datos no existe.</p>";
        }

        if ($this->conexion->query("DROP DATABASE IF EXISTS " . $this->baseDatos)) {
            return "<p>Base de datos " . $this->baseDatos . " eliminada correctamente.</p>";
        } else {
            return "<p>Error al eliminar la base de datos: " . $this->conexion->error . "</p>";
        }
    }

    public function exportarDatos() {
        $this->conectar(false);
        
        if (!$this->conexion) {
            return "<p>Error al conectar con el servidor.</p>";
        }

        if (!$this->verificarExistenciaBaseDatos()) {
            return "<p>La base de datos no existe. Por favor, créala primero.</p>";
        }

        $error = $this->reconectarConBaseDatos();
        if ($error) {
            return $error;
        }

        $nombreArchivo = "usabilidad_" . date('Y-m-d_H-i-s') . ".csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $nombreArchivo . '"');
        $archivo = fopen('php://output', 'w');
        
        fprintf($archivo, chr(0xEF).chr(0xBB).chr(0xBF));

        $this->exportarTabla($archivo, 'Usuario', 'USUARIOS', ['Codigo', 'Profesion', 'Edad', 'ID_Genero', 'ID_Pericia']);
        $this->exportarTabla($archivo, 'Resultado', 'RESULTADOS', ['ID', 'Codigo_Usuario', 'ID_Dispositivo', 'Tiempo', 'Completada', 'Comentarios', 'Propuestas', 'Valoracion']);
        $this->exportarTabla($archivo, 'Observaciones', 'OBSERVACIONES', ['ID', 'Codigo_Usuario', 'Comentarios']);
        $this->exportarTabla($archivo, 'Genero', 'GENEROS', ['ID', 'Nombre']);
        $this->exportarTabla($archivo, 'PericiaInformatica', 'PERICIA INFORMATICA', ['ID', 'Nivel']);
        $this->exportarTabla($archivo, 'Dispositivo', 'DISPOSITIVOS', ['ID', 'Tipo']);

        fclose($archivo);
        exit();
    }

    public function crearBaseDatos() {
        $this->conectar(false);
        
        if (!$this->conexion) {
            return "<p>Error al conectar con el servidor.</p>";
        }
        
        $mensajes = "";
        
        if ($this->conexion->query("CREATE DATABASE IF NOT EXISTS " . $this->baseDatos)) {
            $mensajes .= "<p>Base de datos " . $this->baseDatos . " creada correctamente.</p>";
        } else {
            $mensajes .= "<p>Error al crear la base de datos: " . $this->conexion->error . "</p>";
            return $mensajes;
        }

        $this->conexion->select_db($this->baseDatos);
        $mensajes .= $this->procesarArchivoSQL();
        $this->insertarValoresIniciales();
        
        $mensajes .= "<p>Tablas creadas correctamente.</p>";
        return $mensajes;
    }

    public function __destruct() {
        if ($this->conexion && !$this->conexion->connect_error) {
            $this->conexion->close();
        }
    }
}

// Procesar acciones
$configuracion = new Configuracion();
$mensajes = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['reiniciar'])) {
        $mensajes = $configuracion->reiniciarBaseDatos();
    } elseif (isset($_POST['eliminar'])) {
        $mensajes = $configuracion->eliminarBaseDatos();
    } elseif (isset($_POST['exportar'])) {
        $resultado = $configuracion->exportarDatos();
        if ($resultado !== null) {
            $mensajes = $resultado;
        }
    } elseif (isset($_POST['crear'])) {
        $mensajes = $configuracion->crearBaseDatos();
    }
}
?>
<!DOCTYPE HTML>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="author" content="Alejandro Aldea Viana - UO293873" />
    <meta name="description" content="Configuración de la base de datos de pruebas de usabilidad" />
    <meta name="keywords" content="MotoGP, usabilidad, configuración, base de datos" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Configuración Test de Usabilidad</title>
    <link rel="icon" type="image/x-icon" href="../multimedia/icono.png" />
    <link rel="stylesheet" type="text/css" href="../estilo/estilo.css" />
    <link rel="stylesheet" type="text/css" href="../estilo/layout.css" />
</head>

<body>
    <header>
        <h1>Configuración Test de Usabilidad</h1>
    </header>

    <p>Estás en: <a href="../index.html">Inicio</a> &gt; <a href="../juegos.html">Juegos</a> &gt; <strong>Configuración Test de Usabilidad</strong></p>

    <main>
        <section>
            <h2>Gestión de la Base de Datos</h2>
            <p>Utiliza las siguientes opciones para gestionar la base de datos de pruebas de usabilidad.</p>

            <form method="post" action="configuracion.php">
                <fieldset>
                    <legend>Operaciones de Base de Datos</legend>

                    <button type="submit" name="crear">Crear Base de Datos</button>

                    <button type="submit" name="reiniciar">Reiniciar Base de Datos</button>

                    <button type="submit" name="exportar">Exportar Datos (CSV)</button>

                    <button type="submit" name="eliminar" >Eliminar Base de Datos</button>
                </fieldset>
            </form>

            <?php if (!empty($mensajes)): ?>
                <div>
                    <?php echo $mensajes; ?>
                </div>
            <?php endif; ?>

            <p><a href="../juegos.html">Volver a Juegos</a></p>
        </section>
    </main>
</body>
</html>