<nav>
    <a id="homepage" href="<?= URL_BASE ?>/gestor/inicial">
        <img id="icone" src="<?= URL_BASE ?>/assets/imagens/icone-ludoskill-transparente.png" alt="Ícone do LudoSkill">
        <img id="logo" src="<?= URL_BASE ?>/assets/imagens/logo-ludoskill.png" alt="Logo do LudoSkill">
    </a>
    <ul id="navegacao">
        <li class="dropdown">
            <a class="botao brilho" href="#"><i class="bi bi-person-circle"></i> Perfil</a>
            <div class="dropdown-menu">
                <a href="<?= URL_BASE ?>/perfil"><i class="bi bi-person"></i> Ver perfil</a>
                <a href="<?= URL_BASE ?>/perfil/editar"><i class="bi bi-pencil"></i> Editar perfil</a>
                <form action="<?= URL_BASE ?>/logout" method="post"><button type="submit"><i class="bi bi-door-open-fill"></i> Logout</button></form>
            </div>
        </li>
    </ul>
</nav>
