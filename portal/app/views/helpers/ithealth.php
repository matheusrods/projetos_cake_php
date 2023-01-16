<?php
/*
    Esta classe é um wrapper da biblioteca legada de formulários, porém seguindo os nomes do formHelper do cakephp versão 4.

    Foi feita com o propósito é facilitar migrações no futuro já que a implementação destes componentes exigem uma centralização das lógicas.

    // https://book.cakephp.org/4/en/views/helpers/form.html#Cake\View\Helper\FormHelper::create

    Cake\View\Helper\FormHelper::create(mixed $context = null, array $options = [])
    Cake\View\Helper\FormHelper::text(string $name, array $options)
    Cake\View\Helper\FormHelper::hidden(string $fieldName, array $options)
    Cake\View\Helper\FormHelper::textarea(string $fieldName, array $options)
    Cake\View\Helper\FormHelper::checkbox(string $fieldName, array $options)
    Cake\View\Helper\FormHelper::radio(string $fieldName, array $options, array $attributes)
    Cake\View\Helper\FormHelper::select(string $fieldName, array $options, array $attributes)
    Cake\View\Helper\FormHelper::file(string $fieldName, array $options)
    Cake\View\Helper\FormHelper::dateTime($fieldName, $options = [])
    Cake\View\Helper\FormHelper::date($fieldName, $options = [])
    Cake\View\Helper\FormHelper::time($fieldName, $options = [])
    Cake\View\Helper\FormHelper::month(string $fieldName, array $attributes)
    Cake\View\Helper\FormHelper::year(string $fieldName, array $options = [])
    Cake\View\Helper\FormHelper::label(string $fieldName, string $text, array $options)
    Cake\View\Helper\FormHelper::password(string $fieldName, array $options)
    Cake\View\Helper\FormHelper::error(string $fieldName, mixed $text, array $options)
    Cake\View\Helper\FormHelper::isFieldError(string $fieldName)
    Cake\View\Helper\FormHelper::submit(string $caption, array $options)
    Cake\View\Helper\FormHelper::button(string $title, array $options = [])
    Cake\View\Helper\FormHelper::end($secureAttributes = [])
    Cake\View\Helper\FormHelper::postButton(string $title, mixed $url, array $options = [])
    Cake\View\Helper\FormHelper::postLink(string $title, mixed $url = null, array $options = [])
    Cake\View\Helper\FormHelper::submit(string $caption, array $options)
    Cake\View\Helper\FormHelper::button(string $title, array $options = [])
    Cake\View\Helper\FormHelper::control(string $fieldName, array $options = [])
    Cake\View\Helper\FormHelper::controls(array $fields = [], $options = [])
    Cake\View\Helper\FormHelper::allControls(array $fields, $options = [])
    Cake\View\Helper\FormHelper::unlockField($name)
    Cake\View\Helper\FormHelper::secure(array $fields = [], array $secureAttributes = [])


    Documentação desta lib
    
    Javascript encontra o campo graças class-key definida a cada campo de input com a tag data-id
    

    Estrutura exemplo para definir valores padrões de elementos
    
    'salvar_button_submit' => array(
        'name' => 'Salvar',
        'type'  => 'Submit',
        'attributes' => (
            'value' => 'Salvar',
            'class' => 'salvar-form-key',
            'style' => 'width:100%',
        )
    ),

    'email_usuario_text' => array(
        'name' => 'email_usuario',
        'type'  => 'text',
        'required' => true,
        'attributes' => (
            'value' => null,
            'class' => 'email-usuario-key',
            'style' => 'width:100%',
        ),
        'options' => [
            'label' => 'Email',
        ],
        'validators' => [
            'name' => 'isEmail'
        ]
    ),

    'codigo_documento_text' => array(
        'name' => 'codigo_documento',
        'type'  => 'text',
        'attributes' => (
            'value' => null,
            'class' => 'codigo-documento-key',
            'style' => 'width:100%',
        ),
        'options' => [
            'label' => 'CNPJ',
        ],
        'validators' => [
            'name' => 'isCnpj'
        ]
    ),

*/

class IthealthHelper extends AppHelper {

	var $helpers = array('Rhhealth', 'RhhealthForm', 'Html', 'Javascript');

    

	private $_elements_default_params = array(

		'salvar_button_submit' => array(
            'name' => "salvar",
            'options' => array(
                'label' => 'Salvar'
            ),
            'attributes' => array(
                'class' => 'btn btn-primary'
            )
		),

		'voltar_button_link' => array(
            'name' => "voltar",
            'options' => array(
                'label' => 'Voltar',
            ),
            'attributes'=> array(
                'class' => 'btn btn-voltar'
            )
        ),

        'cliente_codigo_documento_select2' => array(
            'name'  => 'codigo_documento',
            'required' => true,
            'options' => array(
                'label' => 'CNPJ',
            ),
            'attributes' => array(
                'class' => 'codigo-documento-key',
                'style' => 'width:100%',
            )
        ),
        
        'cliente_codigo_cliente_select2' => array(
            'name'  => 'codigo_cliente',
            'required' => true,
            'options' => array(
                'label' => 'Código Cliente',
            ),
            'attributes' => array(
                'class' => 'codigo-key',
                'style' => 'width:100%',
            )
        ),

        'cliente_razao_social_select2' => array(
            'name'  => 'razao_social',
            'required' => true,
            'options' => array(
                'label' => 'Razão Social',
            ),
            'attributes' => array(
                'class' => 'razao-social-key',
                'style' => 'width:100%',
            )
        ),

        'cliente_nome_fantasia_text' => array(
            'name'  => 'nome',
            'required' => true,
            'options' => array(
                'label' => 'Nome Fantasia',
            ),
            'attributes' => array(
                'class' => 'nome-fantasia-key',
                'style' => 'width:100%',
            )
        ),

        'codigo_cliente_text' => array(
            'name'  => 'codigo_cliente',
            'required' => true,
            'options' => array(
                'label' => 'Código Cliente',
            ),
            'attributes' => array(
                'class' => 'codigo-key',
                'style' => 'width:100%',
            )
        ),

        'codigo_documento_text' => array(
            'name'  => 'codigo_documento',
            'required' => true,
            'options' => array(
                'label' => 'CNPJ',
            ),
            'attributes' => array(
                'class' => 'codigo-documento-key',
                'style' => 'width:100%',
            )
        ),
        
        'codigo_credenciado_text' => array(
            'name'  => 'codigo_credenciado',
            'required' => true,
            'options' => array(
                'label' => 'Código Credenciado',
            ),
            'attributes' => array(
                'class' => 'codigo-key',
                'style' => 'width:100%',
            )
        ),

        'razao_social_text' => array(
            'name'  => 'razao_social',
            'required' => true,
            'options' => array(
                'label' => 'Razão Social',
            ),
            'attributes' => array(
                'class' => 'razao-social-key',
                'style' => 'width:100%',
            )
        ),

        'nome_fantasia_text' => array(
            'name'  => 'nome',
            'required' => true,
            'options' => array(
                'label' => 'Nome Fantasia',
            ),
            'attributes' => array(
                'class' => 'nome-fantasia-key',
                'style' => 'width:100%',
            )
        ),


        'nome_fantasia_select2' => array(
            'name'  => 'nome_fantasia',
            'required' => true,
            'options' => array(
                'label' => 'Nome Fantasia',
            ),
            'attributes' => array(
                'class' => 'nome-fantasia-key',
                'style' => 'width:100%',
            )
        ),

        'codigo_documento_select2' => array(
            'name'  => 'codigo_documento',
            'required' => true,
            'options' => array(
                'label' => 'CNPJ',
            ),
            'attributes' => array(
                'class' => 'codigo-documento-key',
                'style' => 'width:100%',
            )
        ),
        
        'codigo_credenciado_select2' => array(
            'name'  => 'codigo_fornecedor',
            'required' => true,
            'options' => array(
                'label' => 'Código Credenciado',
            ),
            'attributes' => array(
                'class' => 'codigo-key',
                'style' => 'width:100%',
            )
        ),

        'razao_social_select2' => array(
            'name'  => 'razao_social',
            'required' => true,
            'options' => array(
                'label' => 'Razão Social',
            ),
            'attributes' => array(
                'class' => 'razao-social-key',
                'style' => 'width:100%',
            )
        ),

	);

	function __construct(){
		parent::__construct();
	
	}

    function loadHelperJs($strHelperName = null)
    {
        if(!empty($strHelperName)){
            return $this->Rhhealth->link_js('rhhealth/helpers/'.$strHelperName);
        }
		return $this->Rhhealth->link_js('rhhealth/ithealthHelper');
	}


	function link_js($paths, $inline = true) {

		return $this->RhhealthForm->link_js($paths, $inline = true);
	}

    // Cake\View\Helper\FormHelper::create(mixed $context = null, array $options = [])
	function create($mixContext = null, $arrOptions = array()){
		
		
		if(!isset($arrOptions['autocomplete']))
		{
			$arrOptions['autocomplete'] = 'off';
		}

		if(!isset($arrOptions['type']))
		{
			$arrOptions['type'] = 'post';
		}
		
		if(!isset($arrOptions['class']))
		{
			$arrOptions['class'] = 'form-default';
        }
        
        $ehAjax = false; // false padrão mvc cake

        if(isset($arrOptions['ajax_submit']) && $arrOptions['ajax_submit'] == true )
		{
            $ehAjax = true;
            unset($arrOptions['ajax_submit']);
		}
		
		return $this->RhhealthForm->form($mixContext, $arrOptions, $ehAjax);
	}


	function formEnd(){
		return $this->RhhealthForm->formEnd();
	}


	function formSubmit( $label, $options = array())
	{
		return $this->RhhealthForm->submit($label, $options);
	}


	function htmlLink($label = null, $href = null, $options = array())
	{
		return $this->Html->link($label, $href, $options);
	}


	function buttonSalvar($arrAttributes = array())
	{
        // obter valores padroes
        $_element_key = 'salvar_button_submit';
        $_field = $this->_elements_default_params[$_element_key]; 
        
        $arrAttributes = $this->evaluateAttributes($_element_key, $arrAttributes);
        $arrAttributes['div'] = false;
        $arrAttributes['id'] = 'btn-submit-form-default'; // :TODO:

		return $this->formSubmit($_field['options']['label'], $arrAttributes);
	}


	function buttonLinkVoltar($href = array('action' => 'index'), $arrAttributes = array())
    {
        // obter valores padroes
        $_element_key = 'voltar_button_link';
        $_field = $this->_elements_default_params[$_element_key]; 
        
        $arrAttributes = $this->evaluateAttributes($_element_key, $arrAttributes);

		return $this->htmlLink($_field['options']['label'], $href, $arrAttributes);
	}
    
    private function evaluateAttributes($_element_key, $arrAttributes)
    {
        

        if(!isset($_element_key) || empty($_element_key)){
            throw new Exception("Nome de elemento não encontrado", 1);
        }

        if(!isset($this->_elements_default_params[$_element_key]) || empty($this->_elements_default_params[$_element_key])){
            throw new Exception(sprintf("Definições deste campo %s não foram encontrados",$_element_key), 1);
        }

        $_field = $this->_elements_default_params[$_element_key]; 
        
        // definindo atributo adicional no input data-id
        if(isset($_field['data-id'])){
            $arrAttributes['data-id'] = $_field['data-id'];
        } else {
            $strFieldNameCamelized = Inflector::camelize($_field['name']);
            $arrAttributes['data-id'] = $strFieldNameCamelized;
        }

        if(isset($_field['attributes']['class']) && (!isset($arrAttributes['class']) || empty($arrAttributes['class']))){
            $arrAttributes['class'] = $_field['attributes']['class'];
        } 

        // se precisar incrementar class 
        if(isset($arrAttributes['append-class']) && !empty($arrAttributes['append-class'])){
            $arrAttributes['class'] = $_field['attributes']['class'] . ' ' . $arrAttributes['append-class'];
            unset($arrAttributes['append-class']);
        } 

        if(isset($_field['attributes']['style']) && (!isset($arrAttributes['style']) || empty($arrAttributes['style']))){
            $arrAttributes['style'] = $_field['attributes']['style'];
        } 

        // se precisar incrementar style
        if(isset($arrAttributes['append-style']) && !empty($arrAttributes['append-style'])){
            $arrAttributes['style'] = $_field['attributes']['style'] . ' ' . $arrAttributes['append-style'];
            unset($arrAttributes['append-style']);
        } 

        // se não estiver definido algum label então use um padrão
        if(isset($_field['options']['label']) && (!isset($arrAttributes['label']) || empty($arrAttributes['label'])))
        {
            $arrAttributes['label'] = $_field['options']['label'];
        }

        // se não estiver definido algum label então use um padrão
        if(isset($_field['required']) && (!isset($arrAttributes['required']) || empty($arrAttributes['required'])))
        {
            $arrAttributes['required'] = $_field['required'];
        }

        return $arrAttributes;
    }

    function input($strFieldName = null, $arrAttributes = array())
    {
        
        $strFieldNameCamelized = Inflector::camelize($strFieldName);
        $arrAttributes['data-id'] = $strFieldNameCamelized;
        $arrAttributes['div'] = false;

        return $this->RhhealthForm->inputText( 
            $strFieldName, 
            $arrAttributes
        );
    }
    
    function select2CodigoCliente($strFieldName = null, $arrOptions = array(), $mixValue = null, $arrAttributes = array())
    {
        $_element_key = 'cliente_codigo_cliente_select2';
        $_field = $this->_elements_default_params[$_element_key]; 
        
        if(empty($strFieldName)){
            $strFieldName = $_field['name'];
        }

        // select2 precisa de um valor "$arrOptions" inicializado ex. array(''=>'')
        $arrOptions = empty($arrOptions) ? array(''=>'') : $arrOptions;
        
        // valor pre selecionado pode ser string ou int
        $mixSelected = empty($mixSelected) ? null : $mixSelected;
        
        // avalia parametros pré definidos deste campo
        $arrAttributes = $this->evaluateAttributes($_element_key, $arrAttributes);

        // sobrescrevendo 
        $arrAttributes['data-service'] = 'ithealth_helper/obter_cliente'; // servico de comunicação
        $arrAttributes['required'] = true;

        return $this->RhhealthForm->selectForm( 
            $strFieldName, 
            $arrOptions, 
            $mixSelected,
            $arrAttributes
        );
    }


    function select2CodigoDocumentoCliente($strFieldName = null, $arrOptions = array(), $mixValue = null, $arrAttributes = array())
    {
        
        $_element_key = 'cliente_codigo_documento_select2';
        $_field = $this->_elements_default_params[$_element_key]; 

        if(empty($strFieldName)){
            $strFieldName = $_field['name'];
        }

        // select2 precisa de um valor "$arrOptions" inicializado ex. array(''=>'')
        $arrOptions = empty($arrOptions) ? array(''=>'') : $arrOptions;
        
        // valor pre selecionado pode ser string ou int
        $mixSelected = empty($mixSelected) ? null : $mixSelected;
        
        // avalia parametros pré definidos deste campo
        $arrAttributes = $this->evaluateAttributes($_element_key, $arrAttributes);

        // sobrescrevendo 
        $arrAttributes['data-service'] = 'ithealth_helper/obter_cliente';
        $arrAttributes['label'] = 'CNPJ'; // sobreescrevendo Label padrão de codigo_documento que é "CNPJ"
        $arrAttributes['required'] = true;
        
        return $this->RhhealthForm->selectForm( 
            $strFieldName, 
            $arrOptions, 
            $mixSelected,
            $arrAttributes
        );
    }


    function select2RazaoSocialCliente($strFieldName = null, $arrOptions = array(), $mixValue = null, $arrAttributes = array())
    {
        $_element_key = 'cliente_razao_social_select2';
        $_field = $this->_elements_default_params[$_element_key]; 
        
        if(empty($strFieldName)){
            $strFieldName = $_field['name'];
        }

        // select2 precisa de um valor "$arrOptions" inicializado ex. array(''=>'')
        $arrOptions = empty($arrOptions) ? array(''=>'') : $arrOptions;
        
        // valor pre selecionado pode ser string ou int
        $mixSelected = empty($mixSelected) ? null : $mixSelected;
        
        // parametros pré definidos deste campo
        $arrAttributes = $this->evaluateAttributes($_element_key, $arrAttributes);
        $arrAttributes['data-service'] = 'ithealth_helper/obter_cliente';

        return $this->RhhealthForm->selectForm( 
            $strFieldName, 
            $arrOptions, 
            $mixSelected,
            $arrAttributes
        );

    }


    function inputNomeFantasiaCliente($strFieldName = null, $mixValue = null, $arrAttributes = array())
    {
        
        $_element_key = 'cliente_nome_fantasia_text';
        $_field = $this->_elements_default_params[$_element_key]; 

        if(empty($strFieldName)){
            $strFieldName = $_field['name'];
        }
        
        // valor pre selecionado pode ser string ou int
        $arrAttributes['value'] = empty($mixValue) ? null : $mixValue;
        
        // parametros pré definidos deste campo
        $arrAttributes = $this->evaluateAttributes($_element_key, $arrAttributes);
        $arrAttributes['readonly'] = true; 
        $arrAttributes['div'] = false;
        $arrAttributes['data-service'] = 'ithealth_helper/obter_cliente';
        
        return $this->RhhealthForm->inputText( 
            $strFieldName, 
            $arrAttributes
        );
    }    
    
    // $ithealth->selectCodigoCredenciado(
    //     $options = array($codigo_credenciado => $codigo_credenciado),
    //     $selected = null,
    //     $atributtes = array('style'=>'width:100%','empty'=>false));
    //     
    //     Caso precise de um segundo campo na pagina, deve definir o nome diferente de codigo_credenciado
    //     $idName = 'codigo_credenciado_auxiliar'

    function select2CodigoCredenciado($strFieldName = null, $arrOptions = array(), $mixValue = null, $arrAttributes = array())
    {
        $_element_key = 'codigo_credenciado_select2';
        $_field = $this->_elements_default_params[$_element_key]; 
        
        if(empty($strFieldName)){
            $strFieldName = $_field['name'];
        }

        // select2 precisa de um valor "$arrOptions" inicializado ex. array(''=>'')
        $arrOptions = empty($arrOptions) ? array(''=>'') : $arrOptions;
        
        // valor pre selecionado pode ser string ou int
        $mixSelected = empty($mixSelected) ? null : $mixSelected;
        
        // avalia parametros pré definidos deste campo
        $arrAttributes = $this->evaluateAttributes($_element_key, $arrAttributes);

        // sobrescrevendo 
        $arrAttributes['data-service'] = 'ithealth_helper/obter_credenciado'; // servico de comunicação
        $arrAttributes['required'] = true;

        return $this->RhhealthForm->selectForm( 
            $strFieldName, 
            $arrOptions, 
            $mixSelected,
            $arrAttributes
        );
    }


    function select2CodigoDocumentoCredenciado($strFieldName = null, $arrOptions = array(), $mixValue = null, $arrAttributes = array())
    {
        
        $_element_key = 'codigo_documento_select2';
        $_field = $this->_elements_default_params[$_element_key]; 

        if(empty($strFieldName)){
            $strFieldName = $_field['name'];
        }

        // select2 precisa de um valor "$arrOptions" inicializado ex. array(''=>'')
        $arrOptions = empty($arrOptions) ? array(''=>'') : $arrOptions;
        
        // valor pre selecionado pode ser string ou int
        $mixSelected = empty($mixSelected) ? null : $mixSelected;
        
        // avalia parametros pré definidos deste campo
        $arrAttributes = $this->evaluateAttributes($_element_key, $arrAttributes);

        // sobrescrevendo 
        $arrAttributes['data-service'] = 'ithealth_helper/obter_credenciado';
        $arrAttributes['label'] = 'CNPJ Credenciado'; // sobreescrevendo Label padrão de codigo_documento que é "CNPJ"
        $arrAttributes['required'] = true;
        
        return $this->RhhealthForm->selectForm( 
            $strFieldName, 
            $arrOptions, 
            $mixSelected,
            $arrAttributes
        );
    }


    function select2RazaoSocialCredenciado($strFieldName = null, $arrOptions = array(), $mixValue = null, $arrAttributes = array())
    {
        $_element_key = 'razao_social_select2';
        $_field = $this->_elements_default_params[$_element_key]; 
        
        if(empty($strFieldName)){
            $strFieldName = $_field['name'];
        }

        // select2 precisa de um valor "$arrOptions" inicializado ex. array(''=>'')
        $arrOptions = empty($arrOptions) ? array(''=>'') : $arrOptions;
        
        // valor pre selecionado pode ser string ou int
        $mixSelected = empty($mixSelected) ? null : $mixSelected;
        
        // parametros pré definidos deste campo
        $arrAttributes = $this->evaluateAttributes($_element_key, $arrAttributes);
        $arrAttributes['data-service'] = 'ithealth_helper/obter_credenciado';

        return $this->RhhealthForm->selectForm( 
            $strFieldName, 
            $arrOptions, 
            $mixSelected,
            $arrAttributes
        );

    }


    function inputNomeFantasiaCredenciado($strFieldName = null, $mixValue = null, $arrAttributes = array())
    {
        
        $_element_key = 'nome_fantasia_text';
        $_field = $this->_elements_default_params[$_element_key]; 

        if(empty($strFieldName)){
            $strFieldName = $_field['name'];
        }
        
        // valor pre selecionado pode ser string ou int
        $arrAttributes['value'] = empty($mixValue) ? null : $mixValue;
        
        // parametros pré definidos deste campo
        $arrAttributes = $this->evaluateAttributes($_element_key, $arrAttributes);
        $arrAttributes['readonly'] = true; 
        $arrAttributes['div'] = false;
        $arrAttributes['data-service'] = 'ithealth_helper/obter_credenciado';
        
        return $this->RhhealthForm->inputText( 
            $strFieldName, 
            $arrAttributes
        );
    }

    
    function selectMotivosAcrescimo($strFieldName = null, $arrOptions = array(), $arrAttributes = array())
    {
        if(empty($strFieldName)){
            $strFieldName = 'codigo_motivo_acrescimo';
        }
        
        App::import('Model','MotivosAcrescimo');
        $this->MotivosAcrescimo = new MotivosAcrescimo();
        $arrOptions = $this->MotivosAcrescimo->find('list', array('fields' => array('codigo','descricao')));

        $arrAttributes['label'] = 'Motivo de Acréscimo';
        $arrAttributes['style'] = 'width: 100%';
        $arrAttributes['empty'] = 'Selecione';
        $arrAttributes['default'] = '';
        
        return $this->RhhealthForm->selectForm( 
            $strFieldName, 
            $arrOptions,
            $mixSelected = null,
            $arrAttributes);
    }

    function selectMotivosDesconto($strFieldName = null, $arrOptions = array(), $arrAttributes = array())
    {
        if(empty($strFieldName)){
            $strFieldName = 'codigo_motivo_desconto';
        }
        
        App::import('Model','MotivosDesconto');
        $this->MotivosDesconto = new MotivosDesconto();
        $arrOptions = $this->MotivosDesconto->find('list', array('fields' => array('codigo','descricao')));

        $arrAttributes['label'] = 'Motivo de Desconto';
        $arrAttributes['style'] = 'width: 100%';
        $arrAttributes['empty'] = 'Selecione';
        $arrAttributes['default'] = '';
        
        return $this->RhhealthForm->selectForm( 
            $strFieldName, 
            $arrOptions,
            $mixSelected = null,
            $arrAttributes);
    }


    function selectStatusNotaFiscal($strFieldName = null, $arrOptions = array(), $arrAttributes = array())
    {
        if(empty($strFieldName)){
            $strFieldName = 'codigo_nota_fiscal_status';
        }
        
        App::import('Model','NotaFiscalStatus');
        $this->NotaFiscalStatus = new NotaFiscalStatus();
        $arrOptions = $this->NotaFiscalStatus->find('list', array('fields' => array('codigo','descricao'),'conditions' => array('ativo' => 1)));

        $arrAttributes['label'] = 'Status';
        $arrAttributes['style'] = 'width: 100%';
        $arrAttributes['empty'] = 'Selecione';
        $arrAttributes['default'] = '';
        
        return $this->RhhealthForm->selectForm( 
            $strFieldName, 
            $arrOptions,
            $mixSelected = null,
            $arrAttributes);
    }


    function selectTiposRecebimento($strFieldName = null, $arrOptions = array(), $arrAttributes = array())
    {
        if(empty($strFieldName)){
            $strFieldName = 'codigo_tipo_recebimento';
        }

        App::import('Model','TipoRecebimento');
        $this->TipoRecebimento = new TipoRecebimento();
        $arrOptions = $this->TipoRecebimento->find('list', array('fields' => array('codigo','descricao')));

        $arrAttributes['label'] = 'Tipo de Recebimento';
        $arrAttributes['style'] = 'width: 100%';
        $arrAttributes['empty'] = 'Selecione';
        $arrAttributes['default'] = '';
        
        return $this->RhhealthForm->selectForm( 
            $strFieldName, 
            $arrOptions,
            $mixSelected = null,
            $arrAttributes);
    }


    function selectFormasPagamento($strFieldName = null, $arrOptions = array(), $arrAttributes = array())
    {
        if(empty($strFieldName)){
            $strFieldName = 'codigo_formas_pagto';
        }

        App::import('Model','FormaPagto');
        $this->FormaPagto = new FormaPagto();
        $arrOptions = $this->FormaPagto->find('list', array('fields' => array('codigo','descricao')));

        $arrAttributes['label'] = 'Formas de Pagamento';
        $arrAttributes['style'] = 'width: 100%';
        $arrAttributes['empty'] = 'Selecione';
        $arrAttributes['default'] = '';
        
        return $this->RhhealthForm->selectForm( 
            $strFieldName, 
            $arrOptions,
            $mixSelected = null,
            $arrAttributes);
    }


    function selectTipoServicosNfs($strFieldName = null, $arrOptions = array(), $arrAttributes = array())
    {
        if(empty($strFieldName)){
            $strFieldName = 'codigo_tipo_servicos_nfs';
        }

        App::import('Model','TipoServicosNfs');
        $this->TipoServicosNfs = new TipoServicosNfs();
        $arrOptions = $this->TipoServicosNfs->find('list', array('fields' => array('codigo','descricao')));

        $arrAttributes['label'] = 'Tipo de Serviço de NF';
        $arrAttributes['style'] = 'width: 100%';
        $arrAttributes['empty'] = 'Selecione';
        $arrAttributes['default'] = '';
        
        return $this->RhhealthForm->selectForm( 
            $strFieldName, 
            $arrOptions,
            $mixSelected = null,
            $arrAttributes);
    }

    function moeda($valor, $opcoes = array()) {
        // Se possuir os dois separadores ent�o deixar somente a "," que � o separador de decimal
        if (strpos($valor, '.') && strpos($valor, ',')) {
            $valor = preg_replace('/[^0-9,\-]/', '', $valor);
            if (empty($valor))
                $valor = 0;
        }

        if (isset($opcoes['nozero']) && $opcoes['nozero'] && $valor == 0)
            return '';

        if (!isset($opcoes['format']))
            $opcoes['format'] = false;

        if (isset($opcoes['edit']) && $opcoes['edit']) {
            $valor = abs(str_replace(',', '.', $valor));
            //$opcoes['thousands'] = '.';
            $opcoes['before'] = '';
        }

        $unidadeMonetaria = $opcoes['format'] ? '<span>R$ </span>' : '';

        $valor = self::formataNumero($valor, array_merge(array('thousands' => '.', 'decimals' => ',', 'zero' => '0', 'before' => $unidadeMonetaria, 'escape' => false), $opcoes));

        if (isset($opcoes['format_decimals']) && $opcoes['format_decimals']) {
            $valor .= '</span>';
        }
        return $this->output("$valor");
    }//FINAL FUNCTION moeda

    function formataNumero($number, $options = false) {
        $places = 0;
        if (is_int($options)) {
            $places = $options;
        }

        $separators = array(',', '.', '-', ':');

        $before = $after = null;
        if (is_string($options) && !in_array($options, $separators)) {
            $before = $options;
        }
        $thousands = ',';
        if (!is_array($options) && in_array($options, $separators)) {
            $thousands = $options;
        }
        $decimals = '.';
        if (!is_array($options) && in_array($options, $separators)) {
            $decimals = $options;
        }

        $escape = true;
        if (is_array($options)) {
            $options = array_merge(array('before'=>'$', 'places' => 2, 'thousands' => ',', 'decimals' => '.'), $options);
            extract($options);
        }
        
        //tratamento feito para prevenir quando a pagina tiver dado retorno error, ele nao trazer valores com virgulas e sim em ponto para realizar a conversao corretamente.
        $number = str_replace(",",".",$number);

        $out = $before . number_format($number, $places, $decimals, $thousands) . $after;

        if ($escape) {
            return h($out);
        }
        return $out;
    }
        
}
