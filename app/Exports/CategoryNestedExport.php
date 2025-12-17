<?php

namespace App\Exports;

use App\Models\Category;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class CategoryNestedExport implements FromView, ShouldAutoSize, WithHeadings
{
    public function view(): \Illuminate\Contracts\View\View
    {
        $data['categories'] = Category::query()->with([
            'subCategories',
            'subCategories.subCategories',
            'topics',
            'exam_section',
        ])->get();

//        return $data['categories'];
        return view('category.export_view', $data);
    }

    public function headings(): array
    {
        return [
            'Category id',
            'Category name',
            'Sub Category id',
            'Sub Category name',
        ];
    }

//    public function registerEvents(): array
//    {
//        return [
//            AfterSheet::class => function (AfterSheet $event) {
//                $last_column = $event->sheet->getHighestColumn();
//                $event->sheet->getStyle("A1:" . $last_column . "1")->applyFromArray([
//                    'font' => [
//                        'bold' => true,
//                        'size' => 18,
//                    ],
//                    'fill' => [
//                        'fillType' => Fill::FILL_SOLID,
//                        'startColor' => [
//                            'argb' => 'bfefed',
//                        ],
//                    ],
//                ]);
//
//                // Apply conditional formatting based on specific conditions
//                foreach ($event->sheet->getRowIterator() as $row) {
//
//                    $event->sheet->getStyle("A" . $row->getRowIndex() . ":$last_column" . $row->getRowIndex())->applyFromArray([
//                        'borders' => [
//                            'allBorders' => [
//                                'borderStyle' => Border::BORDER_THIN,
//                            ],
//                        ],
//                        'font' => [
//                            'size' => 14,
//                        ],
//                        'alignment' => [
//                            'horizontal' => Alignment::HORIZONTAL_CENTER,
//                            'vertical' => Alignment::VERTICAL_CENTER,
//                        ],
//
//                    ]);
////
////                    $event->sheet->getStyle("A" . $row->getRowIndex())->applyFromArray([
////                        'fill' => [
////                            'fillType' => Fill::FILL_SOLID,
////                            'startColor' => [
////                                'argb' => 'bfefed',
////                            ],
////                        ],
////                    ]);
//
////                    if ($row->getRowIndex() > 1) {
////                        $event->sheet->getStyle("$last_column" . $row->getRowIndex())->applyFromArray([
////                            'fill' => [
////                                'fillType' => Fill::FILL_SOLID,
////                                'startColor' => [
////                                    'argb' => 'ebedf3',
////                                ],
////                            ],
////                        ]);
////                    }
//
//                }
//            },
//        ];
//    }

}
