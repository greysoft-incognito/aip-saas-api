<?php

namespace App\Http\Controllers\Auth;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\Extendable;
use DeviceDetector\DeviceDetector;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    use Extendable;

    /**
     * Handle an incoming registration request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        Validator::make($request->all(), [
            'name' => ['required_without:firstname', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => "required|string|max:255|unique:users,phone",
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'address' => ['required', 'string', 'min:5', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:farmer,processsor,marketer,transporter,offtaker,researcher'],
        ], [
            'name.required_without' => 'Please enter your fullname.'
        ], [
            'email' => 'Email Address',
            'phone' => 'Phone Number',
        ])->validate();

        $user = $this->createUser($request);

        return $this->setUserData($request, $user);
    }

    /**
     * Create a new user based on the provided data
     *
     * @param  Request  $request
     * @return App\Models\v1\User
     */
    public function createUser(Request $request)
    {
        $firstname = str($request->get('name'))->explode(' ')->first();
        $lastname = str($request->get('name'))->explode(' ')->last(fn($n)=> $n !== $firstname);

        $user = User::create([
            'role' => 'user',
            'type' => $request->get('type', 'farmer'),
            'firstname' => $request->get('firstname', $firstname),
            'lastname' => $request->get('lastname', $lastname),
            'email' => $request->get('email'),
            'phone' => $request->get('phone'),
            'dob' => $request->get('dob'),
            'address' => $request->get('address'),
            'country' => $request->get('country'),
            'state' => $request->get('state'),
            'city' => $request->get('city'),
            'password' => Hash::make($request->get('password')),
        ]);

        return $user;
    }

    public function setUserData(Request $request, $user)
    {
        event(new Registered($user));

        $dev = new DeviceDetector($request->userAgent());

        $device = $dev->getBrandName()
            ? ($dev->getBrandName() . $dev->getDeviceName())
            : $request->userAgent();

        $user->access_data = $this->ipInfo();
        if (isset($user->access_data['loc'])) {
            $user->location = $user->access_data['loc'];
        }
        $user->save();

        $token = $user->createToken($device)->plainTextToken;

        return $this->preflight($token);
    }

    /**
     * Log the newly registered user in
     *
     * @param  string  $token
     * @return App\Http\Resources\v1\User\UserResource
     */
    public function preflight($token)
    {
        [$id, $user_token] = explode('|', $token, 2);

        $token_data = DB::table('personal_access_tokens')->where('token', hash('sha256', $user_token))->first();

        $user_id = $token_data->tokenable_id;

        Auth::loginUsingId($user_id);

        $user = Auth::user();

        return (new UserResource($user))->additional([
            'message' => 'Registration was successfull',
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
            'token' => $token,
        ])->response()->setStatusCode(HttpStatus::CREATED);
    }
}
