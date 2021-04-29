<?php
namespace ddRunSnippets;

class Snippet extends \DDTools\Snippet {
	protected
		$version = '3.3.0',
		
		$params = [
			//Defaults
			'snippets' => [],
			'snippets_parseResults' => false,
			'tpl' => null,
			'tpl_placeholders' => [],
		],
		
		$paramsTypes = [
			'snippets' => 'objectArray',
			'snippets_parseResults' => 'boolean',
			'tpl_placeholders' => 'objectArray'
		],
		
		$renamedParamsCompliance = [
			'tpl_placeholders' => 'placeholders'
		]
	;
	
	/**
	 * run
	 * @version 1.1.1 (2021-04-30)
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
				$aSnippetParams = $this->parseObject([
					'object' => $aSnippetParams,
					'data' => $resultArray
				]);
				
				$resultArray[$aSnippetAlias] = \ddTools::$modx->runSnippet(
					$aSnippetName,
					$aSnippetParams
				);
			}else{
				$resultArray[$aSnippetAlias] = \ddTools::$modx->runSnippet($aSnippetName);
			}
			
			if ($this->params->snippets_parseResults){
				$resultArray[$aSnippetAlias] = \ddTools::parseSource($resultArray[$aSnippetAlias]);
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
	
	/**
	 * parseObject
	 * @version 1.0 (2021-04-30)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted} @required
	 * @param $params->object {arrayAssociative} — Source object. @required
	 * @param $params->data {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted} — Data has to be replaced in keys and values of `$params->object`. @required
	 * 
	 * @return {arrayAssociative}
	 */
	private function parseObject($params){
		$params = \DDTools\ObjectTools::convertType([
			'object' => $params,
			'type' => 'objectStdClass'
		]);
		
		//Extend is needed to prevent references
		$result = \DDTools\ObjectTools::extend([
			'objects' => [
				[],
				$params->object
			]
		]);
		
		foreach (
			$result as
			$propName =>
			$propValue
		){
			//If prop name contains placeholders
			if (
				strpos(
					$propName,
					'[+'
				) !== false
			){
				//Remove unprepared name
				unset($result[$propName]);
				
				//Replace placeholders
				$propName = \ddTools::parseText([
					'text' => $propName,
					'data' => $params->data,
					'mergeAll' => false
				]);
				
				//Save parameter with the new name
				$result[$propName] = $propValue;
			}
			
			$isPropValueString = is_string($propValue);
			
			//If the value is not a string
			if (!$isPropValueString){
				//We need to convert it to a string for replacing placeholders
				$propValue = \DDTools\ObjectTools::convertType([
					'object' => $propValue,
					'type' => 'stringJsonAuto'
				]);
			}
			
			$propValueParsed = $propValue;
			
			//If parameter value contains placeholders
			if (
				strpos(
					$propValue,
					'[+'
				) !== false
			){
				//Replace placeholders
				$propValueParsed = \ddTools::parseText([
					'text' => $propValue,
					'data' => $params->data,
					'mergeAll' => false
				]);
			}
			
			//If something changed after parsing
			if ($propValueParsed != $propValue){
				//Save parameter with the new value
				$result[$propName] = $propValueParsed;
				//Nothing changed after parsing but the value was converted before
			}elseif (!$isPropValueString){
				//Prevent back converstion because it's no needed
				$isPropValueString = true;
			}
			
			//If the value was converted before from object to JSON
			if (!$isPropValueString){
				//Convert it back
				//Получается тройная конверсия туда-сюда-обратно. Как-то нехорошо, но что делать?
				$result[$propName] = \DDTools\ObjectTools::convertType([
					'object' => $result[$propName],
					'type' => 'ojbectAuto'
				]);
			}
		}
		
		return $result;
	}
}