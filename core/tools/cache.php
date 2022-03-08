<?php
// нужно будет создать класс для работы с файлами, чтобы выдавать ошибку когда нет прав доступа на файл
// 

/**
 * TODO
 * нужно будет создать класс для работы с файлами, чтобы выдавать ошибку когда нет прав доступа на файл
 * - потому что если в папке cache нет папки model то она не будет создана с конструкцией
 *   Cache::set('model/tables', $this->tables);
 * - так же надо будет сделать возможность очистки дириктории в новом классе для Файлов
 *   чтобы можно было сделать полную очистку кэша во всех папках
 */


class Cache
{
    public static function set($name, $data)
    {
        $debug = (require('config/settings.php'))['debug'];
        if ($debug) {
            self::clear('all');
            return null;
        }
        $path = 'cache/'.$name.'.php';
        $data = "<?php\nreturn ".var_export($data, true).";";
        file_put_contents($path, $data);
    }

    public static function get($name)
    {
        $debug = (require('config/settings.php'))['debug'];
        if ($debug) {
            self::clear('all');
            return null;
        }
        $path = 'cache/'.$name.'.php';
        if (file_exists($path)) {
            $data = require($path);
            return $data;
        }
        return null;
    }

    /**
     * @return 
     * true - успешно
     * false - ошибка
     * null - файл не найден
     */

    // переделать это потому что в папке кэша есть еще папка кэша view

    public static function clear($name)
    {
    //     if ($name == 'all') {
    //         $files = glob('cache/*');
    //         foreach ($files as $file) {
    //             if (is_file($file)) {
    //                 return unlink($file);
    //             } else {
    //                 rmdir($file);
    //             }
    //         }
    //     } else {
    //         $path = 'cache/'.$name.'.php';
    //         if (file_exists($path)) {
    //             return unlink($path);
    //         }
    //     }
    //     return null;
    }
}

// new Cache();