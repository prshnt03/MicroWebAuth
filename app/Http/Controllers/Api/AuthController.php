<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Validator;
use Illuminate\Support\Str;
use SebastianBergmann\Environment\Console;

class AuthController extends Controller
{

  private $apiToken;
  public function __construct()
  {
    $this->apiToken = uniqid(base64_encode(Str::random(40)));
  }
  /** 
   * Register API 
   * 
   * @return \Illuminate\Http\Response 
   */
  public function register(Request $request)
  {

    $validator = Validator::make($request->all(), [
      'name' => 'required',
      'email' => 'required|email',
      'password' => 'required',

    ]);
    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()]);
    }
    $postArray = $request->all();

    $postArray['password'] = bcrypt($postArray['password']);
    $token = $this->apiToken;
    $postArray['auth_token'] = $token;
    $user = User::create($postArray);

    $success['auth_token'] = $token;
    $success['name'] =  $user->name;
    $success['message'] = 'Successfully Registered';
    return response()->json([
      'status' => 'success',
      'message' => 'Successfully Registered',
      'data' => $success,
    ]);
  }


  /**
   * Login api
   *
   * @return \Illuminate\Http\Response
   */
  public function login(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
      'password' => 'required',

    ]);
    if ($validator->fails()) {
      return response()->json(['error' => $validator->errors()]);
    }

    $postArray = $request->all();

    $mpass = $request->password;
    $email = $request->email;

    if (Auth::attempt(['email' => $email, 'password' => $mpass])) {
      $user = Auth::user();
      $token = $this->apiToken;
      //$success['token'] = $user->auth_token;
      //$success['name'] =  $user->name;
      //$success['data'] =  $user;
      $user['ref_token'] =  $token;

      return response()->json([
        'status' => 'success',
        'message' => 'Login successfully',
        'data' => $user,
      ]);
    } else {
      //return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
      return response()->json([
        'status' => 'failed',
        'message' => 'Login failed',
      ]);
    }
  }


  public function addImage(Request $request)
  {


    $postArray = $request->all();

    $type = $postArray['type'];

    //$image = base64_encode(file_get_contents($request->file('img')->pat‌​h()));
    $image = base64_encode(file_get_contents($request->file('img')));
    echo $image;
    exit();

    return response()->json([
      'status' => 'success',
      'message' => 'Added photo',
    ]);


    $folderPath = "images/";


    $image_parts = explode(";base64,", $image);
    $image_type_aux = explode("image/", $image_parts[0]);
    $image_type = $image_type_aux[1];
    $image_base64 = base64_decode($image_parts[1]);
    $file = $folderPath . uniqid() . '. ' . $image_type;

    file_put_contents($file, $image_base64);
  }

  /*
  public function saveImage()
  {
    $img    = Image::make($image->getRealPath());

    $width  = $img->width();
    $height = $img->height();
    $dimension = 2362;

    $vertical   = (($width < $height) ? true : false);
    $horizontal = (($width > $height) ? true : false);
    $square     = (($width = $height) ? true : false);

    if ($vertical) {
      $top = $bottom = 245;
      $newHeight = ($dimension) - ($bottom + $top);
      $img->resize(null, $newHeight, function ($constraint) {
        $constraint->aspectRatio();
      });
    } else if ($horizontal) {
      $right = $left = 245;
      $newWidth = ($dimension) - ($right + $left);
      $img->resize($newWidth, null, function ($constraint) {
        $constraint->aspectRatio();
      });
    } else if ($square) {
      $right = $left = 245;
      $newWidth = ($dimension) - ($left + $right);
      $img->resize($newWidth, null, function ($constraint) {
        $constraint->aspectRatio();
      });
    }

    $img->resizeCanvas($dimension, $dimension, 'center', false, '#ffffff');
    $img->save(public_path("storage/{$token}/{$origFilename}"));
  }*/
}
