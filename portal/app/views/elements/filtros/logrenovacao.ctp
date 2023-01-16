<?php echo $this->Buonny->link_css('fichas_scorecard'); ?>
<div class='well'>
    <h5><?= $this->Html->link((!empty($filtrado) ? 'Listagem Filtrada' : 'Definir Filtros'), 'javascript:void(0)', array('id' => 'filtros', 'class' => 'link-hide-show')) ?></h5>
    <div id='filtros'>
    	<?php echo $bajax->form('LogRenovacao', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'LogRenovacao', 'element_name' => 'logrenovacao'), 'divupdate' => '.form-procurar')) ?>
            <div class="row-fluid inline">
                <?php echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', false,'') ?>   
                <?php //echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente') ?>
                <?php //echo $this->BForm->input("Cliente.razao_social", array('label' => false, 'class' => 'input-xxlarge', 'readonly'=>true)) ?>  
                <?php echo $this->BForm->input('codigo_documento',array('label' => false,'type' => 'text','class' => 'input-medium formata-cpf', 'placeholder' => 'CPF')) ?>
                <?php echo $this->BForm->input('codigo_tipo_profissional',array('label' => false, 'empty' => 'Selecione uma Categoria','options' => $lista_tipo_profissional,'class'=>'input-large' ));?>
            
            </div>                
            <div class="row-fluid inline">
                <?php echo $this->BForm->input('contato', array('class' => 'input-mediun','label'=>false,'placeholder' => 'Contato')) ?>
                <?php echo $this->BForm->input('representante', array('class' => 'input-mediun','label'=>false,'placeholder' => 'Representante')) ?>
                <?php echo $this->BForm->input('usuario', array('class' => 'input-mediun','label'=>false,'placeholder' => 'Usuário')) ?>             
                <?php echo $this->Buonny->input_periodo($this,'LogRenovacao') ?>
            </div>

            <?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
            <?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php echo $this->BForm->end() ?>
    </div> 
</div> 

<?php echo $this->addScript($this->Buonny->link_js( array('fichas_scorecard', 'solicitacoes_monitoramento') )) ?>

<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        setup_mascaras(); 
        setup_codigo_cliente();
        var div = jQuery("div.lista");
        bloquearDiv(div);
        div.load(baseUrl + "fichas_scorecard/listagemLogRenovacao/" + Math.random()); 
        jQuery("a#filtros").click(function(){
            jQuery("div#filtros").slideToggle("slow");
        });
         
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:LogRenovacao/element_name:logrenovacao/" + Math.random())
        });
        
    });', false); 
?>
<?php if (!empty($filtrado)): ?>
    <?php echo $this->Javascript->codeBlock('jQuery(document).ready(function(){jQuery("div#filtros").hide()})');?>
 <?php endif; ?>
   
           