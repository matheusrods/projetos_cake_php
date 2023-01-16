<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $session->delete('Message.flash');
        echo $javascript->codeBlock(
            "close_dialog();
            var div = jQuery('div.lista');
            bloquearDiv(div);
            div.load(baseUrl + 'supervisores_equipes/listagem/' + Math.random());");
        exit;
    } else if($session->read('Message.flash.params.type') == MSGT_ERROR){
        $session->delete('Message.flash');
    }
?>
<?php echo $bajax->form('Usuario', array('url' => array('controller' => 'supervisores_equipes', 'action' => 'remanejamento', $codigo_usuario_pai, $codigo_uperfil ))); ?>
<div class='row-fluid inline'>
    <div class='well'>
        <div class="row-fluid inline" >
            <span class="span4">
                De: <strong><?php echo strtoupper($dados_responsavel_atual['Usuario']['apelido']); ?></strong> para: 
            </span>
            <span class="span8">
                <?php echo $this->BForm->input('Usuario.codigo_usuario_pai', array('label' => false, 'empty'=>'Selecione o responsável' , 'options' => $lista_usuarios_pais,'class'=>'input-xlarge' ));?>
            </span>
        </div>
    </div>    
</div>
<h4>Equipe</h4>
<div class='row-fluid inline filhos'>
    <table class='table table-striped' data-index="0">
        <thead>
            <th>Usuário</th>
            <th>Nome</th>
            <th>Responsável real</th>
        </thead>
    <?php foreach ($lista_filhos as $key => $dados_filho ):?>
        <tr>
            <td><?php echo $dados_filho['Usuario']['apelido'];?></td>
            <td> <?php echo $dados_filho['Usuario']['nome'];?></td>
            <td> 
                <?php if(!empty($dados_filho['Usuario']['codigo_usuario_pai_real']) && ($dados_filho['Usuario']['codigo_usuario_pai'] != $dados_filho['Usuario']['codigo_usuario_pai_real'])):?>
                    <?php echo $dados_filho['UsuarioPaiReal']['apelido'];?>
                <?php endif; ?>
            </td>
        </tr>    
    <?php endforeach; ?>
    </table>
</div>
<div class="form-actions">
    <?php echo $this->BForm->submit('Salvar', array('div' => false, 'class' => 'btn btn-success')); ?>
    <?php echo $html->link('Voltar', 'javascript:close_dialog()', array('class' => 'btn')); ?>
</div>
<?php echo $this->BForm->end(); ?>