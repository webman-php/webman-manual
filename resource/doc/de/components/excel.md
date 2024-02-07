# Excel

## phpoffice/phpspreadsheet

### Projektadresse

https://github.com/PHPOffice/PhpSpreadsheet
  
### Installation
 
  ```php
  composer require phpoffice/phpspreadsheet
  ```
  
### Verwendung

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
        // Datei im Public-Verzeichnis speichern
        $writer->save($file_path);
        // Datei herunterladen
        return response()->download($file_path, 'Dateiname.xlsx');
    }

}
```
  
  
### Weitere Informationen

Besuchen Sie https://phpspreadsheet.readthedocs.io/en/latest/
