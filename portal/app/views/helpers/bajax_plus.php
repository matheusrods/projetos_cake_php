<?php
class BajaxPlusHelper extends AppHelper {

	var $helpers = array('BForm', 'Javascript');

	private $model = null;

	private $form = array();
	
	private $js = array();

	private $options = array(
		'divupdate' => '.form-procurar', // '#modal_dialog',
		'callback' => 'function(){}',
		'url' => array(
			'controller' => 'filtros', 
			'action' => 'filtrar', 
			'model' => '', 
			'element_name' => ''), 
		'isAjax' => true,
		'autocomplete' => 'off'
	);
	
	// function form($model = null, $options = array(), $ehAjax = true){
	// 	$divupdate = isset($options['divupdate']) ? $options['divupdate'] : '#modal_dialog';
	// 	unset($options['divupdate']);
	// 	$callback = isset($options['callback']) ? $options['callback'] : 'function(){}';
	// 	unset($options['callback']);
	// 	if($ehAjax)
	// 		$options = array_merge($options, array('onsubmit'=>"return ajaxFormRequest(this, '$divupdate', true, $callback)"));
	// 	return $this->BForm->create($model, $options);
	// }
	public function form($model = null, $options = array(), $ehAjax = true){
		
		$this->model = $model;

		$options = $this->evaluateOptions($options);
		
		return $this->BForm->create($model, $options);
		
		return print_r($this->form, true);
	}
	
	private function evaluateOptions($options = array()){
		
		if(isset($options['divupdate'])){
			$this->options['divupdate'] = $options['divupdate'];
			unset($options['divupdate']);
		}

		if(isset($options['callback'])){
			$this->options['callback'] = $options['callback'];
			unset($options['callback']);
		}

		if(isset($options['autocomplete']))
			$this->options['autocomplete'] = $options['autocomplete'];

		if(isset($options['url'])){

			$url = $options['url'];
			
			if(isset($url['controller']))
				$this->options['url']['controller'] = $url['controller'];

			if(isset($url['action']))
				$this->options['url']['action'] = $url['action'];

			if(isset($url['model'])){
				$this->options['url']['model'] = $url['model'];
			} else {
				$this->options['url']['model'] = $this->model;
			}

			if(isset($url['element_name']))
				$this->options['url']['element_name'] = $url['element_name'];
			
		}

		if(isset($options['isAjax']))
			$this->options['url'] = $options['url'];

		// $options = array_merge($options, array(
		// 				'onsubmit'=>"return ajaxFormRequest(this, '$divupdate', true, $callback)")
		// );

		return $this->options;
		
	}

	public function formJavascript( $code = null, $jqueryTag = true ){
		$script = '';
		
		if($jqueryTag)
			$script .= 'jQuery(document).ready(function(){';

		if(!is_null($code))
			$script .= $code;
		
		if($jqueryTag)
			$script .= '});';
		
		return $this->Javascript->codeBlock($script);
	}


	// jQuery(document).ready(function(){
        
    //     $("#ClienteFuncionarioCandidatoCandidatoEntre").exists(function(){console.log("this")});
        
    //     setup_datepicker(); 
	// 	var div = jQuery(".lista");
	// 	bloquearDiv(div);
	// 	div.load(baseUrl + "clientes_funcionarios/consulta_vidas_listagem/" + Math.random());
	// 	jQuery("#limpar-filtro-consulta-vidas").click(function(){
    //         bloquearDiv(jQuery(".form-procurar"));
    //         jQuery(".form-procurar").load(baseUrl + "/filtros/limpar/model:ClienteFuncionario/element_name:consulta_vidas/" + Math.random())
    //     });

	public function formStart($options = array()){

	}

	public function formEnd($options = array()){

	}
	
}
?>