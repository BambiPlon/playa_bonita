<?php require 'includes/header.php'; ?>

<main class="main-content">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h1 style="margin: 0 0 4px 0; font-size: 1.5rem; font-weight: 700; color: var(--gray-900);">Nueva Salida</h1>
            <p style="margin: 0; color: var(--text-muted); font-size: 0.8125rem;">Registrar una nueva salida de almacen</p>
        </div>
        <a href="salidas.php" class="btn btn-secondary" style="display: inline-flex; align-items: center; gap: 6px;">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>

    <?php if ($mensaje): ?>
        <div style="display: flex; align-items: center; gap: 10px; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1rem; background: <?php echo $tipo_mensaje === 'success' ? 'var(--success-light)' : '#fef2f2'; ?>; border: 1px solid <?php echo $tipo_mensaje === 'success' ? '#bbf7d0' : '#fecaca'; ?>; color: <?php echo $tipo_mensaje === 'success' ? '#166534' : '#991b1b'; ?>;">
            <i class="fas <?php echo $tipo_mensaje === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'; ?>" style="font-size: 1.125rem;"></i>
            <span style="font-weight: 500; font-size: 0.875rem;"><?php echo htmlspecialchars($mensaje); ?></span>
        </div>
    <?php endif; ?>

    <div class="card">
        <form method="POST" action="nueva-salida.php" id="formNuevaSalida" onsubmit="return confirmarSalida(event)">
            
            <!-- Datos generales -->
            <?php 
            // Buscar el ID del Almacén General
            $almacen_general_id = 1;
            foreach ($sub_almacenes as $almacen) {
                if (stripos($almacen['nombre'], 'general') !== false) {
                    $almacen_general_id = $almacen['id'];
                    break;
                }
            }
            ?>
            <input type="hidden" id="sub_almacen_id" name="sub_almacen_id" value="<?php echo $almacen_general_id; ?>">
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--gray-100);">
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-warehouse" style="color: var(--primary); font-size: 0.75rem;"></i> Almacen
                    </label>
                    <div style="padding: 10px 14px; background: linear-gradient(135deg, #dbeafe, #eff6ff); border: 2px solid #3b82f6; border-radius: 8px; color: #1e40af; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                        <i class="fas fa-check-circle"></i> Almacen General
                    </div>
                </div>
                
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-calendar" style="color: var(--primary); font-size: 0.75rem;"></i> Fecha <span style="color: var(--danger);">*</span>
                    </label>
                    <input type="date" id="fecha_salida" name="fecha_salida" value="<?php echo date('Y-m-d'); ?>" required class="form-input">
                </div>
                
                <div>
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                        <i class="fas fa-map-marker-alt" style="color: var(--primary); font-size: 0.75rem;"></i> Destino
                    </label>
                    <input type="text" id="destino" name="destino" placeholder="Ej: Oficina, Evento..." class="form-input">
                </div>
            </div>
            
            <!-- Productos Section -->
            <div style="margin-bottom: 1.5rem;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; font-size: 0.875rem;">
                        <i class="fas fa-boxes" style="color: var(--primary);"></i> Productos a dar salida <span style="color: var(--danger);">*</span>
                    </label>
                    <div style="display: flex; gap: 0.5rem;">
                        <?php if (!empty($requisiciones_completadas)): ?>
                        <button type="button" onclick="abrirModalRequisicion()" class="btn btn-sm" style="background: #f0f9ff; color: #0284c7; border: 1px solid #bae6fd; display: inline-flex; align-items: center; gap: 5px; font-size: 0.75rem; padding: 6px 12px; border-radius: var(--radius-md);">
                            <i class="fas fa-file-import"></i> Cargar de Requisicion
                        </button>
                        <?php endif; ?>
                        <button type="button" onclick="agregarProducto()" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Agregar Producto
                        </button>
                    </div>
                </div>
                
                <div id="productos-container">
                    <!-- Producto inicial -->
                    <div class="producto-salida-item" data-index="1" style="display: grid; grid-template-columns: 1fr 180px 40px; gap: 0.75rem; align-items: end; padding: 1rem; background: var(--gray-50); border-radius: 8px; margin-bottom: 0.5rem;">
                        <div>
                            <label style="font-size: 0.75rem; color: var(--gray-600); font-weight: 600; margin-bottom: 4px; display: block;">Producto</label>
                            <select name="productos_ids[]" class="form-input producto-select" required onchange="actualizarStock(this)">
                                <option value="">Seleccionar producto...</option>
                                <?php foreach ($productos as $prod): ?>
                                    <option value="<?php echo $prod['id']; ?>" 
                                            data-stock="<?php echo intval($prod['cantidad']); ?>"
                                            data-unidad="<?php echo htmlspecialchars($prod['unidad'] ?? ''); ?>"
                                            data-almacen="<?php echo $prod['sub_almacen_id']; ?>">
                                        <?php echo htmlspecialchars($prod['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div style="display: flex; gap: 8px; align-items: end;">
                            <div style="flex: 1;">
                                <label style="font-size: 0.75rem; color: var(--gray-600); font-weight: 600; margin-bottom: 4px; display: block;">Cantidad</label>
                                <input type="number" name="cantidades[]" min="1" class="form-input cantidad-input" required placeholder="0" style="text-align: center; font-weight: 600;">
                            </div>
                            <div class="stock-badge" style="padding: 8px 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; color: #166534; font-size: 0.75rem; font-weight: 600; white-space: nowrap; height: 38px; display: flex; align-items: center; gap: 4px;">
                                <i class="fas fa-box"></i> <span class="stock-num">--</span>
                            </div>
                        </div>
                        <div style="padding-bottom: 2px;">
                            <button type="button" onclick="this.closest('.producto-salida-item').remove(); actualizarIndices();" class="btn btn-sm" style="background: #fee2e2; color: #dc2626; border: none; width: 36px; height: 36px; padding: 0; border-radius: 8px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div id="info-stock" style="margin-top: 0.5rem; font-size: 0.75rem; color: var(--gray-500);"></div>
            </div>
            
            <!-- Motivo -->
            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center; gap: 6px; color: var(--gray-700); font-weight: 600; margin-bottom: 6px; font-size: 0.8125rem;">
                    <i class="fas fa-comment-alt" style="color: var(--primary); font-size: 0.75rem;"></i> Motivo <span style="color: var(--danger);">*</span>
                </label>
                <textarea id="motivo" name="motivo" rows="2" required placeholder="Describe el motivo de la salida..." class="form-input" style="resize: vertical;"></textarea>
            </div>
            
            <div style="display: flex; gap: 8px; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid var(--gray-100);">
                <a href="salidas.php" class="btn btn-secondary"><i class="fas fa-times"></i> Cancelar</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Registrar Salida</button>
            </div>
        </form>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
var contadorProductos = 1;
var productosData = <?php echo json_encode($productos); ?>;
var requisicionesData = <?php echo json_encode($requisiciones_completadas ?? []); ?>;

function agregarProducto() {
    contadorProductos++;
    var container = document.getElementById('productos-container');
    var div = document.createElement('div');
    div.className = 'producto-salida-item';
    div.setAttribute('data-index', contadorProductos);
    div.style.cssText = 'display: grid; grid-template-columns: 1fr 180px 40px; gap: 0.75rem; align-items: end; padding: 1rem; background: var(--gray-50); border-radius: 8px; margin-bottom: 0.5rem;';
    
    var optionsHtml = '<option value="">Seleccionar producto...</option>';
    productosData.forEach(function(p) {
        optionsHtml += '<option value="' + p.id + '" data-stock="' + p.cantidad + '" data-unidad="' + (p.unidad || '') + '" data-almacen="' + p.sub_almacen_id + '">' + p.nombre + '</option>';
    });
    
    div.innerHTML = '<div>' +
        '<label style="font-size: 0.75rem; color: var(--gray-600); font-weight: 600; margin-bottom: 4px; display: block;">Producto</label>' +
        '<select name="productos_ids[]" class="form-input producto-select" required onchange="actualizarStock(this)">' + optionsHtml + '</select>' +
        '</div>' +
        '<div style="display: flex; gap: 8px; align-items: end;">' +
        '<div style="flex: 1;">' +
        '<label style="font-size: 0.75rem; color: var(--gray-600); font-weight: 600; margin-bottom: 4px; display: block;">Cantidad</label>' +
        '<input type="number" name="cantidades[]" min="1" class="form-input cantidad-input" required placeholder="0" style="text-align: center; font-weight: 600;">' +
        '</div>' +
        '<div class="stock-badge" style="padding: 8px 12px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 6px; color: #166534; font-size: 0.75rem; font-weight: 600; white-space: nowrap; height: 38px; display: flex; align-items: center; gap: 4px;">' +
        '<i class="fas fa-box"></i> <span class="stock-num">--</span>' +
        '</div>' +
        '</div>' +
        '<div style="padding-bottom: 2px;">' +
        '<button type="button" onclick="this.closest(\'.producto-salida-item\').remove(); actualizarIndices();" class="btn btn-sm" style="background: #fee2e2; color: #dc2626; border: none; width: 36px; height: 36px; padding: 0; border-radius: 8px;"><i class="fas fa-trash"></i></button>' +
        '</div>';
    
    container.appendChild(div);
    filtrarProductosPorAlmacen();
}

function actualizarStock(selectElem) {
    var selected = selectElem.options[selectElem.selectedIndex];
    var stock = parseInt(selected.getAttribute('data-stock')) || 0;
    var unidad = selected.getAttribute('data-unidad') || 'pz';
    var item = selectElem.closest('.producto-salida-item');
    var cantidadInput = item.querySelector('.cantidad-input');
    var stockBadge = item.querySelector('.stock-badge');
    var stockNum = item.querySelector('.stock-num');
    
    cantidadInput.max = stock;
    
    // Actualizar badge de stock
    if (selectElem.value) {
        stockNum.textContent = stock + ' ' + unidad;
        if (stock <= 0) {
            stockBadge.style.background = '#fef2f2';
            stockBadge.style.borderColor = '#fecaca';
            stockBadge.style.color = '#991b1b';
            cantidadInput.disabled = true;
            cantidadInput.placeholder = 'Sin stock';
        } else if (stock < 5) {
            stockBadge.style.background = '#fffbeb';
            stockBadge.style.borderColor = '#fde68a';
            stockBadge.style.color = '#92400e';
            cantidadInput.disabled = false;
            cantidadInput.placeholder = '0';
        } else {
            stockBadge.style.background = '#f0fdf4';
            stockBadge.style.borderColor = '#bbf7d0';
            stockBadge.style.color = '#166534';
            cantidadInput.disabled = false;
            cantidadInput.placeholder = '0';
        }
    } else {
        stockNum.textContent = '--';
        stockBadge.style.background = '#f0fdf4';
        stockBadge.style.borderColor = '#bbf7d0';
        stockBadge.style.color = '#166534';
    }
}

function actualizarIndices() {
    var items = document.querySelectorAll('.producto-salida-item');
    items.forEach(function(item, i) {
        item.setAttribute('data-index', i + 1);
    });
    contadorProductos = items.length;
}

function filtrarProductosPorAlmacen() {
    var almacenSelect = document.getElementById('sub_almacen_id');
    if (!almacenSelect) return;
    var almacenId = almacenSelect.value;
    
    document.querySelectorAll('.producto-select').forEach(function(sel) {
        var currentVal = sel.value;
        Array.from(sel.options).forEach(function(opt) {
            if (opt.value === '') return;
            var optAlmacen = opt.getAttribute('data-almacen');
            if (!almacenId || optAlmacen === almacenId) {
                opt.style.display = '';
            } else {
                opt.style.display = 'none';
            }
        });
        // Si el valor actual ya no es visible, resetear
        var currentOpt = sel.querySelector('option[value="' + currentVal + '"]');
        if (currentOpt && currentOpt.style.display === 'none') {
            sel.value = '';
        }
    });
}

function abrirModalRequisicion() {
    if (requisicionesData.length === 0) {
        Swal.fire('Sin requisiciones', 'No hay requisiciones completadas disponibles.', 'info');
        return;
    }
    
    var optionsHtml = '<div style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 350px; overflow-y: auto; text-align: left;">';
    requisicionesData.forEach(function(r) {
        optionsHtml += '<label style="display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px; cursor: pointer; transition: all 0.15s;" ' +
            'onmouseover="this.style.borderColor=\'#3b82f6\'; this.style.background=\'#f0f7ff\'" ' +
            'onmouseout="if(!this.querySelector(\'input\').checked){this.style.borderColor=\'#e5e7eb\'; this.style.background=\'white\'}">' +
            '<input type="radio" name="req_sel" value="' + r.id + '" style="accent-color: #3b82f6;">' +
            '<div style="flex: 1; min-width: 0;">' +
            '<div style="font-weight: 600; color: #1f2937;">' + r.folio + '</div>' +
            '<div style="font-size: 0.75rem; color: #6b7280;">' + r.solicitante + ' - ' + r.total_productos + ' productos</div>' +
            '</div></label>';
    });
    optionsHtml += '</div>';
    
    Swal.fire({
        title: 'Cargar de Requisicion',
        html: '<p style="color: #6b7280; font-size: 0.8125rem; margin-bottom: 0.75rem;">Selecciona una requisicion completada para cargar sus productos:</p>' + optionsHtml,
        showCancelButton: true,
        confirmButtonText: '<i class="fas fa-download"></i> Cargar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3b82f6',
        width: '480px',
        preConfirm: function() {
            var selected = document.querySelector('input[name="req_sel"]:checked');
            if (!selected) {
                Swal.showValidationMessage('Selecciona una requisicion');
                return false;
            }
            return selected.value;
        }
    }).then(function(result) {
        if (result.isConfirmed) {
            cargarProductosDeRequisicion(result.value);
        }
    });
}

function cargarProductosDeRequisicion(reqId) {
    Swal.fire({ title: 'Cargando...', allowOutsideClick: false, didOpen: function() { Swal.showLoading(); } });
    
    fetch('api/obtener-productos-requisicion.php?id=' + reqId)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            Swal.close();
            if (data.error) {
                Swal.fire('Error', data.error, 'error');
                return;
            }
            
            var productos = data.productos;
            if (!productos || productos.length === 0) {
                Swal.fire('Vacio', 'Esta requisicion no tiene productos.', 'info');
                return;
            }
            
            // Limpiar contenedor
            var container = document.getElementById('productos-container');
            container.innerHTML = '';
            contadorProductos = 0;
            
            var productosNoEncontrados = [];
            
            productos.forEach(function(prod) {
                // Verificar si el producto existe en inventario
                if (!prod.inventario_id || !prod.stock_disponible || prod.stock_disponible <= 0) {
                    productosNoEncontrados.push(prod.producto_nombre + ' (' + (prod.unidad || 'sin unidad') + ')');
                    return;
                }
                
                contadorProductos++;
                var stock = parseInt(prod.stock_disponible) || 0;
                var cantidadSugerida = Math.min(parseInt(prod.cantidad), stock);
                
                var optionsHtml = '<option value="">Seleccionar producto...</option>';
                productosData.forEach(function(p) {
                    var selected = (p.id == prod.inventario_id) ? 'selected' : '';
                    optionsHtml += '<option value="' + p.id + '" data-stock="' + p.cantidad + '" data-unidad="' + (p.unidad || '') + '" data-almacen="' + p.sub_almacen_id + '" ' + selected + '>' + p.nombre + ' (Stock: ' + p.cantidad + ' ' + (p.unidad || '') + ')</option>';
                });
                
                var div = document.createElement('div');
                div.className = 'producto-salida-item';
                div.setAttribute('data-index', contadorProductos);
                div.style.cssText = 'display: grid; grid-template-columns: 2fr 120px 40px; gap: 0.75rem; align-items: end; padding: 1rem; background: var(--gray-50); border-radius: 8px; margin-bottom: 0.5rem;';
                
                div.innerHTML = '<div>' +
                    '<label style="font-size: 0.75rem; color: var(--gray-600); font-weight: 600; margin-bottom: 4px; display: block;">Producto</label>' +
                    '<select name="productos_ids[]" class="form-input producto-select" required onchange="actualizarStock(this)">' + optionsHtml + '</select>' +
                    '</div>' +
                    '<div>' +
                    '<label style="font-size: 0.75rem; color: var(--gray-600); font-weight: 600; margin-bottom: 4px; display: block;">Cantidad <span style="font-weight: 400; color: #9ca3af;">(max: ' + stock + ')</span></label>' +
                    '<input type="number" name="cantidades[]" min="1" max="' + stock + '" class="form-input cantidad-input" required value="' + cantidadSugerida + '" style="text-align: center; font-weight: 600;">' +
                    '</div>' +
                    '<div style="padding-bottom: 2px;">' +
                    '<button type="button" onclick="this.closest(\'.producto-salida-item\').remove(); actualizarIndices();" class="btn btn-sm" style="background: #fee2e2; color: #dc2626; border: none; width: 36px; height: 36px; padding: 0; border-radius: 8px;"><i class="fas fa-trash"></i></button>' +
                    '</div>';
                
                container.appendChild(div);
            });
            
            if (contadorProductos === 0) {
                Swal.fire('Sin productos', 'Los productos de esta requisicion no estan en el inventario actual o no tienen stock.', 'warning');
                agregarProducto();
            } else {
                var msgExtra = '';
                if (productosNoEncontrados.length > 0) {
                    msgExtra = '<br><br><small style="color:#b45309;">Productos sin stock o no encontrados:<br>' + productosNoEncontrados.join(', ') + '</small>';
                }
                Swal.fire({
                    icon: 'success',
                    title: 'Productos cargados',
                    html: contadorProductos + ' producto(s) agregados. Verifica las cantidades.' + msgExtra,
                    showConfirmButton: true,
                    confirmButtonColor: '#3b82f6'
                });
            }
        })
        .catch(function(err) {
            Swal.close();
            Swal.fire('Error', 'No se pudo cargar la requisicion.', 'error');
        });
}

function confirmarSalida(event) {
    event.preventDefault();
    
    var items = document.querySelectorAll('.producto-salida-item');
    if (items.length === 0) {
        Swal.fire({ icon: 'error', title: 'Sin productos', text: 'Agrega al menos un producto.', confirmButtonColor: '#ef4444' });
        return false;
    }
    
    var errores = [];
    var resumen = [];
    
    items.forEach(function(item, i) {
        var select = item.querySelector('.producto-select');
        var cantidadInput = item.querySelector('.cantidad-input');
        var selected = select.options[select.selectedIndex];
        var stock = parseInt(selected.getAttribute('data-stock')) || 0;
        var cantidad = parseInt(cantidadInput.value) || 0;
        var nombre = selected.text;
        
        if (!select.value) {
            errores.push('Producto ' + (i + 1) + ': no seleccionado');
        } else if (cantidad > stock) {
            errores.push(nombre.split('(')[0].trim() + ': stock insuficiente (max: ' + stock + ')');
        } else if (cantidad > 0) {
            resumen.push('<li>' + cantidad + ' x ' + nombre.split('(')[0].trim() + '</li>');
        }
    });
    
    if (errores.length > 0) {
        Swal.fire({
            icon: 'error',
            title: 'Errores encontrados',
            html: '<ul style="text-align: left; margin: 0; padding-left: 1.25rem;">' + errores.map(function(e) { return '<li>' + e + '</li>'; }).join('') + '</ul>',
            confirmButtonColor: '#ef4444'
        });
        return false;
    }
    
    Swal.fire({
        title: 'Confirmar salida',
        html: '<p style="margin-bottom: 0.5rem;">Se registrara la salida de:</p><ul style="text-align: left; margin: 0; padding-left: 1.25rem; font-size: 0.875rem;">' + resumen.join('') + '</ul>',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: 'var(--primary)',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Si, registrar',
        cancelButtonText: 'Cancelar'
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('formNuevaSalida').submit();
        }
    });
    
    return false;
}

// Validar cantidades en tiempo real
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('cantidad-input')) {
        var item = e.target.closest('.producto-salida-item');
        var select = item.querySelector('.producto-select');
        var selected = select.options[select.selectedIndex];
        var stock = parseInt(selected.getAttribute('data-stock')) || 0;
        
        if (parseInt(e.target.value) > stock) {
            e.target.style.borderColor = '#ef4444';
            e.target.style.background = '#fef2f2';
        } else {
            e.target.style.borderColor = '';
            e.target.style.background = '';
        }
    }
});
</script>

<?php require 'includes/footer.php'; ?>
