# Excel

## phpoffice/phpspreadsheet

### Project Address

https://github.com/PHPOffice/PhpSpreadsheet

### Installation

```php
composer require phpoffice/phpspreadsheet
```

### Usage

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
        // Save file to public directory
        $writer->save($file_path);
        // Download file
        return response()->download($file_path, 'filename.xlsx');
    }
}
```

### Read More

Visit https://phpspreadsheet.readthedocs.io/en/latest/
