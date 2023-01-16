<?php
class CriacaoLayoutsController extends AppController
{

    public $name = 'CriacaoLayouts';
    public $layout = 'criacao_layouts';
    public $components = array('Filtros', 'RequestHandler', 'ExportCsv', 'Upload');
    public $helpers = array('Html', 'Ajax', 'Highcharts', 'Buonny', 'Ithealth');
    public $uses = array(
        'MapLayout',
        'MapLayoutDetalhe',
        'IntClienteCargos',
        'IntClienteEmpresa',
        'IntClienteSetores',
        'IntClienteCentroResultado',
        'IntClienteFuncionarios',
        'IntClienteFuncionariosEmpresa'
    );


    /**
     * Redirect to self index method
     * 
     * @returns
     */
    public function para_o_inicio()
    {
        return $this->redirect(array('action' => 'index'));
    }

    function beforeFilter()
    {
        parent::beforeFilter();
        $this->BAuth->allow(array('*'));
    }

    /**
     * Return integration tables as array
     * 
     * @return array
     */
    private function obterIntegracoes()
    {
        $integrations = array(
            $this->IntClienteCargos,
            $this->IntClienteSetores,
            $this->IntClienteEmpresa,
            $this->IntClienteCentroResultado,
            $this->IntClienteFuncionarios,
            $this->IntClienteFuncionariosEmpresa
        );

        $tables = array();
        foreach ($integrations as $integration) {
            $tables[] = array(
                'name'         => $integration->slugedTable,
                'original' => $integration->useTable,
                'fields'       => $integration->fillable,
            );
        }

        return $tables;
    }

    public function index()
    {
        $this->pageTitle = 'Layouts';
    }

    /**
     * Listagem de layouts
     */
    public function listagem()
    {
        $this->layout = 'ajax';
        
        $filtros = $this->Filtros->controla_sessao($this->data, $this->MapLayout->name);
        $conditions = $this->MapLayout->converteFiltroEmCondition($filtros);

        $this->paginate['MapLayout'] = array(
            'conditions' => $conditions,
            'fields' => array('*'),
            'limit' => 50,
        );

        $layouts = $this->paginate('MapLayout');

        $this->set(
            compact(
                'layouts'
            )
        );
    }

    public function incluir()
    {
        if ($this->RequestHandler->isPost() == false) {
            $this->pageTitle = 'Incluir layout';
            $tables = $this->obterIntegracoes();

            $this->set(
                compact(
                    'tables'
                )
            );
            return;
        }

        $this->armazenar($this->obter_post("layout", array()));
    }

    /**
     * Set session flash error
     * 
     * @param string $message
     */
    protected function com_erro($message)
    {
        return $this->__flash(MSGT_ERROR, $message);
    }

    /**
     * Set success flash to session
     * 
     * @param string $message
     */
    protected function com_sucesso($message)
    {
        return $this->__flash(MSGT_SUCCESS, $message);
    }

    /**
     * Set flash to session
     * 
     * @param string $code
     * @param string $message
     * @returns
     */
    protected function __flash($code, $message)
    {
        return $this->BSession->setFlash(array(
            $code,
            $message
        ));
    }

    /**
     * Response json
     * 
     * @returns
     */
    public function json($data)
    {
        echo json_encode($data);
        exit;
    }

    /**
     * Filtra registros
     */
    public function filtrar()
    {

        $data = array();

        return $this->json($data);
    }

    /**
     * Change row status
     * 
     * @param int $codigo
     * @returns
     */
    public function troca_status($codigo)
    {
        try {
            $data                 = $this->MapLayout->carregar($codigo);
            $data['MapLayout']['ativo'] = !$data['MapLayout']['ativo'];
            $status               = $data['MapLayout']['ativo'];
            $this->MapLayout->atualizar($data);
            return $this->json($status);
        } catch (\Exception $e) {
            $this->reportar($e);
            return $this->json($status);
        }
    }

    /**
     * Obtém um valor POST especifico
     * 
     * @param string $index
     * @param mixed $default
     * @return string|mixed
     */
    public function obter_post($index, $default = null)
    {
        return isset($_POST[$index]) ? $_POST[$index] : $default;
    }

    /**
     * Checa se os campos POST obrigatórios foram preenchidos
     *  
     * @param array $data
     * @return boolean
     */
    public function validar_campos_obrigatorios($data)
    {
        $campos_obrigatorios = array(
            'nome',
            'dsname',
            'apelido',
            'codigo_cliente',
            'tipo_layout'
        );
        $valido = true;
        foreach ($campos_obrigatorios as $campo) {
            if (array_key_exists($campo, $data) == false) {
                $valido = false;
            }
        }

        return $valido;
    }


    /**
     * Reporta um exceção
     * 
     * @param \Throwable $th
     * @returns
     */
    public function reportar($th)
    {
        $this->com_erro(utf8_encode($th->getMessage()));
        // ...resto
    }

    /**
     * Cria um detalhe do layout, armazenando suas colunas
     * 
     * @param array $data
     * @param int $mapLayoutId
     * @returns
     */
    protected function armazenar_colunas($data, $mapLayoutId, $codigo_cliente)
    {
        if (count($data) <= 0) {
            return true;
        }
        $fullDataFormat = array();
        foreach ($data as $key => $row) {
            $preRow                      = array();
            $preRow['codigo_map_layout'] = $mapLayoutId;
            $preRow['posicao']           = $row['position'];
            $preRow['tabela']            = $row['tabela'];
            $preRow['campo_saida']       = $row['coluna'];
            $preRow['delimitador']       = ";";
            $preRow['ativo'] = 1;
            $preRow['codigo_cliente']    = $codigo_cliente;
            $fullDataFormat[]            = $preRow;
        }

        $saved = $this->MapLayoutDetalhe->saveAll($fullDataFormat);
        

        return $saved;
    }
    protected function checa_ignora_primeira_linha($data) {
        return isset($data['ignora_primeira_linha']) && $data['ignora_primeira_linha'] == 'on';
    }
    /**
     * Armazena layout no banco de dados
     * 
     * @param array $data
     * @returns
     */
    protected function armazenar($data)
    {
        try {
            if ($this->validar_campos_obrigatorios($data) == false) {
                throw new \InvalidArgumentException(
                    "Os campos obrigatórios não foram preenchidos"
                );
            }
            $data['ativo']   = 1;
            $data['ignora_primeira_linha'] = $this->checa_ignora_primeira_linha($data);
            $saved           = $this->MapLayout->incluir($data);
            $lastId          = $this->MapLayout->getInsertID();
            $columnsInserted = $this->armazenar_colunas(isset($data['columns']) ? $data['columns'] : array(), $lastId, $data['codigo_cliente']);

            if (!$saved || !$columnsInserted) {
                throw new \Exception(
                    "Falha ao incluir layout, por favor tente novamente mais tarde."
                );
            }

            $this->com_sucesso(
                "Layout cadastrado com sucesso!"
            );

            return $this->para_o_inicio();
        } catch (\InvalidArgumentException $e) {
            $this->reportar($e);
            return $this->redirect(array('action' => 'incluir'));
        } catch (\Exception $e) {
            $this->reportar($e);
            return $this->para_o_inicio();
        }
    }

    /**
     * Atualiza as colunas(MAP_LAYOUT_DETALHE) do layout
     * 
     * @param array $columns
     * $param array $layout
     * @returns
     */
    public function atualizar_colunas($columns, $layout)
    {
        if (count($layout['MapLayoutDetalhe']) > 0) {
            foreach ($layout['MapLayoutDetalhe'] as &$column) {
                $column['ativo'] = 0;
            }
            $atualizado = $this->MapLayoutDetalhe->saveAll($layout['MapLayoutDetalhe']);
            if (!$atualizado) {
                throw new Exception(
                    "Falha ao atualizar colunas, por favor tente novamente mais tarde."
                );
            }
        }
        return $this->armazenar_colunas($columns, $layout['MapLayout']['codigo'], $layout['MapLayout']['codigo_cliente']);
    }

    /**
     * Atualiza layout
     * 
     * @param array $data
     * @param array $layout
     * @return view
     */
    public function atualizar($data, $layout)
    {
        try {
            if ($this->validar_campos_obrigatorios($data) == false) {
                throw new \Exception(
                    "Os campos obrigatórios não foram preenchidos"
                );
            }
            $data['ignora_primeira_linha'] = $this->checa_ignora_primeira_linha($data);
            foreach ($data as $key => $value) {
                if (isset($layout['MapLayout'][$key])) {
                    $layout['MapLayout'][$key] = $value;
                }
            }
            $saved  = $this->MapLayout->atualizar($layout);
            $columnsUpdated = $this->atualizar_colunas(isset($data['columns']) ? $data['columns'] : array(), $layout);

            if (!$saved || !$columnsUpdated) {
                throw new \Exception(
                    "Falha ao atualizar layout, por favor tente novamente mais tarde."
                );
            }

            $this->com_sucesso(
                "Layout atualizado com sucesso!"
            );

            return $this->para_o_inicio();
        } catch (\Exception $e) {
            $this->reportar($e);
            return $this->redirect(array('action' => 'editar', $layout['MapLayout']['codigo']));
        }
    }

    /**
     * Check if HTTP method and choose action
     * 
     * @param int $codigo
     * @return view
     */
    public function editar($codigo = null)
    {
        if (is_null($codigo)) {
            $codigo = $this->obter_post("_codigo", null);
            $layout = $this->MapLayout->with_bind($codigo);
        } else {
            $layout = $this->MapLayout->with_bind($codigo);
        }
        if (!$layout) {
            return $this->cakeError('error404');
        }
        if ($this->RequestHandler->isPost() == false) {
            $this->pageTitle = 'Editar layout';
            $tables = $this->obterIntegracoes();

            $this->set(
                compact(
                    'tables',
                    'layout'
                )
            );
            return;
        }
        return $this->atualizar($this->obter_post("layout", array()), $layout);
    }
}
