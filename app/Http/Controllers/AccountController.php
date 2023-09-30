<?php

namespace App\Http\Controllers;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AccountController extends Controller
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'new_password_confirmation',
        'new_password',
        'password',
        'firstname',
        'lastname',
        'name',
        'country',
        'state',
        'city',
        'address',
        'location',
        'image',
        'type',
    ];

    public function ping()
    {
        return response()->json([
            'message' => 'PONG!',
        ], 200);
    }

    /**
     * Get the currently logged user.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();

        return (new UserResource($user))->additional([
            'message' => 'OK',
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    public function update(Request $request, $identifier = 'password')
    {
        /** @var \App\Models\User */
        $user = $request->user();

        $filled = collect($request->all());
        $fields = collect($request->all())->only($this->fillable)->keys();

        $updated = [];

        $valid = $fields->mapWithKeys(function ($field) use ($filled) {
            if (Str::contains($field, ':image')) {
                $field = current(explode(':image', $field));
            }

            $vals = $field == 'image' ? 'mimes:png,jpg' : (is_array($filled[$field])
                ? 'array'
                : (is_int($filled[$field])
                    ? 'numeric'
                    : 'string'
                )
            );
            if ($field === 'new_password') {
                $vals .= '|min:8|confirmed';
            }
            if ($field === 'password') {
                $vals .= '|current_password';
            }

            if (is_array($filled[$field])) {
                return [$field . '.*' => 'required'];
            }

            return [$field => "required|$vals"];
        })->all();

        $this->validate($request, $valid, [], $fields->filter(function ($k) use ($filled) {
            return is_array($filled[$k]);
        })->mapWithKeys(function ($field, $value) use ($filled) {
            return collect(array_keys((array) $filled[$field]))->mapWithKeys(fn ($k) => ["$field.$k" => "$field $k"]);
        })->all());

        $fields = $fields->filter(function ($k) {
            return ! Str::contains($k, '_confirmation');
        });

        if (! $request->hasFile('image')) {
            foreach ($fields as $_field) {
                if (Str::contains($_field, ':image')) {
                    $_field = current(explode(':image', (string) $_field));
                }

                if (!in_array($_field, ['password', 'new_password', 'new_password_confirmation'])) {
                    $updated[$_field] = $request->{$_field};
                }

                if ($_field === 'new_password') {
                    $user->password = $request->new_password;
                } else {
                    $user->{$_field} = $request->{$_field};
                }
            }
        }

        $user->save();

        return (new UserResource($user))->additional([
            'message' => "Your profile $identifier has been successfully updated.",
            'status' => 'success',
            'status_code' => HttpStatus::OK,
            'image' => $user->avatar,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Update the user data
     *
     * @param  Request  $request
     * @return void
     */
    public function store(Request $request)
    {
        $user = $request->user();

        $this->validate($request, [
            'firstname' => ['required', 'string', 'max:255'],
            'lastname' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'address' => ['nullable', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'about' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:34'],
        ], [], [
            'phone' => 'Phone Number',
            'email' => 'Email Address',
        ]);

        $user->phone = $request->phone ?? $user->phone;
        $user->email = $request->email ?? $user->email;
        $user->firstname = $request->firstname ?? $user->firstname;
        $user->lastname = $request->lastname ?? $user->lastname;
        $user->location = $request->location ?? $user->location;
        $user->address = $request->address ?? $user->address;
        $user->country = $request->country ?? $user->country;
        $user->state = $request->state ?? $user->state;
        $user->city = $request->city ?? $user->city;
        $user->about = $request->about ?? $user->about;

        $user->save();

        return (new UserResource($user))->additional([
            'message' => 'Your profile has been successfully updated.',
            'status' => 'success',
            'status_code' => 200,
        ])->response()->setStatusCode(200);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $this->validate($request, [
            'reason' => ['required', 'string', 'min:15'],
        ], [
            'reason.required' => 'We would really love to know why you are leaving.'
        ]);

        User::whereId($request->user()->id)->delete();

        return $this->responseBuilder([
            'message' => 'Your account has now been deleted successfully.',
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ]);
    }
}
