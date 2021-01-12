<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

class ExceptionCode
{
    public static $FILTER_VALIDATE_URL = 'Передаваемый аргумент не является ссылкой';
    public static $FILTER_VALIDATE_BASE64_IMAGE = 'Строка не является изображением base64';
    public static $MIME_TYPE_NOT_AVAILABLE = 'Тип файла не может быть обработан';
    public static $URL_NOT_LOAD = 'URL не доступен для загрузки';
    public static $MAKE_DIRECTORY = 'Ошибка создания директории хранения изображений';
    public static $FILE_NOT_EXISTS = 'Файл не найден';
}
