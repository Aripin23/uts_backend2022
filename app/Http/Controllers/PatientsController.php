<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Models\Patients;
use Illuminate\Support\Facades\Validator;

class PatientsController extends Controller
{
    public function index(Request $request)
    {
        //model
        $patients = new Patients();
        // ambil semua data
        $data = $patients::all();
        // validasi data
        if ($data == null) {
            $response = [
                'meta' => [
                    'code' => '200',
                    'message' => 'Dat is empty'
                ]
            ];

            return response()->json($response, 200);
        }
        // respons
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Get All Resource'
            ],
            'data' => $data
        ];
        return response()->json($response, 200);
    }

    public function show(Request $request, $id)
    {
        //model
        $patients = new Patients();
        // ambil data sesuai id
        $data = $patients::where('id', $id)->first();
        // fail respinse
        if ($data == null) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'Resource not found'
                ]
            ];

            return response()->json($response, 404);
        }
        // sukses respon
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Get Detail Resource'
            ],
            'data' => $data
        ];

        return response()->json($response, 200);
    }

    public function store(Request $request)
    {
        // model
        $patients = new Patients();
        // validasi  parameter
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone' => 'required|numeric',
            'address' => 'required',
            'status' => 'required',
            'in_date' => 'required|date_format:Y-m-d',
            'out_date' => 'required|date_format:Y-m-d'
        ]);
        
        if ($validator->fails()) {
            $response = [
                'meta' => [
                    'message' => $validator->errors()
                ]
            ];

            return response()->json($response);
        }
        // cek data dengan nama
        $check = $patients::where('name', $request->name)->first();
        if ($check != null) {
            $response = [
                'meta' => [
                    'message' => 'Data already exists'
                ]
            ];

            return response()->json($response);
        }
        // format dimpan di database
        $patients->name = $request->name;
        $patients->phone = $request->phone;
        $patients->address = $request->address;
        $patients->status = $request->status;
        $patients->in_date_at = $request->in_date;
        $patients->out_date_at = $request->out_date;
        $patients->created_at = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $patients->save();

        $response = [
            'meta' => [
                'code' => '201',
                'message' => 'Resource is added successfully'
            ],
            'data' => $patients
        ];

        return response()->json($response, 201);
        
    }

    public function update(Request $request, $id)
    {
        // model
        $patients = new Patients();
        // validasi parameter
        if ($request->phone != null || $request->in_date != null || $request->out_date != null) {
            $validator = Validator::make($request->all(), [
                'phone' => 'numeric',
                'in_date_at' => 'date_format:Y-m-d',
                'out_date_at' => 'date_format:Y-m-d'
            ]);
    
            if ($validator->fails()) {
                $response = [
                    'meta' => [
                        'message' => $validator->errors()
                    ]
                ];
    
                return response()->json($response);
            }
        }

        // update data
        $doChange = $patients::where('id', $id)->first();
        if ($doChange == null) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' =>'Resource not found'
                ]
            ];

            return response()->json($response, 404);
        }

        $doChange->name = $request->name == null ? $doChange->name : $request->name;
        $doChange->phone = $request->phone == null ? $doChange->phone : $request->phone;
        $doChange->address = $request->address == null ? $doChange->address : $request->address;
        $doChange->status = $request->status == null ? $doChange->status : $request->status;
        $doChange->in_date_at = $request->in_date == null ? $doChange->in_date_at : $request->in_date;
        $doChange->out_date_at = $request->out_date == null ? $doChange->out_date_at : $request->out_date;
        $doChange->updated_at = Carbon::now('Asia/Jakarta')->format('Y-m-d H:i:s');
        $doChange->update();

        
        // sukses respon
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Resource is update successfully'
            ],
            'data' => $doChange
        ];
        return response()->json($response, 200);
    }

    public function destroy(Request $request, $id)
    {
        // model
        $patients = new Patients();

        $destroy = $patients::where('id', $id)->first();
        if ($destroy == null) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'Resource not found'
                ]
            ];

            return response()->json($response, 404);
        }
        
        $destroy->delete();
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Resource is delete successfully'
            ]
        ];

        return response()->json($response, 200);
    }

    public function search(Request $request, $name)
    {
        // model
        $patients = new Patients();
        // cari data dengan nama
        $search = $patients::where('name', 'like', '%' . $name . '%')->get();
        // validation to data not found
        if (count($search) == 0) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'Resource not found'
                ]
            ];
            return response()->json($response, 404);
        } 

        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Get searched resource'
            ],
            'data' => $search
        ];
        
        return response()->json($response, 200);
    }

    public function positive(Request $request)
    {
        // model
        $patients = new Patients();
        // ambil data yang positive
        $data = $patients::where('status', 'positive')->get();
        if (count($data) == 0) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'Resource not found'
                ]
            ];

            return response()->json($response, 404);
        }
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Get positive resource'
            ],
            'total' => count($data),
            'data' => $data
        ];

        return response()->json($response, 200);
    }

    public function recovered(Request $request)
    {
        // model
        $patients = new Patients();
        // ambil data yang sembuh
        $data = $patients::where('status', 'recovered')->get();

        if (count($data) == 0) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'Resource not found'
                ]
            ];

            return response()->json($response, 404);
        }

        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Get recovered resource'
            ],
            'total' => count($data),
            'data' => $data
        ];

        return response()->json($response, 200);
    }

    public function dead(Request $request)
    {
        // model
        $patients = new Patients();
        // ambil data yang meninggal
        $data = $patients::where('status', 'dead')->get();

        if (count($data) == 0) {
            $response = [
                'meta' => [
                    'code' => '404',
                    'message' => 'Resource not found'
                ]
            ];

            return response()->json($response, 404);
        }
        // respon sukses
        $response = [
            'meta' => [
                'code' => '200',
                'message' => 'Get dead resource'
            ],
            'total' => count($data),
            'data' => $data
        ];

        return response()->json($response, 200);
    }

}
