<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show()
    {
        return view('contact');
    }

    public function submit(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:120'],
            'email'    => ['required', 'email:rfc', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:30'],
            'topic'    => ['nullable', 'string', 'max:60'],
            'score'    => ['nullable', 'string', 'max:60'],
            'timeline' => ['nullable', 'string', 'max:60'],
            'source'   => ['nullable', 'string', 'max:60'],
            'message'  => ['required', 'string', 'min:5', 'max:4000'],
        ]);

        // Persist
        Contact::create($validated + [
            'ip'         => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 512),
        ]);

        Log::info('Contact form submission', ['email' => $validated['email']]);

        // Send the message to the inbox. Falls back to log driver per .env.
        try {
            Mail::raw(
                "New contact form message\n\n"
                . "Name:      {$validated['name']}\n"
                . "Email:     {$validated['email']}\n"
                . "Phone:     " . ($validated['phone']    ?? '—') . "\n"
                . "Topic:     " . ($validated['topic']    ?? '—') . "\n"
                . "Score:     " . ($validated['score']    ?? '—') . "\n"
                . "Timeline:  " . ($validated['timeline'] ?? '—') . "\n"
                . "Source:    " . ($validated['source']   ?? '—') . "\n\n"
                . "Message:\n{$validated['message']}\n",
                function ($mail) use ($validated) {
                    $mail->to('info@victoriousopportunities.com')
                         ->replyTo($validated['email'], $validated['name'])
                         ->subject('New contact form — ' . $validated['name']);
                }
            );
        } catch (\Throwable $e) {
            Log::error('Contact mail failed', ['error' => $e->getMessage()]);
        }

        return redirect()
            ->route('contact.show')
            ->with('success', true)
            ->with('contact_name', $validated['name']);
    }
}
