<?php

namespace Tests\Feature;

use App\Models\HsCodeData;
use Exception;
use Tests\TestCase;
use Illuminate\Support\Facades\File;

class KoreaCustomTest extends TestCase
{
    # php artisan test --filter testHsCodeUpsert
    public function testHsCodeUpsert()
    {
        $filePath = public_path('app/hs_code_datas.txt');
        if (!File::exists($filePath)) {
            throw new Exception("파일이 존재하지 않습니다.");
        }

        $lines = File::lines($filePath);
        foreach($lines as $key => $line){
            if( $key == 0 ) continue;

            $data               = explode('||', $line);
            $hs_code            = $data[0];
            $apply_start_date   = $data[1];
            $apply_end_date     = $data[2];
            $ko_name            = $data[3];
            $en_name            = $data[4];
            $ko_trade_name      = $data[5];
            $max_unit           = $data[6];
            $max_weight         = $data[7];
            $unit_code          = $data[8];
            $weight_code        = $data[9];
            $export_code        = $data[10];
            $import_code        = $data[11];
            $item_spec_name     = $data[12];
            $required_spec_name = $data[13];
            $add_spec_name      = $data[14];
            $spec_name          = $data[15];
            $spen_memo          = $data[16];
            $property_code      = $data[17];
            $property_code_name = $data[18];

            HsCodeData::updateOrCreate([
                "hs_code" => $hs_code,
            ],[
                'apply_start_date'   => $apply_start_date,
                'apply_end_date'     => $apply_end_date,
                'ko_name'            => $ko_name,
                'en_name'            => $en_name,
                'ko_trade_name'      => $ko_trade_name,
                'max_unit'           => $max_unit,
                'max_weight'         => $max_weight,
                'unit_code'          => $unit_code,
                'weight_code'        => $weight_code,
                'export_code'        => $export_code,
                'import_code'        => $import_code,
                'item_spec_name'     => $item_spec_name,
                'required_spec_name' => $required_spec_name,
                'add_spec_name'      => $add_spec_name,
                'spec_name'          => $spec_name,
                'spen_memo'          => $spen_memo,
                'property_code'      => $property_code,
                'property_code_name' => $property_code_name,
            ]);
        }

        dd("끝");
    }
}
