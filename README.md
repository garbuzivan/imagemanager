# Image Manager: библиотека для работы с изображениями + расширение для Laravel

## Установка

`composer require garbuzivan/imagemanager`

<p>При использовании стандартного Transport класса пакет не подразуемвает хранение данных об изображениях 
и при использовании методов поиска и хранения будет возвращать пустые данные.</p>

<p>EloquentTransport - подразумевает использование Laravel Eloquent, предварительно требует публикации конфигурации и миграции.</p>

### Laravel
<p>и опубликовать конфигурацию</p>

`php artisan vendor:publish  --force --provider="GarbuzIvan\ImageManager\ImageManagerServiceProvider" --tag="config"`

<p>Теперь нужно применить миграции:</p>

`php artisan migrate`

## Архитектура библиотеки

## Использование

### Загрузка Laravel конфига из файла 
`$config = new GarbuzIvan\ImageManager\Laravel\Config;`

### Экземпляр класса ImageManager
`$image = new GarbuzIvan\ImageManager\ImageManager($config);`

### Загрузка ищображения по ссылке
`$image->loadUrl('https://zebrains.ru/static/images/intep_case_preview.4865ac.jpg');`

### Загрузка ищображения из файла
`$image->loadFile($_FILES["fileToUpload"]["tmp_name"]);`

### Загрузка ищображения из строки base64
`$image->loadBase64('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAAB3RJTUUH1ggDCwMADQ4NnwAAAFVJREFUGJWNkMEJADEIBEcbSDkXUnfSgnBVeZ8LSAjiwjyEQXSFEIcHGP9oAi+H0Bymgx9MhxbFdZE2a0s9kTZdw01ZhhYkABSwgmf1Z6r1SNyfFf4BZ+ZUExcNUQUAAAAASUVORK5CYII=');`

### Сохранение загруженного файла $title название изображения, удобно использовать для поиску по содержимому и в СЕО
`$image->save(string $title = null);`

### Поиск изображения по ID
`$image->getByID(1)->getImage();`

### Поиск изображения по hash
`$image->getByHash('5b041cd17933badbb7658de2b45ba8de188df628')->getImage();`

### Поиск изображения по filesize
<p>Аргументы применяют значения в формате int, если установить null - аргумент не будет учитываться при поиске</p>
`$list = $image->getByFileSize($minFileSize = null, $mxnFileSize = null, int $limitItem = 10, int $numberPage = 1);`
<p>Метод возвращает массив изображений соответствующих запросу.</p>

### Поиск изображения по hash
<p>Аргументы применяют значения в формате int, если установить null - аргумент не будет учитываться при поиске</p>
`$list = $image->getBySize(int $minWidth = null, int $maxWidth = null, int $minHeight = null, int $maxHeight = null, , int $limitItem = 10, int $numberPage = 1);`
<p>Метод возвращает массив изображений соответствующих запросу.</p>

### Обновление title изображения
`$img = $ImageManager->getByID(2)->getImage(); `
`$img['title'] = 'Тестовое ZB изображение';`
`$ImageManager->update($img);`

### Конфигурация пакета

## Тестирование

`./vendor/bin/phpunit ./vendor/garbuzivan/imagemanager/tests`
