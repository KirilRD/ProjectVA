<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToolsApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_status_returns_ok(): void
    {
        $response = $this->getJson('/api/status');
        $response->assertStatus(200)
            ->assertJson(['status' => 'ok', 'backend' => 'Laravel']);
    }

    public function test_tools_index_returns_empty_array(): void
    {
        $response = $this->getJson('/api/tools');
        $response->assertStatus(200)->assertExactJson([]);
    }

    public function test_tools_store_creates_tool(): void
    {
        $payload = [
            'name' => 'ChatGPT',
            'link' => 'https://chat.openai.com',
            'description' => 'AI assistant',
            'how_to_use' => 'Ask questions in natural language.',
            'examples' => ['Summarize this text'],
            'roles' => ['frontend', 'backend'],
        ];
        $response = $this->postJson('/api/tools', $payload);
        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'ChatGPT', 'link' => 'https://chat.openai.com']);
        $this->assertDatabaseHas('tools', ['name' => 'ChatGPT']);
    }
}
