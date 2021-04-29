<?php
namespace ddRunSnippets;

class Snippet extends \DDTools\Snippet {
	protected
		$version = '3.2.0',
		
		$params = [
			//Defaults
			'snippets' => [],
			'tpl' => null,
			'tpl_placeholders' => []
		],
		
		$paramsTypes = [
			'snippets' => 'objectArray',
			'tpl_placeholders' => 'objectArray'
		],
		
		$renamedParamsCompliance = [
			'tpl_placeholders' => 'placeholders'
		]
	;
	
	/**
	 * run
	 * @version 1.0 (2021-04-29)
	 * 
	 * @return {string}
	 */
	public function run(){
		//The snippet must return an empty string even if result is absent
		$result = '';
		
		$resultArray = [];
		
		foreach (
			$this->params->snippets as
			$aSnippetName =>
			$aSnippetParams
		){
			//If snippet alias is set
			if (
				strpos(
					$aSnippetName,
					'='
				) !== false
			){
				$aSnippetName = explode(
					'=',
					$aSnippetName
				);
				
				$aSnippetAlias = $aSnippetName[1];
				$aSnippetName = $aSnippetName[0];
			}else{
				$aSnippetAlias = $aSnippetName;
			}
			
			//If snippet parameters are passed
			if (is_array($aSnippetParams)){
				//Fill parameters with previous snippets execution results
				foreach (
					$aSnippetParams as
					$aSnippetParamName =>
					$aSnippetParamValue
				){
					//If parameter name contains placeholders
					if (
						strpos(
							$aSnippetParamName,
							'[+'
						) !== false
					){
						//Remove unprepared name
						unset($aSnippetParams[$aSnippetParamName]);
						
						//Replace to previous snippets results
						$aSnippetParamName = \ddTools::parseText([
							'text' => $aSnippetParamName,
							'data' => $resultArray,
							'mergeAll' => false
						]);
						
						//Save parameter with the new name
						$aSnippetParams[$aSnippetParamName] = $aSnippetParamValue;
					}
					
					$isASnippetParamValueString = is_string($aSnippetParamValue);
					
					//If the value is not a string
					if (!$isASnippetParamValueString){
						//We need to convert it to a string for replacing preverious snippets results
						$aSnippetParamValue = \DDTools\ObjectTools::convertType([
							'object' => $aSnippetParamValue,
							'type' => 'stringJsonAuto'
						]);
					}
					
					$aSnippetParamValueParsed = $aSnippetParamValue;
					
					//If parameter value contains placeholders
					if (
						strpos(
							$aSnippetParamValue,
							'[+'
						) !== false
					){
						//Replace to previous snippets results
						$aSnippetParamValueParsed = \ddTools::parseText([
							'text' => $aSnippetParamValue,
							'data' => $resultArray,
							'mergeAll' => false
						]);
					}
					
					//If something changed after parsing
					if ($aSnippetParamValueParsed != $aSnippetParamValue){
						//Save parameter with the new value
						$aSnippetParams[$aSnippetParamName] = $aSnippetParamValueParsed;
						//Nothing changed after parsing but the value was converted before
					}elseif (!$isASnippetParamValueString){
						//Prevent back converstion because it's no needed
						$isASnippetParamValueString = true;
					}
					
					//If the value was converted before from object to JSON
					if (!$isASnippetParamValueString){
						//Convert it back
						//Получается тройная конверсия туда-сюда-обратно. Как-то нехорошо, но что делать?
						$aSnippetParams[$aSnippetParamName] = \DDTools\ObjectTools::convertType([
							'object' => $aSnippetParams[$aSnippetParamName],
							'type' => 'ojbectAuto'
						]);
					}
				}
				
				$resultArray[$aSnippetAlias] = \ddTools::$modx->runSnippet(
					$aSnippetName,
					$aSnippetParams
				);
			}else{
				$resultArray[$aSnippetAlias] = \ddTools::$modx->runSnippet($aSnippetName);
			}
		}
		
		if (!empty($resultArray)){
			//Если задан шаблон для вывода
			if (!is_null($this->params->tpl)){
				//If template is not empty (if set as empty, the empty string must be returned)
				if ($this->params->tpl != ''){
					//Remove empty results
					$resultArray = array_filter(
						$resultArray,
						function($aSnippetResult){
							return $aSnippetResult != '';
						}
					);
					
					//Если есть хоть один результат
					if (!empty($resultArray)){
						//Если есть дополнительные данные
						if (!empty($this->params->tpl_placeholders)){
							$resultArray = \DDTools\ObjectTools::extend([
								'objects' => [
									$resultArray,
									$this->params->tpl_placeholders
								]
							]);
						}
						
						$result .= \ddTools::parseText([
							'text' => \ddTools::$modx->getTpl($this->params->tpl),
							'data' => $resultArray
						]);
					}
				}
			//Если шаблон не задан
			}else{
				$result .= implode(
					'',
					$resultArray
				);
			}
		}
		
		return $result;
	}
}