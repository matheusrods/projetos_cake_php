 <div class="row-fluid inline">
            <?php echo $this->Buonny->input_codigo_cliente($this); ?>
</div>
 <div class="row-fluid inline">
            <?php echo $this->Buonny->input_posicao_exames($this,"Exame",$unidades, $setores, $exames); ?>
</div>

<style type="text/css">
	.error-message{
		color: red;
	}
</style>