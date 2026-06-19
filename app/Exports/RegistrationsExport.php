<?php

namespace App\Exports;

use App\Models\EventRegistration;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Cell\Hyperlink;
use Illuminate\Http\Request;

class RegistrationsExport implements WithMultipleSheets
{


    public function __construct(protected Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);

        $this->serial = (($page - 1) * $perPage) + 1;
    }
    public function sheets(): array
    {
        return [
            new RegistrationsSheet($this->request),
            new SummarySheet($this->request),
        ];
    }
}



class RegistrationsSheet implements
    FromQuery, WithHeadings, WithMapping, WithColumnWidths, WithTitle, WithEvents
{
    protected int $rowCount = 0;
    protected int $serial = 1;
    public function __construct(protected Request $request) {}

    public function title(): string { return 'Registrations'; }

    public function query()
    {
        $q = EventRegistration::query()->latest();

        if ($this->request->filled('city')) {
            $q->where('city', $this->request->city);
        }
        if ($this->request->filled('date_from')) {
            $q->whereDate('created_at', '>=', $this->request->date_from);
        }
        if ($this->request->filled('date_to')) {
            $q->whereDate('created_at', '<=', $this->request->date_to);
        }

        $this->rowCount = (clone $q)->count();
        return $q;
    }

    public function headings(): array
    {
        return [
            'SR No',
            'Full Name',
            'City',
            'Gender',
            'Event Date',
            'Banner URL (Full Link)',
            'Registered At',
        ];
    }

    public function map($reg): array
    {
        $bannerUrl = $reg->generated_banner
            ? Storage::disk('s3')->url($reg->generated_banner)
            : '';

        return [
            $this->serial++,
            $reg->full_name,
            $reg->city,
            $reg->gender,
            $reg->event_date
                ? \Carbon\Carbon::parse($reg->event_date)->format('d M Y')
                : '',
            $bannerUrl,
            $reg->created_at->timezone('Asia/Kolkata')->format('d M Y, h:i A'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 26,
            'C' => 16,
            'D' => 30,
            'E' => 16,
            'F' => 14,
            'G' => 13,
            'H' => 60,
            'I' => 22,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet   = $event->sheet->getDelegate();
                $lastRow = $this->rowCount + 2; // 1 header + data rows

                // ── Header row bold ───────────────────────────────────────────
                $sheet->getStyle('A1:I1')->applyFromArray([
                    'font'      => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER],
                    'borders'   => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM]],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(20);

                for ($row = 2; $row <= $lastRow; $row++) {
                    $sheet->getRowDimension($row)->setRowHeight(18);

                    $sheet->getStyle("A{$row}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("G{$row}")
                        ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $urlCell = $sheet->getCell("H{$row}");
                    $url = $urlCell->getValue();
                    if ($url) {
                        $urlCell->setHyperlink(new Hyperlink($url, $url));
                    }
                }

                if ($lastRow >= 2) {
                    $sheet->getStyle("A1:I{$lastRow}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                    ]);
                }

                $sheet->freezePane('A2');

                $sheet->setAutoFilter("A1:I{$lastRow}");
            },
        ];
    }
}



class SummarySheet implements
    \Maatwebsite\Excel\Concerns\WithTitle,
    \Maatwebsite\Excel\Concerns\WithEvents,
    \Maatwebsite\Excel\Concerns\FromArray
{
    public function __construct(protected Request $request) {}

    public function title(): string { return 'Summary'; }

    public function array(): array { return []; }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $sheet->setCellValue('A1', 'Metric');
                $sheet->setCellValue('B1', 'Count');

                $rows = [
                    ['Total Registrations',   '=COUNTA(Registrations!B2:B99999)'],
                    ['With Banner Generated', '=COUNTIF(Registrations!G2:G99999,"Yes")'],
                    ['Without Banner',        '=COUNTIF(Registrations!G2:G99999,"No")'],
                    ['Export Generated On',   now()->format('d M Y, h:i A')],
                ];

                foreach ($rows as $i => [$label, $value]) {
                    $row = $i + 2;
                    $sheet->setCellValue("A{$row}", $label);
                    $sheet->setCellValue("B{$row}", $value);
                    $sheet->getRowDimension($row)->setRowHeight(18);
                }

                // Header bold + border
                $sheet->getStyle('A1:B1')->applyFromArray([
                    'font'    => ['bold' => true],
                    'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM]],
                ]);

                // All cells thin border
                $sheet->getStyle('A1:B' . (count($rows) + 1))->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
                ]);

                $sheet->getColumnDimension('A')->setWidth(28);
                $sheet->getColumnDimension('B')->setWidth(22);
                $sheet->getRowDimension(1)->setRowHeight(20);
            },
        ];
    }
}
