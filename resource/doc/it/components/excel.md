# Excel

## phpoffice/phpspreadsheet

### Indirizzo del progetto

https://github.com/PHPOffice/PhpSpreadsheet

### Installazione

```php
composer require phpoffice/phpspreadsheet
```

### Utilizzo

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
        $sheet->setCellValue('A1', 'Ciao Mondo!');

        $writer = new Xlsx($spreadsheet);
        $file_path = public_path().'/hello_world.xlsx';
        // Salva il file nella directory pubblica
        $writer->save($file_path);
        // Scarica il file
        return response()->download($file_path, 'Nome file.xlsx');
    }

}
```

### Ulteriori informazioni

Visita https://phpspreadsheet.readthedocs.io/en/latest/
