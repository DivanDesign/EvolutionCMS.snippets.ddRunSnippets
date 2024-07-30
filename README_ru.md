# (MODX)EvolutionCMS.snippets.ddRunSnippets

Сниппет запускает необходимые сниппеты с необходимыми параметрами. Возможности:

* Последовательный запуск нескольких сниппетов.
* Результаты выполнения сниппетов можно передавать в названия параметров и/или значения других сниппетов (и так сколько угодно).
* Любой запускаемый сниппет может возвращать как строку, так и нативный PHP-массив. Удобно использовать с “вложенными” плейсхолдерами (см. примеры ниже).
* Результат выполнения сниппета можно выводить в чанк `outputterParams->tpl`, передав дополнительные данные через параметр `outputterParams->placeholders`.

Для более полного представления смотрите документацию.

___
☝ Обратите внимание, что сниппеты запускаются через `\DDTools\Snippet::runSnippet`.
Это повышает производительность и экономит ресурсы сервера, но, к сожалению, вы не можете запускать сниппеты, которые не используют `\DDTools\Snippet`.
Пожалуйста, дайте нам обратную связь через [Telegram-чат](https://t.me/dd_code), если это критично.


## Использует

* PHP >= 7.4
* [(MODX)EvolutionCMS.libraries.ddTools](https://code.divandesign.ru/modx/ddtools) >= 0.62


## Установка


### Используя [(MODX)EvolutionCMS.libraries.ddInstaller](https://github.com/DivanDesign/EvolutionCMS.libraries.ddInstaller)

Просто вызовите следующий код в своих исходинках или модуле [Console](https://github.com/vanchelo/MODX-Evolution-Ajax-Console):

```php
//Подключение (MODX)EvolutionCMS.libraries.ddInstaller
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddInstaller/require.php'
);

//Установка (MODX)EvolutionCMS.snippets.ddRunSnippets
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.snippets.ddRunSnippets',
	'type' => 'snippet',
]);

//Установка (MODX)EvolutionCMS.plugins.ddRunSnippets (обязателен, если хотите использовать параметры `snippets->{$snippetName}->runParams->cache`)
\DDInstaller::install([
	'url' => 'https://github.com/DivanDesign/EvolutionCMS.plugins.ddRunSnippets',
	'type' => 'plugin',
]);
```

* Если `ddRunSnippets` отсутствует на вашем сайте, `ddInstaller` просто установит его.
* Если `ddRunSnippets` уже есть на вашем сайте, `ddInstaller` проверит его версию и обновит, если нужно. 


### Вручную


#### 1. Элементы → Сниппеты: Создайте новый сниппет со следующими параметрами

1. Название сниппета: `ddRunSnippets`.
2. Описание: `<b>4.2</b> Сниппет запускает необходимые сниппеты с необходимыми параметрами.`.
3. Категория: `Core`.
4. Анализировать DocBlock: `no`.
5. Код сниппета (php): Вставьте содержимое файла `ddRunSnippets_snippet.php` из архива.


#### 2. Элементы → Управление файлами

1. Создайте новую папку `assets/snippets/ddRunSnippets/`.
2. Извлеките содержимое архива в неё (кроме файла `ddRunSnippets_snippet.php`).


#### 3. Установите [(MODX)EvolutionCMS.plugins.ddRunSnippets](https://github.com/DivanDesign/EvolutionCMS.plugins.ddRunSnippets)

Плагин обязателен, если хотите использовать параметры `snippets->{$snippetName}->runParams->cache`


## Описание параметров


### Параметры запуска сниппетов

* `snippets`
	* Описание: Список сниппетов для выполнения. Сниппеты вызываются в соответствии с указанным порядком.
	* Допустимые значения:
		* `stringJsonObject` — в виде [JSON](https://ru.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — в виде [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
		* Также может быть задан, как нативный PHP объект или массив (например, для вызовов через `\DDTools\Snippet::runSnippet`).
			* `arrayAssociative`
			* `object`
	* **Обязателен**
	
* `snippets->{$snippetName}`
	* Описание: Сниппет, где ключ — имя сниппета, значение — параметры сниппета.
		* По умолчанию результат сниппета (в параметрах или чанке) будет выглядеть как `[+snippetName+]` (где `snippetName` — имя сниппета).  
		* Но можно в этом параметре через `'='` задать произвольное значение (например: `ddGetDocuments=docs`).
		* Каждый сниппет может возвращать как строку, так и нативный PHP-массив (удобно использовать с “вложенными” плейсхолдерами, см. примеры ниже).
	* Допустимые значения:
		* `object` — объект с параметрами сниппета (см. ниже)
		* `boolean` — для простых вызовов сниппетов без параметров, можно просто передать `true`
	* **Обязателен**
	
* `snippets->{$snippetName}->{$paramName}`
	* Описание: Параметр сниппета, где ключ — имя параметра, значение — значение параметра.
		* Используйте `[+snippetName+]` для замены любым предыдущим результатом выполнения сниппета в имени параметра или значении (где `snippetName` — имя сниппета).
		* Либо используйте псевдоним `[+snippetAlias+]`, если задан (например, `docs`, если имя сниппета задано как `ddGetDocuments=docs`).
	* Допустимые значения:
		* `mixed` — в отличие от стандартного вызова CMS вы можете передавать не только строковые параметры, поддерживаютя любые типы
	* Значение по умолчанию: —
	
* `snippets->{$snippetName}->runParams`
	* Описание: Дополнительные параметры запуска сниппета
	* Допустимые значения: `object`
	* Значение по умолчанию: —
	
* `snippets->{$snippetName}->runParams->parseResultCompletely`
	* Описание: Окончательно распарсить результат парсером CMS.
	* Допустимые значения: `boolean`
	* Значение по умолчанию: — (зависит от `snippets_parseEachResultCompletely`)
	
* `snippets->{$snippetName}->runParams->cache`
	* Описание: Результат выполнения сниппета можно кэшировать в определённый файл.
	* Допустимые значения: `object`
	* Значение по умолчанию: —
	
* `snippets->{$snippetName}->runParams->cache->resourceId`
	* Описание: ID ресурса (например, документа), связанного с кэшем.  
		Это означает, что файл кэша будет уничтожен, когда документ будет обновлен или удален.
	* Допустимые значения: `string`
	* **Обязателен**
	
* `snippets->{$snippetName}->runParams->cache->name`
	* Описание: Уникальное имя файла кэша в пределах документа.
	* Допустимые значения: `string`
	* **Обязателен**
	
* `snippets->{$snippetName}->runParams->cache->prefix`
	* Описание: Префикс файла кэша. Удобно, если вы хотите кэшировать какие-то произвольные данные, не связанные ни с какими документами.
	* Допустимые значения: `string`
	* Значение по умолчанию: `'doc'`
	
* `snippets_parseEachResultCompletely`
	* Описание: Парсить результат каждого сниппета парсером CMS.  
		Сразу после запуска каждого сниппета, его результат будет пропущен через `$modx->parseDocumentSource()`.
	* Допустимые значения: `boolean`
	* Значение по умолчанию: `false`


### Параметры вывода
	
* `outputterParams`
	* Описание: Параметры вывода.
	* Допустимые значения:
		* `stringJsonObject` — в виде [JSON](https://ru.wikipedia.org/wiki/JSON)
		* `stringHjsonObject` — в виде [HJSON](https://hjson.github.io/)
		* `stringQueryFormatted` — в виде [Query string](https://en.wikipedia.org/wiki/Query_string)
		* Также может быть задан, как нативный PHP объект или массив (например, для вызовов через `\DDTools\Snippet::runSnippet`).
			* `arrayAssociative`
			* `object`
	* Значение по умолчанию: —
	
* `outputterParams->tpl`
	* Описание: Чанк для вывода результатов.    
		Доступные плейсхолдеры:
		* `[+snippetName+]` — результат выполнения сниппета (где `snippetName` — имя сниппета).
		* `[+ddRunSnippetsResult.all+]` — результаты всех выполненных сниппетов, склеенные через `''`
		* `[+`_любой плейсхолдер из параметра `placeholders`_`+]`
	* Допустимые значения:
		* `stringChunkName`
		* `string` — передавать код напрямую без чанка можно начиная значение с `@CODE:`
		* `''` — все сниппеты выполнятся, но ничего не выведется
	* Значение по умолчанию: `''`
	
* `outputterParams->placeholders`
	* Описание: Дополнительные данные, которые будут переданы в чанк `outputterParams->tpl`.  
		Вложенные объекты и массивы также поддерживаются:
		* `{"someOne": "1", "someTwo": "test" }` => `[+someOne+], [+someTwo+]`.
		* `{"some": {"a": "one", "b": "two"} }` => `[+some.a+]`, `[+some.b+]`.
		* `{"some": ["one", "two"] }` => `[+some.0+]`, `[+some.1+]`.
	* Допустимые значения: `object`
	* Значение по умолчанию: —


## Примеры

Все примеры написаны с использованием [HJSON](https://hjson.github.io/), но вместо него можно также использвоать обычный JSON.


### Базовый пример

```
[[ddRunSnippets?
	&snippets=`{
		someSnippet: {
			exampleParam: Какое-то значение параметра.
		}
		
		otherSnippet: {
			someParam: "[+someSnippet+]"
		}
		
		anotherSnippet: {
			//Результаты предыдущих сниппетов могут быть использованы как в именах параметров, так и в их значениях
			"[+otherSnippet+]": "[+someSnippet+]"
		}
	}`
	&outputterParams=`{
		tpl: "@CODE:[+anotherSnippet+]"
	}`
]]
```


### Использование псевдонимаов в именах сниппетов

```
[[ddRunSnippets?
	&snippets=`{
		//Запускаем «someSnippet» и сохраняем его результат как «snippet1»
		someSnippet=snippet1: {
			exampleParam: Какое-то значение параметра.
		}
		
		//Запускаем «someSnippet» и сохраняем его результат как «snippet2»
		someSnippet=snippet2: {
			exampleParam: Ещё какое-то значение параметра.
			//Плейсхолдер «[+snippet1+]» будет заменён на результат предыдущего вызова «someSnippet»
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

Мы вызвали сниппет `someSnippet` дважды с разными параметрами, используя два разных псевдонима для каждого вызова: `snippet1` и `snippet2`.


### Передача объектов и массивов в качестве параметров сниппета

В отличие от стандартного вызова CMS вы можете передавать не только строковые параметры, поддерживаютя любые типы.

```
[[ddRunSnippets?
	&snippets=`{
		someSnippet: {
			//Объект в качестве значения параметра
			exampleParam1: {
				objectField: Параметр «exampleParam1» — объект, не строка.
				anotherField: true
			}
			
			//Массив в качестве значения параметра
			exampleParam2: [
				Параметр «exampleParam2» — массив, не строка.
				2.71
			]
		}
	}`
]]
```


### Использование результатов выполнения предыдущих сниппетов в объектах-значениях параметров

```
[[ddRunSnippets?
	&snippets=`{
		someSnippet: {
			exampleParam: Какое-то значение параметра.
		}
		otherSnippet: {
			exampleParam1: {
				objectField: Параметр «exampleParam1» — объект, не строка.
				otherField: При этом, результат выполнения сниппета «someSnippet» будет подставлен место плейсхолдера [+someSnippet+] сюда.
				anotherField: {
					deepField: Плейсхолдеры результатов сниппетов будут работать вне зависимости от глубины объектов параметров.
					otherDeepField: Плейсхолдер [+someSnippet+] подставится и сюда.
				}
			}
		}
	}`
]]
```


### Нативные PHP-массивы в качестве результатов сниппетов и «вложенные» плейсхолдеры

Сделаем для примера тестовый сниппет с названием `personData`, который вернёт нативный PHP объект вместо привычной строки:

```php
//Просто для примера пусть сниппет ничего не делает, только возвращает массив :)
return [
	'name' => 'Тамара Эйдельман',
	'birthdate' => '1959.12.15',
	'links' => [
		'youtube' => 'https://youtube.com/c/TamaraEidelmanHistory',
		'site' => 'https://eidelman.ru',
	],
];
```

Потом запустим несколько сниппетов через ddRunSnippets:

```
[[ddRunSnippets?
	&snippets=`{
		//Запустим наш тестовый сниппет, который верёнт нативный PHP-массив (без параметров в этом тестовом примере)
		personData: true
		
		//Запустим другой сниппет
		someSnippet: {
			//Если нам нужно передать весь результат первого сниппета, просто используем обычный плейсхолдер
			//ddRunSnippet сконвертирует нативный PHP-массив в JSON в этом случае
			personData: "[+personData+]"
		}
		
		//Запустим ещё один сниппет
		anotherSnippet: {
			//Также мы можем использовать «вложенные» плейсхолдеры, чтобы получить конкретный элемент массива, который вернул наш тестовый сниппет
			info: "[+personData.name+], [+personData.birthdate+]"
			//И это работает с любой глубиной вложенности
			youtube: "[+personData.links.youtube+]"
		}
	}`
]]
```


### Запустить сниппет через `\DDTools\Snippet::runSnippet` без DB и eval

```php
//Подключение (MODX)EvolutionCMS.libraries.ddTools
require_once(
	$modx->getConfig('base_path')
	. 'assets/libs/ddTools/modx.ddtools.class.php'
);

//Запуск (MODX)EvolutionCMS.snippets.ddRunSnippets
\DDTools\Snippet::runSnippet([
	'name' => 'ddRunSnippets',
	'params' => [
		'snippets' => [
			'someSnippet' => [
				'exampleParam' => 'Какое-то значение параметра.',
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


## Ссылки

* [Home page](https://code.divandesign.ru/modx/ddrunsnippets)
* [Telegram chat](https://t.me/dd_code)
* [Packagist](https://packagist.org/packages/dd/evolutioncms-snippets-ddrunsnippets)
* [GitHub](https://github.com/DivanDesign/EvolutionCMS.snippets.ddRunSnippets)


<link rel="stylesheet" type="text/css" href="https://raw.githack.com/DivanDesign/CSS.ddMarkdown/master/style.min.css" />