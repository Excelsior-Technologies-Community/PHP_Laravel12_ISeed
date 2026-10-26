<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\SeederHistory;
use App\Models\User;
use App\Services\SeederDataMasker;
use App\Services\SeederDiffService;
use App\Services\TableDependencyResolver;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ISeedSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed some test data
        User::create([
            'name' => 'John Doe',
            'email' => 'john.doe@production-company.com',
            'password' => bcrypt('super_secret_password_123'),
        ]);

        Product::create([
            'name' => 'Gaming Keyboard',
            'category' => 'Electronics',
            'price' => 2500,
            'description' => 'RGB mechanical switches',
            'status' => 'active',
        ]);
        Product::create([
            'name' => 'Office Desk',
            'category' => 'Furniture',
            'price' => 8500,
            'description' => 'Ergonomic standing desk',
            'status' => 'inactive',
        ]);
    }

    public function test_seeder_dashboard_loads_with_discovered_tables(): void
    {
        $response = $this->get(route('seeder.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Dependency-Aware Seeding Studio');
        $response->assertSee('products');
        $response->assertSee('users');
        $response->assertSee('Enable Sensitive Data Masking');
    }

    public function test_batch_seeding_with_dependency_resolution_and_gdpr_masking(): void
    {
        $response = $this->post(route('seeder.generate'), [
            'tables' => ['users', 'products'],
            'mask_sensitive_data' => 1,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verify history entries
        $userHistory = SeederHistory::where('table_name', 'users')->latest()->first();
        $productHistory = SeederHistory::where('table_name', 'products')->latest()->first();

        $this->assertNotNull($userHistory);
        $this->assertNotNull($productHistory);
        $this->assertTrue($userHistory->masking_applied);

        // Verify that the generated UsersTableSeeder has masked the email and password
        $userSeederPath = database_path('seeders/UsersTableSeeder.php');
        $this->assertFileExists($userSeederPath);

        $seederContent = File::get($userSeederPath);
        // Original email must be sanitized
        $this->assertStringNotContainsString('john.doe@production-company.com', $seederContent);
        $this->assertStringContainsString('@example.test', $seederContent);
        // Original password plaintext should never appear
        $this->assertStringNotContainsString('super_secret_password_123', $seederContent);
    }

    public function test_data_slicing_with_limit_and_where_filtering(): void
    {
        $response = $this->post(route('seeder.generate'), [
            'tables' => ['products'],
            'max_records' => 1,
            'where_clause' => "status = 'active'",
            'exclude_columns' => 'description',
        ]);

        $response->assertSessionHasNoErrors();

        $productSeederPath = database_path('seeders/ProductsTableSeeder.php');
        $this->assertFileExists($productSeederPath);

        $content = File::get($productSeederPath);
        $this->assertStringContainsString('Gaming Keyboard', $content);
        $this->assertStringNotContainsString('Office Desk', $content);
    }

    public function test_seeder_code_inspector_json_endpoint(): void
    {
        // Generate a product seeder first
        $this->post(route('seeder.generate'), ['tables' => ['products']]);

        $response = $this->get(route('seeder.code', 'ProductsTableSeeder'));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'name' => 'ProductsTableSeeder',
            'file_name' => 'ProductsTableSeeder.php',
        ]);
        $this->assertNotEmpty($response->json('code'));
    }

    public function test_seeder_diff_computation_service(): void
    {
        $diffService = new SeederDiffService();

        $oldCode = "line1\nline2\nline3";
        $newCode = "line1\nline2_modified\nline3\nline4_added";

        $result = $diffService->computeDiff($oldCode, $newCode);

        $this->assertTrue($result['has_changes']);
        $this->assertGreaterThan(0, $result['added']);
        $this->assertGreaterThan(0, $result['removed']);
    }

    public function test_in_browser_code_editor_and_syntax_validation(): void
    {
        // 1. Generate seeder
        $this->post(route('seeder.generate'), ['tables' => ['products']]);

        // 2. Try invalid PHP syntax
        $invalidResponse = $this->postJson(route('seeder.update.code'), [
            'seeder_name' => 'ProductsTableSeeder',
            'code' => '<?php function parseError( { echo 1; }',
        ]);
        $invalidResponse->assertStatus(422);
        $this->assertFalse($invalidResponse->json('success'));

        // 3. Save valid PHP code
        $validCode = "<?php\nnamespace Database\Seeders;\nuse Illuminate\Database\Seeder;\nclass ProductsTableSeeder extends Seeder { public function run() { /* verified edit */ } }\n";
        $validResponse = $this->postJson(route('seeder.update.code'), [
            'seeder_name' => 'ProductsTableSeeder',
            'code' => $validCode,
        ]);
        $validResponse->assertStatus(200);
        $this->assertTrue($validResponse->json('success'));

        $savedContent = File::get(database_path('seeders/ProductsTableSeeder.php'));
        $this->assertStringContainsString('verified edit', $savedContent);
    }

    public function test_seeder_execution_with_latency_recording(): void
    {
        // Generate seeder
        $this->post(route('seeder.generate'), ['tables' => ['products']]);

        $history = SeederHistory::where('table_name', 'products')->latest()->first();

        $response = $this->post(route('seeder.run'), [
            'seeder' => 'ProductsTableSeeder',
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $history->refresh();
        $this->assertEquals('executed', $history->status);
        $this->assertNotNull($history->execution_time_ms);
    }
}
