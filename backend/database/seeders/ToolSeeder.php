<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Role;
use App\Models\Tag;
use App\Models\Tool;
use App\Models\User;
use Illuminate\Database\Seeder;

class ToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();
        $categories = Category::all();
        $tags = Tag::all();
        $roles = Role::all();

        if ($users->isEmpty() || $categories->isEmpty() || $tags->isEmpty() || $roles->isEmpty()) {
            return;
        }

        $toolsData = [
            [
                'name' => 'ChatGPT',
                'link' => 'https://chat.openai.com',
                'description' => 'AI assistant for conversation, coding, and analysis.',
                'how_to_use' => 'Ask questions or give instructions in natural language.',
                'examples' => ['Summarize this article', 'Write a Python function'],
                'roles' => ['frontend', 'backend', 'owner'],
            ],
            [
                'name' => 'GitHub Copilot',
                'link' => 'https://github.com/features/copilot',
                'description' => 'AI pair programmer that suggests code in the editor.',
                'how_to_use' => 'Install the extension and start typing; accept suggestions with Tab.',
                'examples' => ['Complete a function', 'Generate tests'],
                'roles' => ['backend', 'frontend'],
            ],
            [
                'name' => 'Figma',
                'link' => 'https://figma.com',
                'description' => 'Collaborative interface design and prototyping tool.',
                'how_to_use' => 'Create frames, components, and prototypes; share with links.',
                'examples' => ['Design a dashboard', 'Prototype a flow'],
                'roles' => ['design', 'frontend'],
            ],
            [
                'name' => 'Notion',
                'link' => 'https://notion.so',
                'description' => 'All-in-one workspace for notes, docs, and project management.',
                'how_to_use' => 'Create pages and databases; use blocks and templates.',
                'examples' => ['Meeting notes', 'Task board'],
                'roles' => ['owner', 'frontend'],
            ],
            [
                'name' => 'Claude',
                'link' => 'https://claude.ai',
                'description' => 'AI assistant with long context and analysis.',
                'how_to_use' => 'Paste documents or chat; use for writing and reasoning.',
                'examples' => ['Analyze a contract', 'Draft a blog post'],
                'roles' => ['owner', 'backend', 'frontend'],
            ],
            [
                'name' => 'VS Code',
                'link' => 'https://code.visualstudio.com',
                'description' => 'Source code editor with extensions and Git integration.',
                'how_to_use' => 'Install, add extensions, open folder, code.',
                'examples' => ['Edit PHP', 'Debug JavaScript'],
                'roles' => ['backend', 'frontend'],
            ],
            [
                'name' => 'Postman',
                'link' => 'https://postman.com',
                'description' => 'API development and testing platform.',
                'how_to_use' => 'Create requests, set headers, send and inspect responses.',
                'examples' => ['Test REST API', 'Share collection'],
                'roles' => ['backend', 'devops'],
            ],
            [
                'name' => 'Docker',
                'link' => 'https://docker.com',
                'description' => 'Container platform for building and shipping apps.',
                'how_to_use' => 'Write Dockerfile, build image, run container.',
                'examples' => ['Containerize Laravel', 'Compose stack'],
                'roles' => ['backend', 'devops'],
            ],
            [
                'name' => 'Slack',
                'link' => 'https://slack.com',
                'description' => 'Team messaging and channel-based collaboration.',
                'how_to_use' => 'Create channels, invite members, send messages and files.',
                'examples' => ['Daily standup', 'Support channel'],
                'roles' => ['owner', 'frontend', 'backend'],
            ],
            [
                'name' => 'Linear',
                'link' => 'https://linear.app',
                'description' => 'Issue tracking and product management for teams.',
                'how_to_use' => 'Create issues, cycles, roadmaps; link to Git.',
                'examples' => ['Sprint planning', 'Bug triage'],
                'roles' => ['owner', 'backend', 'frontend'],
            ],
            [
                'name' => 'Grammarly',
                'link' => 'https://grammarly.com',
                'description' => 'Writing assistant for grammar and clarity.',
                'how_to_use' => 'Install extension or use editor; get suggestions as you type.',
                'examples' => ['Email draft', 'Document review'],
                'roles' => ['owner', 'frontend'],
            ],
            [
                'name' => 'Canva',
                'link' => 'https://canva.com',
                'description' => 'Visual design tool for graphics and presentations.',
                'how_to_use' => 'Pick template, edit text and images, download or share.',
                'examples' => ['Social post', 'Pitch deck'],
                'roles' => ['design', 'owner'],
            ],
            [
                'name' => 'Jest',
                'link' => 'https://jestjs.io',
                'description' => 'JavaScript testing framework.',
                'how_to_use' => 'Write test files, run npm test.',
                'examples' => ['Unit tests', 'Snapshot tests'],
                'roles' => ['backend', 'frontend', 'qa'],
            ],
            [
                'name' => 'Laravel Forge',
                'link' => 'https://forge.laravel.com',
                'description' => 'Server management and deployment for Laravel.',
                'how_to_use' => 'Connect server, create site, deploy from Git.',
                'examples' => ['Deploy app', 'SSL and queue'],
                'roles' => ['backend', 'devops'],
            ],
            [
                'name' => 'GitHub',
                'link' => 'https://github.com',
                'description' => 'Code hosting, pull requests, and collaboration.',
                'how_to_use' => 'Push repo, open PR, review and merge.',
                'examples' => ['Version control', 'CI/CD'],
                'roles' => ['backend', 'frontend', 'owner'],
            ],
            [
                'name' => 'Vercel',
                'link' => 'https://vercel.com',
                'description' => 'Deploy and host frontends and serverless functions.',
                'how_to_use' => 'Import Git repo, configure build, deploy.',
                'examples' => ['Next.js deploy', 'Preview URLs'],
                'roles' => ['frontend', 'devops'],
            ],
            [
                'name' => 'Obsidian',
                'link' => 'https://obsidian.md',
                'description' => 'Note-taking with linked thoughts and local markdown.',
                'how_to_use' => 'Create vault, write notes, link with [[ ]]',
                'examples' => ['Knowledge base', 'Meeting notes'],
                'roles' => ['owner', 'frontend'],
            ],
            [
                'name' => 'Cursor',
                'link' => 'https://cursor.com',
                'description' => 'AI-powered code editor built on VS Code.',
                'how_to_use' => 'Open project, use chat or inline edit with AI.',
                'examples' => ['Refactor code', 'Generate tests'],
                'roles' => ['backend', 'frontend'],
            ],
            [
                'name' => 'Stripe',
                'link' => 'https://stripe.com',
                'description' => 'Payments and billing APIs for products.',
                'how_to_use' => 'Integrate SDK or API; create customers and charges.',
                'examples' => ['Checkout', 'Subscriptions'],
                'roles' => ['backend', 'owner'],
            ],
            [
                'name' => 'Swagger',
                'link' => 'https://swagger.io',
                'description' => 'API documentation and design with OpenAPI.',
                'how_to_use' => 'Define spec (YAML/JSON), generate docs and clients.',
                'examples' => ['API docs', 'Mock server'],
                'roles' => ['backend', 'devops'],
            ],
        ];

        foreach ($toolsData as $data) {
            $payload = collect($data)->except('roles')->toArray();
            $payload['user_id'] = $users->random()->id;
            $payload['category_id'] = $categories->random()->id;
            // SQLite: tools.roles is NOT NULL; provide empty JSON array
            $payload['roles'] = json_encode($data['roles'] ?? []);

            // Ensure examples is JSON for the DB (avoids "Array to string conversion")
            if (isset($payload['examples']) && is_array($payload['examples'])) {
                $payload['examples'] = json_encode($payload['examples']);
            }

            $tool = Tool::updateOrCreate(
                ['name' => $data['name']],
                $payload
            );

            // README: some tools approved (catalog/API), rest pending (admin approval workflow)
            $index = array_search($data, $toolsData);
            if ($index !== false && $index < 10) {
                $tool->update(['status' => 'approved', 'is_approved' => true]);
            }

            $roleCount = random_int(1, min(3, $roles->count()));
            $randomRoleIds = $roles->random($roleCount)->pluck('id')->unique()->values()->all();
            $tool->roles()->sync($randomRoleIds);

            $tagCount = random_int(2, 3);
            $randomTagIds = $tags->random(min($tagCount, $tags->count()))->pluck('id')->unique()->values()->all();
            $tool->tags()->detach();
            $tool->tags()->attach($randomTagIds);
        }
    }
}
