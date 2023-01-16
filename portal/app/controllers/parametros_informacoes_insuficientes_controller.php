<?php
class ParametrosInformacoesInsuficientesController extends AppController {
    var $name = 'ParametrosInformacoesInsuficientes';
    var $uses = array('ParametroInfoInsuficiente');

    function index() {
        $this->pageTitle    = 'Parâmetros para Informações Insuficientes';
        $parametros         = $this->ParametroInfoInsuficiente->find('all', array('order' => 'codigo_parametro_pai, descricao ASC', 'conditions' => array('ativo <>' => 0  )));

        $i = 0;

        foreach($parametros as $parametro) {
            $categorias[$i] = $this->ParametroInfoInsuficiente->find('first',array('conditions' => array('codigo' => $parametro['ParametroInfoInsuficiente']['codigo_parametro_pai'], 'ativo <>' => 0 )));
            $parametros[$i]['ParametroInfoInsuficiente']['categoria'] = $categorias[$i]['ParametroInfoInsuficiente']['codigo'];
            $i++;

        }

        $this->set(compact('parametros','categorias'));
    }

    
    function incluir() {
        $this->pageTitle = 'Incluir Parâmetro de Informação Insuficiente';
        
        if (!empty($this->data)){
            $this->data['ParametroInfoInsuficiente']['ativo'] = true;
            $parametro_encontrado = $this->ParametroInfoInsuficiente->find('first', array('conditions' => array('descricao' => $this->data['ParametroInfoInsuficiente']['descricao'], 'ativo <>' => 0, 'codigo_parametro_pai' => null )));
            if($parametro_encontrado) {
                $parametro_encontrado['ParametroInfoInsuficiente']['ativo'] = true;
				
				/* CORRECAO NO CHARSET */
				$utf8 = $parametro_encontrado['ParametroInfoInsuficiente']['descricao'];
				$parametro_encontrado['ParametroInfoInsuficiente']['descricao'] = mb_convert_encoding($utf8, 'ISO-8859-1', 'UTF-8');
				
                if ($this->ParametroInfoInsuficiente->atualizar($parametro_encontrado)) {
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->BSession->setFlash('save_error');
                    $parametro= null;
                    $this->set(compact('parametro'));
                }

            } else {
				
				/* CORRECAO NO CHARSET */
				$utf8 = $this->data['ParametroInfoInsuficiente']['descricao'];
				$this->data['ParametroInfoInsuficiente']['descricao'] = mb_convert_encoding($utf8, 'ISO-8859-1', 'UTF-8');
				
                if ($this->ParametroInfoInsuficiente->incluir($this->data)) {
                    $this->BSession->setFlash('save_success');
                    $this->redirect(array('action' => 'index'));
                } else {
                    $this->BSession->setFlash('save_error');
                    $parametro= null;
                    $this->set(compact('parametro'));
                }
            }
            
        } else {
            $parametro= null;
            $categorias = $this->ParametroInfoInsuficiente->find('list', array('conditions' => array('codigo_parametro_pai' => null, 'ativo <>' => 0)));
            $this->set(compact('parametro'));
            $this->set(compact('categorias'));
            $this->data = array('ParametroInfoInsuficiente' => array(
            'descricao' => null
            ));
        }
    }

    function editar($parametros) {
        $this->pageTitle = 'Editar Parâmetro de Informação Insuficiente';
       
        if (!empty($this->data)) {
			
			/* CORRECAO NO CHARSET */
			$utf8 = $this->data['ParametroInfoInsuficiente']['descricao'];
			$this->data['ParametroInfoInsuficiente']['descricao'] = mb_convert_encoding($utf8, 'ISO-8859-1', 'UTF-8');
				
            if ($this->ParametroInfoInsuficiente->atualizar($this->data)) {
               $this->BSession->setFlash('save_success');
               $this->redirect(array('action' => 'index'));
           
            }else {
               $this->BSession->setFlash('save_error');                
               $parametro= $this->data = $this->ParametroInfoInsuficiente->carregar($parametros);
               $this->set(compact('parametro')); 
            }
        }else {
         $parametro= $this->data = $this->ParametroInfoInsuficiente->carregar($parametros);
        }
        $categorias = $this->ParametroInfoInsuficiente->find('list', array('conditions' => array('codigo_parametro_pai' => null)));
        $this->set(compact('categorias'));
            
        $this->set(compact('parametro'));
    }


    function excluir($parametro) {
        
        if ($this->ParametroInfoInsuficiente->excluirOuInativar($parametro)) {
            $this->BSession->setFlash('delete_success');
        } else {
            $this->BSession->setFlash('delete_error');
        }
        $this->redirect(array('action' => 'index'));
    }


}
?>