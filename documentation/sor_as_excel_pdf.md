# Proposal: SOR Chapter wise Report

## 1. Objective

This document proposes a new type of report: the **SOR Report**.

The goal of this report is to provide a high-level financial overview of a complete Schedule of Rates (SOR). Instead of showing the deep, nested analysis of every resource, this report will provide a single summary line for each item, highlighting its cost composition .

In Old codignetor this was done with following logic

1. Determine Rate Table:                                                                                                                                                                   
       * It first checks the $currentRateMode flag. If true, it sets the source table for rates to the "live" item_rates table. If false, it uses the sor_temp.item_rates table for  experimental rates.                                        
   
   2. Fetch Data:                                                                                                                                                                             
       * It executes a single large SQL query to get all items belonging to the specified sor Items.                                                                                               
       * It LEFT JOINs with the rate table decided in the previous step to pull in the rate and unit for each item.                                                                           
       * Crucially, the query uses ORDER BY lft. This is the key to the entire process. It assumes the items are already sorted in the correct hierarchical display order in  the database, so the PHP code only needs to iterate through a flat, pre-ordered list.                                                                                                
       
   3. Initialize Excel Object:                                                                                                                                                                
       * It loads the excel library (a wrapper for PHPExcel or PhpSpreadsheet).                                    
       * It sets up document properties (author, title, etc.) using a resource_file_info helper.   
       * It creates a worksheet and sets its title .                                                                           
       
   4. Set Up Headers and Styles:                                                                                                                                                              
       * It defines an array ($colwidthsetArray) that contains the configuration for the header row: column widths and names ("sno", "Chapter/Item No", "Particulars of Item", "Rate", "unit").                                                                                                                                                                             
       * It writes these headers to the sheet and applies basic styling (centering, background color).
       
   5. Iterate and Render Rows:                                                                                                                                                                
       * The code enters a foreach loop, iterating through the pre-ordered list of items fetched from the database.                                                                           
       * Depth and Styling: For each item, it calls $item->depth to find its level in the hierarchy. This "depth" value is used to select a font color and size from a predefined array 
         ($colorarray), making chapters appear different from items. Items (item_type ==3) get a standard style, while chapters (item_type <= 2) get dynamic styling based on their depth.  
       * Write Cell Data: It writes the serial number, item number, and description to their respective cells.                                                                                
       * Conditional Rate Logic: It checks if the item has a ref_from value.                                                                                                                  
           * If refferencd_from > 0, it means the rate is just a reference to another item. It writes the text "As per item no {referenced_item_no}" and merges the rate and unit cells.             
           * If ref_from is not set, it writes the actual rate and unit values fetched from the database.                                                                                     
       * The styling determined by the item's depth is applied to the entire row.                                                                                                            ▄
                                                                                                                                                                                             ▀
   6. Finalize and Save:                                                                                                                                                                      
       * After the loop, it applies final formatting to the sheet (e.g., vertical alignment, text wrapping, currency format for the "Rate" column).                                           
       * It generates a filename  on basis of sor id , ratecard id and date (e.g., SOR_MySOR_rc_1_22_11_2025.xlsx).                                                                                                                      
       * It calls printfileFromPHPExcel(), which saves the generated Excel object to the /uploads/files/ directory.                                                                           
       * Finally, it logs the creation of the file in the sor.files database table. 

## 2. Implementation Idea



Laravel Migration Plan                                                                                                                                                                      
You will need maatwebsite/excel for the export functionality and a PDF renderer like dompdf for PDF generation.                                                                                                                                                                                               
  The goal is to replicate this functionality using modern, modular, and maintainable Laravel practices. We will use the maatwebsite/excel package, which is the standard for Laravel.                                                          
                                                                                                                                                                                              
  Option 1: The "Data Mapping" Approach (Recommended for this use case)   option 2 is blade approach   

  This approach is clean, efficient, and uses dedicated maatwebsite/excel concerns to map data and apply styles.                                                                                                                                                                                                                                               

   1. Create an Export Class:                                                                                                                                                                 
      Generate a new export class using Artisan:                                                                                                                                              
      php artisan make:export SorExport                                                                                                                                                       

   2. Implement the `SorExport` Class:                                                                                                                                                        
      This class will handle all the logic for the export.   

      File: app/Exports/SorExport.php                                                                                                                                                         
                                                                                                                                                                                                    
               namespace App\Exports;                                                                                                                                                             
                                                                                                                                                                                                  
               use App\Models\Sor;                                                                                                                                                                
               use App\Models\Item;                                                                                                                                                               
               use Maatwebsite\Excel\Concerns\FromCollection;                                                                                                                                     
               use Maatwebsite\Excel\Concerns\WithMapping;                                                                                                                                        
               use Maatwebsite\Excel\Concerns\WithHeadings;                                                                                                                                       
               use Maatwebsite\Excel\Concerns\WithStyles;                                                                                                                                         
               use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;                                                                                                                                  
                                                                                                                                                                                                 
              class SorExport implements FromCollection, WithHeadings, WithMapping, WithStyles                                                                                                    
             {                                                                                                                                                                                
                 protected $sor;                                                                                                                                                              
                 protected $rateCard;                                                                                                                                                         
                                                                                                                                                                                              
                 public function __construct(Sor $sor, $rateCard                                                                                                                              
                 {                                                                                                                                                                            
                     $this->sor = $sor;                                                                                                                                                       
                     $this->rateCard = $rateCard;                                                                                                                                             
                 }                                                                                                                                                                            
                                                                                                                                                                                              
                 // 1. Fetches the data                                                                                                                                                       
                 public function collection()                                                                                                                                                 
                 {                                                                                                                                                                            
                     // Replicates the original query using Eloquen                                                                                                                           
                     return Item::where('sorId', $this->sor->sorid                                                                                                                            
                         ->with(['rate' => fn($q) => $q->where('ratecard', $this->rateCard->ratecardid)]                                                                                      
                         ->orderBy('orderFromNestedList')                                                                                                                                     
                         ->get();                                                                                                                                                                
                 } 
                                                                     
                 // 2. Defines the header row                                                                                                                                                
                 public function headings(): array                   
                 {                                                    
                     return ['S.No', 'Chapter/Item No', 'Particulars of Item', 'Rate', 'Unit']                    
                 }    

      

      // 3. Maps each item to an array for its row                                                                                                                                    
                 public function map($item): array                                                                                                                                            
                 {                                                                                                                                                                            
                     static $sno = 0;                                    
                     $sno++;                                                                                                                                                                  
                     $rate = $item->rate->rate ?? '';                   
                     $unit = $item->rate->unit->vUnitName ?? '';          
                                                                                                                                                                                              
                     if ($item->ref_from > 0) {                                                                                                                                               
                         // Logic to get referenced item number               
                         $referencedItemNo = Item::find($item->ref_from)->ItemNo ?? ''                                                                                                       
                         $rate = "As per item no " . $referencedItemNo       
                         $unit = '';                                          
                     }                                                    
                                                                                                                                                                                              
                     return [                                           
                        $sno,                               
                        $item->item_no,                                       
                        $item->description,                                  
                        $rate,                                           
                        $unit,                                            
                   ];                                                                                                                                                                       
               }                                                                                                                                                                            
                                                                                                                                                                                            
               // 4. Applies styling to the sheet                                                                                                                                           
               public function styles(Worksheet $sheet)                                                                                                                                     
               {                                              
                   $sheet->getStyle('A1:E1')->getFont()->setBold(true)                                                                
                                                                                                                                                                                            
                   // Loop through rows to apply depth-based stylin 
                   // This is a simplified example.                                                                                                                                         
                   foreach ($sheet->getRowIterator() as $row)                                                                                                                               
                       if ($row->getRowIndex() <= 1) continue; // Skip heade                                                          
                                                                                                                                                                                           █
                       $itemCode = $sheet->getCell('B' . $row->getRowIndex())->getValue();                                
                         // You would need a way to get the item's depth    
                         // This could be done by adding a 'depth' property in the collection query               
                         // For example, if( $item->depth == 0) { ... set bold ...                                                                                                            
                     }                                    

      

      The controller's job is simply to prepare the dependencies and trigger the download.                                                                                                        
                                                                                                                                                                                                    
        File: app/Http/Controllers/SorExportController.php              
                                                                                                                                                                                                    
          use Maatwebsite\Excel\Facades\Excel;                    
          use App\Exports\SorExport;                           
                                                                                                                                                                                                  
          class SorExportController extends Controller                                                                                                                                                  
          {                                                   
               public function export(Sor $sor, RateCard $rateCard,$format = 'xlsx') 
               {                                                 
                    $fileName = "SOR_{$sor->sorname}_{$rateCard->ratecardid}.{$format}"; 
                     // Determine the writer type based on the format requested in the URL
                      $writerType = match(strtolower($format)) {     
                          'pdf' => ExcelWriter::DOMPDF,           
                          'csv' => ExcelWriter::CSV,              
                          default => ExcelWriter::XLSX,            
                      };                    
                   // Trigger the download  or option to save in aws repo and then save url in File Model                 
                     return Excel::download(new SorExport($sor, $rateCard), $fileName, $writerType);
                }
          }
      

      //Example route: /export/sor/{sor}/rate-card/{rateCard}/format/{format}                                        
      Route::get('/export/sor/{sor}/rate-card/{rateCard}/format/{format?}', [SorExportController::class, 'export'])->name('sor.export');                                                                
