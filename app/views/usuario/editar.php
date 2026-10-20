<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/geralUsuario.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/formulariosAdministrador.css">
    <link rel="stylesheet" href="<?= URL_BASE ?>/assets/css/perfil.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <title>LudoSkill - Editar Perfil</title>
</head>
<body>
    <header>
        <?php
        $perfil = $usuario->getPerfil();
        if ($perfil === 'admin') {
            include_once __DIR__ . '/../includes/menuAdministrador.html';
        } elseif ($perfil === 'gestor') {
            include_once __DIR__ . '/../includes/menuGestor.php';
        } else {
            include_once __DIR__ . '/../includes/menuFuncionario.php';
        }
        ?>
    </header>

    <main>
        <div class="container pagina-editar-perfil">
            <div id="topo">
                <a class="botao brilho" href="<?= URL_BASE ?>/perfil"><i class="bi bi-arrow-left"></i> Voltar</a>
                <div>
                    <h1>Edição de Perfil</h1>
                    <p class="mensagem">Mantenha seus dados cadastrais atualizados.</p>
                </div>
            </div>


            <?php
            $valores = $dados ?? [];
            $nomeAtual = $valores['nome_completo'] ?? $usuario->getNomeCompleto();
            $dataAtual = $valores['data_nascimento'] ?? ($usuario->getDataNascimento()?->format('Y-m-d') ?? '');
            $emailAtual = $valores['email'] ?? $usuario->getEmail();
            $telefoneAtual = $valores['numero_telefone'] ?? ($usuario->getNumeroTelefone() ?? '');

            $cpfLimpo = preg_replace('/\D/', '', $usuario->getCpf() ?? '');
            $cpfFormatado = strlen($cpfLimpo) === 11 
                ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfLimpo) 
                : ($usuario->getCpf() ?? '');
            ?>

            <form class="card" action="<?= URL_BASE ?>/perfil/atualizar" method="post" id="form-editar-perfil">
                
                <!-- Nome completo -->
                <div class="formgroup">
                    <label for="nome_completo"><i class="bi bi-person"></i> Nome completo:</label>
                    <input class="card-secundario" type="text" id="nome_completo" name="nome_completo" 
                           value="<?= htmlspecialchars($nomeAtual) ?>" required maxlength="255">
                    <?php if (isset($erros['nome_completo'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['nome_completo']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="data_nascimento"><i class="bi bi-calendar-event"></i> Data de nascimento:</label>
                    <input class="card-secundario" type="date" id="data_nascimento" name="data_nascimento" 
                           value="<?= htmlspecialchars($dataAtual) ?>" required max="<?= date('Y-m-d') ?>">
                    <?php if (isset($erros['data_nascimento'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['data_nascimento']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="cpf_protegido"><i class="bi bi-shield-lock"></i> CPF (Identificador único):</label>
                    <input class="card-secundario campo-desabilitado" type="text" id="cpf_protegido" 
                           value="<?= htmlspecialchars($cpfFormatado) ?>" disabled readonly>
                </div>

                <div class="formgroup">
                    <label for="email"><i class="bi bi-envelope"></i> E-mail:</label>
                    <input class="card-secundario" type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($emailAtual) ?>" required maxlength="255">
                    <?php if (isset($erros['email'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['email']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="numero_telefone"><i class="bi bi-telephone"></i> Telefone de contato:</label>
                    <input class="card-secundario" type="text" id="numero_telefone" name="numero_telefone" 
                           value="<?= htmlspecialchars($telefoneAtual) ?>" required maxlength="20" placeholder="(45) 99999-9999">
                    <?php if (isset($erros['numero_telefone'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['numero_telefone']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="senha"><i class="bi bi-key"></i> Nova senha:</label>
                    <input class="card-secundario" type="password" id="senha" name="senha" minlength="8" 
                           placeholder="Deixe em branco para não alterar sua senha">
                    <small class="ajuda-campo">
                        Caso queira trocar sua senha, digite pelo menos 8 caracteres.
                    </small>
                    <?php if (isset($erros['senha'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['senha']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="grupo-botoes-form">
                    <button type="submit" class="botao brilho">
                        <i class="bi bi-check-lg"></i> Salvar alterações
                    </button>
                    <a class="botao" href="<?= URL_BASE ?>/perfil">
                        <i class="bi bi-x-lg"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </main>
</body>
</html>