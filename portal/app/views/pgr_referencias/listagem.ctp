<div class='actionbar-right'>
    <?= $html->link('<i class="icon-plus icon-white"></i> Incluir', array('action' => 'incluir'), array('escape' => false, 'class' => 'btn btn-success', 'title' =>'Incluir PGR Alvo')) ?>
</div>
<?php 
    if (isset($paginator)):
        echo $paginator->options(array('update' => 'div.lista')); 
?>
        <?php if (!empty($pgrsAlvo)): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Alvo</th>
                    <th>PGR</th>
                    <th style="width: 45px">&nbsp;</th>
                </tr>
            </thead>
            <tbody>
                    <?php foreach ($pgrsAlvo as $pgr): ?>
                    <tr>
                        <td><?= $pgr['TRefeReferencia']['refe_descricao'] ?></td>
                        <td><?php echo $this->Html->link($pgr['TPgpgPg']['pgpg_codigo'], 'javascript:void(0)', array('onclick' => "visualizar_pgr('{$pgr['TPgpgPg']['pgpg_codigo']}')")) ?></td>
                        <td class="pagination-centered">
                            <?php echo $this->Html->link('', array('action'=>false), array('class' => 'icon-trash', 'title' => 'Excluir PGR Alvo', 'onclick'=>'javascript: inativar_pgr_alvo('.$pgr['TPrefPgrReferencia']['pref_codigo'].' ); return false;')) ?>
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
        <?php else: ?>
            <div class="alert">Nenhum Registro Encontrado</div>
        <?php endif; ?>
<?php
    endif;
?>
<?php echo $this->Javascript->codeBlock("
    function visualizar_pgr(codigo_pgr) {
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('action', '/portal/pgpg_pgs/consulta_pgr/');
        form.setAttribute('target', form_id);
        field = document.createElement('input');
        field.setAttribute('name', 'data[TPgpgPg][pgpg_codigo]');
        field.setAttribute('value', codigo_pgr);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        document.body.appendChild(form);
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-30)+',width='+(janela.width-80).toString()+',resizable=yes,toolbar=no,status=no');
        form.submit();
    }
    function inativar_pgr_alvo(codigo) {
        if (!confirm('Deseja realmente inativar este PGR para este Alvo?')) return false;
        
        $.ajax({
            url: baseUrl + 'pgr_referencias/inativar/' + codigo + '/' + Math.random(),
            dataType: 'json',
            success: function(data) {
                if (data){
                    $.ajax({
                        url: baseUrl + 'pgr_referencias/listagem',
                        dataType: 'html',
                        success: function(data_in){
                            $('.lista').html(data_in);
                        }
                    });
                } else {
                    location.href = 'pgr_referencias/index';
                }
            },
        });

    }


    jQuery(document).ready(function(){
        //
    });", false); 
?>    

