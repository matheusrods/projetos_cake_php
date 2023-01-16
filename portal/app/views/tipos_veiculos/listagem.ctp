<?php
    if($session->read('Message.flash.params.type') == MSGT_SUCCESS){
        $classe = 'alert-success';
    } else if($session->read('Message.flash.params.type') == MSGT_ERROR){
        $classe = 'alert-error';
    } else {
        $classe = 'alert-info';
    }
?>
<?php if ($message = $session->flash()): ?>
<?php $session->delete('Message.flash'); ?>
<div id="flash_data" class="message" style="vertical-align: middle;">
    <div class="alert <?php echo $classe?>" style="vertical-align: middle;"><br/><?php echo $message; ?><br/></div>
</div>
<?php echo $this->Javascript->codeBlock("
    $(document).ready(function() {
      // fade out flash 'success' messages
      $('#flash_data').width($('.container').width());
      $('#flash_data').delay(3000).hide('highlight', {}, 3000);
    });
");?>
<?php endif; ?>
<div class='actionbar-right'>
    <?= $html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Nova Proposta')) ?>
</div>
<?php 
    if (isset($paginator)):
        echo $paginator->options(array('update' => 'div.lista')); 
?>
        <? if (!empty($tipos_veiculo)): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="input-mini numeric">Código</th>
                    <th>Descrição</th>
                    <th style="width: 45px">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($tipos_veiculo as $tipo_veiculo): ?>
                    <tr>
                        <td class="input-mini numeric"><?= $tipo_veiculo['TTveiTipoVeiculo']['tvei_codigo'] ?></td>
                        <td><?= $tipo_veiculo['TTveiTipoVeiculo']['tvei_descricao'] ?></td>
                        <td class="pagination-centered">
                            <?php echo $this->Html->link('', array('action' => 'editar', $tipo_veiculo['TTveiTipoVeiculo']['tvei_codigo']), array('class' => 'icon-edit ', 'title' => 'Editar')); ?>
                            <?php echo $this->Html->link('', array('action'=>false), array('class' => 'icon-trash', 'title' => 'Excluir tipo_veiculo', 'onclick'=>'javascript: inativar_tipo_veiculo('.$tipo_veiculo['TTveiTipoVeiculo']['tvei_codigo'].' ); return false;')) ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>        
            </tbody>
        </table>

        <div class='row-fluid'>
        	<div class='numbers span6'>
        		<?php echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
        	  <?php echo $this->Paginator->numbers(); ?>
        		<?php echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
        	</div>
        	<div class='counter span6'>
        		<?php echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
        	</div>
        </div>
        <?php echo $this->Js->writeBuffer(); ?>
        <?php echo $this->Buonny->link_js('estatisticas') ?>
        <? else: ?>
            <div class="alert">Nenhum Registro Encontrado</div>
        <? endif; ?>
<?php
    endif;
?>
<?php echo $this->Javascript->codeBlock('
    function inativar_tipo_veiculo(codigo) {
        if (!confirm("Deseja realmente excluir este Tipo de Veículo?")) return false;
        
        $.ajax({
            url: baseUrl + \'tipos_veiculos/excluir/\' + codigo + \'/\' + Math.random(),
            dataType: \'json\',
            success: function(data) {
                if (data){
                    $.ajax({
                        url: baseUrl + "tipos_veiculos/listagem",
                        dataType: "html",
                        success: function(data_in){
                            $(".lista").html(data_in);
                        }
                    });
                } else {
                    location.href = \'tipos_veiculos/index\';
                }
            },
            error: function(data) {
                if (data){
                    $.ajax({
                        url: baseUrl + "tipos_veiculos/listagem",
                        dataType: "html",
                        success: function(data_in){
                            $(".lista").html(data_in);
                        }
                    });
                } else {
                    location.href = \'tipos_veiculos/index\';
                }            
            }
        });

    }


    jQuery(document).ready(function(){
        //
    });', false); 
?>    

