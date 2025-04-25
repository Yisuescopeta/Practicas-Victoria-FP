<?php require_once BASE_PATH . 'ticket_system/views/partials/header.php'; ?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="text-primary"><i class="fas fa-file-alt me-2"></i>Informes Personalizados</h1>
        <div>
            <a href="index.php?controller=report&action=index" class="btn btn-secondary me-2">
                <i class="fas fa-arrow-left me-2"></i> Volver a Reportes
            </a>
            <button id="theme-button" class="btn btn-primary">
                <i class="fas fa-moon me-2"></i> Modo Oscuro
            </button>
        </div>
    </div>
    
    <!-- Filtros de Informe Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Filtros de Informe</h5>
        </div>
        <div class="card-body">
            <form method="post" action="index.php?controller=report&action=custom" id="reportForm">
                <div class="row g-3">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="start_date" class="form-label">Fecha Inicio</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-control" id="start_date" name="start_date" 
                                    value="<?php echo $startDate; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="end_date" class="form-label">Fecha Fin</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-control" id="end_date" name="end_date" 
                                    value="<?php echo $endDate; ?>">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="technician" class="form-label">Técnico</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user-cog"></i></span>
                                <select class="form-select" id="technician" name="technician">
                                    <option value="">Todos</option>
                                    <?php foreach ($technicians as $tech): ?>
                                    <option value="<?php echo $tech['id']; ?>" <?php echo ($selectedTechnician == $tech['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tech['username']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="category" class="form-label">Categoría</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-tag"></i></span>
                                <select class="form-select" id="category" name="category">
                                    <option value="">Todas</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>" <?php echo ($selectedCategory == $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['name']); ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status" class="form-label">Estado</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-info-circle"></i></span>
                                <select class="form-select" id="status" name="status">
                                    <option value="">Todos</option>
                                    <option value="open" <?php echo ($selectedStatus == 'open') ? 'selected' : ''; ?>>Abierto</option>
                                    <option value="in_progress" <?php echo ($selectedStatus == 'in_progress') ? 'selected' : ''; ?>>En Progreso</option>
                                    <option value="resolved" <?php echo ($selectedStatus == 'resolved') ? 'selected' : ''; ?>>Resuelto</option>
                                    <option value="closed" <?php echo ($selectedStatus == 'closed') ? 'selected' : ''; ?>>Cerrado</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search me-2"></i> Generar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($report)): ?>
    <!-- Resultados del Informe Card -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-table me-2"></i>Resultados del Informe</h5>
            <div class="btn-group">
                <a href="index.php?controller=report&action=export&format=csv&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>&technician=<?php echo $selectedTechnician; ?>&category=<?php echo $selectedCategory; ?>&status=<?php echo $selectedStatus; ?>" 
                   class="btn btn-success">
                    <i class="fas fa-file-csv me-2"></i> CSV
                </a>
                <a href="index.php?controller=report&action=export&format=pdf&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>&technician=<?php echo $selectedTechnician; ?>&category=<?php echo $selectedCategory; ?>&status=<?php echo $selectedStatus; ?>" 
                   class="btn btn-danger">
                    <i class="fas fa-file-pdf me-2"></i> PDF
                </a>
                <a href="index.php?controller=report&action=performance" class="btn btn-info">
                    <i class="fas fa-chart-line me-2"></i> Gráficos
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover" id="reportTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th><i class="fas fa-heading me-2"></i>Título</th>
                            <th><i class="fas fa-info-circle me-2"></i>Estado</th>
                            <th><i class="fas fa-exclamation-triangle me-2"></i>Prioridad</th>
                            <th><i class="fas fa-tag me-2"></i>Categoría</th>
                            <th><i class="fas fa-calendar-plus me-2"></i>Creado</th>
                            <th><i class="fas fa-calendar-check me-2"></i>Actualizado</th>
                            <th><i class="fas fa-user me-2"></i>Cliente</th>
                            <th><i class="fas fa-user-cog me-2"></i>Técnico</th>
                            <th><i class="fas fa-cogs me-2"></i>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($report as $ticket): ?>
                        <tr>
                            <td><?php echo $ticket['id']; ?></td>
                            <td><?php echo htmlspecialchars($ticket['title']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $this->getStatusColor($ticket['status']); ?>">
                                    <?php echo $this->getStatusLabel($ticket['status']); ?>
                                </span>
                            </td>
                            <td><?php echo ucfirst($ticket['priority']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['category_name']); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($ticket['created_at'])); ?></td>
                            <td><?php echo date('d/m/Y H:i', strtotime($ticket['updated_at'])); ?></td>
                            <td><?php echo htmlspecialchars($ticket['client_name']); ?></td>
                            <td><?php echo htmlspecialchars($ticket['technician_name'] ?? 'Sin asignar'); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="index.php?controller=report&action=export&format=csv&ticket_id=<?php echo $ticket['id']; ?>" 
                                       class="btn btn-sm btn-success" title="Exportar a CSV">
                                        <i class="fas fa-file-csv"></i>
                                    </a>
                                    <a href="index.php?controller=report&action=export&format=pdf&ticket_id=<?php echo $ticket['id']; ?>" 
                                       class="btn btn-sm btn-danger" title="Exportar a PDF">
                                        <i class="fas fa-file-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i> No se encontraron resultados para los filtros seleccionados.
        </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        // Script para el modo oscuro/claro
        const themeButton = document.getElementById('theme-button');
        const body = document.body;
        const icon = themeButton.querySelector('i');

        if (localStorage.getItem('darkMode') === 'true') {
            enableDarkMode();
        }

        themeButton.addEventListener('click', toggleDarkMode);

        function toggleDarkMode() {
            if (body.classList.contains('dark-mode')) {
                disableDarkMode();
            } else {
                enableDarkMode();
            }
        }

        function enableDarkMode() {
            body.classList.add('dark-mode');
            icon.classList.replace('fa-moon', 'fa-sun');
            themeButton.innerHTML = '<i class="fas fa-sun me-2"></i> Modo Claro';
            localStorage.setItem('darkMode', 'true');
        }

        function disableDarkMode() {
            body.classList.remove('dark-mode');
            icon.classList.replace('fa-sun', 'fa-moon');
            themeButton.innerHTML = '<i class="fas fa-moon me-2"></i> Modo Oscuro';
            localStorage.setItem('darkMode', 'false');
        }

        // Inicializar DataTable
        $('#reportTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            order: [[0, 'desc']],
            responsive: true
        });
    });
</script>

<?php require_once BASE_PATH . 'ticket_system/views/partials/footer.php'; ?>