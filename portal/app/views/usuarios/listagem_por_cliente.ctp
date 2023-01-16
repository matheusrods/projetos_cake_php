<div class='well'>
    <strong>Código: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['codigo']); ?>
    <strong>Cliente: </strong><?php echo $this->Html->tag('span', $cliente['Cliente']['razao_social']); ?>
</div>

<div class='actionbar-right'>  

    <?php 
    //valida se o usuario logado é um usuario administrador
    if($authUsuario['Usuario']['admin'] == 1 || $authUsuario['Usuario']['codigo_cliente'] == ''):
    ?>
        <div class='actionbar-left' style="float: left;">
                <a href='/portal/importar/importar_usuario/<?php echo $this->passedArgs[0]; ?>' id='link_importacao_usuario' class='btn btn-warning' title='Importar Usuário'><i class='icon-plus icon-white'></i>Importar Usuários</a>
        </div>
    <?php endif;?>


    <a href='/portal/importar/importar_usuario_unidade/<?php echo $this->passedArgs[0]; ?>' id='link_importacao_usuario_unidade' class='btn btn-warning' title='Importar Usuário Unidades'><i class='icon-plus icon-white'></i>Importar Usuários Unidades</a>

    <?= $html->link('<i class="icon-plus icon-white"></i> Incluir', 
        array('action' => 'incluir_por_cliente', $this->passedArgs[0]), 
        array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir Perfil')
    )?>
</div>
<table class='table table-striped tablesorter'>
    <thead>
        <tr>
            <th><?= $this->Html->link('Login', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Nome', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Email', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Perfil', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Administrador', 'javascript:void(0)') ?></th>
            <th><?= $this->Html->link('Status', 'javascript:void(0)') ?></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($usuarios as $usuario): ?>
        <tr>
            <td><?= $usuario['Usuario']['apelido'] ?></td>
            <td><?= $usuario['Usuario']['nome'] ?></td>
            <td><?= $usuario['Usuario']['email'] ?></td>
            <td><?= $usuario['Uperfil']['descricao'] ?></td>
            <?php if($usuario['Usuario']['admin'] == true) :?>
                <td><?= 'Sim' ?></td>
                <?php else: ?>
                <td><?= 'Não' ?></td>
            <?php endif; ?>
            <td><?= ($usuario['Usuario']['ativo'] ? 'ativo' : 'inativo') ?></td>
            <td><?=$html->link('', array('action' => 'editar_por_cliente', $usuario['Usuario']['codigo']), 
                array('class' => 'icon-edit', 'title' => 'Editar')
                ) ?>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php $this->addScript($this->Buonny->link_css('tablesorter')) ?>
<?php $this->addScript($this->Buonny->link_js('jquery.tablesorter.min')) ?>
<?php $this->addScript($this->Javascript->codeBlock("
jQuery('table.table').tablesorter({sortList: [[0,1]], headers: {3: {sorter:false}} })")) ?>
