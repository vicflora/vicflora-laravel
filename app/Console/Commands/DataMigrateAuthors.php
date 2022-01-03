<?php

namespace App\Console\Commands;

use App\Models\Agent;
use App\Models\AgentType;
use App\Models\Contributor;
use App\Models\ContributorRole;
use App\Models\GroupPerson;
use App\Models\Reference;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DataMigrateAuthors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:migrate:authors';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean up and migrate authors';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $conn = DB::connection('mysql');

        $authors = $conn->table('vicflora_reference')
                ->whereNotIn('ReferenceType', ['Protologue', 'MISSING'])
                ->whereNotNull('Author')
                ->select('guid', DB::raw("trim(replace(replace(replace(Author, ' & ', '; '), ';;', ';'), '  ', ' ')) as author"))
                ->get();

        foreach ($authors as $rec) {
            $isEditor = false;
            $pos = strpos($rec->author, '(');
            if ($pos !== false) {
                $isEditor = true;
                $rec->author = substr($rec->author, 0, $pos-1);
            }


            $members = $this->splitAuthorString($rec->author);
            if ($members) {
                $rec->author = implode('; ', $members);
            }

            $agent = Agent::where('name', $rec->author)->first();

            if (!$agent) {
                $lastName = null;
                $initials = null;

                if ($members) {
                    $agentType = AgentType::where('name', count($members) > 1 ? 'Group' : 'Person')->first();

                    if (count($members) < 2 && strpos($rec->author, ',') !== false) {
                        $beforeComma = substr($rec->author, 0, strpos($rec->author, ','));
                        $afterComma = trim(substr($rec->author, strpos($rec->author, ',')+1));
                        if (strlen($afterComma) <= 10) {
                            $lastName = $beforeComma;
                            $initials = $afterComma;
                        }
                        else {
                            $agentType = AgentType::where('name', 'Other')->first();
                        }
                    }

                }
                else {
                    $agentType = AgentType::where('name', 'Organization')->first();
                }

                $agent = Agent::create([
                    'guid' => Str::uuid(),
                    'name' => $rec->author,
                    'agent_type_id' => $agentType->id,
                    'last_name' => $lastName,
                    'initials' => $initials,
                    'created_by_id' => 1
                ]);
            }

            $reference = Reference::where('guid', $rec->guid)->first();
            $reference->author_id = $agent->id;
            $reference->save();

            if (count($members) > 1) {
                foreach ($members as $index => $member) {
                    $memberAgent = Agent::where('name', $member)
                            ->whereHas('AgentType', function(Builder $query) {
                                $query->where('name', 'Person');
                            })->first();

                    if (!$memberAgent) {
                        $memberAgent = Agent::create([
                            'guid' => Str::uuid(),
                            'name' => $member,
                            'agent_type_id' => AgentType::where('name', 'Person')->value('id'),
                            'last_name' => substr($member, 0, strpos($member, ',')),
                            'initials' => trim(substr($member, strpos($member, ',')+1)),
                            'created_by_id' => 1
                        ]);
                    }

                    GroupPerson::create([
                        'group_id' => $agent->id,
                        'member_id' => $memberAgent->id,
                        'sequence' => $index,
                        'created_by_id' => 1
                    ]);
                }
            }

            if ($members) {
                $authorRole = ContributorRole::where('name', 'Author')->first();
                if (!$authorRole) {
                    $authorRole = ContributorRole::create([
                        'name' => 'Author',
                        'label' => 'Author',
                        'guid' => Str::uuid(),
                        'created_by_id' => 1
                    ]);
                }

                $editorRole = ContributorRole::where('name', 'Editor')->first();
                if (!$editorRole) {
                    $editorRole = ContributorRole::create([
                        'name' => 'Editor',
                        'label' => 'Editor',
                        'guid' => Str::uuid(),
                        'created_by_id' => 1
                    ]);
                }


                foreach ($members as $index => $member) {
                    $contrAgent = Agent::where('name', $member)
                            ->whereHas('AgentType', function(Builder $query) {
                                $query->where('name', 'Person');
                            })->first();

                    if (!$contrAgent) {
                        $contrAgent = Agent::create([
                            'guid' => Str::uuid(),
                            'name' => $member,
                            'agent_type_id' => AgentType::where('name', 'Person')->value('id'),
                            'last_name' => substr($member, 0, strpos($member, ',')),
                            'initials' => trim(substr($member, strpos($member, ',')+1)),
                            'created_by_id' => 1
                        ]);
                    }

                    Contributor::create([
                        'reference_id' => $reference->id,
                        'agent_id' => $contrAgent->id,
                        'contributor_role_id' => $isEditor ? $editorRole->id : $authorRole->id,
                        'sequence' => $index,
                        'created_by_id' => 1
                    ]);

                }
            }
        }

        // Protologue authors (when different from name authors)
        $protoAuthors = $conn->table('vicflora_reference as r')
                ->join('vicflora_name as n', 'r.ReferenceID', '=', 'n.ProtologueID')
                ->where('n.Author', 'not like', DB::raw("concat('%', r.Author)"))
                ->select('r.guid', DB::raw("trim(r.author) as author"))
                ->get();

        foreach ($protoAuthors as $author) {
            $agent = Agent::where('name', $author->author)->first();
            if (!$agent) {
                $agent = Agent::create([
                    'guid' => Str::uuid(),
                    'name' => $author->author,
                    'agent_type_id' => AgentType::where('name', 'Other')->value('id'),
                    'created_by_id' => 1,
                ]);
            }

            $reference = Reference::where('guid', $author->guid)->first();
            if ($reference) {
                $reference->author_id = $agent->id;
                $reference->save();
            }
        }


    }

    private function splitAuthorString($str)
    {
        $ret = [];
        $bits = preg_split('/[,;]+/', $str);
        if (count($bits) > 1 && count($bits) % 2 == 0) {
            for ($i = 0; $i < count($bits); $i += 2) {
                $ret[] = trim($bits[$i]) . ', ' . trim($bits[$i+1]);
            }
        }
        return $ret;
    }
}
