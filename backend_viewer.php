<?php
/**
 * backend_viewer.php - Visor especializado para documentación de Backend
 */

// Definición de las secciones fijas del Backend
$backend_docs = [
    [
        'id' => 'doc_analisis',
        'label' => 'Documentación',
        'file' => 'documentacion-analisis-backend.pdf',
        'icon' => '📄'
    ],
    [
        'id' => 'arq_flujo',
        'label' => 'Arquitectura y Flujo',
        'file' => 'arquitectura_capas_y_flujos_backend.pdf',
        'icon' => '🏗️'
    ]
];

$current_doc_id = isset($_GET['doc']) ? $_GET['doc'] : null;
$folder = "Backend/"; // Carpeta donde deben residir los archivos
$auto_load_file = null;

// Buscar el archivo correspondiente al link seleccionado
if ($current_doc_id) {
    foreach ($backend_docs as $d) {
        if ($d['id'] === $current_doc_id) {
            $auto_load_file = $d['file'];
            break;
        }
    }
}
?>

<style>
    #backend-layout { display: flex; width: 100%; height: 100%; background: #f1f5f9; overflow: hidden; }

    /* PANEL IZQUIERDO: LINKS DIRECTOS */
    #nav-pane { width: 300px; background: #1e293b; color: white; display: flex; flex-direction: column; flex-shrink: 0; border-right: 2px solid #0f172a; }
    .section-header { padding: 15px; background: #0f172a; font-size: 0.7rem; color: #64748b; text-transform: uppercase; font-weight: bold; }

    .doc-link {
        display: block; padding: 15px 20px; color: #cbd5e1; text-decoration: none;
        font-size: 0.9rem; border-left: 4px solid transparent; transition: 0.2s;
        border-bottom: 1px solid #334155;
    }
    .doc-link:hover { background: #334155; color: #3b82f6; }
    .doc-link.active { background: #2d3748; border-left-color: #3b82f6; color: white; font-weight: bold; }

    /* VISOR CENTRAL (PANTALLA COMPLETA) */
    #viewer-container { flex-grow: 1; background: #475569; position: relative; }
    #pdf-viewer { width: 100%; height: 100%; border: none; background: white; }

    .placeholder { color: #94a3b8; text-align: center; margin-top: 20%; font-style: italic; }
    .pane-tag { position: absolute; top: 10px; right: 20px; background: #1e293b; color: white; padding: 4px 12px; border-radius: 4px; font-size: 0.7rem; z-index: 5; opacity: 0.9; }
</style>

<div id="backend-layout">
    <div id="nav-pane">
        <div class="section-header">Estructura Backend</div>
        <?php foreach ($backend_docs as $doc): ?>
            <a href="index.php?page=backend&doc=<?php echo $doc['id']; ?>"
               class="doc-link <?php echo ($current_doc_id === $doc['id']) ? 'active' : ''; ?>">
               <?php echo $doc['icon']; ?> <?php echo $doc['label']; ?>
            </a>
        <?php endforeach; ?>
    </div>

    <div id="viewer-container">
        <span class="pane-tag">VISOR BACKEND</span>
        <div id="pdf-empty" class="placeholder" <?php echo $auto_load_file ? 'style="display:none;"' : ''; ?>>
            <h3>Documentación del Servidor</h3>
            <p>Seleccione una sección a la izquierda para visualizar el análisis o la arquitectura.</p>
        </div>

        <iframe id="pdf-viewer"
                style="<?php echo $auto_load_file ? 'display:block;' : 'display:none;'; ?>"
                src="<?php echo $auto_load_file ? 'view_pdf.php?file=' . urlencode($folder . $auto_load_file) . '#view=FitH' : ''; ?>">
        </iframe>
    </div>
</div>

<script>
    // La carga es gestionada directamente por PHP mediante el parámetro de URL
    // No obstante, mantenemos la coherencia visual con la transición
</script>
