<?php echo $this->BForm->create('Cliente', array('action' => 'editar_gerenciadoras', $codigo_cliente)); ?>
<div class="row-fluid">
    <?php echo $this->BForm->hidden('codigo'); ?>
    <?php echo $this->BForm->hidden('TPjurPessoaJuridica.pjur_pess_oras_codigo'); ?>
</div>
<div class='actionbar-right'>
    <?php echo $this->Html->link('Incluir', array('action' => 'adicionar_gerenciadora', $this->data['Cliente']['codigo'], rand()), array('onclick' => 'return open_dialog(this, "Adicionar Gerenciadora", 560)', 'title' => 'Adicionar Gerenciadora', 'class' => 'btn btn-success'));?>
</div>
<div class="lista">
</div>

<?php $this->addScript($this->Buonny->link_js('clientes.js')); ?>
<?php echo $this->Javascript->codeBlock('
	$(document).ready(function() {
        atualizaClienteGerenciadoras('.$this->data['Cliente']['codigo'].');
		$("a.excluir-gerenciadora").click(function(){
			if(confirm("Deseja excluir este registro?")){
				var link = $( this );
				$.ajax({
					url: link.attr("href"),
					success: function(data){
						if(data)alert(data);
                        window.location = window.location;
					},
				})
			}
			return false;
		});
	});
');
?>