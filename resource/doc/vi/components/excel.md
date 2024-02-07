# Excel

## phpoffice/phpspreadsheet

### Địa chỉ dự án

https://github.com/PHPOffice/PhpSpreadsheet
  
### Cài đặt
 
  ```php
  composer require phpoffice/phpspreadsheet
  ```
  
### Sử dụng

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
        $sheet->setCellValue('A1', 'Xin chào thế giới !');

        $writer = new Xlsx($spreadsheet);
        $file_path = public_path().'/hello_world.xlsx';
        // Lưu tệp tin vào thư mục public
        $writer->save($file_path);
        // Tải tệp tin về
        return response()->download($file_path, 'Tên_tệp.xlsx');
    }

}
```
  
### Thêm nội dung

Truy cập https://phpspreadsheet.readthedocs.io/en/latest/
