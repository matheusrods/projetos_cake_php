<div class='well'>
	<?php echo $this->Bajax->form('Notafis', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'Notafis', 'element_name' => 'ranking_faturamento'), 'divupdate' => '.form-procurar')) ?>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('mes', array('type' => 'select', 'options' => $meses, 'class' => 'input-small', 'label' => false, 'default' => date('m'))); ?>
		<?php echo $this->BForm->input('ano', array('type' => 'select', 'options' => $anos, 'class' => 'input-small', 'label' => false, 'default' => date('Y'))); ?>
		<?php echo $this->Buonny->input_grupo_empresas($this, $grupos_empresas, $empresas); ?>
		<?php echo $this->BForm->input('razao_social', array('label' => false, 'placeholder' => 'Nome','class' => 'input-large', 'type' => 'text'))."" ?>
	</div>
	<div class="row-fluid inline">
		<?php echo $this->BForm->input('gestores', array('type' => 'select', 'options' => $gestores, 'class' => 'input-large', 'label' => false, 'empty' => 'Todos Gestores')); ?>
		<?php echo $this->BForm->input('corretoras', array('type' => 'select', 'options' => $corretoras, 'class' => 'input-large', 'label' => false, 'empty' => 'Todas Corretoras')); ?>
		<?php echo $this->BForm->input('seguradoras', array('type' => 'select', 'options' => $seguradoras, 'class' => 'input-large', 'label' => false, 'empty' => 'Todas Seguradoras')); ?>
		<?php echo $this->BForm->input('produtos', array('type' => 'select', 'options' => $produtos, 'class' => 'input-large', 'label' => false, 'empty' => 'Todos Produtos')); ?>
		
	</div>

	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')); ?>
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
	<?php echo $this->BForm->end();?>
</div>
<?php echo $this->Javascript->codeBlock('
    jQuery(document).ready(function(){
        atualizaListaRankingFaturamento("clientes");
        setup_datepicker();
        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:Notafis/element_name:ranking_faturamento/" + Math.random())
        });
    });', false);
?>