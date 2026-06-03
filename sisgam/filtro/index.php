<?php
session_start();
require_once '../auth/session.php';
require_once '../conexao/conexao.php';

$usuario_nome  = $_SESSION['usuario_nome'];
$usuario_nivel = $_SESSION['usuario_nivel'];


$eixos           = $con->query("SELECT * FROM eixo_tematico ORDER BY nome_eixo ASC");
$periodicidades  = $con->query("SELECT * FROM periodicidade ORDER BY nome_periodicidade ASC");
$espacos         = $con->query("SELECT * FROM espaco_fisico ORDER BY nome_espaco ASC");
$lista_titulos   = $con->query("SELECT id_atividade, titulo FROM atividade ORDER BY titulo ASC");


$where_clauses = [];
$params = [];
$types = "";


if (!empty($_GET['filter_atividade'])) {
    $where_clauses[] = "a.id_atividade = ?";
    $params[] = intval($_GET['filter_atividade']);
    $types .= "i";
}


if (!empty($_GET['date_de'])) {
    $where_clauses[] = "a.data_inicio >= ?";
    $params[] = $_GET['date_de'];
    $types .= "s";
}
if (!empty($_GET['date_ate'])) {
    $where_clauses[] = "a.data_fim <= ?";
    $params[] = $_GET['date_ate'];
    $types .= "s";
}


if (!empty($_GET['filter_espaco'])) {
    $where_clauses[] = "a.fk_espaco = ?";
    $params[] = intval($_GET['filter_espaco']);
    $types .= "i";
}


if (!empty($_GET['filter_eixo'])) {
    $where_clauses[] = "a.fk_eixo = ?";
    $params[] = intval($_GET['filter_eixo']);
    $types .= "i";
}


if (!empty($_GET['filter_periodicidade'])) {
    $where_clauses[] = "a.fk_periodicidade = ?";
    $params[] = intval($_GET['filter_periodicidade']);
    $types .= "i";
}


if (!empty($_GET['filter_visibilidade'])) {
    $where_clauses[] = "a.visibilidade = ?";
    $params[] = $_GET['filter_visibilidade'];
    $types .= "s";
}


if (!empty($_GET['filter_status'])) {
    $where_clauses[] = "a.status = ?";
    $params[] = $_GET['filter_status'];
    $types .= "s";
}


if (!in_array($usuario_nivel, ['1', '2'])) {
    $where_clauses[] = "a.visibilidade = 'Publica'";
}


$where_sql = "";
if (count($where_clauses) > 0) {
    $where_sql = " WHERE " . implode(" AND ", $where_clauses);
}


$sql_busca = "SELECT a.*, e.nome_eixo, p.nome_periodicidade, esp.nome_espaco, esp.capacidade_maxima,
                     c.link_midia AS cine_link, c.detalhes AS cine_detalhes,
                     GROUP_CONCAT(u.nome SEPARATOR '||') AS inscritos_nomes,
                     COUNT(ie.id_inscricao) AS total_inscritos
              FROM atividade a
              INNER JOIN eixo_tematico e ON a.fk_eixo = e.id_eixo
              INNER JOIN periodicidade p ON a.fk_periodicidade = p.id_periodicidade
              INNER JOIN espaco_fisico esp ON a.fk_espaco = esp.id_espaco
              LEFT JOIN cine_biblioteca c ON c.fk_atividade = a.id_atividade
              LEFT JOIN inscricao_evento ie ON ie.fk_atividade = a.id_atividade
              LEFT JOIN usuario u ON ie.fk_id_usuario = u.id_usuario" . 
              $where_sql . " 
              GROUP BY a.id_atividade 
              ORDER BY a.data_inicio DESC";

$stmt = $con->prepare($sql_busca);
if (count($params) > 0) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$resultados = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SisGAM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png" />
    <link rel="stylesheet" type="text/css" href="css/estilo.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-firjan p-3 shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="../home/">SisGAM <span class="badge bg-secondary ms-1" style="font-size: 0.65em;">FILTRO AVANÇADO</span></a>
        <a href="../home/" class="btn btn-outline-light btn-sm fw-bold"><i class="fa-solid fa-arrow-left me-1"></i> Voltar ao Painel</a>
    </div>
</nav>

<div class="container my-4">

    <div class="card card-filtro p-4 mb-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <i class="fa-solid fa-sliders text-primary fs-4"></i>
            <h4 class="fw-bold text-dark mb-0">Central de Inteligência e Filtros</h4>
        </div>
        <form method="GET" action="">
            <div class="row g-3">
                <div class="col-xl-4 col-lg-6">
                    <label class="form-label small fw-bold text-secondary">Atividade / Projeto</label>
                    <select class="form-select" name="filter_atividade">
                        <option value="">Todas as Atividades...</option>
                        <?php while($t = $lista_titulos->fetch_assoc()): ?>
                            <option value="<?= $t['id_atividade']; ?>" <?= isset($_GET['filter_atividade']) && $_GET['filter_atividade'] == $t['id_atividade'] ? 'selected' : ''; ?>><?= htmlspecialchars($t['titulo']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-xl-2 col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-secondary">Data Início (A partir de)</label>
                    <input type="date" class="form-control" name="date_de" value="<?= $_GET['date_de'] ?? ''; ?>">
                </div>

                <div class="col-xl-2 col-lg-3 col-md-6">
                    <label class="form-label small fw-bold text-secondary">Data Fim (Até)</label>
                    <input type="date" class="form-control" name="date_ate" value="<?= $_GET['date_ate'] ?? ''; ?>">
                </div>

                <div class="col-xl-4 col-lg-6">
                    <label class="form-label small fw-bold text-secondary">Local / Espaço Físico</label>
                    <select class="form-select" name="filter_espaco">
                        <option value="">Todos os Espaços...</option>
                        <?php while($esp = $espacos->fetch_assoc()): ?>
                            <option value="<?= $esp['id_espaco']; ?>" <?= isset($_GET['filter_espaco']) && $_GET['filter_espaco'] == $esp['id_espaco'] ? 'selected' : ''; ?>><?= htmlspecialchars($esp['nome_espaco']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">Eixo Temático</label>
                    <select class="form-select" name="filter_eixo">
                        <option value="">Todos os Eixos...</option>
                        <?php while($e = $eixos->fetch_assoc()): ?>
                            <option value="<?= $e['id_eixo']; ?>" <?= isset($_GET['filter_eixo']) && $_GET['filter_eixo'] == $e['id_eixo'] ? 'selected' : ''; ?>><?= htmlspecialchars($e['nome_eixo']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">Periodicidade</label>
                    <select class="form-select" name="filter_periodicidade">
                        <option value="">Todas...</option>
                        <?php while($p = $periodicidades->fetch_assoc()): ?>
                            <option value="<?= $p['id_periodicidade']; ?>" <?= isset($_GET['filter_periodicidade']) && $_GET['filter_periodicidade'] == $p['id_periodicidade'] ? 'selected' : ''; ?>><?= htmlspecialchars($p['nome_periodicidade']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">Escopo (Visibilidade)</label>
                    <select class="form-select" name="filter_visibilidade">
                        <option value="">Todos os Escopos...</option>
                        <option value="Publica" <?= isset($_GET['filter_visibilidade']) && $_GET['filter_visibilidade'] == 'Publica' ? 'selected' : ''; ?>>🌐 Pública</option>
                        <option value="Interna" <?= isset($_GET['filter_visibilidade']) && $_GET['filter_visibilidade'] == 'Interna' ? 'selected' : ''; ?>>🔒 Interna</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary">Status de Execução</label>
                    <select class="form-select" name="filter_status">
                        <option value="">Todos os Status...</option>
                        <option value="Planejado" <?= isset($_GET['filter_status']) && $_GET['filter_status'] == 'Planejado' ? 'selected' : ''; ?>>🕒 Planejado</option>
                        <option value="Executado" <?= isset($_GET['filter_status']) && $_GET['filter_status'] == 'Executado' ? 'selected' : ''; ?>>✅ Executado</option>
                    </select>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="index.php" class="btn btn-secondary fw-semibold px-4"><i class="fa-solid fa-trash-can me-1"></i> Limpar Filtros</a>
                <button type="submit" class="btn btn-primary fw-bold px-5 shadow-sm"><i class="fa-solid fa-magnifying-glass me-1"></i> Filtrar Base de Dados</button>
            </div>
        </form>
    </div>

    <div class="d-flex align-items-center justify-content-between mb-3 px-1">
        <h5 class="fw-bold text-secondary mb-0"><i class="fa-solid fa-square-poll-horizontal me-1"></i> Resultados da Busca (<?= $resultados->num_rows; ?> encontrados)</h5>
    </div>

    <?php if ($resultados->num_rows > 0): ?>
        <?php while($row = $resultados->fetch_assoc()): ?>
            <div class="card card-resultado p-4">
                <div class="row align-items-start g-3">
                    <div class="col-md-2 col-sm-3 text-center">
                        <img src="../img/img_atividades/<?= !empty($row['img']) ? $row['img'] : 'default.png'; ?>" class="img-fluid rounded-3 border" style="max-height: 110px; object-fit: cover; width: 100%;">
                    </div>

                    <div class="col-md-7 col-sm-9">
                        <div class="d-flex align-items-center gap-2 flex-wrap mb-2">
                            <span class="badge bg-primary-subtle text-primary badge-custom"><?= htmlspecialchars($row['nome_eixo']); ?></span>
                            <span class="badge bg-secondary-subtle text-secondary badge-custom"><i class="fa-solid fa-clock me-1"></i> <?= htmlspecialchars($row['nome_periodicidade']); ?></span>
                            <?php if($row['status'] === 'Planejado'): ?>
                                <span class="badge bg-warning-subtle text-warning badge-custom">🕒 Planejado</span>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success badge-custom">✅ Executado</span>
                            <?php endif; ?>
                            <?php if($row['visibilidade'] === 'Interna'): ?>
                                <span class="badge bg-danger-subtle text-danger badge-custom">🔒 Interna</span>
                            <?php else: ?>
                                <span class="badge bg-info-subtle text-info badge-custom">🌐 Pública</span>
                            <?php endif; ?>
                        </div>

                        <h4 class="fw-bold text-dark mb-1"><?= htmlspecialchars($row['titulo']); ?></h4>
                        <p class="text-secondary small mb-2"><i class="fa-solid fa-location-dot text-danger me-1"></i> <strong>Local:</strong> <?= htmlspecialchars($row['nome_espaco']); ?> | <i class="fa-solid fa-calendar me-1 text-primary"></i> <strong>Período:</strong> <?= date('d/m/Y', strtotime($row['data_inicio'])); ?> até <?= date('d/m/Y', strtotime($row['data_fim'])); ?></p>
                        <div class="text-muted small text-justify bg-light p-2 rounded border" style="font-size: 0.92em;">
                            <strong>Objetivo:</strong> <?= htmlspecialchars($row['objetivo']); ?>
                        </div>
                    </div>

                    <div class="col-md-3 text-md-end text-start d-flex flex-md-column justify-content-between align-items-md-end h-100">
                        <div class="bg-light border rounded-3 p-2 text-center min-w-100 mb-md-3">
                            <span class="d-block small text-uppercase text-muted fw-bold" style="font-size: 0.68em;">Inscritos Confirmados</span>
                            <span class="fs-4 fw-bold text-dark"><?= $row['total_inscritos']; ?></span>
                            <span class="text-muted small" style="font-size: 0.75em;"> / Max: <?= $row['capacidade_maxima']; ?></span>
                        </div>

                        <button class="btn btn-outline-primary fw-bold w-100 d-flex align-items-center justify-content-center gap-1" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAtv<?= $row['id_atividade']; ?>" aria-expanded="false">
                            <i class="fa-solid fa-folder-plus"></i> Ver Detalhes e Pessoas
                        </button>
                    </div>
                </div>

                <div class="collapse mt-3 pt-3 border-top" id="collapseAtv<?= $row['id_atividade']; ?>">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-users-viewfinder text-primary me-1"></i> Segmentação e Observações</h6>
                            <p class="text-secondary small mb-2"><strong>Público-Alvo Mapeado:</strong> <?= htmlspecialchars($row['publico_alvo']); ?></p>
                            <p class="text-secondary small mb-0"><strong>Notas de Campo:</strong> <?= !empty($row['observacoes']) ? htmlspecialchars($row['observacoes']) : '<i>Sem observações extras.</i>'; ?></p>

                            <?php if (!empty($row['cine_link'])): ?>
                                <div class="box-cine p-3 mt-3">
                                    <h6 class="fw-bold text-warning-emphasis mb-2"><i class="fa-solid fa-video me-1"></i> Desdobramento: CineBiblioteca Ativo</h6>
                                    <p class="small mb-1 text-dark"><strong>Link da Mídia:</strong> <a href="<?= htmlspecialchars($row['cine_link']); ?>" target="_blank"><?= htmlspecialchars($row['cine_link']); ?></a></p>
                                    <p class="small mb-0 text-secondary"><strong>Síntese da Obra:</strong> <?= htmlspecialchars($row['cine_detalhes']); ?></p>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6 border-start">
                            <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-id-card text-success me-1"></i> Pessoas Inscritas na Atividade</h6>
                            <?php if (!empty($row['inscritos_nomes'])): 
                                $arr_inscritos = explode('||', $row['inscritos_nomes']);
                            ?>
                                <ul class="list-inscritos">
                                    <?php foreach($arr_inscritos as $nome_pessoa): 
                                        $inicial = strtoupper(substr($nome_pessoa, 0, 1));
                                    ?>
                                        <li>
                                            <div class="avatar-inscrito"><?= $inicial; ?></div>
                                            <span class="fw-semibold text-dark"><?= htmlspecialchars($nome_pessoa); ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted small italic my-3"><i class="fa-solid fa-user-slash"></i> Nenhuma inscrição efetuada para este evento até o momento.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="card card-filtro p-5 text-center text-muted">
            <i class="fa-solid fa-database-blur fs-1 mb-3 text-secondary"></i>
            <h4>Nenhum registro corresponde aos critérios de filtragem selecionados.</h4>
            <p class="small">Tente ajustar as datas ou remover algum termo de especificação.</p>
        </div>
    <?php endif; ?>

</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>