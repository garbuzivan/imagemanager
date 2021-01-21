# Image Manager: библиотека для работы с изображениями + расширение для Laravel

## Установка

`composer require garbuzivan/imagemanager`

### Laravel
и опубликовать конфигурацию

`php artisan vendor:publish  --force --provider="GarbuzIvan\ImageManager\ImageManagerServiceProvider" --tag="config"`

Теперь нужно применить миграции:

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
`$image->loadBase64('data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAoAAAAKCAYAAACNMs+9AAAABmJLR0QA/wD/AP+gvaeTAAAAB3RJTUUH1ggDCwMADQ4NnwAAAFVJREFUGJWNkMEJADEIBEcbSDkXUnfSgnBVeZ8LSAjiwjyEQXSFEIcHGP9oAi+H0Bymgx9MhxbFdZE2a0s9kTZdw01ZhhYkABSwgmf1Z6r1SNyfFf4BZ+ZUExcNUQUAAAAASUVORK5CYII=')
             ->save()->pipes();`

### Сохранение загруженного файла и постобработка Pipes из конфига
`$image->save()->pipes();`

### Поиск изображения по ID
`$image->getByID(1)->getImage();`

### Поиск изображения по hash
`$image->getByHash('5b041cd17933badbb7658de2b45ba8de188df628')->getImage();`

### Конфигурация пакета

## Тестирование

`./vendor/bin/phpunit ./vendor/garbuzivan/imagemanager/tests`
