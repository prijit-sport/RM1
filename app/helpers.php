<?php

use App\Support\EnumLabel;

if (!function_exists('enum_th')) {
    function enum_th(string $group, $value, ?string $fallback = null): string
    {
        return EnumLabel::th($group, $value !== null ? (string) $value : null, $fallback);
    }
}

if (!function_exists('enum_bi')) {
    function enum_bi(string $group, $value, ?string $fallback = null): string
    {
        return EnumLabel::bi($group, $value !== null ? (string) $value : null, $fallback);
    }
}

if (!function_exists('csv_sanitize_text')) {
    function csv_sanitize_text($value): string
    {
        $text = (string) $value;
        $text = str_replace(["\0", "\u{FFFD}"], '', $text);
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }
}

if (!function_exists('xlsx_download')) {
    function xlsx_download(string $filename, array $rows)
    {
        $safeRows = array_map(static function ($row): array {
            return array_map(static function ($value): string {
                return csv_sanitize_text($value);
            }, is_array($row) ? $row : [$row]);
        }, $rows);

        $sheetXml = build_xlsx_sheet_xml($safeRows);
        $contentTypesXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">
    <Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>
    <Default Extension="xml" ContentType="application/xml"/>
    <Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/>
    <Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/>
    <Override PartName="/docProps/core.xml" ContentType="application/vnd.openxmlformats-package.core-properties+xml"/>
    <Override PartName="/docProps/app.xml" ContentType="application/vnd.openxmlformats-officedocument.extended-properties+xml"/>
</Types>
XML;
        $relsXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="xl/workbook.xml"/>
    <Relationship Id="rId2" Type="http://schemas.openxmlformats.org/package/2006/relationships/metadata/core-properties" Target="docProps/core.xml"/>
    <Relationship Id="rId3" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/extended-properties" Target="docProps/app.xml"/>
</Relationships>
XML;
        $workbookXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships">
    <sheets>
        <sheet name="Sheet1" sheetId="1" r:id="rId1"/>
    </sheets>
</workbook>
XML;
        $workbookRelsXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">
    <Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet" Target="worksheets/sheet1.xml"/>
</Relationships>
XML;
        $coreXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<cp:coreProperties xmlns:cp="http://schemas.openxmlformats.org/package/2006/metadata/core-properties" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:dcterms="http://purl.org/dc/terms/" xmlns:dcmitype="http://purl.org/dc/dcmitype/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
    <dc:creator>Rm1</dc:creator>
    <cp:lastModifiedBy>Rm1</cp:lastModifiedBy>
    <dcterms:created xsi:type="dcterms:W3CDTF">2026-01-01T00:00:00Z</dcterms:created>
    <dcterms:modified xsi:type="dcterms:W3CDTF">2026-01-01T00:00:00Z</dcterms:modified>
</cp:coreProperties>
XML;
        $appXml = <<<'XML'
<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<Properties xmlns="http://schemas.openxmlformats.org/officeDocument/2006/extended-properties" xmlns:vt="http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes">
    <Application>Rm1</Application>
</Properties>
XML;

        $tempPath = tempnam(sys_get_temp_dir(), 'xlsx_');
        if ($tempPath === false) {
            abort(500, 'ไม่สามารถสร้างไฟล์ชั่วคราวได้');
        }

        if (!class_exists('ZipArchive')) {
            @unlink($tempPath);
            abort(500, 'ZipArchive class not found. PHP zip extension may be missing/enabled incorrectly.');
        }

        $zip = new \ZipArchive();
        if ($zip->open($tempPath, \ZipArchive::OVERWRITE) !== true) {
            @unlink($tempPath);
            abort(500, 'ไม่สามารถสร้างไฟล์ Export ได้');
        }

        $zip->addFromString('[Content_Types].xml', $contentTypesXml);
        $zip->addFromString('_rels/.rels', $relsXml);
        $zip->addFromString('xl/workbook.xml', $workbookXml);
        $zip->addFromString('xl/_rels/workbook.xml.rels', $workbookRelsXml);
        $zip->addFromString('xl/worksheets/sheet1.xml', $sheetXml);
        $zip->addFromString('docProps/core.xml', $coreXml);
        $zip->addFromString('docProps/app.xml', $appXml);
        $zip->close();

        $downloadName = preg_replace('/\.xlsx$/i', '', $filename) . '.xlsx';

        return response()->download(
            $tempPath,
            $downloadName,
            ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']
        )->deleteFileAfterSend(true);
    }
}

if (!function_exists('build_xlsx_sheet_xml')) {
    function build_xlsx_sheet_xml(array $rows): string
    {
        $lines = [];
        $lines[] = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $lines[] = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">';
        $lines[] = '<sheetData>';

        foreach ($rows as $rowIndex => $row) {
            $rowNumber = $rowIndex + 1;
            $lines[] = '<row r="' . $rowNumber . '">';

            foreach (array_values($row) as $colIndex => $value) {
                $cellRef = xlsx_cell_ref($colIndex + 1, $rowNumber);
                $escaped = htmlspecialchars((string) $value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
                $lines[] = '<c r="' . $cellRef . '" t="inlineStr"><is><t>' . $escaped . '</t></is></c>';
            }

            $lines[] = '</row>';
        }

        $lines[] = '</sheetData>';
        $lines[] = '</worksheet>';

        return implode('', $lines);
    }
}

if (!function_exists('csv_stream_download')) {
    function csv_stream_download(string $filename, array $headers, callable $rowProducer)
    {
        $downloadName = preg_replace('/\.csv$/i', '', $filename) . '.csv';

        return response()->streamDownload(function () use ($headers, $rowProducer) {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }

            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $headers);

            $rowProducer(static function (array $row) use ($out): void {
                fputcsv($out, array_map(static fn ($value) => csv_sanitize_text($value), $row));
            });

            fclose($out);
        }, $downloadName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}

if (!function_exists('csv_download')) {
    function csv_download(string $filename, array $rows)
    {
        $safeRows = array_map(static function ($row): array {
            return array_map(static function ($value): string {
                return csv_sanitize_text($value);
            }, is_array($row) ? $row : [$row]);
        }, $rows);

        $downloadName = preg_replace('/\.csv$/i', '', $filename) . '.csv';

        return response()->streamDownload(function () use ($safeRows): void {
            $out = fopen('php://output', 'w');
            if ($out === false) {
                return;
            }

            fwrite($out, "\xEF\xBB\xBF");

            foreach ($safeRows as $row) {
                fputcsv($out, $row);
            }

            fclose($out);
        }, $downloadName, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}

if (!function_exists('xlsx_cell_ref')) {
    function xlsx_cell_ref(int $column, int $row): string
    {
        $letters = '';
        while ($column > 0) {
            $remainder = ($column - 1) % 26;
            $letters = chr(65 + $remainder) . $letters;
            $column = (int) floor(($column - 1) / 26);
        }

        return $letters . $row;
    }
}
