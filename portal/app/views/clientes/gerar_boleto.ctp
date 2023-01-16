<?php
	if(isset($formulario_itau) && !empty($formulario_itau)) {
		echo $this->Buonny->link_js('jquery');
		echo $formulario_itau;
		echo $this->Javascript->codeBlock("
		    $(document).ready(function(){
				$('#boletoItau').submit();
			});
		", false);
	}
?>