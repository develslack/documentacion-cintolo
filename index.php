<?php
// Configuración de rutas
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Función para cargar el archivo dump de forma segura
$dump_file = 'dump-cintolo-database-struct.txt';
$db_content = '';
if (file_exists($dump_file)) {
    $db_content = file_get_contents($dump_file);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Documentación Cintolo</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cytoscape/3.26.0/cytoscape.min.js"></script>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; margin: 0; display: flex; height: 100vh; overflow: hidden; background: #f1f5f9; }

        #sidebar { width: 220px; background: #0f172a; color: #f8fafc; padding: 20px 0; display: flex; flex-direction: column; flex-shrink: 0; }
        .sidebar-header { padding: 0 20px 20px; font-size: 0.75rem; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; font-weight: bold; }

        .nav-item {
            padding: 12px 20px; cursor: pointer; text-decoration: none; color: #f8fafc;
            transition: all 0.2s ease; font-size: 0.85rem; border-left: 4px solid transparent;
        }
        .nav-item:hover { background: #1e293b; }
        .nav-item.active { background: #1e293b; border-left-color: #3b82f6; color: #3b82f6; font-weight: bold; }

        #content-area { flex-grow: 1; height: 100%; position: relative; overflow: hidden; }
        .welcome-container { padding: 40px; }
        .btn-entry {
            display: inline-block; padding: 12px 20px; background: #3b82f6; color: white;
            text-decoration: none; border-radius: 6px; font-weight: bold; margin-top: 15px;
        }
    </style>
</head>
<body>
    <nav id="sidebar">
        <div class="sidebar-header">Documentación Cintolo</div>
        <a href="index.php?page=home" class="nav-item <?php echo $page == 'home' ? 'active' : ''; ?>">Inicio</a>
        <a href="index.php?page=grafica_er" class="nav-item <?php echo $page == 'grafica_er' ? 'active' : ''; ?>">Gráfica ER</a>
        <a href="index.php?page=frontend" class="nav-item <?php echo $page == 'frontend' ? 'active' : ''; ?>">Frontend</a>
    </nav>

    <main id="content-area">
        <?php
        // Inyectamos el contenido del dump en una variable JS global para que esté disponible siempre
        echo "<script>const DUMP_RAW_DATA = " . json_encode($db_content) . ";</script>";

        switch ($page) {
            case 'grafica_er':
                include 'grafica_er.php';
                break;
            case 'frontend':
                include 'frontend_viewer.php';
                break;
            case 'home':
            default:
                echo '<div class="welcome-container">
                        <h1>Bienvenido al Portal de Documentación</h1>
                        <p>Plataforma centralizada para la estructura de datos y procesos.</p>
                        <a href="index.php?page=grafica_er" class="btn-entry">Abrir Diagrama ER</a>
                        <a href="index.php?page=frontend" class="btn-entry">Abrir Documentación Frontend</a>
                      </div>';
                break;
        }
        ?>
    </main>
</body>
</html>
