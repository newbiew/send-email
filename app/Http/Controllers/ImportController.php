<?php

namespace App\Http\Controllers;

use App\Contact;
use App\CsvData;
use App\Http\Requests\CsvImportRequest;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Mail\SendVoucherEmail;
use Illuminate\Support\Facades\Mail;

class ImportController extends Controller
{

    public function getImport()
    {
        return view('import');
    }

    public function parseImport(CsvImportRequest $request)
    {

        $path = $request->file('csv_file')->getRealPath();

        // if ($request->has('header')) {
        //     $file = $path;
        //     $row = 1;
        //     $array = [];
        //         if (($handle = fopen($file, "r")) !== FALSE) {
        //           while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        //             $array['fullname'] =  $data[0];
        //             $array['email'] =  $data[1];
        //             $array['code'] =  $data[2];
                       
        //              $data[] =  $array;
        //           }
        //           fclose($handle);
        //         }
    
        //         // dd($array2);

        //     // $data = Excel::load($path, function($reader) {})->get()->toArray();
        // } else {
            $data = array_map('str_getcsv', file($path));
        // }

        if (count($data) > 0) {
            // if ($request->has('header')) {
            //     $csv_header_fields = [];
            //     foreach ($data[0] as $key => $value) {
            //         $csv_header_fields[] = $key;
            //     }
            // }
            
            // $voucher = \App\Voucher::select('code')->get()->toArray();

            // dd($data[0]['email']);
            
            // foreach($data as $key => $value){
            //     if(count($voucher) > 0){
            //         $data[$key]['code'] = isset($voucher[$key]) ?  $voucher[$key]['code'] : "N/A";
            //     }else{
            //         $data[$key]['code'] = "N/A";
            //     }
            // }

            // $csv_data = $data;


            $csv_data = array_slice($data, 0, 2);

            $csv_data_file = CsvData::create([
                'csv_filename' => $request->file('csv_file')->getClientOriginalName(),
                'csv_header' => $request->has('header'),
                'csv_data' => json_encode($data)
            ]);
        } else {
            return redirect()->back();
        }

        $csv_header_fields[0] = 'fullname';
        $csv_header_fields[1] = 'email';
        $csv_header_fields[2] = 'code';

        return view('import_fields', compact( 'csv_header_fields', 'csv_data', 'csv_data_file'));

    }

    public function processImport(Request $request)
    {
        $data = CsvData::find($request->csv_data_file_id);


        $csv_data = json_decode($data->csv_data, true);
        foreach ($csv_data as $row) {
            $contact = new Contact();

            $contact->email = $row[1];
            $contact->fullname = $row[0];
            $contact->code = $row[2];
            $contact->save();

            dispatch(new \App\Jobs\SendEmail($contact));
            // Mail::to($row['email'])->queue(new SendVoucherEmail($contact));
            dd("done");
        }

        return view('import_success');
    }

}
