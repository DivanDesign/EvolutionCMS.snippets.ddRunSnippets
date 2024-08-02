# (MODX)EvolutionCMS.snippets.ddRunSnippets

Snippet runs necessary snippets with necessary params. Capabilities:

* Run a few snippets consequentially.
* Snippets results can be sent into parameters names and/or into other snippets values (can repeat this as much as you like).
* Any executed snippet can return either a string or a native PHP array. It is convenient to use with “nested” placeholders (see examples below).
* The snippet result can be returned into the chunk `outputterParams->tpl`, have transferring additional data through the parameter `outputterParams->placeholders`.

See the documentation for a more complete picture.

___
☝ Please note that snippets are run through `\DDTools\Snippet::runSnippet`.
This increases performance and saves server resources, but unfortunately you can't run snippets that do not use `\DDTools\Snippet`.
Please give us feedback via [Telegram chat](https://t.me/dd_code) if this is critical.


## Requires

* PHP >= 7.4
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.ru/modx/ddtools) >= 0.63


## Installation


### Using [(MODX)EvolutionCMS.libraries.ddInstaller](https://github.com/DivanDesign/EvolutionCMS.libraries.ddInstaller)

Just run the following PHP code in your sources or [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
//Include (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddInstaller/require.php'
);

//Install (MODX)EvolutionCMS.snippets.ddRunSnippets
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.snippets.ddRunSnippets',
	'type' => 'snippet',
]);

//Install (MODX)EvolutionCMS.plugins.ddRunSnippets (it is required if you want to use the `snippets->{$snippetName}->runParams->cache` parameters)
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.plugins.ddRunSnippets',
	'type' => 'plugin',
]);
```

* If `ddRunSnippets` is not exist on your site, `ddInstaller` will just install it.
* If `ddRunSnippets` is already exist on your site, `ddInstaller` will check it version and update it if needed.


### Manually


#### 1. Elements → Snippets: Create a new snippet with the following data

1. Snippet name: `ddRunSnippets`.
2. Description: `<b>4.2.1</b> Snippet runs necessary snippets with necessary params.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddRunSnippets_snippet.php` file from the archive.


#### 2. Elements → Manage Files

1. Create a new folder `assets/snippets/ddRunSnippets/`.
2. Extract the archive to the folder (except `ddRunSnippets_snippet.php`).


#### 3. Install [(MODX)EvolutionCMS.plugins.ddRunSnippets](https://github.com/DivanDesign/EvolutionCMS.plugins.ddRunSnippets)

The plugin is required if you want to use the `snippets->{$snippetName}->runParams->cache` parameters.


## Parameters description


### Snippets execution parameters

* `snippets`
	* Description: List of snippets to be run. Snippets are called in accordance with the specified order.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as a native PHP object or array (e. g. for calls through `\DDTools\Snippet::runSnippet`):
			* `arrayAssociative`
			* `object`
	* **Required**
	
* `snippets->{$snippetName}`
	* Description: A snippet, when the key is the snippet name and the value is the snippet parameters.
		* By default a snippet result will be equal to `[+snippetName+]` in parameters and chunk (where `snippetName` is the snippet name).
		* But also you can set custom snippet aliases in this parameter using the `'='` delimiter (e. g. `ddGetDocuments=docs`).
		* Every snippet can return either a string or a native PHP array (it is convenient to use with “nested” placeholders, see examples below).
	* Valid values:
		* `object` — an object with snippet parameters (see below)
		* `boolean` — for simple snippet calls without parameters or if you need to use default parameters, you can just pass `true`
	* **Required**
	
* `snippets->{$snippetName}->{$paramName}`
	* Description: A snippet parameter, when the key is the parameter name and the value is the parameter value.
		* Use `[+snippetName+]` for substitution by any previous snippet execution result in the parameter name or value (where `snippetName` is the snippet name).
		* Or use `[+snippetAlias+]` if specified (e. g. `docs` if the snippet name set as `ddGetDocuments=docs`).
	* Valid values:
		* `mixed` — as opposed to standard CMS calling you can pass not only string parameters to the snippet, any types are supported
	* Default value: —
	
* `snippets->{$snippetName}->runParams`
	* Description: Additional parameters of snippet running.
	* Valid values: `object`
	* Default value: —
	
* `snippets->{$snippetName}->runParams->parseResultCompletely`
	* Description: Completely parse result of the snippet by CMS parser.
	* Valid values: `boolean`
	* Default value: — (depends on `snippets_parseEachResultCompletely`)
	
* `snippets->{$snippetName}->runParams->cache`
	* Description: You can cache snippet result to a specific file.
	* Valid values: `object`
	* Default value: —
	
* `snippets->{$snippetName}->runParams->cache->resourceId`
	* Description: Resource ID (e. g. document) related to cache.  
		It means that the cache file will be destroyed when the document will be updated or deleted.
	* Valid values: `string`
	* **Required**
	
* `snippets->{$snippetName}->runParams->cache->suffix`
	* Description: Unique cache name for the document.
	* Valid values: `string`
	* **Required**
	
* `snippets->{$snippetName}->runParams->cache->prefix`
	* Description: Cache file prefix. Useful if you want to cache some custom data that is not related to any documents.
	* Valid values: `string`
	* Default value: `'doc'`
	
* `snippets_parseEachResultCompletely`
	* Description: Parse result of each snippet by CMS parser.  
		Immediately after running each snippet, its result will be parsed by `$modx->parseDocumentSource()`.
	* Valid values: `boolean`
	* Default value: `false`


### Output parameters
	
* `outputterParams`
	* Description: Parameters to be passed to the specified outputter.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — as [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
		* It can also be set as native PHP object or array (e. g. for calls through `\DDTools\Snippet::runSnippet` or `$modx->runSnippet`):
			* `arrayAssociative`
			* `object`
	* Default value: —
	
* `outputterParams->tpl`
	* Description: Chunk for output results.  
		Available placeholders:
		* `[+`_snippetName_`+]` — a snippet result (where `snippetName` is a snippet name)
		* `[+ddRunSnippetsResult.all+]` — results of all executed snippets combined by `''`
		* `[+`_any placeholders from the `outputterParams->placeholders` parameter_`+]`
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
		* `''` — all snippets will be executed but nothing will be returned
	* Default value: `''`
	
* `outputterParams->placeholders`
	* Description:
		Additional data has to be passed into `outputterParams->tpl`.  
		Nested objects and arrays are supported too:
		* `{"someOne": "1", "someTwo": "test" }` => `[+someOne+], [+someTwo+]`.
		* `{"some": {"a": "one", "b": "two"} }` => `[+some.a+]`, `[+some.b+]`.
		* `{"some": ["one", "two"] }` => `[+some.0+]`, `[+some.1+]`.
	* Valid values: `object`
	* Default value: —


## Examples

All examples are written using [HJSON](https://hjson.github.io/), but if you want you can use vanilla JSON instead.


### Basic example

```
[[ddRunSnippets?
	&snippets=`{
		someSnippet: {
			exampleParam: Example value.
		}
		
		otherSnippet: {
			someParam: "[+someSnippet+]"
		}
		
		anotherSnippet: {
			//Results of previous snippets can be used both in parameters names and their values
			"[+otherSnippet+]": "[+someSnippet+]"
		}
	}`
	&outputterParams=`{
		tpl: "@CODE:[+anotherSnippet+]"
	}`
]]
```


### Use alias in snippet names

```
[[ddRunSnippets?
	&snippets=`{
		//Run “someSnippet” and save it's result as “snippet1”
		someSnippet=snippet1: {
			exampleParam: First example value.
		}
		
		//Run “someSnippet” and save it's result as “snippet2”
		someSnippet=snippet2: {
			exampleParam: Second example value.
			//Placeholder “[+snippet1+]” will be replaced to results of previous “someSnippet” call
			exampleParam2: "[+snippet1+]"
		}
		
		anotherSnippet: {
			someParam: "[+snippet2+]"
			"[+snippet1+]": "[+snippet2+]"
		}
	}`
	&outputterParams=`{
		tpl: "@CODE:[+anotherSnippet+]"
	}`
]]
```

We called snippet `someSnippet` twice with different parameters using two different aliases for each call: `snippet1` and `snippet2`.


### Pass objects and arrays as snippet params

As opposed to standard CMS calling you can pass not only string parameters to a snippet, any types are supported.

```
[[ddRunSnippets?
	&snippets=`{
		someSnippet: {
			//Object as parameter value
			exampleParam1: {
				objectField: “exampleParam1” is an object, not a string.
				otherField: true
			}
			
			//Array as parameter value
			exampleParam2: [
				“exampleParam2” is an array, not a string
				2.71
			]
		}
	}`
]]
```


### Using results of previous snippets in object-values of parameters

```
[[ddRunSnippets?
	&snippets=`{
		someSnippet: {
			exampleParam: Example value.
		}
		
		otherSnippet: {
			exampleParam1: {
				objectField: “exampleParam1” is an object, not a string.
				otherField: So, “someSnippet” result will be replace the [+someSnippet+] placeholder here.
				anotherField: {
					deepField: Snipepts results placeholders will work fine independent of object depth.
					otherDeepField: The palceholder [+someSnippet+] works here too.
				}
			}
		}
	}`
]]
```


### Native PHP arrays as results of snippets and “nested” placeholders

Let the example snippet named `personData` that returs a native PHP array instead of usual string:

```php
//Just for example let the snippet do nothing but only return an array :)
return [
	'name' => 'Tamara Eidelman',
	'birthdate' => '1959.12.15',
	'links' => [
		'youtube' => 'https://youtube.com/c/TamaraEidelmanHistory',
		'site' => 'https://eidelman.ru',
	],
];
```

Then execute some snippets via ddRunSnippets:

```
[[ddRunSnippets?
	&snippets=`{
		//Run our example snippet that returns a native PHP array (without parameters in this example case)
		personData: true
		
		//Run other snippet
		someSnippet: {
			//If we need to pass whole result of the first snippet, just use usual placeholder
			//ddRunSnippet will convert the native PHP array to JSON in this case
			personData: "[+personData+]"
		}
		
		//Run another snippet
		anotherSnippet: {
			//Also we can use “nested” placeholders to get an item of the snippet result array
			info: "[+personData.name+], [+personData.birthdate+]"
			//And it works with depth
			youtube: "[+personData.links.youtube+]"
		}
	}`
]]
```


### Run the snippet through `\DDTools\Snippet::runSnippet` without DB and eval

```php
//Include (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddTools/modx.ddtools.class.php'
);

//Run (MODX)EvolutionCMS.snippets.ddRunSnippets
\DDTools\Snippet::runSnippet([
	'name' => 'ddRunSnippets',
	'params' => [
		'snippets' => [
			'someSnippet' => [
				'exampleParam' => 'Example value.',
			],
			'otherSnippet' => [
				'someParam' => '[+someSnippet+]',
			],
			'anotherSnippet' => [
				'[+otherSnippet+]' => '[+someSnippet+]',
			],
		],
		'outputterParams' => [
			'tpl' => '@CODE:[+anotherSnippet+]',
		],
	],
]);
```


## Links

* [Home page](https://code.divandesign.ru/modx/ddrunsnippets)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddrunsnippets)
* [GitHub](https://github.com/DivanDesign/EvolutionCMS.snippets.ddRunSnippets)


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />