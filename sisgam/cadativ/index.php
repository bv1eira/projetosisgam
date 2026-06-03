<?php
session_start();
require_once '../conexao/conexao.php';

$eixos = $con->query("SELECT * FROM eixo_tematico");

$periodicidades = $con->query("SELECT * FROM periodicidade");

$espacos = $con->query("SELECT * FROM espaco_fisico");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SisGAM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="../img/logo.png" />
    <link rel="stylesheet" type="text/css" href="css/estilo.css">    
</head>
<body>

<div class="container my-5">
    <div class="card">
        <div class="p-4 header-firjan d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">SisGAM</h2>
                <small>Novo Cadastro de Atividade - Padrão FIRJAN | SENAI</small>
            </div>
            <a href="../home/" class="btn btn-outline-light btn-sm">Voltar ao Painel</a>
        </div>
        
        <div class="card-body p-4">
            <form action="insert/" method="POST" id="formAtividade" enctype="multipart/form-data">
                
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="titulo" class="form-label fw-bold">Título / Projeto *</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" required>
                    </div>
                    <div class="col-md-4">
                        <label for="status" class="form-label fw-bold">Status de Execução *</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="Planejado">Planejado</option>
                            <option value="Executado">Executado</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="objetivo" class="form-label fw-bold">Objetivo *</label>
                        <textarea class="form-control" id="objetivo" name="objetivo" rows="2" required></textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="publico_alvo" class="form-label fw-bold">Público-Alvo *</label>
                        <input type="text" class="form-control" id="publico_alvo" name="publico_alvo" placeholder="Ex: Alunos de TI, Comunidade" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="fk_eixo" class="form-label fw-bold">Eixo Temático *</label>
                        <select class="form-select" id="fk_eixo" name="fk_eixo" required onchange="verificarEixo(this.value)">
                            <option value="">Selecione...</option>
                            <?php while($e = $eixos->fetch_assoc()): ?>
                                <option value="<?= $e['id_eixo'] ?>"><?= $e['nome_eixo'] ?></option>
                            <?php endwhile; ?> 
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="fk_periodicidade" class="form-label fw-bold">Periodicidade *</label>
                        <select class="form-select" id="fk_periodicidade" name="fk_periodicidade" required>
                            <option value="">Selecione...</option>
                            <?php while($p = $periodicidades->fetch_assoc()): ?>
                                <option value="<?= $p['id_periodicidade'] ?>"><?= $p['nome_periodicidade'] ?></option>
                            <?php endwhile; ?> 
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="fk_espaco" class="form-label fw-bold">Alocação de Espaço Físico *</label>
                        <select class="form-select" id="fk_espaco" name="fk_espaco" required>
                            <option value="">Selecione...</option>
                            <?php while($esp = $espacos->fetch_assoc()): ?>
                                <option value="<?= $esp['id_espaco'] ?>"><?= $esp['nome_espaco'] ?> (Máx: <?= $esp['capacidade_maxima'] ?>)</option>
                            <?php endwhile; ?> 
                        </select>
                    </div>
                </div>

                <div id="secao_cine" class="p-3 mb-3 rounded shadow-sm" style="display: none;">
                    <h5 class="text-warning-emphasis mb-3 fw-bold">🎬 Desdobramento Complementar</h5>
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="link" class="form-label fw-bold">URL do Link *</label>
                            <input type="url" class="form-control" id="link" name="link" placeholder="https://exemplo.com">
                        </div>
                        <div class="col-md-12">
                            <label for="detalhes" class="form-label fw-bold">Detalhes *</label>
                            <textarea class="form-control" id="detalhes" name="detalhes" rows="3" placeholder="Insira os detalhes adicionais aqui..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-4">
                        <label for="data_inicio" class="form-label fw-bold">Data Início *</label>
                        <input type="date" class="form-control" id="data_inicio" name="data_inicio" required>
                    </div>
                    <div class="col-md-4">
                        <label for="data_fim" class="form-label fw-bold">Data Fim *</label>
                        <input type="date" class="form-control" id="data_fim" name="data_fim" required>
                    </div> 
                    <div class="col-md-4">
                        <label for="quantidade" class="form-label fw-bold">Qtd Público *</label>
                        <input type="number" class="form-control" id="quantidade" name="quantidade" required>
                    </div>                     
                </div>

                <div class="row mb-4">
                    <div class="col-md-8 mb-3 mb-md-0">
                        <label for="observacoes" class="form-label fw-bold">Observações Gerais</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="2"></textarea>
                        
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Escopo de Visibilidade *</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="visibilidade" id="vis_publica" value="Publica" checked>
                            <label class="form-check-label" for="vis_publica">Pública (Outros setores visualizam)</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="visibilidade" id="vis_interna" value="Interna">
                            <label class="form-check-label" for="vis_interna">Interna (Gestão da Biblioteca apenas)</label>
                        </div>                        
                    </div>
                    <div class="col-md-12">
                        <div class="mt-3" style="text-align: center;">
                            <label for="imagem_atividade" class="form-label fw-bold">Foto da Atividade <small class="text-muted">(Opcional - JPG, JPEG ou PNG)</small></label>
                            <input type="file" class="form-control" id="imagem_atividade" name="imagem_atividade" accept="image/png, image/jpeg, image/jpg" onchange="gerarPreview(this)">
                            <div id="container-preview" class="preview-container mt-3 text-center">
                                <img id="img-preview" src="../img/semfoto.png" alt="Preview da Imagem" class="img-fluid">
                            </div>
                        </div>
                    </div>
                    
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2" onclick="limparPreview()">Limpar</button>
                    <button type="submit" class="btn btn-primary px-4">Salvar Atividade</button>
                </div>
            </form>
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

<script>
function verificarEixo(idEixo) {
    const secaoCine = document.getElementById('secao_cine');
    const camposCine = secaoCine.querySelectorAll('input, textarea');   
    
    if (idEixo === '4') {
        secaoCine.style.display = 'block';
        camposCine.forEach(campo => campo.setAttribute('required', 'required'));
    } else {
        secaoCine.style.display = 'none';
        camposCine.forEach(campo => {
            campo.removeAttribute('required');
            campo.value = ''; 
        });
    }
}


function gerarPreview(input) {
    const container = document.getElementById('container-preview');
    const imagem = document.getElementById('img-preview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            imagem.src = e.target.result;
            container.style.display = 'block'; 
        }

        reader.readAsDataURL(input.files[0]);
    } else {
        limparPreview();
    }
}


function limparPreview() {
    document.getElementById('container-preview').style.display = 'none';
    document.getElementById('img-preview').src = '#';
}
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

</body>
</html>