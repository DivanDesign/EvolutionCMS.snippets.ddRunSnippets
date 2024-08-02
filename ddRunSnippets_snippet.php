<?php
/**
 * ddRunSnippets
 * @version 4.2.1 (2024-08-02)
 * 
 * @see README.md
 * 
 * @link https://code.divandesign.ru/modx/ddrunsnippets
 * 
 * @copyright 2011–2024 https://Ronef.me
 */

//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddTools/modx.ddtools.class.php'
);

return \DDTools\Snippet::runSnippet([
	'name' => 'ddRunSnippets',
	'params' => $params,
]);
?>