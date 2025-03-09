<?php

namespace App\Http\Controllers;

use App\Models\Card;
use Illuminate\Http\Request;

class CardController extends Controller
{


    public function index($todoListId)
    {
        return Card::where('to_do_list_id', $todoListId)->get();
    }

    public function store(Request $request, $todoListId)
    {
        $validated = $request->validate([
            'text' => 'required|string',
            'to_do_list_id' => 'required|exists:to_do_lists,id',
        ]);

        $card = Card::create($validated);
        return response()->json($card, 201);
    }

    public function show(Card $card)
    {
        return $card;
    }

    public function update(Request $request, Card $card)
    {
        $request->validate(['title' => 'required|string']);
        $card->update($request->all());
        return $card;
    }

    public function destroy(Card $card)
    {
        $card->delete();
        return response()->noContent();
    }
}
