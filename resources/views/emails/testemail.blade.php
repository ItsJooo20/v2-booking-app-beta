<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Booking</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .booking-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .detail-row {
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
            color: #667eea;
            display: inline-block;
            width: 120px;
        }
        .value {
            color: #333;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background: #764ba2;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #666;
            font-size: 12px;
            border-top: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>New Booking Alert!</h1>
    </div>
    
    <div class="content">
        <p>Hi Admin,</p>
        <p>A new booking has been created and requires your attention.</p>
        
        <div class="booking-details">
            <h2 style="color: #667eea; margin-top: 0;">Booking Details</h2>
            
            <div class="detail-row">
                <span class="label">Booking ID:</span>
                <span class="value">#{{ $booking->id }}</span>
            </div>

            <div class="detail-row">
                <span class="label">Booking Date:</span>
                <span class="value">{{ \Carbon\Carbon::parse($booking->created_at)->format('d M Y H:i') }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">User:</span>
                <span class="value">{{ $user->name }}</span>
            </div>
            
            <div class="detail-row">
                <span class="label">Email:</span>
                <span class="value">{{ $user->email }}</span>
            </div>

            <div class="detail-row">
                <span class="label">Phone:</span>
                <span class="value">{{ $user->phone }}</span>
            </div>

            <div class="detail-row">
                <span class="label">Start DateTime:</span>
                <span class="value">
                    {{ \Carbon\Carbon::parse($booking->start_datetime)->format('d M Y H:i') }}
                </span>
            </div>

            <div class="detail-row">
                <span class="label">End DateTime:</span>
                <span class="value">
                    {{ \Carbon\Carbon::parse($booking->end_datetime)->format('d M Y H:i') }}
                </span>
            </div>

            <div class="detail-row">
                <span class="label">Item:</span>
                <span class="value">{{ $booking->facilityItem->item_code }}</span>
            </div>
            
            @if($booking->purpose)
            <div class="detail-row">
                <span class="label">Notes:</span>
                <span class="value">{{ $booking->purpose }}</span>
            </div>
            @endif
        </div>
        
        <center>
            <a href="{{ url('/bookings/' . $booking->id) }}" class="button">
                View Booking Details
            </a>
        </center>
        
        <p style="color: #666; font-size: 14px; margin-top: 30px;">
            If the button can't open, try this one:
            <a href="{{ url('/bookings/' . $booking->id) }}" 
            style="color: #667eea; text-decoration: underline;">
            {{ url('/bookings/' . $booking->id) }}
            </a>
        </p>

        <p style="color: #666; font-size: 14px; margin-top: 30px;">
            Please review and process this booking as soon as possible.
        </p>

    </div>
    
    <div class="footer">
        <p>This is an automated email. Please do not reply.</p>
        <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
</html>