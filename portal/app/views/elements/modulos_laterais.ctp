<nav class="scoop-navbar">

    <div class="scoop-inner-navbar">

        <ul class="scoop-item scoop-right-item">

            <?php $this->Session->read('modulo_selecionado') ?>
            <?php $menu_modulos = ''; ?>

            <div class="scoop-navigatio-lavel">Módulos</div>

            <?php if(isset($authUsuario['Usuario']) && empty($authUsuario['Usuario']['codigo_empresa'])) : ?>
                <?php echo $this->element('menu_lateral/modulos/sistema/modulo_sistema'); ?>
            <?php endif; ?>

            <?php echo $this->element('menu_lateral/modulos/administrativo/modulo_administrativo'); ?>

            <?php echo $this->element('menu_lateral/modulos/financeiro/modulo_financeiro'); ?>

            <?php echo $this->element('menu_lateral/modulos/covid/modulo_covid'); ?>

            <?php echo $this->element('menu_lateral/modulos/comercial/modulo_comercial'); ?>

            <?php echo $this->element('menu_lateral/modulos/contas_medicas/modulo_contas_medicas'); ?>

            <?php echo $this->element('menu_lateral/modulos/credenciamento/modulo_credenciamento'); ?>

            <?php echo $this->element('menu_lateral/modulos/gestao_doc/modulo_gestao_doc'); ?>

            <?php echo $this->element('menu_lateral/modulos/saude/modulo_saude'); ?>

            <?php echo $this->element('menu_lateral/modulos/seguranca/modulo_seguranca'); ?>

            <?php echo $this->element('menu_lateral/modulos/mapeamento_risco/modulo_mapeamento_risco'); ?>

            <?php echo $this->element('menu_lateral/modulos/e_social/modulo_e_social'); ?>

            <?php echo $this->element('menu_lateral/modulos/plano_de_acao/modulo_plano_de_acao'); ?>

            <?php echo $this->element('menu_lateral/modulos/safety_walk_talk/modulo_safety_walk_talk'); ?>

            <?php echo $this->element('menu_lateral/modulos/observador_ehs/modulo_observador_ehs'); ?>

            <!-- Menus de usuário-->
            <div class="scoop-navigatio-lavel" style="margin-top: 20px;">Configurações</div>

            <ul class="scoop-item scoop-right-item" item-border="true" item-border-style="solid" subitem-border="true">

                <?php if($authUsuario['Usuario']['codigo_cliente']) : ?>
                    <li class="">
                        <a href="/portal/usuarios_multi_cliente/selecionar_cliente">
                            <span class="scoop-micon"><i class="fas fa-user"></i></span>
                            <span class="scoop-mtext">Acessar Cliente</span>
                            <span class="scoop-mcaret"></span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="">
                    <a href="/portal/usuarios/minhas_configuracoes">
                        <span class="scoop-micon"><i class="fas fa-user"></i></span>
                        <span class="scoop-mtext">Minhas Configurações</span>
                        <span class="scoop-mcaret"></span>
                    </a>
                </li>

                <?php if (!empty($authUsuario['Usuario']['codigo_cliente'])): ?>
                    <li class="">
                        <a href="/portal/usuarios/trocar_senha">
                            <span class="scoop-micon"><i class="fas fa-user"></i></span>
                            <span class="scoop-mtext">Trocar Senha</span>
                            <span class="scoop-mcaret"></span>
                        </a>
                    </li>

                    <?php if (isset($authUsuario['Usuario']['admin']) && $authUsuario['Usuario']['admin'] == 1): ?>

                        <li class="">
                            <a href="/portal/usuarios/index">
                                <span class="scoop-micon"><i class="fas fa-user"></i></span>
                                <span class="scoop-mtext">Gerenciar Usuarios</span>
                                <span class="scoop-mcaret"></span>
                            </a>
                        </li>
                        <li class="">
                            <a href="/portal/uperfis/index">
                                <span class="scoop-micon"><i class="fas fa-user"></i></span>
                                <span class="scoop-mtext">Gerenciar Perfis</span>
                                <span class="scoop-mcaret"></span>
                            </a>
                        </li>
                    <?php endif ?>
                <?php endif ?>

                <li class="">
                    <a href="/portal/processamentos/index">
                        <span class="scoop-micon"><i class="fas fa-user"></i></span>
                        <span class="scoop-mtext">Processamentos</span>
                        <span class="scoop-mcaret"></span>
                    </a>
                </li>

                <li class="">
                    <a href="/portal/usuarios/logout">
                        <span class="scoop-micon"><i class="fas fa-power-off"></i></span>
                        <span class="scoop-mtext">Sair</span>
                        <span class="scoop-mcaret"></span>
                    </a>
                </li>
            </ul>

        </ul>
    </div>
</nav>

