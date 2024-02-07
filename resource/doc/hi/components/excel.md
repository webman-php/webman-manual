# एक्सेल

## phpoffice/phpspreadsheet

### परियोजना पता

https://github.com/PHPOffice/PhpSpreadsheet

### स्थापना

```php
composer require phpoffice/phpspreadsheet
```

### प्रयोग

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
        // पब्लिक नजरबंद/ डुबान मार्ग पर फाइल सहेजें
        $writer->save($file_path);
        // फाइल डाउनलोड करें
        return response()->download($file_path, 'फ़ाइल्नाम.xlsx');
    }

}
```

### अधिक सामग्री

https://phpspreadsheet.readthedocs.io/en/latest/
