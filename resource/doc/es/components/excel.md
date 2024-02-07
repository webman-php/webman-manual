# Excel

## phpoffice/phpspreadsheet

### Dirección del proyecto

https://github.com/PHPOffice/PhpSpreadsheet
  
### Instalación
```php
composer require phpoffice/phpspreadsheet
```
  
### Uso
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
        $sheet->setCellValue('A1', '¡Hola, mundo!');

        $writer = new Xlsx($spreadsheet);
        $file_path = public_path().'/hello_world.xlsx';
        // Guardar archivo en la carpeta public
        $writer->save($file_path);
        // Descargar archivo
        return response()->download($file_path, 'filename.xlsx');
    }

}
```
### Más información
Visita https://phpspreadsheet.readthedocs.io/en/latest/
