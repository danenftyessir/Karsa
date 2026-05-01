<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentRegisterRequest;
use App\Http\Requests\InstitutionRegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * ValidationController
 * 
 * handle validasi step by step untuk form registrasi
 */
class ValidationController extends Controller
{
    /**
     * validasi step registrasi student
     */
    public function validateStudentStep(Request $request)
    {
        $step = $request->input('step');

        // buat instance dari form request hanya untuk mendapatkan aturannya
        $formRequest = new StudentRegisterRequest();
        $allRules = $formRequest->rules();
        $allMessages = $formRequest->messages();
        
        // tentukan field mana yang akan divalidasi untuk setiap langkah
        $rulesForStep = [];
        if ($step == 1) {
            $fields = ['first_name', 'last_name', 'email', 'whatsapp_number', 'profile_photo'];
        } elseif ($step == 2) {
            $fields = ['university_id', 'major', 'nim', 'semester'];
        } elseif ($step == 3) {
            $fields = ['username', 'password', 'password_confirmation'];
        } else {
            return response()->json(['message' => 'Langkah tidak valid.'], 400);
        }

        // filter aturan validasi hanya untuk field di langkah saat ini
        foreach ($fields as $field) {
            if (isset($allRules[$field])) {
                $rulesForStep[$field] = $allRules[$field];
            }
        }
        
        // lakukan validasi secara manual menggunakan validator facade
        $validator = Validator::make($request->all(), $rulesForStep, $allMessages);

        if ($validator->fails()) {
            // jika validasi gagal, kembalikan error dalam format json
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // jika validasi berhasil
        return response()->json(['message' => 'Validasi berhasil.'], 200);
    }

    /**
     * validasi step registrasi institution
     */
    public function validateInstitutionStep(Request $request)
    {
        $step = $request->input('step');

        // buat instance dari form request
        $formRequest = new InstitutionRegisterRequest();
        $allRules = $formRequest->rules();
        $allMessages = $formRequest->messages();
        
        // tentukan field untuk setiap step
        $rulesForStep = [];
        if ($step == 1) {
            $fields = ['institution_name', 'institution_type', 'official_email'];
        } elseif ($step == 2) {
            $fields = ['address', 'province_id', 'regency_id'];
        } elseif ($step == 3) {
            $fields = ['pic_name', 'pic_position', 'phone_number', 'logo', 'verification_document', 'website', 'description'];
        } elseif ($step == 4) {
            $fields = ['username', 'password', 'password_confirmation'];
        } else {
            return response()->json(['message' => 'Langkah tidak valid.'], 400);
        }

        // filter rules
        foreach ($fields as $field) {
            if (isset($allRules[$field])) {
                $rulesForStep[$field] = $allRules[$field];
            }
        }
        
        // validasi
        $validator = Validator::make($request->all(), $rulesForStep, $allMessages);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        return response()->json(['message' => 'Validasi berhasil.'], 200);
    }
}