<div class="well">
	<div class="row-fluid inline">
		<?php echo $this->Buonny->input_setor_cargo($this, 'Consulta', $codigo_cliente ); ?>
        <?php if($tipo == 'pcmso'){
            echo $this->Buonny->input_nome_funcionario_sem_label($this, 'Consulta', null, $codigo_cliente);
        }
        ?>
        <?php echo $this->BForm->input('Consulta.status', array('options' => $options_status, 'empty' => 'Status', 'class' => 'input-medium', 'label' => false)); ?>
	</div>
	<?php echo $this->BForm->submit('Buscar', array('div' => false, 'class' => 'btn')) ?>		
	<?php echo $html->link('Limpar busca', 'javascript:void(0)', array('id' => 'limpar-filtro', 'class' => 'btn')) ;?>
</div>

<?php echo $this->Javascript->codeBlock('
		
	$(function(){

        atualizaLista();

        jQuery("#limpar-filtro").click(function(){
            bloquearDiv(jQuery(".form-procurar"));
            jQuery(".form-procurar").load(baseUrl + "filtros/limpar/model:Consulta/element_name:pcmso_ppra_pendente_sc_terceiros/" + Math.random())
        });	
        
        function atualizaLista() {
            var div = jQuery("div.lista");
            bloquearDiv(div);
            div.load(baseUrl + "consultas/listagem_ppra_pcmso_pendente_sc_terceiros/'. $codigo_cliente .'/'.$tipo.'/" + Math.random());
        }
        
    });', false);
?>
