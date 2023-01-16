<?php
    echo @$paginator->options(array('update' => 'div.lista'));
    $total_paginas = $this->Paginator->numbers();
?>
<table class="table table-striped">
    <thead>
        <tr>
            <th><?=$this->Paginator->sort('Nome', 'nome') ?></th>
            <th><?=$this->Paginator->sort('CPF', 'codigo_documento') ?></th>
            <th><?=$this->Paginator->sort('RG', 'rg') ?></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($profissionais as $profissional): 
        
        ?>
        <tr>
            <td><?=$profissional[0]['nome'] ?></td>            
            <td><?=$buonny->documento($profissional[0]['codigo_documento']) ?></td>
            <td><?=$buonny->documento($profissional[0]['rg']) ?></td>
            <?php if ($profissional[0]['tem_pesquisa'] == 0 and $profissional[0]['codigo_ficha']!='') {?>
                     
                 <td>   
                    <?php echo $html->link('', array('controller' => 'fichas_status_criterios', 'action' => 'resultado_ficha', $profissional[0]['codigo_ficha']), array('title' => 'Resultado detalhado', 'class'=>'icon-search')); ?>
                </td>
                <td>    
                    <?php     
                         echo $html->link('', array('controller' => 'fichas_status_criterios', 'action' => 'alterar_score', $profissional[0]['codigo_ficha']), array('title' => 'Alterar score', 'class'=>'icon-cog')); 
                          
                    ?>

                </td>
                
             <?php }?>
             <?php if ($profissional[0]['tem_pesquisa'] > 0) {?>
                    <td colspan='2' ><b><font color='red'>PROFISSIONAL EM PESQUISA<b></font></td>
             <?php }?>
             <?php if ($profissional[0]['codigo_ficha'] == '') {?>
                    <td colspan='2' ><b><font color='red'>PROFISSIONAL SEM FICHA<b></font></td>
             <?php }?>
        </tr>
        <?php endforeach; ?>
<!--
        <tfoot>
        <?php //if( isset($ocorrencias) ): ?>
            <tr>
                <td><strong>Total</strong></td>
                <td colspan="6" class="input-xlarge"><strong>
                    <?php 
                        //if($this->Paginator->counter('{:count}') > 1)
                          //  echo $this->Paginator->counter('{:count}')." Profissinais";
                        //else
                          //  echo $this->Paginator->counter('{:count}')." Profissionais";
                    ?></strong>
                </td>
            </tr>
        <?php  //endif;?>
    </tfoot> -->

    </tbody>
</table>
<!--
<div class='row-fluid'>
	<div class='numbers span6'>
		<?php //echo $this->Paginator->prev('Página Anterior', null, null, array('class' => 'disabled paginacao_anterior')); ?>
	  <?php //echo $this->Paginator->numbers(); ?>
		<?php //echo $this->Paginator->next('Próxima Página', null, null, array('class' => 'disabled paginacao_proximo')); ?>
	</div>
	<div class='counter span6'>
	    <?php //echo $this->Paginator->counter(array('format' => 'Página %page% de %pages%')); ?>
	</div>
</div>
<?php //echo $this->Js->writeBuffer(); ?>
 -->