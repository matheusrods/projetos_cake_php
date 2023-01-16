<!-- Sub menus saude Operações terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-pie-chart"></i></span>
        <span class="scoop-mtext">Operações terceiros</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'audiometrias','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/audiometrias">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Audiometria</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'atestados','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/atestados">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Absenteísmo</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'itens_pedidos_exames_baixa','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/itens_pedidos_exames_baixa">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Baixa de pedidos</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'consultas_agendas','action'=>'index2'))) :?>
            <li class="">
                <a href="/portal/consultas_agendas/index2">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Exames agendados</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes_funcionarios','action'=>'selecao_funcionarios'))) :?>
            <li class="">
                <a href="/portal/clientes_funcionarios/selecao_funcionarios">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Emissão de pedidos</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'fichas_clinicas','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/fichas_clinicas">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Ficha clinica</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'fichas_assistenciais','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/fichas_assistenciais">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Ficha assistencial</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'ficha_psicossocial','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/ficha_psicossocial">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Ficha psicossocial</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'importar','action'=>'manutencao_pedido_exame'))) :?>
            <li class="">
                <a href="/portal/importar/manutencao_pedido_exame">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Manutenção pedido</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes_implantacao','action'=>'index_pcmso_ext'))) :?>
            <li class="">
                <a href="/portal/clientes_implantacao/index_pcmso_ext">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">PCMSO</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes_implantacao','action'=>'gestao_cronograma_pcmso'))) :?>
            <li class="">
                <a href="/portal/clientes_implantacao/gestao_cronograma_pcmso">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Gestão cronogramas</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>
