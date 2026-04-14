<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; }
        .header { background: #5b4cdb; color: white; padding: 20px; text-align: center; border-radius: 8px 8px 0 0; }
        .content { padding: 24px; background: #fff; border: 1px solid #e0e0e0; }
        .footer { padding: 16px 24px; background: #f5f5f5; font-size: 12px; color: #999; border-radius: 0 0 8px 8px; }
        .btn { display: inline-block; padding: 12px 24px; background: #5b4cdb; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header"><h1>{{ $companyName ?? 'Wellness Behavioral Health' }}</h1></div>
    <div class="content">
        {!! nl2br(e($body)) !!}
        @if(!empty($actionUrl))
        <p style="text-align:center;margin-top:24px"><a href="{{ $actionUrl }}" class="btn">{{ $actionText ?? 'Take Action' }}</a></p>
        @endif
    </div>
    <div class="footer">&copy; {{ date('Y') }} {{ $companyName ?? 'Wellness Behavioral Health' }}</div>
</body>
</html>
