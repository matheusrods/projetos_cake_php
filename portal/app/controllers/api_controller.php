<?php
class ApiController extends AppController {
    public $uses = array();

    public function beforeFilter() {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
    }

    public function getListaEstados(){
        $this->loadModel('EnderecoEstado');

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Content-type: application/json');

        $this->layout     = false;
        $this->autoRender = false;

        $raw_post = trim(file_get_contents('php://input'));
        $data     = json_decode($raw_post, true);

        $saida = array('erro' => 0, 'data' => array());

        $data['pais'] = !empty($data['pais']) ? $data['pais'] : 'BR';

        $this->EnderecoEstado->bindPais();

        $estados = $this->EnderecoEstado->find('all', array('conditions' => array('EnderecoPais.abreviacao' => $data['pais']), 'fields' => array('EnderecoEstado.codigo', 'EnderecoEstado.abreviacao', 'EnderecoEstado.descricao'), 'order' => 'EnderecoEstado.abreviacao'));

        if(!empty($estados)){
            foreach($estados as $item){
                $saida['data'][] = array(
                    'codigo'     => $item['EnderecoEstado']['codigo'], 
                    'abreviacao' => $item['EnderecoEstado']['abreviacao'],
                    'descricao'  => $item['EnderecoEstado']['descricao']
                );
            }
        }

        echo json_encode($saida);
        exit();
    }

    public function getListaCidades(){
        $this->loadModel('EnderecoCidade');

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Content-type: application/json');

        $this->layout     = false;
        $this->autoRender = false;

        $raw_post = trim(file_get_contents('php://input'));
        $data     = json_decode($raw_post, true);

        $saida = array('erro' => 0, 'data' => array());

        $data['pais'] = !empty($data['pais']) ? $data['pais'] : 'BR';
        $data['uf']   = !empty($data['uf'])   ? $data['uf']   : NULL;

        $confs = array(
            'conditions' => array(
                'EnderecoPais.abreviacao' => $data['pais'], 
                'EnderecoCidade.invalido' => false 
            ), 

            'fields' => array(
                'EnderecoCidade.codigo', 
                'EnderecoEstado.abreviacao', 
                'EnderecoCidade.descricao'
            ), 

            'order' => array(
                'EnderecoEstado.abreviacao', 
                'EnderecoCidade.descricao'
            )
        );

        if($data['uf']) $confs['conditions']['EnderecoEstado.abreviacao'] = $data['uf'];

        $this->EnderecoCidade->bindPais();

        $cidades = $this->EnderecoCidade->find('all', $confs);

        if(!empty($cidades)){
            foreach($cidades as $item){
                $saida['data'][] = array(
                    'codigo'     => $item['EnderecoCidade']['codigo'], 
                    'uf'         => $item['EnderecoEstado']['abreviacao'],
                    'descricao'  => $item['EnderecoCidade']['descricao']
                );
            }
        }

        echo json_encode($saida);
        exit();
    }

    public function getListaFornecedoresEnderecoCoord(){
        $this->loadModel('FornecedorEndereco');

        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: *');
        header('Content-type: application/json');

        $this->layout     = false;
        $this->autoRender = false;

        $raw_post = trim(file_get_contents('php://input'));
        $data     = json_decode($raw_post, true);

        $saida = array('erro' => 0, 'data' => array());

        $data['refe_raio']      = !empty($data['refe_raio'])      ? $data['refe_raio']                             : 20;
        $data['refe_latitude']  = !empty($data['refe_latitude'])  ? str_replace(',', '.', $data['refe_latitude'])  : NULL;
        $data['refe_longitude'] = !empty($data['refe_longitude']) ? str_replace(',', '.', $data['refe_longitude']) : NULL;

        $confs = array(
            'conditions' => array(
                'Fornecedor.ativo' => true, 
                'FornecedorEndereco.codigo_tipo_contato' => 2, 
                'FornecedorEndereco.latitude NOT' => NULL, 
                'FornecedorEndereco.longitude NOT' => NULL, 
            ), 

            'fields' => array(
                'FornecedorEndereco.codigo', 
                'FornecedorEndereco.latitude', 
                'FornecedorEndereco.longitude'
            ), 

            'order' => array(
                'FornecedorEndereco.codigo', 
            )
        );

        if($data['refe_latitude'] && $data['refe_longitude']){
            $confs['conditions']['(6371 * acos(
                    cos(radians(' . $data['refe_latitude'] . ')) *
                    cos(radians(latitude)) *
                    cos(radians(' . $data['refe_longitude'] . ') - radians(longitude)) +
                    sin(radians(' . $data['refe_latitude'] . ')) *
                    sin(radians(latitude))
                 )) <='] = $data['refe_raio'];
        }

        $this->FornecedorEndereco->bindEndereco();
        $this->FornecedorEndereco->bindFornecedor();

        $locais = $this->FornecedorEndereco->find('all', $confs);

        if(!empty($locais)){
            foreach($locais as $item){
                $saida['data'][] = array(
                    'codigo'    => $item['FornecedorEndereco']['codigo'], 
                    'latitude'  => $item['FornecedorEndereco']['latitude'],
                    'longitude' => $item['FornecedorEndereco']['longitude']
                );
            }
        }

        echo json_encode($saida);
        exit();
    }
}
