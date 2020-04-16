<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $request->validate([
            'paginate' => 'nullable|in:true,false',
            'page' => 'nullable|integer',
            'perPage' => 'nullable|integer',
            'sortKey' => 'nullable',
            'sortOrder' => 'nullable|in:asc,desc',
            'search' => 'nullable|string'
        ]);

        $sortKey = $request->sortKey ?? 'id';
        $sortOrder = $request->sortOrder ?? 'asc';

        $query = User::with(['employee'])->orderBy($sortKey, $sortOrder);
        $pagination = [];

        if ($request->search) {
            $fields = ['name', 'email', 'username'];
            $keyword = "%$request->search%";
            $query = $query->where(function ($q) use ($fields, $keyword) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'ilike', $keyword);
                }
            });
        }

        if ($request->paginate == true) {
            $page = (int)($request->page ?? 1);
            $perPage = (int)($request->perPage ?? 20);
            $offset = ($page - 1) * $perPage;

            $total = $query->count();
            $lastPage = ceil($total / $perPage);

            $query = $query->offset($offset)->limit($perPage);
            $pagination = [
                'page' => $page,
                'perPage' => $perPage,
                'lastPage' => $lastPage,
                'start' => $page > $lastPage ? 0 : $offset + 1,
                'end' => $page > $lastPage ? 0 : $offset + $query->count(),
                'total' => $total
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully get all users',
            'results' => $query->get(),
            'pagination' => $pagination
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully created user',
            'result' => $user
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully get user',
            'result' => $user,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email,' . $user->id
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully updated user',
            'result' => $user
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully deleted user'
        ]);
    }

    /**
     * Change password user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Password has been changed',
            'user' => $user
        ]);
    }
}
