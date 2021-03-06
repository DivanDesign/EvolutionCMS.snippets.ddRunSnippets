<?php
namespace ddRunSnippets;

class Snippet extends \DDTools\Snippet {
	protected
		$version = '3.4.0',
		
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
	 * @version 1.2 (2021-04-30)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted} @required
	 * @param $params->object {stdClass|arrayAssociative} — Source object. @required
	 * @param $params->data {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted} — Data has to be replaced in keys and values of `$params->object`. @required
	 * 
	 * @return {arrayAssociative}
	 */
	private function parseObject($params){
		$params = \DDTools\ObjectTools::convertType([
			'object' => $params,
			'type' => 'objectStdClass'
		]);
		
		$isResultObject = is_object($params->object);
		
		//Extend is needed to prevent references
		$result = \DDTools\ObjectTools::extend([
			'objects' => [
				(
					$isResultObject ?
					new \stdClass() :
					[]
				),
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
				if ($isResultObject){
					unset($result->{$propName});
				}else{
					unset($result[$propName]);
				}
				
				//Replace placeholders
				$propName = \ddTools::parseText([
					'text' => $propName,
					'data' => $params->data,
					'mergeAll' => false
				]);
				
				//Save parameter with the new name
				if ($isResultObject){
					$result->{$propName} = $propValue;
				}else{
					$result[$propName] = $propValue;
				}
			}
			
			if (
				is_object($propValue) ||
				is_array($propValue)
			){
				//Start recursion
				$propValue = $this->parseObject([
					'object' => $propValue,
					'data' => $params->data
				]);
			}elseif (
				is_string($propValue) &&
				//If parameter value contains placeholders
				strpos(
					$propValue,
					'[+'
				) !== false
			){
				//Replace placeholders
				$propValue = \ddTools::parseText([
					'text' => $propValue,
					'data' => $params->data,
					'mergeAll' => false
				]);
			}
			
			//Save parameter with the new value
			if ($isResultObject){
				$result->{$propName} = $propValue;
			}else{
				$result[$propName] = $propValue;
			}
		}
		
		return $result;
	}
}