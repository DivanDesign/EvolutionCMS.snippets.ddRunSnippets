# (MODX)EvolutionCMS.snippets.ddRunSnippets

Сниппет запускает необходимые сниппеты с необходимыми параметрами. Возможности:

* Последовательный запуск нескольких сниппетов.
* Результаты выполнения сниппетов можно передавать в названия параметров и/или значения других сниппетов (и так сколько угодно).
* Результат выполнения сниппета можно выводить в чанк `tpl`, передав дополнительные данные через параметр `placeholders`.

Для более полного представления смотрите документацию.


## Использует

* PHP >= 5.6
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.biz/modx/ddtools) >= 0.49.1


## Документация


### Установка

Элементы → Сниппеты: Создайте новый сниппет со следующими параметрами:

1. Название сниппета: `ddRunSnippets`.
2. Описание: `<b>3.2</b> Сниппет запускает необходимые сниппеты с необходимыми параметрами.`.
3. Категория: `Core`.
4. Анализировать DocBlock: `no`.
5. Код сниппета (php): Вставьте содержимое файла `ddRunSnippets_snippet.php` из архива.


### Описание параметров

* `snippets`
	* Описание: Список сниппетов для выполнения. Сниппеты вызываются в соответствии с указанным порядком.
	* Допустимые значения:
		* `stringJsonObject` — в виде [JSON](https://ru.wikipedia.org/wiki/JSON)
		* `stringQueryFormated` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
	* **Обязателен**
	
* `snippets->{$snippetName}`
	* Описание: Сниппет, где ключ — имя сниппета, значение — параметры сниппета.
		* По умолчанию результат сниппета (в параметрах или чанке) будет выглядеть как `[+snippetName+]` (где `snippetName` — имя сниппета).  
		* Но можно в этом параметре через `'='` задать произвольное значение (например: `Ditto=docs`).
	* Допустимые значения:
		* `object` — объект с параметрами сниппета (см. ниже)
		* `boolean` — для простых вызовов сниппетов без параметров, можно просто передать `true`
	* **Обязателен**
	
* `snippets->{$snippetName}->{$paramName}`
	* Описание: Параметр сниппета, где ключ — имя параметра, значение — значение параметра.
		* Используйте `[+snippetName+]` для замены любым предыдущим результатом выполнения сниппета в имени параметра или значении (где `snippetName` — имя сниппета).
		* Либо используйте псевдоним `[+snippetAlias+]`, если задан (например, `docs`, если имя сниппета задано как `Ditto=docs`).
	* Допустимые значения:
		* `mixed` — в отличие от стандартного вызова CMS вы можете передавать не только строковые параметры, поддерживаютя любые типы
	* Значение по умолчанию: —
	
* `tpl`
	* Описание: Чанк для вывода результатов.    
		Доступные плейсхолдеры:
		* `[+snippetName+]` — результат выполнения сниппета (где `snippetName` — имя сниппета).
	* Допустимые значения:
		* `stringChunkName`
		* `string` — передавать код напрямую без чанка можно начиная значение с `@CODE:`
	* Значение по умолчанию: —
	
* `tpl_placeholders`
	* Описание: Дополнительные данные, которые будут переданы в чанк `tpl`.    
		Вложенные объекты и массивы также поддерживаются:
		* `{"someOne": "1", "someTwo": "test" }` => `[+someOne+], [+someTwo+]`.
		* `{"some": {"a": "one", "b": "two"} }` => `[+some.a+]`, `[+some.b+]`.
		* `{"some": ["one", "two"] }` => `[+some.0+]`, `[+some.1+]`.
	* Допустимые значения:
		* `stringJsonObject` — в виде [JSON](https://ru.wikipedia.org/wiki/JSON)
		* `stringQueryFormated` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
	* Значение по умолчанию: —


### Примеры


#### Базовый пример

```
[[ddRunSnippets?
	&snippets=`{
		"someSnippet": {
			"exampleParam": "Какое-то значение параметра."
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


#### Использование псевдонимаов в именах сниппетов

```
[[ddRunSnippets?
	&snippets=`{
		"someSnippet=snippet1": {
			"exampleParam": "Какое-то значение параметра."
		},
		"someSnippet=snippet2": {
			"exampleParam": "Ещё какое-то значение параметра.",
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

Мы вызвали сниппет `someSnippet` дважды с разными параметрами, используя два разных псевдонима для каждого вызова: `snippet1` и `snippet2`.


#### Передача объектов и массивов в качестве параметров сниппета

В отличие от стандартного вызова CMS вы можете передавать не только строковые параметры, поддерживаютя любые типы.

```
[[ddRunSnippets?
	&snippets=`{
		"someSnippet": {
			"exampleParam1": {
				"objectField": "Параметр «exampleParam1» — объект, не строка.",
				"anotherField": true
			},
			"exampleParam2": [
				"Параметр «exampleParam2» — массив, не строка.",
				2.71
			]
		}
	}`
]]
```


#### Использование результатов выполнения предыдущих сниппетов в объектах-значениях параметров

```
[[ddRunSnippets?
	&snippets=`{
		"someSnippet": {
			"exampleParam": "Какое-то значение параметра."
		},
		"otherSnippet": {
			"exampleParam1": {
				"objectField": "Параметр «exampleParam1» — объект, не строка.",
				"otherField": "При этом, результат выполнения сниппета «someSnippet» будет подставлен место плейсхолдера [+someSnippet+] сюда.",
				"anotherField": {
					"deepField": "Плейсхолдеры результатов сниппетов будут работать вне зависимости от глубины объектов параметров.",
					"otherDeepField": "[+someSnippet+] подставится и сюда."
				}
			}
		}
	}`
]]
```


## [Home page →](https://code.divandesign.biz/modx/ddrunsnippets)


<link rel="stylesheet" type="text/css" href="https://DivanDesign.ru/assets/files/ddMarkdown.css" />