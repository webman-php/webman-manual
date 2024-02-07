# எக்செல்

## phpoffice/phpspreadsheet

### பிராஜெக்ட் இடம்

https://github.com/PHPOffice/PhpSpreadsheet
  
### நிறுவு

```php
composer require phpoffice/phpspreadsheet
```
  
### பயன்பாடு

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
        $sheet->setCellValue('A1', 'ஹலோ உலகமே !');

        $writer = new Xlsx($spreadsheet);
        $file_path = public_path().'/hello_world.xlsx';
        // பொது கோப்புக்கு கோப்பு சேமிக்க
        $writer->save($file_path);
        // கோப்பு பதிவிறக்க
        return response()->download($file_path, 'கோப்புப் பெயர்.xlsx');
    }

}
```
  
### மேலும் உள்ளடங்கள்

விரிவாக https://phpspreadsheet.readthedocs.io/en/latest/ பார்க்க 
