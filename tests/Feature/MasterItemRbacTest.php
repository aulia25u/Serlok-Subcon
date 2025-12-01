<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\MasterItem;
use App\Models\Role;
use App\Models\TenantOwner;
use App\Models\User;
use App\Models\UserDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterItemRbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles
        Role::create(['role_name' => 'Administrator']);
        Role::create(['role_name' => 'Employee']);
    }

    public function test_tenant_user_can_only_see_own_items()
    {
        // Setup Tenant A
        $customerA = Customer::create(['customer_name' => 'Tenant A', 'customer_code' => 'TA']);
        $tenantOwnerA = TenantOwner::create(['customer_id' => $customerA->id]);
        $userA = User::factory()->create();
        UserDetail::create([
            'user_id' => $userA->id,
            'customer_id' => $customerA->id,
            'role_id' => Role::where('role_name', 'Employee')->first()->id,
        ]);

        // Setup Tenant B
        $customerB = Customer::create(['customer_name' => 'Tenant B', 'customer_code' => 'TB']);
        $tenantOwnerB = TenantOwner::create(['customer_id' => $customerB->id]);
        $userB = User::factory()->create();
        UserDetail::create([
            'user_id' => $userB->id,
            'customer_id' => $customerB->id,
            'role_id' => Role::where('role_name', 'Employee')->first()->id,
        ]);

        // Create Items
        $itemA = MasterItem::create([
            'tenant_id' => $tenantOwnerA->id,
            'item_name' => 'Item A',
            'item_code' => 'ITEM-A',
        ]);

        $itemB = MasterItem::create([
            'tenant_id' => $tenantOwnerB->id,
            'item_name' => 'Item B',
            'item_code' => 'ITEM-B',
        ]);

        // Act & Assert for User A
        $responseA = $this->actingAs($userA)->getJson(route('rbac.master-item'));
        $responseA->assertOk();
        $responseA->assertJsonFragment(['item_code' => 'ITEM-A']);
        $responseA->assertJsonMissing(['item_code' => 'ITEM-B']);
    }

    public function test_internal_user_without_admin_role_sees_nothing()
    {
        // Setup Internal User (No Customer, Employee Role)
        $userInternal = User::factory()->create();
        UserDetail::create([
            'user_id' => $userInternal->id,
            'customer_id' => null,
            'role_id' => Role::where('role_name', 'Employee')->first()->id,
        ]);

        // Create an Item
        $customer = Customer::create(['customer_name' => 'Tenant A', 'customer_code' => 'TA']);
        $tenantOwner = TenantOwner::create(['customer_id' => $customer->id]);
        MasterItem::create([
            'tenant_id' => $tenantOwner->id,
            'item_name' => 'Item A',
            'item_code' => 'ITEM-A',
        ]);

        // Act
        $response = $this->actingAs($userInternal)->getJson(route('rbac.master-item'));

        // Assert
        $response->assertOk();
        // Should NOT see the item because they are not Admin
        $response->assertJsonMissing(['item_code' => 'ITEM-A']);
        $response->assertJsonCount(0, 'data');
    }

    public function test_admin_user_sees_all_items()
    {
        // Setup Admin User
        $userAdmin = User::factory()->create();
        UserDetail::create([
            'user_id' => $userAdmin->id,
            'customer_id' => null,
            'role_id' => Role::where('role_name', 'Administrator')->first()->id,
        ]);

        // Create Items
        $customer = Customer::create(['customer_name' => 'Tenant A', 'customer_code' => 'TA']);
        $tenantOwner = TenantOwner::create(['customer_id' => $customer->id]);
        MasterItem::create([
            'tenant_id' => $tenantOwner->id,
            'item_name' => 'Item A',
            'item_code' => 'ITEM-A',
        ]);

        // Act
        $response = $this->actingAs($userAdmin)->getJson(route('rbac.master-item'));

        // Assert
        $response->assertOk();
        $response->assertJsonFragment(['item_code' => 'ITEM-A']);
    }
}
