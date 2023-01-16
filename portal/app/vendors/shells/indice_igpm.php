<?php
App::import('Vendor','IGPMClient', array('file' => 'igpm/igpm_client.php'));
App::import('Model','Igpm');

class IndiceIgpmShell extends Shell {
    var $uses = array('Igpm');
    
    function main() {
        $IGPM = new IGPMClient();
        $result = $IGPM->getUltimos12Meses();
        for ($i = 0; $i < count($result->SERIE->ITEM); $i++) {
            $arr[] = get_object_vars($result->SERIE->ITEM[$i]);
        }

        $dados_insert = array();
        $dados_update = array();
        
        foreach($arr as $chave => $valor) {
            list($mes, $ano) = explode('/', $valor['DATA']);
            $dados_insert[$chave]['mes'] = $ano.str_pad($mes, 2, '0', STR_PAD_LEFT).'01 00:00:00';
            $dados_insert[$chave]['aliquota'] = $valor['VALOR'];
            $mes_da_base = $this->Igpm->find('first', array('conditions' => array('mes' => $dados_insert[$chave]['mes'])));
            if(!empty($mes_da_base)) {
                if($mes_da_base['Igpm']['aliquota'] != $dados_insert[$chave]['aliquota']) {
                    $mes_da_base['Igpm']['aliquota'] = $dados_insert[$chave]['aliquota'];
                    array_push($dados_update, $mes_da_base['Igpm']);
                }
                unset($dados_insert[$chave]);
            }
        }
        if(!empty($dados_update))
            $this->Igpm->saveAll($dados_update);
        if(!empty($dados_insert))
            $this->Igpm->saveAll($dados_insert);
    }
}
?>
