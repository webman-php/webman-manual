# Excel

## phpoffice/phpspreadsheet

### プロジェクトアドレス

https://github.com/PHPOffice/PhpSpreadsheet
  
### インストール
 
  ```php
  composer require phpoffice/phpspreadsheet
  ```
  
### 使用方法

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
        // ファイルをpublicに保存
        $writer->save($file_path);
        // ファイルをダウンロード
        return response()->download($file_path, 'ファイル名.xlsx');
    }

}
```
  
### さらに詳細な情報

https://phpspreadsheet.readthedocs.io/en/latest/

