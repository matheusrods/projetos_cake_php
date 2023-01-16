<!-- Sub menus Mapeamento de risco Consultas terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-bulb"></i></span>
        <span class="scoop-mtext">Consultas terceiros</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <li class="scoop-hasmenu">
            <a href="javascript:void(0)">
                <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                <span class="scoop-mtext">Dashboard</span>
                <span class="scoop-mcaret"></span>
            </a>
            <ul class="scoop-submenu">
                <?php if ($this->BMenu->permiteMenu(array('controller'=>'dados_saude_consultas','action'=>'dashboard','colaboradores_atestados'))) :?>
                    <li class="">
                        <a href="/portal/dados_saude_consultas/dashboard/colaboradores_atestados">
                            <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                            <span class="scoop-mtext">Colaboradores e atestados</span>
                            <span class="scoop-mcaret"></span>
                        </a>
                    </li>
                <?php endif;?>

                <?php if ($this->BMenu->permiteMenu(array('controller'=>'dados_saude_consultas','action'=>'dashboard','dados_gerais'))) :?>
                    <li class="">
                        <a href="/portal/dados_saude_consultas/dashboard/dados_gerais">
                            <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                            <span class="scoop-mtext">Dados gerais</span>
                            <span class="scoop-mcaret"></span>
                        </a>
                    </li>
                <?php endif;?>

            </ul>
        </li>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'dados_saude_consultas','action'=>'relatorio_faixa_etaria'))) :?>
            <li class="">
                <a href="/portal/dados_saude_consultas/relatorio_faixa_etaria">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Faixa etária</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'dados_saude_consultas','action'=>'relatorio_fatores_risco'))) :?>
            <li class="">
                <a href="/portal/dados_saude_consultas/relatorio_fatores_risco">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Fatores de risco</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'dados_saude_consultas','action'=>'relatorio_imc'))) :?>
            <li class="">
                <a href="/portal/dados_saude_consultas/relatorio_imc">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">IMC</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'dados_saude_consultas','action'=>'relatorio_genero'))) :?>
            <li class="">
                <a href="/portal/dados_saude_consultas/relatorio_genero">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Percentual (Homens / &nbsp;&nbsp;&nbsp;&nbsp;Mulheres)</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'dados_saude_consultas','action'=>'relatorio_posicao_questionarios'))) :?>
            <li class="">
                <a href="/portal/dados_saude_consultas/relatorio_posicao_questionarios">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Posição de preenchiento &nbsp;&nbsp;&nbsp;&nbsp;questionário</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif;?>

    </ul>
</li>
