<div class='well'>
  	<?php echo $bajax->form('PosSwtForm', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PosSwtForm', 'element_name' => 'index_form'), 'divupdate' => '.form-procurar')) ?>
        
        <div class="row-fluid inline">
            <?php
            $is_filtro = 1;
            if($this->Buonny->seUsuarioForMulticliente()) { 
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'PosSwtForm');

            }
            else if(!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])) {
                echo $this->BForm->input('name_cliente', array('class' => 'input-xlarge', 'value' => $nome_cliente, 'label' => 'Cliente', 'type' => 'text','readonly' => true)); 
                echo $this->BForm->hidden('codigo_cliente', array('value' => $_SESSION['Auth']['Usuario']['codigo_cliente']));
                $is_filtro = 0;
            }
            else{
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'PosSwtForm', isset($codigo_cliente) ? $codigo_cliente : '');
            }
            
            ?>
    	</div>
        <?php if($is_filtro) : ?>
          	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
          	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
        <?php endif; ?>
  	<?php echo $this->BForm->end() ?>

<?php
    echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){

        setup_datepicker();
        listagem();

		function listagem(){
            var div = jQuery(".lista");
            bloquearDiv(div);
            div.load(baseUrl + "swt/listagem_form/" + Math.random());
        }

		jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PosSwtForm/element_name:index_form/" + Math.random())
        
            listagem();
        });

	});', false);
?>

</div>