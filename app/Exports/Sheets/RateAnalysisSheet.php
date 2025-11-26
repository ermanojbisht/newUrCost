<?php

namespace App\Exports\Sheets;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\NamedRange;

class RateAnalysisSheet implements FromView, WithTitle, WithEvents
{
    protected $items;
    protected $rateCard;
    protected $date;

    public function __construct($items, $rateCard, $date)
    {
        $this->items = $items;
        $this->rateCard = $rateCard;
        $this->date = $date;
    }

    public function view(): View
    {
        return view('exports.rate_analysis', [
            'items' => $this->items,
            'rateCard' => $this->rateCard,
            'date' => $this->date
        ]);
    }

    public function title(): string
    {
        return 'Rate Analysis';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // We need to calculate where each item's final rate cell ends up.
                // Since the blade view renders them sequentially, we can estimate or 
                // we might need a more robust way to know the exact cell.
                // For now, let's assume a fixed structure per item in the view 
                // or use a counter if the view structure is predictable.
                
                // A better approach with Blade is to add a hidden column or metadata 
                // but Excel export doesn't easily support that.
                
                // Alternatively, we can search for the Item Code in the sheet 
                // and find the rate cell relative to it.
                
                // Given the complexity of dynamic height (variable resources/subitems),
                // we might need to rely on the view to output a "map" or 
                // we can iterate the rows in PHP if we knew the exact count of rows per item.
                
                // Let's try a simpler approach: 
                // The view will render a table for each item.
                // We can't easily know the row number here without pre-calculating it.
                
                // Strategy:
                // We will calculate the row positions here in PHP before rendering?
                // No, that duplicates view logic.
                
                // Alternative Strategy:
                // In the view, we can output the Item Code in a specific column (e.g., A)
                // and the Rate in another (e.g., F).
                // Then we can iterate the rows to find them.
                
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();
                
                // Iterate rows to find Item Headers or specific markers
                // This is a bit "hacky" but works for dynamic content.
                for ($row = 1; $row <= $highestRow; $row++) {
                    $cellValue = $sheet->getCell('A' . $row)->getValue();
                    
                    // Assuming we put "Item Code: {code}" in Column A
                    if (strpos($cellValue, 'Item Code:') !== false) {
                        $itemCode = trim(str_replace('Item Code:', '', $cellValue));
                        
                        // Assuming the Rate is in the same row, Column F (6th column)
                        // Or maybe in a "Total" row below?
                        // Let's look at the view structure we will build.
                        // If we put the Final Rate in a specific relative position to the header.
                        
                        // Let's refine the View first to make this easier.
                        // We can put a hidden "marker" or just use the Item Code.
                        
                        // Let's assume the Final Rate is in the row where Column E says "Rate per Unit"
                        // and the value is in Column F.
                        // And we need to know WHICH item this belongs to.
                        // We can track the "current item" as we scan down.
                    }
                }
                
                // Actually, the helper doc suggested:
                // "$finalRateCell = $item['analysis_data']['final_rate_cell'];"
                // This implies pre-calculation.
                
                // Let's implement a pre-calculation helper in the Service or here.
                // We can calculate the number of rows each item will take.
                // Header (1) + Skeletons (count) + Subitems (count) + Overheads (count) + Footer (1) + Spacing (1).
                
                $currentRow = 1;
                foreach ($this->items as $item) {
                    // Header row
                    $currentRow++; 
                    
                    // Resources
                    $currentRow += $item->skeletons->count();
                    
                    // Subitems
                    $currentRow += $item->subitems->count();
                    
                    // Overheads
                    $currentRow += $item->overheads->count();
                    
                    // Footer row (Rate per Unit)
                    // This is where the rate is.
                    $rateCell = 'F' . $currentRow; 
                    
                    // Create Named Range
                    // Sanitize item code for named range (remove spaces, special chars)
                    $safeCode = preg_replace('/[^a-zA-Z0-9_]/', '_', $item->item_code);
                    $namedRangeName = 'ra_' . $safeCode;
                    
                    $event->sheet->getParent()->addNamedRange(
                        new NamedRange($namedRangeName, $event->sheet->getDelegate(), $rateCell)
                    );
                    
                    $currentRow++; // For the footer row itself
                    $currentRow++; // Spacing
                }
            }
        ];
    }
}
