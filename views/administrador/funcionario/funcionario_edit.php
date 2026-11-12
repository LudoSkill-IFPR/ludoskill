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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edicao de Funcionarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>

    <div class="container">
        <div>
            <h1 class="mt-5">Edição de Funcionario</h1>

            <form action="<?= URL_BASE ?>/gestor/funcionarios/atualizar" method="post">
                <input type="hidden" name="id_funcionario" value="<?= htmlspecialchars((string) ($funcionario['id_funcionario'] ?? '')) ?>">
                <input type="hidden" name="id_usuario" value="<?= htmlspecialchars((string) ($funcionario['id_usuario'] ?? '')) ?>">
                <input type="hidden" name="bolotas_totais" value="<?= htmlspecialchars((string) ($funcionario['bolotas_totais'] ?? 0)) ?>">
                <input type="hidden" name="pontuacao_total" value="<?= htmlspecialchars((string) ($funcionario['pontuacao_total'] ?? 0)) ?>">
                <input type="hidden" name="nivel" value="<?= htmlspecialchars((string) ($funcionario['nivel'] ?? 1)) ?>">

                <div>
                    <label for="nome_completo">Nome</label>
                    <input class="form-control" type="text" id="nome_completo" name="nome_completo" value="<?= htmlspecialchars($usuario['nome_completo'] ?? '') ?>">
                    <?php if (isset($erros['nome_completo'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['nome_completo']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input class="form-control" type="date" id="data_nascimento" name="data_nascimento" value="<?= htmlspecialchars($dataNascimento) ?>">
                    <?php if (isset($erros['data_nascimento'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['data_nascimento']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="cpf" class="form-label">CPF</label>
                    <input class="form-control" type="text" id="cpf" name="cpf" maxlength="14" value="<?= htmlspecialchars($usuario['CPF'] ?? '') ?>">
                    <?php if (isset($erros['cpf'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['cpf']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="email" class="form-label">E-mail</label>
                    <input class="form-control" type="email" id="email" name="email" value="<?= htmlspecialchars($usuario['email'] ?? '') ?>">
                    <?php if (isset($erros['email'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['email']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="senha_hash" class="form-label">Senha</label>
                    <input class="form-control" type="password" id="senha_hash" name="senha_hash" value="">
                    <div class="form-text"></div>
                    <?php if (isset($erros['senha_hash'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['senha_hash']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="numero_telefone" class="form-label">Numero de telefone</label>
                    <input class="form-control" type="number" id="numero_telefone" name="numero_telefone" value="<?= htmlspecialchars($usuario['numero_telefone'] ?? '') ?>">
                    <?php if (isset($erros['numero_telefone'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['numero_telefone']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="id_empresa" class="form-label">Empresa</label>
                    <select class="form-control" id="id_empresa" name="id_empresa">
                        <option value="" hidden disabled <?= !$idEmpresaAtual ? 'selected' : '' ?>>Selecione</option>
                        <?php foreach($empresas as $empresa):?>
                            <option value="<?= $empresa['id_empresa'] ?>" <?= $idEmpresaAtual == $empresa['id_empresa'] ? 'selected' : '' ?>><?= $empresa['nome'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($erros['id_empresa'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['id_empresa']) ?></span>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Atualizar</button>
            </form>
        </div>

    </div>

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