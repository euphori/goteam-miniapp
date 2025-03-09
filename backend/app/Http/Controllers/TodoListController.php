<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ToDoList;
use App\Models\Card;

class ToDoListController extends Controller
{
    // Fetch all lists with their cards
    public function index(Request $request)
    {
        $boardId = $request->query('board_id'); // Get board_id from query params

        if (!$boardId) {
            return response()->json(['error' => 'Board ID is required'], 400);
        }

        // Ensure filtering by board_id
        $lists = ToDoList::where('board_id', $boardId)->with('cards')->get();

        return response()->json($lists);
    }


    // Create a new list
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'board_id' => 'required|exists:boards,id',
        ]);

        $list = ToDoList::create([
            'title' => $request->title,
            'board_id' => $request->board_id,
        ]);

        return response()->json($list);
    }


    // Delete a list and its cards
    public function destroy($id)
    {
        $list = ToDoList::findOrFail($id);
        $list->cards()->delete(); // Delete all cards in the list
        $list->delete();
        return response()->json(['message' => 'List deleted']);
    }

    // Add a card to a list
    public function addCard(Request $request, $listId)
    {
        $request->validate(['text' => 'required|string']);
        $card = Card::create([
            'text' => $request->text,
            'to_do_list_id' => $listId,
        ]);
        return response()->json($card);
    }

    // Delete a card
    public function deleteCard($id)
    {
        Card::findOrFail($id)->delete();
        return response()->json(['message' => 'Card deleted']);
    }
}
