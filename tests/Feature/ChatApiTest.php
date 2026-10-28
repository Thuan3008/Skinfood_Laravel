<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatApiTest extends TestCase
{
    public function test_chat_can_use_openai(): void
    {
        config([
            'services.ai.provider' => 'openai',
            'services.openai.key' => 'test-openai-key',
            'services.openai.model' => 'test-openai-model',
        ]);
        Http::fake([
            'api.openai.com/*' => Http::response([
                'choices' => [['message' => ['content' => 'Phản hồi từ OpenAI']]],
            ]),
        ]);

        $this->post('/Chat/SendMessage', ['message' => 'Tư vấn da dầu'])
            ->assertOk()
            ->assertSeeText('Phản hồi từ OpenAI');

        Http::assertSent(fn (Request $request) =>
            $request->url() === 'https://api.openai.com/v1/chat/completions'
            && $request['model'] === 'test-openai-model'
        );
    }

    public function test_chat_can_use_gemini(): void
    {
        config([
            'services.ai.provider' => 'gemini',
            'services.gemini.key' => 'test-gemini-key',
            'services.gemini.model' => 'test-gemini-model',
        ]);
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::response([
                'candidates' => [['content' => ['parts' => [['text' => 'Phản hồi từ Gemini']]]]],
            ]),
        ]);

        $this->post('/Chat/SendMessage', ['message' => 'Tư vấn kem chống nắng'])
            ->assertOk()
            ->assertSeeText('Phản hồi từ Gemini');

        Http::assertSent(fn (Request $request) =>
            str_contains($request->url(), '/models/test-gemini-model:generateContent')
            && str_contains($request->url(), 'key=test-gemini-key')
        );
    }

    public function test_chat_can_use_groq(): void
    {
        config([
            'services.ai.provider' => 'groq',
            'services.groq.key' => 'test-groq-key',
            'services.groq.model' => 'test-groq-model',
        ]);
        Http::fake([
            'api.groq.com/*' => Http::response([
                'choices' => [['message' => ['content' => 'Phản hồi từ Groq']]],
            ]),
        ]);

        $this->post('/Chat/SendMessage', ['message' => 'Tư vấn serum'])
            ->assertOk()
            ->assertSeeText('Phản hồi từ Groq');

        Http::assertSent(fn (Request $request) =>
            $request->url() === 'https://api.groq.com/openai/v1/chat/completions'
            && $request['model'] === 'test-groq-model'
            && $request->hasHeader('Authorization', 'Bearer test-groq-key')
        );
    }

    public function test_chat_rejects_unrelated_question_without_calling_ai(): void
    {
        config([
            'services.ai.provider' => 'groq',
            'services.groq.key' => 'test-groq-key',
        ]);
        Http::fake();

        $this->post('/Chat/SendMessage', ['message' => 'Tôi muốn ăn cơm'])
            ->assertOk()
            ->assertSeeText('Mình chỉ hỗ trợ tư vấn sản phẩm có tại SkinFood Shop.');

        Http::assertNothingSent();
    }

    public function test_chat_lists_matching_products_from_database_without_calling_ai(): void
    {
        Http::fake();

        $this->post('/Chat/SendMessage', ['message' => 'Gợi ý cho tôi một vài sản phẩm serum'])
            ->assertOk()
            ->assertSeeText('Một số sản phẩm phù hợp trong cửa hàng:')
            ->assertSeeText('Serum');

        Http::assertNothingSent();
    }

    public function test_chat_returns_product_price_from_database_without_calling_ai(): void
    {
        Http::fake();

        $this->post('/Chat/SendMessage', ['message' => 'Giá Serum Retinol 0.5% bao nhiêu?'])
            ->assertOk()
            ->assertSeeText('Serum Retinol 0.5% tái tạo da ban đêm')
            ->assertSeeText('420.000đ');

        Http::assertNothingSent();
    }

    public function test_chat_remembers_suggested_products_for_follow_up_price_question(): void
    {
        Http::fake();

        $this->post('/Chat/SendMessage', ['message' => 'Gợi ý cho tôi một vài sản phẩm serum'])
            ->assertOk();

        $this->post('/Chat/SendMessage', ['message' => 'Giá sản phẩm đó bao nhiêu?'])
            ->assertOk()
            ->assertSeeText('Serum')
            ->assertSeeText('đ');

        Http::assertNothingSent();
    }
}
