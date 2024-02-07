# Excel

## phpoffice/phpspreadsheet

### ที่อยู่โครงการ

https://github.com/PHPOffice/PhpSpreadsheet
  
### การติดตั้ง
 
  ```php
  composer require phpoffice/phpspreadsheet
  ```
  
### การใช้

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
        $sheet->setCellValue('A1', 'สวัสดีชาวโลก!');

        $writer = new Xlsx($spreadsheet);
        $file_path = public_path().'/hello_world.xlsx';
        // บันทึกไฟล์ไว้ที่โฟลเดอร์ public
        $writer->save($file_path);
        // ดาวน์โหลดไฟล์
        return response()->download($file_path, 'ชื่อไฟล์.xlsx');
    }

}
```
  
### เนื้อหาเพิ่มเติม

เยี่ยมชม https://phpspreadsheet.readthedocs.io/en/latest/
