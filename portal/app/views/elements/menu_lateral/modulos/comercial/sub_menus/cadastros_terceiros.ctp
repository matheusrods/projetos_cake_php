<!-- Sub menus Comercial cadastros terceiros-->

<li class="scoop-hasmenu">
    <a href="javascript:void(0)">
        <span class="scoop-micon"><i class="fas fa-pie-chart"></i></span>
        <span class="scoop-mtext">Cadastros terceiros</span>
        <span class="scoop-mcaret"></span>
    </a>
    <ul class="scoop-submenu">
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'assinatura_eletronica','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/assinatura_eletronica/">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Assinatura eletrônica</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'cargos','action'=>'cargo_terceiros'))) :?>
            <li class="">
                <a href="/portal/cargos/cargo_terceiros">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Cargos</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

         <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes','action'=>'funcionarios'))) :?>     
            <li class="">
                <a href="/portal/clientes/funcionarios">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Funcionário</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes','action'=>'funcionarios_percapita'))) :?>    
            <li class="">
                <a href="/portal/clientes/funcionarios_percapita">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Funcionário per capita</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'medicos','action'=>'index'))) :?>    
            <li class="">
                <a href="/portal/medicos">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Profissional</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'setores','action'=>'setor_terceiros'))) :?>  
            <li class="">
                <a href="/portal/setores/setor_terceiros">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Setores</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>
        
        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes','action'=>'cliente_tomador'))) :?>  
            <li class="">
                <a href="/portal/clientes/cliente_tomador">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Tomador de serviço</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'clientes','action'=>'cliente_terceiros'))) :?>
            <li class="">
                <a href="/portal/clientes/cliente_terceiros">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Unidades</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>

        <?php if ($this->BMenu->permiteMenu(array('controller'=>'tipos_acoes','action'=>'index'))) :?>
            <li class="">
                <a href="/portal/tipos_acoes">
                    <span class="scoop-micon"><i class="fas fa-chart"></i></span>
                    <span class="scoop-mtext">Tipos ações</span>
                    <span class="scoop-mcaret"></span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</li>
