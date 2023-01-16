<?php

class AprovacaoAutomaticaExamesShell extends Shell {
    var $uses = array(
        'AuditoriaExame',
        'AnexoExame',
        'AnexoFichaClinica'
    );

  
    function main() {
        echo "Funcoes: \n";
        echo "=> aprovacao_automatica_anexos \n";
    }

    function is_alive(){
        $retorno = shell_exec("ps -ef | grep \"envio_email_anexos_reprovados \" | wc -l");
        return ($retorno > 3);
    }

    function aprovar(){
        if($this->is_alive())
            return false;
        
        $this->aprovacao_automatica_anexos();
    }

    function aprovacao_automatica_anexos(){

        $query_params = $this->getExamesPendentes();
		$exames_pendentes = $this->AuditoriaExame->find('all',
            array(
                'fields' => $query_params['fields'],
                'joins' => $query_params['joins'],
                'conditions' => $query_params['conditions'],
                'order' => $query_params['order'],
            )
        );
        $count_exames = 0;

        foreach($exames_pendentes as $exame_pendente){

            $auditoria_save = array(
                'AuditoriaExame' => array(
                'codigo' => $exame_pendente[0]['auditoria_codigo'],
                'aprovacao_automatica' => 1,
                'codigo_status_auditoria_imagem' => 3
                )
            );
            $anexo_save = array(
                'AnexoExame' => array(
                    'codigo' => $exame_pendente[0]['anexo_codigo'],
                    'aprovado_auditoria' => 1
                )
            );
            
            if(!$this->AuditoriaExame->atualizar($auditoria_save)){
                echo "Ocorreu um erro ao salvar a codigo da auditoria: ".$auditoria_save['codigo']. "\n" ;
                return false;
            }
            if(!$this->AnexoExame->atualizar($anexo_save)){
                echo "Ocorreu um erro ao salvar o codigo do anexo: ".$anexo_save['codigo']."\n";
                return false;
            }
            $count_exames++; 
        }

        $query_params_fichas = $this->getFichasClinicasAprovadas();
		$aprovar_fichas = $this->AuditoriaExame->find('all',
            array(
                'fields' => $query_params_fichas['fields'],
                'joins' => $query_params_fichas['joins'],
                'conditions' => $query_params_fichas['conditions'],
                'order' => $query_params_fichas['order'],
            )
        );

        
        $count_fichas = 0;

        foreach($aprovar_fichas as $aprovar_ficha){


            $ficha_aprovar_save = array(
                'AnexoFichaClinica' => array(
                    'codigo' => $aprovar_ficha[0]['anexo_codigo'],
                    'aprovado_auditoria' => 1
                )
            );
            
            if(!$this->AnexoFichaClinica->atualizar($ficha_aprovar_save)){
                echo "Ocorreu um erro ao salvar o codigo da ficha: ".$ficha_aprovar_save['codigo']."\n";
                return false;
            }
            $count_fichas++; 
        }

        $query_params_fichas = $this->getFichasClinicasPendentes();
		$fichas_pendentes = $this->AuditoriaExame->find('all',
            array(
                'fields' => $query_params_fichas['fields'],
                'joins' => $query_params_fichas['joins'],
                'conditions' => $query_params_fichas['conditions'],
                'order' => $query_params_fichas['order'],
            )
        );

        foreach($fichas_pendentes as $ficha_pendente){

            $auditoria_save = array(
                'AuditoriaExame' => array(
                'codigo' => $ficha_pendente[0]['auditoria_codigo'],
                'aprovacao_automatica' => 1,
                'codigo_status_auditoria_imagem' => 3
                )
            );
            $ficha_clinica_save = array(
                'AnexoFichaClinica' => array(
                    'codigo' => $ficha_pendente[0]['anexo_codigo'],
                    'aprovado_auditoria' => 1
                )
            );
            
            if(!$this->AuditoriaExame->atualizar($auditoria_save)){
                echo "Ocorreu um erro ao salvar a codigo da auditoria: ".$auditoria_save['codigo']. "\n" ;
                return false;
            }
            if(!$this->AnexoFichaClinica->atualizar($ficha_clinica_save)){
                echo "Ocorreu um erro ao salvar o codigo da ficha: ".$ficha_clinica_save['codigo']."\n";
                return false;
            }
            $count_fichas++; 
        }

        echo $count_exames." exames aprovadas automaticamente.\n";
        echo $count_fichas." fichas aprovadas automaticamente.\n";

    }

    private function getExamesPendentes(){

        $fields = array(
            'AuditoriaExame.codigo as auditoria_codigo',
            'AuditoriaExame.codigo_item_pedido_exame as auditoria_codigo_ipe',
            'AuditoriaExame.codigo_status_auditoria_imagem as status_imagem',
			'AnexosExames.data_inclusao as anexo_inclusao',
            'AnexosExames.codigo as anexo_codigo',
            'AnexosExames.aprovado_auditoria as anexo_aprovado_auditoria'
        );

        $joins = array(
			array(
                'table' => 'anexos_exames',
                'alias' => 'AnexosExames',
                'type' => 'INNER',
                'conditions' => 'AnexosExames.codigo_item_pedido_exame = AuditoriaExame.codigo_item_pedido_exame'
            ),            
        );

        $conditions = array(
            'AnexosExames.data_inclusao < ' => '2021-11-03 23:59:59',
            '(AuditoriaExame.codigo_status_auditoria_imagem is null OR AuditoriaExame.codigo_status_auditoria_imagem = 1)',
            'AnexosExames.aprovado_auditoria is null'
        );

        $order = array(
            'AnexosExames.data_inclusao' => 'ASC',
        );



	    $exames = array(
			'fields' => $fields,
			'joins' => $joins, 
			'conditions' => $conditions, 
			'order' => $order, 
		);

		return $exames;
    }

    private function getFichasClinicasAprovadas(){

        $fields = array(
            'AuditoriaExame.codigo as auditoria_codigo',
            'ItemPedidoExame.codigo as codigo_ipe',
            'AuditoriaExame.codigo_status_auditoria_imagem as status_imagem',
			'AnexoFichaClinica.data_inclusao as anexo_inclusao',
            'AnexoFichaClinica.codigo as anexo_codigo',
            'AnexoFichaClinica.aprovado_auditoria as anexo_aprovado_auditoria'
        );

        $joins = array(
            array(
                'table' => 'itens_pedidos_exames',
                'alias' => 'ItemPedidoExame',
                'type' => 'INNER',
                'conditions' => 'AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo'
            ),
            array(
                'table' => 'pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
            ),
			array(
                'table' => 'fichas_clinicas',
                'alias' => 'FichaClinica',
                'type' => 'INNER',
                'conditions' => 'FichaClinica.codigo_pedido_exame = PedidoExame.codigo'
            ),    
            array(
                'table' => 'anexos_fichas_clinicas',
                'alias' => 'AnexoFichaClinica',
                'type' => 'INNER',
                'conditions' => 'AnexoFichaClinica.codigo_ficha_clinica = FichaClinica.codigo'
            ),    
        );

        $conditions = array(
            'AnexoFichaClinica.data_inclusao < ' => '2021-11-03 23:59:59',
            'AnexoFichaClinica.aprovado_auditoria is null',
            'AuditoriaExame.aprovacao_automatica' => 1,
            'ItemPedidoExame.codigo_exame' => 52
        );

        $order = array(
            'AnexoFichaClinica.data_inclusao' => 'ASC',
        );



	    $exames = array(
			'fields' => $fields,
			'joins' => $joins, 
			'conditions' => $conditions, 
			'order' => $order, 
		);

		return $exames;
    }

    private function getFichasClinicasPendentes(){

        $fields = array(
            'AuditoriaExame.codigo as auditoria_codigo',
            'ItemPedidoExame.codigo as codigo_ipe',
            'AuditoriaExame.codigo_status_auditoria_imagem as status_imagem',
			'AnexoFichaClinica.data_inclusao as anexo_inclusao',
            'AnexoFichaClinica.codigo as anexo_codigo',
            'AnexoFichaClinica.aprovado_auditoria as anexo_aprovado_auditoria'
        );

        $joins = array(
            array(
                'table' => 'itens_pedidos_exames',
                'alias' => 'ItemPedidoExame',
                'type' => 'INNER',
                'conditions' => 'AuditoriaExame.codigo_item_pedido_exame = ItemPedidoExame.codigo'
            ),
            array(
                'table' => 'pedidos_exames',
                'alias' => 'PedidoExame',
                'type' => 'INNER',
                'conditions' => 'ItemPedidoExame.codigo_pedidos_exames = PedidoExame.codigo'
            ),
			array(
                'table' => 'fichas_clinicas',
                'alias' => 'FichaClinica',
                'type' => 'INNER',
                'conditions' => 'FichaClinica.codigo_pedido_exame = PedidoExame.codigo'
            ),    
            array(
                'table' => 'anexos_fichas_clinicas',
                'alias' => 'AnexoFichaClinica',
                'type' => 'INNER',
                'conditions' => 'AnexoFichaClinica.codigo_ficha_clinica = FichaClinica.codigo'
            ),    
        );

        $conditions = array(
            'AnexoFichaClinica.data_inclusao < ' => '2021-11-03 23:59:59',
            '(AuditoriaExame.codigo_status_auditoria_imagem is null OR AuditoriaExame.codigo_status_auditoria_imagem = 1)',
            'AnexoFichaClinica.aprovado_auditoria is null',
            'ItemPedidoExame.codigo_exame' => 52
        );

        $order = array(
            'AnexoFichaClinica.data_inclusao' => 'ASC',
        );



	    $exames = array(
			'fields' => $fields,
			'joins' => $joins, 
			'conditions' => $conditions, 
			'order' => $order, 
		);

		return $exames;
    }
}