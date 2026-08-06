<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de empresas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" >
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>

    <div class="container">
        <div>
            <h1 class="mt-5">Cadastro de Empresas</h1>

            <form action="<?=  URL_BASE ?>/administrador/empresas/salvar" method="post">
                <div>
                    <label for="nome">Nome</label>
                    <input class="form-control" type="text" id="nome" name="nome" value="<?= isset($empresa['nome']) ? htmlspecialchars($empresa['nome']) : '' ?>">
                    <?php if (isset($erros['nome'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['nome']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="cnpj" class="form-label">CNPJ</label>
                    <input class="form-control" type="text" id="cnpj" name="cnpj" maxlength="18" value="<?= isset($empresa['cnpj']) ? htmlspecialchars($empresa['cnpj']) : '' ?>">
                    <?php if (isset($erros['cnpj'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['cnpj']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input class="form-control" type="email" id="email" name="email" value="<?= isset($empresa['email']) ? htmlspecialchars($empresa['email']) : '' ?>">
                    <?php if (isset($erros['email'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['email']) ?></span>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="plano" class="form-label">Plano</label>
                    <select class="form-control" id="plano" name="plano">
                        <option value="" hidden disabled <?= !isset($empresa['plano']) ? 'selected' : '' ?>>Selecione</option>
                        <option value="BASICO" <?= isset($empresa['plano']) && $empresa['plano'] === 'BASICO' ? 'selected' : '' ?>>Básico</option>
                        <option value="INTERMEDIARIO" <?= isset($empresa['plano']) && $empresa['plano'] === 'INTERMEDIARIO' ? 'selected' : '' ?>>Intermediário</option>
                        <option value="AVANCADO" <?= isset($empresa['plano']) && $empresa['plano'] === 'AVANCADO' ? 'selected' : '' ?>>Avançado</option>
                        <option value="CORPORATIVO" <?= isset($empresa['plano']) && $empresa['plano'] === 'CORPORATIVO' ? 'selected' : '' ?>>Corporativo</option>
                    </select>

                    <?php if (isset($erros['plano'])): ?>
                        <span class="text-danger"><?= htmlspecialchars($erros['plano']) ?></span>
                    <?php endif; ?>
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