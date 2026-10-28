<?php

namespace App\Http\Controllers;

use App\Mail\ContactArtist;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Send a contact message directly to the artist.
     */
    public function send(Request $request, string $slug)
    {
        $sender = Auth::user();
        $artist = User::where('slug', $slug)->firstOrFail();

        // Prevent users from contacting themselves
        if ($sender->id === $artist->id) {
            $msg = 'You cannot send a contact message to yourself.';
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Validate contact message input
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:150'],
            'message' => ['required', 'string', 'min:10', 'max:2500'],
        ]);

        // Send email to artist
        Mail::to($artist->email)->send(new ContactArtist(
            $sender->name,
            $sender->email,
            $artist->name,
            $validated['subject'],
            $validated['message']
        ));

        $successMsg = 'Your message has been sent to the artist successfully!';

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $successMsg,
            ]);
        }

        return redirect()->back()->with('success', $successMsg);
    }
}

