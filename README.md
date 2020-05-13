# (MODX)EvolutionCMS.snippets.ddRunSnippets

Snippet runs necessary snippets with necessary params. Capabilities:

* Run a few snippets consequentially.
* Snippets results can be sent into parameters names and/or into other snippets values (can repeat this as much as you like).
* The snippet result can be returned into the chunk `tpl`, have transferring additional data through the parameter `placeholders`.

See the documentation for a more complete picture.


## Requires

* PHP >= 5.4
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.35.1 (not tested in older versions)


## Documentation


### Installation

Elements → Snippets: Create a new snippet with the following data:

1. Snippet name: `ddRunSnippets`.
2. Description: `<b>3.0</b> Snippet runs necessary snippets with necessary params.`.
3. Category: `Core`.
4. Parse DocBlock: `no`.
5. Snippet code (php): Insert content of the `ddRunSnippets_snippet.php` file from the archive.


### Parameters description

* `snippets`
	* Desctription: List of snippets to be run. Snippets are called in accordance with the specified order.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
	* **Required**
	
* `snippets->{$snippetName}`
	* Desctription: A snippet, when the key is the snippet name and the value is the snippet parameters.
		* By default a snippet result will be equal to `[+snippetName+]` in parameters and chunk (where `snippetName` is the snippet name).
		* But also you can set custom snippet aliases in this parameter using the `'='` delimiter (e. g. `Ditto=docs`).
	* Valid values:
		* `object` — an object with snippet parameters (see below)
		* `boolean` — for simple snippet calls without parameters or if you need to use default parameters, you can just pass `true`
	* **Required**
	
* `snippets->{$snippetName}->{$paramName}`
	* Desctription: A snippet parameter, when the key is the parameter name and the value is the parameter value.
		* Use `[+snippetName+]` for substitution by any previous snippet execution result in the parameter name or value (where `snippetName` is the snippet name).
		* Or use `[+snippetAlias+]` if specified (e. g. `docs` if the snippet name set as `Ditto=docs`).
	* Valid values: `string`
	* Default value: —
	
* `tpl`
	* Desctription: Chunk for output results.  
		If not set, all snippets results will be returned joined by `''`.  
		If set as empty string, nothing will be returned (`''`).
		
		Available placeholders:
		* `[+snippetName+]` — a snippet result (where `snippetName` is the snippet name).
	* Valid values:
		* `stringChunkName`
		* `string` — use inline templates starting with `@CODE:`
	* Default value: —
	
* `tpl_placeholders`
	* Desctription:
		Additional data has to be passed into `tpl`.  
		Nested objects and arrays are supported too:
		* `{"someOne": "1", "someTwo": "test" }` => `[+someOne+], [+someTwo+]`.
		* `{"some": {"a": "one", "b": "two"} }` => `[+some.a+]`, `[+some.b+]`.
		* `{"some": ["one", "two"] }` => `[+some.0+]`, `[+some.1+]`.
	* Valid values:
		* `stringJsonObject` — as [JSON](https://en.wikipedia.org/wiki/JSON)
		* `stringQueryFormated` — as [Query string](https://en.wikipedia.org/wiki/Query_string)
	* Default value: —


### Examples


#### Basic example

```
[[ddRunSnippets?
	&snippets=`{
		"someSnippet": {
			"exampleParam": "Example value."
		},
		"otherSnippet": {
			"someParam": "[+someSnippet+]"
		},
		"anotherSnippet": {
			"[+otherSnippet+]": "[+someSnippet+]"
		}
	}`
	&tpl=`@CODE:[+anotherSnippet+]`
]]
```


#### Use alias in snippet names

```
[[ddRunSnippets?
	&snippets=`{
		"someSnippet=snippet1": {
			"exampleParam": "First example value."
		},
		"someSnippet=snippet2": {
			"exampleParam": "Second example value.",
			"exampleParam2": "[+snippet1+]"
		},
		"anotherSnippet": {
			"someParam": "[+snippet2+]",
			"[+snippet1+]": "[+snippet2+]"
		}
	}`
	&tpl=`@CODE:[+anotherSnippet+]`
]]
```

We called snippet `someSnippet` twice with different parameters using two different aliases for each call: `snippet1` and `snippet2`.


## [Home page →](https://code.divandesign.biz/modx/ddrunsnippets)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />