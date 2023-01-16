<div class='well'>
  	<?php echo $bajax->form('PosQtdParticipantes', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'PosQtdParticipantes', 'element_name' => 'index_qtd_participantes'), 'divupdate' => '.form-procurar')) ?>
        
        <div class="row-fluid inline">
            <?php
            if($this->Buonny->seUsuarioForMulticliente()) { 
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'PosQtdParticipantes'); 
            }
            else if(!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])){
                echo $this->BForm->input('name_cliente', array('class' => 'input-xlarge', 'value' => $nome_cliente, 'label' => 'Cliente', 'type' => 'text','readonly' => true)); 
                echo $this->BForm->hidden('codigo_cliente', array('value' => $_SESSION['Auth']['Usuario']['codigo_cliente']));
            }
            else{
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'PosQtdParticipantes', isset($codigo_cliente) ? $codigo_cliente : '');
            }
            ?>
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
            div.load(baseUrl + "swt/listagem_qtd_participantes/" + Math.random());
        }

		jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:PosQtdParticipantes/element_name:index_qtd_participantes/" + Math.random())
        
            listagem();
        });

	});', false);
?>

</div>