<?php if(isset($dados_historico) && count($dados_historico)) : ?>

    <?php echo $paginator->options(array('update' => 'div.lista')); ?>

    <div class='well'>
       <?php echo $this->Html->link('<i class="cus-page-white-excel"></i>', array('controller' => $this->name, 'action' => $this->action, 'destino','export'), array('escape' => false, 'title' =>'Exportar para Excel', 'style' => 'float:right'));?>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th><?= $this->Paginator->sort('Código de Usuário', 'codigo_usuario') ?></th>
                <th><?= $this->Paginator->sort('Nome de usuário', 'nome_usuario') ?></th>
                <th><?= $this->Paginator->sort('Tipo de Usuário', 'tipo_usuario') ?></th>
                <th><?= $this->Paginator->sort('Login', 'login') ?></th>
                <th><?= $this->Paginator->sort('Perfil', 'perfil') ?></th>
                <th><?= $this->Paginator->sort('Código de Cliente', 'codigo_cliente') ?></th>
                <th><?= $this->Paginator->sort('Nome de Cliente', 'nome_cliente') ?></th>
                <th><?= $this->Paginator->sort('Sistema', 'sistema') ?></th>
                <th><?= $this->Paginator->sort('Data Login', 'data_login') ?></th>
                <th><?= $this->Paginator->sort('Hora Login', 'hora_login') ?></th>
                <th><?= $this->Paginator->sort('Tempo Logado', 'tempo_logado') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($dados_historico as $value) : ?>
                <tr>
                    <td><?php echo $value['Usuario']['codigo'];?></td>
                    <td><?php echo $value['Usuario']['nome'];?></td>
                    <td><?php echo $value[0]['tipo_perfil'];?></td>
                    <td><?php echo $value['Usuario']['apelido'];?></td>
                    <td><?php echo $value['Uperfil']['descricao'];?></td>               
                    <td><?php echo $value['ClienteP']['codigo'];?></td>
                    <td><?php echo $value['ClienteP']['nome_fantasia'];?></td>
                    <td><?php echo $value['Sistema']['descricao'];?></td>
                    <td><?php echo Comum::formataData($value[0]['data_acesso'],'ymd', 'dmy');?></td>
                    <td><?php echo Comum::formataHora($value[0]['hora_acesso']);?></td>
                    <td>
                        <?php
                            if(empty($value[0]['tempo_logado'])){
                                echo $value['UsuarioHistorico']['data_logout'];
                            } else {
                                echo Comum::calculaTempo($value[0]['hora_acesso'], $value[0]['hora_logout']);
                            }
                        ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </tbody>  
    </table>
    <div class='row-fluid'>
        <div class='numbers span6'>
            <?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
            <?php echo $this->Paginator->numbers(); ?>
            <?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        </div>
        <div class='counter span7'>
            <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
            
        </div>
    </div>
    <?php echo $this->Js->writeBuffer(); ?>
<?php else:?>
    <div class="alert">Nenhum dado foi encontrado.</div>
<?php endif;?>

<?php echo $this->Javascript->codeBlock("
    $('[data-toggle=\"tooltip\"]').tooltip();
    function atualizaListaFichasClinicas() {
        var div = jQuery('div.lista');
        bloquearDiv(div);
        div.load(baseUrl + 'usuarios_historicos/lista_logins_users/' + Math.random());
    }
");
?>