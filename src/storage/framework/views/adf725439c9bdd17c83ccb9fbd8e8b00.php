<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Check your email</title>
</head>
<body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4; padding-top: 20px">
    <table role="presentation" style="width: 100%; background-color: #f4f4f4;" cellspacing="0" cellpadding="0" border="0">
        <tr>
            <td align="center">
                <table role="presentation" style="max-width: 600px; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td align="center" style="padding: 20px;">
                            <img src="data:image/png;base64,<?php echo e(base64_encode(file_get_contents(public_path('logo.png')))); ?>"
                                alt="Logo"
                                style="max-width: 150px;">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px; text-align: center;">
                            <h2 style="color: #333; margin-bottom: 10px;">Verify your email</h2>
                            <p style="color: #555;">Hello <strong><?php echo e($user->email); ?></strong></p>
                            <p style="color: #555;">Thank you, you have registered an account, please check your email by clicking the button below.</p>
                            <a href="<?php echo e($url); ?>" style="display: inline-block; padding: 12px 24px; margin-top: 20px; font-size: 16px; color: #fff; background-color: #007bff; border-radius: 5px; text-decoration: none;">Check it out now</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 20px; text-align: center; color: #999; font-size: 12px;">
                        If you did not create this account, please ignore this email.
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding: 20px; font-size: 12px; color: #999;">
                            &copy; 2025 CodeLOR. All rights reserved.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
<?php /**PATH /app/resources/views/emails/verify-email.blade.php ENDPATH**/ ?>