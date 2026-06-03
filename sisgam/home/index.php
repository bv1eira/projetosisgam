<?php
include 'php/config.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SisGAM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png" />
    <link rel="stylesheet" type="text/css" href="css/estilo.css">    
    <style>
        body { background-color: #f4f6f9; }        
        .card-grafico { border: none; border-radius: 15px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); height: 380px; }
        .table-container { border-radius: 15px; }
        .btn-action-size { width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; }
        
        .card-atividade-modo { border: none; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.04); transition: transform 0.2s; background: #fff; height: 100%; overflow: hidden; }
        .card-atividade-modo:hover { transform: translateY(-3px); box-shadow: 0 6px 15px rgba(0,0,0,0.08); }
        .card-img-top-modo { height: 140px; object-fit: cover; width: 100%; background-color: #eaeaea; }

        /* =====================================================================
           ESTILOS DO BOTÃO FLUTUANTE E REGRAS DE ACESSIBILIDADE DUPLA (PC / CEL)
           ===================================================================== */
        .btn-acessibilidade-flutuante {
            position: fixed;
            bottom: 25px;
            right: 25px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background-color: #003366;
            color: #ffffff;
            border: 2px solid #ffffff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.25);
            z-index: 9999;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 22px;
        }
        .btn-acessibilidade-flutuante:hover {
            transform: scale(1.1);
        }
        .btn-acessibilidade-flutuante.ativo {
            background-color: #1cc88a !important;
            box-shadow: 0 0 15px rgba(28, 200, 138, 0.6);
        }

        /* COMPORTAMENTO PARA DESKTOPS (COMPUTADORES): Lupa interativa guiada pelo cursor */
        @media (min-width: 768px) {
            .modo-lupa {
                cursor: zoom-in !important;
            }
            .modo-lupa span:hover, 
            .modo-lupa p:hover, 
            .modo-lupa h4:hover, 
            .modo-lupa h5:hover, 
            .modo-lupa h6:hover, 
            .modo-lupa td:hover, 
            .modo-lupa th:hover,
            .modo-lupa small:hover,
            .modo-lupa .fs-2:hover,
            .modo-lupa .text-uppercase:hover {
                display: inline-block;
                transform: scale(1.30) !important;
                transform-origin: center left;
                color: #003366 !important;
                transition: transform 0.1s ease-in-out;
                background-color: rgba(255, 243, 205, 0.85);
                border-radius: 4px;
                padding: 2px 6px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
        }

        /* COMPORTAMENTO PARA DISPOSITIVOS MÓVEIS (CELULARES): Zoom global e automático */
        @media (max-width: 767px) {
            .modo-lupa span, 
            .modo-lupa p, 
            .modo-lupa h4, 
            .modo-lupa h5, 
            .modo-lupa h6, 
            .modo-lupa td, 
            .modo-lupa th,
            .modo-lupa small,
            .modo-lupa .fs-2,
            .modo-lupa .text-uppercase,
            .modo-lupa .dropdown-item,
            .modo-lupa .btn {
                font-size: 115% !important; /* Incrementa proporcionalmente o tamanho da fonte global */
                line-height: 1.5 !important;
                transition: font-size 0.2s ease-in-out;
            }
            /* Garante que os números grandes do topo ganhem o destaque ideal no mobile */
            .modo-lupa .fs-2 {
                font-size: 2.2rem !important;
            }
        }
    </style>
</head>
<body>

<button type="button" class="btn-acessibilidade-flutuante" id="btnAcessibilidade" title="Ativar Lupa de Leitura">
    <i class="fa-solid fa-magnifying-glass-plus"></i>
</button>

<nav class="navbar navbar-expand-lg navbar-dark navbar-firjan p-3 shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">SisGAM <span class="badge bg-secondary text-wrap" style="font-size: 0.65em;">FIRJAN SENAI</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <div class="d-flex align-items-center text-white me-3">
                <span class="small"><span id='ola'>Olá</span>, <strong><?= htmlspecialchars($usuario_nome); ?></strong> (<span class="text-warning"><?= $texto_nivel; ?></span>)</span>
            </div>
            
            <div class="dropdown">
              <button class="btn btn-warning dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-cog"></i>
                <span class="d-none d-sm-inline ms-1" id='ferramentas'>FERRAMENTAS</span>
              </button>

              <div class="dropdown-menu dropdown-menu-end">              
                <a class="dropdown-item" href="../relatorios/geral.php">
                  <i class="fas fa-layer-group me-2 text-primary"></i> <span id='relatoriog'>Relatório Geral</span>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="../filtro/">
                  <i class="fas fa-search text-success"></i> <span id='filtro'>Filtro Avançado</span>
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="../logout/">
                  <i class="fas fa-sign-out-alt me-2 text-danger"></i> <span id='sair'>Sair</span>
                </a>
              </div>
            </div>
        </div>
    </div>
</nav>

<div class="language-switcher" style="position: absolute; top: 80px; right: 10px;">
  <img onclick="setLanguage('en')" style="cursor: pointer;" src="https://flagicons.lipis.dev/flags/4x3/us.svg" alt="English Flag" width="30" height="20">
  <img onclick="setLanguage('pt')" style="cursor: pointer;" src="https://flagicons.lipis.dev/flags/4x3/br.svg" alt="Bandeira do Brasil" width="30" height="20">
  <img onclick="setLanguage('es')" style="cursor: pointer;" src="https://flagicons.lipis.dev/flags/4x3/es.svg" alt="Bandeira da Espanha" width="30" height="20">
  <img onclick="setLanguage('fr')" style="cursor: pointer;" src="https://flagicons.lipis.dev/flags/4x3/fr.svg" alt="Bandeira da França" width="30" height="20">   
</div>

<div class="container-fluid my-4 px-4 bloco">

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card card-stats p-3 bg-white border-start border-primary border-4 rounded-3 shadow-sm">
                <div class="text-muted small fw-bold text-uppercase" id='totalacoes'>Total de Ações</div>
                <div class="fs-2 fw-bold text-dark"><?= $total_atividades; ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stats p-3 bg-white border-start border-warning border-4 rounded-3 shadow-sm">
                <div class="text-muted small fw-bold text-uppercase" id='planejamento'>🕒 Em Planejamento</div>
                <div class="fs-2 fw-bold text-warning"><?= $total_planejado; ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-stats p-3 bg-white border-start border-success border-4 rounded-3 shadow-sm">
                <div class="text-muted small fw-bold text-uppercase" id='executadas'>✅ Executadas / Concluídas</div>
                <div class="fs-2 fw-bold text-success"><?= $total_executado; ?></div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-4 col-md-6">
            <div class="card card-grafico p-4 bg-white">
                <h5 class="fw-bold text-dark mb-1" id='acoeseixo'>Ações por Eixo Temático</h5>
                <small class="text-muted d-block mb-3" id='distribuicaoprojeto'>Distribuição volumétrica de projetos</small>
                <div style="position: relative; height:240px; width:100%">
                    <canvas id="chartEixos"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card card-grafico p-4 bg-white">
                <h5 class="fw-bold text-dark mb-1" id='statusespaco'>Status por Espaço Físico</h5>
                <small class="text-muted d-block mb-3" id='acoesplanejadas'>Ações planejadas e concluídas por local</small>
                <div style="position: relative; height:240px; width:100%">
                    <canvas id="chartEspacos"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-12">
            <div class="card card-grafico p-4 bg-white">
                <h5 class="fw-bold text-dark mb-1" id='publicoeixo'>Público por Eixo Temático</h5>
                <small class="text-muted d-block mb-3" id='somaparticipantes'>Soma de participantes em cada categoria</small>
                <div style="position: relative; height:240px; width:100%">
                    <canvas id="chartPublicoEixo"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="table-container bg-white p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-dark" id='acoespedagogicas'>Ações Pedagógicas e Culturais</h4>
                <small class="text-muted" id='memoria'>Memória histórica e monitoramento de atividades</small>
            </div>          
            
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <div class="btn-group" role="group" aria-label="Alterar visualização">
                    <button type="button" class="btn btn-outline-dark active" id="btn-view-tabela" title="Visualizar em Tabela">
                        <i class="fa-solid fa-list"></i>
                    </button>
                    <button type="button" class="btn btn-outline-dark" id="btn-view-cards" title="Visualizar em Cards">
                        <i class="fa-solid fa-table-cells-large"></i>
                    </button>
                </div>

                <?php if ($usuario_nivel === '1'): ?>
                    <a href="../usuarios/" class="btn btn-outline-dark d-flex align-items-center gap-1 fw-bold">
                        <i class="fa-solid fa-users"></i>
                        <span id="geuser">Gerenciar Usuários</span>
                    </a>
                <?php endif; ?>

                <?php if ($usuario_nivel === '1' || $usuario_nivel === '2'): ?>
                    <a href="../cadativ/" class="btn btn-primary d-flex align-items-center gap-1 fw-bold">
                        <i class="fa-solid fa-plus"></i>
                        <span id="cadativ">Cadastrar Atividade</span>
                    </a>
                <?php endif; ?>
            </div>           
        </div>

        <?php
        $atividades_cache = [];
        if ($dados_atividades->num_rows > 0) {
            while($row = $dados_atividades->fetch_assoc()) {
                $atividades_cache[] = $row;
            }
        }
        ?>

        <div id="container-view-tabela" class="table-responsive">
            <table id="tabelaAtividades" class="table table-hover align-middle width-100 table-striped">
                <thead class="table-light">
                    <tr>
                        <th><span id='img'>IMG</span></th>
                        <th><span id='tituloproj'>Título / Projeto</span></th>
                        <th><span id='tituloeixo'>Eixo Temático</span></th>
                        <th><span id='tituloperi'>Periodicidade</span></th>
                        <th><span id='tituloespaco'>Espaço Alocado</span></th>
                        <th class="text-center"><span id='tituloperiodo'>Período</span></th>                        
                        <th class="text-center"><span id='titulostatus'>Status</span></th>
                        <th class="text-center"><span id='tituloescopo'>Escopo</span></th>
                        <th class="text-center no-sort">
                            <?php if ($usuario_nivel === '1' || $usuario_nivel === '2'): ?>
                            <span id='tituloacoes'>Ações</span>
                             <?php endif; ?>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($atividades_cache) > 0): ?>
                        <?php foreach($atividades_cache as $row): ?>
                            <tr>
                                <td>
                                    <img src="../img/img_atividades/<?php echo $row['img']; ?>" width='100' style="border-radius: 6px;">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="fw-bold text-primary"><?= htmlspecialchars($row['titulo']); ?></span>
                                        <?php if (!empty($row['cine_link'])): ?>
                                            <button type="button" class="btn btn-sm btn-outline-info py-0 px-2 rounded-pill d-flex align-items-center gap-1" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#modalVisualizarCine"
                                                    data-titulo="<?= htmlspecialchars($row['titulo']); ?>"
                                                    data-eixo="<?= htmlspecialchars($row['nome_eixo']); ?>"
                                                    data-local="<?= htmlspecialchars($row['nome_espaco']); ?>"
                                                    data-link="<?= htmlspecialchars($row['cine_link']); ?>"
                                                    data-detalhes="<?= htmlspecialchars($row['cine_detalhes']); ?>"
                                                    title="Visualizar Mídia Complementar">
                                                <i class="fa-solid fa-video" style="font-size: 0.85em;"></i> Mídia
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                    <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;" title="<?= htmlspecialchars($row['objetivo']); ?>">
                                        <?= htmlspecialchars($row['objetivo']); ?>
                                    </small>
                                </td>
                                <td><span class="badge bg-secondary"><?= htmlspecialchars($row['nome_eixo']); ?></span></td>
                                <td><?= htmlspecialchars($row['nome_periodicidade']); ?></td>
                                <td><?= htmlspecialchars($row['nome_espaco']); ?></td>
                                <td class="text-center small">
                                    <span class="d-none"><?= $row['data_inicio']; ?></span><?= date('d/m/Y', strtotime($row['data_inicio'])); ?><br>
                                    <span class="text-muted">até</span><br>
                                    <?= date('d/m/Y', strtotime($row['data_fim'])); ?>
                                </td>                                
                                <td class="text-center">
                                    <?php if ($row['status'] === 'Planejado'): ?>
                                        <span class="badge bg-warning text-dark px-3 py-2 fw-bold">🕒 Planejado</span>
                                    <?php else: ?>
                                        <span class="badge bg-success px-3 py-2 fw-bold">Executado</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($row['visibilidade'] === 'Interna'): ?>
                                        <span class="badge bg-danger-subtle text-danger px-2 py-1">🔒 Interna</span>
                                    <?php else: ?>
                                        <span class="badge bg-success-subtle text-success px-2 py-1">🌐 Pública</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <form action="../relatorios/unico.php" method="POST" target="_blank" class="m-0">
                                            <input type="hidden" name="id" value="<?= $row['id_atividade']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary btn-action-size rounded-2" title="Imprimir Ficha Técnica">
                                                <i class="fa-solid fa-print"></i>
                                            </button>
                                        </form>
                                        <?php if ($usuario_nivel === '1' || $usuario_nivel === '2'): ?>
                                        <form action="../editarativ/" method="POST" class="m-0">
                                            <input type="hidden" name="id" value="<?= $row['id_atividade']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-primary btn-action-size rounded-2" title="Editar Atividade">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>
                                        <?php if ($usuario_nivel === '1'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-action-size rounded-2" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalConfirmarExclusao" 
                                                data-id="<?= $row['id_atividade']; ?>"
                                                data-titulo="<?= htmlspecialchars($row['titulo']); ?>"
                                                title="Excluir Atividade">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div id="container-view-cards" class="row g-4 d-none">
            <?php if (count($atividades_cache) > 0): ?>
                <?php foreach($atividades_cache as $row): ?>
                    <div class="col-xl-3 col-lg-4 col-md-6 col-sm-12">
                        <div class="card-atividade-modo d-flex flex-column h-100 border">
                            <img src="../img/img_atividades/<?php echo (!empty($row['img']) ? $row['img'] : 'default.png'); ?>" class="card-img-top-modo" alt="Capa">
                            
                            <div class="card-body p-3 d-flex flex-column flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.75em;"><?= htmlspecialchars($row['nome_eixo']); ?></span>
                                    <?php if ($row['status'] === 'Planejado'): ?>
                                        <span class="badge bg-warning text-dark font-monospace" style="font-size: 0.75em;">🕒 Plan.</span>
                                    <?php else: ?>
                                        <span class="badge bg-success font-monospace" style="font-size: 0.75em;">✅ Exec.</span>
                                    <?php endif; ?>
                                </div>

                                <h6 class="fw-bold text-dark text-truncate-2 mb-1" title="<?= htmlspecialchars($row['titulo']); ?>"><?= htmlspecialchars($row['titulo']); ?></h6>
                                <p class="text-muted small text-truncate-2 mb-3" style="font-size: 0.82em; min-height: 36px;"><?= htmlspecialchars($row['objetivo']); ?></p>
                                
                                <div class="mt-auto pt-2 border-top">
                                    <div class="small text-muted mb-1" style="font-size: 0.8em;">
                                        <i class="fa-solid fa-location-dot text-danger me-1"></i> <?= htmlspecialchars($row['nome_espaco']); ?>
                                    </div>
                                    <div class="small text-muted mb-3" style="font-size: 0.8em;">
                                        <i class="fa-solid fa-calendar text-primary me-1"></i> <?= date('d/m/Y', strtotime($row['data_inicio'])); ?>
                                    </div>

                                    <div class="d-flex gap-2 justify-content-end bg-light p-2 rounded-2">
                                        <?php if (!empty($row['cine_link'])): ?>
                                            <button type="button" class="btn btn-sm btn-outline-info btn-action-size rounded-2" 
                                                    data-bs-toggle="modal" data-bs-target="#modalVisualizarCine"
                                                    data-titulo="<?= htmlspecialchars($row['titulo']); ?>"
                                                    data-eixo="<?= htmlspecialchars($row['nome_eixo']); ?>"
                                                    data-local="<?= htmlspecialchars($row['nome_espaco']); ?>"
                                                    data-link="<?= htmlspecialchars($row['cine_link']); ?>"
                                                    data-detalhes="<?= htmlspecialchars($row['cine_detalhes']); ?>" title="Mídia">
                                                <i class="fa-solid fa-video"></i>
                                            </button>
                                        <?php endif; ?>

                                        <form action="../relatorios/unico.php" method="POST" target="_blank" class="m-0">
                                            <input type="hidden" name="id" value="<?= $row['id_atividade']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-secondary btn-action-size rounded-2" title="Imprimir">
                                                <i class="fa-solid fa-print"></i>
                                            </button>
                                        </form>

                                        <?php if ($usuario_nivel === '1' || $usuario_nivel === '2'): ?>
                                        <form action="../editarativ/" method="POST" class="m-0">
                                            <input type="hidden" name="id" value="<?= $row['id_atividade']; ?>">
                                            <button type="submit" class="btn btn-sm btn-outline-primary btn-action-size rounded-2" title="Editar">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </button>
                                        </form>
                                        <?php endif; ?>

                                        <?php if ($usuario_nivel === '1'): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-action-size rounded-2" 
                                                data-bs-toggle="modal" data-bs-target="#modalConfirmarExclusao" 
                                                data-id="<?= $row['id_atividade']; ?>"
                                                data-titulo="<?= htmlspecialchars($row['titulo']); ?>" title="Excluir">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-4 text-muted">Nenhum registro para exibição em vitrine.</div>
            <?php endif; ?>
        </div>

    </div>
</div>

<div class="modal fade" id="modalVisualizarCine" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">🎬 Detalhes do Desdobramento Complementar</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <small class="text-muted d-block uppercase fw-bold text-xs">Atividade / Projeto</small>
                    <h4 class="text-primary fw-bold mb-1" id="viewCineTitulo"></h4>
                    <span class="badge bg-secondary me-2" id="viewCineEixo"></span>
                    <span class="badge bg-light text-dark border" id="viewCineLocal"></span>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">🌐 URL do Link Cadastrado:</label>
                    <div class="input-group">
                        <input type="text" class="form-control bg-light" id="viewCineLink" readonly>
                        <a href="" id="viewCineLinkBtn" target="_blank" class="btn btn-primary fw-bold">🔗 Abrir Link</a>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold text-dark">📄 Detalhes / Síntese da Obra:</label>
                    <div class="p-3 bg-light rounded border text-secondary" id="viewCineDetalhes" style="white-space: pre-wrap; min-height: 100px;"></div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary fw-bold" data-bs-dismiss="modal">Fechar Visualização</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmarExclusao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">⚠️ Confirmar Exclusão</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Você está prestes a excluir permanentemente a atividade abaixo:</p>
                <p class="fw-bold text-primary fs-5" id="tituloAtividadeExclusao"></p>
                <p class="text-danger small mb-0"><strong>Atenção:</strong> Esta ação não poderá ser desfeita.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                <a href="" id="linkConfirmarExclusao" class="btn btn-danger fw-bold">Sim, Excluir Registro</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalsucesso" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-dark">
                <h5 class="modal-title fw-bold" style="color: white;">Sucesso!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <img src="../img/gif-OK.gif" class="img-fluid" alt="Sucesso">         
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="myModalerro" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-dark">
                <h5 class="modal-title fw-bold" style="color: white;">Erro!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <img src="../img/erro.gif" class="img-fluid" alt="erro">         
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
$(document).ready(function() {
    
    $('#tabelaAtividades').DataTable({
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" },
        "order": [[4, "desc"]], 
        "columnDefs": [ { "orderable": false, "targets": "no-sort" } ],
        "pageLength": 10,
        "lengthMenu": [5, 10, 25, 50]
    });

    $('#btn-view-tabela').on('click', function() {
        $('#btn-view-cards').removeClass('active');
        $(this).addClass('active');
        $('#container-view-cards').addClass('d-none');
        $('#container-view-tabela').removeClass('d-none');
    });

    $('#btn-view-cards').on('click', function() {
        $('#btn-view-tabela').removeClass('active');
        $(this).addClass('active');
        $('#container-view-tabela').addClass('d-none');
        $('#container-view-cards').removeClass('d-none');
    });

    
    $('#btnAcessibilidade').on('click', function() {
        $('body').toggleClass('modo-lupa');
        $(this).toggleClass('ativo');

        const icone = $(this).find('i');
        if ($(this).hasClass('ativo')) {
            icone.removeClass('fa-magnifying-glass-plus').addClass('fa-eye');
            $(this).attr('title', 'Desativar Lupa de Leitura');
        } else {
            icone.removeClass('fa-eye').addClass('fa-magnifying-glass-plus');
            $(this).attr('title', 'Ativar Lupa de Leitura');
        }
    });

    const modalExclusao = document.getElementById('modalConfirmarExclusao');
    if (modalExclusao) {
        modalExclusao.addEventListener('show.bs.modal', function (event) {
            const botao = event.relatedTarget;
            const idAtividade = botao.getAttribute('data-id');
            const tituloAtividade = botao.getAttribute('data-titulo');            
            const textoTitulo = modalExclusao.querySelector('#tituloAtividadeExclusao');
            const linkConfirmacao = modalExclusao.querySelector('#linkConfirmarExclusao');
            textoTitulo.textContent = tituloAtividade;
            linkConfirmacao.href = 'del/index.php?id=' + idAtividade;
        });
    }

    const modalCine = document.getElementById('modalVisualizarCine');
    if (modalCine) {
        modalCine.addEventListener('show.bs.modal', function (event) {
            const botao = event.relatedTarget;
            const titulo = botao.getAttribute('data-titulo');
            const eixo = botao.getAttribute('data-eixo');
            const local = botao.getAttribute('data-local');
            const link = botao.getAttribute('data-link');
            const detalhes = botao.getAttribute('data-detalhes');
            
            modalCine.querySelector('#viewCineTitulo').textContent = titulo;
            modalCine.querySelector('#viewCineEixo').textContent = eixo;
            modalCine.querySelector('#viewCineLocal').textContent = "📍 " + local;
            modalCine.querySelector('#viewCineLink').value = link;
            modalCine.querySelector('#viewCineLinkBtn').href = link;
            modalCine.querySelector('#viewCineDetalhes').textContent = detalhes;
        });
    }

    const ctxEixos = document.getElementById('chartEixos').getContext('2d');
    new Chart(ctxEixos, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($eixos_labels); ?>,
            datasets: [{
                data: <?= json_encode($eixos_valores); ?>,
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom', labels: { boxWidth: 10, font: { size: 10 } } }
            }
        }
    });

    const ctxEspacos = document.getElementById('chartEspacos').getContext('2d');
    new Chart(ctxEspacos, {
        type: 'bar',
        data: {
            labels: <?= json_encode($espacos_labels); ?>,
            datasets: [
                {
                    label: '🕒 Plan.',
                    data: <?= json_encode($total_planejado_valores); ?>,
                    backgroundColor: 'rgba(255, 193, 7, 0.85)',
                    borderColor: '#ffc107',
                    borderWidth: 1,
                    borderRadius: 4
                },
                {
                    label: '✅ Exec.',
                    data: <?= json_encode($total_executado_valores); ?>,
                    backgroundColor: 'rgba(28, 200, 138, 0.85)',
                    borderColor: '#1cc88a',
                    borderWidth: 1,
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, grid: { color: '#eaecf4' }, ticks: { stepSize: 1, font: { size: 10 } } },
                x: { ticks: { font: { size: 10 } }, grid: { display: false } }
            },
            plugins: { legend: { position: 'top', labels: { boxWidth: 10, font: { size: 10 } } } }
        }
    });

    const ctxPublicoEixo = document.getElementById('chartPublicoEixo').getContext('2d');
    new Chart(ctxPublicoEixo, {
        type: 'pie', 
        data: {
            labels: <?= json_encode($eixos_publico_labels); ?>,
            datasets: [{
                data: <?= json_encode($eixos_publico_valores); ?>,
                backgroundColor: ['#36b9cc', '#f6c23e', '#4e73df', '#1cc88a'],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom', 
                    labels: { boxWidth: 10, font: { size: 10 } } 
                }
            }
        }
    });
});
</script>

<?php if(isset($_SESSION['sucesso'])) { ?>
  <script type="text/javascript">    
    $(document).ready(function() {
        var meuModal = new bootstrap.Modal(document.getElementById('myModalsucesso'));
        meuModal.show();
    });
  </script>
<?php } unset($_SESSION['sucesso']); ?>

<?php if(isset($_SESSION['erro'])) { ?>
  <script type="text/javascript">    
    $(document).ready(function() {
        var meuModal = new bootstrap.Modal(document.getElementById('myModalerro'));
        meuModal.show();
    });
  </script>
<?php } unset($_SESSION['erro']); ?>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    setLanguage('pt');       
  });

  function setLanguage(lang) {
    const translations = {
      pt: {
            ola: "Olá",
            ferramentas: "Ferramentas",
            sair: "Sair",
            relatoriog: "Relatório Geral",
            totalacoes: "Total de Ações",
            planejamento: "Em Planejamento",
            executadas: "Executadas / Concluídas",
            acoeseixo: "Ações por Eixo Temático",
            distribuicaoprojeto: "Distribuição volumétrica de projetos",
            statusespaco: "Status por Espaço Físico",
            acoesplanejadas: "Ações planejadas e concluídas por local",
            publicoeixo: "Público por Eixo Temático",
            somaparticipantes: "Soma de participantes em cada categoria",
            acoespedagogicas: "Ações Pedagógicas e Culturais",
            memoria: "Memória histórica e monitoramento de atividades",
            geuser: "Gerenciar Usuários",
            cadativ: "Cadastrar Atividade",
            tituloproj: "Título / Projeto",
            tituloeixo: "Eixo Temático",
            tituloperi: "Periodicidade",
            tituloespaco: "Espaço Alocado",
            tituloperiodo: "Período",
            titulostatus: "Status",
            tituloescopo: "Escopo",
            tituloacoes: "Ações",
            filtro: "Filtro Avançado",            
          },
      en: {
            ola: "Hello",
            ferramentas: "Tools",
            sair: "Logout",
            relatoriog: "General Report",
            totalacoes: "Total Actions",
            planejamento: "In Planning",
            executadas: "Executed / Completed",
            acoeseixo: "Actions by Thematic Axis",
            distribuicaoprojeto: "Volumetric Project Distribution",
            statusespaco: "Status by Physical Space",
            acoesplanejadas: "Planned and Completed Actions by Location",
            publicoeixo: "Audience by Thematic Axis",
            somaparticipantes: "Sum of Participants in Each Category",
            acoespedagogicas: "Pedagogical and Cultural Actions",
            memoria: "Historical Memory and Activity Monitoring",
            geuser: "Manage Users",
            cadativ: "Register Activity",
            tituloproj: "Title / Project",
            tituloeixo: "Thematic Axis",
            tituloperi: "Frequency",
            tituloespaco: "Allocated Space",
            tituloperiodo: "Period",
            titulostatus: "Status",
            tituloescopo: "Scope",
            tituloacoes: "Actions",
            filtro: "Advanced Filter",              
        },
      es: {
            ola: "Hola",
            ferramentas: "Herramientas",
            sair: "Salir",
            relatoriog: "Informe General",
            totalacoes: "Total de Acciones",
            planejamento: "En Planificación",
            executadas: "Ejecutadas / Completadas",
            acoeseixo: "Acciones por Eje Temático",
            distribuicaoprojeto: "Distribución volumétrica de proyectos",
            statusespaco: "Estado por Espacio Físico",
            acoesplanejadas: "Acciones planificadas y completadas por ubicación",
            publicoeixo: "Público por Eje Temático",
            somaparticipantes: "Suma de participantes en cada categoría",
            acoespedagogicas: "Acciones Pedagógicas e Culturales",
            memoria: "Memoria histórica y monitoreo de actividades",
            geuser: "Gestionar Usuarios",
            cadativ: "Registrar Actividad",
            tituloproj: "Título / Proyecto",
            tituloeixo: "Eje Temático",
            tituloperi: "Periodicidade",
            tituloespaco: "Espacio Asignado",
            tituloperiodo: "Período",
            titulostatus: "Estado",
            tituloescopo: "Alcance",
            tituloacoes: "Acciones",  
            filtro: "Filtro avanzado",            
        },
        fr: {
            ola: "Bonjour",
            ferramentas: "Outils",
            sair: "Se déconnecter",
            relatoriog: "Rapport Général",
            totalacoes: "Total des Actions",
            planejamento: "En Planification",
            executadas: "Exécutées / Terminées",
            acoeseixo: "Actions par Axe Thématique",
            distribuicaoprojeto: "Distribution volumétrique des projets",
            statusespaco: "État par Espace Physique",
            acoesplanejadas: "Actions planifiées et terminées par emplacement",
            publicoeixo: "Public par Axe Thématique",
            somaparticipantes: "Somme des participants dans chaque catégorie",
            acoespedagogicas: "Actions Pédagogiques et Culturelles",
            memoria: "Mémoire historique et suivi des activités",
            geuser: "Gérer les Utilisateurs",
            cadativ: "Enregistrer l'Activité",
            tituloproj: "Titre / Projet",
            tituloeixo: "Axe Thématique",
            tituloperi: "Périodicité",
            tituloespaco: "Espace Alloué",
            tituloperiodo: "Période",
            titulostatus: "Statut",
            tituloescopo: "Portée",
            tituloacoes: "Actions",  
            filtro: "Filtre avancé",            
        },    
    };

    const t = translations[lang];
    
    if(document.getElementById("ola")) document.getElementById("ola").innerText = t.ola;
    if(document.getElementById("ferramentas")) document.getElementById("ferramentas").innerText = t.ferramentas;
    if(document.getElementById("sair")) document.getElementById("sair").innerText = t.sair;
    if(document.getElementById("relatoriog")) document.getElementById("relatoriog").innerText = t.relatoriog;
    if(document.getElementById("totalacoes")) document.getElementById("totalacoes").innerText = t.totalacoes;
    if(document.getElementById("planejamento")) document.getElementById("planejamento").innerText = t.planejamento;
    if(document.getElementById("executadas")) document.getElementById("executadas").innerText = t.executadas;
    if(document.getElementById("acoeseixo")) document.getElementById("acoeseixo").innerText = t.acoeseixo;
    if(document.getElementById("distribuicaoprojeto")) document.getElementById("distribuicaoprojeto").innerText = t.distribuicaoprojeto;
    if(document.getElementById("statusespaco")) document.getElementById("statusespaco").innerText = t.statusespaco;
    if(document.getElementById("acoesplanejadas")) document.getElementById("acoesplanejadas").innerText = t.acoesplanejadas;
    if(document.getElementById("publicoeixo")) document.getElementById("publicoeixo").innerText = t.publicoeixo;
    if(document.getElementById("somaparticipantes")) document.getElementById("somaparticipantes").innerText = t.somaparticipantes;
    if(document.getElementById("acoespedagogicas")) document.getElementById("acoespedagogicas").innerText = t.acoespedagogicas;
    if(document.getElementById("memoria")) document.getElementById("memoria").innerText = t.memoria;
    if(document.getElementById("geuser")) document.getElementById("geuser").innerText = t.geuser;
    if(document.getElementById("cadativ")) document.getElementById("cadativ").innerText = t.cadativ;
    if(document.getElementById("tituloproj")) document.getElementById("tituloproj").innerText = t.tituloproj;
    if(document.getElementById("tituloeixo")) document.getElementById("tituloeixo").innerText = t.tituloeixo;
    if(document.getElementById("tituloperi")) document.getElementById("tituloperi").innerText = t.tituloperi;
    if(document.getElementById("tituloespaco")) document.getElementById("tituloespaco").innerText = t.tituloespaco;
    if(document.getElementById("tituloperiodo")) document.getElementById("tituloperiodo").innerText = t.tituloperiodo;
    if(document.getElementById("titulostatus")) document.getElementById("titulostatus").innerText = t.titulostatus;
    if(document.getElementById("tituloescopo")) document.getElementById("tituloescopo").innerText = t.tituloescopo;
    if(document.getElementById("tituloacoes")) document.getElementById("tituloacoes").innerText = t.tituloacoes;
    if(document.getElementById("filtro")) document.getElementById("filtro").innerText = t.filtro;
  }
</script>
</body>
</html>