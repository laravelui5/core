<?php

namespace LaravelUi5\Core\Ui5\Contracts;

use Maatwebsite\Excel\Concerns\FromGenerator;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * Marker Interface for Reports that also generate Excel Downloads.
 */
interface ExportInterface extends FromGenerator, WithHeadings, WithMapping
{
    /**
     * Optional: Returns the name of the exported Excel/CSV file.
     *
     * Example: 'Jahresabgrenzung_2025.xlsx'
     *
     * @return string|null the name of the PDF when downloaded by clients
     */
    public function getExportName(): ?string;

}
