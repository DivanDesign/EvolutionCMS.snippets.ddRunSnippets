<?php
/**
 * ddRunSnippets
 * @version 3.2 (2020-06-03)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddrunsnippets
 * 
 * @copyright 2011–2020 DD Group {@link https://DivanDesign.biz }
 */

global $modx;

//# Include
//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);


//# Prepare params
//Renaming params with backward compatibility
$params = \ddTools::verifyRenamedParams([
	'params' => $params,
	'compliance' => [
		'tpl_placeholders' => 'placeholders'
	],
	'returnCorrectedOnly' => false
]);

$params = \DDTools\ObjectTools::extend([
	'objects' => [
		//Defaults
		(object) [
			'snippets' => [],
			'tpl' => null,
			'tpl_placeholders' => []
		],
		$params
	]
]);

$params->snippets = \DDTools\ObjectTools::convertType([
	'object' => $params->snippets,
	'type' => 'objectArray'
]);

$params->tpl_placeholders = \DDTools\ObjectTools::convertType([
	'object' => $params->tpl_placeholders,
	'type' => 'objectArray'
]);


//# Run
//The snippet must return an empty string even if result is absent
$snippetResult = '';
$snippetResultArray = [];

foreach (
	$params->snippets as
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
					'data' => $snippetResultArray,
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
					'data' => $snippetResultArray,
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
		
		$snippetResultArray[$aSnippetAlias] = $modx->runSnippet(
			$aSnippetName,
			$aSnippetParams
		);
	}else{
		$snippetResultArray[$aSnippetAlias] = $modx->runSnippet($aSnippetName);
	}
}

if (!empty($snippetResultArray)){
	//Если задан шаблон для вывода
	if (!is_null($params->tpl)){
		//If template is not empty (if set as empty, the empty string must be returned)
		if ($params->tpl != ''){
			//Remove empty results
			$snippetResultArray = array_filter(
				$snippetResultArray,
				function($aSnippetResult){
					return $aSnippetResult != '';
				}
			);
			
			//Если есть хоть один результат
			if (!empty($snippetResultArray)){
				//Если есть дополнительные данные
				if (!empty($params->tpl_placeholders)){
					$snippetResultArray = \DDTools\ObjectTools::extend([
						'objects' => [
							$snippetResultArray,
							$params->tpl_placeholders
						]
					]);
				}
				
				$snippetResult .= \ddTools::parseText([
					'text' => $modx->getTpl($params->tpl),
					'data' => $snippetResultArray
				]);
			}
		}
	//Если шаблон не задан
	}else{
		$snippetResult .= implode(
			'',
			$snippetResultArray
		);
	}
}

return $snippetResult;
?>