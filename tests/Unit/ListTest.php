<?php

namespace App\Tests\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ListTest extends WebTestCase
{
    public function test_can_get_lists_from_list_provider(): void
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $listProvider = $container->get('App\Service\ListProvider');

        $lists = $listProvider->getLists();

        $this->assertIsArray($lists);
        $this->assertNotEmpty($lists);

        $this->assertArrayHasKey('01JZJ0NYE3NYKGC326KS6VGKET', $lists);
        $this->assertArrayHasKey('01JZJ0NYE3NYKGC326KS6VGKEV', $lists);
        $this->assertArrayHasKey('01JZJ0NYE3NYKGC326KS6VGKEW', $lists);
        $this->assertArrayHasKey('01JZJ0NYE3NYKGC326KS6VGKEX', $lists);

        $this->assertEquals('Default list', $lists['01JZJ0NYE3NYKGC326KS6VGKET']);
        $this->assertEquals('London', $lists['01JZJ0NYE3NYKGC326KS6VGKEV']);
        $this->assertEquals('Birmingham', $lists['01JZJ0NYE3NYKGC326KS6VGKEW']);
        $this->assertEquals('Edinburgh', $lists['01JZJ0NYE3NYKGC326KS6VGKEX']);
        $this->assertCount(4, $lists);

    }
}
