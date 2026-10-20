<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Internship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InternshipController extends Controller
{
    public function index(): JsonResponse
    {
        $internships = Internship::orderByDesc('id')->get();

        return response()->json([
            'message' => 'Daftar data magang berhasil diambil.',
            'data' => $internships,
        ], 200);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'student_name' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['Pending', 'Diterima', 'Selesai'])],
        ]);

        $internship = Internship::create($validated);

        return response()->json([
            'message' => 'Data magang berhasil ditambahkan.',
            'data' => $internship,
        ], 201);
    }

    public function show(Internship $internship): JsonResponse
    {
        return response()->json([
            'message' => 'Detail data magang berhasil diambil.',
            'data' => $internship,
        ], 200);
    }

    public function update(Request $request, Internship $internship): JsonResponse
    {
        $validated = $request->validate([
            'student_name' => ['sometimes', 'required', 'string', 'max:255'],
            'company_name' => ['sometimes', 'required', 'string', 'max:255'],
            'position' => ['sometimes', 'required', 'string', 'max:255'],
            'start_date' => ['sometimes', 'required', 'date'],
            'status' => ['sometimes', 'required', Rule::in(['Pending', 'Diterima', 'Selesai'])],
        ]);

        $internship->update($validated);

        return response()->json([
            'message' => 'Data magang berhasil diperbarui.',
            'data' => $internship->fresh(),
        ], 200);
    }

    public function destroy(Internship $internship): JsonResponse
    {
        $internship->delete();

        return response()->json([
            'message' => 'Data magang berhasil dihapus.',
        ], 200);
    }
}
