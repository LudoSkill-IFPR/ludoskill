<nav>
    <a id="homepage" href="<?= URL_BASE ?>/funcionario/inicial">
        <img id="icone" src="../assets/imagens/icone-ludoskill-transparente.png" alt="icone do ludoskill">
        <img id="logo" src="../assets/imagens/logo-ludoskill.png" alt="logo do ludoskill">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    </a>

    <ul id="navegacao">
        <li><a href="<?= URL_BASE ?>/funcionario/modulos">Módulos</a></li>
        <li><a href="<?= URL_BASE ?>/funcionario/loja">Loja</a></li>
        <!-- <li><a class="botao brilho" href="<?= URL_BASE ?>/funcionario/inicial"><i class="bi bi-person-circle"></i>Perfil</a></li> -->
        
        <li class="dropdown">
            <a class="botao brilho" href="#"><i class="bi bi-person-circle"></i> Perfil</a>

            <div class="dropdown-menu">
                <a href=""><i class="bi bi-door-open-fill"></i> Logout</a>
            </div>
        </li>
        
    </ul>
</nav>
