<?php
session_start();
require_once '../conexao/conexao.php';

$usuario_nivel = $_SESSION['usuario_nivel'];

if(!in_array($usuario_nivel, ['4'])){
    header("location: ../");
    exit;
}

$id_usuario_logado = isset($_SESSION['usuario_id']) ? intval($_SESSION['usuario_id']) : 0;

$sql_vitrine = "SELECT a.*, e.nome_eixo, esp.nome_espaco 
        FROM atividade a
        INNER JOIN eixo_tematico e ON a.fk_eixo = e.id_eixo
        INNER JOIN espaco_fisico esp ON a.fk_espaco = esp.id_espaco
        WHERE a.visibilidade = 'Publica'
        ORDER BY a.data_inicio DESC";

$dados_vitrine = $con->query($sql_vitrine);

if (!$dados_vitrine) {
    die("<h3>Erro ao carregar a vitrine de atividades:</h3>" . $con->error);
}

$sql_inscricoes = "SELECT ie.id_inscricao, ie.data_inscricao, a.*, e.nome_eixo, esp.nome_espaco 
                   FROM inscricao_evento ie
                   INNER JOIN atividade a ON ie.fk_atividade = a.id_atividade
                   INNER JOIN eixo_tematico e ON a.fk_eixo = e.id_eixo
                   INNER JOIN espaco_fisico esp ON a.fk_espaco = esp.id_espaco
                   WHERE ie.fk_id_usuario = ?
                   ORDER BY ie.data_inscricao DESC";

$stmt_ins = $con->prepare($sql_inscricoes);
$stmt_ins->bind_param("i", $id_usuario_logado);
$stmt_ins->execute();
$dados_inscricoes = $stmt_ins->get_result();
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
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">SisGAM <span class="badge bg-secondary ms-1" style="font-size: 0.65em;">EVENTOS</span></a>
        <a href="../logout/" class="btn btn-outline-light btn-sm fw-bold">Sair</a>
    </div>
</nav>

<?php if(isset($_SESSION['result'])){ ?>
    <?php if($_SESSION['result']==1){ ?>
        <div class="alert alert-success alert-dismissible fade show fw-semibold" id="alertsucesso" role="alert">
            ✅ Operação realizada com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script type="text/javascript">
          setTimeout(function () {
              $("#alertsucesso").css("display", "none");
          }, 3000);
        </script>
    <?php }else{ ?>
        <div class="alert alert-danger alert-dismissible fade show fw-semibold" id="alerterro" role="alert">
            ❌ Ocorreu um erro ao processar a requisição. Verifique os dados.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script type="text/javascript">
          setTimeout(function () {
              $("#alerterro").css("display", "none");
          }, 3000);
        </script>
    <?php } ?>        
<?php } unset($_SESSION['result']); ?>

<div class="container my-5">

    <?php if ($dados_inscricoes->num_rows > 0): ?>
        <div class="mb-5 p-4 bg-light rounded-4 border">
            <div class="d-flex align-items-center gap-2 mb-4">
                <i class="fa-solid fa-circle-check text-success fs-3"></i>
                <div>
                    <h3 class="fw-bold text-dark mb-0">Minhas Inscrições Confirmadas</h3>
                    <small class="text-muted">Ações em que você garantiu presença</small>
                </div>
            </div>
            
            <div class="row g-4">
                <?php while($insc = $dados_inscricoes->fetch_assoc()): ?>
                    <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                        <div class="card-evento border border-success-subtle position-relative shadow-sm bg-white" style="border-radius: 12px; overflow: hidden;">
                            
                            <div class="position-absolute top-0 end-0 m-2 z-3">
                                <span class="badge bg-success p-2 rounded-circle shadow-sm" title="Inscrição Confirmada">
                                    <i class="fa-solid fa-check"></i>
                                </span>
                            </div>

                            <div class="card-body p-3">
                                <span class="badge bg-secondary mb-2"><i class="fa-solid fa-bookmark me-1"></i> <?= htmlspecialchars($insc['nome_eixo']); ?></span>
                                <h5 class="fw-bold text-dark text-truncate-2 mb-2" style="min-height: 44px; line-height: 1.3;">
                                    <?= htmlspecialchars($insc['titulo']); ?>
                                </h5>
                                
                                <div class="meta-info small text-muted mb-1">
                                    <i class="fa-solid fa-calendar-days text-success me-1"></i> 
                                    <strong>Período:</strong> <?= date('d/m/Y', strtotime($insc['data_inicio'])); ?> a <?= date('d/m/Y', strtotime($insc['data_fim'])); ?>
                                </div>
                                <div class="meta-info small text-muted mb-3">
                                    <i class="fa-solid fa-location-dot text-danger me-1"></i> 
                                    <strong>Local:</strong> <?= htmlspecialchars($insc['nome_espaco']); ?>
                                </div>

                                <button type="button" 
                                        class="btn btn-sm btn-outline-danger w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2 btn-cancelar-inscricao"
                                        data-id-inscricao="<?= $insc['id_inscricao']; ?>"
                                        data-titulo="<?= htmlspecialchars($insc['titulo']); ?>">
                                    <i class="fa-solid fa-user-minus"></i> Cancelar Inscrição
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="text-center mb-5">
        <h1 class="fw-bold text-dark">Exposição de Atividades e Projetos</h1>
        <p class="text-muted">Explore as ações pedagógicas da Midiateca FIRJAN SENAI Sapucaí e garanta sua vaga</p>
        <hr class="w-25 mx-auto border-primary border-2">
    </div>

    <div class="row g-4">
        <?php if ($dados_vitrine->num_rows > 0): ?>
            <?php while($item = $dados_vitrine->fetch_assoc()): ?>
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-12">
                    <div class="card-evento">
                        
                        <div class="img-container">
                            <?php 
                            $caminho_foto = "../img/img_atividades/" . $item['img'];
                            if (!empty($item['img']) && file_exists($caminho_foto)): 
                            ?>
                                <img src="<?= $caminho_foto; ?>" class="card-evento-img" alt="Imagem da atividade">
                            <?php else: ?>
                                <img src="https://images.unsplash.com/photo-1506784983877-45594efa4cbe?q=80&w=600&auto=format&fit=crop" class="card-evento-img" alt="Banner padrão">
                            <?php endif; ?>
                            <span class="badge-eixo"><i class="fa-solid fa-bookmark me-1"></i> <?= htmlspecialchars($item['nome_eixo']); ?></span>
                        </div>

                        <div class="card-body-evento">
                            <h5 class="fw-bold text-dark mb-2 text-truncate-2" style="min-height: 48px; line-height: 1.3;">
                                <?= htmlspecialchars($item['titulo']); ?>
                            </h5>
                            
                            <p class="text-muted small text-truncate-3 mb-3" style="min-height: 54px; font-size: 0.9rem;">
                                <?= htmlspecialchars($item['objetivo']); ?>
                            </p>

                            <div class="mt-auto">
                                <div class="meta-info">
                                    <i class="fa-solid fa-calendar-days text-primary me-2"></i>
                                    <strong>Período:</strong> <?= date('d/m/Y', strtotime($item['data_inicio'])); ?> até <?= date('d/m/Y', strtotime($item['data_fim'])); ?>
                                </div>
                                <div class="meta-info mb-4">
                                    <i class="fa-solid fa-location-dot text-danger me-2"></i>
                                    <strong>Local:</strong> <?= htmlspecialchars($item['nome_espaco']); ?>
                                </div>

                                <button type="button" 
                                        class="btn btn-primary w-100 fw-bold d-flex align-items-center justify-content-center gap-2 py-2 shadow-sm btn-inscrever"
                                        data-id="<?= $item['id_atividade']; ?>"
                                        data-titulo="<?= htmlspecialchars($item['titulo']); ?>"
                                        data-local="<?= htmlspecialchars($item['nome_espaco']); ?>">
                                    <i class="fa-solid fa-user-plus"></i> Realizar Inscrição
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-calendar-xmark text-muted fs-1 mb-3"></i>
                <h4 class="text-secondary">Nenhuma atividade disponível para exposição no momento.</h4>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="modal fade" id="modalConfirmarInscricao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content style-modal" style="border-radius: 16px; border: none;">
            <div class="modal-header bg-primary text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-circle-check"></i> Confirmar Inscrição</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="crud/" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_atividade" id="modalIdAtividade">
                    
                    <p class="fs-6 text-secondary">Você está confirmando sua participação na seguinte ação da Midiateca:</p>
                    <h4 class="fw-bold text-primary my-3" id="modalTituloAtividade"></h4>
                    
                    <div class="p-3 bg-light rounded-3 border d-flex align-items-center gap-2">
                        <i class="fa-solid fa-location-dot text-danger fs-5"></i>
                        <div>
                            <small class="text-muted d-block uppercase fw-bold" style="font-size: 0.75rem;">Espaço Reservado</small>
                            <span class="fw-semibold text-dark" id="modalLocalAtividade"></span>
                        </div>
                    </div>
                    
                    <p class="text-muted small mt-3 mb-0"><i class="fa-solid fa-circle-info"></i> Lembre-se de comparecer ao local no horário estipulado portando seu crachá institucional.</p>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <input type="hidden" name="tipo" value="1">
                    <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Desistir</button>
                    <button type="submit" class="btn btn-success px-4 fw-bold">Confirmar Presença</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCancelarInscricao" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none;">
            <div class="modal-header bg-danger text-white" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-triangle-exclamation"></i> Cancelar Inscrição</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form action="crud/" method="POST">
                <div class="modal-body p-4">
                    <input type="hidden" name="id_inscricao" id="modalIdInscricao">
                    
                    <p class="fs-6 text-secondary">Você deseja abrir mão da sua vaga no projeto abaixo?</p>
                    <h5 class="fw-bold text-danger my-3" id="modalCancelTituloAtividade"></h5>
                    <p class="text-muted small mb-0"><strong>Aviso:</strong> Essa ação liberará a vaga para outros colaboradores da unidade imediatamente.</p>
                </div>
                <div class="modal-footer bg-light" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                    <input type="hidden" name="tipo" value="2">
                    <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Voltar</button>
                    <button type="submit" class="btn btn-danger px-4 fw-bold">Sim, Cancelar Vaga</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {    
    
    $('.btn-inscrever').on('click', function() {
        const idAtividade = $(this).data('id');
        const titulo     = $(this).data('titulo');
        const local      = $(this).data('local');
        
        $('#modalIdAtividade').val(idAtividade);
        $('#modalTituloAtividade').text(titulo);
        $('#modalLocalAtividade').text(local);
        
        var meuModal = new bootstrap.Modal(document.getElementById('modalConfirmarInscricao'));
        meuModal.show();
    });

    
    $('.btn-cancelar-inscricao').on('click', function() {
        const idInscricao = $(this).data('id-inscricao');
        const tituloAtv   = $(this).data('titulo');
        
        $('#modalIdInscricao').val(idInscricao);
        $('#modalCancelTituloAtividade').text(tituloAtv);
        
        var modalCancel = new bootstrap.Modal(document.getElementById('modalCancelarInscricao'));
        modalCancel.show();
    });
});
</script>
</body>
</html>