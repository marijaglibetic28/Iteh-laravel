<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::all();

        if ($courses->isEmpty()) {
            return response()->json(['message' => 'Nema kurseva'], 404);
        }

        return response()->json($courses);
    }

    public function show($id)
    {
        $course = Course::findOrFail($id);
        return response()->json($course);
    }

    public function insert(Request $request)
    { 
        $request->validate([

            'kurs' => 'required|string|max:255',
            'opis' => 'nullable|string',
        ]);

        $course = new Course();
        $course->kurs = $request->kurs;
        $course->opis = $request->opis;
        

    // Postavi cenu na osnovu opisa
    switch ($request->opis) {
        case 'Početni nivo':
            $course->cena = 11000;
            break;
        case 'Srednji nivo':
            $course->cena = 20000;
            break;
        case 'Napredni nivo':
            $course->cena = 36000;
            break;
        default:
            return response()->json(['message' => 'Nepriznat opis kursa', 'opis' => $request->opis], 400);
    }

    $course->save();
        return response()->json(['message' => 'Kurs uspešno dodat', 'course' => $course], 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kurs' => 'required|string|max:255',
            'opis' => 'nullable|string',
        ]);

        $course = Course::findOrFail($id);
        $course->kurs = $request->kurs;
        $course->opis = $request->opis;
        $course->save();

        return response()->json(['message' => 'Kurs uspešno ažuriran', 'course' => $course]);
    }

    public function delete($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();

        return response()->json(['message' => 'Kurs uspešno obrisan']);
    }

    public function search(Request $request)
    {
        $searchTerm = $request->query('search');
    
        // Pretražujemo i po 'kurs' i po 'opis'
        $courses = Course::where(function($query) use ($searchTerm) {
            $query->where('kurs', 'like', "%$searchTerm%")
                  ->orWhere('opis', 'like', "%$searchTerm%");
        })->get();
    
        return response()->json($courses);
    }
    
}
  




