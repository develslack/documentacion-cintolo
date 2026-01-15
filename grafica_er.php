<style>
    /* Estilos internos para mantener el layout de 3 columnas */
    #er-layout { display: flex; width: 100%; height: 100%; }
    #module-nav { width: 180px; background: #1e293b; color: white; overflow-y: auto; flex-shrink: 0; }
    .mod-btn { padding: 12px 15px; cursor: pointer; font-size: 0.8rem; border-bottom: 1px solid #334155; transition: 0.2s; }
    .mod-btn:hover { background: #334155; }
    .mod-btn.active { background: #3b82f6; font-weight: bold; }

    #cy-container { flex-grow: 1; position: relative; background: #fff; }
    #cy { width: 100%; height: 100%; }

    #right-pane { width: 350px; padding: 20px; overflow-y: auto; background: #fff; border-left: 1px solid #e2e8f0; }
    .ddl-code { font-family: 'Consolas', monospace; font-size: 0.75rem; background: #0f172a; color: #e2e8f0; padding: 12px; border-radius: 6px; white-space: pre-wrap; display: block; margin-top: 10px; }
</style>

<div id="er-layout">
    <div id="module-nav">
        <div style="padding:15px; font-size:0.65rem; color:#64748b; font-weight:bold; text-transform:uppercase;">Módulos DB</div>
        <div id="module-buttons"></div>
    </div>

    <div id="cy-container">
        <div id="cy"></div>
    </div>

    <aside id="right-pane">
        <h2 style="font-size: 1rem; color: #1e3a8a; border-bottom: 2px solid #3b82f6; padding-bottom: 5px;">Detalle de Tabla</h2>
        <div id="table-detail">
            <p style="font-size:0.85rem; color:#64748b;">Seleccione un módulo y luego haga clic en una tabla.</p>
        </div>
    </aside>
</div>

<script>
    const modules = [
        { id: 'ADMIN', color: '#64748b', regex: /usuarios|roles|plantas|etl_|aux_|programas_auxiliares|ultimo_valor/ },
        { id: 'INGENIERIA', color: '#3b82f6', regex: /fichas_tecnicas|productos_fabricados|articulos|unidades_de_medida|plantillas_corte/ },
        { id: 'INVENTARIO', color: '#10b981', regex: /depositos|stocks_|recepciones_de_mercaderia|vales_de_consumo|movimientos_materiales/ },
        { id: 'PRODUCCIÓN', color: '#f59e0b', regex: /ordenes_de_produccion|carga_de_maquinas|planificacion_de_ventas|registros_mrp|retrabajos|operaciones_provisorias|saldos_produccion/ },
        { id: 'PISO PLANTA', color: '#06b6d4', regex: /partes_diarios|volante_secuencia|legajos|piso/ },
        { id: 'COSTOS', color: '#6366f1', regex: /asientos_contables|cuentas_contables|costos_|periodos_fiscales|tarifas|monedas|esquemas_de_descuentos/ },
        { id: 'MANTENIMIENTO', color: '#8b5cf6', regex: /ordenes_de_trabajo|maquinas_herramientas|instrumentos|tipos_de_tareas/ },
        { id: 'CALIDAD', color: '#ec4899', regex: /recepciones_tecnicas|estudios|radiografias|agrupaciones_calidades|ensayos_|certificados_|perfiles_barrada/ }
    ];

    let database = {};
    let cyInstance = null;

    function startApp() {
        // En lugar de fetch, usamos los datos inyectados por PHP
        if (!DUMP_RAW_DATA) {
            document.getElementById('table-detail').innerHTML = "<p style='color:red'>Error: No se encontró el archivo dump en el servidor.</p>";
            return;
        }
        database = parseDDL(DUMP_RAW_DATA);
        renderButtons();
    }

    function parseDDL(text) {
        const tables = {};
        const blocks = text.split('CREATE TABLE');
        blocks.shift();
        blocks.forEach(block => {
            const nameMatch = block.match(/`([^`]+)`/);
            if (!nameMatch) return;
            const name = nameMatch[1];
            const fks = [];
            const fkRegex = /FOREIGN KEY \(`([^`]+)`\) REFERENCES `([^`]+)` \(`([^`]+)`\)/g;
            let m;
            while ((m = fkRegex.exec(block)) !== null) { fks.push({ col: m[1], ref: m[2] }); }
            const modInfo = modules.find(m => name.match(m.regex)) || modules[0];
            tables[name] = { name, ddl: "CREATE TABLE " + block.split(';')[0].trim() + ";", relations: fks, module: modInfo };
        });
        return tables;
    }

    function renderButtons() {
        const container = document.getElementById('module-buttons');
        modules.forEach(mod => {
            const btn = document.createElement('div');
            btn.className = 'mod-btn';
            btn.innerText = mod.id;
            btn.onclick = () => loadModule(mod.id, btn);
            container.appendChild(btn);
        });
    }

    function loadModule(modId, btn) {
        document.querySelectorAll('.mod-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const modTables = Object.values(database).filter(t => t.module.id === modId);
        const elements = modTables.map(t => ({ data: { id: t.name, color: t.module.color } }));

        modTables.forEach(t => {
            t.relations.forEach(r => {
                if (modTables.find(mt => mt.name === r.ref)) {
                    elements.push({ data: { source: t.name, target: r.ref } });
                }
            });
        });

        if (cyInstance) cyInstance.destroy();
        cyInstance = cytoscape({
            container: document.getElementById('cy'),
            elements: elements,
            style: [
                { selector: 'node', style: { 'label': 'data(id)', 'background-color': 'data(color)', 'color': '#fff', 'font-size': '10px', 'text-valign': 'center', 'width': '180px', 'height': '40px', 'shape': 'round-rectangle' } },
                { selector: 'edge', style: { 'width': 2, 'line-color': '#cbd5e1', 'target-arrow-shape': 'triangle', 'target-arrow-color': '#94a3b8', 'curve-style': 'bezier' } }
            ],
            layout: { name: 'grid', columns: modTables.length > 10 ? 3 : 2, padding: 30 }
        });
        cyInstance.on('tap', 'node', e => showDetails(e.target.id()));
    }

    function showDetails(tableName) {
        const t = database[tableName];
        const rels = t.relations.map(r => `<li><b>${r.col}</b> → ${r.ref}</li>`).join('') || '<li>Sin relaciones internas.</li>';
        document.getElementById('table-detail').innerHTML = `
            <div style="color:${t.module.color}; font-weight:bold; font-size:0.7rem;">${t.module.id}</div>
            <h3 style="margin:5px 0; font-size:1.1rem;">${t.name}</h3>
            <strong>Relaciones:</strong><ul style="font-size:0.8rem; padding-left:15px;">${rels}</ul>
            <code class="ddl-code">${t.ddl}</code>`;
    }

    startApp();
</script>
