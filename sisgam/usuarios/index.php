<?php
session_start();
require_once '../auth/session.php';
require_once '../conexao/conexao.php';


if ($_SESSION['usuario_nivel'] !== '1') {
    header("Location: ../home/");
    exit();
}

$usuario_nome  = $_SESSION['usuario_nome'];
$usuario_nivel = $_SESSION['usuario_nivel'];

if($usuario_nivel==1){
    $texto_nivel = "Adm";
}elseif($usuario_nivel==2){
    $texto_nivel = "Gestor";
}elseif($usuario_nivel==3){
    $texto_nivel = "Colaborador";
}


$sql_usuarios = "SELECT id_usuario, nome, email, nivel_permissao, data_criacao, idade, sexo FROM usuario ORDER BY nome ASC";
$dados_usuarios = $con->query($sql_usuarios);

if (!$dados_usuarios) {
    die("<h3>Erro ao carregar usuários:</h3>" . $con->error);
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SisGAM - Gerenciar Usuários</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="icon" type="image/png" href="../img/logo.png" />
    <link rel="stylesheet" type="text/css" href="css/estilo.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-firjan p-3 shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="index.php">SisGAM <span class="badge bg-secondary text-wrap" style="font-size: 0.65em;">FIRJAN SENAI</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <div class="d-flex align-items-center text-white me-3">
                <span class="small">Olá, <strong><?= htmlspecialchars($usuario_nome); ?></strong> (<span class="text-warning"><?= $texto_nivel; ?></span>)</span>
            </div>
            <a href="../home/" class="btn btn-outline-light btn-sm fw-bold me-2">Voltar ao Painel</a>
            <a href="../logout/" class="btn btn-outline-light btn-sm fw-bold">Sair</a>
        </div>
    </div>
</nav>

<div class="container-fluid my-4 px-4">

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
    

    <div class="table-container bg-white p-4 rounded shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-dark">Controle de Usuários e Acessos</h4>
                <small class="text-muted">Gerenciamento de credenciais e níveis de permissão do sistema</small>
            </div>
            
            <button type="button" class="btn btn-primary d-flex align-items-center gap-1 fw-bold" data-bs-toggle="modal" data-bs-target="#modalCadastrarUsuario">
                <span>➕</span> Novo Usuário
            </button>
        </div>

        <div class="table-responsive">
            <table id="tabelaUsuarios" class="table table-hover align-middle width-100 table-striped">
                <thead class="table-light">
                    <tr>
                        <th>Nome Completo</th>
                        <th>E-mail (Login)</th>
                        <th>Nível de Acesso</th>
                        <th>Data de Cadastro</th>
                        <th class="text-center no-sort">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dados_usuarios->num_rows > 0): ?>
                        <?php while($row = $dados_usuarios->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <span class="fw-bold text-dark d-block"><?= htmlspecialchars($row['nome']); ?></span>
                                </td>
                                <td><code><?= htmlspecialchars($row['email']); ?></code></td>
                                <td>
                                    <?php if ($row['nivel_permissao'] === 'Administrativo'): ?>
                                        <span class="badge bg-danger px-3 py-2 fw-bold">⚙️ Administrativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark px-3 py-2 fw-bold">👁️ Visualização Interna</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($row['data_creation'] ?? $row['data_criacao'])); ?></td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary btn-action-size rounded-2" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalEditarUsuario"
                                                data-id="<?= $row['id_usuario']; ?>"
                                                data-nome="<?= htmlspecialchars($row['nome']); ?>"
                                                data-email="<?= htmlspecialchars($row['email']); ?>"
                                                data-nivel="<?= $row['nivel_permissao']; ?>"
                                                data-idade="<?= $row['idade']; ?>"
                                                data-sexo="<?= $row['sexo']; ?>"
                                                title="Editar Usuário">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-danger btn-action-size rounded-2"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#modalConfirmarExclusaoUsuario" 
                                                data-id="<?= $row['id_usuario']; ?>"
                                                data-nome="<?= htmlspecialchars($row['nome']); ?>"
                                                title="Excluir Usuário">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalCadastrarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">➕ Cadastrar Novo Usuário</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="crud/index.php?acao=cadastrar" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome Completo</label>
                        <input type="text" name="nome" class="form-control" placeholder="Ex: João Silva" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail Corporativo</label>
                        <input type="email" name="email" class="form-control" placeholder="Ex: joao@instituicao.com.br" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Senha de Acesso</label>
                        <input type="password" name="senha" class="form-control" placeholder="Digite uma senha segura" required>
                    </div>
                    <div class="mb-3">
                        <label for="idade" class="form-label fw-bold">Idade</label>
                        <input type="number" class="form-control" id="idade" name="idade" placeholder="..." required >
                    </div>
                    <div class="mb-3">
                    <label for="sexo" class="form-label fw-bold">Sexo</label>
                        <select class="form-control" name="sexo" required="">
                            <option value="">--selecione--</option>
                            <option value="1">Masculino</option>
                            <option value="2">Feminino</option>
                            <option value="3">Outros</option>
                        </select>
                    </div>                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nível de Permissão</label>
                        <select name="nivel_permissao" class="form-select" required>
                            <option value="">--selecione--</option>
                            <option value="1">Administrativo</option>
                            <option value="2">Gestor</option>
                            <option value="3">Colaborador</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary fw-bold">Salvar Usuário</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold">📝 Editar Credenciais</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="crud/index.php?acao=atualizar" method="POST">
                <input type="hidden" name="id" id="editUsuarioId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nome Completo</label>
                        <input type="text" name="nome" id="editUsuarioNome" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">E-mail Corporativo</label>
                        <input type="email" name="email" id="editUsuarioEmail" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nova Senha <small class="text-muted">(Deixe em branco para manter a atual)</small></label>
                        <input type="password" name="senha" class="form-control" placeholder="Preencha apenas se for alterar">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Idade</label>
                        <input type="number" class="form-control" id="editUsuarioIdade" name="idade" placeholder="..." required >
                    </div>
                    <div class="mb-3">
                    <label class="form-label fw-bold">Sexo</label>
                        <select class="form-control" id="editUsuarioSexo" name="sexo" required="">
                            <option value="">--selecione--</option>
                            <option value="1">Masculino</option>
                            <option value="2">Feminino</option>
                            <option value="3">Outros</option>
                        </select>
                    </div>                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nível de Permissão</label>
                        <select name="nivel_permissao" id="editUsuarioNivel" class="form-select" required>
                            <option value="">--selecione--</option>
                            <option value="1">Administrativo</option>
                            <option value="2">Gestor</option>
                            <option value="3">Colaborador</option>
                        </select>
                    </div>

                    <!-- <div class="mb-3">
                        <label class="form-label fw-bold">Nível de Permissão</label>
                        <select name="nivel_permissao" id="editUsuarioNivel" class="form-select" required>
                            <option value="Visualizacao">Visualização Interna</option>
                            <option value="Administrativo">Administrativo</option>
                        </select>
                    </div> -->
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning fw-bold text-dark">Atualizar Dados</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalConfirmarExclusaoUsuario" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">⚠️ Confirmar Remoção</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Você está prestes a excluir permanentemente o usuário abaixo:</p>
                <p class="fw-bold text-danger fs-5" id="nomeUsuarioExclusao"></p>
                <p class="small text-muted mb-0">Esta ação removerá as credenciais e o usuário perderá o acesso ao SisGAM.</p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary fw-semibold" data-bs-dismiss="modal">Cancelar</button>
                <a href="" id="linkExcluirUsuario" class="btn btn-danger fw-bold">Sim, Excluir Usuário</a>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    $('#tabelaUsuarios').DataTable({
        "language": { "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json" },
        "columnDefs": [ { "orderable": false, "targets": "no-sort" } ],
        "pageLength": 10
    });

    const modalEditar = document.getElementById('modalEditarUsuario');
    if (modalEditar) {
        modalEditar.addEventListener('show.bs.modal', function (event) {
            const botao = event.relatedTarget;
            document.getElementById('editUsuarioId').value = botao.getAttribute('data-id');
            document.getElementById('editUsuarioNome').value = botao.getAttribute('data-nome');
            document.getElementById('editUsuarioEmail').value = botao.getAttribute('data-email');
            document.getElementById('editUsuarioNivel').value = botao.getAttribute('data-nivel');
            document.getElementById('editUsuarioIdade').value = botao.getAttribute('data-idade');
            document.getElementById('editUsuarioSexo').value = botao.getAttribute('data-sexo');
        });
    }

    const modalExclusao = document.getElementById('modalConfirmarExclusaoUsuario');
    if (modalExclusao) {
        modalExclusao.addEventListener('show.bs.modal', function (event) {
            const botao = event.relatedTarget;
            const id = botao.getAttribute('data-id');
            const nome = botao.getAttribute('data-nome');
            document.getElementById('nomeUsuarioExclusao').textContent = nome;
            document.getElementById('linkExcluirUsuario').href = 'crud/index.php?acao=deletar&id=' + id;
        });
    }
});
</script>
</body>
</html>