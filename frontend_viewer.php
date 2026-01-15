<?php
/**
 * frontend_viewer.php - Carga automática de documentos al pulsar submódulos
 */

$frontend_modules = [
    ['id' => 'Administracion', 'label' => 'Administración'],
    ['id' => 'Produccion_MRP', 'label' => 'Producción-MRP'],
    ['id' => 'Ingenieria', 'label' => 'Ingeniería'],
    ['id' => 'Inventario', 'label' => 'Inventario'],
    ['id' => 'Piso_Planta', 'label' => 'Piso Planta'],
    ['id' => 'Calidad', 'label' => 'Calidad'],
    ['id' => 'Mantenimiento', 'label' => 'Mantenimiento'],
    ['id' => 'Costos', 'label' => 'Costos']
];

$sub_modules_map = [
    'Administracion' => [
        ['prefix' => '01', 'label' => 'Depósitos'],
        ['prefix' => '02', 'label' => 'Centros de Costos'],
        ['prefix' => '03', 'label' => 'Legajos'],
        ['prefix' => '04', 'label' => 'Instrumentales'],
        ['prefix' => '05', 'label' => 'Usuarios'],
        ['prefix' => '06', 'label' => 'Programas Auxiliares'],
        ['prefix' => '07', 'label' => 'Plantas']
    ],
    'Costos' => [
        ['prefix' => '01', 'label' => 'Libro Diario'],
        ['prefix' => '02', 'label' => 'Saldos / Cuentas'],
        ['prefix' => '03', 'label' => 'Categorización / Costos'],
        ['prefix' => '04', 'label' => 'Estructura de Costos'],
        ['prefix' => '05', 'label' => 'Tarifas'],
        ['prefix' => '06', 'label' => 'Indirectos Variables']
    ],
    'Mantenimiento' => [
        ['prefix' => '01', 'label' => 'Ordenes de Trabajo'],
        ['prefix' => '02', 'label' => 'Matricería'],
        ['prefix' => '03', 'label' => 'Mantenimientos Preventivos'],
        ['prefix' => '04', 'label' => 'Mantenimiento Componentes Comunes']
    ],
    'Calidad' => [
        ['prefix' => '01', 'label' => 'Agrupaciones de Calidad'],
        ['prefix' => '02', 'label' => 'Familias y tipos de fabricación'],
        ['prefix' => '03', 'label' => 'Calidades de fabricación'],
        ['prefix' => '04', 'label' => 'Normas de fabricación'],
        ['prefix' => '05', 'label' => 'Maestro de materiales'],
        ['prefix' => '06', 'label' => 'Colores de coladas'],
        ['prefix' => '07', 'label' => 'Recepciones técnicas'],
        ['prefix' => '08', 'label' => 'Gestión de probetas'],
        ['prefix' => '09', 'label' => 'Retrabajos'],
        ['prefix' => '10', 'label' => 'Requerimientos especiales'],
        ['prefix' => '11', 'label' => 'Ingreso de producción'],
        ['prefix' => '12', 'label' => 'Informes radiográficos'],
        ['prefix' => '13', 'label' => 'Gestion de Certificados'],
        ['prefix' => '14', 'label' => 'Componentes Comunes'],
        ['prefix' => '15', 'label' => 'Mantenimiento Colada']
    ],
    'Piso_Planta' => [
        ['prefix' => '01', 'label' => 'Partes Diarios'],
        ['prefix' => '02', 'label' => 'Partes Diarios Especiales'],
        ['prefix' => '03', 'label' => 'Vales de Consumo'],
        ['prefix' => '04', 'label' => 'OP Matricería'],
        ['prefix' => '05', 'label' => 'Transformaciones'],
        ['prefix' => '06', 'label' => 'Ingreso de producción'],
        ['prefix' => '07', 'label' => 'Transformaciones / Componentes Comunes'],
        ['prefix' => '08', 'label' => 'Componentes Comunes']
    ],
    'Inventario' => [
        ['prefix' => '01', 'label' => 'Recepción y Despachos'],
        ['prefix' => '02', 'label' => 'Gestión de Materiales'],
        ['prefix' => '03', 'label' => 'Consulta / Stock de Materiales']
    ],
    'Ingenieria' => [
        ['prefix' => '01', 'label' => 'Gestión de materiales'],
        ['prefix' => '02', 'label' => 'Productos fabricados'],
        ['prefix' => '03', 'label' => 'Fichas técnicas'],
        ['prefix' => '04', 'label' => 'Grupo de fichas técnicas'],
        ['prefix' => '05', 'label' => 'Hojas de procesos'],
        ['prefix' => '06', 'label' => 'Pesada de productos'],
        ['prefix' => '07', 'label' => 'Tiempo Setup de Máquinas'],
        ['prefix' => '08', 'label' => 'Operaciones provisorias'],
        ['prefix' => '09', 'label' => 'Lotes Económicos'],
        ['prefix' => '10', 'label' => 'Componentes Ingeniería']
    ],
    'Produccion_MRP' => [
        ['prefix' => '01', 'label' => 'Planificación OP'],
        ['prefix' => '02', 'label' => 'Sugerencia OP'],
        ['prefix' => '03', 'label' => 'Planificación y Asignación OP'],
        ['prefix' => '04', 'label' => 'Proyección y deuda'],
        ['prefix' => '05', 'label' => 'Previsión de abastecimiento'],
        ['prefix' => '06', 'label' => 'Registro MRP'],
        ['prefix' => '07', 'label' => 'Analítica de Compras'],
        ['prefix' => '08', 'label' => 'Análitica de Capacidad'],
        ['prefix' => '09', 'label' => 'Forecast'],
        ['prefix' => '10', 'label' => 'Componentes Ingeniería']
    ]
];

$current_mod = isset($_GET['mod']) ? $_GET['mod'] : 'Administracion';
$current_sub = isset($_GET['sub']) ? $_GET['sub'] : null;

$root_dir = __DIR__;
$relative_path = $current_mod . "/";
$full_path = $root_dir . DIRECTORY_SEPARATOR . $relative_path;

$auto_load_file = null;
$files = [];

if (is_dir($full_path)) {
    $raw = scandir($full_path);
    // Filtrar PDFs
    $files = array_filter($raw, function($f) {
        return strtolower(pathinfo($f, PATHINFO_EXTENSION)) === 'pdf';
    });
    sort($files);

    // Si hay un submódulo seleccionado, buscamos el primer archivo que coincida para cargarlo
    if ($current_sub) {
        foreach ($files as $f) {
            if (strpos($f, $current_sub . '_') === 0) {
                $auto_load_file = $f;
                break; // Tomamos el primero y salimos
            }
        }
    }
}
?>

<style>
    #frontend-layout { display: flex; width: 100%; height: 100%; background: #f1f5f9; overflow: hidden; }

    /* NAVEGACIÓN */
    #nav-pane { width: 320px; background: #1e293b; color: white; display: flex; flex-direction: column; flex-shrink: 0; border-right: 2px solid #0f172a; }
    .section-header { padding: 12px 15px; background: #0f172a; font-size: 0.65rem; color: #64748b; text-transform: uppercase; font-weight: bold; }

    .mod-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 4px; padding: 10px; border-bottom: 2px solid #0f172a; }
    .mod-btn { padding: 8px; font-size: 0.7rem; color: #94a3b8; text-decoration: none; background: #334155; border-radius: 4px; text-align: center; }
    .mod-btn.active { background: #3b82f6; color: white; font-weight: bold; }

    .submod-list { flex-grow: 1; overflow-y: auto; padding: 10px 0; }
    .submod-link {
        display: block; padding: 12px 20px; color: #cbd5e1; text-decoration: none;
        font-size: 0.85rem; border-left: 4px solid transparent; transition: 0.2s;
    }
    .submod-link:hover { background: #334155; color: #3b82f6; }
    .submod-link.active { background: #2d3748; border-left-color: #3b82f6; color: white; font-weight: bold; }

    /* VISORES */
    #viewers-container { flex-grow: 1; display: flex; flex-direction: column; }
    #pdf-pane { height: 55%; background: #fff; border-bottom: 5px solid #cbd5e1; position: relative; }
    #graph-pane { height: 45%; background: #f8fafc; overflow: auto; padding: 15px; display: flex; justify-content: center; position: relative; }
    #graph-pane img { max-width: 95%; height: auto; border: 1px solid #e2e8f0; box-shadow: 0 4px 10px rgba(0,0,0,0.1); background: white; }

    .placeholder { color: #94a3b8; text-align: center; margin-top: 15%; font-style: italic; font-size: 0.85rem; }
    .pane-tag { position: absolute; top: 10px; right: 20px; background: #64748b; color: white; padding: 2px 8px; border-radius: 4px; font-size: 0.65rem; z-index: 5; opacity: 0.8; }

    #graph-pane {
        height: 45%;
        background: #f8fafc;
        overflow: auto; /* Permite el scroll cuando hay zoom */
        padding: 20px;
        display: flex;
        justify-content: center;
        align-items: flex-start;
        position: relative;
        cursor: grab;
    }

    #graph-pane:active { cursor: grabbing; }

    /* Contenedor para la imagen con zoom */
    #graph-wrapper {
        transition: transform 0.2s ease;
        transform-origin: top center;
    }

    #graph-pane img {
        max-width: 95%;
        height: auto;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        background: white;
    }

    /* Botonera de Zoom flotante */
    .zoom-controls {
        position: absolute;
        bottom: 20px;
        right: 20px;
        display: flex;
        flex-direction: column;
        gap: 5px;
        z-index: 100;
    }

    .zoom-btn {
        width: 35px;
        height: 35px;
        background: #1e293b;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-weight: bold;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0.8;
        transition: 0.2s;
    }

    .zoom-btn:hover { opacity: 1; background: #3b82f6; }

</style>

<div id="frontend-layout">
    <div id="nav-pane">
        <div class="section-header">Módulos</div>
        <div class="mod-grid">
            <?php foreach ($frontend_modules as $m): ?>
                <a href="index.php?page=frontend&mod=<?php echo $m['id']; ?>" class="mod-btn <?php echo $current_mod == $m['id'] ? 'active' : ''; ?>">
                    <?php echo $m['label']; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="section-header">Submódulos</div>
        <div class="submod-list">
            <?php
            $sub_modules = $sub_modules_map[$current_mod] ?? [];
            foreach ($sub_modules as $sm):
            ?>
                <a href="index.php?page=frontend&mod=<?php echo $current_mod; ?>&sub=<?php echo $sm['prefix']; ?>"
                   class="submod-link <?php echo ($current_sub === $sm['prefix']) ? 'active' : ''; ?>">
                   📁 <?php echo $sm['label']; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div id="viewers-container">
        <div id="pdf-pane">
            <span class="pane-tag">DOCUMENTACIÓN</span>
            <div id="pdf-empty" class="placeholder">Seleccione un submódulo para cargar la documentación.</div>
            <iframe id="pdf-viewer" style="display:none; width:100%; height:100%; border:none;"></iframe>
        </div>

        <div id="graph-pane" id="scroll-container">
            <span class="pane-tag">DIAGRAMA LÓGICO</span>

            <div class="zoom-controls">
                <button class="zoom-btn" onclick="adjustZoom(0.1)" title="Aumentar">+</button>
                <button class="zoom-btn" onclick="adjustZoom(-0.1)" title="Disminuir">-</button>
                <button class="zoom-btn" onclick="resetZoom()" title="Restablecer">⟲</button>
            </div>

            <div id="graph-empty" class="placeholder">El diagrama se cargará automáticamente.</div>

            <div id="graph-wrapper" style="display:none;">
                <img id="graph-viewer">
            </div>
        </div>

    </div>
</div>

<script>
    let currentZoom = 1;

    function loadCombo(pdfName) {
        const folder = "<?php echo $relative_path; ?>";
        const pdfViewer = document.getElementById('pdf-viewer');
        const graphViewer = document.getElementById('graph-viewer');
        const graphWrapper = document.getElementById('graph-wrapper');
        const graphEmpty = document.getElementById('graph-empty');
        const pdfEmpty = document.getElementById('pdf-empty');

        // Reset de Zoom al cambiar de documento
        resetZoom();

        // Carga PDF
        pdfEmpty.style.display = 'none';
        pdfViewer.style.display = 'block';
        pdfViewer.src = "view_pdf.php?file=" + encodeURIComponent(folder + pdfName) + "#view=FitH";

        // Carga Imagen
        const baseName = pdfName.substring(0, pdfName.lastIndexOf('.'));
        graphEmpty.style.display = 'none';
        graphWrapper.style.display = 'block';
        graphViewer.style.display = 'block';
        graphViewer.src = folder + baseName + ".png";

        graphViewer.onerror = function() {
            graphWrapper.style.display = 'none';
            graphEmpty.innerHTML = "⚠️ No existe gráfico:<br><small>" + baseName + ".png</small>";
            graphEmpty.style.display = 'block';
        };
    }

    // Funciones de Zoom
    function adjustZoom(delta) {
        currentZoom += delta;
        // Limitamos el zoom entre 0.5x y 3x
        currentZoom = Math.min(Math.max(0.5, currentZoom), 3);
        applyZoom();
    }

    function resetZoom() {
        currentZoom = 1;
        applyZoom();
    }

    function applyZoom() {
        const wrapper = document.getElementById('graph-wrapper');
        wrapper.style.transform = `scale(${currentZoom})`;
    }

    window.onload = function() {
        <?php if ($auto_load_file): ?>
            loadCombo('<?php echo $auto_load_file; ?>');
        <?php endif; ?>
    };
</script>
