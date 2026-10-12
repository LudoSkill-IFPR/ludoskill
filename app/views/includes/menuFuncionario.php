<?php if (!empty($_SESSION['tema_equipado'])): $paleta = (int) $_SESSION['tema_equipado'] % 3; ?><style>:root{<?php if ($paleta === 0): ?>--roxo:#176b87;--roxo-escuro:#0f4255;--verde-escuro:#2aa198;--verde-claro:#9ee8df;<?php elseif ($paleta === 1): ?>--roxo:#a33b20;--roxo-escuro:#652412;--verde-escuro:#d88916;--verde-claro:#ffd27c;<?php else: ?>--roxo:#6c3ca0;--roxo-escuro:#3e2260;--verde-escuro:#ca4e92;--verde-claro:#ffc1e3;<?php endif; ?>}</style><?php endif; ?>
<nav class="menu-funcionario">
    <a id="homepage" href="<?= URL_BASE ?>/funcionario/inicial">
        <img id="icone" src="<?= URL_BASE ?>/assets/imagens/icone-ludoskill-transparente.png" alt="Ícone do LudoSkill">
        <img id="logo" src="<?= URL_BASE ?>/assets/imagens/logo-ludoskill.png" alt="LudoSkill">
    </a>

    <ul id="navegacao">
        <li><a href="<?= URL_BASE ?>/funcionario/modulos">Módulos</a></li>
        <li class="dropdown">
            <a class="botao brilho" href="#"><i class="bi bi-person-circle"></i> Perfil</a>

            <div class="dropdown-menu">
                <a href="<?= URL_BASE ?>/perfil"><i class="bi bi-person"></i> Ver perfil</a>
                <a href="<?= URL_BASE ?>/perfil/editar"><i class="bi bi-pencil"></i> Editar</a>
                <form action="<?= URL_BASE ?>/logout" method="post"><button type="submit"><i class="bi bi-door-open-fill"></i> Logout</button></form>
            </div>
        </li>
        
    </ul>
</nav>
