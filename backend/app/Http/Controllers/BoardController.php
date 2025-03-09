<?php

namespace App\Http\Controllers;

use App\Models\Board;
use Illuminate\Http\Request;

class BoardController extends Controller
{
    public function index()
    {
        return Board::all();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        try {
            $board = Board::create([
                'title' => $request->title,
            ]);

            return response()->json($board, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function show(Board $board)
    {
        return $board->load('todoLists.cards');
    }

    public function update(Request $request, Board $board)
    {
        $request->validate(['title' => 'required|string']);
        $board->update($request->all());
        return $board;
    }

    public function destroy(Board $board)
    {
        $board->delete();
        return response()->noContent();
    }
}
