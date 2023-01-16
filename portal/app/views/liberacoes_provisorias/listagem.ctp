<?php echo $paginator->options(array('update' => 'div.lista')); ?>
<table class="table table-striped">
    <thead>
       <tr>
            <th><?php echo $this->Paginator->sort('Cliente', 'Cliente.razao_social');?></th>
            <th><?php echo $this->Paginator->sort('Produto', 'codigo_produto');?></th>
            <th><?php echo $this->Paginator->sort('Nome Profissional', 'codigo_profissional');?></th>
            <th><?php echo $this->Paginator->sort('CPF Profissional', 'Profissional.codigo_documento');?></th>
            <th><?php echo $this->Paginator->sort('Data validade', 'data_liberacao');?></th>
            <th></th>
       </tr>
    </thead>
    <?php foreach ($liberacoes as $liberacao): ?>
    <tr>
        <td><?php echo empty($liberacao['Cliente']['razao_social']) ? 'TODOS' : $liberacao['Cliente']['razao_social']; ?></td>
        <td><?php echo $liberacao['Produto']['descricao']; ?></td>
        <td><?php echo $liberacao['Profissional']['nome']; ?></td>
        <td><?php echo $liberacao['Profissional']['codigo_documento']; ?></td>
        <td><?php echo $liberacao['LiberacaoProvisoria']['data_liberacao']; ?></td>
        <td class="actions">
            <?php
            if (Comum::dateToTimestamp($liberacao['LiberacaoProvisoria']['data_liberacao']) >= time()){
                echo $this->Html->link('', array('action' => 'cancelar', $liberacao['LiberacaoProvisoria']['codigo']), array('class' => 'icon-trash evt-excluir-cliente-produto', 'title' => 'Excluir'), sprintf(__('Deseja cancelar perfil adequado por prazo?', true), $liberacao['LiberacaoProvisoria']['codigo']));
            }else{?>
                <span class="icon-trash desable_cancel" title="Data validade vencida"></span>
            <?php }?>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
        
<div class="row-fluid">
    <div class="numbers span6">
    	<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
        <?php echo $this->Paginator->numbers(); ?>
    	<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
    </div>
    <div class="counter span6">
        <?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
    </div>
</div>
<div id="dialog-confirm" title="Operação não permitida" style="display:none">
    <h5>Data validade vencida</h5>
    <p style="font-size:12px;text-align:justify;">Não é permitida a exclusão desta liberação pois a data de validade está vencida</p>    
</div>
<?php echo $this->Js->writeBuffer(); ?>
<?php echo $this->Javascript->codeBlock('
    $(function(){
        $(".desable_cancel").css("opacity", 0.3);
        $(document).on("click","#dialog-confirm .cancelar",function(){
            $( "#dialog-confirm" ).dialog( "close" );
            return false;
        });
        $(".desable_cancel").click(function(){
            $( "#dialog-confirm" ).dialog({
                resizable: false,
                height:180,
                width:300
            });      
            return false;        
        });        
    });', false);?>