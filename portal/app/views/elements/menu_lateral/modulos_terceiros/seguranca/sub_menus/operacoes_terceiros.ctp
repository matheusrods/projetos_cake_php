<!-- Sub menus Segurança Operações terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-pie-chart"></i></span>
        <span class="scoop-mtext">Operações</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'cat','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/cat">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">CAT</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'chamados','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/chamados">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Chamados</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'ghe','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/ghe">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">GHE</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes_implantacao','action'=>'gestao_cronograma_ppra'))) :?>
            <li class="">
                <a href="/portal/clientes_implantacao/gestao_cronograma_ppra">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Gestão cronograma</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes_implantacao','action'=>'index_ppra_ext'))) :?>
            <li class="">
                <a href="/portal/clientes_implantacao/index_ppra_ext">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">PGR</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'processos','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/processos">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Processos</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'riscos_tipos','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/riscos_tipos">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Riscos Tipo</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'perigos_aspectos','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/perigos_aspectos">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Perigos Aspectos</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'riscos_impactos','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/riscos_impactos">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Riscos Impactos</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'agentes_riscos','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/agentes_riscos">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Agentes de Risco</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'unidades_medicao','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/unidades_medicao">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Unidades de Medida</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes','action'=>'visualizar_clientes_gestao_de_risco'))) :?>
            <li class="">
                <a href="/portal/clientes/visualizar_clientes_gestao_de_risco">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Configurações</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'versoes_ppra','action'=>'versoes_ppra'))) :?>
            <li class="">
                <a href="/portal/ppra_versoes/versoes_ppra">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Versões PGR</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'relatorio_insalubridade','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/relatorio_insalubridade">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Insalubridade</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'relatorio_periculosidade','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/relatorio_periculosidade">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Periculosidade</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

    </ul>
</li>
