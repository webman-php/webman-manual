# Excel

## phpoffice/phpspreadsheet

### Адрес проекта

https://github.com/PHPOffice/PhpSpreadsheet
  
### Установка
 
  ```php
  composer require phpoffice/phpspreadsheet
  ```
  
### Использование

```php
<?php
namespace app\controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExcelController
{
    public function index($request)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');

        $writer = new Xlsx($spreadsheet);
        $file_path = public_path().'/hello_world.xlsx';
        // Сохранение файла в public
        $writer->save($file_path);
        // Скачивание файла
        return response()->download($file_path, 'имя_файла.xlsx');
    }

}
```
  
### Дополнительная информация

Посетите https://phpspreadsheet.readthedocs.io/en/latest/
