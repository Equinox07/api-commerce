<?php

namespace App\Http\Controllers\User;

use App\User;
use GuzzleHttp\retry;
use App\Mail\UserCreated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Transformers\UserTransformer;
use App\Http\Controllers\ApiController;

class UserController extends ApiController
{
    public function __construct()
    {
        $this->middleware('client.credentials')->only(['store', 'resend']);
        $this->middleware('auth:api')->except(['store','verify' ,'resend']);
        $this->middleware('transform.input:'. UserTransformer::class)->only(['store','update']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        // return response()->json(['data'=>$users], 200);
        return $this->showAll($users);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ];
        // return $request;
        
        $this->validate($request, $rules);

        $data = $request->all();
        $data['password'] = bcrypt($request->password);
        $data['verified'] = User::UNVERIFIED_USER;
        $data['verification_token'] = User::generateVerificationCode();
        $data['admin'] = User::REGULAR_USER;

        $user = User::create($data);

        return $this->showOne($user, 201);
        // return response()->json(['data'=>$user], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        // $user = User::findOrFail($id);
        return $this->showOne($user, 201);
        // return response()->json(['data'=>$user], 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        // $user = User::findOrFail($id);
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users,email'. $user->id,
            'admin' => 'in:'. User::ADMIN_USER . ',' . User::REGULAR_USER,
        ];

        if ($request->has('name'))
        {
            $user->name = $request->name;
        }
        if($request->has('email') && $user->email != $request->email)
        {
            $user->verified = User::UNVERIFIED_USER;
            $user->verification_token = User::generateVerificationCode();
            $user->email = $request->email;
        }
        if($request->has('password'))
        {
            $user->password = bcrypt($request->password); 
        }
        if($request->has('admin'))
        {
            if(!$user->isVerified())
            {
                return  $this->errorResponse('Only verified users can modify the admin field', 409);
            }
            $user->admin = $request->admin; 
        }
        
        if(!$user->isDirty())
        {
            return $this->errorResponse('You need to specifiy different value to update', 422);
        }
        $user->save();
        // return response()->json(['data'=>$user], 200);
        return $this->showOne($user);
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        // $user = User::findOrFail($id);
        $user->delete();
        // return response()->json(['data'=>$user], 200);
        return $this->showOne($user);
    }

    public function verify($token)
    {

        $user = User::where('verification_token', $token)->firstOrFail();

        $user->verified = User::VERIFIED_USER;
        $user->verification_token = null;

        $user->save();

        return $this->showMessage("You account has sucessfully been verified");
    }

    public function resend(User $user)
    {
        if($user->isVerified())
        {
            return $this->errorResponse("This user is already verified", 409);
        }

        retry(5, function() use($user){
            Mail::to($event->user)->send(new UserCreated($event->user));
        },100);

        return $this->showMessage("The verification email has been resent..");
    }
}
