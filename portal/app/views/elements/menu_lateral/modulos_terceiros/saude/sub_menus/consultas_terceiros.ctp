<!-- Sub menus Saude Consultas terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-bulb"></i></span>
        <span class="scoop-mtext">Consultas</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'atestados','action'=>'sintetico'))) :?>
            <li class="">
                <a href="/portal/atestados/sintetico">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Absenteísmo sintético</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'medicos','action'=>'corpo_clinico'))) :?>
            <li class="">
                <a href="/portal/medicos/corpo_clinico">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Corpo clinico</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'consultas_agendas','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/consultas_agendas">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Exames agendados</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'consulta_pedidos_exames','action'=>'baixa_exames_sintetico'))) :?>
            <li class="">
                <a href="/portal/consulta_pedidos_exames/baixa_exames_sintetico">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Exames baixados</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'ficha_psicossocial','action'=>'ficha_psicossocial_terceiros'))) :?>
            <li class="">
                <a href="/portal/ficha_psicossocial/ficha_psicossocial_terceiros">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Ficha psicossocial</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'fichas_pcd','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/fichas_pcd">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Laudo caracterizador de &nbsp;&nbsp;&nbsp;&nbsp;deficiência</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes','action'=>'funcionarios_ppp'))) :?>
            <li class="">
                <a href="/portal/clientes/funcionarios_ppp">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">PPP</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'exames','action'=>'posicao_exames_sintetico'))) :?>
            <li class="">
                <a href="/portal/exames/posicao_exames_sintetico">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Posição de exames</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'exames','action'=>'posicao_exames_analitico2'))) :?>
            <li class="">
                <a href="/portal/exames/posicao_exames_analitico2">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Posição de exames analítico</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'exames','action'=>'relatorio_anual'))) :?>
            <li class="">
                <a href="/portal/exames/relatorio_anual">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Relatório anual</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'fichas_clinicas','action'=>'fichas_clinicas_terceiros'))) :?>
            <li class="">
                <a href="/portal/fichas_clinicas/fichas_clinicas_terceiros">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Relatório ficha clínica</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'pcmso_versoes','action'=>'versoes_pcmso'))) :?>
            <li class="">
                <a href="/portal/pcmso_versoes/versoes_pcmso">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Versões PCMSO</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>
