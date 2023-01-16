<?php

App::import('Helper', 'BForm');

class RhhealthFormHelper extends BFormHelper {

	function form($model = null, $options = array(), $ehAjaxOnsubmit = true){
		
		$jsNull = 'null';
        $extra_options = array();
        $chaveIdentificaForm = 'ithealth-form-dinamico';
        
        // $extra_options['divupdate'] = isset($options['divupdate']) ? $options['divupdate'] : $jsNull;
        // $extra_options = json_encode($extra_options);
        $extra_options = $jsNull;
		unset($options['divupdate']);

		if(isset($options['class'])){
            $options['class'] = $options['class'].' '.$chaveIdentificaForm;
        } else {
            $options['class'] = $chaveIdentificaForm;
        }
		
		$callbackBeforeSend = isset($options['callbackBeforeSend']) ? $options['callbackBeforeSend'] : $jsNull;
		unset($options['callbackBeforeSend']);

		$callbackSuccess = isset($options['callbackSuccess']) ? $options['callbackSuccess'] : $jsNull;
		unset($options['callbackSuccess']);

		$callbackError = isset($options['callbackError']) ? $options['callbackError'] : $jsNull;
		unset($options['callbackError']);

		$callbackComplete = isset($options['callbackComplete']) ? $options['callbackComplete'] : $jsNull;
		unset($options['callbackComplete']);
		
		if($ehAjaxOnsubmit)
			$options = array_merge($options, array(
				'onsubmit'=>"return ithealthAjaxFormRequest(this, $extra_options, $callbackBeforeSend, $callbackSuccess, $callbackError, $callbackComplete)")
			);
		
		return $this->create($model, $options);
	}


	function formEnd(){
		return $this->end();
	}
	
	
	function selectForm($strFieldName, $arrOptions, $mixSelected, $arrAttributes)
	{

		$dataId = '';
		$controlGroupId = '';
		
		$requiredLabel = '';
		if(isset($arrAttributes['required'])){
			$requiredLabel = '<h11 style="color:red;">*</h11>';
		}

		if(isset($arrAttributes['data-id'])){
			$dataId = $arrAttributes['data-id'];
			$controlGroupId = ' data-id ="'.$dataId.'ControlGroup"';
		}

		$helpInlineMessage = '';
		if(isset($arrAttributes['help-inline-message'])){
			$helpInlineMessage = $arrAttributes['help-inline-message'];
			unset($arrAttributes['help-inline-message']);
		}

		$html = '';
		$html .= '<div class="control-group"'.$controlGroupId.'>';
		
		if(isset($arrAttributes['label']) && !empty($arrAttributes['label']))
		{
			$html .= '<label class="control-label">';
			$html .= $arrAttributes['label'].$requiredLabel;
			$html .= '</label>';
		}

		$html .= '<div class="controls">';
		
		if(isset($arrAttributes['label'])){
			unset($arrAttributes['label']);
		}

		$arrAttributes['options'] = $arrOptions;
		$arrAttributes['selected'] = $mixSelected;
		$arrAttributes['label'] = false;
        $arrAttributes['type'] = 'select';
		$arrAttributes['style'] = 'width: 100%';
		$arrAttributes['div'] = false;
		
		
		$html .= $this->input( 
			$strFieldName, 
			$arrAttributes
		);

		// $html .= $this->select( 
		// 	$strFieldName, 
		// 	$arrOptions, 
		// 	$mixSelected,  
		// 	$arrAttributes);
		
		$html .= '<span class="help-block">'.$helpInlineMessage.'</span>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;
	}
		

	function inputText($strFieldName, $arrAttributes)
	{
		
		$dataId = '';
		$controlGroupId = '';
		$isCheckBox = isset($arrAttributes['type']) && $arrAttributes['type'] == 'checkbox';

		$requiredLabel = '';
		if(isset($arrAttributes['required'])){
			$requiredLabel = '<h11 style="color:red;">*</h11>';
		}

		if(isset($arrAttributes['data-id'])){
			$dataId = $arrAttributes['data-id'];
			$controlGroupId = ' data-id ="'.$dataId.'ControlGroup"';
		}

		$helpInlineMessage = '';
		if(isset($arrAttributes['help-inline-message'])){
			$helpInlineMessage = $arrAttributes['help-inline-message'];
			unset($arrAttributes['help-inline-message']);
		}

		$html = '';
		
		if($isCheckBox){
			
			if(isset($arrAttributes['label']) && !empty($arrAttributes['label']))
			{
				$html .= '<label class="checkbox inline" style="padding-top: 28px;">'; // : TODO:
				$label = $arrAttributes['label'];
				$arrAttributes['label'] = false;
				$arrAttributes['div'] = false;
				
				$html .= $this->input( 
					$strFieldName, 
					$arrAttributes
				);

				$html .= $label.$requiredLabel;
				$html .= '</label>';
			}
			return $html;
		}

		$html .= '<div class="control-group"'.$controlGroupId.'>';
		
		if(isset($arrAttributes['label']) && !empty($arrAttributes['label']))
		{
			
			$html .= '<label class="control-label">';
			$html .= $arrAttributes['label'].$requiredLabel;
			$html .= '</label>';
		}

		// hack para não trabalhar o label no input chamado abaixo
		$arrAttributes['label'] = false;

		$html .= '<div class="controls">';
		
		if(isset($arrAttributes['class']) && strstr('datepickerjs', $arrAttributes['class'])){
			$arrAttributes['style'] = isset($arrAttributes['style']) ? $arrAttributes['style'] . ' margin-right:-20px' : ' margin-right:-20px';
		}
		// if(isset($arrAttributes['class']) && strstr('datepickerjs', $arrAttributes['class'])){
		// 	$html .= '<div class="input-append">';
		// }
		// $html .= $this->input( 
		// 	$strFieldName, 
		// 	$arrAttributes
		// );
		// if(isset($arrAttributes['class']) && strstr('datepickerjs', $arrAttributes['class'])){
		// $html .= '	<span class="add-on"><i class="icon-calendar"></i></span>';
		// $html .= '</div>';
		// }

		$html .= $this->input( 
			$strFieldName, 
			$arrAttributes
		);
		
		$html .= '<span class="help-block">'.$helpInlineMessage.'</span>';
		$html .= '</div>';
		$html .= '</div>';

		return $html;

	}
}