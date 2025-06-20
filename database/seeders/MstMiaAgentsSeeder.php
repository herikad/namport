<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DB;

class MstMiaAgentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $agents = [
            'Ruva', 'Neli', 'Oshi', 'Loma', 'Tuli',
            'Tana', 'Zali', 'Zato', 'Simo',
            'Kayo', 'Bako', 'Meka', 'Peni',
        ];

        $imageUrls = [
            'https://images.unsplash.com/photo-1502685104226-ee32379fefbe?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1529626455594-4ff0802cfb7e?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1531123897727-8f129e1688ce?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1599566150163-29194dcaad36?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1520813792240-56fc4a3765a7?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1544005313-94ddf0286df2?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1547425260-76bcadfb4f2c?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1535713875002-d1d0cf377fde?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1544005313-94ddf0286df2?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1607746882042-944635dfe10e?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1599566150163-29194dcaad36?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1552374196-c4e7ffc6e126?crop=faces&fit=crop&h=200&w=200',
            'https://images.unsplash.com/photo-1552058544-f2b08422138a?crop=faces&fit=crop&h=200&w=200',
        ];

        foreach ($agents as $index => $name) {
            DB::table('mst_miaagents')->insert([
                'mia_agent_id'          => $index + 1,
                'client_id'             => 1,
                'customer_id'           => 1,
                'miaid'                 => null,
                'nameofagent'           => $name,
                'totalprocessassigned'  => rand(1, 5),
                'totalsessions'         => rand(0, 10),
                'totalmeetingscheduled' => rand(0, 5),
                'totalprocesscomplete'  => rand(0, 5),
                'totalmeetingcompleted' => rand(0, 5),
                'repository_folderid'   => null,
                'agentpersonafile'      => $imageUrls[$index] ?? $imageUrls[array_rand($imageUrls)],
                'is_active'             => 1,
                'created_at'            => now(),
                'created_by'            => 1,
            ]);
        }
    }

}
