<!-- Sub menus Saude Operações-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-pie-chart"></i></span>
        <span class="scoop-mtext">Operações</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'agendamento','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/agendamento">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Agendar Sugestões</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <li class="">
            <a href="/portal/agendamento/fila">
                <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                <span class="scoop-mtext">Fila de agendamento</span>
                <span class="scoop-mcaret"></span>
            </a>
        </li>
        <li class="">
            <a href="/portal/consultas_agendas/moderacao_anexos">
                <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                <span class="scoop-mtext">Moderação exames &nbsp;&nbsp;&nbsp;&nbsp;digitalizados</span>
                <span class="scoop-mcaret"></span>
            </a>
        </li>
        <li class="">
            <a href="/portal/clientes_implantacao/index_pcmso">
                <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                <span class="scoop-mtext">PCMSO</span>
                <span class="scoop-mcaret"></span>
            </a>
        </li>
    </ul>
</li>
