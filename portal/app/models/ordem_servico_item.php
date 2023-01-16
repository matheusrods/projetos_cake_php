<?php

class OrdemServicoItem extends AppModel {

	var $name = 'OrdemServicoItem';
	var $tableSchema = 'dbo';
	var $databaseTable = 'RHHealth';
	var $useTable = 'ordem_servico_item';
	var $primaryKey = 'codigo';
	var $actsAs = array('Secure');

	function atualiza_status($codigo_cliente,$status){
        $conditions = array('codigo_cliente' => $codigo_cliente);

        $cliente = $this->find('first', array('conditions' => $conditions));
        
        if(!empty($cliente)){ //CLIENTE NÃO POSSUI PPRA.
            if($cliente['OrdemServico']['status']!=3){
                $dados = array('OrdemServico' =>
                            array(
                                    'codigo' => $cliente['OrdemServico']['codigo'],
                                    'codigo_cliente' => $cliente['OrdemServico']['codigo_cliente'],
                                    'status' => $status
                                )
                        );  

                if($this->save($dados)){
                    return true;
                } 
                else {                
                    return false;
                }
            }
            else{
                return true;
            }
        }
        else{ // CLIENTE JA POSSUI PPRA. CADASTRAR OS DADOS SOMENTE.

            return false;
        }
    }    

}

?>