<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Requests\CandidateStoreRequest;
use App\Http\Requests\CandidateUpdateRequest;
use App\Models\Users;
use App\Models\Currency;
use App\Models\Candidate;
use App\Models\Address;
use Illuminate\Support\Facades\Hash;
use Str;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ApiController extends Controller
{
    public function register(RegisterRequest $request){
        try {
            $param = $request->all();
            $insert['id'] = Str::Uuid();
            $insert['first_name'] = $param['first_name'];
            $insert['last_name'] = $param['last_name'];
            $insert['email'] = $param['email'];
            $insert['password'] = Hash::make($param['password']);
            
            $store = Users::insert($insert);
            return response()->json(['message' => 'User registered successfully'], 200);
        } catch(\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }


    public function login(LoginRequest $request){
        try {
            $param = $request->all();
            $input = $request->only('email', 'password');
            
            if (!$token = JWTAuth::attempt($input)) {
                return response()->json(['message' => 'Invalid Email or Password.'], 400);
            }
            return response()->json(['message' => 'Logged in successfully', 'token' => $token], 200);
            
        } catch(\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }


    public function logout(Request $request){
        try {
            $token = $request->header('authorization');
            
            JWTAuth::invalidate($token);
            return response()->json(['message' => 'Logged out successfully']);
            
        } catch(\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }


    public function refreshToken(Request $request){
        try {
            $token = $request->header('authorization');
            
            $newToken = JWTAuth::refresh($token);
            return response()->json(['message' => 'Token refreshed successfully', 'token' => $newToken], 200);
            
        } catch(\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
    public function saveCandidates(CandidateStoreRequest $request){
        try {
            $param = $request->all();

            $checkCurrency = Currency::where('id', $param['currency_id'])->exists();
            if($checkCurrency){
                $checkAddress = Address::where('id', $param['address_id'])->exists();
                if($checkAddress){
                    $param['id'] = Str::Uuid();
                    $param['owner_id'] = JWTAuth::user()->id;
                    
                    $store = Candidate::insert($param);
                    return response()->json(['message' => "Candidate stored successfully."], 200);
                } else {
                    return response()->json(['message' => "Address do not exists."], 200);
                }
            } else {
                return response()->json(['message' => "Currency do not exists."], 200);
            }
            
        } catch(\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
    public function candidateData(string $id){
        try {
            $data = Candidate::where('id', $id)->with('owner', 'currency', 'address')->first();
            if($data){
                $loggedInUserId = JWTAuth::user()->id;
                if($loggedInUserId === $data['owner_id']){
                    return response()->json(['message' => 'Candidate details found.', 'data' => $data], 200);
                } else {
                    return response()->json(['message' => "Owner Id and Logged in used id do not match."], 200);
                }
                
            } else {
                return response()->json(['message' => 'Candidate details not found.'], 200);
            }
        } catch(\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
    public function candidateList(){
        try {
            $list = Candidate::where('owner_id', JWTAuth::user()->id)->with('owner', 'currency', 'address')->paginate(10);
            if($list){
                return response()->json(['message' => 'Candidate list found.', 'data' => $list], 200);
            } else {
                return response()->json(['message' => 'Candidate list not found.'], 200);
            }

        } catch(\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
    public function updateCandidate(CandidateUpdateRequest $request, string $id){
        try {
            $param = $request->all();
            $candidate = Candidate::where('id', $id)->first(); //Checking if candidate exists...
            if($candidate){
                $loggedInUserId = JWTAuth::user()->id;
                if($loggedInUserId === $candidate['owner_id']){ //Comparing owner id and loggedIn user Id...
                    $checkCurrency = Currency::where('id', $param['currency_id'])->exists(); //Checking if currency exists...
                    if($checkCurrency){
                        $checkAddress = Address::where('id', $param['address_id'])->exists(); //Checking if address exists...
                        if($checkAddress){
                            $param['owner_id'] = JWTAuth::user()->id;
                            //dd($id,$param);
                            $store = Candidate::where('id', $id)->update($param);
                            return response()->json(['message' => "Candidate updated successfully."], 200);
                        } else {
                            return response()->json(['message' => "Address do not exists."], 200);
                        }
                    } else {
                        return response()->json(['message' => "Currency do not exists."], 200);
                    }
                } else {
                    return response()->json(['message' => "Owner Id and Logged in used id do not match."], 200);
                }
            } else {
                return response()->json(['message' => "Candidate do not exists."], 200); 
            }

            

        } catch(\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
    public function deleteCandidate(string $id){
        try {
            $candidate = Candidate::where('id', $id)->first();
            if($candidate){
                $loggedInUserId = JWTAuth::user()->id;
                if($loggedInUserId === $candidate['owner_id']){
                    $delete = Candidate::where('id', $id)->delete();
                    return response()->json(['message' => "Candidate deleted successfully."], 200);
                } else {
                    return response()->json(['message' => "Owner Id and Logged in used id do not match."], 200);
                }
            } else {
                return response()->json(['message' => "Candidate do not exists."], 200);
            }

        } catch(\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }
    
    
    public function searchCandidate(Request $request){
        try {
            $param = $request->all();
            $keyword = $param['keyword']??Null;
            $list = Candidate::where('owner_id', JWTAuth::user()->id);
            if(isset($keyword)){
                $list = $list->where(function ($q) use($keyword) {
                    $q->where('first_name', 'LIKE', "%" . $keyword . "%")
                        ->orWhere('last_name', 'LIKE', "%" . $keyword . "%")
                        ->orWhere('age', 'LIKE', "%" . $keyword . "%")
                        ->orWhere('department', 'LIKE', "%" . $keyword . "%")
                        ->orWhere('min_salary_expectation', 'LIKE', "%" . $keyword . "%")
                        ->orWhere('max_salary_expectation', 'LIKE', "%" . $keyword . "%");
                });
            }
            
            $list =  $list->paginate(10);
            if($list){
                return response()->json(['message' => 'Candidate list found.', 'data' => $list], 200);
            } else {
                return response()->json(['message' => 'Candidate list not found.'], 200);
            }
        } catch(\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'error' => $e->getMessage()], 500);
        }
    }
}
