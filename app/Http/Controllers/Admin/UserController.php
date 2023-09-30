<?php

namespace App\Http\Controllers\Admin;

use App\EnumsAndConsts\HttpStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * @param  Request  $request
     *
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::query();

        $query->when($request->search, function (Builder $q) use ($request) {
            $q->where(function (Builder $q) use ($request) {
                $q->where("country", $request->search);
                $q->orWhere("state", $request->search);
                $q->orWhere("type", $request->search);
                $q->orWhere("email", $request->search);
                $q->orWhere("phone", $request->search);
                $q->orWhere("address", 'like', "%{$request->search}%");
                $q->orWhere("username", 'like', "%{$request->search}%");
                $q->orWhere('firstname', 'like', "%{$request->search}%");
                $q->orWhereRaw("CONCAT_WS(' ', firstname, lastname) LIKE '%$request->search%'");
            });
        })->when($request->order && is_array($request->order), function (Builder $q) use ($request) {
            foreach ($request->order as $by => $dir) {
                if (!in_array($by, ['desc', 'asc', 'null', null, ''])) {
                    $q->orderBy($by, mb_strtoupper($dir));
                }
            }
        })->when($request->type, function (Builder $q) use ($request) {
            $q->where("type", $request->type);
        })->latest();

        $users = $query->paginate($request->get('limit', '15'));

        return (new UserCollection($users))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return (new UserResource($user))->additional([
            'message' => HttpStatus::message(HttpStatus::OK),
            'status' => 'success',
            'status_code' => HttpStatus::OK,
        ])->response()->setStatusCode(HttpStatus::OK);
    }

    /**
     * Update a resource in storage.
     */
    public function update(Request $request, User $user)
    {
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
            'role' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:34'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
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
        $user->about = $request->about ?? $user->about;
        $user->state = $request->state ?? $user->state;
        $user->city = $request->city ?? $user->city;
        $user->role = $request->role ?? $user->role;
        if ($request->password) {
            $user->password = Hash::make($request->get('password'));
        }
        $user->save();

        return (new UserResource($user))->additional([
            'message' => __('User ":0" has been updated successfully', [$user->fullname]),
            'status' => 'success',
            'status_code' => HttpStatus::CREATED,
        ])->response()->setStatusCode(HttpStatus::CREATED);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        $user->delete();

        return (new UserResource($user))->additional([
            'message' => __('User ":0" has been deleted successfully', [$user->fullname]),
            'status' => 'success',
            'status_code' => HttpStatus::ACCEPTED,
        ])->response()->setStatusCode(HttpStatus::ACCEPTED);
    }
}