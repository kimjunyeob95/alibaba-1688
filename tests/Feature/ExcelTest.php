<?php

namespace Tests\Feature;

use Exception;
use Tests\TestCase;
use Illuminate\Support\Facades\File;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ExcelTest extends TestCase
{
    # php artisan test --filter testExcelImg
    public function testExcelImg()
    {
        ini_set('memory_limit', '-1'); // 메모리 제한 해제

        $filePath    = storage_path('excel/test_214161239.xlsx');
        $excelPath   = storage_path('excel');
        $storagePath = storage_path('excel/img');

        if (!File::exists($excelPath)) {
            File::makeDirectory($excelPath, 0755, true);
        }

        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        // 엑셀 파일 로드
        $spreadsheet = IOFactory::load($filePath);
        $sheet       = $spreadsheet->getActiveSheet();

        // 엑셀의 모든 행에 대해 반복 (2행부터 시작)
        $highestRow = $sheet->getHighestRow();
        $startRow   = 2001;
        $maxRow     = 2002;
        $imagePaths = [];

        for ($row = $startRow; $row <= $maxRow; $row++) {
            // 특정 셀의 이미지 URL 읽기 (예: A열)
            $imageUrlCell = 'B' . $row;
            $imageUrl = $sheet->getCell($imageUrlCell)->getValue();

            try {
                // 이미지가 있는지 확인
                if (!empty($imageUrl)) {
                    $imageContent = fileContents($imageUrl);
                    $imagePath    = storage_path('excel/img/temp_image_' . $row . '.jpg');
                    file_put_contents($imagePath, $imageContent);
    
                    // 기존 텍스트 삭제
                    $sheet->setCellValue($imageUrlCell, '');
    
                    // 이미지 경로 저장
                    $imagePaths[] = $imagePath;

                    // 이미지 삽입 (예: B열)
                    $this->insertImageIntoCell($sheet, $imageUrlCell, $imagePath);
                }
            } catch (Exception $e) {
                print($e->getMessage() . " | " . $imageUrlCell. "\r\n");
            }

        }

        // 수정된 엑셀 파일 저장
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save(storage_path('excel/modified_excel_file_' . $startRow . '_to_' . $maxRow . '.xlsx'));

        // 임시 이미지 파일 삭제
        foreach ($imagePaths as $path) {
            if (file_exists($path)) {
                unlink($path);
            }
        }
        dd("끝");
    }

    private function insertImageIntoCell(Worksheet $sheet, $cell, $imagePath)
    {
        $drawing = new Drawing();
        $drawing->setName('Image');
        $drawing->setDescription('Image');
        $drawing->setPath($imagePath); // 파일 경로 설정
        $drawing->setHeight(150);
        $drawing->setWidth(150);
        $drawing->setCoordinates($cell);
        $drawing->setWorksheet($sheet);
    }

}
