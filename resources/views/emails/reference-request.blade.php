<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family:Arial,sans-serif;line-height:1.6;color:#333;max-width:600px;margin:0 auto">
    <h2>Reference Request</h2>
    <p>Dear {{ $referenceName }},</p>
    <p>{{ $candidateName }} has listed you as a reference for a position at {{ $companyName }}.</p>
    <ol>
        <li>How long have you known the candidate and in what capacity?</li>
        <li>What are their key strengths?</li>
        <li>Are there any areas for improvement?</li>
        <li>Would you recommend this candidate?</li>
    </ol>
    <p><a href="{{ $responseUrl }}" style="display:inline-block;padding:12px 24px;background:#5b4cdb;color:white;text-decoration:none;border-radius:6px">Submit Reference</a></p>
    <p>Thank you,<br>{{ $hrName }}<br>{{ $companyName }}</p>
</body>
</html>
