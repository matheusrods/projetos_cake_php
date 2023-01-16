<div class='well'>
  	<?php echo $bajax->form('PdaConfigRegra', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PdaConfigRegra', 'element_name' => 'index_pda_regra'), 'divupdate' => '.form-procurar')) ?>
        
        <div class="row-fluid inline">
            <?php
            if($this->Buonny->seUsuarioForMulticliente()) { 
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'PdaConfigRegra'); 
            }
            else if(!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
                echo $this->BForm->input('name_cliente', array('class' => 'input-xlarge', 'value' => $nome_cliente, 'label' => 'Cliente', 'type' => 'text','readonly' => true));
                echo $this->BForm->hidden('codigo_cliente', array('value' => $_SESSION['Auth']['Usuario']['codigo_cliente']));
            }
            else{
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'PdaConfigRegra', isset($codigo_cliente) ? $codigo_cliente : '');
            }
            ?>
    	</div>

        <div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo_pos_ferramenta', array('label' => false, 'class' => 'input-xlarge','options' => $pos_ferramenta, 'empty' => 'Selecione a ferramenta'));?>
        </div>

  	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
  	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  	<?php echo $this->BForm->end() ?>

<?php
    echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){

        setup_datepicker();
        listagem();

		function listagem(){
            var div = jQuery(".lista");
            bloquearDiv(div);
            div.load(baseUrl + "pda_config_regra/listagem_pda_regra/" + Math.random());
        }

		jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PdaConfigRegra/element_name:index_pda_regra/" + Math.random())
        
            listagem();
        });

	});', false);
?>

</div>