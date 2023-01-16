<?php
class SincronizarCodigoDocumentoBehavior extends ModelBehavior {

    function beforeSave(&$model) {
        $this->Documento = & ClassRegistry::init('Documento');

        $codigo_documento = $model->data[$model->alias]['codigo_documento'];
        if (!$this->Documento->existeCadastro($codigo_documento)) {
                    
            if (isset($_SESSION['Auth']['Usuario']['codigo'])) {
                $usuario_inclusao = $_SESSION['Auth']['Usuario']['codigo'];
            } else {
                $usuario_inclusao = 1;
            }

            $novo_documento = array(
                'Documento' => array(
                    'codigo' => $codigo_documento,
                    'codigo_pais' => 1,
                    'tipo' => null,
                    'data_inclusao' => Date('Ymd H:i:s'),
                    'codigo_usuario_inclusao' => $usuario_inclusao
            ));
            
            if($this->Documento->isCPF($codigo_documento)) {
               $novo_documento['Documento']['tipo'] = true; 
            }

            if(($model->alias == 'Cliente' || $model->alias == 'Fornecedor') && isset($model->data[$model->alias]['tipo_unidade'])){
                
                if(isset($model->data[$model->alias]['tipo_unidade'])){
                    
                    if($model->data[$model->alias]['tipo_unidade'] == 'F'){
                        
                        if($this->Documento->isCNPJ($codigo_documento)) {
                           $novo_documento['Documento']['tipo'] = false; 
                        }
                    }
                    else{
                        $novo_documento['Documento']['tipo'] = false; 
                    }
                }
            }
            else{
                if($this->Documento->isCNPJ($codigo_documento)) {
                   $novo_documento['Documento']['tipo'] = false; 
                }
            }

            if(is_null($novo_documento['Documento']['tipo'])) {
                return false;
            }
            
            $this->Documento->incluir($novo_documento);
        }
        
        return true;
    }//FINAL FUNCTION beforeSave

}//FINAL CLASS SincronizarCodigoDocumentoBehavior

?>