<div class="row-fluid inline">
    <?php 

    if($this->Buonny->seUsuarioForMulticliente()){
        echo $this->Buonny->input_codigo_cliente($this, 'codigo_cliente', 'Cliente' ,'Código', 'Cliente'); 
    }else{
        echo $this->Buonny->input_codigo_cliente2($this, array('input_name' => 'codigo_cliente', 'label' => 'Código', 'name_display' => array('label' => 'Cliente'), 'checklogin' => false), 'PreFaturamento');
    }   

    echo $this->BForm->input('status', array('label' => 'Status', 'type' => 'select','class' => 'input-large form-control', 'options' => $status)); 

echo $this->BForm->input('mes', array('label' => 'Data da Baixa', 'placeholder' => 'Mês', 'type' => 'select', 'class' => 'input-large form-control', 'options' => $meses/*, 'default'=> array(0 =>'Selecione...')*/ )); 
echo $this->BForm->input('ano', array('label' => '&nbsp', 'placeholder' => 'Ano', 'type' => 'text', 'class' => 'input-small form-control'/*, 'default'=>date("Y")*/));

    ?>
</div>

<style type="text/css">
	.error-message{ color: red; }
</style>


