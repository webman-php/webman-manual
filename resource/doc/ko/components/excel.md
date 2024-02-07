# Excel

## phpoffice/phpspreadsheet

### 프로젝트 주소

https://github.com/PHPOffice/PhpSpreadsheet
  
### 설치
 
  ```php
  composer require phpoffice/phpspreadsheet
  ```
  
### 사용

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
        // public 폴더에 파일 저장
        $writer->save($file_path);
        // 파일 다운로드
        return response()->download($file_path, '파일명.xlsx');
    }

}
``` 
### 추가 정보

https://phpspreadsheet.readthedocs.io/en/latest/
