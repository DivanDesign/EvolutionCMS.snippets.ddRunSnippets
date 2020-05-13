<?php
/**
 * ddRunSnippets
 * @version 2.5.1b (2016-12-28)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddrunsnippets
 * 
 * @copyright 2011–2016 DD Group {@link https://DivanDesign.biz }
 */

global $modx;

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

//The snippet must return an empty string even if result is absent
$snippetResult = '';
$snippetResultArray = [];

$snippets = \ddTools::encodedStringToArray($snippets);

foreach (
	$snippets as
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
			
			//If parameter value contains placeholders
			if (
				strpos(
					$aSnippetParamValue,
					'[+'
				) !== false
			){
				//Replace to previous snippets results
				$aSnippetParamValue = \ddTools::parseText([
					'text' => $aSnippetParamValue,
					'data' => $snippetResultArray,
					'mergeAll' => false
				]);
				
				//Save parameter with the new name
				$aSnippetParams[$aSnippetParamName] = $aSnippetParamValue;
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
	if (isset($tpl)){
		//If template is not empty (if set as empty, the empty string must be returned)
		if ($tpl != ''){
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
				if (isset($placeholders)){
					//Разбиваем их
					$snippetResultArray = array_merge(
						$snippetResultArray,
						\ddTools::explodeAssoc($placeholders)
					);
				}
				
				$snippetResult .= \ddTools::parseText([
					'text' => $modx->getTpl($tpl),
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