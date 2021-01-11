<?php

declare(strict_types=1);

namespace GarbuzIvan\ImageManager;

class ExceptionCode
{
    public static $FILTER_VALIDATE_URL = 'Передаваемый аргумент не является ссылкой';
    public static $MIME_TYPE_NOT_AVAILABLE = 'Тип файла не может быть обработан';
    public static $URL_NOT_LOAD = 'URL не доступен для загрузки';
}
