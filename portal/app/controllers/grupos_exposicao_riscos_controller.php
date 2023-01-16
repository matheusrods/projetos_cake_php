<?php
class GruposExposicaoRiscosController extends AppController {
    public $name = 'GruposExposicaoRiscos'; 
    var $uses = array(
        'GrupoExposicao', 
        'GrupoExposicaoRisco',
        'GrupoExpRiscoFonteGera',
        'GrupoExposicaoRiscoEpi',
        'GrupoExposicaoRiscoEpc'
        );
	
    function excluir($codigo, $codigo_grupo_exposicao) {
        $this->layout = 'ajax';
        $this->render(false, false);

        //pega o risco para chegar no grupo de exposicao e verifcar se tem grupo_homogeneo
        $gr_exp = $this->GrupoExposicao->find('first', array('conditions' => array('codigo' => $codigo_grupo_exposicao)));
        //pega o risco passado
        $busca_risco = $this->GrupoExposicaoRisco->find("first", array('conditions' => array('codigo' => $codigo)));
        
        
        if(!empty($gr_exp['GrupoExposicao']['codigo_grupo_homogeneo'])) {
            
            $joins = array(
                array(
                    'table' => 'RHHealth.dbo.grupos_exposicao_risco',
                    'alias' => 'GrupoExposicaoRisco',
                    'type' => 'INNER',
                    'conditions' => 'GrupoExposicao.codigo = GrupoExposicaoRisco.codigo_grupo_exposicao',
                ),
            );
            $gr_exp_all = $this->GrupoExposicao->find('all', array('fields' => 'GrupoExposicaoRisco.codigo as codigo','joins' => $joins, 'conditions' => array('GrupoExposicao.codigo_grupo_homogeneo' => $gr_exp['GrupoExposicao']['codigo_grupo_homogeneo'],'GrupoExposicaoRisco.codigo_risco' => $busca_risco['GrupoExposicaoRisco']['codigo_risco'])));

            $return = 1;
            //verifica se tem registro
            if(!empty($gr_exp_all)) {

                foreach($gr_exp_all as $ger) {
                    if(!empty($ger[0]['codigo'])) {
                        if(!$this->GrupoExposicaoRisco->excluir($ger[0]['codigo'])){
                            $return = 0;
                        }          
                    }
                }//fim foreach

            }
            else {
                $return = 0;
            }//fim else gr exp all

            echo $return;

        }
        else {


            if(!empty($busca_risco)){
                
                if($this->GrupoExposicaoRisco->excluir($busca_risco['GrupoExposicaoRisco']['codigo'])){
                    echo 1;
                }
                else{
                    echo 0;
                }
            }
            else{
                echo 0;
            }
        }

        exit;

    }

}