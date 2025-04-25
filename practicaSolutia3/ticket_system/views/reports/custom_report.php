<?php
// Incluir header
require_once BASE_PATH . 'ticket_system/views/partials/header.php';

// Asegurar que $report está definido
$report = $report ?? [];
$startDate = $startDate ?? date('Y-m-d', strtotime('-30 days'));
$endDate = $endDate ?? date('Y-m-d');
$selectedTechnician = $selectedTechnician ?? '';
$selectedCategory = $selectedCategory ?? '';
$selectedStatus = $selectedStatus ?? '';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-4 text-gray-800">Informes Personalizados</h1>
        <a href="index.php?controller=report&action=index" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Volver a Reportes
        </a>
    </div>
    
    <!-- Filtros de Informe Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filtros de Informe</h6>
        </div>
        <div class="card-body">
            <form method="post" action="index.php?controller=report&action=custom" id="reportForm">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="start_date">Fecha Inicio</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" 
                                value="<?php echo $startDate; ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="end_date">Fecha Fin</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" 
                                value="<?php echo $endDate; ?>">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="technician">Técnico</label>
                            <select class="form-control" id="technician" name="technician">
                                <option value="">Todos</option>
                                <?php foreach ($technicians as $tech): ?>
                                <option value="<?php echo $tech['id']; ?>" <?php echo ($selectedTechnician == $tech['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($tech['username']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="category">Categoría</label>
                            <select class="form-control" id="category" name="category">
                                <option value="">Todas</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php echo ($selectedCategory == $cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">Estado</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">Todos</option>
                                <option value="open" <?php echo ($selectedStatus == 'open') ? 'selected' : ''; ?>>Abierto</option>
                                <option value="in_progress" <?php echo ($selectedStatus == 'in_progress') ? 'selected' : ''; ?>>En Progreso</option>
                                <option value="resolved" <?php echo ($selectedStatus == 'resolved') ? 'selected' : ''; ?>>Resuelto</option>
                                <option value="closed" <?php echo ($selectedStatus == 'closed') ? 'selected' : ''; ?>>Cerrado</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Generar Informe
                </button>
            </form>
        </div>
    </div>

    <?php if (!empty($report)): ?>
    <!-- Resultados del Informe Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Resultados del Informe</h6>
            <div class="dropdown no-arrow">
                <a href="index.php?controller=report&action=export&format=csv&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>&technician=<?php echo $selectedTechnician; ?>&category=<?php echo $selectedCategory; ?>&status=<?php echo $selectedStatus; ?>" class="btn btn-sm btn-success">
                    <i class="bi bi-file-earmark-excel"></i> Exportar CSV
                </a>
                <a href="index.php?controller=report&action=export&format=pdf&start_date=<?php echo $startDate; ?>&end_date=<?php echo $endDate; ?>&technician=<?php echo $selectedTechnician; ?>&category=<?php echo $selectedCategory; ?>&status=<?php echo $selectedStatus; ?>" class="btn btn-sm btn-danger">
                    <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
                </a>
                <a href="index.php?controller=report&action=performance" class="btn btn-sm btn-primary">
                    <i class="bi bi-graph-up"></i> Ver Gráficos
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="reportTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Estado</th>
                            <th>Prioridad</th>
                            <th>Categoría</th>
                            <th>Creado</th>
                            <th>Actualizado</th>
                            <th>Cliente</th>
                            <th>Técnico</th>
                            <th>Acciones</th>
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
                                <div class="btn-group">
                                    <a href="index.php?controller=report&action=export&format=csv&ticket_id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-success" title="Exportar a CSV">
                                        <i class="bi bi-file-earmark-excel"></i>
                                    </a>
                                    <a href="index.php?controller=report&action=export&format=pdf&ticket_id=<?php echo $ticket['id']; ?>" class="btn btn-sm btn-danger" title="Exportar a PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
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
            No se encontraron resultados para los filtros seleccionados.
        </div>
    <?php endif; ?>
</div>

<script>
    $(document).ready(function() {
        $('#reportTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json'
            },
            order: [[0, 'desc']]
        });
    });
</script>

<?php
// Incluir footer
require_once BASE_PATH . 'ticket_system/views/partials/footer.php';
?>
