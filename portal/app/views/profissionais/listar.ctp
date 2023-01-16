<?php
    echo $paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?=$this->Paginator->sort('Nome', 'nome') ?></th>
            <th><?=$this->Paginator->sort('Cod. Documento', 'codigo_documento') ?></th>
            <th><?=$this->Paginator->sort('Rg', 'rg') ?></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($profissionais as $profissional): ?>
        <tr>
            <td><?=$profissional['Profissional']['nome'] ?></td>            
            <td><?=$buonny->documento($profissional['Profissional']['codigo_documento']) ?></td>
            <td><?=$buonny->documento($profissional['Profissional']['rg']) ?></td>
            <td>
            	<?=$html->link('', array('action' => 'editar_profissional',  $profissional['Profissional']['codigo']), array('class' => 'icon-edit', 'title' => 'Editar')) ?>
            </td>
            <td>
                <?php echo $this->Html->link('','javascript:void(0)',array('onclick' => "profissional_logs( '{$profissional['Profissional']['codigo_documento']}' )",'class' => 'icon-eye-open', 'title' => 'Logs de alterações')); ?>
            </td>
        </tr>
        <?php endforeach; ?>

        <tfoot>
        <?php if( isset($ocorrencias) ): ?>
            <tr>
                <td><strong>Total</strong></td>
                <td colspan="6" class="input-xlarge"><strong>
                    <?php 
                        if($this->Paginator->counter('{:count}') > 1)
                            echo $this->Paginator->counter('{:count}')." Profissinais";
                        else
                            echo $this->Paginator->counter('{:count}')." Profissionais";
                    ?></strong>
                </td>
            </tr>
        <?php  endif;?>
    </tfoot>
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
<?php echo $this->Javascript->codeBlock("
    function profissional_logs( codigo_documento ) {
        var form = document.createElement('form');
        var form_id = ('formresult' + Math.random()).replace('.','');
        form.setAttribute('method', 'post');
        form.setAttribute('target', form_id );
        form.setAttribute('action', '/portal/profissionais_logs/index/' + Math.random());
        
        field = document.createElement('input');
        field.setAttribute('name', 'data[ProfissionalLog][codigo_documento]');
        field.setAttribute('value', codigo_documento);
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[ProfissionalLog][nome]');
        field.setAttribute('value', '');
        field.setAttribute('type', 'hidden');
        form.appendChild(field);

        field = document.createElement('input');
        field.setAttribute('name', 'data[ProfissionalLog][popup]');
        field.setAttribute('value', 1 );
        field.setAttribute('type', 'hidden');
        form.appendChild(field);
        
        var janela = window_sizes();
        window.open('', form_id, 'scrollbars=yes,menubar=no,height='+(janela.height-200)+',width='+(janela.width-80)+',resizable=yes,toolbar=no,status=no');
        document.body.appendChild(form);
        form.submit();
    }");?>