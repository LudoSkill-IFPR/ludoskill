<?php
    use app\repositories\EmpresaRepository;
    use app\models\Empresa;

    $empresaRepository = new EmpresaRepository();
    
    $empresas = $empresaRepository->getEmpresas();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Funcionarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>

    <div class="container">
        <div>
            <h1 class="mt-5">Cadastro de Funcionarios</h1>

            <form action="<?=  URL_BASE ?>/administrador/funcionario/salvar" method="post">
                <div>
                    <label for="nome_completo">Nome</label>
                    <input class="form-control" type="text" id="nome_completo" name="nome_completo" value="<?= isset($funcionario['nome_completo']) ? htmlspecialchars($funcionario['nome_completo']) : '' ?>">
                    <?php if (isset($erros['nome_completo'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['nome_completo']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="data_nascimento" class="form-label">Data de Nascimento</label>
                    <input class="form-control" type="date" id="data_nascimento" name="data_nascimento" value="<?= isset($funcionario['data_nascimento']) ? htmlspecialchars($funcionario['data_nascimento']) : '' ?>">
                    <?php if (isset($erros['data_nascimento'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['data_nascimento']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="cpf" class="form-label">CPF</label>
                    <input class="form-control" type="text" id="cpf" name="cpf" maxlength="18" value="<?= isset($funcionario['cpf']) ? htmlspecialchars($funcionario['cpf']) : '' ?>">
                    <?php if (isset($erros['cpf'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['cpf']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="email" class="form-label">E-mail</label>
                    <input class="form-control" type="email" id="email" name="email" value="<?= isset($funcionario['email']) ? htmlspecialchars($funcionario['email']) : '' ?>">
                    <?php if (isset($erros['email'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['email']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="senha_hash" class="form-label">Senha</label>
                    <input class="form-control" type="password" id="senha_hash" name="senha_hash" value="<?= isset($funcionario['senha_hash']) ? htmlspecialchars($funcionario['senha_hash']) : '' ?>">
                    <?php if (isset($erros['senha_hash'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['senha_hash']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="numero_telefone" class="form-label">Numero de telefone</label>
                    <input class="form-control" type="number" id="numero_telefone" name="numero_telefone" value="<?= isset($funcionario['numero_telefone']) ? htmlspecialchars($funcionario['numero_telefone']) : '' ?>">
                    <?php if (isset($erros['numero_telefone'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['numero_telefone']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="id_empresa" class="form-label">Empresa</label>
                    <select class="form-control" id="id_empresa" name="id_empresa">
                        <option value="" hidden disabled <?= !isset($empresa['id_empresa']) ? 'selected' : '' ?>>Selecione</option>
                        <?php foreach($empresas as $empresa):?>
                            <option value="<?= $empresa['id_empresa'] ?>"><?= $empresa['nome'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Salvar</button>
            </form>
        </div>

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const cnpjInput = document.getElementById('cnpj');
            if (!cnpjInput) return;

            const formatCnpj = (value) => {
                const digits = (value || '').replace(/\D/g, '').slice(0, 14);
                if (!digits) return '';
                return digits.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5');
            };

            cnpjInput.value = formatCnpj(cnpjInput.value);

            cnpjInput.addEventListener('input', function () {
                cnpjInput.value = formatCnpj(cnpjInput.value);
            });

            cnpjInput.closest('form').addEventListener('submit', function () {
                cnpjInput.value = cnpjInput.value.replace(/\D/g, '');
            });
        });
    </script>
    
</body>
</html>