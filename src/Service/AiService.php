<?php
namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AiService
{
    private HttpClientInterface $client;


    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function resumeAnalyze(string $jd, string $resume): string
    {
        $response = $this->client->request('POST', 'https://api.groq.com/openai/v1/chat/completions', [
            'headers' => [
                'Authorization' => 'Bearer ' . $_SERVER['GROQ_API_KEY'],
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'model' => 'llama-3.1-8b-instant',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => "You are an expert resume writer and senior career coach with 15+ years of experience.\n\n" .
                            "Analyze the resume and job description I provide and give structured, honest, and actionable feedback.\n\n" .
                            "Rules:\n" .
                            "- Respond ONLY with bullet points. No introductions, no conclusions, no extra text.\n" .
                            "- Use clear section headings in bold.\n" .
                            "- Keep every bullet concise and actionable.\n" .
                            "- Focus on impact, achievements, clarity, and ATS compatibility."
                    ],
                    [
                        'role' => 'user',
                        'content' => "Job Description:\n" . $jd . "\n\n" .
                            "Resume:\n" . $resume
                    ],
                ],
                'temperature' => 0.5,
                'max_tokens' => 1500
            ],
        ]);

        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);
        if ($statusCode !== 200) {
            throw new \Exception('Error from Groq API: ' . $content);
        }

        $data = json_decode($content, true);
        return $data['choices'][0]['message']['content'] ?? 'No feedback generated.';

    }
}