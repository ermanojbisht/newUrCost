<?php

namespace App\Exports;

use App\Models\Sor;
use App\Models\Item;
use App\Models\RateCard;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class SorExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $sor;
    protected $rateCard;

    public function __construct(Sor $sor, RateCard $rateCard)
    {
        $this->sor = $sor;
        $this->rateCard = $rateCard;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // Fetch items for the SOR, eager loading the specific rate for the given rate card
        // Ordering by 'lft' (nested set model) to maintain hierarchy
        return Item::where('sor_id', $this->sor->id)
            ->with(['itemRates' => function($q) {
                $q->where('rate_card_id', $this->rateCard->id);
            }, 'unit', 'parent'])
            ->orderBy('lft')
            ->get();
    }

    public function headings(): array
    {
        return ['S.No', 'Chapter/Item No', 'Particulars of Item', 'Rate', 'Unit'];
    }

    public function map($item): array
    {
        static $sno = 0;
        $sno++;

        // Get the rate for the specific rate card
        // Since we eager loaded 'itemRates' with a filter, we can grab the first one
        $rateRecord = $item->itemRates->first();
        
        $rate = $rateRecord ? $rateRecord->rate : '';
        $unit = $item->unit ? $item->unit->unit_name : '';

        // Handle reference items
        if ($item->reference_from > 0) {
            $referencedItem = Item::find($item->reference_from);
            $referencedItemNo = $referencedItem ? $referencedItem->item_number : 'Unknown';
            $rate = "As per item no " . $referencedItemNo;
            $unit = ''; // Unit is usually empty for reference items in the report
        }

        return [
            $sno,
            $item->item_number,
            $item->description,
            $rate,
            $unit,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // S.No
            'B' => 15,  // Item No
            'C' => 60,  // Description
            'D' => 15,  // Rate
            'E' => 10,  // Unit
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header Style
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);
        $sheet->getStyle('A1:E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1:E1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFE0E0E0');

        // Iterate through rows to apply depth-based styling
        // Row 1 is header, data starts at Row 2
        $rowIterator = $sheet->getRowIterator();
        
        // We need to access the collection again or keep track of it to know the depth
        // Since map() doesn't pass the index easily in a way that syncs with rows perfectly if we filter,
        // but here we are not filtering in map, so row index corresponds to collection index + 2.
        
        $items = $this->collection();
        $rowIndex = 2;

        foreach ($items as $item) {
            $rowDimension = $sheet->getRowDimension($rowIndex);
            
            // Apply styles based on item type or depth
            // Assuming item_type 1 = Chapter, 2 = Sub-chapter, 3 = Item
            // Or using depth from nested set
            
            if ($item->item_type == 1) { // Chapter
                $sheet->getStyle("A$rowIndex:E$rowIndex")->getFont()->setBold(true)->setSize(14)->getColor()->setARGB('FF0000FF'); // Blue
            } elseif ($item->item_type == 2) { // Sub-chapter
                $sheet->getStyle("A$rowIndex:E$rowIndex")->getFont()->setBold(true)->setSize(12)->getColor()->setARGB('FF008000'); // Green
            }
            
            // Wrap text for description
            $sheet->getStyle("C$rowIndex")->getAlignment()->setWrapText(true);
            
            // Vertical alignment center
            $sheet->getStyle("A$rowIndex:E$rowIndex")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);

            $rowIndex++;
        }
        
        return [];
    }
}
