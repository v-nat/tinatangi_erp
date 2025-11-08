<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Account Credentials</title>
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
                                Your Account Has Been Created
                            </h1>

                            <p style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #eeeeee; margin: 0 0 15px 0; font-size: 16px; line-height: 1.6;">
                                Hi {{ $name }},
                            </p>

                            <p style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #eeeeee; margin: 0 0 25px 0; font-size: 16px; line-height: 1.6;">
                                Your account for the <strong style="color: #cda45e;">Tinatangi Cafe ERP System</strong> has been successfully created. Here are your login credentials:
                            </p>

                            <div style="background-color: #1a1816; border: 1px solid #444; border-radius: 5px; padding: 20px; font-size: 16px; line-height: 1.7; color: #eeeeee;">
                                <strong style="color: #cda45e;">Email:</strong> {{ $email }}<br>
                                <strong style="color: #cda45e;">Password:</strong> {{ $password }}
                            </div>

                            <p style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #eeeeee; margin: 25px 0 25px 0; font-size: 16px; line-height: 1.6; text-align: center;">
                                You can log in at the following link:
                            </p>

                            <p style="text-align: center; margin: 25px 0 25px 0;">
                                <a href="{{ $login_link }}"
                                   style="display: inline-block; padding: 12px 20px; background-color: #cda45e; color: #1a1816; text-decoration: none; border-radius: 5px; font-weight: bold; font-family: Arial, sans-serif; font-size: 16px;">
                                    Login Now
                                </a>
                            </p>

                            <div style="border-left: 4px solid #d9534f; background-color: #2f2221; padding: 15px 20px; margin-top: 25px; font-size: 15px; line-height: 1.6; color: #f0f0f0;">
                                <strong style="color: #d9534f;">IMPORTANT:</strong> For your security, please change your password after logging in for the first time.
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="background-color: #1a1816; padding: 30px 20px; font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; color: #aaa; font-size: 14px;">
                            <p style="margin: 0 0 10px 0; color: #cda45e; font-size: 18px; font-family: Georgia, 'Times New Roman', Times, serif;">Tinatangi Cafe ERP System</p>
                            <p style="margin: 0;">&copy; {{ date('Y') }} Tinatangi Cafe. All Rights Reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
