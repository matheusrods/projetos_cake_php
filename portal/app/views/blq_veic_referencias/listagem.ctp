<div class='actionbar-right'>
    <?= $html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Nova Proposta')) ?>
</div>
<?php 
    if (isset($paginator)):
        echo $paginator->options(array('update' => 'div.lista')); 
?>
        <? if (!empty($bloqueios)): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Alvo</th>
                    <th>Placa</th>
                    <th style="width: 45px">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($bloqueios as $bloqueio): ?>
                    <tr>
                        <td><?=(!empty($bloqueio['TRefeReferencia']['refe_descricao']) ? $bloqueio['TRefeReferencia']['refe_descricao'] : 'TODOS') ?></td>
                        <td><?=$this->Buonny->placa($bloqueio['TVeicVeiculo']['veic_placa'], Date('d/m/Y 00:00:00'), Date('d/m/Y 23:59:59')) ?></td>
                        <td class="pagination-centered">
                            <?php echo $this->Html->link('', array('action'=>false), array('class' => 'icon-trash', 'title' => 'Excluir Bloqueio', 'onclick'=>'javascript: inativar_bloqueio('.$bloqueio['TBvreBlqVeicReferencia']['bvre_codigo'].' ); return false;')) ?>
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
    function inativar_bloqueio(codigo) {
        if (!confirm("Deseja realmente inativar este bloqueio?")) return false;
        
        $.ajax({
            url: baseUrl + \'blq_veic_referencias/inativar/\' + codigo + \'/\' + Math.random(),
            dataType: \'json\',
            success: function(data) {
                if (data){
                    $.ajax({
                        url: baseUrl + "blq_veic_referencias/listagem",
                        dataType: "html",
                        success: function(data_in){
                            $(".lista").html(data_in);
                        }
                    });
                } else {
                    location.href = \'blq_veic_referencias/index\';
                }
            },
        });

    }


    jQuery(document).ready(function(){
        //
    });', false); 
?>    

