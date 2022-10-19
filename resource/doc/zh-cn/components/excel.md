# Excel

## phpoffice/phpspreadsheet

### 项目地址

https://github.com/PHPOffice/PhpSpreadsheet
  
### 安装
 
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
        // 保存文件到 public 下
        $writer->save($file_path);
        // 下载文件
        return response()->download($file_path, '文件名.xlsx');
    }

}
```
  
  
### 更多内容

访问 https://phpspreadsheet.readthedocs.io/en/latest/
  

