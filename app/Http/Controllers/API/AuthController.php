<?php


namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('Token Name')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }
    /*
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('Token Name')->plainTextToken;
            return response()->json(['token' => $token], 200);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
*/

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
       
       
  
        $user = User::where('email', $request->input('email'))->firstOrFail();
        if ($user->role != 'admin') {
            DB::statement("UPDATE users SET role='loggedIn' WHERE id=$user->id");
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Hello, Team: ' . $user->name . ' welcome! Have a nice day!',
            'access_token' => $token,
            'token_type' => 'Bearer',
            // Dodato da se user_id vrati

            'role' => $user->role,
            'user_id' => $user->id
        ]);
    }


    public function forgotPassword(Request $request)
    {
        // Validacija dolaznih podataka
        $request->validate([
            'email' => 'required|email', // Dodata je validacija da mora biti validan email
            'password' => 'required|string|min:4|confirmed' // Dodato je confirmed pravilo za potvrdu lozinke
        ]);
    
        // Pronalaženje korisnika na osnovu emaila
        $user = User::where('email', $request->email)->firstOrFail();
    
        // Promena lozinke
        $user->password = Hash::make($request->password);
        $user->save();
    
        // Odgovor u JSON formatu
        return response()->json([
            'message' => 'Lozinka je uspešno resetovana.',
            'user' => $user
        ]);
    }
    
    public function logout(Request $request)
    {
        $user = $request->user();

        // Ažuriranje uloge korisnika, ako je potrebno
        if ($user->role !== 'admin') {
            $user->update(['role' => 'loggedOut']);
        }

        // Brisanje svih tokena korisnika
       $user->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out!']);
    }
}
