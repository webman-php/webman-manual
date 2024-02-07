# ওয়েবম্যান

ওয়েবম্যান হল একটি উচ্চ কার্যকরী PHP ফ্রেমওয়ার্ক, যা workerman এর উপর নির্মিত, নিম্নলিখিতটি ওয়েবম্যান এর নথি শুরু হয়।

## Excel

### phpoffice/phpspreadsheet

#### প্রকল্প ঠিকানা

https://github.com/PHPOffice/PhpSpreadsheet
  
#### ইনস্টলেশন
```php
composer require phpoffice/phpspreadsheet
```
  
#### ব্যবহার
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
        // ফাইলটি public এ সংরক্ষণ করুন
        $writer->save($file_path);
        // ফাইল ডাউনলোড করুন
        return response()->download($file_path, 'ফাইলের_নাম.xlsx');
    }

}
```
  
#### আরও বিস্তারিত

https://phpspreadsheet.readthedocs.io/en/latest/
