# Excel

## phpoffice/phpspreadsheet

### Proje Adresi

https://github.com/PHPOffice/PhpSpreadsheet
  
### Kurulum
 
  ```php
  composer require phpoffice/phpspreadsheet
  ```
  
### Kullanım

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
        $sheet->setCellValue('A1', 'Merhaba Dünya !');

        $writer = new Xlsx($spreadsheet);
        $file_path = public_path().'/hello_world.xlsx';
        // Dosyayı public dizinine kaydet
        $writer->save($file_path);
        // Dosyayı indir
        return response()->download($file_path, 'dosya_adi.xlsx');
    }

}
```


### Daha Fazla İçerik

https://phpspreadsheet.readthedocs.io/en/latest/ adresini ziyaret edin.
