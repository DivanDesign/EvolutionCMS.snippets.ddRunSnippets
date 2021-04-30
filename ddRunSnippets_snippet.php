<?php
/**
 * ddRunSnippets
 * @version 3.4 (2021-04-30)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.biz/modx/ddrunsnippets
 * 
 * @copyright 2011–2021 DD Group {@link https://DivanDesign.biz }
 */

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path') .
	'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddRunSnippets',
	'params' => $params
]);
?>