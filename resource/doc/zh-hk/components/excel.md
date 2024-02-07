# Excel

## phpoffice/phpspreadsheet

### Project Address

https://github.com/PHPOffice/PhpSpreadsheet

### 安裝

```php
composer require phpoffice/phpspreadsheet
```

### 使用

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
        // 將文件保存到 public 目錄下
        $writer->save($file_path);
        // 下載文件
        return response()->download($file_path, '文件名.xlsx');
    }

}
```

### 更多內容

請參閱 https://phpspreadsheet.readthedocs.io/en/latest/
