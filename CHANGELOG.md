# (MODX)EvolutionCMS.snippets.ddRunSnippets changelog


## Version 4.1.1 (2024-04-14)

* \* Parameters → `outputterParams->tpl`: Snippet placeholders will be replaced even with empty results.
* \* `\ddTools::getTpl` is used instead of `$modx->getTpl` (means a bit less bugs).
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.60 is required.


## Version 4.1 (2023-05-04)

* \+ Parameters → `snippets->{$snippetName}->runParams->cache`:
	* \+ The new group of parameters. Allows to cache a snippet result to a specific file (see README).
	* \+ Created cache files will be automatically deleted by [(MODX)EvolutionCMS.plugins.ddRunSnippets](https://github.com/DivanDesign/EvolutionCMS.plugins.ddRunSnippets).
* \+ Parameters → `snippets->{$snippetName}->runParams->parseResultCompletely`: The new parameter. Allows to completely parse result of required snippet by CMS parser.
* \* Parameters → `snippets_parseEachResultCompletely`: Has been renamed from `snippets_parseResults` (with backward compatibility).
* \* Attention! PHP >= 7.4 is required.


## Version 4.0 (2023-03-30)

* \* Attention! Backward compatibility is broken.
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.59 is required.
* \* `\DDTools\Snippet::runSnippet` is used instead of `$modx->runSnippet` to run snippets without DB and eval. This increases performance and saves server resources, but unfortunately you can't run snippets that do not use `\DDTools\Snippet`. Please give us feedback via [Telegram chat](https://t.me/dd_code) if this is critical.
* \+ Any executed snippet can return either a string or a native PHP array. It is convenient to use with “nested” placeholders (see README → Examples).
* \* Parameters → `outputterParams`:
	* \* The following parameters have been renamed (with backward compatibility):
		* \* `tpl` → `outputterParams->tpl`.
		* \* `tpl_placeholders` → `outputterParams->placeholders`.
	* \* `outputterParams->tpl`:
		* \+ Placeholders → `[+ddRunSnippetsResult.all+]`: The new placeholder. Contains results of all executed snippets combined by `''`.
		* \* Default value: Has been changed to `''` (all snippets will be executed but nothing will be returned).
	* \- `outputterParams->placeholders`: The outdated parameter name `placeholders` is no longer supported.
* \* README:
	* \+ Parameters description: Additional subheadings have been added.
	* \* Examples:
		* \+ Some additional comments have been added.
		* \* HJSON is used for all examples.
	* \+ Links → GitHub.


## Version 3.4 (2021-04-30)

* \+ Conversion to JSON and back is not used for nested objects anymore, recursion is used instead. So, you don't have to care about correct JSON format in result of each snippet when placeholders are used in object-values of parameters in any depth.


## Version 3.3 (2021-04-29)

* \* Attention! PHP >= 5.6 is required.
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.49.1 is required.
* \+ Parameters:
	* \+ `snippets_parseResults`: The new parameter. Adds the ability to parse result of each snippet by CMS parser.
	* \+ `snippets`, `tpl_placeholders`: Can also be set as [HJSON](https://hjson.github.io/) or as a native PHP object or array (e. g. for calls through `$modx->runSnippet`).
* \+ You can just call `\DDTools\Snippet::runSnippet` to run the snippet without DB and eval (see README → Examples).
* \+ `\ddRunSnippets\Snippet`: The new class. All snippet code was moved here.
* \+ README:
	* \+ Documentation → Installation → Using (MODX)EvolutionCMS.libraries.ddInstaller.
	* \+ Links.
* \+ Composer.json → `support`.


## Version 3.2 (2020-06-03)

* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.38.1 is required.
* \+ Parameters → `snippets->{$snippetName}->{$paramName}`: Placeholders of previous snippet executions results also works in object-values of parameters in any depth.
* \* Refactoring.
* \+ README → Documentation → Examples → Pass objects and arrays as snippet params.


## Version 3.1.1 (2020-05-21)

* \* Parameters → `tpl_placeholders`:
	* \* Renamed from `placeholders` (with backward compatibility).
	* \* Truly supports JSON and QueryString.


## Version 3.1 (2020-05-14)

* \+ Parameters → `snippets->{$snippetName}->{$paramName}`: As opposed to standard CMS calling you can pass not only string parameters to the snippet, any types are supported.


## Version 3.0 (2020-05-13)

* \* Attention! Backward compatibility is broken.
* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.35.1 is required (not tested in older versions).
* \* The snippet completely revised, see README.
* \+ README → Documentation → Examples.


## Version 2.5.1b (2016-12-28)

* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.16.1 is required.
* \* Compatibility with the new (MODX)EvolutionCMS.libraries.ddTools.
* \* Minor changes.


## Version 2.5b (2013-04-19)

* \+ The alias setting feature for snippets results are now available (`snipName` parameter).
* \* A few small fixes.


## Version 2.4 (2012-10-24)

* \+ The feature allowing snippet return placeholder prefix to be changed has been added (`resultPrefix` parameter with default value equals `'ddresult'`).


## Version 2.3 (2012-08-20)

* \* Added processing of empty results outputting by template (empty results will be removed).


## Version 2.2 (2012-04-09)

* \* Attention! (MODX)EvolutionCMS.libraries.ddTools >= 0.2 is required.
* \+ Added the ability to return a few snippets results at random through the separator with returning without a template (params `num` and `glue`).


## Version 2.1 (2012-03-18)

* \+ Added the ability to return final result into placeholder (params `toPlaceholder` and `placeholderName`).


## Version 2.0 (2012-03-10)

* \+ Snippet redeveloped to run few snippets.
* \* Large amount of code changes.
* \* Params `name`, `parameters` and `values` renamed into `snipName`, `snipParams` and `snipValues` respectively.


## Version 1.0 (2011-12-12)

* \+ The first release.


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />
<style>ul{list-style:none;}</style>