<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Course;

class PaymentController extends Controller
{
    public function create()
    {
        return response()->json(['message' => 'Endpoint not needed for API'], 200);
    }
    public function store(Request $request)
    {



        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric',

        ]);

        try {
            $payment = new Payment();
            $payment->user_id = $validated['user_id'];
            $payment->cena = $validated['amount'];

            $payment->save();
            return response()->json($payment, 201);
        } catch (\Exception $e) {
            // Loguj grešku u log fajl

            // Vrati tačnu poruku o grešci kao odgovor
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }






























    public function index()
    {
        $payments = Payment::with('user')->get();
        return response()->json($payments);
    }

    public function show($id)
    {
        $payment = Payment::with('courses')->findOrFail($id);
        return response()->json($payment);
    }

    public function addCoursesForm($id)
    {
        $payment = Payment::findOrFail($id);
        $courses = Course::all();
        return response()->json(['message' => 'Endpoint not needed for API'], 200);
    }

    public function addCourses(Request $request, $id)
    {
        $payment = Payment::findOrFail($id);
        $payment->courses()->sync($request->course_ids);

        return response()->json($payment->load('courses'), 200);
    }
}
