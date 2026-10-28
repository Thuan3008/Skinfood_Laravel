<?php

namespace App\Http\Controllers;

use App\Models\SanPham;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        if (! $this->isShopQuestion($data['message'])) {
            return response(
                'Mình chỉ hỗ trợ tư vấn sản phẩm có tại SkinFood Shop. Bạn có thể hỏi về chăm sóc da, tóc, cơ thể, trang điểm, nước hoa, giá hoặc cách sử dụng sản phẩm.',
                200
            )->header('Content-Type', 'text/plain; charset=UTF-8');
        }

        if (($catalogReply = $this->catalogReply($data['message'])) !== null) {
            return response($catalogReply, 200)
                ->header('Content-Type', 'text/plain; charset=UTF-8');
        }

        $provider = strtolower((string) config('services.ai.provider', 'gemini'));

        try {
            $reply = match ($provider) {
                'openai' => $this->askOpenAI($data['message']),
                'gemini' => $this->askGemini($data['message']),
                'groq' => $this->askGroq($data['message']),
                default => throw new \RuntimeException("AI_PROVIDER '{$provider}' không được hỗ trợ."),
            };

            return response($reply, 200)->header('Content-Type', 'text/plain; charset=UTF-8');
        } catch (ConnectionException $exception) {
            Log::warning('AI provider connection failed', [
                'provider' => $provider,
                'message' => $exception->getMessage(),
            ]);

            return response('Không thể kết nối tới dịch vụ AI. Vui lòng thử lại sau vài giây.', 503);
        } catch (RequestException $exception) {
            $providerMessage = (string) data_get($exception->response?->json(), 'error.message');
            Log::warning('AI provider request failed', [
                'provider' => $provider,
                'status' => $exception->response?->status(),
                'provider_message' => $providerMessage,
            ]);

            return response($this->friendlyProviderError($exception), 503);
        } catch (\Throwable $exception) {
            Log::warning('AI chat configuration failed', [
                'provider' => $provider,
                'message' => $exception->getMessage(),
            ]);

            return response($exception->getMessage(), 503);
        }
    }

    private function askOpenAI(string $message): string
    {
        $key = trim((string) config('services.openai.key'));
        if ($key === '') {
            throw new \RuntimeException('Chưa cấu hình OPENAI_API_KEY trong file .env.');
        }

        $response = Http::baseUrl(config('services.openai.base_url'))
            ->withToken($key)
            ->acceptJson()
            ->connectTimeout(15)
            ->timeout(30)
            ->withOptions([
                'force_ip_resolve' => 'v4',
                'version' => 1.1,
            ])
            ->retry(4, 750, fn (\Throwable $exception) => $this->shouldRetryProviderRequest($exception), throw: false)
            ->post('/chat/completions', [
                'model' => config('services.openai.model'),
                'messages' => [
                    ['role' => 'system', 'content' => $this->systemPrompt()],
                    ['role' => 'user', 'content' => $message],
                ],
                'temperature' => 0.5,
                'max_tokens' => 500,
            ])
            ->throw();

        $reply = trim((string) data_get($response->json(), 'choices.0.message.content'));
        if ($reply === '') {
            throw new \RuntimeException('OpenAI không trả về nội dung.');
        }

        return $reply;
    }

    private function askGemini(string $message): string
    {
        $key = trim((string) config('services.gemini.key'));
        if ($key === '') {
            throw new \RuntimeException('Chưa cấu hình GEMINI_API_KEY trong file .env.');
        }

        $model = rawurlencode((string) config('services.gemini.model', 'gemini-3.5-flash'));
        $response = Http::baseUrl(config('services.gemini.base_url'))
            ->acceptJson()
            ->connectTimeout(15)
            ->timeout(30)
            ->withOptions([
                'force_ip_resolve' => 'v4',
                'version' => 1.1,
            ])
            ->retry(4, 750, fn (\Throwable $exception) => $this->shouldRetryProviderRequest($exception), throw: false)
            ->post("/models/{$model}:generateContent?key=".rawurlencode($key), [
                'system_instruction' => [
                    'parts' => [['text' => $this->systemPrompt()]],
                ],
                'contents' => [[
                    'role' => 'user',
                    'parts' => [['text' => $message]],
                ]],
                'generationConfig' => [
                    'temperature' => 0.3,
                    'maxOutputTokens' => 2048,
                    'thinkingConfig' => [
                        'thinkingLevel' => 'LOW',
                    ],
                ],
            ])
            ->throw();

        $parts = data_get($response->json(), 'candidates.0.content.parts', []);
        $reply = trim(collect($parts)->pluck('text')->filter()->implode("\n"));
        if ($reply === '') {
            throw new \RuntimeException('Gemini không trả về nội dung.');
        }

        return $reply;
    }

    private function askGroq(string $message): string
    {
        $key = trim((string) config('services.groq.key'));
        if ($key === '') {
            throw new \RuntimeException('Chưa cấu hình GROQ_API_KEY trong file .env.');
        }

        $response = Http::baseUrl(config('services.groq.base_url'))
            ->withToken($key)
            ->acceptJson()
            ->connectTimeout(15)
            ->timeout(30)
            ->withOptions([
                'force_ip_resolve' => 'v4',
                'version' => 1.1,
            ])
            ->retry(4, 750, fn (\Throwable $exception) => $this->shouldRetryProviderRequest($exception), throw: false)
            ->post('/chat/completions', [
                'model' => config('services.groq.model'),
                'messages' => [
                    ['role' => 'system', 'content' => $this->systemPrompt()],
                    ['role' => 'user', 'content' => $message],
                ],
                'temperature' => 0.3,
                'max_completion_tokens' => 1000,
                'reasoning_effort' => 'none',
            ])
            ->throw();

        $reply = trim((string) data_get($response->json(), 'choices.0.message.content'));
        if ($reply === '') {
            throw new \RuntimeException('Groq không trả về nội dung.');
        }

        return $reply;
    }

    private function friendlyProviderError(RequestException $exception): string
    {
        $status = $exception->response?->status();
        $message = strtolower((string) data_get($exception->response?->json(), 'error.message'));

        if ($status === 401 || $status === 403 || str_contains($message, 'api key')) {
            return 'API key AI không hợp lệ hoặc chưa được cấp quyền. Vui lòng kiểm tra lại file .env.';
        }

        if ($status === 404 && str_contains($message, 'model')) {
            return 'Model AI đang cấu hình không còn khả dụng. Vui lòng kiểm tra GEMINI_MODEL trong file .env.';
        }

        if ($status === 429 && (
            str_contains($message, 'no credits')
            || str_contains($message, 'credit balance')
            || str_contains($message, 'insufficient_quota')
        )) {
            return 'Tài khoản API không còn credit. Vui lòng nạp credit trong trang Billing của nhà cung cấp AI.';
        }

        if ($status === 429) {
            return 'Dịch vụ AI đã hết hạn mức hoặc đang giới hạn lượt gọi. Vui lòng thử lại sau.';
        }

        return 'Dịch vụ AI đang bận. Vui lòng thử lại sau.';
    }

    private function shouldRetryProviderRequest(\Throwable $exception): bool
    {
        if ($exception instanceof ConnectionException) {
            return true;
        }

        if (! $exception instanceof RequestException) {
            return false;
        }

        $status = $exception->response?->status();
        $message = strtolower((string) data_get($exception->response?->json(), 'error.message'));

        if ($status === 429) {
            return ! str_contains($message, 'no credits')
                && ! str_contains($message, 'credit balance')
                && ! str_contains($message, 'insufficient_quota');
        }

        return in_array($status, [500, 502, 503, 504], true);
    }

    private function isShopQuestion(string $message): bool
    {
        $normalized = Str::lower(Str::ascii($message));
        $keywords = [
            'my pham', 'san pham', 'skinfood', 'skin food', 'cua hang', 'gia', 'mua',
            'ton kho', 'con hang', 'khuyen mai', 'giam gia', 'thanh phan', 'cach dung',
            'cham soc da', 'loai da', 'da dau', 'da kho', 'da nhay cam', 'da lao hoa',
            'mun', 'tham', 'nam', 'lo chan long', 'nep nhan', 'duong am', 'duong sang',
            'serum', 'retinol', 'niacinamide', 'vitamin c', 'peptide', 'bha', 'aha',
            'toner', 'kem duong', 'kem mat', 'rua mat', 'tay trang', 'chong nang',
            'mat na', 'tay te bao chet', 'mieng dan mun',
            'trang diem', 'makeup', 'son', 'cushion', 'kem nen', 'che khuyet diem',
            'phan phu', 'ma hong', 'mascara', 'ke may', 'kep mi',
            'cham soc toc', 'toc', 'da dau', 'dau goi', 'dau xa', 'u toc', 'duong toc',
            'cham soc co the', 'co the', 'sua tam', 'duong the', 'khu mui', 'kem tay',
            'nuoc hoa', 'collagen', 'may rua mat',
        ];

        foreach ($keywords as $keyword) {
            if (str_contains($normalized, $keyword)) {
                return true;
            }
        }

        try {
            $productNames = SanPham::query()
                ->where('TrangThaiSanPham', 'Còn hàng')
                ->pluck('TenSP');

            foreach ($productNames as $name) {
                $tokens = collect(preg_split('/\s+/u', Str::lower(Str::ascii((string) $name))))
                    ->map(fn (string $token) => preg_replace('/[^a-z0-9]+/', '', $token))
                    ->filter(fn (?string $token) => $token !== null && mb_strlen($token) >= 5)
                    ->unique();

                if ($tokens->contains(fn (string $token) => str_contains($normalized, $token))) {
                    return true;
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('Could not inspect product names for AI scope guard', [
                'message' => $exception->getMessage(),
            ]);
        }

        return false;
    }

    private function catalogReply(string $message): ?string
    {
        $normalized = Str::lower(Str::ascii($message));
        $isPriceQuestion = collect(['gia', 'bao nhieu', 'bao tien', 'gia ban'])
            ->contains(fn (string $phrase) => str_contains($normalized, $phrase));
        $isListQuestion = collect([
            'mot vai san pham', 'vai san pham', 'goi y san pham', 'goi y cho',
            'san pham nao', 'danh sach san pham', 'co nhung san pham',
            'cho xem san pham', 'cho toi san pham', 'co gi',
        ])->contains(fn (string $phrase) => str_contains($normalized, $phrase));

        if (! $isPriceQuestion && ! $isListQuestion) {
            return null;
        }

        try {
            $products = SanPham::query()
                ->with(['thuongHieu:MaTH,TenTH', 'loai:MaLoai,TenLoai'])
                ->where('TrangThaiSanPham', 'Còn hàng')
                ->get();

            if ($products->isEmpty()) {
                return 'Cửa hàng hiện chưa có sản phẩm đang bán.';
            }

            $exactProduct = $products->first(function (SanPham $product) use ($normalized) {
                $name = Str::lower(Str::ascii((string) $product->TenSP));

                return mb_strlen($name) >= 5 && str_contains($normalized, $name);
            });

            $stopWords = [
                'toi', 'cho', 'mot', 'vai', 'san', 'pham', 'gia', 'ban', 'bao', 'nhieu',
                'tien', 'cua', 'nay', 'kia', 'la', 'co', 'khong', 'xem', 'muon', 'can',
                'goi', 'danh', 'sach', 'nhung', 'loai', 'nao', 'shop', 'skinfood',
            ];
            $tokens = collect(preg_split('/\s+/u', $normalized))
                ->map(fn (string $token) => preg_replace('/[^a-z0-9.%]+/', '', $token))
                ->filter(fn (?string $token) => $token !== null
                    && mb_strlen($token) >= 3
                    && ! in_array($token, $stopWords, true))
                ->unique()
                ->values();

            $ranked = $products->map(function (SanPham $product) use ($tokens) {
                $name = Str::lower(Str::ascii((string) $product->TenSP));
                $type = Str::lower(Str::ascii((string) $product->loai?->TenLoai));
                $brand = Str::lower(Str::ascii((string) $product->thuongHieu?->TenTH));
                $details = Str::lower(Str::ascii(implode(' ', [
                    $product->LoaiDa,
                    $product->VanDeChiRi,
                    $product->ThanhPhanChiNhYeu,
                    strip_tags((string) $product->MoTa),
                ])));
                $score = $tokens->sum(function (string $token) use ($name, $type, $brand, $details) {
                    if (str_contains($name, $token)) {
                        return 8;
                    }
                    if (str_contains($type, $token)) {
                        return 5;
                    }
                    if (str_contains($brand, $token)) {
                        return 4;
                    }

                    return str_contains($details, $token) ? 2 : 0;
                });

                return ['product' => $product, 'score' => $score];
            })->sortByDesc('score')->values();

            if ($exactProduct !== null) {
                $matches = collect([['product' => $exactProduct, 'score' => PHP_INT_MAX]]);
            } elseif ($isPriceQuestion && $tokens->isEmpty() && session()->has('chat_last_product_ids')) {
                $lastIds = collect(session('chat_last_product_ids', []));
                $matches = $lastIds->map(function ($id) use ($products) {
                    $product = $products->firstWhere('MaSP', (int) $id);

                    return $product ? ['product' => $product, 'score' => PHP_INT_MAX] : null;
                })->filter()->take(5)->values();
            } else {
                $matches = $ranked->where('score', '>', 0)->take($isPriceQuestion ? 3 : 5);
            }

            if ($isPriceQuestion && $matches->count() > 1) {
                $firstScore = (int) data_get($matches->first(), 'score', 0);
                $secondScore = (int) data_get($matches->values()->get(1), 'score', 0);
                if ($firstScore >= $secondScore + 4) {
                    $matches = $matches->take(1);
                }
            }

            if ($matches->isEmpty() && $isListQuestion && $tokens->isEmpty()) {
                $matches = $ranked
                    ->sortByDesc(fn (array $item) => (float) $item['product']->DoTinCay)
                    ->take(5);
            }

            if ($matches->isEmpty()) {
                return $isPriceQuestion
                    ? 'Mình chưa tìm thấy sản phẩm này trong cửa hàng. Bạn hãy nhập rõ tên sản phẩm cần xem giá.'
                    : 'Mình chưa tìm thấy sản phẩm phù hợp trong cửa hàng. Bạn có thể nói rõ loại sản phẩm hoặc nhu cầu của da/tóc.';
            }

            session()->put(
                'chat_last_product_ids',
                $matches->map(fn (array $match) => (int) $match['product']->MaSP)->values()->all()
            );

            $lines = $matches->map(function (array $match) {
                /** @var SanPham $product */
                $product = $match['product'];
                $price = (float) $product->GiaBan;
                $discount = (float) $product->GiamGia;
                $finalPrice = round($price * (1 - $discount / 100));
                $priceText = number_format($finalPrice, 0, ',', '.').'đ';
                if ($discount > 0) {
                    $priceText .= ' (giá gốc '.number_format($price, 0, ',', '.').'đ, giảm '.number_format($discount, 0).'%)';
                }

                return '- '.$product->TenSP.': '.$priceText
                    .'; còn '.(int) $product->SoLuongTon.' sản phẩm'
                    .'; xem tại /san-pham/'.$product->MaSP;
            });

            if ($isPriceQuestion && $matches->count() === 1) {
                return "Giá sản phẩm hiện tại:\n".$lines->first();
            }

            return "Một số sản phẩm phù hợp trong cửa hàng:\n".$lines->implode("\n");
        } catch (\Throwable $exception) {
            Log::warning('Could not answer AI catalog question', [
                'message' => $exception->getMessage(),
            ]);

            return 'Dữ liệu sản phẩm đang tạm thời không truy xuất được. Bạn vui lòng thử lại sau.';
        }
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Bạn là nhân viên tư vấn mỹ phẩm của SkinFood Shop.

Quy tắc bắt buộc:
- Trả lời thẳng vào câu hỏi; không lặp lại lời chào ở mỗi tin nhắn.
- Với câu hỏi về một sản phẩm, trình bày ngắn gọn: công dụng, loại da phù hợp, cách dùng và lưu ý an toàn.
- Không bịa giá, tồn kho, chương trình khuyến mãi, thành phần riêng của sản phẩm hoặc chính sách cửa hàng khi chưa được cung cấp dữ liệu.
- Nếu thiếu dữ liệu riêng của sản phẩm, nói rõ đó là hướng dẫn chung dựa trên loại hoạt chất.
- Không chẩn đoán hoặc điều trị bệnh. Nếu có kích ứng nặng, khuyên ngừng dùng và gặp bác sĩ da liễu.
- Viết bằng tiếng Việt tự nhiên, khoảng 4 đến 8 câu hoặc các gạch đầu dòng ngắn.
- Không dùng Markdown như dấu **, tiêu đề #, bảng hoặc khối mã vì cửa sổ chat chỉ hiển thị văn bản thuần.
PROMPT;
    }
}
