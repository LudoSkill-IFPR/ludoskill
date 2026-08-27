<?php
    use app\repositories\EmpresaRepository;
    use app\models\Empresa;
    use app\repositories\UsuarioRepository;

    $usuarioRepository = new UsuarioRepository();
    $empresaRepository = new EmpresaRepository();
    $empresas = $empresaRepository->getEmpresas();

    // O service/repository retorna um array associativo (não um objeto Funcionario).
    $funcionario = $data['funcionario'];
    $usuario = $usuarioRepository->getUsuario($funcionario['id_usuario']);

    $dataNascimento = $usuario['data_nascimento'] ?? '';
    // Se vier como DateTime/DateTimeImmutable em algum cenário, formata; se vier string do banco, usa direto.
    if ($dataNascimento instanceof \DateTimeInterface) {
        $dataNascimento = $dataNascimento->format('Y-m-d');
    }

    $idEmpresaAtual = $funcionario['id_empresa'] ?? null;
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/geralUsuario.css">
    <link rel="stylesheet" href="../../assets/css/formulariosAdministrador.css">
    
    <title>LudoSkill - Editar Funcionários</title>
</head>

<body>
    <header>
        <?php include_once(__DIR__ . "/../../includes/menuAdministrador.html"); ?>
    </header>

    <main>
        <div class="container">
            <div id="topo">
                <a href="/administrador/modulos/" class="botao brilho"><i class="bi bi-arrow-left"></i> Voltar</a>
                <div>
                    <h1>Edição de Funcionários</h1>
                    <p class="mensagem">Provisório.</p>
                </div>
            </div>

            <form class="card" action="<?= URL_BASE ?>/gestor/funcionarios/atualizar" method="post">
                <input type="hidden" name="id_funcionario" value="<?= htmlspecialchars((string) ($funcionario['id_funcionario'] ?? '')) ?>">
                <input type="hidden" name="id_usuario" value="<?= htmlspecialchars((string) ($funcionario['id_usuario'] ?? '')) ?>">
                <input type="hidden" name="bolotas_totais" value="<?= htmlspecialchars((string) ($funcionario['bolotas_totais'] ?? 0)) ?>">
                <input type="hidden" name="pontuacao_total" value="<?= htmlspecialchars((string) ($funcionario['pontuacao_total'] ?? 0)) ?>">
                <input type="hidden" name="nivel" value="<?= htmlspecialchars((string) ($funcionario['nivel'] ?? 1)) ?>">

                <div class="formgroup">
                    <label for="nome_completo">Nome</label>
                    <input class="card-secundario" type="text" id="nome_completo" name="nome_completo" value="<?= htmlspecialchars($usuario['nome_completo'] ?? '') ?>">

                    <?php if (isset($erros['nome_completo'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['nome_completo']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input class="card-secundario" type="date" id="data_nascimento" name="data_nascimento" value="<?= htmlspecialchars($dataNascimento) ?>">

                    <?php if (isset($erros['data_nascimento'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['data_nascimento']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="cpf" class="form-label">CPF</label>
                    <input class="card-secundario" type="text" id="cpf" name="cpf" maxlength="14" value="<?= htmlspecialchars($usuario['CPF'] ?? '') ?>">
                    
                    <?php if (isset($erros['cpf'])): ?>
                        <div class="text-danger"><?= htmlspecialchars($erros['cpf']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="email" class="form-label">E-mail</label>
                    <input class="card-secundario" type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
                    
                    <?php if (isset($erros['email'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['email']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="senha_hash" class="form-label">Senha</label>
                    <input class="card-secundario" type="password" id="senha_hash" name="senha_hash" value="">
                    <div></div>

                    <?php if (isset($erros['senha_hash'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['senha_hash']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="numero_telefone" class="form-label">Numero de telefone</label>
                    <input class="card-secundario" type="number" id="numero_telefone" name="numero_telefone" value="<?= htmlspecialchars($usuario['numero_telefone'] ?? '') ?>">
                    
                    <?php if (isset($erros['numero_telefone'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['numero_telefone']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="formgroup">
                    <label for="id_empresa" class="form-label">Empresa</label>
                    <select class="card-secundario" id="id_empresa" name="id_empresa">
                        <option value="" hidden disabled <?= !$idEmpresaAtual ? 'selected' : '' ?>>Selecione</option>
                        <?php foreach($empresas as $empresa):?>
                            <option value="<?= $empresa['id_empresa'] ?>" <?= $idEmpresaAtual == $empresa['id_empresa'] ? 'selected' : '' ?>><?= $empresa['nome'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erros['id_empresa'])): ?>
                        <div class="erro"><?= htmlspecialchars($erros['id_empresa']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="botao brilho">Atualizar</button>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cpfInput = document.getElementById('cpf');
            if (!cpfInput) return;

            const formatCpf = (value) => {
                const digits = (value || '').replace(/\D/g, '').slice(0, 11);
                if (!digits) return '';
                return digits.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4');
            };

            cpfInput.value = formatCpf(cpfInput.value);

            cpfInput.addEventListener('input', function () {
                cpfInput.value = formatCpf(cpfInput.value);
            });

            cpfInput.closest('form').addEventListener('submit', function () {
                cpfInput.value = cpfInput.value.replace(/\D/g, '');
            });
        });
    </script>

</body>
</html>
