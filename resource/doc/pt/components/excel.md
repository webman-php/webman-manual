# Excel

## phpoffice/phpspreadsheet

### Endereço do Projeto

https://github.com/PHPOffice/PhpSpreadsheet
  
### Instalação
 
```php
composer require phpoffice/phpspreadsheet
```
 
### Uso

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
        $sheet->setCellValue('A1', 'Olá Mundo!');

        $writer = new Xlsx($spreadsheet);
        $file_path = public_path().'/hello_world.xlsx';
        // Salvar arquivo em público
        $writer->save($file_path);
        // Baixar arquivo
        return response()->download($file_path, 'nome_do_arquivo.xlsx');
    }

}
```


### Mais Conteúdo

Visite https://phpspreadsheet.readthedocs.io/en/latest/
