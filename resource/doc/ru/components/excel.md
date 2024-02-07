# Excel

## phpoffice/phpspreadsheet

### Ссылка на проект

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
        // Сохранить файл в папке public
        $writer->save($file_path);
        // Скачать файл
        return response()->download($file_path, 'Название файла.xlsx');
    }

}
```


### Больше информации

Посетите https://phpspreadsheet.readthedocs.io/en/latest/
