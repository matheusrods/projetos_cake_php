<div class='well'>
  	<?php echo $bajax->form('AparelhoAudiometrico', array('autocomplete' => 'off', 'url' => array('controller' => 'filtros', 'action' => 'filtrar', 'model' => 'AparelhoAudiometrico', 'element_name' => 'cliente_aparelho_audiometrico'), 'divupdate' => '.form-procurar')) ?>
        
        <div class="row-fluid inline">
            <?php if(!empty($_SESSION['Auth']['Usuario']['codigo_cliente'])): ?>
                <?php echo $this->BForm->input('name_cliente', array('class' => 'input-xlarge', 'value' => $nome_cliente, 'label' => 'Cliente', 'type' => 'text','readonly' => true)); ?>
                <?php echo $this->BForm->hidden('codigo_cliente', array('value' => $_SESSION['Auth']['Usuario']['codigo_cliente']));?>
            <?php endif; ?>               
                       
            <?php

            if($this->Buonny->seUsuarioForMulticliente()) { 
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', null, 'AparelhoAudiometrico'); 
            }
            else{
                echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente', 'Cliente', 'AparelhoAudiometrico', isset($codigo_cliente) ? $codigo_cliente : '');
            }

            ?>
        	<?php echo $this->Buonny->input_codigo_fornecedor($this, 'codigo_fornecedor', 'Cód. Fornecedor','Fornecedor','AparelhoAudiometrico'); ?>
    		<?php echo $this->BForm->input('ativo', array('options' => array('1' => 'Ativo','0' => 'Inativo'), 'empty' => 'Todos', 'class' => 'input-medium', 'label' => 'Status')); ?>
    	</div>
    	<div class="row-fluid inline">
            <?php echo $this->BForm->input('codigo_aparelho', array('class' => 'input-mini', 'placeholder' => 'Código', 'label' => 'Cód. Aparelho', 'type' => 'text')) ?>
            <?php echo $this->BForm->input('descricao', array('class' => 'input-xlarge', 'placeholder' => 'Descrição', 'label' => 'Descrição Aparelho')) ?>  
            <?php echo $this->BForm->input('fabricante', array('class' => 'input-xlarge', 'placeholder' => 'Fabricante', 'label' => 'Fabricante do Aparelho')) ?>   
        </div>
  	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>
  	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
  	<?php echo $this->BForm->end() ?>

<?php
    $btn_incluir_apaudi = "<a href='/portal/cliente_aparelho_audiometrico/incluir/' id='link_incluir_apaudi' class='btn btn-success' title='Cadastrar Aparelho Audiometrico'><i class='icon-plus icon-white'></i></a>";
    echo $this->Javascript->codeBlock('
	jQuery(document).ready(function(){

        var incluir_aparelho_audiometrico_botao = $("#incluir-aparelho-audiometrico");
        setup_datepicker();
        listagem();

		function listagem(){
            var div = jQuery(".lista");
            bloquearDiv(div);
            div.load(baseUrl + "cliente_aparelho_audiometrico/listagem/" + Math.random());
        }

		jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:AparelhoAudiometrico/element_name:cliente_aparelho_audiometrico/" + Math.random())

            if(incluir_aparelho_audiometrico_botao.html()) {
                incluir_aparelho_audiometrico_botao.html(" ");
            }
            listagem();
        });

        $(function() {
            var codigo_cliente = $("#AparelhoAudiometricoCodigoCliente");
                        
            if(codigo_cliente.val() > 0) {
                criaBotaoIncluir(incluir_aparelho_audiometrico_botao);
            }

            codigo_cliente.blur(function(){
                if(codigo_cliente.val() > 0) {
                    criaBotaoIncluir(incluir_aparelho_audiometrico_botao);
                } else {
                    incluir_aparelho_audiometrico_botao.html(" ");
                }
            });

            function criaBotaoIncluir(incluir_aparelho_audiometrico_botao) {
                var btn_incluir_apaudi  = "' . $btn_incluir_apaudi . '";
                incluir_aparelho_audiometrico_botao.html(btn_incluir_apaudi);
                $("#link_incluir_apaudi").attr("href", "/portal/cliente_aparelho_audiometrico/incluir/" + codigo_cliente.val())
            }
        });

	});', false);
?>

</div>