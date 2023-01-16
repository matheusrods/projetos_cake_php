<!-- Sub menus Comercial Consultas terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-bulb"></i></span>
        <span class="scoop-mtext">Consultas terceiros</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'tipo_digitalizacao','action'=>'consulta_digitalizacao_terceiros'))) :?>
            <li class="">
                <a href="/portal/tipo_digitalizacao/consulta_digitalizacao_terceiros">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Digitalização</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'consultas','action'=>'ppra_pcmso_pendente_terceiros'))) :?> 
            <li class="">
                <a href="/portal/consultas/ppra_pcmso_pendente_terceiros">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">PGR e PCMSO pendentes</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'riscos_exames','action'=>'aplicados'))) :?>
            <li class="">
                <a href="/portal/riscos_exames/aplicados">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Riscos exames aplicados</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes_funcionarios','action'=>'consulta_vidas'))) :?>
            <li class="">
                <a href="/portal/clientes_funcionarios/consulta_vidas">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Vidas</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'aplicacao_exames','action'=>'vigencia_ppra_pcmso'))) :?>
            <li class="">
                <a href="/portal/aplicacao_exames/vigencia_ppra_pcmso">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Vigência de PGR e PCMSO</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>
