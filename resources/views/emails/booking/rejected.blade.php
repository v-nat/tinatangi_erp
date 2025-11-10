<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Update</title>
</head>
<body style="margin: 0; padding: 0; background-color: #1a1816; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td align="center" style="padding: 20px 0;">
                <table border="0" cellpadding="0" cellspacing="0" width="600" style="max-width: 600px; border-collapse: collapse; background-color: #2a2826; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.5);">

                    <tr>
                        <td align="center" style="background-color: #1a1816; padding: 40px 20px;">
                            <img src="https://tinatangi.site/logo.png" alt="Tinatangi Cafe" width="120" style="display: block; width: 120px;">
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 40px 30px;">
                            <h1 style="font-family: Georgia, 'Times New Roman', Times, serif; color: #cda45e; margin: 0 0 20px 0; font-size: 28px; font-weight: bold;">
                                Update on Your Booking Request
                            </h1>
                            <p style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #eeeeee; margin: 0 0 15px 0; font-size: 16px; line-height: 1.6;">
                                Hi {{ $booking->name }},
                            </p>
                            <p style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #eeeeee; margin: 0 0 20px 0; font-size: 16px; line-height: 1.6;">
                                Thank you for choosing Tinatangi Cafe. Unfortunately, we are unable to accommodate your booking request on <strong style="color: #cda45e;">{{ $booking->date->format('F d, Y') }}</strong> at <strong style="color: #cda45e;">{{ \Carbon\Carbon::parse($booking->time)->format('g:i A') }}</strong>.
                            </p>

                            <div style="background-color: #1a1816; border: 1px solid #444; border-radius: 5px; padding: 20px; font-size: 16px; line-height: 1.7; color: #eeeeee;">
                                <strong style="color: #cda45e;">Guests:</strong> {{ $booking->people }}<br>
                                <strong style="color: #cda45e;">Requested Table:</strong> {{ $booking->table->name ?? 'Not specified' }}
                            </div>

                            @if(!empty($booking->status_note))
                                <div style="background-color: #2f2221; border-left: 4px solid #d9534f; padding: 15px 20px; margin-top: 25px; font-size: 15px; line-height: 1.6; color: #f0f0f0;">
                                    <strong style="color: #d9534f;">Reason Provided:</strong><br>
                                    {!! nl2br(e($booking->status_note)) !!}
                                </div>
                            @endif

                            <p style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #eeeeee; margin: 25px 0 0 0; font-size: 16px; line-height: 1.6;">
                                We sincerely apologize for the inconvenience. Please feel free to contact us if you would like to explore an alternative date or time — we'd be happy to assist you.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="background-color: #1a1816; padding: 30px 20px; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #aaa; font-size: 14px;">
                            <p style="margin: 0 0 10px 0; color: #cda45e; font-size: 18px; font-family: Georgia, 'Times New Roman', Times, serif;">Tinatangi Cafe</p>
                            <p style="margin: 0 0 5px 0;">Brgy 13 Jose Abad Santos Ave, Dasmariñas, 4114 Cavite</p>
                            <p style="margin: 0;">&copy; {{ date('Y') }} Tinatangi Cafe. All Rights Reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>

