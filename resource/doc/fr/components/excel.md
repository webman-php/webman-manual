# Excel

## phpoffice/phpspreadsheet

### Adresse du projet

https://github.com/PHPOffice/PhpSpreadsheet
  
### Installation
 
  ```php
  composer require phpoffice/phpspreadsheet
  ```
  
### Utilisation

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
        $sheet->setCellValue('A1', 'Bonjour le monde !');

        $writer = new Xlsx($spreadsheet);
        $file_path = public_path().'/hello_world.xlsx';
        // Enregistrer le fichier dans le répertoire public
        $writer->save($file_path);
        // Télécharger le fichier
        return response()->download($file_path, 'nom_du_fichier.xlsx');
    }

}
```


### Plus d'informations

Visitez https://phpspreadsheet.readthedocs.io/en/latest/
