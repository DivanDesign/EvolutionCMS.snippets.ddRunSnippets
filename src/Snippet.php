<?php
namespace ddRunSnippets;

class Snippet extends \DDTools\Snippet {
	protected
		$version = '4.2.1',
		
		$params = [
			//Defaults
			'snippets' => [],
			'snippets_parseEachResultCompletely' => false,
			'outputterParams' => [
				'tpl' => '',
				'placeholders' => [],
			],
		],
		
		$paramsTypes = [
			'snippets' => 'objectArray',
			'snippets_parseEachResultCompletely' => 'boolean',
			'outputterParams' => 'objectStdClass',
		],
		
		$renamedParamsCompliance = [
			'snippets_parseEachResultCompletely' => 'snippets_parseResults',
		]
	;
	
	/**
	 * prepareParams
	 * @version 1.0.1 (2024-07-29)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted}
	 * 
	 * @return {void}
	 */
	protected function prepareParams($params = []){
		parent::prepareParams($params);
		
		//Backward compatibility
		if (
			\DDTools\ObjectTools::isPropExists([
				'object' => $this->params,
				'propName' => 'tpl',
			])
		){
			$this->params->outputterParams->tpl = $this->params->tpl;
		}
		if (
			\DDTools\ObjectTools::isPropExists([
				'object' => $this->params,
				'propName' => 'tpl_placeholders',
			])
		){
			$this->params->outputterParams->placeholders = \DDTools\ObjectTools::convertType([
				'object' => $this->params->tpl_placeholders,
				'type' => 'objectStdClass',
			]);
		}
	}
	/**
	 * run
	 * @version 3.3.4 (2024-08-02)
	 * 
	 * @return {string}
	 */
	public function run(){
		//The snippet must return an empty string even if result is absent
		$result = '';
		
		$resultArray = [];
		
		foreach (
			$this->params->snippets
			as $aSnippetName
			=> $aSnippetParams
		){
			//If snippet alias is set
			if (
				strpos(
					$aSnippetName,
					'='
				)
				!== false
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
			
			$aRunParams = (object) [
				'parseResultCompletely' => $this->params->snippets_parseEachResultCompletely,
			];
			$aSnippetResultFromCache = null;
			
			//If snippet parameters are passed
			if (is_array($aSnippetParams)){
				//Fill parameters with previous snippets execution results
				$aSnippetParams = $this->parseObject([
					'object' => $aSnippetParams,
					'data' => $resultArray,
				]);
				
				$aRunParams = \DDTools\ObjectTools::extend([
					'objects' => [
						$aRunParams,
						\DDTools\ObjectTools::getPropValue([
							'object' => $aSnippetParams,
							'propName' => 'runParams',
						]),
					],
				]);
				
				//If cache is used
				if (
					\DDTools\ObjectTools::isPropExists([
						'object' => $aRunParams,
						'propName' => 'cache',
					])
				){
					$aRunParams->cache = \DDTools\ObjectTools::convertType([
						'object' => \DDTools\ObjectTools::getPropValue([
							'object' => $aRunParams,
							'propName' => 'cache',
						]),
						'type' => 'objectStdClass',
					]);
					
					//Backward compatibility
					$aRunParams->cache = \ddTools::verifyRenamedParams([
						'params' => $aRunParams->cache,
						'compliance' => [
							'resourceId' => 'docId',
							'suffix' => 'name',
						],
						'returnCorrectedOnly' => false,
					]);
					
					//Validate cache params
					if (
						!empty($aRunParams->cache->resourceId)
						&& is_numeric($aRunParams->cache->resourceId)
						&& !empty($aRunParams->cache->suffix)
					){
						$aSnippetResultFromCache = \DDTools\Tools\Cache::get($aRunParams->cache);
					}else{
						//Mark that cache is not used because of invalid parameters
						$aRunParams->cache = null;
					}
				}
			}
			
			//Use result from cache if exist
			if (!is_null($aSnippetResultFromCache)){
				$resultArray[$aSnippetAlias] = $aSnippetResultFromCache;
			}else{
				//Run snippet
				$resultArray[$aSnippetAlias] = \DDTools\Snippet::runSnippet([
					'name' => $aSnippetName,
					'params' =>
						is_array($aSnippetParams)
						? $aSnippetParams
						: []
					,
				]);
				
				if (
					$aRunParams->parseResultCompletely
					//Only string results can be parsed
					&& is_string($resultArray[$aSnippetAlias])
				){
					$resultArray[$aSnippetAlias] = \ddTools::parseSource($resultArray[$aSnippetAlias]);
				}
				
				//Cache file is not exist but cache is used
				if (!\ddTools::isEmpty($aRunParams->cache)){
					//Save result to cache
					\DDTools\Tools\Cache::save(
						\DDTools\ObjectTools::extend([
							'objects' => [
								(object) [
									'data' => $resultArray[$aSnippetAlias],
								],
								$aRunParams->cache,
							]
						])
					);
				}
			}
		}
		
		if (
			!\ddTools::isEmpty($resultArray)
			//If template is not empty (if set as empty, the empty string must be returned)
			&& !empty($this->params->outputterParams->tpl)
		){
			//Если есть хоть один не пустой результат
			if (
				!\ddTools::isEmpty(
					//Remove empty results
					array_filter(
						$resultArray,
						function($aSnippetResult){
							return $aSnippetResult != '';
						}
					)
				)
			){
				$resultArray['ddRunSnippetsResult.all'] = implode(
					'',
					$resultArray
				);
				
				$resultArray = \DDTools\ObjectTools::extend([
					'objects' => [
						$resultArray,
						$this->params->outputterParams->placeholders,
					],
				]);
				
				$result .= \ddTools::parseText([
					'text' => \ddTools::getTpl($this->params->outputterParams->tpl),
					'data' => $resultArray,
				]);
			}
		}
		
		return $result;
	}
	
	/**
	 * parseObject
	 * @version 1.2.2 (2024-07-29)
	 * 
	 * @param $params {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted}
	 * @param $params->object {stdClass|arrayAssociative} — Source object.
	 * @param $params->data {stdClass|arrayAssociative|stringJsonObject|stringHjsonObject|stringQueryFormatted} — Data has to be replaced in keys and values of `$params->object`.
	 * 
	 * @return {arrayAssociative|stdClass}
	 */
	private function parseObject($params){
		$params = \DDTools\ObjectTools::convertType([
			'object' => $params,
			'type' => 'objectStdClass',
		]);
		
		$isResultObject = is_object($params->object);
		
		//Extend is needed to prevent references
		$result = \DDTools\ObjectTools::extend([
			'objects' => [
				(
					$isResultObject
					? new \stdClass()
					: []
				),
				$params->object,
			],
		]);
		
		foreach (
			$result
			as $propName
			=> $propValue
		){
			//If prop name contains placeholders
			if (
				strpos(
					$propName,
					'[+'
				)
				!== false
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
					'isCompletelyParsingEnabled' => false,
				]);
				
				//Save parameter with the new name
				if ($isResultObject){
					$result->{$propName} = $propValue;
				}else{
					$result[$propName] = $propValue;
				}
			}
			
			if (
				is_object($propValue)
				|| is_array($propValue)
			){
				//Start recursion
				$propValue = $this->parseObject([
					'object' => $propValue,
					'data' => $params->data,
				]);
			}elseif (
				is_string($propValue)
				//If parameter value contains placeholders
				&& strpos(
					$propValue,
					'[+'
				)
				!== false
			){
				//Replace placeholders
				$propValue = \ddTools::parseText([
					'text' => $propValue,
					'data' => $params->data,
					'isCompletelyParsingEnabled' => false,
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