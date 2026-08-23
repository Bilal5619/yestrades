<?php

namespace App\Http\Controllers\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Session;

class UserController extends Controller
{
  public function index(Request $request)
  {
    $searchKey = null;

    if ($request->filled('info')) {
      $searchKey = $request['info'];
    }

    $users = User::query()->when($searchKey, function ($query, $searchKey) {
      return $query->where('username', 'like', '%' . $searchKey . '%')
        ->orWhere('email', 'like', '%' . $searchKey . '%');
    })
      ->orderByDesc('id')
      ->paginate(10);

    return view('admin.end-user.user.index', compact('users'));
  }

  public function create()
  {
    return view('admin.end-user.user.create');
  }
  public function store(Request $request)
  {
    $rules = [
      'name' => 'required',
      'username' => [
        'required',
        Rule::unique('users', 'username')->whereNull('deleted_at')
      ],
      'email' => [
        'required',
        Rule::unique('users', 'email')->whereNull('deleted_at')
      ],
      'image' => 'required|dimensions:width=80,height=80',
      'password' => 'required|min:6',
    ];

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }
    $file = $request->file('image');
    $in = $request->all();
    if ($file) {
      $extension = $file->getClientOriginalExtension();
      $directory = public_path('assets/img/users/');
      $fileName = uniqid() . '.' . $extension;
      @mkdir($directory, 0775, true);
      $file->move($directory, $fileName);
    }
    $user = new User();
    $user->image = $fileName;
    $user->name = $request->name;
    $user->username = $request->username;
    $user->email = $request->email;
    $user->phone = $request->phone;
    $user->country = $request->country;
    $user->city = $request->city;
    $user->state = $request->state;
    $user->zip_code = $request->zip_code;
    $user->address = $request->address;
    $user->password = Hash::make($request->password);
    $user->email_verified_at = Carbon::now();
    $user->status = 1;
    $user->save();
    Session::flash('success', 'A new user has been added successfully.');
    return response()->json(['status' => 'success'], 200);
  }

  public function updateEmailStatus(Request $request, $id)
  {
    $user = User::query()->find($id);

    if ($request['email_status'] == 1) {
      $user->update([
        'email_verified_at' => date('Y-m-d H:i:s')
      ]);
    } else {
      $user->update([
        'email_verified_at' => NULL
      ]);
    }

    $request->session()->flash('success', 'Email status updated successfully!');

    return redirect()->back();
  }

  public function updateAccountStatus(Request $request, $id)
  {
    $user = User::query()->find($id);

    if ($request['account_status'] == 1) {
      $user->update([
        'status' => 1
      ]);
    } else {
      $user->update([
        'status' => 0
      ]);
    }

    $request->session()->flash('success', 'Account status updated successfully!');

    return redirect()->back();
  }

  public function edit($id)
  {
    $user = User::query()->findOrFail($id);
    $information['user'] = $user;
    return view('admin.end-user.user.edit', $information);
  }

  public function update(Request $request, $id)
  {
    $rules = [
      'name' => 'required',
      'username' => [
        'required',
        Rule::unique('users', 'username')->whereNull('deleted_at')->ignore($id)
      ],
      'email' => [
        'required',
        Rule::unique('users', 'email')->whereNull('deleted_at')->ignore($id)
      ],
    ];
    if ($request->hasFile('image')) {
      $rules['image'] = 'dimensions:width=80,height=80';
    }

    $validator = Validator::make($request->all(), $rules);
    if ($validator->fails()) {
      return response()->json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }
    $file = $request->file('image');
    $in = $request->all();
    if ($file) {
      $extension = $file->getClientOriginalExtension();
      $directory = public_path('assets/img/users/');
      $fileName = uniqid() . '.' . $extension;
      @mkdir($directory, 0775, true);
      $file->move($directory, $fileName);
      $in['image'] = $fileName;
    }
    $user = User::where('id', $id)->firstOrFail();
    $in['email'] = $request->email;
    $user->update($in);
    Session::flash('success', 'User has been updated successfully.');
    return response()->json(['status' => 'success'], 200);
  }

  public function changePassword($id)
  {
    $userInfo = User::query()->findOrFail($id);

    return view('admin.end-user.user.change-password', compact('userInfo'));
  }

  public function updatePassword(Request $request, $id)
  {
    $rules = [
      'new_password' => 'required|confirmed',
      'new_password_confirmation' => 'required'
    ];

    $messages = [
      'new_password.confirmed' => 'Password confirmation does not match.',
      'new_password_confirmation.required' => 'The confirm new password field is required.'
    ];

    $validator = Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
      return Response::json([
        'errors' => $validator->getMessageBag()->toArray()
      ], 400);
    }

    $user = User::query()->find($id);

    $user->update([
      'password' => Hash::make($request->new_password)
    ]);

    $request->session()->flash('success', 'Password updated successfully!');

    return Response::json(['status' => 'success'], 200);
  }

  public function secret_login($id)
  {
    $user = User::where('id', $id)->first();
    Auth::guard('web')->login($user);
    Session::put('secret_login', 1);
    return redirect()->route('user.dashboard');
  }

  public function destroy($id)
  {
    $user = User::query()->findOrFail($id);
    $user->delete();

    return redirect()->back()->with('success', 'User deleted successfully!');
  }

  public function bulkDestroy(Request $request)
  {
    $ids = $request->ids;

    foreach ($ids as $id) {
      $user = User::query()->findOrFail($id);
      $user->delete();
    }

    $request->session()->flash('success', 'Users deleted successfully!');

    return Response::json(['status' => 'success'], 200);
  }
}
