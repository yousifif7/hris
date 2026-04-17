<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\SendMessageJob;
use App\Models\Candidate;
use App\Models\EmailTemplate;
use App\Models\Message;
use App\Models\Setting;
use App\Services\MailService;
use App\Services\SmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    // ─── Email messages ─────────────────────────────────────────────────────

    /**
     * List messages in a folder.
     * GET /api/messages?folder=inbox|sent|draft|trash&search=&page=
     */
    public function index(Request $request): JsonResponse
    {
        $folder = $request->input('folder', 'inbox');
        $search = $request->input('search', '');

        $query = Message::emails()
            ->with(['candidate:id,first_name,last_name', 'createdBy:id,first_name,last_name'])
            ->folder($folder)
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('to', 'like', "%{$search}%")
                  ->orWhere('from', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(25);

        // Unread count per folder
        $unread = Message::emails()->where('folder', 'inbox')->where('is_read', false)->count();

        return response()->json([
            'messages'     => $messages,
            'unread_count' => $unread,
        ]);
    }

    /**
     * Show a single message (marks it as read).
     */
    public function show(Message $message): JsonResponse
    {
        if ($message->type !== 'email') abort(404);
        $message->update(['is_read' => true]);
        return response()->json($message->load('candidate', 'createdBy'));
    }

    /**
     * Compose — create draft or send immediately.
     * POST /api/messages
     * body: { to, subject, body, body_html?, cc?, bcc?, candidate_id?, template_id?, send_now }
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'to'           => 'required_if:send_now,true|nullable|email',
            'cc'           => 'nullable|string',
            'bcc'          => 'nullable|string',
            'subject'      => 'required_if:send_now,true|nullable|string|max:255',
            'body'         => 'required|string',
            'body_html'    => 'nullable|string',
            'candidate_id' => 'nullable|exists:candidates,id',
            'template_id'  => 'nullable|exists:email_templates,id',
            'send_now'     => 'nullable|boolean',
        ]);

        $sendNow = ! empty($data['send_now']);
        $from    = Setting::get('smtp_from_email', config('mail.from.address', ''));

        // Always create as draft first — only promote to sent after confirmed delivery
        $message = Message::create([
            'type'         => 'email',
            'folder'       => 'draft',
            'candidate_id' => $data['candidate_id'] ?? null,
            'template_id'  => $data['template_id'] ?? null,
            'created_by'   => auth()->id(),
            'from'         => $from,
            'to'           => $data['to'] ?? null,
            'cc'           => $data['cc'] ?? null,
            'bcc'          => $data['bcc'] ?? null,
            'subject'      => $data['subject'] ?? null,
            'body'         => $data['body'],
            'is_html'      => ! empty($data['body_html']),
            'sent_at'      => null,
        ]);

        if ($sendNow) {
            try {
                MailService::send(
                    to: $data['to'],
                    subject: $data['subject'] ?? '(No Subject)',
                    body: $data['body'],
                    isHtml: ! empty($data['body_html']),
                    fromEmail: $from ?: null,
                    cc: $data['cc'] ?? null,
                    bcc: $data['bcc'] ?? null,
                );
                $message->update(['folder' => 'sent', 'sent_at' => now()]);
            } catch (\Throwable $e) {
                // Keep as draft so the user can retry; surface the real SMTP error
                return response()->json(['error' => 'Failed to send: ' . $e->getMessage()], 500);
            }
        }

        return response()->json($message->load('candidate'), 201);
    }

    /**
     * Update a draft.
     * PATCH /api/messages/{message}
     */
    public function update(Request $request, Message $message): JsonResponse
    {
        if ($message->folder !== 'draft') {
            return response()->json(['error' => 'Only drafts can be edited.'], 422);
        }

        $data = $request->validate([
            'to'           => 'nullable|email',
            'cc'           => 'nullable|string',
            'bcc'          => 'nullable|string',
            'subject'      => 'nullable|string|max:255',
            'body'         => 'nullable|string',
            'body_html'    => 'nullable|string',
            'candidate_id' => 'nullable|exists:candidates,id',
            'template_id'  => 'nullable|exists:email_templates,id',
        ]);

        $message->update(array_filter($data, fn($v) => $v !== null));
        return response()->json($message->fresh('candidate'));
    }

    /**
     * Send a draft or move to trash / permanently delete.
     * DELETE /api/messages/{message}?permanent=1
     */
    public function destroy(Message $message): JsonResponse
    {
        if ($message->folder === 'trash') {
            $message->delete(); // permanent
        } else {
            $message->update(['folder' => 'trash']);
        }
        return response()->json(['ok' => true]);
    }

    /**
     * Send a saved draft.
     * POST /api/messages/{message}/send
     */
    public function send(Message $message): JsonResponse
    {
        if ($message->folder !== 'draft') {
            return response()->json(['error' => 'Message is not a draft.'], 422);
        }
        if (empty($message->to)) {
            return response()->json(['error' => 'Recipient (to) is required.'], 422);
        }

        try {
            MailService::send(
                to: $message->to,
                subject: $message->subject ?? '(No Subject)',
                body: $message->body,
                isHtml: (bool) $message->is_html,
                cc: $message->cc,
                bcc: $message->bcc,
            );
            $message->update(['folder' => 'sent', 'sent_at' => now()]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to send: ' . $e->getMessage()], 500);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Mark email as read / unread.
     * PATCH /api/messages/{message}/read
     */
    public function markRead(Request $request, Message $message): JsonResponse
    {
        $message->update(['is_read' => $request->boolean('read', true)]);
        return response()->json(['ok' => true]);
    }

    // ─── Candidate quick-send ────────────────────────────────────────────────

    /**
     * Send an email directly to a candidate.
     * POST /api/candidates/{candidate}/send-email
     */
    public function sendToCandidate(Request $request, Candidate $candidate): JsonResponse
    {
        $data = $request->validate([
            'subject'     => 'required|string|max:255',
            'body'        => 'required|string',
            'body_html'   => 'nullable|string',
            'template_id' => 'nullable|exists:email_templates,id',
            'cc'          => 'nullable|string',
        ]);

        if (empty($candidate->email)) {
            return response()->json(['error' => 'Candidate has no email address.'], 422);
        }

        // Replace template tokens if body came from a template
        if (! empty($data['template_id'])) {
            $tpl  = EmailTemplate::find($data['template_id']);
            $vars = $this->buildCandidateVars($candidate);
            if ($tpl) {
                $rendered        = $tpl->render($vars);
                $data['subject'] = $rendered['subject'];
                $data['body']    = $rendered['body'];
                if ($rendered['body_html']) {
                    $data['body_html'] = $rendered['body_html'];
                }
            }
        }

        $from = Setting::get('smtp_from_email', config('mail.from.address', ''));

        $isHtml = ! empty($data['body_html']);
        $body   = $isHtml ? $data['body_html'] : $data['body'];

        try {
            MailService::send(
                to: $candidate->email,
                subject: $data['subject'],
                body: $body,
                isHtml: $isHtml,
                fromEmail: $from,
                cc: $data['cc'] ?? null,
            );
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Failed to send: ' . $e->getMessage()], 500);
        }

        $message = Message::create([
            'type'         => 'email',
            'folder'       => 'sent',
            'candidate_id' => $candidate->id,
            'template_id'  => $data['template_id'] ?? null,
            'created_by'   => auth()->id(),
            'from'         => $from,
            'to'           => $candidate->email,
            'cc'           => $data['cc'] ?? null,
            'subject'      => $data['subject'],
            'body'         => $data['body'],
            'is_html'      => $isHtml,
            'sent_at'      => now(),
        ]);

        return response()->json(['ok' => true, 'message_id' => $message->id]);
    }

    /**
     * Send an SMS directly to a candidate.
     * POST /api/candidates/{candidate}/send-sms
     */
    public function sendSmsToCandidate(Request $request, Candidate $candidate): JsonResponse
    {
        $data = $request->validate([
            'body' => 'required|string|max:1600',
        ]);

        if (empty($candidate->phone)) {
            return response()->json(['error' => 'Candidate has no phone number.'], 422);
        }

        // Replace tokens
        $body = $data['body'];
        foreach ($this->buildCandidateVars($candidate) as $key => $val) {
            $body = str_replace("{{{$key}}}", (string) $val, $body);
        }

        $from = Setting::get('twilio_from_number', '');

        $message = Message::create([
            'type'         => 'sms',
            'folder'       => 'sent',
            'candidate_id' => $candidate->id,
            'created_by'   => auth()->id(),
            'from'         => $from,
            'to'           => $candidate->phone,
            'body'         => $body,
            'sent_at'      => now(),
        ]);

        SendMessageJob::dispatchSync($message);

        return response()->json(['ok' => true, 'message_id' => $message->id]);
    }

    // ─── SMS inbox/sent/trash ────────────────────────────────────────────────

    /**
     * List SMS messages.
     * GET /api/sms?folder=sent|trash&search=
     */
    public function smsIndex(Request $request): JsonResponse
    {
        $folder = $request->input('folder', 'sent');
        $search = $request->input('search', '');

        $query = Message::sms()
            ->with(['candidate:id,first_name,last_name'])
            ->folder($folder)
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('to', 'like', "%{$search}%")
                  ->orWhere('body', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate(25));
    }

    /**
     * Compose and send an SMS.
     * POST /api/sms
     */
    public function smsSend(Request $request): JsonResponse
    {
        $data = $request->validate([
            'to'           => 'required|string',
            'body'         => 'required|string|max:1600',
            'candidate_id' => 'nullable|exists:candidates,id',
        ]);

        $sms = app(SmsService::class);
        if (! $sms->isConfigured()) {
            return response()->json(['error' => 'SMS is not configured. Please add Twilio credentials in Settings.'], 422);
        }

        $from = Setting::get('twilio_from_number', '');

        $message = Message::create([
            'type'         => 'sms',
            'folder'       => 'sent',
            'candidate_id' => $data['candidate_id'] ?? null,
            'created_by'   => auth()->id(),
            'from'         => $from,
            'to'           => $data['to'],
            'body'         => $data['body'],
            'sent_at'      => now(),
        ]);

        SendMessageJob::dispatchSync($message);

        return response()->json($message, 201);
    }

    /**
     * Delete SMS message (move to trash or permanently delete).
     */
    public function smsDestroy(Message $message): JsonResponse
    {
        if ($message->type !== 'sms') abort(404);
        if ($message->folder === 'trash') {
            $message->delete();
        } else {
            $message->update(['folder' => 'trash']);
        }
        return response()->json(['ok' => true]);
    }

    // ─── Bulk send ───────────────────────────────────────────────────────────

    /**
     * Send an email to multiple candidates at once.
     * POST /api/candidates/bulk-email
     */
    public function bulkEmail(Request $request): JsonResponse
    {
        $data = $request->validate([
            'candidate_ids' => 'required|array|min:1',
            'candidate_ids.*' => 'integer|exists:candidates,id',
            'subject'       => 'required|string|max:255',
            'body'          => 'required|string',
        ]);

        $sent    = 0;
        $skipped = 0;
        $from    = Setting::get('smtp_from_email', config('mail.from.address', ''));

        foreach ($data['candidate_ids'] as $candidateId) {
            $candidate = Candidate::find($candidateId);
            if (! $candidate || empty($candidate->email)) { $skipped++; continue; }

            $vars    = $this->buildCandidateVars($candidate);
            $subject = $data['subject'];
            $body    = $data['body'];
            foreach ($vars as $key => $val) {
                $subject = str_replace("{{{$key}}}", (string) $val, $subject);
                $body    = str_replace("{{{$key}}}", (string) $val, $body);
            }

            try {
                MailService::send(
                    to: $candidate->email,
                    subject: $subject,
                    body: $body,
                    isHtml: false,
                    fromEmail: $from,
                );
            } catch (\Throwable $e) {
                $skipped++;
                continue;
            }

            Message::create([
                'type'         => 'email',
                'folder'       => 'sent',
                'candidate_id' => $candidate->id,
                'created_by'   => auth()->id(),
                'from'         => $from,
                'to'           => $candidate->email,
                'subject'      => $subject,
                'body'         => $body,
                'sent_at'      => now(),
            ]);
            $sent++;
        }

        return response()->json(['ok' => true, 'sent' => $sent, 'skipped' => $skipped]);
    }

    /**
     * Send an SMS to multiple candidates at once.
     * POST /api/candidates/bulk-sms
     */
    public function bulkSms(Request $request): JsonResponse
    {
        $data = $request->validate([
            'candidate_ids'   => 'required|array|min:1',
            'candidate_ids.*' => 'integer|exists:candidates,id',
            'body'            => 'required|string|max:1600',
        ]);

        $sent    = 0;
        $skipped = 0;
        $from    = Setting::get('twilio_from_number', '');

        foreach ($data['candidate_ids'] as $candidateId) {
            $candidate = Candidate::find($candidateId);
            if (! $candidate || empty($candidate->phone)) { $skipped++; continue; }

            $body = $data['body'];
            foreach ($this->buildCandidateVars($candidate) as $key => $val) {
                $body = str_replace("{{{$key}}}", (string) $val, $body);
            }

            $message = Message::create([
                'type'         => 'sms',
                'folder'       => 'sent',
                'candidate_id' => $candidate->id,
                'created_by'   => auth()->id(),
                'from'         => $from,
                'to'           => $candidate->phone,
                'body'         => $body,
                'sent_at'      => now(),
            ]);

            SendMessageJob::dispatchSync($message);
            $sent++;
        }

        return response()->json(['ok' => true, 'sent' => $sent, 'skipped' => $skipped]);
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    protected function buildCandidateVars(Candidate $candidate): array
    {
        $user  = auth()->user();
        $offer = $candidate->latestOffer ?? null;

        return [
            'candidate_name'        => $candidate->full_name,
            'candidate_first_name'  => $candidate->first_name,
            'candidate_last_name'   => $candidate->last_name,
            'candidate_email'       => $candidate->email ?? '',
            'candidate_phone'       => $candidate->phone ?? '',
            'role'                  => $candidate->category?->name ?? '',
            'company_name'          => Setting::get('company_name', 'HR Team'),
            'hr_name'               => $candidate->assignedTo?->full_name ?? 'HR Team',
            'hr_email'              => $candidate->assignedTo?->email ?? '',
            'user_name'             => $user?->full_name ?? '',
            'user_email'            => $user?->email ?? '',
            'today'                 => now()->format('M d, Y'),
            'offer_pay_rate'        => $offer ? '$' . number_format($offer->pay_rate, 2) . '/' . $offer->pay_type : '',
            'offer_employment_type' => $offer?->employment_type ?? '',
            'offer_start_date'      => $offer?->start_date?->format('M d, Y') ?? '',
            'offer_orientation_date'=> $offer?->orientation_date?->format('M d, Y') ?? '',
            'offer_link'            => $offer?->token ? config('app.url') . '/offer/' . $offer->token : '',
            'scheduling_link'       => config('app.url') . '/schedule/' . $candidate->id,
        ];
    }
}
