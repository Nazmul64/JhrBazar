<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class MailConfigurationController extends Controller
{
    // GET /admin/mail-configuration
    public function index()
    {
        $config = MailConfiguration::first();
        return view('admin.mailconfiguration.index', compact('config'));
    }

    // POST /admin/mail-configuration/update
    public function update(Request $request)
    {
        $validated = $request->validate([
            'mail_mailer'       => ['required', 'string', 'max:50'],
            'mail_host'         => ['required', 'string', 'max:255'],
            'mail_port'         => ['required', 'integer', 'min:1', 'max:65535'],
            'mail_username'     => ['nullable', 'string', 'max:255'],
            'mail_password'     => ['nullable', 'string', 'max:255'],
            'mail_encryption'   => ['required', 'string', 'in:ssl,tls,starttls,none'],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'mail_from_name'    => ['nullable', 'string', 'max:255'],
        ]);

        $config = MailConfiguration::first();

        if ($config) {
            if (empty($validated['mail_password'])) {
                unset($validated['mail_password']);
            }
            $config->update($validated);
        } else {
            $config = MailConfiguration::create($validated);
        }

        $this->updateEnv([
            'MAIL_MAILER'       => $config->mail_mailer,
            'MAIL_HOST'         => $config->mail_host,
            'MAIL_PORT'         => $config->mail_port,
            'MAIL_USERNAME'     => $config->mail_username ?? '',
            'MAIL_PASSWORD'     => $config->mail_password ?? '',
            'MAIL_ENCRYPTION'   => $config->mail_encryption,
            'MAIL_FROM_ADDRESS' => $config->mail_from_address,
            'MAIL_FROM_NAME'    => '"' . ($config->mail_from_name ?? config('app.name')) . '"',
        ]);

        return redirect()->route('admin.mailconfiguration.index')
            ->with('success', 'Mail configuration saved successfully.');
    }

    // POST /admin/mail-configuration/send-test
    public function sendTestMail(Request $request)
    {
        $request->validate([
            'recipient_email' => ['required', 'email'],
            'message'         => ['required', 'string', 'max:2000'],
        ]);

        $config = MailConfiguration::first();

        if (!$config) {
            return back()->with('error', 'Please save mail configuration first.');
        }

        $this->applyRuntimeMailConfig($config);

        try {
            Mail::raw($request->message, function ($mail) use ($request, $config) {
                $mail->to($request->recipient_email)
                     ->from($config->mail_from_address, $config->mail_from_name ?? config('app.name'))
                     ->subject('Test Mail from ' . config('app.name'));
            });

            return back()->with('success', 'Test email sent successfully to ' . $request->recipient_email);

        } catch (\Throwable $e) {
            return back()->with('error', 'Failed to send email: ' . $e->getMessage());
        }
    }

    private function applyRuntimeMailConfig(MailConfiguration $config): void
    {
        Config::set('mail.default', $config->mail_mailer);
        Config::set('mail.mailers.' . $config->mail_mailer . '.host',       $config->mail_host);
        Config::set('mail.mailers.' . $config->mail_mailer . '.port',       $config->mail_port);
        Config::set('mail.mailers.' . $config->mail_mailer . '.username',   $config->mail_username);
        Config::set('mail.mailers.' . $config->mail_mailer . '.password',   $config->mail_password);
        Config::set('mail.mailers.' . $config->mail_mailer . '.encryption', $config->mail_encryption === 'none' ? null : $config->mail_encryption);
        Config::set('mail.from.address', $config->mail_from_address);
        Config::set('mail.from.name',    $config->mail_from_name ?? config('app.name'));
    }

    private function updateEnv(array $data): void
    {
        $envPath = base_path('.env');

        if (!file_exists($envPath)) {
            return;
        }

        $envContent = file_get_contents($envPath);

        foreach ($data as $key => $value) {
            $escaped = (str_contains((string) $value, ' ') && !str_starts_with((string) $value, '"'))
                ? '"' . $value . '"'
                : $value;

            if (preg_match("/^{$key}=.*/m", $envContent)) {
                $envContent = preg_replace("/^{$key}=.*/m", "{$key}={$escaped}", $envContent);
            } else {
                $envContent .= PHP_EOL . "{$key}={$escaped}";
            }
        }

        file_put_contents($envPath, $envContent);
    }
}
